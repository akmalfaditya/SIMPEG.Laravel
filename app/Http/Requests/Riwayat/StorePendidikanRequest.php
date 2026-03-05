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
            'tingkat_pendidikan' => 'required|string',
            'institusi' => 'required|string',
            'jurusan' => 'required|string',
            'tahun_lulus' => 'required|integer',
            'no_ijazah' => 'nullable|string',
            'tanggal_ijazah' => 'nullable|date',
            'file_pdf_path' => 'nullable|string',
            'google_drive_link' => 'nullable|string',
        ];
    }
}
