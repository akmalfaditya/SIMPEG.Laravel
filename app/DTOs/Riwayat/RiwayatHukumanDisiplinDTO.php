<?php

namespace App\DTOs\Riwayat;

class RiwayatHukumanDisiplinDTO
{
    public function __construct(
        public readonly ?int $pegawaiId,
        public readonly int $tingkatHukuman,
        public readonly string $jenisHukuman,
        public readonly ?string $nomorSk,
        public readonly ?string $tanggalSk,
        public readonly string $tmtHukuman,
        public readonly ?string $tmtSelesaiHukuman,
        public readonly ?string $deskripsi,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            pegawaiId: $validated['pegawai_id'] ?? null,
            tingkatHukuman: (int) $validated['tingkat_hukuman'],
            jenisHukuman: $validated['jenis_hukuman'],
            nomorSk: $validated['nomor_sk'] ?? null,
            tanggalSk: $validated['tanggal_sk'] ?? null,
            tmtHukuman: $validated['tmt_hukuman'],
            tmtSelesaiHukuman: $validated['tmt_selesai_hukuman'] ?? null,
            deskripsi: $validated['deskripsi'] ?? null,
        );
    }

    public function toArray(): array
    {
        $data = [
            'tingkat_hukuman' => $this->tingkatHukuman,
            'jenis_hukuman' => $this->jenisHukuman,
            'nomor_sk' => $this->nomorSk,
            'tanggal_sk' => $this->tanggalSk,
            'tmt_hukuman' => $this->tmtHukuman,
            'tmt_selesai_hukuman' => $this->tmtSelesaiHukuman,
            'deskripsi' => $this->deskripsi,
        ];

        if ($this->pegawaiId !== null) {
            $data['pegawai_id'] = $this->pegawaiId;
        }

        return $data;
    }
}
