<?php

namespace App\Services;

use App\Enums\GolonganRuang;
use App\Enums\JenisSanksi;
use App\Enums\StatusHukdis;

use App\Models\Pegawai;
use App\Models\RiwayatPangkat;
use App\Models\RiwayatJabatan;
use App\Models\RiwayatKgb;
use App\Models\RiwayatHukumanDisiplin;
use App\Models\RiwayatPendidikan;
use App\Models\RiwayatLatihanJabatan;
use App\Models\PenilaianKinerja;

use App\DTOs\Riwayat\RiwayatPangkatDTO;
use App\DTOs\Riwayat\RiwayatJabatanDTO;
use App\DTOs\Riwayat\RiwayatKgbDTO;
use App\DTOs\Riwayat\RiwayatHukumanDisiplinDTO;
use App\DTOs\Riwayat\RiwayatPendidikanDTO;
use App\DTOs\Riwayat\RiwayatLatihanJabatanDTO;
use App\DTOs\Riwayat\PenilaianKinerjaDTO;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class RiwayatService
{
    public function __construct(
        private DocumentUploadService $uploadService,
        private KGBCalculationService $kgbService,
    ) {}

    /**
     * Generic SK file upload with optional old file deletion.
     */
    public function uploadSk(UploadedFile $file, string $subfolder, ?string $oldPath = null): string
    {
        if ($oldPath) {
            $this->uploadService->delete($oldPath);
        }
        return $this->uploadService->upload($file, $subfolder);
    }

    // --- PANGKAT ---
    public function storePangkat(RiwayatPangkatDTO $dto): RiwayatPangkat
    {
        return DB::transaction(fn () => RiwayatPangkat::create($dto->toArray()));
    }

    public function updatePangkat(RiwayatPangkat $riwayat, RiwayatPangkatDTO $dto): bool
    {
        return DB::transaction(fn () => $riwayat->update($dto->toArray()));
    }

    public function deletePangkat(RiwayatPangkat $riwayat): bool
    {
        return DB::transaction(function () use ($riwayat) {
            if ($riwayat->file_pdf_path) {
                $this->uploadService->delete($riwayat->file_pdf_path);
            }
            return $riwayat->delete();
        });
    }

    // --- JABATAN ---
    public function storeJabatan(RiwayatJabatanDTO $dto): RiwayatJabatan
    {
        return DB::transaction(fn () => RiwayatJabatan::create($dto->toArray()));
    }

    public function updateJabatan(RiwayatJabatan $riwayat, RiwayatJabatanDTO $dto): bool
    {
        return DB::transaction(fn () => $riwayat->update($dto->toArray()));
    }

    public function deleteJabatan(RiwayatJabatan $riwayat): bool
    {
        return DB::transaction(function () use ($riwayat) {
            if ($riwayat->file_pdf_path) {
                $this->uploadService->delete($riwayat->file_pdf_path);
            }
            return $riwayat->delete();
        });
    }

    // --- KGB ---
    public function storeKgb(RiwayatKgbDTO $dto): RiwayatKgb
    {
        return DB::transaction(function () use ($dto) {
            $riwayat = RiwayatKgb::create($dto->toArray());

            // Update Base Salary
            Pegawai::where('id', $dto->pegawaiId)->update(['gaji_pokok' => $dto->gajiBaru]);

            return $riwayat;
        });
    }

    public function updateKgb(RiwayatKgb $riwayat, RiwayatKgbDTO $dto): bool
    {
        return DB::transaction(function () use ($riwayat, $dto) {
            $result = $riwayat->update($dto->toArray());

            // Sync gaji_pokok if this is the latest KGB
            $pegawaiId = $riwayat->pegawai_id;
            $latestKgb = RiwayatKgb::where('pegawai_id', $pegawaiId)
                ->orderByDesc('tmt_kgb')->first();
            if ($latestKgb && $latestKgb->id === $riwayat->id) {
                Pegawai::where('id', $pegawaiId)->update(['gaji_pokok' => $dto->gajiBaru]);
            }

            return $result;
        });
    }

    public function deleteKgb(RiwayatKgb $riwayat): bool
    {
        return DB::transaction(function () use ($riwayat) {
            $pegawaiId = $riwayat->pegawai_id;
            if ($riwayat->file_pdf_path) {
                $this->uploadService->delete($riwayat->file_pdf_path);
            }
            $riwayat->delete();

            // Revert gaji_pokok to previous KGB's gaji_baru, or 0 if none left
            $previousKgb = RiwayatKgb::where('pegawai_id', $pegawaiId)
                ->orderByDesc('tmt_kgb')->first();
            $newGaji = $previousKgb ? $previousKgb->gaji_baru : 0;
            Pegawai::where('id', $pegawaiId)->update(['gaji_pokok' => $newGaji]);

            return true;
        });
    }

    // --- HUKUMAN DISIPLIN ---
    public function storeHukuman(RiwayatHukumanDisiplinDTO $dto): RiwayatHukumanDisiplin
    {
        return DB::transaction(function () use ($dto) {
            $data = $dto->toArray();
            $data['status'] = StatusHukdis::Aktif->value;
            if ($dto->filePdfPath) {
                $data['file_pdf_path'] = $dto->filePdfPath;
            }

            $hukuman = RiwayatHukumanDisiplin::create($data);

            // Type 2: Hard-update — insert demotion record into riwayat tables
            $jenisSanksi = JenisSanksi::from($dto->jenisSanksi);
            $this->applyType2Demotion($jenisSanksi, $hukuman, $dto);

            return $hukuman;
        });
    }

    /**
     * Apply Type 2 demotion: insert new riwayat record with is_hukdis_demotion=true
     */
    private function applyType2Demotion(JenisSanksi $jenis, RiwayatHukumanDisiplin $hukuman, RiwayatHukumanDisiplinDTO $dto): void
    {
        $pegawaiId = $hukuman->pegawai_id;

        if ($jenis === JenisSanksi::PenurunanPangkat && $dto->demotionGolonganRuang !== null) {
            RiwayatPangkat::create([
                'pegawai_id' => $pegawaiId,
                'golongan_ruang' => $dto->demotionGolonganRuang,
                'nomor_sk' => $dto->nomorSk,
                'tmt_pangkat' => $dto->tmtHukuman,
                'tanggal_sk' => $dto->tanggalSk ?? $dto->tmtHukuman,
                'is_hukdis_demotion' => true,
            ]);

            // Update gaji_pokok based on the new (lower) pangkat
            $this->recalculateGajiPokok($pegawaiId);
        }

        if (in_array($jenis, [JenisSanksi::PenurunanJabatan, JenisSanksi::PembebasanJabatan]) && $dto->demotionJabatanId !== null) {
            RiwayatJabatan::create([
                'pegawai_id' => $pegawaiId,
                'jabatan_id' => $dto->demotionJabatanId,
                'nomor_sk' => $dto->nomorSk,
                'tmt_jabatan' => $dto->tmtHukuman,
                'tanggal_sk' => $dto->tanggalSk ?? $dto->tmtHukuman,
                'is_hukdis_demotion' => true,
            ]);
        }
    }

    /**
     * Recalculate gaji_pokok based on the latest pangkat record.
     */
    private function recalculateGajiPokok(int $pegawaiId): void
    {
        $pegawai = Pegawai::find($pegawaiId);
        if (!$pegawai) return;

        $latestPangkat = RiwayatPangkat::where('pegawai_id', $pegawaiId)
            ->orderByDesc('tmt_pangkat')
            ->first();

        if (!$latestPangkat) return;

        $golongan = $latestPangkat->golongan_ruang;
        $tmtPangkat = $latestPangkat->tmt_pangkat;
        $today = today();
        $totalMonths = (($today->year - $tmtPangkat->year) * 12) + $today->month - $tmtPangkat->month;
        $masaKerjaTahun = intdiv($totalMonths, 12);

        $gajiBaru = $this->kgbService->calculateNewSalary($golongan, $masaKerjaTahun);
        if ($gajiBaru !== null) {
            $pegawai->update(['gaji_pokok' => $gajiBaru]);
        }
    }

    public function updateHukuman(RiwayatHukumanDisiplin $riwayat, RiwayatHukumanDisiplinDTO $dto): bool
    {
        return DB::transaction(function () use ($riwayat, $dto) {
            $data = $dto->toArray();
            if ($dto->filePdfPath) {
                $data['file_pdf_path'] = $dto->filePdfPath;
            }
            return $riwayat->update($data);
        });
    }

    public function deleteHukuman(RiwayatHukumanDisiplin $riwayat): bool
    {
        return DB::transaction(function () use ($riwayat) {
            // Revert Type 2 demotion records
            $jenis = $riwayat->jenis_sanksi;
            $pegawaiId = $riwayat->pegawai_id;

            if ($jenis === JenisSanksi::PenurunanPangkat) {
                RiwayatPangkat::where('pegawai_id', $pegawaiId)
                    ->where('is_hukdis_demotion', true)
                    ->where('tmt_pangkat', $riwayat->tmt_hukuman)
                    ->delete();

                // Recalculate gaji_pokok from the now-latest pangkat
                $this->recalculateGajiPokok($pegawaiId);
            }

            if (in_array($jenis, [JenisSanksi::PenurunanJabatan, JenisSanksi::PembebasanJabatan])) {
                RiwayatJabatan::where('pegawai_id', $pegawaiId)
                    ->where('is_hukdis_demotion', true)
                    ->where('tmt_jabatan', $riwayat->tmt_hukuman)
                    ->delete();
            }

            if ($riwayat->file_pdf_path) {
                $this->uploadService->delete($riwayat->file_pdf_path);
            }
            if ($riwayat->file_sk_pemulihan_path) {
                $this->uploadService->delete($riwayat->file_sk_pemulihan_path);
            }
            return $riwayat->delete();
        });
    }

    /**
     * Restore (Pemulihan) a hukuman disiplin.
     * For Type 2 sanctions, insert restoration records into riwayat tables.
     */
    public function pulihkanHukuman(
        RiwayatHukumanDisiplin $hukuman,
        string $nomorSkPemulihan,
        string $tanggalPemulihan,
        ?string $fileSkPemulihanPath = null,
        ?int $restorationGolonganRuang = null,
        ?int $restorationJabatanId = null,
    ): bool {
        return DB::transaction(function () use ($hukuman, $nomorSkPemulihan, $tanggalPemulihan, $fileSkPemulihanPath, $restorationGolonganRuang, $restorationJabatanId) {
            $hukuman->update([
                'status' => StatusHukdis::Dipulihkan->value,
                'nomor_sk_pemulihan' => $nomorSkPemulihan,
                'tanggal_pemulihan' => $tanggalPemulihan,
                'file_sk_pemulihan_path' => $fileSkPemulihanPath,
            ]);

            $jenis = $hukuman->jenis_sanksi;
            $pegawaiId = $hukuman->pegawai_id;

            // Type 2 restoration: insert new riwayat record to restore position
            if ($jenis === JenisSanksi::PenurunanPangkat && $restorationGolonganRuang !== null) {
                RiwayatPangkat::create([
                    'pegawai_id' => $pegawaiId,
                    'golongan_ruang' => $restorationGolonganRuang,
                    'nomor_sk' => $nomorSkPemulihan,
                    'tmt_pangkat' => $tanggalPemulihan,
                    'tanggal_sk' => $tanggalPemulihan,
                ]);

                // Recalculate gaji_pokok based on restored pangkat
                $this->recalculateGajiPokok($pegawaiId);
            }

            if (in_array($jenis, [JenisSanksi::PenurunanJabatan, JenisSanksi::PembebasanJabatan]) && $restorationJabatanId !== null) {
                RiwayatJabatan::create([
                    'pegawai_id' => $pegawaiId,
                    'jabatan_id' => $restorationJabatanId,
                    'nomor_sk' => $nomorSkPemulihan,
                    'tmt_jabatan' => $tanggalPemulihan,
                    'tanggal_sk' => $tanggalPemulihan,
                ]);
            }

            return true;
        });
    }

    public function uploadHukumanSk(UploadedFile $file, ?string $oldPath = null): string
    {
        return $this->uploadSk($file, 'sk_hukuman', $oldPath);
    }

    // --- PENDIDIKAN ---
    public function storePendidikan(RiwayatPendidikanDTO $dto): RiwayatPendidikan
    {
        return DB::transaction(fn () => RiwayatPendidikan::create($dto->toArray()));
    }

    public function updatePendidikan(RiwayatPendidikan $riwayat, RiwayatPendidikanDTO $dto): bool
    {
        return DB::transaction(fn () => $riwayat->update($dto->toArray()));
    }

    public function deletePendidikan(RiwayatPendidikan $riwayat): bool
    {
        return DB::transaction(function () use ($riwayat) {
            if ($riwayat->file_pdf_path) {
                $this->uploadService->delete($riwayat->file_pdf_path);
            }
            return $riwayat->delete();
        });
    }

    // --- LATIHAN JABATAN ---
    public function storeLatihan(RiwayatLatihanJabatanDTO $dto): RiwayatLatihanJabatan
    {
        return DB::transaction(fn () => RiwayatLatihanJabatan::create($dto->toArray()));
    }

    public function updateLatihan(RiwayatLatihanJabatan $riwayat, RiwayatLatihanJabatanDTO $dto): bool
    {
        return DB::transaction(fn () => $riwayat->update($dto->toArray()));
    }

    public function deleteLatihan(RiwayatLatihanJabatan $riwayat): bool
    {
        return DB::transaction(function () use ($riwayat) {
            if ($riwayat->file_pdf_path) {
                $this->uploadService->delete($riwayat->file_pdf_path);
            }
            return $riwayat->delete();
        });
    }

    // --- PENILAIAN KINERJA (SKP) ---
    public function storeSKP(PenilaianKinerjaDTO $dto): PenilaianKinerja
    {
        return DB::transaction(fn () => PenilaianKinerja::create($dto->toArray()));
    }

    public function updateSKP(PenilaianKinerja $riwayat, PenilaianKinerjaDTO $dto): bool
    {
        return DB::transaction(fn () => $riwayat->update($dto->toArray()));
    }

    public function deleteSKP(PenilaianKinerja $riwayat): bool
    {
        return DB::transaction(function () use ($riwayat) {
            if ($riwayat->file_pdf_path) {
                $this->uploadService->delete($riwayat->file_pdf_path);
            }
            return $riwayat->delete();
        });
    }

    // --- PENGHARGAAN ---
    public function storePenghargaan(array $data): \App\Models\RiwayatPenghargaan
    {
        return DB::transaction(fn () => \App\Models\RiwayatPenghargaan::create($data));
    }

    public function deletePenghargaan(\App\Models\RiwayatPenghargaan $riwayat): bool
    {
        return DB::transaction(fn () => $riwayat->delete());
    }
}
