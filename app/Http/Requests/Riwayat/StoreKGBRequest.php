<?php

namespace App\Http\Requests\Riwayat;

use App\Enums\JenisSanksi;
use App\Models\Pegawai;
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

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $pegawaiId = $this->input('pegawai_id');
            if (!$pegawaiId) return;

            $pegawai = Pegawai::with('riwayatHukumanDisiplin')->find($pegawaiId);
            if (!$pegawai) return;

            $today = today();
            $active = $pegawai->riwayatHukumanDisiplin
                ->filter(fn($h) => $h->jenis_sanksi === JenisSanksi::PenundaanKgb
                    && ($h->tmt_selesai_hukuman === null || $h->tmt_selesai_hukuman->gte($today)));

            if ($active->isNotEmpty()) {
                $durasi = $active->sum(fn($h) => $h->durasi_tahun ?? 1);
                $validator->errors()->add('pegawai_id',
                    "Pegawai sedang menjalani sanksi Penundaan KGB selama {$durasi} tahun. KGB tidak dapat ditambahkan.");
            }
        });
    }
}
