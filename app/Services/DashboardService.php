<?php

namespace App\Services;

use App\Models\GolonganPangkat;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use Illuminate\Support\Carbon;

class DashboardService
{
    public function __construct(
        private KGBService $kgbService,
        private PensiunService $pensiunService,
        private SatyalencanaService $satyalencanaService,
    ) {}

    public function getFilterOptions(): array
    {
        $unitKerjaList = UnitKerja::orderBy('nama')->pluck('nama', 'id')->toArray();

        $golonganList = [];
        foreach (GolonganPangkat::where('is_active', true)->orderBy('golongan_ruang')->get() as $g) {
            $golonganList[$g->id] = $g->label;
        }

        return [
            'unit_kerja_list' => $unitKerjaList,
            'golongan_list' => $golonganList,
        ];
    }

    public function getDashboardData(array $filters = []): array
    {
        $query = Pegawai::with([
            'riwayatPangkat.golongan',
            'riwayatJabatan.jabatan',
            'riwayatKgb',
            'riwayatPendidikan',
            'riwayatLatihanJabatan',
            'jenisKelamin',
            'unitKerja',
        ])->where('is_active', true);

        if (!empty($filters['unit_kerja'])) {
            $query->where('unit_kerja_id', $filters['unit_kerja']);
        }

        if (!empty($filters['tmt_cpns_from'])) {
            $query->where('tmt_cpns', '>=', $filters['tmt_cpns_from']);
        }
        if (!empty($filters['tmt_cpns_to'])) {
            $query->where('tmt_cpns', '<=', $filters['tmt_cpns_to']);
        }

        $allPegawai = $query->get();

        // Apply golongan filter in-memory (requires latest riwayat_pangkat)
        if (!empty($filters['golongan'])) {
            $golFilter = (int) $filters['golongan'];
            $allPegawai = $allPegawai->filter(function ($peg) use ($golFilter) {
                $pangkat = $peg->riwayatPangkat->sortByDesc('tmt_pangkat')->first();
                return $pangkat && $pangkat->golongan_id === $golFilter;
            })->values();
        }

        $kgbAlerts = array_merge(
            $this->kgbService->getUpcomingKGB(60),
            $this->kgbService->getEligiblePegawai()
        );
        usort($kgbAlerts, fn($a, $b) => $a['hari_menuju_jatuh_tempo'] <=> $b['hari_menuju_jatuh_tempo']);

        $pensiunAlerts = $this->pensiunService->getPensiunAlerts();
        $satyalencanaCandidates = $this->satyalencanaService->getEligibleCandidates();

        $today = today();
        $charts = $this->generateChartData($allPegawai, $today);
        $advancedCharts = $this->generateAdvancedChartData($allPegawai, $today);
        $summaryPerUnit = $this->generateUnitKerjaSummary($allPegawai, $today);
        $stats = $this->calculateAdvancedStats($allPegawai, $today);

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
            'advanced_charts' => $advancedCharts,
            'summary_per_unit' => $summaryPerUnit,
            'stats' => $stats,
            'filters' => array_merge($filters, [
                'unit_kerja' => !empty($filters['unit_kerja'])
                    ? UnitKerja::find($filters['unit_kerja'])?->nama
                    : null,
            ]),
        ];
    }

    private function calculateAdvancedStats($allPegawai, Carbon $today): array
    {
        if ($allPegawai->isEmpty()) {
            return ['rata_rata_usia' => 0, 'rata_rata_masa_kerja' => 0, 'total_laki' => 0, 'total_perempuan' => 0];
        }

        $totalUsia = 0;
        $totalMasaKerja = 0;
        $totalLaki = 0;
        $totalPerempuan = 0;

        foreach ($allPegawai as $peg) {
            $age = $today->year - $peg->tanggal_lahir->year
                - ($today->dayOfYear < $peg->tanggal_lahir->dayOfYear ? 1 : 0);
            $totalUsia += $age;

            $masaKerjaBulan = (($today->year - $peg->tmt_cpns->year) * 12) + $today->month - $peg->tmt_cpns->month;
            $totalMasaKerja += $masaKerjaBulan;

            if ($peg->jenisKelamin?->nama === 'Laki-laki') {
                $totalLaki++;
            } else {
                $totalPerempuan++;
            }
        }

        $count = $allPegawai->count();
        $avgMasaKerjaBulan = $totalMasaKerja / $count;

        return [
            'rata_rata_usia' => round($totalUsia / $count, 1),
            'rata_rata_masa_kerja' => intdiv((int) $avgMasaKerjaBulan, 12) . ' Thn ' . ((int) $avgMasaKerjaBulan % 12) . ' Bln',
            'total_laki' => $totalLaki,
            'total_perempuan' => $totalPerempuan,
        ];
    }

    private function generateChartData($allPegawai, Carbon $today): array
    {
        $golongan = [];
        $gender = [];
        $usia = [];
        $unitKerja = [];

        foreach ($allPegawai as $peg) {
            $pangkat = $peg->riwayatPangkat->sortByDesc('tmt_pangkat')->first();
            if ($pangkat) {
                $label = $pangkat->golongan?->label ?? 'Tidak Diketahui';
                $golongan[$label] = ($golongan[$label] ?? 0) + 1;
            }

            $gLabel = $peg->jenisKelamin?->nama ?? 'Tidak Diketahui';
            $gender[$gLabel] = ($gender[$gLabel] ?? 0) + 1;

            $age = $today->year - $peg->tanggal_lahir->year
                - ($today->dayOfYear < $peg->tanggal_lahir->dayOfYear ? 1 : 0);
            $bracket = match (true) {
                $age < 25 => '<25',
                $age < 30 => '25-29',
                $age < 35 => '30-34',
                $age < 40 => '35-39',
                $age < 45 => '40-44',
                $age < 50 => '45-49',
                $age < 55 => '50-54',
                default => '55+',
            };
            $usia[$bracket] = ($usia[$bracket] ?? 0) + 1;

            $unit = $peg->unitKerja?->nama ?? 'Belum Ditetapkan';
            $unitKerja[$unit] = ($unitKerja[$unit] ?? 0) + 1;
        }

        return [
            'golongan' => $golongan,
            'gender' => $gender,
            'usia' => $usia,
            'unit_kerja' => $unitKerja,
        ];
    }

    private function generateAdvancedChartData($allPegawai, Carbon $today): array
    {
        // 1. Tren KGB per bulan (12 bulan ke depan)
        $kgbTren = [];
        for ($i = 0; $i < 12; $i++) {
            $month = $today->copy()->addMonths($i);
            $label = $month->translatedFormat('M Y');
            $kgbTren[$label] = 0;
        }

        foreach ($allPegawai as $peg) {
            $lastKGB = $peg->riwayatKgb->sortByDesc('tmt_kgb')->first();
            if (!$lastKGB) continue;
            $jatuhTempo = $lastKGB->tmt_kgb->copy()->addYears(2);
            $diffMonths = (($jatuhTempo->year - $today->year) * 12) + $jatuhTempo->month - $today->month;
            if ($diffMonths >= 0 && $diffMonths < 12) {
                $label = $jatuhTempo->copy()->startOfMonth()->translatedFormat('M Y');
                if (isset($kgbTren[$label])) {
                    $kgbTren[$label]++;
                }
            }
        }

        // 2. Proyeksi Pensiun per tahun (5 tahun ke depan)
        $pensiunProyeksi = [];
        for ($y = 0; $y < 5; $y++) {
            $pensiunProyeksi[$today->year + $y] = 0;
        }

        foreach ($allPegawai as $peg) {
            $jabatanTerakhir = $peg->riwayatJabatan->sortByDesc('tmt_jabatan')->first();
            if (!$jabatanTerakhir?->jabatan) continue;
            $bup = $jabatanTerakhir->jabatan->bup;
            $tglPensiun = $peg->tanggal_lahir->copy()->addYears($bup);
            if ($tglPensiun->year >= $today->year && $tglPensiun->year < $today->year + 5) {
                $pensiunProyeksi[$tglPensiun->year]++;
            }
        }

        // 3. Distribusi Pendidikan Terakhir
        $pendidikan = [];
        $pendidikanOrder = ['S3', 'S2', 'S1', 'D4', 'D3', 'D2', 'D1', 'SMA/SMK', 'Lainnya'];
        foreach ($allPegawai as $peg) {
            $lastEdu = $peg->riwayatPendidikan->sortByDesc('tahun_lulus')->first();
            $level = $lastEdu ? $lastEdu->tingkat_pendidikan : 'Tidak Ada Data';
            $pendidikan[$level] = ($pendidikan[$level] ?? 0) + 1;
        }

        // 4. Distribusi Jenis Jabatan
        $jenisJabatan = [];
        foreach ($allPegawai as $peg) {
            $jabatanTerakhir = $peg->riwayatJabatan->sortByDesc('tmt_jabatan')->first();
            $label = $jabatanTerakhir?->jabatan?->jenis_jabatan?->label() ?? 'Belum Ada';
            $jenisJabatan[$label] = ($jenisJabatan[$label] ?? 0) + 1;
        }

        // 5. Distribusi Masa Kerja
        $masaKerja = [];
        $mkBrackets = ['<5 Thn' => 0, '5-9 Thn' => 0, '10-14 Thn' => 0, '15-19 Thn' => 0, '20-24 Thn' => 0, '25-29 Thn' => 0, '30+ Thn' => 0];
        foreach ($allPegawai as $peg) {
            $years = $today->year - $peg->tmt_cpns->year;
            $bracket = match (true) {
                $years < 5 => '<5 Thn',
                $years < 10 => '5-9 Thn',
                $years < 15 => '10-14 Thn',
                $years < 20 => '15-19 Thn',
                $years < 25 => '20-24 Thn',
                $years < 30 => '25-29 Thn',
                default => '30+ Thn',
            };
            $mkBrackets[$bracket]++;
        }

        return [
            'kgb_tren' => $kgbTren,
            'pensiun_proyeksi' => $pensiunProyeksi,
            'pendidikan' => $pendidikan,
            'jenis_jabatan' => $jenisJabatan,
            'masa_kerja' => $mkBrackets,
        ];
    }

    private function generateUnitKerjaSummary($allPegawai, Carbon $today): array
    {
        $summary = [];

        foreach ($allPegawai as $peg) {
            $unit = $peg->unitKerja?->nama ?? 'Belum Ditetapkan';
            if (!isset($summary[$unit])) {
                $summary[$unit] = ['unit_kerja' => $unit, 'total' => 0, 'laki' => 0, 'perempuan' => 0, 'total_usia' => 0];
            }
            $summary[$unit]['total']++;

            if ($peg->jenisKelamin?->nama === 'Laki-laki') {
                $summary[$unit]['laki']++;
            } else {
                $summary[$unit]['perempuan']++;
            }

            $age = $today->year - $peg->tanggal_lahir->year
                - ($today->dayOfYear < $peg->tanggal_lahir->dayOfYear ? 1 : 0);
            $summary[$unit]['total_usia'] += $age;
        }

        $result = [];
        foreach ($summary as $unit => $data) {
            $data['rata_rata_usia'] = $data['total'] > 0 ? round($data['total_usia'] / $data['total'], 1) : 0;
            unset($data['total_usia']);
            $result[] = $data;
        }

        usort($result, fn($a, $b) => $b['total'] <=> $a['total']);
        return $result;
    }
}
