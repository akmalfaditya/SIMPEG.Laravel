<?php

namespace App\Http\Controllers;

use App\Models\AgamaMaster;
use App\Models\Bagian;
use App\Models\GolonganDarahMaster;
use App\Models\GolonganPangkat;
use App\Models\Jabatan;
use App\Models\JenisKelaminMaster;
use App\Models\Pegawai;
use App\Models\StatusKepegawaian;
use App\Models\StatusPernikahanMaster;
use App\Models\TipePegawai;
use App\Models\UnitKerja;
use App\Services\DocumentUploadService;
use App\Services\PegawaiService;
use App\Services\SalaryCalculatorService;
use App\Http\Requests\StorePegawaiRequest;
use App\Http\Requests\UpdatePegawaiRequest;
use App\DTOs\PegawaiDTO;
use App\Http\Resources\PegawaiResource;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    private const DEFAULT_PAGE = 1;
    private const DEFAULT_LIMIT = 10;

    public function __construct(
        private PegawaiService $service,
        private SalaryCalculatorService $salaryCalculatorService,
        private DocumentUploadService $documentService,
    ) {}

    public function index()
    {
        return view('pegawai.index');
    }

    public function getPaginated(Request $request)
    {
        $limit = (int) $request->input('limit', self::DEFAULT_LIMIT);
        $search = $request->input('search');
        $status = $request->input('status', 'aktif');

        $paginated = $this->service->getPaginatedByStatus($status, $limit, $search);

        return response()->json([
            'data' => PegawaiResource::collection($paginated),
            'total' => $paginated->total(),
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
        ]);
    }

    public function show(Pegawai $pegawai)
    {
        $pegawai->load([
            'jenisKelamin',
            'agama',
            'statusPernikahan',
            'golonganDarah',
            'tipePegawai',
            'statusKepegawaian',
            'bagian',
            'unitKerja',
            'riwayatPangkat',
            'riwayatJabatan.jabatan',
            'riwayatKgb',
            'riwayatHukumanDisiplin',
            'riwayatPendidikan',
            'riwayatLatihanJabatan',
            'penilaianKinerja',
            'riwayatPenghargaan',
        ]);
        return view('pegawai.show', [
            'pegawai' => $pegawai,
            'golonganOptions' => GolonganPangkat::where('is_active', true)->orderBy('golongan_ruang')->get(),
            'jabatanOptions' => Jabatan::orderBy('nama_jabatan')->get(),
            'estimasiKgbSelanjutnya' => $this->salaryCalculatorService->calculateNextKgbDate($pegawai),
            'timeline' => $this->service->getCareerTimeline($pegawai),
        ]);
    }

    public function create()
    {
        return view('pegawai.create', $this->masterDataOptions() + [
            'golonganOptions' => GolonganPangkat::where('is_active', true)->orderBy('golongan_ruang')->get(),
            'jabatanOptions' => Jabatan::where('is_active', true)->orderBy('nama_jabatan')->get(),
        ]);
    }

    public function store(StorePegawaiRequest $request)
    {
        $validated = $request->validated();

        // Upload foundational SK documents if provided
        $skPaths = $this->uploadSkDocuments($request, $validated['nip']);
        $validated = array_merge($validated, $skPaths);

        $dto = PegawaiDTO::fromRequest($validated);
        $pegawai = $this->service->create(
            $dto,
            golonganId: (int) $request->validated('golongan_id'),
            jabatanId: (int) $request->validated('jabatan_id'),
        );

        $docInfo = $this->buildDocumentFlashInfo($skPaths);
        return redirect()->route('pegawai.show', $pegawai)
            ->with('success', "Data pegawai berhasil ditambahkan. Riwayat pangkat dan jabatan awal telah dibuat otomatis.{$docInfo}");
    }

    public function edit(Pegawai $pegawai)
    {
        return view('pegawai.edit', $this->masterDataOptions() + [
            'pegawai' => $pegawai,
        ]);
    }

    public function update(UpdatePegawaiRequest $request, Pegawai $pegawai)
    {
        $validated = $request->validated();

        // Upload foundational SK documents if provided (replace old files)
        $skPaths = $this->uploadSkDocuments($request, $pegawai->nip, $pegawai);
        $validated = array_merge($validated, $skPaths);

        $dto = PegawaiDTO::fromRequest($validated);
        $this->service->update($pegawai, $dto);

        $docInfo = $this->buildDocumentFlashInfo($skPaths);
        return redirect()->route('pegawai.show', $pegawai)
            ->with('success', "Data pegawai berhasil diperbarui.{$docInfo}");
    }

    public function destroy(Pegawai $pegawai)
    {
        $this->service->delete($pegawai);
        return redirect()->route('pegawai.index')->with('success', 'Data pegawai berhasil dihapus.');
    }

    public function reactivate(Pegawai $pegawai)
    {
        $this->service->reactivate($pegawai);
        return redirect()->route('pegawai.index')->with('success', "Pegawai {$pegawai->nama_lengkap} berhasil diaktifkan kembali.");
    }

    public function cancelPensiun(Pegawai $pegawai)
    {
        $this->service->cancelPensiun($pegawai);
        activity()->performedOn($pegawai)->log("Membatalkan pensiun untuk pegawai #{$pegawai->id} atas nama {$pegawai->nama_lengkap}");
        return redirect()->route('pegawai.index')->with('success', "Pensiun pegawai {$pegawai->nama_lengkap} berhasil dibatalkan.");
    }

    /**
     * Upload SK CPNS and SK PNS documents if present in the request.
     * Returns an array with sk_cpns_path and/or sk_pns_path keys.
     */
    private function uploadSkDocuments(Request $request, string $nip, ?Pegawai $existing = null): array
    {
        $paths = [];
        $timestamp = now()->format('Ymd_His');

        if ($request->hasFile('sk_cpns_file')) {
            // Delete old file if replacing
            if ($existing?->sk_cpns_path) {
                $this->documentService->delete($existing->sk_cpns_path);
            }
            $fileName = "{$nip}_SK_CPNS_{$timestamp}.pdf";
            $paths['sk_cpns_path'] = $this->documentService->upload(
                $request->file('sk_cpns_file'),
                'sk_cpns',
                $fileName,
            );
        }

        if ($request->hasFile('sk_pns_file')) {
            // Delete old file if replacing
            if ($existing?->sk_pns_path) {
                $this->documentService->delete($existing->sk_pns_path);
            }
            $fileName = "{$nip}_SK_PNS_{$timestamp}.pdf";
            $paths['sk_pns_path'] = $this->documentService->upload(
                $request->file('sk_pns_file'),
                'sk_pns',
                $fileName,
            );
        }

        return $paths;
    }

    /**
     * Build flash message suffix for uploaded documents.
     */
    private function buildDocumentFlashInfo(array $skPaths): string
    {
        $docs = [];
        if (isset($skPaths['sk_cpns_path'])) {
            $docs[] = 'SK CPNS';
        }
        if (isset($skPaths['sk_pns_path'])) {
            $docs[] = 'SK PNS';
        }

        return $docs ? ' Dokumen ' . implode(' dan ', $docs) . ' berhasil diunggah.' : '';
    }
    public function exportPdf(Pegawai $pegawai)
    {
        $pegawai->load([
            'jenisKelamin',
            'agama',
            'statusPernikahan',
            'golonganDarah',
            'tipePegawai',
            'statusKepegawaian',
            'bagian',
            'unitKerja',
            'riwayatPangkat.golongan',
            'riwayatJabatan.jabatan',
            'riwayatKgb',
            'riwayatHukumanDisiplin',
            'riwayatPendidikan',
            'riwayatLatihanJabatan',
            'penilaianKinerja',
            'riwayatPenghargaan',
        ]);

        $pdf = Pdf::loadView('exports.pegawai-profile-pdf', compact('pegawai'))
            ->setPaper('a4', 'portrait');

        $filename = 'profil_' . $pegawai->nip . '_' . date('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    private function masterDataOptions(): array
    {
        return [
            'jenisKelaminOptions' => JenisKelaminMaster::orderBy('nama')->get(),
            'agamaOptions' => AgamaMaster::orderBy('nama')->get(),
            'statusPernikahanOptions' => StatusPernikahanMaster::orderBy('nama')->get(),
            'golonganDarahOptions' => GolonganDarahMaster::orderBy('nama')->get(),
            'tipePegawaiOptions' => TipePegawai::orderBy('nama')->get(),
            'statusKepegawaianOptions' => StatusKepegawaian::orderBy('nama')->get(),
            'bagianOptions' => Bagian::orderBy('nama')->get(),
            'unitKerjaOptions' => UnitKerja::orderBy('nama')->get(),
        ];
    }
}