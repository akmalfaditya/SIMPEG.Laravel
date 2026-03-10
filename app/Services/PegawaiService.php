<?php

namespace App\Services;

use App\Models\Pegawai;
use App\Models\TabelGaji;
use App\DTOs\PegawaiDTO;
use Illuminate\Support\Facades\DB;

class PegawaiService
{
    public function getAll()
    {
        return Pegawai::with(['riwayatPangkat', 'riwayatJabatan.jabatan', 'riwayatKgb', 'riwayatHukumanDisiplin'])
            ->where('is_active', true)
            ->get();
    }

    public function getById(int $id)
    {
        return Pegawai::with([
            'riwayatPangkat', 'riwayatJabatan.jabatan', 'riwayatKgb',
            'riwayatHukumanDisiplin', 'riwayatPendidikan',
            'riwayatLatihanJabatan', 'penilaianKinerja',
        ])->findOrFail($id);
    }

    public function search(string $keyword)
    {
        return Pegawai::with(['riwayatPangkat', 'riwayatJabatan.jabatan', 'riwayatKgb', 'riwayatHukumanDisiplin'])
            ->where('is_active', true)
            ->where(function ($q) use ($keyword) {
                $q->where('nip', 'like', "%{$keyword}%")
                  ->orWhere('nama_lengkap', 'like', "%{$keyword}%")
                  ->orWhere('unit_kerja', 'like', "%{$keyword}%");
            })
            ->get();
    }

    /**
     * One-Stop Creation Flow:
     * Creates pegawai + auto-generates initial RiwayatPangkat & RiwayatJabatan
     * and looks up starting salary from TabelGaji.
     */
    public function create(PegawaiDTO $dto, int $golonganId, int $jabatanId): Pegawai
    {
        return DB::transaction(function () use ($dto, $golonganId, $jabatanId) {
            // 1. Lookup starting salary (masa_kerja = 0)
            $gajiPokok = TabelGaji::where('golongan_id', $golonganId)
                ->where('masa_kerja_tahun', 0)
                ->value('gaji_pokok') ?? 0;

            // 2. Create pegawai with auto-calculated gaji
            $data = $dto->toArray();
            $data['gaji_pokok'] = $gajiPokok;
            $pegawai = Pegawai::create($data);

            // 3. Auto-create first RiwayatPangkat
            $pegawai->riwayatPangkat()->create([
                'golongan_id' => $golonganId,
                'tmt_pangkat'  => $pegawai->tmt_cpns,
                'tanggal_sk'   => $pegawai->tmt_cpns,
                'nomor_sk'     => 'SK-CPNS/' . $pegawai->tmt_cpns->year . '/AUTO',
            ]);

            // 4. Auto-create first RiwayatJabatan
            $pegawai->riwayatJabatan()->create([
                'jabatan_id'  => $jabatanId,
                'tmt_jabatan' => $pegawai->tmt_cpns,
                'tanggal_sk'  => $pegawai->tmt_cpns,
                'nomor_sk'    => 'SK-JAB/' . $pegawai->tmt_cpns->year . '/AUTO',
            ]);

            return $pegawai;
        });
    }

    public function update(Pegawai $pegawai, PegawaiDTO $dto): bool
    {
        return DB::transaction(function() use ($pegawai, $dto) {
            return $pegawai->update($dto->toArray());
        });
    }

    public function delete(Pegawai $pegawai): bool
    {
        return DB::transaction(function() use ($pegawai) {
            $pegawai->is_active = false;
            $pegawai->save();
            return $pegawai->delete();
        });
    }
}
