<?php

namespace App\DTOs\Riwayat;

class PenilaianKinerjaDTO
{
    public function __construct(
        public readonly ?int $pegawaiId,
        public readonly int $tahun,
        public readonly string $nilaiSkp,
        public readonly ?string $filePdfPath = null,
        public readonly ?string $googleDriveLink = null,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            pegawaiId: $validated['pegawai_id'] ?? null,
            tahun: (int) $validated['tahun'],
            nilaiSkp: $validated['nilai_skp'],
            filePdfPath: $validated['file_pdf_path'] ?? null,
            googleDriveLink: $validated['google_drive_link'] ?? null,
        );
    }

    public function toArray(): array
    {
        $data = [
            'tahun' => $this->tahun,
            'nilai_skp' => $this->nilaiSkp,
        ];

        if ($this->pegawaiId !== null) {
            $data['pegawai_id'] = $this->pegawaiId;
        }

        if ($this->filePdfPath !== null) {
            $data['file_pdf_path'] = $this->filePdfPath;
        }

        if ($this->googleDriveLink !== null) {
            $data['google_drive_link'] = $this->googleDriveLink;
        }

        return $data;
    }
}
