<?php

namespace App\Services;

use App\Models\Pegawai;

class DashboardService
{
    public function __construct(
        private KGBService $kgbService,
        private PensiunService $pensiunService,
        private SatyalencanaService $satyalencanaService,
    ) {}

    public function getDashboardData(): array
    {
        $allPegawai = Pegawai::with(['riwayatPangkat', 'riwayatJabatan.jabatan', 'riwayatKgb'])
            ->where('is_active', true)->get();

        $kgbAlerts = array_merge(
            $this->kgbService->getUpcomingKGB(60),
            $this->kgbService->getEligiblePegawai()
        );
        usort($kgbAlerts, fn($a, $b) => $a['hari_menuju_jatuh_tempo'] <=> $b['hari_menuju_jatuh_tempo']);

        $pensiunAlerts = $this->pensiunService->getPensiunAlerts();
        $satyalencanaCandidates = $this->satyalencanaService->getEligibleCandidates();

        $charts = $this->generateChartData($allPegawai);

        return [
            'total_pegawai' => $allPegawai->count(),
            'total_pegawai_aktif' => $allPegawai->where('is_active', true)->count(),
            'kgb_alerts' => array_slice($kgbAlerts, 0, 10),
            'kgb_alert_count' => count($kgbAlerts),
            'pensiun_alerts' => array_slice($pensiunAlerts, 0, 10),
            'pensiun_alert_count' => count($pensiunAlerts),
            'satyalencana_candidates' => array_slice($satyalencanaCandidates, 0, 10),
            'satyalencana_eligible_count' => count($satyalencanaCandidates),
            'charts' => $charts,
        ];
    }

    private function generateChartData($allPegawai): array
    {
        $today = today();
        $golongan = [];
        $gender = [];
        $usia = [];
        $unitKerja = [];

        foreach ($allPegawai as $peg) {
            // Golongan distribution
            $pangkat = $peg->riwayatPangkat->sortByDesc('tmt_pangkat')->first();
            if ($pangkat) {
                $label = $pangkat->golongan_ruang->label();
                $golongan[$label] = ($golongan[$label] ?? 0) + 1;
            }

            // Gender distribution
            $gLabel = $peg->jenis_kelamin->label();
            $gender[$gLabel] = ($gender[$gLabel] ?? 0) + 1;

            // Usia distribution
            $age = $today->year - $peg->tanggal_lahir->year
                - ($today->dayOfYear < $peg->tanggal_lahir->dayOfYear ? 1 : 0);
            $bracket = match (true) {
                $age < 25 => '<25', $age < 30 => '25-29', $age < 35 => '30-34',
                $age < 40 => '35-39', $age < 45 => '40-44', $age < 50 => '45-49',
                $age < 55 => '50-54', default => '55+',
            };
            $usia[$bracket] = ($usia[$bracket] ?? 0) + 1;

            // Unit Kerja
            $unit = $peg->unit_kerja ?? 'Belum Ditetapkan';
            $unitKerja[$unit] = ($unitKerja[$unit] ?? 0) + 1;
        }

        return [
            'golongan' => $golongan,
            'gender' => $gender,
            'usia' => $usia,
            'unit_kerja' => $unitKerja,
        ];
    }
}
