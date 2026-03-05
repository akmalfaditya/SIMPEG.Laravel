<?php

namespace App\Http\Requests\Riwayat;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLatihanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_latihan' => 'required|string',
            'tahun_pelaksanaan' => 'required|integer',
            'jumlah_jam' => 'required|integer|min:0',
            'penyelenggara' => 'nullable|string',
            'tempat_pelaksanaan' => 'nullable|string',
            'no_sertifikat' => 'nullable|string',
            'file_pdf_path' => 'nullable|string',
            'google_drive_link' => 'nullable|string',
        ];
    }
}
