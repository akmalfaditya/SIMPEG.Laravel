<?php

namespace App\DTOs\Riwayat;

class RiwayatKgbDTO
{
    public function __construct(
        public readonly ?int $pegawaiId,
        public readonly ?string $nomorSk,
        public readonly string $tmtKgb,
        public readonly float $gajiLama,
        public readonly float $gajiBaru,
        public readonly int $masaKerjaGolonganTahun,
        public readonly int $masaKerjaGolonganBulan,
        public readonly ?string $filePdfPath,
        public readonly ?string $googleDriveLink,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            pegawaiId: $validated['pegawai_id'] ?? null,
            nomorSk: $validated['nomor_sk'] ?? null,
            tmtKgb: $validated['tmt_kgb'],
            gajiLama: (float) $validated['gaji_lama'],
            gajiBaru: (float) $validated['gaji_baru'],
            masaKerjaGolonganTahun: (int) $validated['masa_kerja_golongan_tahun'],
            masaKerjaGolonganBulan: (int) $validated['masa_kerja_golongan_bulan'],
            filePdfPath: $validated['file_pdf_path'] ?? null,
            googleDriveLink: $validated['google_drive_link'] ?? null,
        );
    }

    public function toArray(): array
    {
        $data = [
            'nomor_sk' => $this->nomorSk,
            'tmt_kgb' => $this->tmtKgb,
            'gaji_lama' => $this->gajiLama,
            'gaji_baru' => $this->gajiBaru,
            'masa_kerja_golongan_tahun' => $this->masaKerjaGolonganTahun,
            'masa_kerja_golongan_bulan' => $this->masaKerjaGolonganBulan,
            'file_pdf_path' => $this->filePdfPath,
            'google_drive_link' => $this->googleDriveLink,
        ];

        if ($this->pegawaiId !== null) {
            $data['pegawai_id'] = $this->pegawaiId;
        }

        return $data;
    }
}
