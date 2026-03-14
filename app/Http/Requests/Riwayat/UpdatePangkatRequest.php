<?php

namespace App\Http\Requests\Riwayat;

use App\Models\Pegawai;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePangkatRequest extends FormRequest
{
    public function authorize(): bool
    {
        $riwayatPangkat = $this->route('riwayatPangkat');
        $pegawai = Pegawai::with('riwayatJabatan.jabatan.rumpunJabatan')->find($riwayatPangkat->pegawai_id);

        if ($pegawai) {
            $latestJabatan = $pegawai->riwayatJabatan->sortByDesc('tmt_jabatan')->first();
            if ($latestJabatan?->jabatan?->rumpunJabatan?->nama === 'PPPK') {
                abort(403, 'PPPK tidak memiliki skema Kenaikan Pangkat sesuai ketentuan BKN. Kenaikan Pangkat hanya berlaku untuk PNS.');
            }
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'golongan_id' => 'required|integer|exists:golongan_pangkats,id',
            'nomor_sk' => 'nullable|string',
            'tmt_pangkat' => 'required|date',
            'tanggal_sk' => 'required|date',
            'file_sk' => 'nullable|file|mimes:pdf|max:5120',
            'google_drive_link' => 'nullable|url|max:500',
        ];
    }
}
