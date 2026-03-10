<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePegawaiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $pegawaiId = $this->route('pegawai')->id ?? $this->route('pegawai');

        return [
            'nip' => ['required', 'string', 'digits:18', 'unique:pegawais,nip,' . $pegawaiId],
            'gelar_depan' => 'nullable|string|max:50',
            'nama_lengkap' => 'required|string|max:255',
            'gelar_belakang' => 'nullable|string|max:50',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|integer',
            'alamat' => 'nullable|string',
            'no_telepon' => 'nullable|string',
            'email' => 'nullable|email',
            'tmt_cpns' => 'required|date',
            'tmt_pns' => 'nullable|date',
            'gaji_pokok' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'agama' => 'required|integer',
            'status_pernikahan' => 'required|integer',
            'golongan_darah' => 'required|integer',
            'npwp' => 'nullable|string',
            'no_karpeg' => 'nullable|string',
            'no_taspen' => 'nullable|string',
            'unit_kerja' => 'nullable|string',
            'bagian' => 'nullable|string|in:Tata Usaha,Tikim,Lantaskim,Inteldakim,Intaltuskim',
            'tipe_pegawai' => 'required|string|in:PNS,CPNS,PPPK',
            'status_kepegawaian' => 'required|string|in:Aktif,Tidak Aktif',
        ];
    }
}
