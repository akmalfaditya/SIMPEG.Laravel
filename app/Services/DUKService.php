<?php

namespace App\Services;

use App\Models\Pegawai;

class DUKService
{
    public function getDUK(): array
    {
        $pegawaiList = Pegawai::with([
            'riwayatPangkat.golongan',
            'riwayatJabatan.jabatan',
            'riwayatPendidikan',
            'riwayatLatihanJabatan',
        ])->where('is_active', true)->get();

        $today = today();
        $entries = [];

        foreach ($pegawaiList as $p) {
            $pangkat = $p->riwayatPangkat->sortByDesc('tmt_pangkat')->first();
            $jabatan = $p->riwayatJabatan->sortByDesc('tmt_jabatan')->first();
            $pendidikan = $p->riwayatPendidikan->sortByDesc('tahun_lulus')->first();
            $latihan = $p->riwayatLatihanJabatan->sortByDesc('tahun_pelaksanaan')->first();

            $totalMonths = (($today->year - $p->tmt_cpns->year) * 12) + $today->month - $p->tmt_cpns->month;
            $years = intdiv($totalMonths, 12);
            $months = $totalMonths % 12;
            $usia = $today->year - $p->tanggal_lahir->year - ($today->dayOfYear < $p->tanggal_lahir->dayOfYear ? 1 : 0);

            $entries[] = [
                'pegawai_id' => $p->id,
                'nip' => $p->nip,
                'nama_lengkap' => $p->nama_lengkap,
                'golongan_ruang' => $pangkat?->golongan?->label ?? '-',
                'golongan_ruang_level' => $pangkat?->golongan?->golongan_ruang ?? 0,
                'jabatan_terakhir' => $jabatan?->jabatan?->nama_jabatan ?? '-',
                'eselon_level' => $jabatan?->jabatan?->eselon_level ?? 99, // Lower is better (1 > 2 > 99)
                'jenis_jabatan_weight' => $jabatan?->jabatan?->jenis_jabatan?->value ?? 0,
                'masa_kerja' => "{$years} Tahun {$months} Bulan",
                'masa_kerja_total_bulan' => $totalMonths,
                'pendidikan_terakhir' => $pendidikan?->masterPendidikan?->nama ?? '-',
                'pendidikan_bobot' => $pendidikan?->masterPendidikan?->bobot ?? 0,
                'latihan_tahun_terakhir' => $latihan?->tahun_pelaksanaan ?? 0,
                'latihan_total_jam' => $p->riwayatLatihanJabatan->sum('jumlah_jam') ?? 0,
                'tanggal_lahir_raw' => $p->tanggal_lahir,
                'tanggal_lahir' => $p->tanggal_lahir->format('d/m/Y'),
                'usia' => $usia,
            ];
        }

        // DUK sorting hierarchy per BKN:
        // 1. Pangkat / Golongan (Desc)
        // 2. Jabatan (Eselon Asc [1 > 2 > 3 > 99=Non-Eselon], Jenis Jabatan Weight Desc)
        // 3. MKG Total Bulan (Desc)
        // 4. Latihan Total Jam (Desc)
        // 5. Pendidikan Bobot (Desc)
        // 6. Usia (Asc / Oldest date first)
        usort($entries, function ($a, $b) {
            // 1. Golongan (Highest wins)
            if ($a['golongan_ruang_level'] !== $b['golongan_ruang_level']) {
                return $b['golongan_ruang_level'] <=> $a['golongan_ruang_level'];
            }
            
            // 2a. Jabatan Eselon (Lowest wins, 1 is better than 2. 99 is non-eselon)
            if ($a['eselon_level'] !== $b['eselon_level']) {
                return $a['eselon_level'] <=> $b['eselon_level'];
            }
            // 2b. Jenis Jabatan (Highest wins if Eselon is same)
            if ($a['jenis_jabatan_weight'] !== $b['jenis_jabatan_weight']) {
                return $b['jenis_jabatan_weight'] <=> $a['jenis_jabatan_weight'];
            }

            // 3. MKG (Highest wins)
            if ($a['masa_kerja_total_bulan'] !== $b['masa_kerja_total_bulan']) {
                return $b['masa_kerja_total_bulan'] <=> $a['masa_kerja_total_bulan'];
            }

            // 4. Latihan Jabatan (Highest wins)
            if ($a['latihan_total_jam'] !== $b['latihan_total_jam']) {
                return $b['latihan_total_jam'] <=> $a['latihan_total_jam'];
            }

            // 5. Tingkat Pendidikan (Highest bobot wins)
            if ($a['pendidikan_bobot'] !== $b['pendidikan_bobot']) {
                return $b['pendidikan_bobot'] <=> $a['pendidikan_bobot'];
            }

            // 6. Usia (Oldest wins = Smallest date)
            return $a['tanggal_lahir_raw'] <=> $b['tanggal_lahir_raw'];
        });

        foreach ($entries as $i => &$entry) {
            $entry['ranking'] = $i + 1;
        }

        return $entries;
    }

    private function getPendidikanLevel(?string $tingkat): int
    {
        if (!$tingkat) return 0;
        return match (strtoupper($tingkat)) {
            'S3' => 7,
            'S2' => 6,
            'S1', 'D4' => 5,
            'D3' => 4,
            'D2' => 3,
            'D1' => 2,
            'SMA', 'SMK', 'SMU' => 1,
            default => 0,
        };
    }
}
