<?php

namespace App\Http\Requests\Riwayat;

use Illuminate\Foundation\Http\FormRequest;

class StoreJabatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pegawai_id' => 'required|exists:pegawais,id',
            'jabatan_id' => 'required|exists:jabatans,id',
            'nomor_sk' => 'nullable|string',
            'tmt_jabatan' => 'required|date',
            'tanggal_sk' => 'required|date',
            'file_sk' => 'nullable|file|mimes:pdf|max:5120',
            'google_drive_link' => 'nullable|url|max:500',
        ];
    }
}
