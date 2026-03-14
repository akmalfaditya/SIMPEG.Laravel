<?php

namespace App\Services;

use App\Enums\TingkatHukuman;
use App\Models\Pegawai;
use Carbon\Carbon;

class SatyalencanaService
{
    public function getEligibleCandidates(): array
    {
        $pegawaiList = Pegawai::with([
            'riwayatPangkat.golongan',
            'riwayatJabatan.jabatan.rumpunJabatan',
            'riwayatHukumanDisiplin',
            'riwayatPenghargaan',
        ])->where('is_active', true)->get();

        $candidates = [];
        $today = today();

        foreach ($pegawaiList as $pegawai) {
            // Step F-1: Exclude PPPK employees (no Satyalencana scheme)
            $latestJabatan = $pegawai->riwayatJabatan->sortByDesc('tmt_jabatan')->first();
            if ($latestJabatan?->jabatan?->rumpunJabatan?->nama === 'PPPK') {
                continue;
            }

            // Step A: Start from tmt_cpns
            $startDate = Carbon::parse($pegawai->tmt_cpns);

            // Step B & C: Find the latest tmt_selesai_hukuman from Sedang/Berat punishments
            $latestResetDate = $pegawai->riwayatHukumanDisiplin
                ->filter(fn($h) =>
                    $h->tingkat_hukuman === TingkatHukuman::Sedang ||
                    $h->tingkat_hukuman === TingkatHukuman::Berat
                )
                ->whereNotNull('tmt_selesai_hukuman')
                ->sortByDesc('tmt_selesai_hukuman')
                ->first()
                ?->tmt_selesai_hukuman;

            if ($latestResetDate) {
                $startDate = Carbon::parse($latestResetDate);
            }

            // Check for CURRENTLY ACTIVE Sedang/Berat punishment → not yet eligible
            $hasActiveHukdis = $pegawai->riwayatHukumanDisiplin
                ->filter(fn($h) => $h->isAktif())
                ->filter(fn($h) =>
                    $h->tingkat_hukuman === TingkatHukuman::Sedang ||
                    $h->tingkat_hukuman === TingkatHukuman::Berat
                )->isNotEmpty();

            if ($hasActiveHukdis) {
                continue;
            }

            // Step D: Calculate pure service years from (possibly reset) startDate
            $masaKerjaMurni = (int) $startDate->diffInYears($today);

            // Step E: Determine milestone
            $milestone = null;
            if ($masaKerjaMurni >= 30) $milestone = 30;
            elseif ($masaKerjaMurni >= 20) $milestone = 20;
            elseif ($masaKerjaMurni >= 10) $milestone = 10;

            if ($milestone === null) continue;

            // Step F-2: Check if this tier was already awarded
            $alreadyAwarded = $pegawai->riwayatPenghargaan
                ->where('milestone', $milestone)->isNotEmpty();
            if ($alreadyAwarded) continue;

            $pangkat = $pegawai->riwayatPangkat->sortByDesc('tmt_pangkat')->first();

            $namaPenghargaan = match ($milestone) {
                10 => 'Satyalencana Karya Satya X Tahun',
                20 => 'Satyalencana Karya Satya XX Tahun',
                30 => 'Satyalencana Karya Satya XXX Tahun',
                default => '',
            };

            $candidates[] = [
                'pegawai_id' => $pegawai->id,
                'nip' => $pegawai->nip,
                'nama_lengkap' => $pegawai->nama_lengkap,
                'pangkat_terakhir' => $pangkat?->golongan?->label ?? '-',
                'jabatan_terakhir' => $latestJabatan?->jabatan?->nama_jabatan ?? '-',
                'masa_kerja_tahun' => $masaKerjaMurni,
                'tanggal_mulai_hitung' => $startDate->format('d/m/Y'),
                'is_reset' => $latestResetDate !== null,
                'milestone' => $milestone,
                'nama_penghargaan' => $namaPenghargaan,
            ];
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

    public function awardCandidate(int $pegawaiId, int $milestone, array $data): \App\Models\RiwayatPenghargaan
    {
        $namaPenghargaan = match ($milestone) {
            10 => 'Satyalencana Karya Satya X Tahun',
            20 => 'Satyalencana Karya Satya XX Tahun',
            30 => 'Satyalencana Karya Satya XXX Tahun',
            default => 'Satyalencana Karya Satya',
        };

        return \App\Models\RiwayatPenghargaan::create([
            'pegawai_id' => $pegawaiId,
            'nama_penghargaan' => $namaPenghargaan,
            'tahun' => $data['tahun'] ?? date('Y'),
            'milestone' => $milestone,
            'nomor_sk' => $data['nomor_sk'] ?? null,
            'tanggal_sk' => $data['tanggal_sk'] ?? null,
        ]);
    }
}
