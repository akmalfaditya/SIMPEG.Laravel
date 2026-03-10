<?php

namespace App\Services;

use App\Models\GolonganPangkat;
use App\Models\Pegawai;

class KGBCalculationService
{
    public function __construct(private SalaryCalculatorService $salaryService) {}

    public function calculateNewSalary(int $golonganId, int $masaKerjaTahun): ?float
    {
        return $this->salaryService->calculateGaji($golonganId, $masaKerjaTahun);
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

        $lastKgb = $pegawai->riwayatKgb->sortByDesc('tmt_kgb')->first();
        $masaKerjaTotalBulan = (($today->year - $pegawai->tmt_cpns->year) * 12)
            + $today->month - $pegawai->tmt_cpns->month;
        $masaKerjaTotalTahun = intdiv($masaKerjaTotalBulan, 12);

        // Untuk KGB berikutnya, MKG naik 2 tahun dari terakhir
        // Jika belum ada KGB, hitung MKG awal dari tmt_cpns (total masa kerja)
        $mkgUntukKgb = $lastKgb
            ? $lastKgb->masa_kerja_golongan_tahun + 2
            : $masaKerjaTotalTahun;

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
