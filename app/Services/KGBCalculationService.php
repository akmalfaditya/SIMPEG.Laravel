<?php

namespace App\Services;

use App\Models\GolonganPangkat;
use App\Models\Pegawai;
use App\Models\TabelGaji;

class KGBCalculationService
{
    public function calculateNewSalary(int $golonganId, int $masaKerjaTahun): ?float
    {
        $entry = TabelGaji::where('golongan_id', $golonganId)
            ->where('masa_kerja_tahun', $masaKerjaTahun)
            ->first();

        return $entry?->gaji_pokok ? (float) $entry->gaji_pokok : null;
    }

    public function getNextKGBSalary(Pegawai $pegawai): array
    {
        $pegawai->loadMissing(['riwayatPangkat.golongan', 'riwayatKgb']);

        $pangkatTerakhir = $pegawai->riwayatPangkat->sortByDesc('tmt_pangkat')->first();
        if (!$pangkatTerakhir) {
            return ['gaji_lama' => $pegawai->gaji_pokok, 'gaji_baru' => null, 'golongan' => null, 'masa_kerja_tahun' => null];
        }

        $golonganId = $pangkatTerakhir->golongan_id;
        $golongan = $pangkatTerakhir->golongan;
        $today = today();

        // Hitung masa kerja golongan dalam tahun (dari TMT pangkat terakhir)
        $totalMonths = (($today->year - $pangkatTerakhir->tmt_pangkat->year) * 12)
            + $today->month - $pangkatTerakhir->tmt_pangkat->month;
        $masaKerjaGolTahun = intdiv($totalMonths, 12);

        // Tambah 2 tahun untuk KGB berikutnya (MKG setelah KGB)
        $lastKgb = $pegawai->riwayatKgb->sortByDesc('tmt_kgb')->first();
        $masaKerjaTotalBulan = (($today->year - $pegawai->tmt_cpns->year) * 12)
            + $today->month - $pegawai->tmt_cpns->month;
        $masaKerjaTotalTahun = intdiv($masaKerjaTotalBulan, 12);

        // Untuk KGB berikutnya, MKG naik 2 tahun dari terakhir
        $mkgUntukKgb = $lastKgb
            ? $lastKgb->masa_kerja_golongan_tahun + 2
            : $masaKerjaGolTahun;

        $gajiBaru = $this->calculateNewSalary($golonganId, $mkgUntukKgb);

        return [
            'gaji_lama' => (float) $pegawai->gaji_pokok,
            'gaji_baru' => $gajiBaru,
            'golongan' => $golongan?->label,
            'masa_kerja_tahun' => $mkgUntukKgb,
            'masa_kerja_total_tahun' => $masaKerjaTotalTahun,
        ];
    }
}
