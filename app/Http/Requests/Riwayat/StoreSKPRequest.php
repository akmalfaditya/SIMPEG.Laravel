<?php

namespace App\Http\Requests\Riwayat;

use Illuminate\Foundation\Http\FormRequest;

class StoreSKPRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pegawai_id' => 'required|exists:pegawais,id',
            'tahun' => 'required|integer',
            'nilai_skp' => 'required|string',
            'file_sk' => 'nullable|file|mimes:pdf|max:5120',
            'google_drive_link' => 'nullable|url|max:500',
        ];
    }
}
