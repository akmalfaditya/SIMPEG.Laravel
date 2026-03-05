<?php

namespace App\Services;

use App\Models\Pegawai;

class PegawaiService
{
    public function getAll()
    {
        return Pegawai::with(['riwayatPangkat', 'riwayatJabatan.jabatan', 'riwayatKgb'])
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
        return Pegawai::with(['riwayatPangkat', 'riwayatJabatan.jabatan', 'riwayatKgb'])
            ->where('is_active', true)
            ->where(function ($q) use ($keyword) {
                $q->where('nip', 'like', "%{$keyword}%")
                  ->orWhere('nama_lengkap', 'like', "%{$keyword}%")
                  ->orWhere('unit_kerja', 'like', "%{$keyword}%");
            })
            ->get();
    }

    public function create(array $data): Pegawai
    {
        $data['is_active'] = true;
        return Pegawai::create($data);
    }

    public function update(Pegawai $pegawai, array $data): bool
    {
        return $pegawai->update($data);
    }

    public function delete(Pegawai $pegawai): bool
    {
        $pegawai->is_active = false;
        $pegawai->save();
        return $pegawai->delete();
    }
}
