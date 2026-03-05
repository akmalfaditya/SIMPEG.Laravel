<?php

namespace App\Http\Requests\Riwayat;

use Illuminate\Foundation\Http\FormRequest;

class StoreKGBRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pegawai_id' => 'required|exists:pegawais,id',
            'nomor_sk' => 'nullable|string',
            'tmt_kgb' => 'required|date',
            'gaji_lama' => 'required|numeric',
            'gaji_baru' => 'required|numeric',
            'masa_kerja_golongan_tahun' => 'required|integer',
            'masa_kerja_golongan_bulan' => 'required|integer',
            'file_pdf_path' => 'nullable|string',
            'google_drive_link' => 'nullable|string',
        ];
    }
}
