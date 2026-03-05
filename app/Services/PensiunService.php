<?php

namespace App\Services;

use App\Models\Pegawai;

class PensiunService
{
    public function getPensiunAlerts(): array
    {
        $pegawaiList = Pegawai::with(['riwayatJabatan.jabatan'])
            ->where('is_active', true)->get();
        $alerts = [];
        $today = today();

        foreach ($pegawaiList as $pegawai) {
            $jabatanTerakhir = $pegawai->riwayatJabatan->sortByDesc('tmt_jabatan')->first();
            if (!$jabatanTerakhir?->jabatan) continue;

            $bup = $jabatanTerakhir->jabatan->bup;
            $tanggalPensiun = $pegawai->tanggal_lahir->copy()->addYears($bup);
            $bulanMenuju = (($tanggalPensiun->year - $today->year) * 12) + $tanggalPensiun->month - $today->month;

            $alertLevel = null;
            if ($bulanMenuju <= 0) $alertLevel = 'Hitam';
            elseif ($bulanMenuju <= 6) $alertLevel = 'Merah';
            elseif ($bulanMenuju <= 12) $alertLevel = 'Kuning';
            elseif ($bulanMenuju <= 24) $alertLevel = 'Hijau';

            if ($alertLevel === null) continue;

            $alerts[] = [
                'pegawai_id' => $pegawai->id,
                'nip' => $pegawai->nip,
                'nama_lengkap' => $pegawai->nama_lengkap,
                'jabatan_terakhir' => $jabatanTerakhir->jabatan->nama_jabatan,
                'jenis_jabatan' => $jabatanTerakhir->jabatan->jenis_jabatan->label(),
                'bup' => $bup,
                'tanggal_pensiun' => $tanggalPensiun,
                'bulan_menuju_pensiun' => $bulanMenuju,
                'alert_level' => $alertLevel,
            ];
        }

        usort($alerts, fn($a, $b) => $a['bulan_menuju_pensiun'] <=> $b['bulan_menuju_pensiun']);
        return $alerts;
    }
}
