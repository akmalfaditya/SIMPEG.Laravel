<?php

namespace App\Services;

use App\Enums\JenisSanksi;
use App\Models\Pegawai;

class KGBService
{
    public function __construct(private KGBCalculationService $calculationService) {}

    public function getAllKGBStatus(): array
    {
        $pegawaiList = Pegawai::with(['riwayatKgb', 'riwayatPangkat.golongan', 'riwayatHukumanDisiplin'])
            ->where('is_active', true)->get();
        $alerts = [];
        $today = today();

        foreach ($pegawaiList as $pegawai) {
            $lastKGB = $pegawai->riwayatKgb->sortByDesc('tmt_kgb')->first();
            if (!$lastKGB) continue;

            $jatuhTempo = $lastKGB->tmt_kgb->copy()->addYears(2);
            $pangkat = $pegawai->riwayatPangkat->sortByDesc('tmt_pangkat')->first();

            // GAP-08: Check for active Penundaan KGB sanctions
            $hukdisFlag = false;
            $hukdisNote = null;
            $totalPenundaanTahun = 0;

            $activeHukdisKgb = $pegawai->riwayatHukumanDisiplin
                ->filter(function ($h) {
                    return $h->isAktif()
                        && $h->jenis_sanksi === JenisSanksi::PenundaanKgb;
                });

            foreach ($activeHukdisKgb as $h) {
                $durasi = $h->durasi_tahun ?? 1;
                $totalPenundaanTahun += $durasi;
            }

            if ($totalPenundaanTahun > 0) {
                $jatuhTempo = $jatuhTempo->addYears($totalPenundaanTahun);
                $hukdisFlag = true;
                $hukdisNote = "Ditunda {$totalPenundaanTahun} Tahun (Hukdis)";
            }

            $hariMenuju = $today->diffInDays($jatuhTempo, false);
            $isEligible = $hariMenuju <= 0;

            if ($hukdisFlag) {
                $status = 'Ditunda';
            } elseif ($isEligible) {
                $status = 'Eligible';
            } elseif ($hariMenuju <= 60) {
                $status = 'H-60';
            } else {
                $status = 'Mendekati';
            }

            // GAP-30: Estimate next KGB salary from TabelGaji lookup
            $kgbEstimate = $this->calculationService->getNextKGBSalary($pegawai);

            $alerts[] = [
                'pegawai_id' => $pegawai->id,
                'nip' => $pegawai->nip,
                'nama_lengkap' => $pegawai->nama_lengkap,
                'pangkat_terakhir' => $pangkat?->golongan?->label ?? '-',
                'tmt_kgb_terakhir' => $lastKGB->tmt_kgb,
                'tanggal_jatuh_tempo' => $jatuhTempo,
                'hari_menuju_jatuh_tempo' => $hariMenuju,
                'is_eligible' => $isEligible,
                'status' => $status,
                'hukdis_flag' => $hukdisFlag,
                'hukdis_note' => $hukdisNote,
                'gaji_pokok' => (float) $pegawai->gaji_pokok,
                'est_gaji_baru' => $kgbEstimate['gaji_baru'],
            ];
        }

        usort($alerts, fn($a, $b) => $a['hari_menuju_jatuh_tempo'] <=> $b['hari_menuju_jatuh_tempo']);
        return $alerts;
    }

    public function getUpcomingKGB(int $daysAhead = 60): array
    {
        $all = $this->getAllKGBStatus();
        return array_values(array_filter($all, fn($a) => $a['hari_menuju_jatuh_tempo'] <= $daysAhead && $a['hari_menuju_jatuh_tempo'] > 0));
    }

    public function getEligiblePegawai(): array
    {
        $all = $this->getAllKGBStatus();
        return array_values(array_filter($all, fn($a) => $a['is_eligible']));
    }

    public function getDitundaPegawai(): array
    {
        $all = $this->getAllKGBStatus();
        return array_values(array_filter($all, fn($a) => $a['hukdis_flag']));
    }
}
