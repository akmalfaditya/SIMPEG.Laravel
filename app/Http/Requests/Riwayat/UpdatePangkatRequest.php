<?php

namespace App\Http\Requests\Riwayat;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePangkatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'golongan_id' => 'required|integer|exists:golongan_pangkats,id',
            'nomor_sk' => 'nullable|string',
            'tmt_pangkat' => 'required|date',
            'tanggal_sk' => 'required|date',
            'file_sk' => 'nullable|file|mimes:pdf|max:5120',
            'google_drive_link' => 'nullable|url|max:500',
        ];
    }
}
