<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PegawaiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nip' => $this->nip,
            'nama_lengkap' => $this->nama_lengkap,
            'has_active_hukdis' => $this->has_active_hukdis,
            'pangkat_terakhir' => $this->pangkat_terakhir ?? '-',
            'jabatan_terakhir' => $this->jabatan_terakhir ?? '-',
            'masa_kerja' => $this->masa_kerja ?? '-',
            'sk_pensiun_nomor' => $this->sk_pensiun_nomor,
            'tmt_pensiun' => $this->tmt_pensiun?->format('d/m/Y'),
        ];
    }
}
