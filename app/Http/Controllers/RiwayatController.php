<?php

namespace App\Http\Controllers;

use App\Enums\JenisSanksi;
use App\Enums\TingkatHukuman;
use App\Models\GolonganPangkat;
use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\RiwayatHukumanDisiplin;
use App\Models\RiwayatJabatan;
use App\Models\RiwayatKgb;
use App\Models\RiwayatLatihanJabatan;
use App\Models\RiwayatPangkat;
use App\Models\RiwayatPendidikan;
use App\Models\PenilaianKinerja;

use App\Services\RiwayatService;
use App\Services\JabatanService;
use App\Services\KGBCalculationService;

use App\Http\Requests\Riwayat\StorePangkatRequest;
use App\Http\Requests\Riwayat\UpdatePangkatRequest;
use App\DTOs\Riwayat\RiwayatPangkatDTO;

use App\Http\Requests\Riwayat\StoreJabatanRequest;
use App\Http\Requests\Riwayat\UpdateJabatanRequest;
use App\DTOs\Riwayat\RiwayatJabatanDTO;

use App\Http\Requests\Riwayat\StoreKGBRequest;
use App\Http\Requests\Riwayat\UpdateKGBRequest;
use App\DTOs\Riwayat\RiwayatKgbDTO;

use App\Http\Requests\Riwayat\StoreHukumanRequest;
use App\Http\Requests\Riwayat\UpdateHukumanRequest;
use App\DTOs\Riwayat\RiwayatHukumanDisiplinDTO;

use App\Http\Requests\Riwayat\StorePendidikanRequest;
use App\Http\Requests\Riwayat\UpdatePendidikanRequest;
use App\DTOs\Riwayat\RiwayatPendidikanDTO;

use App\Http\Requests\Riwayat\StoreLatihanRequest;
use App\Http\Requests\Riwayat\UpdateLatihanRequest;
use App\DTOs\Riwayat\RiwayatLatihanJabatanDTO;

use App\Http\Requests\Riwayat\StoreSKPRequest;
use App\Http\Requests\Riwayat\UpdateSKPRequest;
use App\DTOs\Riwayat\PenilaianKinerjaDTO;

class RiwayatController extends Controller
{
    public function __construct(
        private RiwayatService $service,
        private JabatanService $jabatanService,
        private KGBCalculationService $kgbCalculationService,
    ) {}

    // --- PANGKAT ---
    public function createPangkat(int $pegawaiId)
    {
        $pegawai = Pegawai::with(['riwayatPangkat', 'riwayatHukumanDisiplin'])->findOrFail($pegawaiId);
        $currentPangkat = $pegawai->riwayatPangkat->sortByDesc('tmt_pangkat')->first();

        // Block if active sanctions that prevent pangkat promotion
        $activeBlocking = $pegawai->riwayatHukumanDisiplin
            ->filter(fn($h) => $h->isAktif()
                && in_array($h->jenis_sanksi, [
                    JenisSanksi::PenundaanPangkat,
                    JenisSanksi::PenurunanPangkat,
                    JenisSanksi::PembebasanJabatan,
                    JenisSanksi::Pemberhentian,
                ]));

        if ($activeBlocking->isNotEmpty()) {
            $notes = $activeBlocking->map(fn($h) => $h->jenis_sanksi->label())->implode(', ');
            return redirect(route('pegawai.show', $pegawaiId) . '#tab-pangkat')
                ->with('error', "Tidak dapat menambah Riwayat Pangkat — pegawai sedang menjalani sanksi aktif: {$notes}.");
        }

        return view('riwayat.create-pangkat', [
            'pegawaiId' => $pegawaiId,
            'golonganOptions' => GolonganPangkat::where('is_active', true)->orderBy('golongan_ruang')->get(),
            'currentPangkat' => $currentPangkat,
        ]);
    }

    public function storePangkat(StorePangkatRequest $request)
    {
        $validated = $request->validated();
        if ($request->hasFile('file_sk')) {
            $validated['file_pdf_path'] = $this->service->uploadSk($request->file('file_sk'), 'sk_pangkat', null, (int) $validated['pegawai_id']);
        }
        $dto = RiwayatPangkatDTO::fromRequest($validated);
        $this->service->storePangkat($dto);
        $docMsg = $request->hasFile('file_sk') ? ' Dokumen SK turut diunggah.' : '';
        return redirect(route('pegawai.show', $dto->pegawaiId) . '#tab-pangkat')
            ->with('success', 'Riwayat Pangkat baru berhasil ditambahkan.' . $docMsg);
    }

