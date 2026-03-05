<?php

namespace App\DTOs\Riwayat;

class RiwayatJabatanDTO
{
    public function __construct(
        public readonly ?int $pegawaiId,
        public readonly int $jabatanId,
        public readonly ?string $nomorSk,
        public readonly string $tmtJabatan,
        public readonly string $tanggalSk,
        public readonly ?string $filePdfPath,
        public readonly ?string $googleDriveLink,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            pegawaiId: $validated['pegawai_id'] ?? null,
            jabatanId: (int) $validated['jabatan_id'],
            nomorSk: $validated['nomor_sk'] ?? null,
            tmtJabatan: $validated['tmt_jabatan'],
            tanggalSk: $validated['tanggal_sk'],
            filePdfPath: $validated['file_pdf_path'] ?? null,
            googleDriveLink: $validated['google_drive_link'] ?? null,
        );
    }

    public function toArray(): array
    {
        $data = [
            'jabatan_id' => $this->jabatanId,
            'nomor_sk' => $this->nomorSk,
            'tmt_jabatan' => $this->tmtJabatan,
            'tanggal_sk' => $this->tanggalSk,
            'file_pdf_path' => $this->filePdfPath,
            'google_drive_link' => $this->googleDriveLink,
        ];

        if ($this->pegawaiId !== null) {
            $data['pegawai_id'] = $this->pegawaiId;
        }

        return $data;
    }
}
