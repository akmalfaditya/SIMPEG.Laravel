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
use App\Models\TabelGaji;
use App\Models\TipePegawai;
use App\Models\UnitKerja;
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
     * afterCreating hook: automatically attach initial RiwayatPangkat & RiwayatJabatan
     * to mimic the One-Stop Creation Flow.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Pegawai $pegawai) {
            $golongan = GolonganPangkat::where('is_active', true)->inRandomOrder()->first();
            $jabatan = Jabatan::where('is_active', true)->inRandomOrder()->first();

            if (!$golongan || !$jabatan) return;

            // Auto-attach initial RiwayatPangkat
            $pegawai->riwayatPangkat()->create([
                'golongan_id' => $golongan->id,
                'tmt_pangkat' => $pegawai->tmt_cpns,
                'tanggal_sk' => $pegawai->tmt_cpns,
                'nomor_sk' => 'SK-CPNS/' . $pegawai->tmt_cpns->year . '/AUTO',
            ]);

            // Auto-attach initial RiwayatJabatan
            $pegawai->riwayatJabatan()->create([
                'jabatan_id' => $jabatan->id,
                'tmt_jabatan' => $pegawai->tmt_cpns,
                'tanggal_sk' => $pegawai->tmt_cpns,
                'nomor_sk' => 'SK-JAB/' . $pegawai->tmt_cpns->year . '/AUTO',
            ]);

            // Lookup & set starting salary
            $gaji = TabelGaji::where('golongan_id', $golongan->id)
                ->where('masa_kerja_tahun', 0)
                ->value('gaji_pokok');

            if ($gaji) {
                $pegawai->update(['gaji_pokok' => $gaji]);
            }
        });
    }
}