    public function editPangkat(RiwayatPangkat $riwayatPangkat)
    {
        return view('riwayat.edit-pangkat', [
            'riwayat' => $riwayatPangkat,
            'golonganOptions' => GolonganPangkat::where('is_active', true)->orderBy('golongan_ruang')->get(),
        ]);
    }

    public function updatePangkat(UpdatePangkatRequest $request, RiwayatPangkat $riwayatPangkat)
    {
        $validated = $request->validated();
        if ($request->hasFile('file_sk')) {
            $validated['file_pdf_path'] = $this->service->uploadSk($request->file('file_sk'), 'sk_pangkat', $riwayatPangkat->file_pdf_path, $riwayatPangkat->pegawai_id);
        }
        $dto = RiwayatPangkatDTO::fromRequest($validated);
        $this->service->updatePangkat($riwayatPangkat, $dto);
        $docMsg = $request->hasFile('file_sk') ? ' Dokumen SK telah diperbarui.' : '';
        return redirect(route('pegawai.show', $riwayatPangkat->pegawai_id) . '#tab-pangkat')
            ->with('success', 'Data Riwayat Pangkat berhasil diperbarui.' . $docMsg);
    }

    public function destroyPangkat(RiwayatPangkat $riwayatPangkat)
    {
        $pegawaiId = $riwayatPangkat->pegawai_id;
        $this->service->deletePangkat($riwayatPangkat);
        return redirect(route('pegawai.show', $pegawaiId) . '#tab-pangkat')
            ->with('success', 'Data Riwayat Pangkat berhasil dihapus.');
    }

    // --- JABATAN ---
    public function createJabatan(int $pegawaiId)
    {
        return view('riwayat.create-jabatan', [
            'pegawaiId' => $pegawaiId,
            'jabatanOptions' => $this->jabatanService->getAllOrderedByName(),
        ]);
    }

    public function storeJabatan(StoreJabatanRequest $request)
    {
        $validated = $request->validated();
        if ($request->hasFile('file_sk')) {
            $validated['file_pdf_path'] = $this->service->uploadSk($request->file('file_sk'), 'sk_jabatan', null, (int) $validated['pegawai_id']);
        }
        $dto = RiwayatJabatanDTO::fromRequest($validated);
        $this->service->storeJabatan($dto);
        $docMsg = $request->hasFile('file_sk') ? ' Dokumen SK turut diunggah.' : '';
        return redirect(route('pegawai.show', $dto->pegawaiId) . '#tab-jabatan')
            ->with('success', 'Riwayat Jabatan baru berhasil ditambahkan.' . $docMsg);
    }

    public function editJabatan(RiwayatJabatan $riwayatJabatan)
    {
        return view('riwayat.edit-jabatan', [
            'riwayat' => $riwayatJabatan,
            'jabatanOptions' => $this->jabatanService->getAllOrderedByName(),
        ]);
    }

    public function updateJabatan(UpdateJabatanRequest $request, RiwayatJabatan $riwayatJabatan)
    {
        $validated = $request->validated();
        if ($request->hasFile('file_sk')) {
            $validated['file_pdf_path'] = $this->service->uploadSk($request->file('file_sk'), 'sk_jabatan', $riwayatJabatan->file_pdf_path, $riwayatJabatan->pegawai_id);
        }
        $dto = RiwayatJabatanDTO::fromRequest($validated);
        $this->service->updateJabatan($riwayatJabatan, $dto);
        $docMsg = $request->hasFile('file_sk') ? ' Dokumen SK telah diperbarui.' : '';
        return redirect(route('pegawai.show', $riwayatJabatan->pegawai_id) . '#tab-jabatan')
            ->with('success', 'Data Riwayat Jabatan berhasil diperbarui.' . $docMsg);
    }

    public function destroyJabatan(RiwayatJabatan $riwayatJabatan)
    {
        $pegawaiId = $riwayatJabatan->pegawai_id;
        $this->service->deleteJabatan($riwayatJabatan);
        return redirect(route('pegawai.show', $pegawaiId) . '#tab-jabatan')
            ->with('success', 'Data Riwayat Jabatan berhasil dihapus.');
    }

