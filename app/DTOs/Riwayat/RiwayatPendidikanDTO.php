<?php

namespace App\DTOs\Riwayat;

class RiwayatPendidikanDTO
{
    public function __construct(
        public readonly ?int $pegawaiId,
        public readonly string $tingkatPendidikan,
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
            tingkatPendidikan: $validated['tingkat_pendidikan'],
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
            'tingkat_pendidikan' => $this->tingkatPendidikan,
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
