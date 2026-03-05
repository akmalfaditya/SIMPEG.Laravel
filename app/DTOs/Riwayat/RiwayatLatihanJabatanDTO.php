<?php

namespace App\DTOs\Riwayat;

class RiwayatLatihanJabatanDTO
{
    public function __construct(
        public readonly ?int $pegawaiId,
        public readonly string $namaLatihan,
        public readonly int $tahunPelaksanaan,
        public readonly int $jumlahJam,
        public readonly ?string $penyelenggara,
        public readonly ?string $tempatPelaksanaan,
        public readonly ?string $noSertifikat,
        public readonly ?string $filePdfPath,
        public readonly ?string $googleDriveLink,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            pegawaiId: $validated['pegawai_id'] ?? null,
            namaLatihan: $validated['nama_latihan'],
            tahunPelaksanaan: (int) $validated['tahun_pelaksanaan'],
            jumlahJam: (int) $validated['jumlah_jam'],
            penyelenggara: $validated['penyelenggara'] ?? null,
            tempatPelaksanaan: $validated['tempat_pelaksanaan'] ?? null,
            noSertifikat: $validated['no_sertifikat'] ?? null,
            filePdfPath: $validated['file_pdf_path'] ?? null,
            googleDriveLink: $validated['google_drive_link'] ?? null,
        );
    }

    public function toArray(): array
    {
        $data = [
            'nama_latihan' => $this->namaLatihan,
            'tahun_pelaksanaan' => $this->tahunPelaksanaan,
            'jumlah_jam' => $this->jumlahJam,
            'penyelenggara' => $this->penyelenggara,
            'tempat_pelaksanaan' => $this->tempatPelaksanaan,
            'no_sertifikat' => $this->noSertifikat,
            'file_pdf_path' => $this->filePdfPath,
            'google_drive_link' => $this->googleDriveLink,
        ];

        if ($this->pegawaiId !== null) {
            $data['pegawai_id'] = $this->pegawaiId;
        }

        return $data;
    }
}
