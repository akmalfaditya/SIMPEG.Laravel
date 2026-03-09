<?php

namespace App\Services;

use App\Models\Pegawai;
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

    public function create(PegawaiDTO $dto): Pegawai
    {
        return DB::transaction(function() use ($dto) {
            return Pegawai::create($dto->toArray());
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
