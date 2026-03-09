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
                'masa_kerja' => "{$years} Tahun {$months} Bulan",
                'masa_kerja_total_bulan' => $totalMonths,
                'pendidikan_terakhir' => $pendidikan?->tingkat_pendidikan ?? '-',
                'pendidikan_level' => $this->getPendidikanLevel($pendidikan?->tingkat_pendidikan),
                'latihan_tahun_terakhir' => $latihan?->tahun_pelaksanaan ?? 0,
                'latihan_total_jam' => $p->riwayatLatihanJabatan->sum('jumlah_jam'),
                'tanggal_lahir' => $p->tanggal_lahir,
                'usia' => $usia,
            ];
        }

        // DUK sorting hierarchy per BKN
        usort($entries, function ($a, $b) {
            if ($a['golongan_ruang_level'] !== $b['golongan_ruang_level'])
                return $b['golongan_ruang_level'] <=> $a['golongan_ruang_level'];
            if ($a['jabatan_terakhir'] !== $b['jabatan_terakhir'])
                return strcmp($a['jabatan_terakhir'], $b['jabatan_terakhir']);
            if ($a['masa_kerja_total_bulan'] !== $b['masa_kerja_total_bulan'])
                return $b['masa_kerja_total_bulan'] <=> $a['masa_kerja_total_bulan'];
            if ($a['latihan_tahun_terakhir'] !== $b['latihan_tahun_terakhir'])
                return $b['latihan_tahun_terakhir'] <=> $a['latihan_tahun_terakhir'];
            if ($a['latihan_total_jam'] !== $b['latihan_total_jam'])
                return $b['latihan_total_jam'] <=> $a['latihan_total_jam'];
            if ($a['pendidikan_level'] !== $b['pendidikan_level'])
                return $b['pendidikan_level'] <=> $a['pendidikan_level'];
            return strcmp($a['tanggal_lahir']->format('Y-m-d'), $b['tanggal_lahir']->format('Y-m-d'));
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
