<?php

namespace App\Http\Requests\Riwayat;

use App\Enums\JenisSanksi;
use App\Models\Pegawai;
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

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $pegawaiId = $this->input('pegawai_id');
            if (!$pegawaiId) return;

            $pegawai = Pegawai::with(['riwayatPangkat', 'riwayatHukumanDisiplin'])->find($pegawaiId);
            if (!$pegawai) return;

            // Golongan baru harus lebih tinggi dari saat ini
            $currentPangkat = $pegawai->riwayatPangkat->sortByDesc('tmt_pangkat')->first();
            if ($currentPangkat) {
                $newGolongan = (int) $this->input('golongan_ruang');
                $currentGolongan = $currentPangkat->golongan_ruang->value;

                if ($newGolongan <= $currentGolongan) {
                    $validator->errors()->add('golongan_ruang',
                        "Golongan baru harus lebih tinggi dari golongan saat ini ({$currentPangkat->golongan_ruang->label()}).");
                }
            }

            // Cek sanksi yang memblokir kenaikan pangkat
            $today = today();
            $activeBlocking = $pegawai->riwayatHukumanDisiplin
                ->filter(fn($h) => in_array($h->jenis_sanksi, [
                        JenisSanksi::PenundaanPangkat,
                        JenisSanksi::PenurunanPangkat,
                        JenisSanksi::PembebasanJabatan,
                        JenisSanksi::Pemberhentian,
                    ])
                    && ($h->tmt_selesai_hukuman === null || $h->tmt_selesai_hukuman->gte($today)));

            if ($activeBlocking->isNotEmpty()) {
                $notes = $activeBlocking->map(fn($h) => $h->jenis_sanksi->label())->implode(', ');
                $validator->errors()->add('pegawai_id',
                    "Pegawai sedang menjalani sanksi: {$notes}. Kenaikan pangkat tidak dapat ditambahkan.");
            }
        });
    }
}
