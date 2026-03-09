<?php

namespace App\Http\Controllers;

use App\Enums\GolonganRuang;
use App\Enums\JenisSanksi;
use App\Enums\TingkatHukuman;
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
            ->filter(fn($h) => in_array($h->jenis_sanksi, [
                    JenisSanksi::PenundaanPangkat,
                    JenisSanksi::PenurunanPangkat,
                    JenisSanksi::PembebasanJabatan,
                    JenisSanksi::Pemberhentian,
                ])
                && ($h->tmt_selesai_hukuman === null || $h->tmt_selesai_hukuman->gte(today())));

        if ($activeBlocking->isNotEmpty()) {
            $notes = $activeBlocking->map(fn($h) => $h->jenis_sanksi->label())->implode(', ');
            return redirect()->route('pegawai.show', $pegawaiId)
                ->with('error', "Tidak dapat menambah Pangkat — pegawai sedang menjalani sanksi: {$notes}.");
        }

        return view('riwayat.create-pangkat', [
            'pegawaiId' => $pegawaiId,
            'golonganOptions' => GolonganRuang::cases(),
            'currentPangkat' => $currentPangkat,
        ]);
    }

    public function storePangkat(StorePangkatRequest $request)
    {
        $dto = RiwayatPangkatDTO::fromRequest($request->validated());
        $this->service->storePangkat($dto);
        return redirect()->route('pegawai.show', $dto->pegawaiId)->with('success', 'Riwayat Pangkat berhasil ditambahkan.');
    }

    public function editPangkat(RiwayatPangkat $riwayatPangkat)
    {
        return view('riwayat.edit-pangkat', [
            'riwayat' => $riwayatPangkat,
            'golonganOptions' => GolonganRuang::cases(),
        ]);
    }

    public function updatePangkat(UpdatePangkatRequest $request, RiwayatPangkat $riwayatPangkat)
    {
        $dto = RiwayatPangkatDTO::fromRequest($request->validated());
        $this->service->updatePangkat($riwayatPangkat, $dto);
        return redirect()->route('pegawai.show', $riwayatPangkat->pegawai_id)->with('success', 'Riwayat Pangkat berhasil diperbarui.');
    }

    public function destroyPangkat(RiwayatPangkat $riwayatPangkat)
    {
        $pegawaiId = $riwayatPangkat->pegawai_id;
        $this->service->deletePangkat($riwayatPangkat);
        return redirect()->route('pegawai.show', $pegawaiId)->with('success', 'Berhasil dihapus.');
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
        $dto = RiwayatJabatanDTO::fromRequest($request->validated());
        $this->service->storeJabatan($dto);
        return redirect()->route('pegawai.show', $dto->pegawaiId)->with('success', 'Riwayat Jabatan berhasil ditambahkan.');
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
        $dto = RiwayatJabatanDTO::fromRequest($request->validated());
        $this->service->updateJabatan($riwayatJabatan, $dto);
        return redirect()->route('pegawai.show', $riwayatJabatan->pegawai_id)->with('success', 'Riwayat Jabatan berhasil diperbarui.');
    }

    public function destroyJabatan(RiwayatJabatan $riwayatJabatan)
    {
        $pegawaiId = $riwayatJabatan->pegawai_id;
        $this->service->deleteJabatan($riwayatJabatan);
        return redirect()->route('pegawai.show', $pegawaiId)->with('success', 'Berhasil dihapus.');
    }

    // --- KGB ---
    public function createKGB(int $pegawaiId)
    {
        $peg = Pegawai::findOrFail($pegawaiId);

        // Block if pegawai has active Penundaan KGB sanction
        $activeHukdisKgb = $peg->riwayatHukumanDisiplin
            ->filter(fn($h) => $h->jenis_sanksi === JenisSanksi::PenundaanKgb
                && ($h->tmt_selesai_hukuman === null || $h->tmt_selesai_hukuman->gte(today())));

        if ($activeHukdisKgb->isNotEmpty()) {
            $durasi = $activeHukdisKgb->sum(fn($h) => $h->durasi_tahun ?? 1);
            return redirect()->route('pegawai.show', $pegawaiId)
                ->with('error', "Tidak dapat menambah KGB — pegawai sedang menjalani sanksi Penundaan KGB selama {$durasi} tahun.");
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
        $dto = RiwayatKgbDTO::fromRequest($request->validated());
        $this->service->storeKgb($dto);
        return redirect()->route('pegawai.show', $dto->pegawaiId)->with('success', 'Riwayat KGB berhasil ditambahkan.');
    }

    public function editKGB(RiwayatKgb $riwayatKgb)
    {
        return view('riwayat.edit-kgb', ['riwayat' => $riwayatKgb]);
    }

    public function updateKGB(UpdateKGBRequest $request, RiwayatKgb $riwayatKgb)
    {
        $dto = RiwayatKgbDTO::fromRequest($request->validated());
        $this->service->updateKgb($riwayatKgb, $dto);
        return redirect()->route('pegawai.show', $riwayatKgb->pegawai_id)->with('success', 'Riwayat KGB berhasil diperbarui.');
    }

    public function destroyKGB(RiwayatKgb $riwayatKgb)
    {
        $pegawaiId = $riwayatKgb->pegawai_id;
        $this->service->deleteKgb($riwayatKgb);
        return redirect()->route('pegawai.show', $pegawaiId)->with('success', 'Berhasil dihapus.');
    }

    // --- HUKUMAN DISIPLIN ---
    public function createHukuman(int $pegawaiId)
    {
        return view('riwayat.create-hukuman', [
            'pegawaiId' => $pegawaiId,
            'tingkatOptions' => TingkatHukuman::cases(),
            'sanksiOptions' => JenisSanksi::cases(),
        ]);
    }

    public function storeHukuman(StoreHukumanRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('file_sk')) {
            $validated['file_pdf_path'] = $this->service->uploadHukumanSk($request->file('file_sk'));
        }

        $dto = RiwayatHukumanDisiplinDTO::fromRequest($validated);
        $this->service->storeHukuman($dto);
        return redirect()->route('pegawai.show', $dto->pegawaiId)->with('success', 'Riwayat Hukuman berhasil ditambahkan.');
    }

    public function editHukuman(RiwayatHukumanDisiplin $riwayatHukuman)
    {
        return view('riwayat.edit-hukuman', [
            'riwayat' => $riwayatHukuman,
            'tingkatOptions' => TingkatHukuman::cases(),
            'sanksiOptions' => JenisSanksi::cases(),
        ]);
    }

    public function updateHukuman(UpdateHukumanRequest $request, RiwayatHukumanDisiplin $riwayatHukuman)
    {
        $validated = $request->validated();

        if ($request->hasFile('file_sk')) {
            $validated['file_pdf_path'] = $this->service->uploadHukumanSk($request->file('file_sk'), $riwayatHukuman->file_pdf_path);
        }

        $dto = RiwayatHukumanDisiplinDTO::fromRequest($validated);
        $this->service->updateHukuman($riwayatHukuman, $dto);
        return redirect()->route('pegawai.show', $riwayatHukuman->pegawai_id)->with('success', 'Riwayat Hukuman berhasil diperbarui.');
    }

    public function destroyHukuman(RiwayatHukumanDisiplin $riwayatHukuman)
    {
        $pegawaiId = $riwayatHukuman->pegawai_id;
        $this->service->deleteHukuman($riwayatHukuman);
        return redirect()->route('pegawai.show', $pegawaiId)->with('success', 'Berhasil dihapus.');
    }

    // --- PENDIDIKAN ---
    public function createPendidikan(int $pegawaiId)
    {
        return view('riwayat.create-pendidikan', ['pegawaiId' => $pegawaiId]);
    }

    public function storePendidikan(StorePendidikanRequest $request)
    {
        $dto = RiwayatPendidikanDTO::fromRequest($request->validated());
        $this->service->storePendidikan($dto);
        return redirect()->route('pegawai.show', $dto->pegawaiId)->with('success', 'Riwayat Pendidikan berhasil ditambahkan.');
    }

    public function editPendidikan(RiwayatPendidikan $riwayatPendidikan)
    {
        return view('riwayat.edit-pendidikan', ['riwayat' => $riwayatPendidikan]);
    }

    public function updatePendidikan(UpdatePendidikanRequest $request, RiwayatPendidikan $riwayatPendidikan)
    {
        $dto = RiwayatPendidikanDTO::fromRequest($request->validated());
        $this->service->updatePendidikan($riwayatPendidikan, $dto);
        return redirect()->route('pegawai.show', $riwayatPendidikan->pegawai_id)->with('success', 'Riwayat Pendidikan berhasil diperbarui.');
    }

    public function destroyPendidikan(RiwayatPendidikan $riwayatPendidikan)
    {
        $pegawaiId = $riwayatPendidikan->pegawai_id;
        $this->service->deletePendidikan($riwayatPendidikan);
        return redirect()->route('pegawai.show', $pegawaiId)->with('success', 'Berhasil dihapus.');
    }

    // --- LATIHAN JABATAN ---
    public function createLatihan(int $pegawaiId)
    {
        return view('riwayat.create-latihan', ['pegawaiId' => $pegawaiId]);
    }

    public function storeLatihan(StoreLatihanRequest $request)
    {
        $dto = RiwayatLatihanJabatanDTO::fromRequest($request->validated());
        $this->service->storeLatihan($dto);
        return redirect()->route('pegawai.show', $dto->pegawaiId)->with('success', 'Riwayat Latihan berhasil ditambahkan.');
    }

    public function editLatihan(RiwayatLatihanJabatan $riwayatLatihan)
    {
        return view('riwayat.edit-latihan', ['riwayat' => $riwayatLatihan]);
    }

    public function updateLatihan(UpdateLatihanRequest $request, RiwayatLatihanJabatan $riwayatLatihan)
    {
        $dto = RiwayatLatihanJabatanDTO::fromRequest($request->validated());
        $this->service->updateLatihan($riwayatLatihan, $dto);
        return redirect()->route('pegawai.show', $riwayatLatihan->pegawai_id)->with('success', 'Riwayat Latihan berhasil diperbarui.');
    }

    public function destroyLatihan(RiwayatLatihanJabatan $riwayatLatihan)
    {
        $pegawaiId = $riwayatLatihan->pegawai_id;
        $this->service->deleteLatihan($riwayatLatihan);
        return redirect()->route('pegawai.show', $pegawaiId)->with('success', 'Berhasil dihapus.');
    }

    // --- PENILAIAN KINERJA (SKP) ---
    public function createSKP(int $pegawaiId)
    {
        return view('riwayat.create-skp', ['pegawaiId' => $pegawaiId]);
    }

    public function storeSKP(StoreSKPRequest $request)
    {
        $dto = PenilaianKinerjaDTO::fromRequest($request->validated());
        $this->service->storeSKP($dto);
        return redirect()->route('pegawai.show', $dto->pegawaiId)->with('success', 'Penilaian Kinerja berhasil ditambahkan.');
    }

    public function editSKP(PenilaianKinerja $penilaianKinerja)
    {
        return view('riwayat.edit-skp', ['riwayat' => $penilaianKinerja]);
    }

    public function updateSKP(UpdateSKPRequest $request, PenilaianKinerja $penilaianKinerja)
    {
        $dto = PenilaianKinerjaDTO::fromRequest($request->validated());
        $this->service->updateSKP($penilaianKinerja, $dto);
        return redirect()->route('pegawai.show', $penilaianKinerja->pegawai_id)->with('success', 'Penilaian Kinerja berhasil diperbarui.');
    }

    public function destroySKP(PenilaianKinerja $penilaianKinerja)
    {
        $pegawaiId = $penilaianKinerja->pegawai_id;
        $this->service->deleteSKP($penilaianKinerja);
        return redirect()->route('pegawai.show', $pegawaiId)->with('success', 'Berhasil dihapus.');
    }
}
