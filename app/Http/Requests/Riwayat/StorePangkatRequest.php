<?php

namespace App\Http\Requests\Riwayat;

use Illuminate\Foundation\Http\FormRequest;

class StorePangkatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pegawai_id' => 'required|exists:pegawais,id',
            'golongan_ruang' => 'required|integer',
            'nomor_sk' => 'nullable|string',
            'tmt_pangkat' => 'required|date',
            'tanggal_sk' => 'required|date',
            'file_pdf_path' => 'nullable|string',
            'google_drive_link' => 'nullable|string',
        ];
    }
}
