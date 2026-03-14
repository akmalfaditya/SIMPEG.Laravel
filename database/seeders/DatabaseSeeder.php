<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            GolonganPangkatSeeder::class,
            MasterDataSeeder::class,
            TabelGajiSeeder::class,
            PegawaiSeeder::class,
            SatyalencanaEdgeCaseSeeder::class,
        ]);
    }
}
