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
            'pangkat_terakhir' => $this->pangkat_terakhir ?? '-',
            'jabatan_terakhir' => $this->jabatan_terakhir ?? '-',
            'masa_kerja' => $this->masa_kerja ?? '-',
        ];
    }
}
