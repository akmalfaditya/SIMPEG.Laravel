<?php

namespace App\Services;

use App\Enums\GolonganRuang;
use App\Models\TabelGaji;
use Illuminate\Support\Collection;

class TabelGajiService
{
    public function getSummaryPerGolongan(): Collection
    {
        return TabelGaji::selectRaw('golongan_ruang, COUNT(*) as jumlah_mkg, MIN(gaji_pokok) as gaji_min, MAX(gaji_pokok) as gaji_max')
            ->groupBy('golongan_ruang')
            ->orderBy('golongan_ruang')
            ->get()
            ->map(fn ($row) => (object) [
                'golongan' => $row->golongan_ruang,
                'jumlah_mkg' => $row->jumlah_mkg,
                'gaji_min' => $row->gaji_min,
                'gaji_max' => $row->gaji_max,
            ]);
    }

    public function getByGolongan(int $golongan): Collection
    {
        return TabelGaji::where('golongan_ruang', $golongan)
            ->orderBy('masa_kerja_tahun')
            ->get();
    }

    public function update(TabelGaji $tabelGaji, int $gajiPokok): bool
    {
        return $tabelGaji->update(['gaji_pokok' => $gajiPokok]);
    }

    public function store(int $golonganRuang, int $masaKerjaTahun, int $gajiPokok): TabelGaji
    {
        return TabelGaji::create([
            'golongan_ruang' => $golonganRuang,
            'masa_kerja_tahun' => $masaKerjaTahun,
            'gaji_pokok' => $gajiPokok,
        ]);
    }

    public function delete(TabelGaji $tabelGaji): bool
    {
        return $tabelGaji->delete();
    }
}
