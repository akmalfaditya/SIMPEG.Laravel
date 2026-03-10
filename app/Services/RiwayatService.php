<?php

namespace App\Services;

use App\Enums\JenisSanksi;
use App\Enums\StatusHukdis;
use App\Enums\TingkatHukuman;

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
    ) {}

    /**
     * Generic SK file upload with optional old file deletion.
     * Builds meaningful filename: {NIP}_{Module}_{timestamp}_{OriginalName}.
     */
    public function uploadSk(UploadedFile $file, string $subfolder, ?string $oldPath = null, ?int $pegawaiId = null): string
    {
        if ($oldPath) {
            $this->uploadService->delete($oldPath);
        }

        $fileName = $this->buildFileName($file, $subfolder, $pegawaiId);

        return $this->uploadService->upload($file, $subfolder, $fileName);
    }

    private function buildFileName(UploadedFile $file, string $subfolder, ?int $pegawaiId): ?string
    {
        if (!$pegawaiId) {
            return null;
        }

        $pegawai = Pegawai::find($pegawaiId);
        if (!$pegawai) {
            return null;
        }

        $moduleName = ucfirst(str_replace('sk_', '', $subfolder));
        $originalName = str_replace(' ', '_', $file->getClientOriginalName());

        return $pegawai->nip . '_' . $moduleName . '_' . time() . '_' . $originalName;
    }

    // --- PANGKAT ---
    public function storePangkat(RiwayatPangkatDTO $dto): RiwayatPangkat
    {
        return DB::transaction(fn() => RiwayatPangkat::create($dto->toArray()));
    }

    public function updatePangkat(RiwayatPangkat $riwayat, RiwayatPangkatDTO $dto): bool
    {
        return DB::transaction(fn() => $riwayat->update($dto->toArray()));
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
        return DB::transaction(fn() => RiwayatJabatan::create($dto->toArray()));
    }

    public function updateJabatan(RiwayatJabatan $riwayat, RiwayatJabatanDTO $dto): bool
    {
        return DB::transaction(fn() => $riwayat->update($dto->toArray()));
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
        return DB::transaction(fn() => RiwayatKgb::create($dto->toArray()));
    }

    public function updateKgb(RiwayatKgb $riwayat, RiwayatKgbDTO $dto): bool
    {
        return DB::transaction(fn() => $riwayat->update($dto->toArray()));
    }

    public function deleteKgb(RiwayatKgb $riwayat): bool
    {
        return DB::transaction(function () use ($riwayat) {
            if ($riwayat->file_pdf_path) {
                $this->uploadService->delete($riwayat->file_pdf_path);
            }
            return $riwayat->delete();
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

            // PP 94/2021: durasi untuk hukuman sedang/berat selalu 1 tahun
            $tingkat = TingkatHukuman::from($dto->tingkatHukuman);
            if (in_array($tingkat, [TingkatHukuman::Sedang, TingkatHukuman::Berat])) {
                $data['durasi_tahun'] = 1;
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

        if ($jenis === JenisSanksi::PenurunanPangkat && $dto->demotionGolonganId !== null) {
            RiwayatPangkat::create([
                'pegawai_id' => $pegawaiId,
                'golongan_id' => $dto->demotionGolonganId,
                'nomor_sk' => $dto->nomorSk,
                'tmt_pangkat' => $dto->tmtHukuman,
                'tanggal_sk' => $dto->tanggalSk ?? $dto->tmtHukuman,
                'is_hukdis_demotion' => true,
            ]);
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

    public function updateHukuman(RiwayatHukumanDisiplin $riwayat, RiwayatHukumanDisiplinDTO $dto): bool
    {
        return DB::transaction(function () use ($riwayat, $dto) {
            $data = $dto->toArray();
            if ($dto->filePdfPath) {
                $data['file_pdf_path'] = $dto->filePdfPath;
            }

            // PP 94/2021: durasi untuk hukuman sedang/berat selalu 1 tahun
            $tingkat = TingkatHukuman::from($dto->tingkatHukuman);
            if (in_array($tingkat, [TingkatHukuman::Sedang, TingkatHukuman::Berat])) {
                $data['durasi_tahun'] = 1;
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
                // Use model-level deletes so RiwayatPangkatObserver fires
                $demotions = RiwayatPangkat::where('pegawai_id', $pegawaiId)
                    ->where('is_hukdis_demotion', true)
                    ->where('tmt_pangkat', $riwayat->tmt_hukuman)
                    ->get();
                foreach ($demotions as $demotion) {
                    $demotion->delete();
                }
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
        ?int $restorationGolonganId = null,
        ?int $restorationJabatanId = null,
    ): bool {
        return DB::transaction(function () use ($hukuman, $nomorSkPemulihan, $tanggalPemulihan, $fileSkPemulihanPath, $restorationGolonganId, $restorationJabatanId) {
            $hukuman->update([
                'status' => StatusHukdis::Dipulihkan->value,
                'nomor_sk_pemulihan' => $nomorSkPemulihan,
                'tanggal_pemulihan' => $tanggalPemulihan,
                'file_sk_pemulihan_path' => $fileSkPemulihanPath,
            ]);

            $jenis = $hukuman->jenis_sanksi;
            $pegawaiId = $hukuman->pegawai_id;

            // Type 2 restoration: insert new riwayat record to restore position
            if ($jenis === JenisSanksi::PenurunanPangkat && $restorationGolonganId !== null) {
                RiwayatPangkat::create([
                    'pegawai_id' => $pegawaiId,
                    'golongan_id' => $restorationGolonganId,
                    'nomor_sk' => $nomorSkPemulihan,
                    'tmt_pangkat' => $tanggalPemulihan,
                    'tanggal_sk' => $tanggalPemulihan,
                ]);
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

    public function uploadHukumanSk(UploadedFile $file, ?string $oldPath = null, ?int $pegawaiId = null): string
    {
        return $this->uploadSk($file, 'sk_hukuman', $oldPath, $pegawaiId);
    }

    // --- PENDIDIKAN ---
    public function storePendidikan(RiwayatPendidikanDTO $dto): RiwayatPendidikan
    {
        return DB::transaction(fn() => RiwayatPendidikan::create($dto->toArray()));
    }

    public function updatePendidikan(RiwayatPendidikan $riwayat, RiwayatPendidikanDTO $dto): bool
    {
        return DB::transaction(fn() => $riwayat->update($dto->toArray()));
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
        return DB::transaction(fn() => RiwayatLatihanJabatan::create($dto->toArray()));
    }

    public function updateLatihan(RiwayatLatihanJabatan $riwayat, RiwayatLatihanJabatanDTO $dto): bool
    {
        return DB::transaction(fn() => $riwayat->update($dto->toArray()));
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
        return DB::transaction(fn() => PenilaianKinerja::create($dto->toArray()));
    }

    public function updateSKP(PenilaianKinerja $riwayat, PenilaianKinerjaDTO $dto): bool
    {
        return DB::transaction(fn() => $riwayat->update($dto->toArray()));
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
        return DB::transaction(fn() => \App\Models\RiwayatPenghargaan::create($data));
    }

    public function deletePenghargaan(\App\Models\RiwayatPenghargaan $riwayat): bool
    {
        return DB::transaction(fn() => $riwayat->delete());
    }
}
