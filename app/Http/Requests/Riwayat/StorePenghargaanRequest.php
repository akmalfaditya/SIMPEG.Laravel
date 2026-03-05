<?php

namespace App\Http\Requests\Riwayat;

use Illuminate\Foundation\Http\FormRequest;

class StorePenghargaanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pegawai_id' => 'required|exists:pegawais,id',
            'nama_penghargaan' => 'required|string|max:255',
            'tahun' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'milestone' => 'required|integer|in:10,20,30',
            'nomor_sk' => 'nullable|string|max:255',
            'tanggal_sk' => 'nullable|date',
        ];
    }
}
