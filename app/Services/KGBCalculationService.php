<?php

namespace App\Services;

use App\Enums\GolonganRuang;
use App\Models\Pegawai;
use App\Models\TabelGaji;

class KGBCalculationService
{
    public function calculateNewSalary(GolonganRuang $golongan, int $masaKerjaTahun): ?float
    {
        $entry = TabelGaji::where('golongan_ruang', $golongan->value)
            ->where('masa_kerja_tahun', $masaKerjaTahun)
            ->first();

        return $entry?->gaji_pokok ? (float) $entry->gaji_pokok : null;
    }

    public function getNextKGBSalary(Pegawai $pegawai): array
    {
        $pegawai->loadMissing(['riwayatPangkat', 'riwayatKgb']);

        $pangkatTerakhir = $pegawai->riwayatPangkat->sortByDesc('tmt_pangkat')->first();
        if (!$pangkatTerakhir) {
            return ['gaji_lama' => $pegawai->gaji_pokok, 'gaji_baru' => null, 'golongan' => null, 'masa_kerja_tahun' => null];
        }

        $golongan = $pangkatTerakhir->golongan_ruang;
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

        $gajiBaru = $this->calculateNewSalary($golongan, $mkgUntukKgb);

        return [
            'gaji_lama' => (float) $pegawai->gaji_pokok,
            'gaji_baru' => $gajiBaru,
            'golongan' => $golongan->label(),
            'masa_kerja_tahun' => $mkgUntukKgb,
            'masa_kerja_total_tahun' => $masaKerjaTotalTahun,
        ];
    }
}
