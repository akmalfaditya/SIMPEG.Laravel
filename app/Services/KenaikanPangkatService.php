<?php

namespace App\Services;

use App\Enums\GolonganRuang;
use App\Models\Pegawai;

class KenaikanPangkatService
{
    public function getEligiblePegawai(): array
    {
        $pegawaiList = Pegawai::with([
            'riwayatPangkat', 'penilaianKinerja',
            'riwayatLatihanJabatan', 'riwayatHukumanDisiplin',
        ])->where('is_active', true)->get();

        $candidates = [];
        $today = today();

        foreach ($pegawaiList as $pegawai) {
            $pangkatTerakhir = $pegawai->riwayatPangkat->sortByDesc('tmt_pangkat')->first();
            if (!$pangkatTerakhir) continue;

            $golSaatIni = $pangkatTerakhir->golongan_ruang;
            $golLevel = $golSaatIni->value;

            if ($golLevel >= GolonganRuang::IV_e->value) continue;

            $golBerikutnya = GolonganRuang::from($golLevel + 1);

            $masaKerjaGolBulan = (($today->year - $pangkatTerakhir->tmt_pangkat->year) * 12)
                + $today->month - $pangkatTerakhir->tmt_pangkat->month;

            $syaratMasaKerja = $masaKerjaGolBulan >= 48;

            $skp2Tahun = $pegawai->penilaianKinerja
                ->where('tahun', '>=', $today->year - 2);
            $syaratSKP = $skp2Tahun->count() >= 2 &&
                $skp2Tahun->every(fn($s) => in_array($s->nilai_skp, ['Sangat Baik', 'Baik']));

            $syaratLatihan = $pegawai->riwayatLatihanJabatan->isNotEmpty();

            $activeHukuman = $pegawai->riwayatHukumanDisiplin
                ->filter(fn($h) => !$h->tmt_selesai_hukuman || $h->tmt_selesai_hukuman->gt($today));
            $syaratHukuman = $activeHukuman->isEmpty();

            $isEligible = $syaratMasaKerja && $syaratSKP && $syaratLatihan && $syaratHukuman;

            $alasan = [];
            if (!$syaratMasaKerja) $alasan[] = 'Masa kerja golongan kurang (' . intdiv($masaKerjaGolBulan, 12) . ' tahun ' . ($masaKerjaGolBulan % 12) . ' bulan, min 4 tahun)';
            if (!$syaratSKP) $alasan[] = 'SKP 2 tahun terakhir belum memenuhi syarat (min Baik)';
            if (!$syaratLatihan) $alasan[] = 'Belum memiliki riwayat latihan jabatan';
            if (!$syaratHukuman) $alasan[] = 'Sedang menjalani hukuman disiplin';

            $candidates[] = [
                'pegawai_id' => $pegawai->id,
                'nip' => $pegawai->nip,
                'nama_lengkap' => $pegawai->nama_lengkap,
                'golongan_saat_ini' => $golSaatIni->label(),
                'golongan_level' => $golLevel,
                'golongan_berikutnya' => $golBerikutnya->label(),
                'tmt_pangkat_terakhir' => $pangkatTerakhir->tmt_pangkat,
                'masa_kerja_golongan_bulan' => $masaKerjaGolBulan,
                'masa_kerja_golongan' => intdiv($masaKerjaGolBulan, 12) . ' Tahun ' . ($masaKerjaGolBulan % 12) . ' Bulan',
                'syarat_masa_kerja' => $syaratMasaKerja,
                'syarat_skp' => $syaratSKP,
                'syarat_latihan' => $syaratLatihan,
                'syarat_hukuman' => $syaratHukuman,
                'is_eligible' => $isEligible,
                'alasan_tidak_eligible' => !empty($alasan) ? implode('; ', $alasan) : null,
            ];
        }

        usort($candidates, function ($a, $b) {
            if ($a['is_eligible'] !== $b['is_eligible']) return $b['is_eligible'] <=> $a['is_eligible'];
            if ($a['golongan_level'] !== $b['golongan_level']) return $b['golongan_level'] <=> $a['golongan_level'];
            return $b['masa_kerja_golongan_bulan'] <=> $a['masa_kerja_golongan_bulan'];
        });

        return $candidates;
    }
}
