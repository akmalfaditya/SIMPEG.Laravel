<?php

namespace App\Services;

use App\Models\TabelGaji;
use Illuminate\Support\Collection;

class TabelGajiService
{
    public function getSummaryPerGolongan(): Collection
    {
        return TabelGaji::selectRaw('golongan_id, COUNT(*) as jumlah_mkg, MIN(gaji_pokok) as gaji_min, MAX(gaji_pokok) as gaji_max')
            ->groupBy('golongan_id')
            ->orderBy('golongan_id')
            ->get()
            ->map(fn($row) => (object) [
                'golongan' => $row->golongan,
                'jumlah_mkg' => $row->jumlah_mkg,
                'gaji_min' => $row->gaji_min,
                'gaji_max' => $row->gaji_max,
            ]);
    }

    public function getByGolongan(int $golonganId): Collection
    {
        return TabelGaji::where('golongan_id', $golonganId)
            ->orderBy('masa_kerja_tahun')
            ->get();
    }

    public function update(TabelGaji $tabelGaji, int $gajiPokok): bool
    {
        return $tabelGaji->update(['gaji_pokok' => $gajiPokok]);
    }

    public function store(int $golonganId, int $masaKerjaTahun, int $gajiPokok): TabelGaji
    {
        return TabelGaji::create([
            'golongan_id' => $golonganId,
            'masa_kerja_tahun' => $masaKerjaTahun,
            'gaji_pokok' => $gajiPokok,
        ]);
    }

    public function delete(TabelGaji $tabelGaji): bool
    {
        return $tabelGaji->delete();
    }
}
