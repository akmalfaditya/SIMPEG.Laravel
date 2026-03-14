<?php

namespace App\Http\Requests\Riwayat;

use Illuminate\Foundation\Http\FormRequest;

class StorePendidikanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pegawai_id' => 'required|exists:pegawais,id',
            'pendidikan_id' => 'required|exists:master_pendidikans,id',
            'institusi' => 'required|string',
            'jurusan' => 'required|string',
            'tahun_lulus' => 'required|integer',
            'no_ijazah' => 'nullable|string',
            'tanggal_ijazah' => 'nullable|date',
            'file_sk' => 'nullable|file|mimes:pdf|max:5120',
            'google_drive_link' => 'nullable|url|max:500',
        ];
    }
}
