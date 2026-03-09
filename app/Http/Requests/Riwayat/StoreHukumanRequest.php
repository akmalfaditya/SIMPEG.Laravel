<?php

namespace App\Http\Requests\Riwayat;

use Illuminate\Foundation\Http\FormRequest;

class StoreHukumanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pegawai_id' => 'required|exists:pegawais,id',
            'tingkat_hukuman' => 'required|integer',
            'jenis_sanksi' => 'required|integer',
            'durasi_tahun' => 'nullable|integer|min:1|max:10',
            'nomor_sk' => 'nullable|string',
            'tanggal_sk' => 'nullable|date',
            'tmt_hukuman' => 'required|date',
            'tmt_selesai_hukuman' => 'nullable|date',
            'deskripsi' => 'nullable|string',
            'file_sk' => 'nullable|file|mimes:pdf|max:5120',
            'google_drive_link' => 'nullable|url|max:500',
        ];
    }
}
