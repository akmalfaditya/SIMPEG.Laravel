<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePegawaiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nip' => ['required', 'string', 'digits:18', 'unique:pegawais,nip'],
            'gelar_depan' => 'nullable|string|max:50',
            'nama_lengkap' => 'required|string|max:255',
            'gelar_belakang' => 'nullable|string|max:50',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin_id' => 'required|integer|exists:jenis_kelamins,id',
            'agama_id' => 'required|integer|exists:agamas,id',
            'status_pernikahan_id' => 'required|integer|exists:status_pernikahans,id',
            'golongan_darah_id' => 'required|integer|exists:golongan_darahs,id',
            'alamat' => 'nullable|string',
            'no_telepon' => 'nullable|string',
            'email' => 'nullable|email',
            'tmt_cpns' => 'required|date',
            'tmt_pns' => 'nullable|date',
            'tipe_pegawai_id' => 'required|integer|exists:tipe_pegawais,id',
            'status_kepegawaian_id' => 'required|integer|exists:status_kepegawaans,id',
            'bagian_id' => 'nullable|integer|exists:bagians,id',
            'unit_kerja_id' => 'nullable|integer|exists:unit_kerjas,id',
            'npwp' => 'nullable|string',
            'no_karpeg' => 'nullable|string',
            'no_taspen' => 'nullable|string',
            // Dokumen Dasar (foundational SK documents)
            'sk_cpns_file' => 'nullable|file|mimes:pdf|max:5120',
            'sk_pns_file' => 'nullable|file|mimes:pdf|max:5120',
            // One-stop creation: pangkat & jabatan awal
            'golongan_id' => 'required|integer|exists:golongan_pangkats,id',
            'jabatan_id' => 'required|integer|exists:jabatans,id',
        ];
    }
}
