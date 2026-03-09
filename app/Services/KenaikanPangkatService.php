<?php

namespace App\Services;

use App\Enums\GolonganRuang;
use App\Enums\JenisSanksi;
use App\Models\Pegawai;
use Carbon\Carbon;

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
            $tmtPangkat = $pangkatTerakhir->tmt_pangkat;

            // --- Hukdis Analysis ---
            $activeHukuman = $pegawai->riwayatHukumanDisiplin
                ->filter(fn($h) => !$h->tmt_selesai_hukuman || $h->tmt_selesai_hukuman->gt($today));

            $hukdisPangkatFlag = false;
            $hukdisPangkatNote = null;
            $penundaanBulan = 0;
            $isBlocked = false;
            $blockedNote = null;

            // GAP-11: Penurunan Pangkat — lower golongan, reset TMT
            $penurunanPangkat = $activeHukuman->filter(fn($h) => $h->jenis_sanksi === JenisSanksi::PenurunanPangkat);
            if ($penurunanPangkat->isNotEmpty()) {
                $hukdisPangkatFlag = true;
                if ($golLevel > 1) {
                    $golSaatIni = GolonganRuang::from($golLevel - 1);
                    $golLevel = $golSaatIni->value;
                }
                $latestPenurunan = $penurunanPangkat->sortByDesc('tmt_hukuman')->first();
                $tmtPangkat = $latestPenurunan->tmt_hukuman;
                $hukdisPangkatNote = "Penurunan Pangkat — efektif " . $golSaatIni->label() . ", hitung ulang dari " . $tmtPangkat->format('d/m/Y');
            }

            // GAP-10: Penundaan Pangkat — shift masa kerja requirement
            $penundaanPangkat = $activeHukuman->filter(fn($h) => $h->jenis_sanksi === JenisSanksi::PenundaanPangkat);
            if ($penundaanPangkat->isNotEmpty()) {
                $hukdisPangkatFlag = true;
                $totalDurasi = $penundaanPangkat->sum(fn($h) => $h->durasi_tahun ?? 1);
                $penundaanBulan = $totalDurasi * 12;
                $noteText = "Penundaan Pangkat {$totalDurasi} Tahun";
                $hukdisPangkatNote = $hukdisPangkatNote
                    ? $hukdisPangkatNote . '; ' . $noteText
                    : $noteText;
            }

            // Blocking sanctions (PembebasanJabatan, Pemberhentian)
            $blockingSanctions = $activeHukuman->filter(fn($h) =>
                in_array($h->jenis_sanksi, [JenisSanksi::PembebasanJabatan, JenisSanksi::Pemberhentian]));
            if ($blockingSanctions->isNotEmpty()) {
                $isBlocked = true;
                $blockedNote = $blockingSanctions->map(fn($h) => $h->jenis_sanksi->label())->implode(', ');
            }

            if ($golLevel >= GolonganRuang::IV_e->value) continue;

            $golBerikutnya = GolonganRuang::from($golLevel + 1);

            // Masa kerja with penundaan shift
            $masaKerjaGolBulan = (($today->year - $tmtPangkat->year) * 12)
                + $today->month - $tmtPangkat->month;

            $requiredBulan = 48 + $penundaanBulan;
            $syaratMasaKerja = $masaKerjaGolBulan >= $requiredBulan;

            $skp2Tahun = $pegawai->penilaianKinerja
                ->where('tahun', '>=', $today->year - 2);
            $syaratSKP = $skp2Tahun->count() >= 2 &&
                $skp2Tahun->every(fn($s) => in_array($s->nilai_skp, ['Sangat Baik', 'Baik']));

            $syaratLatihan = $pegawai->riwayatLatihanJabatan->isNotEmpty();

            $syaratHukuman = !$isBlocked;

            $isEligible = $syaratMasaKerja && $syaratSKP && $syaratLatihan && $syaratHukuman;

            $alasan = [];
            if (!$syaratMasaKerja) {
                $alasan[] = 'Masa kerja golongan kurang (' . intdiv($masaKerjaGolBulan, 12) . ' tahun ' . ($masaKerjaGolBulan % 12) . ' bulan, min ' . intdiv($requiredBulan, 12) . ' tahun)';
            }
            if (!$syaratSKP) $alasan[] = 'SKP 2 tahun terakhir belum memenuhi syarat (min Baik)';
            if (!$syaratLatihan) $alasan[] = 'Belum memiliki riwayat latihan jabatan';
            if ($isBlocked) $alasan[] = 'Terkena sanksi: ' . $blockedNote;

            // GAP-12: Proyeksi Periode April/Oktober
            $tanggalEligible = $tmtPangkat->copy()->addMonths($requiredBulan);
            $proyeksiPeriode = $this->hitungProyeksiPeriode($tanggalEligible);

            $candidates[] = [
                'pegawai_id' => $pegawai->id,
                'nip' => $pegawai->nip,
                'nama_lengkap' => $pegawai->nama_lengkap,
                'golongan_saat_ini' => $golSaatIni->label(),
                'golongan_level' => $golLevel,
                'golongan_berikutnya' => $golBerikutnya->label(),
                'tmt_pangkat_terakhir' => $pangkatTerakhir->tmt_pangkat,
                'tmt_pangkat_efektif' => $tmtPangkat,
                'masa_kerja_golongan_bulan' => $masaKerjaGolBulan,
                'masa_kerja_golongan' => intdiv($masaKerjaGolBulan, 12) . ' Tahun ' . ($masaKerjaGolBulan % 12) . ' Bulan',
                'syarat_masa_kerja' => $syaratMasaKerja,
                'syarat_skp' => $syaratSKP,
                'syarat_latihan' => $syaratLatihan,
                'syarat_hukuman' => $syaratHukuman,
                'is_eligible' => $isEligible,
                'alasan_tidak_eligible' => !empty($alasan) ? implode('; ', $alasan) : null,
                'hukdis_pangkat_flag' => $hukdisPangkatFlag,
                'hukdis_pangkat_note' => $hukdisPangkatNote,
                'proyeksi_periode' => $proyeksiPeriode,
            ];
        }

        usort($candidates, function ($a, $b) {
            if ($a['is_eligible'] !== $b['is_eligible']) return $b['is_eligible'] <=> $a['is_eligible'];
            if ($a['golongan_level'] !== $b['golongan_level']) return $b['golongan_level'] <=> $a['golongan_level'];
            return $b['masa_kerja_golongan_bulan'] <=> $a['masa_kerja_golongan_bulan'];
        });

        return $candidates;
    }

    public function getDitundaPegawai(): array
    {
        $all = $this->getEligiblePegawai();
        return array_values(array_filter($all, fn($c) => $c['hukdis_pangkat_flag']));
    }

    private function hitungProyeksiPeriode(Carbon $tanggalEligible): string
    {
        $year = $tanggalEligible->year;
        $month = $tanggalEligible->month;
        $day = $tanggalEligible->day;

        if ($month < 4 || ($month == 4 && $day <= 1)) {
            return "April {$year}";
        }
        if ($month < 10 || ($month == 10 && $day <= 1)) {
            return "Oktober {$year}";
        }
        return "April " . ($year + 1);
    }
}