    // --- KGB ---
    public function createKGB(int $pegawaiId)
    {
        $peg = Pegawai::findOrFail($pegawaiId);

        // Block if pegawai has active Penundaan KGB sanction
        $activeHukdisKgb = $peg->riwayatHukumanDisiplin
            ->filter(fn($h) => $h->isAktif()
                && $h->jenis_sanksi === JenisSanksi::PenundaanKgb);

        if ($activeHukdisKgb->isNotEmpty()) {
            $durasi = $activeHukdisKgb->sum(fn($h) => $h->durasi_tahun ?? 1);
            return redirect(route('pegawai.show', $pegawaiId) . '#tab-kgb')
                ->with('error', "Tidak dapat menambah Riwayat KGB — pegawai sedang menjalani sanksi Penundaan KGB selama {$durasi} tahun.");
        }

        $nextSalary = $this->kgbCalculationService->getNextKGBSalary($peg);
        return view('riwayat.create-kgb', [
            'pegawaiId' => $pegawaiId,
            'gajiPokok' => $peg->gaji_pokok,
            'calculatedGajiBaru' => $nextSalary['gaji_baru'] ?? null,
            'calculatedMkgTahun' => $nextSalary['masa_kerja_tahun'] ?? 0,
        ]);
    }

    public function storeKGB(StoreKGBRequest $request)
    {
        $validated = $request->validated();
        if ($request->hasFile('file_sk')) {
            $validated['file_pdf_path'] = $this->service->uploadSk($request->file('file_sk'), 'sk_kgb', null, (int) $validated['pegawai_id']);
        }
        $dto = RiwayatKgbDTO::fromRequest($validated);
        $this->service->storeKgb($dto);
        $docMsg = $request->hasFile('file_sk') ? ' Dokumen SK turut diunggah.' : '';
        return redirect(route('pegawai.show', $dto->pegawaiId) . '#tab-kgb')
            ->with('success', 'Riwayat KGB baru berhasil ditambahkan.' . $docMsg);
    }

    public function editKGB(RiwayatKgb $riwayatKgb)
    {
        return view('riwayat.edit-kgb', ['riwayat' => $riwayatKgb]);
    }

    public function updateKGB(UpdateKGBRequest $request, RiwayatKgb $riwayatKgb)
    {
        $validated = $request->validated();
        if ($request->hasFile('file_sk')) {
            $validated['file_pdf_path'] = $this->service->uploadSk($request->file('file_sk'), 'sk_kgb', $riwayatKgb->file_pdf_path, $riwayatKgb->pegawai_id);
        }
        $dto = RiwayatKgbDTO::fromRequest($validated);
        $this->service->updateKgb($riwayatKgb, $dto);
        $docMsg = $request->hasFile('file_sk') ? ' Dokumen SK telah diperbarui.' : '';
        return redirect(route('pegawai.show', $riwayatKgb->pegawai_id) . '#tab-kgb')
            ->with('success', 'Data Riwayat KGB berhasil diperbarui.' . $docMsg);
    }

    public function destroyKGB(RiwayatKgb $riwayatKgb)
    {
        $pegawaiId = $riwayatKgb->pegawai_id;
        $this->service->deleteKgb($riwayatKgb);
        return redirect(route('pegawai.show', $pegawaiId) . '#tab-kgb')
            ->with('success', 'Data Riwayat KGB berhasil dihapus.');
    }

    // --- HUKUMAN DISIPLIN ---
    public function createHukuman(int $pegawaiId)
    {
        $pegawai = Pegawai::with(['riwayatPangkat'])->findOrFail($pegawaiId);

        // Only show golongan lower than current pangkat (exclude demotion records)
        $currentPangkat = $pegawai->riwayatPangkat
            ->where('is_hukdis_demotion', false)
            ->sortByDesc('tmt_pangkat')
            ->first();
        $allGolongan = GolonganPangkat::where('is_active', true)->orderBy('golongan_ruang')->get();
        $golonganOptions = $currentPangkat && $currentPangkat->golongan
            ? $allGolongan->filter(fn($g) => $g->golongan_ruang < $currentPangkat->golongan->golongan_ruang)
            : $allGolongan;

        return view('riwayat.create-hukuman', [
            'pegawaiId' => $pegawaiId,
            'tingkatOptions' => TingkatHukuman::cases(),
            'sanksiOptions' => JenisSanksi::cases(),
            'golonganOptions' => $golonganOptions,
            'jabatanOptions' => $this->jabatanService->getAllOrderedByName(),
        ]);
    }

