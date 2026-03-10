<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessPensiunRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pegawai_id' => 'required|exists:pegawais,id',
            'sk_pensiun_nomor' => 'required|string|max:255',
            'sk_pensiun_tanggal' => 'required|date',
            'tmt_pensiun' => 'required|date',
            'catatan_pensiun' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'sk_pensiun_nomor.required' => 'Nomor SK Pensiun wajib diisi.',
            'sk_pensiun_tanggal.required' => 'Tanggal SK Pensiun wajib diisi.',
            'tmt_pensiun.required' => 'TMT Pensiun wajib diisi.',
        ];
    }
}
