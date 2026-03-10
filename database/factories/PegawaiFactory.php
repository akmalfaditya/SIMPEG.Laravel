<?php

namespace Database\Factories;

use App\Models\AgamaMaster;
use App\Models\Bagian;
use App\Models\GolonganDarahMaster;
use App\Models\GolonganPangkat;
use App\Models\Jabatan;
use App\Models\JenisKelaminMaster;
use App\Models\Pegawai;
use App\Models\StatusKepegawaian;
use App\Models\StatusPernikahanMaster;
use App\Models\TipePegawai;
use App\Models\UnitKerja;
use App\Services\SalaryCalculatorService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class PegawaiFactory extends Factory
{
    protected $model = Pegawai::class;

    public function definition(): array
    {
        $genderId = JenisKelaminMaster::inRandomOrder()->value('id') ?? 1;
        $genderName = JenisKelaminMaster::find($genderId)?->nama;
        $fakerGender = $genderName === 'Laki-laki' ? 'male' : 'female';
        $birthDate = $this->faker->dateTimeBetween('-55 years', '-25 years');
        $tmtCpns = Carbon::parse($birthDate)->addYears($this->faker->numberBetween(22, 28));

        if ($tmtCpns->gt(now()->subYear())) {
            $tmtCpns = now()->subYears($this->faker->numberBetween(1, 10));
        }

        return [
            'nip' => $this->faker->unique()->numerify('##################'),
            'gelar_depan' => $this->faker->optional(0.3)->randomElement(['Dr.', 'Drs.', 'Ir.', 'Prof.']),
            'nama_lengkap' => $this->faker->name($fakerGender),
            'gelar_belakang' => $this->faker->optional(0.4)->randomElement(['S.H.', 'S.E.', 'M.H.', 'M.Sc.', 'S.Kom.']),
            'tempat_lahir' => $this->faker->city(),
            'tanggal_lahir' => $birthDate,
            'jenis_kelamin_id' => $genderId,
            'alamat' => $this->faker->address(),
            'no_telepon' => '08' . $this->faker->numerify('##########'),
            'email' => $this->faker->unique()->safeEmail(),
            'tmt_cpns' => $tmtCpns,
            'tmt_pns' => $tmtCpns->copy()->addYear(),
            'gaji_pokok' => 0,
            'is_active' => true,
            'agama_id' => AgamaMaster::inRandomOrder()->value('id') ?? 1,
            'status_pernikahan_id' => StatusPernikahanMaster::inRandomOrder()->value('id') ?? 1,
            'golongan_darah_id' => GolonganDarahMaster::inRandomOrder()->value('id') ?? 1,
            'npwp' => $this->faker->numerify('##.###.###.#-###.000'),
            'no_karpeg' => 'K-' . $this->faker->numerify('######'),
            'no_taspen' => 'T-' . $this->faker->numerify('#######'),
            'bagian_id' => Bagian::inRandomOrder()->value('id'),
            'unit_kerja_id' => UnitKerja::inRandomOrder()->value('id'),
            'tipe_pegawai_id' => TipePegawai::inRandomOrder()->value('id') ?? 1,
            'status_kepegawaian_id' => StatusKepegawaian::where('nama', 'Aktif')->value('id') ?? 1,
        ];
    }

    /**
     * afterCreating hook: build a logical timeline of history records
     * (Pangkat progression + KGB cycles) and let Observers sync gaji_pokok.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Pegawai $pegawai) {
            $golongan = GolonganPangkat::where('is_active', true)->inRandomOrder()->first();
            $jabatan = Jabatan::where('is_active', true)->inRandomOrder()->first();

            if (!$golongan || !$jabatan) return;

            // 1. Initial RiwayatPangkat at tmt_cpns (Observer fires → sets initial gaji_pokok)
            $pegawai->riwayatPangkat()->create([
                'golongan_id' => $golongan->id,
                'tmt_pangkat' => $pegawai->tmt_cpns,
                'tanggal_sk' => $pegawai->tmt_cpns,
                'nomor_sk' => 'SK-CPNS/' . $pegawai->tmt_cpns->year . '/AUTO',
            ]);

            // 2. Initial RiwayatJabatan
            $pegawai->riwayatJabatan()->create([
                'jabatan_id' => $jabatan->id,
                'tmt_jabatan' => $pegawai->tmt_cpns,
                'tanggal_sk' => $pegawai->tmt_cpns,
                'nomor_sk' => 'SK-JAB/' . $pegawai->tmt_cpns->year . '/AUTO',
            ]);

            // 3. Build KGB timeline every 2 years from tmt_cpns
            //    Observer fires on each create → gaji_pokok auto-synced
            $salaryService = app(SalaryCalculatorService::class);
            $tmtKgb = $pegawai->tmt_cpns->copy()->addYears(2);
            $mkgTahun = 2;
            $kgbCounter = 1;

            while ($tmtKgb->lte(today())) {
                $gajiBaru = $salaryService->calculateGaji($golongan->id, $mkgTahun);
                $gajiLama = $salaryService->calculateGaji($golongan->id, max(0, $mkgTahun - 2));

                if ($gajiBaru) {
                    $pegawai->riwayatKgb()->create([
                        'nomor_sk' => 'SK-KGB/' . $tmtKgb->year . '/' . str_pad($kgbCounter, 3, '0', STR_PAD_LEFT),
                        'tmt_kgb' => $tmtKgb->copy(),
                        'gaji_lama' => $gajiLama ?? $gajiBaru,
                        'gaji_baru' => $gajiBaru,
                        'masa_kerja_golongan_tahun' => $mkgTahun,
                        'masa_kerja_golongan_bulan' => 0,
                    ]);
                    $kgbCounter++;
                }

                $tmtKgb->addYears(2);
                $mkgTahun += 2;
            }
        });
    }
}