    public function storeHukuman(StoreHukumanRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('file_sk')) {
            $validated['file_pdf_path'] = $this->service->uploadHukumanSk($request->file('file_sk'), null, (int) $validated['pegawai_id']);
        }

        $dto = RiwayatHukumanDisiplinDTO::fromRequest($validated);
        $this->service->storeHukuman($dto);
        $docMsg = $request->hasFile('file_sk') ? ' Dokumen SK turut diunggah.' : '';
        return redirect(route('pegawai.show', $dto->pegawaiId) . '#tab-hukuman')
            ->with('success', 'Riwayat Hukuman Disiplin baru berhasil ditambahkan.' . $docMsg);
    }

    public function editHukuman(RiwayatHukumanDisiplin $riwayatHukuman)
    {
        $pegawai = Pegawai::with(['riwayatPangkat'])->findOrFail($riwayatHukuman->pegawai_id);

        // Only show golongan lower than current non-demotion pangkat
        $currentPangkat = $pegawai->riwayatPangkat
            ->where('is_hukdis_demotion', false)
            ->sortByDesc('tmt_pangkat')
            ->first();
        $allGolongan = GolonganPangkat::where('is_active', true)->orderBy('golongan_ruang')->get();
        $golonganOptions = $currentPangkat && $currentPangkat->golongan
            ? $allGolongan->filter(fn($g) => $g->golongan_ruang < $currentPangkat->golongan->golongan_ruang)
            : $allGolongan;

        // Look up existing demotion values from riwayat tables
        $currentDemotionGolongan = null;
        $currentDemotionJabatanId = null;

        if ($riwayatHukuman->jenis_sanksi === JenisSanksi::PenurunanPangkat) {
            $demotionPangkat = RiwayatPangkat::where('pegawai_id', $riwayatHukuman->pegawai_id)
                ->where('is_hukdis_demotion', true)
                ->where('tmt_pangkat', $riwayatHukuman->tmt_hukuman)
                ->first();
            $currentDemotionGolongan = $demotionPangkat?->golongan_id;
        }

        if (in_array($riwayatHukuman->jenis_sanksi, [JenisSanksi::PenurunanJabatan, JenisSanksi::PembebasanJabatan])) {
            $demotionJabatan = RiwayatJabatan::where('pegawai_id', $riwayatHukuman->pegawai_id)
                ->where('is_hukdis_demotion', true)
                ->where('tmt_jabatan', $riwayatHukuman->tmt_hukuman)
                ->first();
            $currentDemotionJabatanId = $demotionJabatan?->jabatan_id;
        }

        return view('riwayat.edit-hukuman', [
            'riwayat' => $riwayatHukuman,
            'tingkatOptions' => TingkatHukuman::cases(),
            'sanksiOptions' => JenisSanksi::cases(),
            'golonganOptions' => $golonganOptions,
            'jabatanOptions' => $this->jabatanService->getAllOrderedByName(),
            'currentDemotionGolongan' => $currentDemotionGolongan,
            'currentDemotionJabatanId' => $currentDemotionJabatanId,
        ]);
    }

    public function updateHukuman(UpdateHukumanRequest $request, RiwayatHukumanDisiplin $riwayatHukuman)
    {
        $validated = $request->validated();

        if ($request->hasFile('file_sk')) {
            $validated['file_pdf_path'] = $this->service->uploadHukumanSk($request->file('file_sk'), $riwayatHukuman->file_pdf_path, $riwayatHukuman->pegawai_id);
        }

        $dto = RiwayatHukumanDisiplinDTO::fromRequest($validated);
        $this->service->updateHukuman($riwayatHukuman, $dto);
        $docMsg = $request->hasFile('file_sk') ? ' Dokumen SK telah diperbarui.' : '';
        return redirect(route('pegawai.show', $riwayatHukuman->pegawai_id) . '#tab-hukuman')
            ->with('success', 'Data Riwayat Hukuman Disiplin berhasil diperbarui.' . $docMsg);
    }

    public function destroyHukuman(RiwayatHukumanDisiplin $riwayatHukuman)
    {
        $pegawaiId = $riwayatHukuman->pegawai_id;
        $this->service->deleteHukuman($riwayatHukuman);
        return redirect(route('pegawai.show', $pegawaiId) . '#tab-hukuman')
            ->with('success', 'Data Riwayat Hukuman Disiplin berhasil dihapus.');
    }

    public function pulihkanHukuman(\Illuminate\Http\Request $request, RiwayatHukumanDisiplin $riwayatHukuman)
    {
        $validated = $request->validate([
            'nomor_sk_pemulihan' => 'required|string|max:255',
            'tanggal_pemulihan' => 'required|date',
            'file_sk_pemulihan' => 'nullable|file|mimes:pdf|max:5120',
            'restoration_golongan_id' => 'nullable|integer|exists:golongan_pangkats,id',
            'restoration_jabatan_id' => 'nullable|integer|exists:jabatans,id',
        ]);

        $filePath = null;
        if ($request->hasFile('file_sk_pemulihan')) {
            $filePath = $this->service->uploadSk($request->file('file_sk_pemulihan'), 'sk_pemulihan', null, $riwayatHukuman->pegawai_id);
        }

        $this->service->pulihkanHukuman(
            $riwayatHukuman,
            $validated['nomor_sk_pemulihan'],
            $validated['tanggal_pemulihan'],
            $filePath,
            isset($validated['restoration_golongan_id']) ? (int) $validated['restoration_golongan_id'] : null,
            isset($validated['restoration_jabatan_id']) ? (int) $validated['restoration_jabatan_id'] : null,
        );

        return redirect(route('pegawai.show', $riwayatHukuman->pegawai_id) . '#tab-hukuman')
            ->with('success', 'Hukuman Disiplin berhasil dipulihkan dan status telah diperbarui.');
    }

    // --- PENDIDIKAN ---
    public function createPendidikan(int $pegawaiId)
    {
        return view('riwayat.create-pendidikan', ['pegawaiId' => $pegawaiId]);
    }

    public function storePendidikan(StorePendidikanRequest $request)
    {
        $validated = $request->validated();
        if ($request->hasFile('file_sk')) {
            $validated['file_pdf_path'] = $this->service->uploadSk($request->file('file_sk'), 'sk_pendidikan', null, (int) $validated['pegawai_id']);
        }
        $dto = RiwayatPendidikanDTO::fromRequest($validated);
        $this->service->storePendidikan($dto);
        $docMsg = $request->hasFile('file_sk') ? ' Dokumen ijazah turut diunggah.' : '';
        return redirect(route('pegawai.show', $dto->pegawaiId) . '#tab-pendidikan')
            ->with('success', 'Riwayat Pendidikan baru berhasil ditambahkan.' . $docMsg);
    }

    public function editPendidikan(RiwayatPendidikan $riwayatPendidikan)
    {
        return view('riwayat.edit-pendidikan', ['riwayat' => $riwayatPendidikan]);
    }

    public function updatePendidikan(UpdatePendidikanRequest $request, RiwayatPendidikan $riwayatPendidikan)
    {
        $validated = $request->validated();
        if ($request->hasFile('file_sk')) {
            $validated['file_pdf_path'] = $this->service->uploadSk($request->file('file_sk'), 'sk_pendidikan', $riwayatPendidikan->file_pdf_path, $riwayatPendidikan->pegawai_id);
        }
        $dto = RiwayatPendidikanDTO::fromRequest($validated);
        $this->service->updatePendidikan($riwayatPendidikan, $dto);
        $docMsg = $request->hasFile('file_sk') ? ' Dokumen ijazah telah diperbarui.' : '';
        return redirect(route('pegawai.show', $riwayatPendidikan->pegawai_id) . '#tab-pendidikan')
            ->with('success', 'Data Riwayat Pendidikan berhasil diperbarui.' . $docMsg);
    }

    public function destroyPendidikan(RiwayatPendidikan $riwayatPendidikan)
    {
        $pegawaiId = $riwayatPendidikan->pegawai_id;
        $this->service->deletePendidikan($riwayatPendidikan);
        return redirect(route('pegawai.show', $pegawaiId) . '#tab-pendidikan')
            ->with('success', 'Data Riwayat Pendidikan berhasil dihapus.');
    }

    // --- LATIHAN JABATAN ---
    public function createLatihan(int $pegawaiId)
    {
        return view('riwayat.create-latihan', ['pegawaiId' => $pegawaiId]);
    }

    public function storeLatihan(StoreLatihanRequest $request)
    {
        $validated = $request->validated();
        if ($request->hasFile('file_sk')) {
            $validated['file_pdf_path'] = $this->service->uploadSk($request->file('file_sk'), 'sk_latihan', null, (int) $validated['pegawai_id']);
        }
        $dto = RiwayatLatihanJabatanDTO::fromRequest($validated);
        $this->service->storeLatihan($dto);
        $docMsg = $request->hasFile('file_sk') ? ' Dokumen sertifikat turut diunggah.' : '';
        return redirect(route('pegawai.show', $dto->pegawaiId) . '#tab-latihan')
            ->with('success', 'Riwayat Latihan Jabatan baru berhasil ditambahkan.' . $docMsg);
    }

    public function editLatihan(RiwayatLatihanJabatan $riwayatLatihan)
    {
        return view('riwayat.edit-latihan', ['riwayat' => $riwayatLatihan]);
    }

    public function updateLatihan(UpdateLatihanRequest $request, RiwayatLatihanJabatan $riwayatLatihan)
    {
        $validated = $request->validated();
        if ($request->hasFile('file_sk')) {
            $validated['file_pdf_path'] = $this->service->uploadSk($request->file('file_sk'), 'sk_latihan', $riwayatLatihan->file_pdf_path, $riwayatLatihan->pegawai_id);
        }
        $dto = RiwayatLatihanJabatanDTO::fromRequest($validated);
        $this->service->updateLatihan($riwayatLatihan, $dto);
        $docMsg = $request->hasFile('file_sk') ? ' Dokumen sertifikat telah diperbarui.' : '';
        return redirect(route('pegawai.show', $riwayatLatihan->pegawai_id) . '#tab-latihan')
            ->with('success', 'Data Riwayat Latihan Jabatan berhasil diperbarui.' . $docMsg);
    }

    public function destroyLatihan(RiwayatLatihanJabatan $riwayatLatihan)
    {
        $pegawaiId = $riwayatLatihan->pegawai_id;
        $this->service->deleteLatihan($riwayatLatihan);
        return redirect(route('pegawai.show', $pegawaiId) . '#tab-latihan')
            ->with('success', 'Data Riwayat Latihan Jabatan berhasil dihapus.');
    }

    // --- PENILAIAN KINERJA (SKP) ---
    public function createSKP(int $pegawaiId)
    {
        return view('riwayat.create-skp', ['pegawaiId' => $pegawaiId]);
    }

    public function storeSKP(StoreSKPRequest $request)
    {
        $validated = $request->validated();
        if ($request->hasFile('file_sk')) {
            $validated['file_pdf_path'] = $this->service->uploadSk($request->file('file_sk'), 'sk_skp', null, (int) $validated['pegawai_id']);
        }
        $dto = PenilaianKinerjaDTO::fromRequest($validated);
        $this->service->storeSKP($dto);
        $docMsg = $request->hasFile('file_sk') ? ' Dokumen SKP turut diunggah.' : '';
        return redirect(route('pegawai.show', $dto->pegawaiId) . '#tab-skp')
            ->with('success', 'Penilaian Kinerja (SKP) baru berhasil ditambahkan.' . $docMsg);
    }

    public function editSKP(PenilaianKinerja $penilaianKinerja)
    {
        return view('riwayat.edit-skp', ['riwayat' => $penilaianKinerja]);
    }

    public function updateSKP(UpdateSKPRequest $request, PenilaianKinerja $penilaianKinerja)
    {
        $validated = $request->validated();
        if ($request->hasFile('file_sk')) {
            $validated['file_pdf_path'] = $this->service->uploadSk($request->file('file_sk'), 'sk_skp', $penilaianKinerja->file_pdf_path, $penilaianKinerja->pegawai_id);
        }
        $dto = PenilaianKinerjaDTO::fromRequest($validated);
        $this->service->updateSKP($penilaianKinerja, $dto);
        $docMsg = $request->hasFile('file_sk') ? ' Dokumen SKP telah diperbarui.' : '';
        return redirect(route('pegawai.show', $penilaianKinerja->pegawai_id) . '#tab-skp')
            ->with('success', 'Data Penilaian Kinerja (SKP) berhasil diperbarui.' . $docMsg);
    }

    public function destroySKP(PenilaianKinerja $penilaianKinerja)
    {
        $pegawaiId = $penilaianKinerja->pegawai_id;
        $this->service->deleteSKP($penilaianKinerja);
        return redirect(route('pegawai.show', $pegawaiId) . '#tab-skp')
            ->with('success', 'Data Penilaian Kinerja (SKP) berhasil dihapus.');
    }
}
