<?php

namespace App\Services;

use App\Models\Pegawai;
use App\Models\TabelGaji;
use Carbon\Carbon;

class SalaryCalculatorService
{
    /**
     * TMT Relay Baton (Tongkat Estafet TMT).
     *
     * Synchronize pegawai.gaji_pokok based on whichever record has the most
     * recent TMT between RiwayatPangkat and RiwayatKgb. BKN rule: the latest
     * TMT is the single source of truth for current base salary.
     */
    public function syncCurrentSalary(Pegawai $pegawai): void
    {
        $latestPangkat = $pegawai->riwayatPangkat()->orderByDesc('tmt_pangkat')->first();
        $latestKgb = $pegawai->riwayatKgb()->orderByDesc('tmt_kgb')->first();

        // Both null — no salary data at all
        if (!$latestPangkat && !$latestKgb) {
            Pegawai::where('id', $pegawai->id)->update(['gaji_pokok' => 0]);
            return;
        }

        // Only Pangkat exists — calculate from TabelGaji
        if ($latestPangkat && !$latestKgb) {
            $mkg = $this->calculateMkg($pegawai);
            $gaji = $this->calculateGaji($latestPangkat->golongan_id, $mkg);
            Pegawai::where('id', $pegawai->id)->update(['gaji_pokok' => $gaji ?? 0]);
            return;
        }

        // Only KGB exists (rare) — use gaji_baru directly
        if (!$latestPangkat && $latestKgb) {
            Pegawai::where('id', $pegawai->id)->update(['gaji_pokok' => $latestKgb->gaji_baru]);
            return;
        }

        // Both exist — the latest TMT wins (Tongkat Estafet)
        if ($latestKgb->tmt_kgb >= $latestPangkat->tmt_pangkat) {
            Pegawai::where('id', $pegawai->id)->update(['gaji_pokok' => $latestKgb->gaji_baru]);
        } else {
            $mkg = $this->calculateMkg($pegawai);
            $gaji = $this->calculateGaji($latestPangkat->golongan_id, $mkg);
            Pegawai::where('id', $pegawai->id)->update(['gaji_pokok' => $gaji ?? 0]);
        }
    }

    /**
     * Calculate Masa Kerja Golongan (MKG) in years from tmt_cpns.
     */
    private function calculateMkg(Pegawai $pegawai): int
    {
        if (!$pegawai->tmt_cpns) {
            return 0;
        }

        $totalMonths = (int) $pegawai->tmt_cpns->diffInMonths(today());
        return intdiv($totalMonths, 12);
    }

    /**
     * Calculate gaji pokok from TabelGaji lookup.
     * Exact MKG match first; fallback to closest lower MKG in that Golongan.
     */
    public function calculateGaji(int $golonganId, int $mkgTahun): ?float
    {
        // Exact match
        $entry = TabelGaji::where('golongan_id', $golonganId)
            ->where('masa_kerja_tahun', $mkgTahun)
            ->first();

        if ($entry) {
            return (float) $entry->gaji_pokok;
        }

        // Fallback: closest lower MKG
        $entry = TabelGaji::where('golongan_id', $golonganId)
            ->where('masa_kerja_tahun', '<', $mkgTahun)
            ->orderByDesc('masa_kerja_tahun')
            ->first();

        return $entry ? (float) $entry->gaji_pokok : null;
    }

    /**
     * Calculate the predicted next KGB date.
     * Takes the MAX of (latest tmt_kgb, latest tmt_pangkat) and adds 2 years.
     */
    public function calculateNextKgbDate(Pegawai $pegawai): ?Carbon
    {
        $pegawai->loadMissing(['riwayatKgb', 'riwayatPangkat']);

        $latestKgb = $pegawai->riwayatKgb->sortByDesc('tmt_kgb')->first();
        $latestPangkat = $pegawai->riwayatPangkat->sortByDesc('tmt_pangkat')->first();

        $dates = [];
        if ($latestKgb) {
            $dates[] = $latestKgb->tmt_kgb;
        }
        if ($latestPangkat) {
            $dates[] = $latestPangkat->tmt_pangkat;
        }

        if (empty($dates)) {
            return null;
        }

        $maxDate = collect($dates)->max();

        return $maxDate->copy()->addYears(2);
    }
}
