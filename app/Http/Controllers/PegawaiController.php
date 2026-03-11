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
use App\Services\PegawaiService;
use App\Services\SalaryCalculatorService;
use App\Http\Requests\StorePegawaiRequest;
use App\Http\Requests\UpdatePegawaiRequest;
use App\DTOs\PegawaiDTO;
use App\Http\Resources\PegawaiResource;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    private const DEFAULT_PAGE = 1;
    private const DEFAULT_LIMIT = 10;

    public function __construct(
        private PegawaiService $service,
        private SalaryCalculatorService $salaryCalculatorService,
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
            'jenisKelamin', 'agama', 'statusPernikahan', 'golonganDarah',
            'tipePegawai', 'statusKepegawaian', 'bagian', 'unitKerja',
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
        $dto = PegawaiDTO::fromRequest($request->validated());
        $pegawai = $this->service->create(
            $dto,
            golonganId: (int) $request->validated('golongan_id'),
            jabatanId: (int) $request->validated('jabatan_id'),
        );

        return redirect()->route('pegawai.show', $pegawai)
            ->with('success', 'Data pegawai berhasil ditambahkan. Riwayat pangkat dan jabatan awal telah dibuat otomatis.');
    }

    public function edit(Pegawai $pegawai)
    {
        return view('pegawai.edit', $this->masterDataOptions() + [
            'pegawai' => $pegawai,
        ]);
    }

    public function update(UpdatePegawaiRequest $request, Pegawai $pegawai)
    {
        $dto = PegawaiDTO::fromRequest($request->validated());
        $this->service->update($pegawai, $dto);

        return redirect()->route('pegawai.show', $pegawai)->with('success', 'Data pegawai berhasil diperbarui.');
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
        return redirect()->route('pegawai.index')->with('success', "Pensiun pegawai {$pegawai->nama_lengkap} berhasil dibatalkan.");
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
