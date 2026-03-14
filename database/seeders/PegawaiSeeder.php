<?php

namespace Database\Seeders;

use App\Enums\JenisSanksi;
use App\Enums\TingkatHukuman;
use App\Models\AgamaMaster;
use App\Models\Bagian;
use App\Models\GolonganDarahMaster;
use App\Models\GolonganPangkat;
use App\Models\Jabatan;
use App\Models\JenisKelaminMaster;
use App\Models\Pegawai;
use App\Models\PenilaianKinerja;
use App\Models\RiwayatHukumanDisiplin;
use App\Models\RiwayatJabatan;
use App\Models\RiwayatKgb;
use App\Models\RiwayatLatihanJabatan;
use App\Models\RiwayatPangkat;
use App\Models\RiwayatPendidikan;
use App\Models\StatusKepegawaian;
use App\Models\StatusPernikahanMaster;
use App\Models\TipePegawai;
use App\Models\UnitKerja;
use App\Services\SalaryCalculatorService;
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
            $this->addKgbTimeline($peg, $today, mt_rand(6, 19));
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
            $this->addKgbTimeline($peg, $today, mt_rand(6, 19));
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
            $this->addKgbTimeline($peg, $today, mt_rand(6, 19));
        }

        // GROUP 2: KGB due soon (20)
        for ($i = 0; $i < 20; $i++) {
            $birthDate = $today->copy()->subYears(mt_rand(30, 49))->subDays(mt_rand(0, 180));
            $tmtCpns = $birthDate->copy()->addYears(22 + mt_rand(0, 4));
            $jabatan = $jabatanList->random();
            $peg = $this->createPegawai($birthDate, $tmtCpns);
            $this->addRiwayatJabatan($peg, $jabatan, $tmtCpns);
            $this->addRiwayatPangkatProgression($peg, $tmtCpns, $today);
            $this->addKgbTimeline($peg, $today, mt_rand(22, 23));
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
            $this->addKgbTimeline($peg, $today, mt_rand(3, 19));
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
            $this->addKgbTimeline($peg, $today, mt_rand(3, 19));

            $tingkat = $i < 5 ? TingkatHukuman::Sedang : TingkatHukuman::Berat;
            $sanksi = $tingkat === TingkatHukuman::Sedang
                ? JenisSanksi::PenundaanPangkat
                : JenisSanksi::PembebasanJabatan;
            RiwayatHukumanDisiplin::create([
                'pegawai_id' => $peg->id,
                'tingkat_hukuman' => $tingkat,
                'jenis_sanksi' => $sanksi,
                'durasi_tahun' => 1,
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
            $this->addKgbTimeline($peg, $today, mt_rand(1, 21));

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
        $genderId = mt_rand(0, 1) === 0
            ? JenisKelaminMaster::where('nama', 'Laki-laki')->value('id')
            : JenisKelaminMaster::where('nama', 'Perempuan')->value('id');
        $genderName = JenisKelaminMaster::find($genderId)?->nama;
        $fakerGender = $genderName === 'Laki-laki' ? 'male' : 'female';

        $nipBirth = $birthDate->format('Ymd');
        $nipTmt = $tmtCpns->format('Ym');
        $nipGender = $genderName === 'Laki-laki' ? '1' : '2';
        $nipSeq = str_pad($this->nipCounter, 3, '0', STR_PAD_LEFT);
        $nip = "{$nipBirth}{$nipTmt}{$nipGender}{$nipSeq}";
        $this->nipCounter++;

        $namaLengkap = $this->faker->name($fakerGender);
        $emailName = strtolower(str_replace([' ', '.', ',', "'"], '', $namaLengkap)) . $this->nipCounter;

        $gelarDepanOptions = [null, null, null, 'Dr.', 'Drs.', 'Ir.', 'Prof.'];
        $gelarBelakangOptions = [null, null, null, 'S.H.', 'S.E.', 'M.H.', 'M.Sc.', 'S.Kom.'];

        $agamaIds = AgamaMaster::pluck('id')->toArray();
        $statusPernikahanIds = StatusPernikahanMaster::pluck('id')->toArray();
        $golonganDarahIds = GolonganDarahMaster::pluck('id')->toArray();
        $bagianIds = Bagian::pluck('id')->toArray();

        return Pegawai::create([
            'nip' => $nip,
            'gelar_depan' => $gelarDepanOptions[mt_rand(0, count($gelarDepanOptions) - 1)],
            'nama_lengkap' => $namaLengkap,
            'gelar_belakang' => $gelarBelakangOptions[mt_rand(0, count($gelarBelakangOptions) - 1)],
            'tempat_lahir' => $this->faker->city(),
            'tanggal_lahir' => $birthDate,
            'jenis_kelamin_id' => $genderId,
            'alamat' => $this->faker->address(),
            'no_telepon' => '08' . $this->faker->numerify('##########'),
            'email' => $emailName . '@kemenipas.go.id',
            'tmt_cpns' => $tmtCpns,
            'tmt_pns' => $tmtCpns->copy()->addYear(),
            'gaji_pokok' => 0,
            'is_active' => true,
            'agama_id' => $agamaIds[mt_rand(0, count($agamaIds) - 1)],
            'status_pernikahan_id' => $statusPernikahanIds[mt_rand(0, count($statusPernikahanIds) - 1)],
            'golongan_darah_id' => $golonganDarahIds[mt_rand(0, count($golonganDarahIds) - 1)],
            'npwp' => mt_rand(10, 99) . '.' . mt_rand(100, 999) . '.' . mt_rand(100, 999) . '.' . mt_rand(1, 9) . '-' . mt_rand(100, 999) . '.000',
            'no_karpeg' => 'K-' . mt_rand(100000, 999999),
            'no_taspen' => 'T-' . mt_rand(1000000, 9999999),
            'bagian_id' => $bagianIds[mt_rand(0, count($bagianIds) - 1)],
            'unit_kerja_id' => UnitKerja::where('nama', 'Kanim Jakut')->value('id'),
            'tipe_pegawai_id' => TipePegawai::where('nama', 'PNS')->value('id'),
            'status_kepegawaian_id' => StatusKepegawaian::where('nama', 'Aktif')->value('id'),
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
        $golonganMap = GolonganPangkat::orderBy('golongan_ruang')->get()->keyBy('golongan_ruang');
        $maxGolonganRuang = $golonganMap->keys()->max();

        // Start golongan_ruang level based on years of service
        $startLevel = $years > 25 ? 5 : ($years > 15 ? 6 : 7); // II/a=5, II/b=6, II/c=7

        $currentLevel = $startLevel;
        $currentTmt = $tmtCpns->copy();
        $counter = 1;

        while ($currentTmt->lt($today) && $currentLevel <= $maxGolonganRuang) {
            $golongan = $golonganMap->get($currentLevel);
            if (!$golongan) break;

            RiwayatPangkat::create([
                'pegawai_id' => $peg->id,
                'golongan_id' => $golongan->id,
                'nomor_sk' => 'SK-PGK/' . $currentTmt->year . '/' . str_pad($counter, 3, '0', STR_PAD_LEFT),
                'tmt_pangkat' => $currentTmt->copy(),
                'tanggal_sk' => $currentTmt->copy()->subDays(30),
            ]);

            $currentTmt->addYears(4);
            $currentLevel++;
            $counter++;
        }
    }

    /**
     * Build a realistic KGB timeline from earliest pangkat TMT to today,
     * with the last KGB occurring `monthsAgoLastKgb` months before today.
     * Observers fire on each RiwayatKgb::create → gaji_pokok auto-synced.
     */
    private function addKgbTimeline(Pegawai $peg, $today, int $monthsAgoLastKgb): void
    {
        $latestPangkat = $peg->riwayatPangkat()->orderByDesc('tmt_pangkat')->first();
        if (!$latestPangkat) return;

        $golonganId = $latestPangkat->golongan_id;
        $salaryService = app(SalaryCalculatorService::class);

        // Build KGB every 2 years from tmt_cpns, but stop so the last one
        // lands at approximately `monthsAgoLastKgb` months before today
        $lastKgbDate = $today->copy()->subMonths($monthsAgoLastKgb);
        $tmtKgb = $peg->tmt_cpns->copy()->addYears(2);
        $mkgTahun = 2;
        $counter = 1;

        while ($tmtKgb->lte($lastKgbDate)) {
            $gajiBaru = $salaryService->calculateGaji($golonganId, $mkgTahun);
            $gajiLama = $salaryService->calculateGaji($golonganId, max(0, $mkgTahun - 2));

            if (!$gajiBaru) break;

            RiwayatKgb::create([
                'pegawai_id' => $peg->id,
                'nomor_sk' => 'SK-KGB/' . $tmtKgb->year . '/' . str_pad($counter, 3, '0', STR_PAD_LEFT),
                'tmt_kgb' => $tmtKgb->copy(),
                'gaji_lama' => $gajiLama ?? $gajiBaru,
                'gaji_baru' => $gajiBaru,
                'masa_kerja_golongan_tahun' => $mkgTahun,
                'masa_kerja_golongan_bulan' => 0,
            ]);

            $tmtKgb->addYears(2);
            $mkgTahun += 2;
            $counter++;
        }
        // gaji_pokok sync handled by RiwayatKgbObserver on each create
    }

    private function addRiwayatPendidikan(Pegawai $peg): void
    {
        $pendidikanIds = \App\Models\MasterPendidikan::pluck('id')->toArray();
        if (empty($pendidikanIds)) return;

        $jurusan = [
            'Ilmu Hukum', 'Hukum Pidana', 'Kriminologi', 'Ilmu Pemerintahan',
            'Administrasi Publik', 'Manajemen', 'Akuntansi', 'Teknik Informatika',
            'Hukum Internasional', 'Ilmu Sosial', 'Psikologi', 'Kesejahteraan Sosial',
        ];
        $pid = $pendidikanIds[mt_rand(0, count($pendidikanIds) - 1)];
        $tahunLulus = now()->year - mt_rand(5, 14);

        RiwayatPendidikan::create([
            'pegawai_id' => $peg->id,
            'pendidikan_id' => $pid,
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
