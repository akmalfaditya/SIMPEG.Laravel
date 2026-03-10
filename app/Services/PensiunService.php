<?php

namespace App\Services;

use App\Models\Pegawai;
use App\Models\StatusKepegawaian;
use Illuminate\Support\Facades\DB;

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

    /**
     * Get pre-filled data for the pensiun process form.
     */
    public function getProcessData(int $pegawaiId): ?array
    {
        $pegawai = Pegawai::with(['riwayatJabatan.jabatan', 'riwayatPangkat.golongan'])
            ->where('is_active', true)
            ->find($pegawaiId);

        if (!$pegawai) return null;

        $jabatanTerakhir = $pegawai->riwayatJabatan->sortByDesc('tmt_jabatan')->first();
        if (!$jabatanTerakhir?->jabatan) return null;

        $bup = $jabatanTerakhir->jabatan->bup;
        $tanggalPensiun = $pegawai->tanggal_lahir->copy()->addYears($bup);
        $bulanMenuju = (($tanggalPensiun->year - today()->year) * 12) + $tanggalPensiun->month - today()->month;

        $pangkatTerakhir = $pegawai->riwayatPangkat->sortByDesc('tmt_pangkat')->first();

        // Already processed?
        if ($pegawai->tmt_pensiun) {
            return [
                'blocked' => true,
                'blocked_reason' => 'Pegawai sudah diproses pensiun dengan SK No. ' . $pegawai->sk_pensiun_nomor,
            ];
        }

        return [
            'blocked' => false,
            'pegawai_id' => $pegawai->id,
            'nip' => $pegawai->nip,
            'nama_lengkap' => $pegawai->nama_lengkap,
            'pangkat_terakhir' => $pangkatTerakhir?->golongan?->label ?? '-',
            'jabatan_terakhir' => $jabatanTerakhir->jabatan->nama_jabatan,
            'bup' => $bup,
            'tanggal_pensiun' => $tanggalPensiun,
            'bulan_menuju_pensiun' => $bulanMenuju,
            'alert_level' => $bulanMenuju <= 0 ? 'Hitam' : ($bulanMenuju <= 6 ? 'Merah' : ($bulanMenuju <= 12 ? 'Kuning' : 'Hijau')),
            'gaji_pokok' => $pegawai->gaji_pokok,
            'masa_kerja' => $pegawai->masa_kerja,
        ];
    }

    /**
     * Process pensiun: update status_kepegawaian → Pensiun, deactivate, record SK.
     */
    public function processPensiun(array $validated): void
    {
        DB::transaction(function () use ($validated) {
            $pensiunStatusId = StatusKepegawaian::where('nama', 'Pensiun')->value('id');

            Pegawai::where('id', $validated['pegawai_id'])->update([
                'status_kepegawaian_id' => $pensiunStatusId,
                'is_active' => false,
                'sk_pensiun_nomor' => $validated['sk_pensiun_nomor'],
                'sk_pensiun_tanggal' => $validated['sk_pensiun_tanggal'],
                'tmt_pensiun' => $validated['tmt_pensiun'],
                'catatan_pensiun' => $validated['catatan_pensiun'] ?? null,
            ]);
        });
    }
}
