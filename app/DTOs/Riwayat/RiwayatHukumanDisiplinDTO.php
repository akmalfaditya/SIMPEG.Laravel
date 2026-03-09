<?php

namespace App\DTOs\Riwayat;

class RiwayatHukumanDisiplinDTO
{
    public function __construct(
        public readonly ?int $pegawaiId,
        public readonly int $tingkatHukuman,
        public readonly int $jenisSanksi,
        public readonly ?int $durasiTahun,
        public readonly ?string $nomorSk,
        public readonly ?string $tanggalSk,
        public readonly string $tmtHukuman,
        public readonly ?string $tmtSelesaiHukuman,
        public readonly ?string $deskripsi,
        public readonly ?string $filePdfPath = null,
        public readonly ?string $googleDriveLink = null,
        public readonly ?int $demotionGolonganRuang = null,
        public readonly ?int $demotionJabatanId = null,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            pegawaiId: $validated['pegawai_id'] ?? null,
            tingkatHukuman: (int) $validated['tingkat_hukuman'],
            jenisSanksi: (int) $validated['jenis_sanksi'],
            durasiTahun: isset($validated['durasi_tahun']) ? (int) $validated['durasi_tahun'] : null,
            nomorSk: $validated['nomor_sk'] ?? null,
            tanggalSk: $validated['tanggal_sk'] ?? null,
            tmtHukuman: $validated['tmt_hukuman'],
            tmtSelesaiHukuman: $validated['tmt_selesai_hukuman'] ?? null,
            deskripsi: $validated['deskripsi'] ?? null,
            filePdfPath: $validated['file_pdf_path'] ?? null,
            googleDriveLink: $validated['google_drive_link'] ?? null,
            demotionGolonganRuang: isset($validated['demotion_golongan_ruang']) ? (int) $validated['demotion_golongan_ruang'] : null,
            demotionJabatanId: isset($validated['demotion_jabatan_id']) ? (int) $validated['demotion_jabatan_id'] : null,
        );
    }

    public function toArray(): array
    {
        $data = [
            'tingkat_hukuman' => $this->tingkatHukuman,
            'jenis_sanksi' => $this->jenisSanksi,
            'durasi_tahun' => $this->durasiTahun,
            'nomor_sk' => $this->nomorSk,
            'tanggal_sk' => $this->tanggalSk,
            'tmt_hukuman' => $this->tmtHukuman,
            'tmt_selesai_hukuman' => $this->tmtSelesaiHukuman,
            'deskripsi' => $this->deskripsi,
            'google_drive_link' => $this->googleDriveLink,
        ];

        if ($this->pegawaiId !== null) {
            $data['pegawai_id'] = $this->pegawaiId;
        }

        return $data;
    }
}
