<?php

namespace App\Services;

use App\Enums\TingkatHukuman;
use App\Models\Pegawai;

class SatyalencanaService
{
    public function getEligibleCandidates(): array
    {
        $pegawaiList = Pegawai::with([
            'riwayatPangkat', 'riwayatJabatan.jabatan',
            'riwayatHukumanDisiplin', 'riwayatPenghargaan',
        ])->where('is_active', true)->get();

        $candidates = [];
        $today = today();

        foreach ($pegawaiList as $pegawai) {
            $totalMonths = (($today->year - $pegawai->tmt_cpns->year) * 12)
                + $today->month - $pegawai->tmt_cpns->month;
            $masaKerjaTahun = intdiv($totalMonths, 12);

            $milestone = null;
            if ($masaKerjaTahun >= 30) $milestone = 30;
            elseif ($masaKerjaTahun >= 20) $milestone = 20;
            elseif ($masaKerjaTahun >= 10) $milestone = 10;

            if ($milestone === null) continue;

            $alreadyAwarded = $pegawai->riwayatPenghargaan
                ->where('milestone', $milestone)->isNotEmpty();
            if ($alreadyAwarded) continue;

            $hasDisqualifying = $pegawai->riwayatHukumanDisiplin
                ->filter(fn($h) =>
                    $h->tingkat_hukuman === TingkatHukuman::Sedang ||
                    $h->tingkat_hukuman === TingkatHukuman::Berat
                )->isNotEmpty();

            $pangkat = $pegawai->riwayatPangkat->sortByDesc('tmt_pangkat')->first();
            $jabatan = $pegawai->riwayatJabatan->sortByDesc('tmt_jabatan')->first();

            $namaPenghargaan = match ($milestone) {
                10 => 'Satyalencana Karya Satya X Tahun',
                20 => 'Satyalencana Karya Satya XX Tahun',
                30 => 'Satyalencana Karya Satya XXX Tahun',
                default => '',
            };

            if (!$hasDisqualifying) {
                $candidates[] = [
                    'pegawai_id' => $pegawai->id,
                    'nip' => $pegawai->nip,
                    'nama_lengkap' => $pegawai->nama_lengkap,
                    'pangkat_terakhir' => $pangkat?->golongan_ruang?->label() ?? '-',
                    'jabatan_terakhir' => $jabatan?->jabatan?->nama_jabatan ?? '-',
                    'masa_kerja_tahun' => $masaKerjaTahun,
                    'milestone' => $milestone,
                    'nama_penghargaan' => $namaPenghargaan,
                ];
            }
        }

        usort($candidates, function ($a, $b) {
            if ($a['milestone'] !== $b['milestone']) return $b['milestone'] <=> $a['milestone'];
            return strcmp($a['nama_lengkap'], $b['nama_lengkap']);
        });

        return $candidates;
    }

    public function getCandidatesByMilestone(int $years): array
    {
        return array_values(array_filter(
            $this->getEligibleCandidates(),
            fn($c) => $c['milestone'] === $years
        ));
    }
}
