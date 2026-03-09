<?php

namespace App\Services;

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
        return DB::transaction(fn () => $riwayat->delete());
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
        return DB::transaction(fn () => $riwayat->delete());
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
        return DB::transaction(fn () => $riwayat->update($dto->toArray()));
    }

    public function deleteKgb(RiwayatKgb $riwayat): bool
    {
        return DB::transaction(fn () => $riwayat->delete());
    }

    // --- HUKUMAN DISIPLIN ---
    public function storeHukuman(RiwayatHukumanDisiplinDTO $dto): RiwayatHukumanDisiplin
    {
        return DB::transaction(function () use ($dto) {
            $data = $dto->toArray();
            if ($dto->filePdfPath) {
                $data['file_pdf_path'] = $dto->filePdfPath;
            }
            return RiwayatHukumanDisiplin::create($data);
        });
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
            if ($riwayat->file_pdf_path) {
                app(DocumentUploadService::class)->delete($riwayat->file_pdf_path);
            }
            return $riwayat->delete();
        });
    }

    public function uploadHukumanSk(UploadedFile $file, ?string $oldPath = null): string
    {
        $uploadService = app(DocumentUploadService::class);
        if ($oldPath) {
            $uploadService->delete($oldPath);
        }
        return $uploadService->upload($file, 'sk_hukuman');
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
        return DB::transaction(fn () => $riwayat->delete());
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
        return DB::transaction(fn () => $riwayat->delete());
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
        return DB::transaction(fn () => $riwayat->delete());
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
