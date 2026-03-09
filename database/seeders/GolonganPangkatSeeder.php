<?php

namespace Database\Seeders;

use App\Models\GolonganPangkat;
use Illuminate\Database\Seeder;

class GolonganPangkatSeeder extends Seeder
{
    public function run(): void
    {
        if (GolonganPangkat::count() > 0) return;

        // Hierarki Golongan/Ruang dan Pangkat PNS berdasarkan:
        // - PP No. 7 Tahun 1977 tentang Peraturan Gaji Pegawai Negeri Sipil
        // - PP No. 99 Tahun 2000 tentang Kenaikan Pangkat PNS
        // - UU No. 5 Tahun 2014 tentang Aparatur Sipil Negara
        // - PP No. 11 Tahun 2017 tentang Manajemen PNS
        // Disesuaikan untuk nomenklatur KEMENIPAS (Kementerian Imigrasi dan Pemasyarakatan)

        $golonganPangkat = [
            // === GOLONGAN I ===
            ['golongan_ruang' => 1,  'label' => 'I/a',   'pangkat' => 'Juru Muda',                 'golongan_group' => 'I',   'min_pendidikan' => 'SD/Sederajat',       'keterangan' => 'Golongan awal untuk lulusan SD/sederajat'],
            ['golongan_ruang' => 2,  'label' => 'I/b',   'pangkat' => 'Juru Muda Tingkat I',       'golongan_group' => 'I',   'min_pendidikan' => 'SD/Sederajat',       'keterangan' => 'Kenaikan reguler dari I/a setelah 4 tahun MKG'],
            ['golongan_ruang' => 3,  'label' => 'I/c',   'pangkat' => 'Juru',                      'golongan_group' => 'I',   'min_pendidikan' => 'SMP/Sederajat',      'keterangan' => 'Golongan awal untuk lulusan SMP/sederajat'],
            ['golongan_ruang' => 4,  'label' => 'I/d',   'pangkat' => 'Juru Tingkat I',            'golongan_group' => 'I',   'min_pendidikan' => 'SMP/Sederajat',      'keterangan' => 'Kenaikan reguler dari I/c setelah 4 tahun MKG'],

            // === GOLONGAN II ===
            ['golongan_ruang' => 5,  'label' => 'II/a',  'pangkat' => 'Pengatur Muda',             'golongan_group' => 'II',  'min_pendidikan' => 'SMA/SMK/Sederajat',  'keterangan' => 'Golongan awal untuk lulusan SMA/SMK/sederajat. Banyak digunakan di KEMENIPAS untuk jabatan pelaksana keimigrasian dan pemasyarakatan'],
            ['golongan_ruang' => 6,  'label' => 'II/b',  'pangkat' => 'Pengatur Muda Tingkat I',   'golongan_group' => 'II',  'min_pendidikan' => 'SMA/SMK/Sederajat',  'keterangan' => 'Kenaikan reguler dari II/a setelah 4 tahun MKG'],
            ['golongan_ruang' => 7,  'label' => 'II/c',  'pangkat' => 'Pengatur',                  'golongan_group' => 'II',  'min_pendidikan' => 'D-III',              'keterangan' => 'Golongan awal untuk lulusan D-III'],
            ['golongan_ruang' => 8,  'label' => 'II/d',  'pangkat' => 'Pengatur Tingkat I',        'golongan_group' => 'II',  'min_pendidikan' => 'D-III',              'keterangan' => 'Kenaikan reguler dari II/c setelah 4 tahun MKG'],

            // === GOLONGAN III ===
            ['golongan_ruang' => 9,  'label' => 'III/a', 'pangkat' => 'Penata Muda',               'golongan_group' => 'III', 'min_pendidikan' => 'D-IV/S-1',           'keterangan' => 'Golongan awal untuk lulusan S-1/D-IV. Umumnya untuk jabatan fungsional ahli pertama di KEMENIPAS (Pemeriksa Keimigrasian, Pembimbing Kemasyarakatan)'],
            ['golongan_ruang' => 10, 'label' => 'III/b', 'pangkat' => 'Penata Muda Tingkat I',     'golongan_group' => 'III', 'min_pendidikan' => 'D-IV/S-1',           'keterangan' => 'Kenaikan reguler dari III/a setelah 4 tahun MKG. Syarat minimal jabatan fungsional ahli muda'],
            ['golongan_ruang' => 11, 'label' => 'III/c', 'pangkat' => 'Penata',                    'golongan_group' => 'III', 'min_pendidikan' => 'D-IV/S-1',           'keterangan' => 'Kenaikan reguler dari III/b. Jenjang menengah fungsional ahli muda di lingkungan KEMENIPAS'],
            ['golongan_ruang' => 12, 'label' => 'III/d', 'pangkat' => 'Penata Tingkat I',          'golongan_group' => 'III', 'min_pendidikan' => 'D-IV/S-1',           'keterangan' => 'Kenaikan reguler dari III/c. Syarat minimal untuk jabatan fungsional ahli madya dan eselon IV'],

            // === GOLONGAN IV ===
            ['golongan_ruang' => 13, 'label' => 'IV/a',  'pangkat' => 'Pembina',                   'golongan_group' => 'IV',  'min_pendidikan' => 'D-IV/S-1',           'keterangan' => 'Golongan awal pejabat struktural eselon III/IV. Di KEMENIPAS: Kepala Kantor Imigrasi Kelas II, Kepala Rutan Kelas II'],
            ['golongan_ruang' => 14, 'label' => 'IV/b',  'pangkat' => 'Pembina Tingkat I',         'golongan_group' => 'IV',  'min_pendidikan' => 'S-2',                'keterangan' => 'Golongan untuk pejabat eselon III senior. Di KEMENIPAS: Kepala Kantor Imigrasi Kelas I, Kepala Lapas Kelas I'],
            ['golongan_ruang' => 15, 'label' => 'IV/c',  'pangkat' => 'Pembina Utama Muda',        'golongan_group' => 'IV',  'min_pendidikan' => 'S-2',                'keterangan' => 'Golongan untuk pejabat eselon II. Di KEMENIPAS: Direktur, Kepala Kantor Wilayah'],
            ['golongan_ruang' => 16, 'label' => 'IV/d',  'pangkat' => 'Pembina Utama Madya',       'golongan_group' => 'IV',  'min_pendidikan' => 'S-2',                'keterangan' => 'Golongan untuk pejabat eselon I. Di KEMENIPAS: Direktur Jenderal, Sekretaris Jenderal'],
            ['golongan_ruang' => 17, 'label' => 'IV/e',  'pangkat' => 'Pembina Utama',             'golongan_group' => 'IV',  'min_pendidikan' => 'S-3',                'keterangan' => 'Golongan tertinggi PNS. Untuk jabatan fungsional utama atau pejabat pimpinan tinggi utama'],
        ];

        foreach ($golonganPangkat as $item) {
            GolonganPangkat::create($item);
        }
    }
}
