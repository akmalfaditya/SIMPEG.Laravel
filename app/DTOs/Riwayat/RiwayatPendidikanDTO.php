<?php

namespace App\DTOs\Riwayat;

class RiwayatPendidikanDTO
{
    public function __construct(
        public readonly ?int $pegawaiId,
        public readonly int $pendidikanId,
        public readonly string $institusi,
        public readonly string $jurusan,
        public readonly int $tahunLulus,
        public readonly ?string $noIjazah,
        public readonly ?string $tanggalIjazah,
        public readonly ?string $filePdfPath,
        public readonly ?string $googleDriveLink,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            pegawaiId: $validated['pegawai_id'] ?? null,
            pendidikanId: (int) $validated['pendidikan_id'],
            institusi: $validated['institusi'],
            jurusan: $validated['jurusan'],
            tahunLulus: (int) $validated['tahun_lulus'],
            noIjazah: $validated['no_ijazah'] ?? null,
            tanggalIjazah: $validated['tanggal_ijazah'] ?? null,
            filePdfPath: $validated['file_pdf_path'] ?? null,
            googleDriveLink: $validated['google_drive_link'] ?? null,
        );
    }

    public function toArray(): array
    {
        $data = [
            'pendidikan_id' => $this->pendidikanId,
            'institusi' => $this->institusi,
            'jurusan' => $this->jurusan,
            'tahun_lulus' => $this->tahunLulus,
            'no_ijazah' => $this->noIjazah,
            'tanggal_ijazah' => $this->tanggalIjazah,
            'file_pdf_path' => $this->filePdfPath,
            'google_drive_link' => $this->googleDriveLink,
        ];

        if ($this->pegawaiId !== null) {
            $data['pegawai_id'] = $this->pegawaiId;
        }

        return $data;
    }
}
