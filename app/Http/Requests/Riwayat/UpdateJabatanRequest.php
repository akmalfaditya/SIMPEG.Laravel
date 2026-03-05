<?php

namespace App\Http\Requests\Riwayat;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJabatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jabatan_id' => 'required|exists:jabatans,id',
            'nomor_sk' => 'nullable|string',
            'tmt_jabatan' => 'required|date',
            'tanggal_sk' => 'required|date',
            'file_pdf_path' => 'nullable|string',
            'google_drive_link' => 'nullable|string',
        ];
    }
}
