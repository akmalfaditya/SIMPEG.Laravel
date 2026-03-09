<?php

namespace App\DTOs\Riwayat;

class RiwayatPangkatDTO
{
    public function __construct(
        public readonly ?int $pegawaiId,
        public readonly int $golonganId,
        public readonly ?string $nomorSk,
        public readonly string $tmtPangkat,
        public readonly string $tanggalSk,
        public readonly ?string $filePdfPath,
        public readonly ?string $googleDriveLink,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            pegawaiId: $validated['pegawai_id'] ?? null,
            golonganId: (int) $validated['golongan_id'],
            nomorSk: $validated['nomor_sk'] ?? null,
            tmtPangkat: $validated['tmt_pangkat'],
            tanggalSk: $validated['tanggal_sk'],
            filePdfPath: $validated['file_pdf_path'] ?? null,
            googleDriveLink: $validated['google_drive_link'] ?? null,
        );
    }

    public function toArray(): array
    {
        $data = [
            'golongan_id' => $this->golonganId,
            'nomor_sk' => $this->nomorSk,
            'tmt_pangkat' => $this->tmtPangkat,
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
