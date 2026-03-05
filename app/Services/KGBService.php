<?php

namespace App\Services;

use App\Models\Pegawai;

class KGBService
{
    public function getAllKGBStatus(): array
    {
        $pegawaiList = Pegawai::with(['riwayatKgb', 'riwayatPangkat'])
            ->where('is_active', true)->get();
        $alerts = [];
        $today = today();

        foreach ($pegawaiList as $pegawai) {
            $lastKGB = $pegawai->riwayatKgb->sortByDesc('tmt_kgb')->first();
            if (!$lastKGB) continue;

            $jatuhTempo = $lastKGB->tmt_kgb->copy()->addYears(2);
            $hariMenuju = $today->diffInDays($jatuhTempo, false);
            $pangkat = $pegawai->riwayatPangkat->sortByDesc('tmt_pangkat')->first();

            $isEligible = $hariMenuju <= 0;
            $status = $isEligible ? 'Eligible' : ($hariMenuju <= 60 ? 'H-60' : 'Mendekati');

            $alerts[] = [
                'pegawai_id' => $pegawai->id,
                'nip' => $pegawai->nip,
                'nama_lengkap' => $pegawai->nama_lengkap,
                'pangkat_terakhir' => $pangkat?->golongan_ruang?->label() ?? '-',
                'tmt_kgb_terakhir' => $lastKGB->tmt_kgb,
                'tanggal_jatuh_tempo' => $jatuhTempo,
                'hari_menuju_jatuh_tempo' => $hariMenuju,
                'is_eligible' => $isEligible,
                'status' => $status,
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
}
