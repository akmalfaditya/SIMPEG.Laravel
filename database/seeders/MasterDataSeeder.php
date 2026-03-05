<?php

namespace Database\Seeders;

use App\Enums\JenisJabatan;
use App\Models\Jabatan;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        if (Jabatan::count() > 0) return;

        $jabatanList = [
            // Pejabat Administrasi - BUP 58
            ['nama_jabatan' => 'Pengadministrasi Umum', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 5],
            ['nama_jabatan' => 'Pengadministrasi Kepegawaian', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 5],
            ['nama_jabatan' => 'Pengadministrasi Keuangan', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 6],
            ['nama_jabatan' => 'Analis Kebijakan', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 7],
            ['nama_jabatan' => 'Pengelola Barang Milik Negara', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 6],
            // Fungsional Ahli Pertama - BUP 58
            ['nama_jabatan' => 'Analis Kepegawaian Ahli Pertama', 'jenis_jabatan' => JenisJabatan::FungsionalAhliPertama, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 8],
            ['nama_jabatan' => 'Perencana Ahli Pertama', 'jenis_jabatan' => JenisJabatan::FungsionalAhliPertama, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 8],
            ['nama_jabatan' => 'Pranata Komputer Ahli Pertama', 'jenis_jabatan' => JenisJabatan::FungsionalAhliPertama, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 8],
            ['nama_jabatan' => 'Auditor Ahli Pertama', 'jenis_jabatan' => JenisJabatan::FungsionalAhliPertama, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 8],
            // Fungsional Ahli Muda - BUP 58
            ['nama_jabatan' => 'Analis Kepegawaian Ahli Muda', 'jenis_jabatan' => JenisJabatan::FungsionalAhliMuda, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 9],
            ['nama_jabatan' => 'Perencana Ahli Muda', 'jenis_jabatan' => JenisJabatan::FungsionalAhliMuda, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 9],
            ['nama_jabatan' => 'Pranata Komputer Ahli Muda', 'jenis_jabatan' => JenisJabatan::FungsionalAhliMuda, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 10],
            ['nama_jabatan' => 'Auditor Ahli Muda', 'jenis_jabatan' => JenisJabatan::FungsionalAhliMuda, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 10],
            // Pejabat Pimpinan Tinggi - BUP 60
            ['nama_jabatan' => 'Kepala Dinas', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 2, 'kelas_jabatan' => 15],
            ['nama_jabatan' => 'Sekretaris Daerah', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 1, 'kelas_jabatan' => 17],
            ['nama_jabatan' => 'Inspektur', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 2, 'kelas_jabatan' => 14],
            ['nama_jabatan' => 'Direktur', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 2, 'kelas_jabatan' => 15],
            // Fungsional Madya - BUP 60
            ['nama_jabatan' => 'Analis Kepegawaian Ahli Madya', 'jenis_jabatan' => JenisJabatan::FungsionalMadya, 'bup' => 60, 'eselon_level' => 0, 'kelas_jabatan' => 11],
            ['nama_jabatan' => 'Perencana Ahli Madya', 'jenis_jabatan' => JenisJabatan::FungsionalMadya, 'bup' => 60, 'eselon_level' => 0, 'kelas_jabatan' => 11],
            ['nama_jabatan' => 'Pranata Komputer Ahli Madya', 'jenis_jabatan' => JenisJabatan::FungsionalMadya, 'bup' => 60, 'eselon_level' => 0, 'kelas_jabatan' => 12],
            ['nama_jabatan' => 'Auditor Ahli Madya', 'jenis_jabatan' => JenisJabatan::FungsionalMadya, 'bup' => 60, 'eselon_level' => 0, 'kelas_jabatan' => 12],
            // Fungsional Utama - BUP 65
            ['nama_jabatan' => 'Analis Kepegawaian Ahli Utama', 'jenis_jabatan' => JenisJabatan::FungsionalUtama, 'bup' => 65, 'eselon_level' => 0, 'kelas_jabatan' => 13],
            ['nama_jabatan' => 'Perencana Ahli Utama', 'jenis_jabatan' => JenisJabatan::FungsionalUtama, 'bup' => 65, 'eselon_level' => 0, 'kelas_jabatan' => 13],
            ['nama_jabatan' => 'Pranata Komputer Ahli Utama', 'jenis_jabatan' => JenisJabatan::FungsionalUtama, 'bup' => 65, 'eselon_level' => 0, 'kelas_jabatan' => 14],
            ['nama_jabatan' => 'Auditor Ahli Utama', 'jenis_jabatan' => JenisJabatan::FungsionalUtama, 'bup' => 65, 'eselon_level' => 0, 'kelas_jabatan' => 14],
        ];

        foreach ($jabatanList as $item) {
            Jabatan::create($item);
        }
    }
}
