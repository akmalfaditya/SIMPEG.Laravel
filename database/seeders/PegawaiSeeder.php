<?php

namespace Database\Seeders;

use App\Enums\Agama;
use App\Enums\GolonganDarah;
use App\Enums\GolonganRuang;
use App\Enums\JenisKelamin;
use App\Enums\StatusPernikahan;
use App\Enums\JenisSanksi;
use App\Enums\TingkatHukuman;
use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\PenilaianKinerja;
use App\Models\RiwayatHukumanDisiplin;
use App\Models\RiwayatJabatan;
use App\Models\RiwayatKgb;
use App\Models\RiwayatLatihanJabatan;
use App\Models\RiwayatPangkat;
use App\Models\RiwayatPendidikan;
use App\Models\TabelGaji;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class PegawaiSeeder extends Seeder
{
    private $random;
    private $faker;
    private int $nipCounter = 1;

    public function run(): void
    {
        if (Pegawai::count() > 0) return;

        $this->random = mt_srand(42);
        $this->faker = Faker::create('id_ID');

        $jabatanList = Jabatan::all();
        if ($jabatanList->isEmpty()) return;

        $today = today();
        $pegawaiList = collect();

        // GROUP 1: Near retirement (15)
        $jabatanBup58 = $jabatanList->where('bup', 58)->values();
        for ($i = 0; $i < 5; $i++) {
            $monthsBefore = $i < 3 ? mt_rand(1, 5) : mt_rand(7, 11);
            $birthDate = $today->copy()->subYears(58)->addMonths($monthsBefore);
            $tmtCpns = $birthDate->copy()->addYears(22 + mt_rand(0, 3));
            $jabatan = $jabatanBup58[$i % $jabatanBup58->count()];
            $peg = $this->createPegawai($birthDate, $tmtCpns);
            $this->addRiwayatJabatan($peg, $jabatan, $tmtCpns);
            $this->addRiwayatPangkatProgression($peg, $tmtCpns, $today);
            $this->addRecentKGB($peg, $today, mt_rand(6, 19));
        }

        $jabatanBup60 = $jabatanList->where('bup', 60)->values();
        for ($i = 0; $i < 5; $i++) {
            $monthsBefore = $i < 3 ? mt_rand(1, 5) : mt_rand(7, 11);
            $birthDate = $today->copy()->subYears(60)->addMonths($monthsBefore);
            $tmtCpns = $birthDate->copy()->addYears(22 + mt_rand(0, 2));
            $jabatan = $jabatanBup60[$i % $jabatanBup60->count()];
            $peg = $this->createPegawai($birthDate, $tmtCpns);
            $this->addRiwayatJabatan($peg, $jabatan, $tmtCpns);
            $this->addRiwayatPangkatProgression($peg, $tmtCpns, $today);
            $this->addRecentKGB($peg, $today, mt_rand(6, 19));
        }

        $jabatanBup65 = $jabatanList->where('bup', 65)->values();
        for ($i = 0; $i < 5; $i++) {
            $monthsBefore = $i < 3 ? mt_rand(1, 5) : mt_rand(7, 11);
            $birthDate = $today->copy()->subYears(65)->addMonths($monthsBefore);
            $tmtCpns = $birthDate->copy()->addYears(22 + mt_rand(0, 2));
            $jabatan = $jabatanBup65[$i % $jabatanBup65->count()];
            $peg = $this->createPegawai($birthDate, $tmtCpns);
            $this->addRiwayatJabatan($peg, $jabatan, $tmtCpns);
            $this->addRiwayatPangkatProgression($peg, $tmtCpns, $today);
            $this->addRecentKGB($peg, $today, mt_rand(6, 19));
        }

        // GROUP 2: KGB due soon (20)
        for ($i = 0; $i < 20; $i++) {
            $birthDate = $today->copy()->subYears(mt_rand(30, 49))->subDays(mt_rand(0, 180));
            $tmtCpns = $birthDate->copy()->addYears(22 + mt_rand(0, 4));
            $jabatan = $jabatanList->random();
            $peg = $this->createPegawai($birthDate, $tmtCpns);
            $this->addRiwayatJabatan($peg, $jabatan, $tmtCpns);
            $this->addRiwayatPangkatProgression($peg, $tmtCpns, $today);
            $this->addRecentKGB($peg, $today, mt_rand(22, 23));
        }

        // GROUP 3: Satyalencana eligible (15)
        $milestones = [10,10,10,10,10, 20,20,20,20,20, 30,30,30,30,30];
        for ($i = 0; $i < 15; $i++) {
            $yearsOfService = $milestones[$i];
            $tmtCpns = $today->copy()->subYears($yearsOfService)->addDays(mt_rand(-30, 30));
            $birthDate = $tmtCpns->copy()->subYears(mt_rand(22, 27));
            $jabatan = $jabatanList->random();
            $peg = $this->createPegawai($birthDate, $tmtCpns);
            $this->addRiwayatJabatan($peg, $jabatan, $tmtCpns);
            $this->addRiwayatPangkatProgression($peg, $tmtCpns, $today);
            $this->addRecentKGB($peg, $today, mt_rand(3, 19));
        }

        // GROUP 4: With hukuman disiplin (10)
        for ($i = 0; $i < 10; $i++) {
            $yearsOfService = $i < 5 ? 10 : 20;
            $tmtCpns = $today->copy()->subYears($yearsOfService)->addDays(mt_rand(-60, 60));
            $birthDate = $tmtCpns->copy()->subYears(mt_rand(22, 27));
            $jabatan = $jabatanList->random();
            $peg = $this->createPegawai($birthDate, $tmtCpns);
            $this->addRiwayatJabatan($peg, $jabatan, $tmtCpns);
            $this->addRiwayatPangkatProgression($peg, $tmtCpns, $today);
            $this->addRecentKGB($peg, $today, mt_rand(3, 19));

            $tingkat = $i < 5 ? TingkatHukuman::Sedang : TingkatHukuman::Berat;
            $sanksi = $tingkat === TingkatHukuman::Sedang
                ? JenisSanksi::PenundaanPangkat
                : JenisSanksi::PembebasanJabatan;
            RiwayatHukumanDisiplin::create([
                'pegawai_id' => $peg->id,
                'tingkat_hukuman' => $tingkat,
                'jenis_sanksi' => $sanksi,
                'durasi_tahun' => $tingkat === TingkatHukuman::Sedang ? 1 : null,
                'nomor_sk' => 'SK-HD/' . ($today->year - mt_rand(1, 4)) . '/00' . ($i + 1),
                'tanggal_sk' => $today->copy()->subYears(mt_rand(1, 4)),
                'tmt_hukuman' => $today->copy()->subYears(mt_rand(1, 4)),
                'deskripsi' => $tingkat === TingkatHukuman::Sedang
                    ? 'Pelanggaran disiplin tingkat sedang'
                    : 'Pelanggaran disiplin tingkat berat',
            ]);
        }

        // GROUP 5: Regular (40)
        for ($i = 0; $i < 40; $i++) {
            $birthDate = $today->copy()->subYears(mt_rand(25, 54))->subDays(mt_rand(0, 180));
            $tmtCpns = $birthDate->copy()->addYears(mt_rand(22, 29));
            if ($tmtCpns->gt($today->copy()->subYear())) {
                $tmtCpns = $today->copy()->subYears(mt_rand(1, 14));
            }
            $jabatan = $jabatanList->random();
            $peg = $this->createPegawai($birthDate, $tmtCpns);
            $this->addRiwayatJabatan($peg, $jabatan, $tmtCpns);
            $this->addRiwayatPangkatProgression($peg, $tmtCpns, $today);
            $this->addRecentKGB($peg, $today, mt_rand(1, 21));

            if ($i % 8 === 0) {
                RiwayatHukumanDisiplin::create([
                    'pegawai_id' => $peg->id,
                    'tingkat_hukuman' => TingkatHukuman::Ringan,
                    'jenis_sanksi' => JenisSanksi::PenundaanKgb,
                    'durasi_tahun' => 1,
                    'nomor_sk' => 'SK-HD/' . ($today->year - 1) . '/R' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                    'tanggal_sk' => $today->copy()->subYear(),
                    'tmt_hukuman' => $today->copy()->subYear(),
                    'deskripsi' => 'Pelanggaran disiplin tingkat ringan - teguran',
                ]);
            }
        }

        // Add secondary data for all pegawai
        foreach (Pegawai::all() as $peg) {
            $this->addRiwayatPendidikan($peg);
            $this->addRiwayatLatihanJabatan($peg);
            $this->addPenilaianKinerja($peg, $today);
        }
    }

    private function createPegawai($birthDate, $tmtCpns): Pegawai
    {
        $gender = mt_rand(0, 1) === 0 ? JenisKelamin::LakiLaki : JenisKelamin::Perempuan;
        $nipBirth = $birthDate->format('Ymd');
        $nipTmt = $tmtCpns->format('Ym');
        $nipGender = $gender === JenisKelamin::LakiLaki ? '1' : '2';
        $nipSeq = str_pad($this->nipCounter, 3, '0', STR_PAD_LEFT);
        $nip = "{$nipBirth}{$nipTmt}{$nipGender}{$nipSeq}";
        $this->nipCounter++;

        $unitKerjaList = [
            // Sekretariat Jenderal
            'Sekretariat Jenderal',
            'Biro Kepegawaian dan Organisasi',
            'Biro Perencanaan dan Keuangan',
            'Biro Hukum dan Hubungan Masyarakat',
            'Biro Umum dan Pengadaan',
            // Inspektorat Jenderal
            'Inspektorat Jenderal',
            // Ditjen Imigrasi
            'Direktorat Jenderal Imigrasi',
            'Kantor Imigrasi Kelas I TPI Jakarta',
            'Kantor Imigrasi Kelas I TPI Surabaya',
            'Kantor Imigrasi Kelas II TPI Semarang',
            'Kantor Imigrasi Kelas II TPI Medan',
            'Kantor Imigrasi Kelas III Non-TPI Cirebon',
            'Rumah Detensi Imigrasi Jakarta',
            // Ditjen Pemasyarakatan
            'Direktorat Jenderal Pemasyarakatan',
            'Lembaga Pemasyarakatan Kelas I Cipinang',
            'Lembaga Pemasyarakatan Kelas II A Tangerang',
            'Lembaga Pemasyarakatan Kelas II B Bogor',
            'Rumah Tahanan Negara Kelas I Jakarta Pusat',
            'Rumah Tahanan Negara Kelas II B Bekasi',
            'Balai Pemasyarakatan Kelas I Jakarta Selatan',
            'Balai Pemasyarakatan Kelas II Bandung',
        ];

        $namaLengkap = $this->faker->name($gender === JenisKelamin::LakiLaki ? 'male' : 'female');
        $emailName = strtolower(str_replace([' ', '.', ',', "'"], '', $namaLengkap)) . $this->nipCounter;

        return Pegawai::create([
            'nip' => $nip,
            'nama_lengkap' => $namaLengkap,
            'tempat_lahir' => $this->faker->city(),
            'tanggal_lahir' => $birthDate,
            'jenis_kelamin' => $gender,
            'alamat' => $this->faker->address(),
            'no_telepon' => '08' . $this->faker->numerify('##########'),
            'email' => $emailName . '@kemenipas.go.id',
            'tmt_cpns' => $tmtCpns,
            'tmt_pns' => $tmtCpns->copy()->addYear(),
            'gaji_pokok' => 0,
            'is_active' => true,
            'agama' => Agama::cases()[mt_rand(0, 5)],
            'status_pernikahan' => StatusPernikahan::cases()[mt_rand(0, 3)],
            'golongan_darah' => GolonganDarah::cases()[mt_rand(0, 3)],
            'npwp' => mt_rand(10, 99) . '.' . mt_rand(100, 999) . '.' . mt_rand(100, 999) . '.' . mt_rand(1, 9) . '-' . mt_rand(100, 999) . '.000',
            'no_karpeg' => 'K-' . mt_rand(100000, 999999),
            'no_taspen' => 'T-' . mt_rand(1000000, 9999999),
            'unit_kerja' => $unitKerjaList[mt_rand(0, count($unitKerjaList) - 1)],
        ]);
    }

    private function addRiwayatJabatan(Pegawai $peg, Jabatan $jabatan, $tmtCpns): void
    {
        RiwayatJabatan::create([
            'pegawai_id' => $peg->id,
            'jabatan_id' => $jabatan->id,
            'nomor_sk' => 'SK-JAB/' . $tmtCpns->year . '/001',
            'tmt_jabatan' => $tmtCpns->copy()->addYear(),
            'tanggal_sk' => $tmtCpns->copy()->addMonths(11),
        ]);
    }

    private function addRiwayatPangkatProgression(Pegawai $peg, $tmtCpns, $today): void
    {
        $years = (int) $tmtCpns->diffInDays($today) / 365;
        $startGol = $years > 25 ? GolonganRuang::II_a : ($years > 15 ? GolonganRuang::II_b : GolonganRuang::II_c);

        $currentGol = $startGol;
        $currentTmt = $tmtCpns->copy();
        $counter = 1;

        while ($currentTmt->lt($today) && $currentGol->value <= GolonganRuang::IV_e->value) {
            RiwayatPangkat::create([
                'pegawai_id' => $peg->id,
                'golongan_ruang' => $currentGol,
                'nomor_sk' => 'SK-PGK/' . $currentTmt->year . '/' . str_pad($counter, 3, '0', STR_PAD_LEFT),
                'tmt_pangkat' => $currentTmt->copy(),
                'tanggal_sk' => $currentTmt->copy()->subDays(30),
            ]);

            $currentTmt->addYears(4);
            $nextVal = $currentGol->value + 1;
            if ($nextVal > GolonganRuang::IV_e->value) break;
            $currentGol = GolonganRuang::from($nextVal);
            $counter++;
        }
    }

    private function addRecentKGB(Pegawai $peg, $today, int $monthsAgo): void
    {
        $tmtKgb = $today->copy()->subMonths($monthsAgo);

        // Get latest pangkat for golongan lookup
        $latestPangkat = $peg->riwayatPangkat()->orderByDesc('tmt_pangkat')->first();
        if (!$latestPangkat) return;

        $golongan = $latestPangkat->golongan_ruang;
        $totalMonths = (($today->year - $peg->tmt_cpns->year) * 12) + $today->month - $peg->tmt_cpns->month;
        $mkgTahun = intdiv($totalMonths, 12);
        $mkgBulan = $totalMonths % 12;

        // Look up gaji_baru from TabelGaji (current MKG, capped to highest available)
        $gajiBaru = TabelGaji::where('golongan_ruang', $golongan->value)
            ->where('masa_kerja_tahun', '<=', $mkgTahun)
            ->orderByDesc('masa_kerja_tahun')
            ->value('gaji_pokok');

        // Look up gaji_lama (MKG - 2 years, the previous KGB period)
        $mkgLama = max(0, $mkgTahun - 2);
        $gajiLama = TabelGaji::where('golongan_ruang', $golongan->value)
            ->where('masa_kerja_tahun', '<=', $mkgLama)
            ->orderByDesc('masa_kerja_tahun')
            ->value('gaji_pokok');

        if (!$gajiBaru) return;

        RiwayatKgb::create([
            'pegawai_id' => $peg->id,
            'nomor_sk' => 'SK-KGB/' . $tmtKgb->year . '/001',
            'tmt_kgb' => $tmtKgb,
            'gaji_lama' => $gajiLama ?? $gajiBaru,
            'gaji_baru' => $gajiBaru,
            'masa_kerja_golongan_tahun' => $mkgTahun,
            'masa_kerja_golongan_bulan' => $mkgBulan,
        ]);

        // Sync pegawai gaji_pokok to latest KGB gaji_baru
        $peg->update(['gaji_pokok' => $gajiBaru]);
    }

    private function addRiwayatPendidikan(Pegawai $peg): void
    {
        $tingkat = ['S1', 'D3', 'SMA', 'S2'];
        $jurusan = [
            'Ilmu Hukum', 'Hukum Pidana', 'Kriminologi', 'Ilmu Pemerintahan',
            'Administrasi Publik', 'Manajemen', 'Akuntansi', 'Teknik Informatika',
            'Hukum Internasional', 'Ilmu Sosial', 'Psikologi', 'Kesejahteraan Sosial',
        ];
        $tp = $tingkat[mt_rand(0, 3)];
        $tahunLulus = now()->year - mt_rand(5, 14);

        RiwayatPendidikan::create([
            'pegawai_id' => $peg->id,
            'tingkat_pendidikan' => $tp,
            'institusi' => 'Universitas ' . $this->faker->city(),
            'jurusan' => $jurusan[mt_rand(0, count($jurusan) - 1)],
            'tahun_lulus' => $tahunLulus,
            'no_ijazah' => 'IJ-' . $tahunLulus . '/' . mt_rand(1000, 9999),
            'tanggal_ijazah' => now()->setYear($tahunLulus)->setMonth(mt_rand(6, 11))->setDay(mt_rand(1, 27)),
        ]);
    }

    private function addRiwayatLatihanJabatan(Pegawai $peg): void
    {
        $latihan = [
            'Diklat Kepemimpinan Tk. IV',
            'Diklat Kepemimpinan Tk. III',
            'Prajabatan Golongan III',
            'Diklat Teknis Keimigrasian',
            'Diklat Teknis Pemasyarakatan',
            'Bimbingan Teknis Penyidikan Imigrasi',
            'Pelatihan Pengelolaan Rumah Tahanan',
            'Pelatihan Pelayanan Paspor',
            'Diklat Pengawasan Orang Asing',
            'Pelatihan Pembinaan Warga Binaan',
            'Bimbingan Teknis Sistem Informasi Keimigrasian',
            'Diklat Manajemen Pemasyarakatan',
        ];
        $penyelenggara = [
            'BPSDM Hukum dan HAM',
            'Pusdiklat Kemenipas',
            'Politeknik Imigrasi',
            'Politeknik Pemasyarakatan',
            'BKN',
            'LAN',
        ];

        RiwayatLatihanJabatan::create([
            'pegawai_id' => $peg->id,
            'nama_latihan' => $latihan[mt_rand(0, count($latihan) - 1)],
            'tahun_pelaksanaan' => now()->year - mt_rand(1, 4),
            'jumlah_jam' => mt_rand(20, 119),
            'penyelenggara' => $penyelenggara[mt_rand(0, count($penyelenggara) - 1)],
            'tempat_pelaksanaan' => $this->faker->city(),
            'no_sertifikat' => 'SERT/' . (now()->year - mt_rand(1, 4)) . '/' . mt_rand(1000, 9999),
        ]);
    }

    private function addPenilaianKinerja(Pegawai $peg, $today): void
    {
        $nilaiOptions = ['Sangat Baik', 'Baik', 'Baik', 'Baik', 'Cukup'];
        $yearsToSeed = mt_rand(2, 3);

        for ($i = 0; $i < $yearsToSeed; $i++) {
            PenilaianKinerja::create([
                'pegawai_id' => $peg->id,
                'tahun' => $today->year - 1 - $i,
                'nilai_skp' => $nilaiOptions[mt_rand(0, 4)],
            ]);
        }
    }
}
