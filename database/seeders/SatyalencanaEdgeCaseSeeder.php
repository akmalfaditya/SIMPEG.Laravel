<?php

namespace Database\Seeders;

use App\Enums\JenisSanksi;
use App\Enums\StatusHukdis;
use App\Enums\TingkatHukuman;
use App\Models\AgamaMaster;
use App\Models\Bagian;
use App\Models\GolonganDarahMaster;
use App\Models\GolonganPangkat;
use App\Models\Jabatan;
use App\Models\JenisKelaminMaster;
use App\Models\Pegawai;
use App\Models\RiwayatHukumanDisiplin;
use App\Models\RiwayatJabatan;
use App\Models\RiwayatPangkat;
use App\Models\StatusKepegawaian;
use App\Models\StatusPernikahanMaster;
use App\Models\TipePegawai;
use App\Models\UnitKerja;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * Satyalencana "Reset Argo" Edge Case Seeder
 *
 * Generates 3 specific PNS test employees to verify the Reset Argo logic:
 *
 * Case 1 (Clean Record):
 *   tmt_cpns = 11 years ago, no hukdis.
 *   Expected: Eligible 10-year Satyalencana (masa_kerja_murni = 11).
 *
 * Case 2 (Hukdis Ringan — no reset):
 *   tmt_cpns = 12 years ago, 1 Ringan hukdis completed 2 years ago.
 *   Expected: Eligible 10-year Satyalencana (masa_kerja_murni = 12, Ringan does NOT reset).
 *
 * Case 3 (Hukdis Sedang — argo reset):
 *   tmt_cpns = 15 years ago, 1 Sedang hukdis with tmt_selesai_hukuman = 4 years ago.
 *   Expected: NOT eligible (masa_kerja_murni = 4, reset from tmt_selesai_hukuman).
 */
class SatyalencanaEdgeCaseSeeder extends Seeder
{
    public function run(): void
    {
        $today = today();
        $pnsId = TipePegawai::where('nama', 'PNS')->value('id');
        $statusId = StatusKepegawaian::where('nama', 'Aktif')->value('id');
        $golongan = GolonganPangkat::where('is_active', true)->first();
        $jabatan = Jabatan::where('is_active', true)
            ->whereHas('rumpunJabatan', fn($q) => $q->where('nama', '!=', 'PPPK'))
            ->first();

        if (!$golongan || !$jabatan || !$pnsId) return;

        // ── Case 1: Clean Record (11 years, no hukdis) ──────────────
        $tmtCpns1 = $today->copy()->subYears(11)->subDays(30);
        $peg1 = $this->createEdgePegawai(
            'EDGE-SATYA Case 1 (Clean)',
            $tmtCpns1,
            $pnsId,
            $statusId,
        );
        $this->seedInitialPangkatAndJabatan($peg1, $golongan, $jabatan);

        // ── Case 2: Hukdis Ringan (12 years, Ringan does NOT reset) ─
        $tmtCpns2 = $today->copy()->subYears(12)->subDays(30);
        $peg2 = $this->createEdgePegawai(
            'EDGE-SATYA Case 2 (Ringan)',
            $tmtCpns2,
            $pnsId,
            $statusId,
        );
        $this->seedInitialPangkatAndJabatan($peg2, $golongan, $jabatan);

        $tmtHukuman2 = $today->copy()->subYears(3);
        RiwayatHukumanDisiplin::create([
            'pegawai_id' => $peg2->id,
            'tingkat_hukuman' => TingkatHukuman::Ringan,
            'jenis_sanksi' => JenisSanksi::PenundaanKgb,
            'durasi_tahun' => 1,
            'nomor_sk' => 'SK-EDGE-RINGAN/001',
            'tanggal_sk' => $tmtHukuman2->copy()->subDays(14),
            'tmt_hukuman' => $tmtHukuman2,
            'tmt_selesai_hukuman' => $tmtHukuman2->copy()->addYear(),
            'status' => StatusHukdis::Selesai,
            'deskripsi' => 'Edge case: Hukdis Ringan — tidak mereset argo Satyalencana',
        ]);

        // ── Case 3: Hukdis Sedang (15 years, RESET to 4 years ago) ──
        $tmtCpns3 = $today->copy()->subYears(15)->subDays(30);
        $peg3 = $this->createEdgePegawai(
            'EDGE-SATYA Case 3 (Sedang Reset)',
            $tmtCpns3,
            $pnsId,
            $statusId,
        );
        $this->seedInitialPangkatAndJabatan($peg3, $golongan, $jabatan);

        $tmtHukuman3 = $today->copy()->subYears(5);
        $tmtSelesai3 = $today->copy()->subYears(4); // Exactly 4 years ago
        RiwayatHukumanDisiplin::create([
            'pegawai_id' => $peg3->id,
            'tingkat_hukuman' => TingkatHukuman::Sedang,
            'jenis_sanksi' => JenisSanksi::PenundaanPangkat,
            'durasi_tahun' => 1,
            'nomor_sk' => 'SK-EDGE-SEDANG/001',
            'tanggal_sk' => $tmtHukuman3->copy()->subDays(14),
            'tmt_hukuman' => $tmtHukuman3,
            'tmt_selesai_hukuman' => $tmtSelesai3,
            'status' => StatusHukdis::Selesai,
            'deskripsi' => 'Edge case: Hukdis Sedang — mereset argo ke tmt_selesai_hukuman',
        ]);
    }

