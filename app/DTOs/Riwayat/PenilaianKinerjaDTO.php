<?php

namespace App\DTOs\Riwayat;

class PenilaianKinerjaDTO
{
    public function __construct(
        public readonly ?int $pegawaiId,
        public readonly int $tahun,
        public readonly string $nilaiSkp,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            pegawaiId: $validated['pegawai_id'] ?? null,
            tahun: (int) $validated['tahun'],
            nilaiSkp: $validated['nilai_skp'],
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

        return $data;
    }
}
