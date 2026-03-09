<?php

namespace App\Http\Requests\Riwayat;

use App\Enums\JenisSanksi;
use App\Models\RiwayatPangkat;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHukumanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
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
            'demotion_golongan_ruang' => 'nullable|integer',
            'demotion_jabatan_id' => 'nullable|integer|exists:jabatans,id',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $jenis = (int) $this->input('jenis_sanksi');
            $hukuman = $this->route('riwayatHukuman');
            $pegawaiId = $hukuman->pegawai_id;

            if ($jenis === JenisSanksi::PenurunanPangkat->value && $this->filled('demotion_golongan_ruang')) {
                $currentPangkat = RiwayatPangkat::where('pegawai_id', $pegawaiId)
                    ->where('is_hukdis_demotion', false)
                    ->orderByDesc('tmt_pangkat')->first();
                if ($currentPangkat && (int) $this->input('demotion_golongan_ruang') >= $currentPangkat->golongan_ruang->value) {
                    $validator->errors()->add('demotion_golongan_ruang', 'Golongan tujuan harus lebih rendah dari golongan saat ini.');
                }
            }

        });
    }
}