    private function createEdgePegawai(string $label, Carbon $tmtCpns, int $pnsId, int $statusId): Pegawai
    {
        $birthDate = $tmtCpns->copy()->subYears(25);
        $nip = 'E' . $birthDate->format('Ymd') . $tmtCpns->format('Ym') . '1' . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);

        return Pegawai::create([
            'nip' => $nip,
            'nama_lengkap' => $label,
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => $birthDate,
            'jenis_kelamin_id' => JenisKelaminMaster::where('nama', 'Laki-laki')->value('id') ?? 1,
            'alamat' => 'Jl. Edge Case No. 1',
            'no_telepon' => '081200000' . mt_rand(100, 999),
            'email' => strtolower(str_replace([' ', '(', ')'], '', $label)) . '@edge.test',
            'tmt_cpns' => $tmtCpns,
            'tmt_pns' => $tmtCpns->copy()->addYear(),
            'gaji_pokok' => 0,
            'is_active' => true,
            'agama_id' => AgamaMaster::first()?->id ?? 1,
            'status_pernikahan_id' => StatusPernikahanMaster::first()?->id ?? 1,
            'golongan_darah_id' => GolonganDarahMaster::first()?->id ?? 1,
            'bagian_id' => Bagian::first()?->id,
            'unit_kerja_id' => UnitKerja::first()?->id,
            'tipe_pegawai_id' => $pnsId,
            'status_kepegawaian_id' => $statusId,
        ]);
    }

    private function seedInitialPangkatAndJabatan(Pegawai $peg, GolonganPangkat $golongan, Jabatan $jabatan): void
    {
        RiwayatPangkat::create([
            'pegawai_id' => $peg->id,
            'golongan_id' => $golongan->id,
            'nomor_sk' => 'SK-EDGE/' . $peg->tmt_cpns->year . '/001',
            'tmt_pangkat' => $peg->tmt_cpns,
            'tanggal_sk' => $peg->tmt_cpns,
        ]);

        RiwayatJabatan::create([
            'pegawai_id' => $peg->id,
            'jabatan_id' => $jabatan->id,
            'nomor_sk' => 'SK-EDGE-JAB/' . $peg->tmt_cpns->year . '/001',
            'tmt_jabatan' => $peg->tmt_cpns->copy()->addYear(),
            'tanggal_sk' => $peg->tmt_cpns->copy()->addMonths(11),
        ]);
    }
}
