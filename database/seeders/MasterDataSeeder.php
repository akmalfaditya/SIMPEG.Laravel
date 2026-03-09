<?php

namespace Database\Seeders;

use App\Enums\JenisJabatan;
use App\Enums\RumpunJabatan;
use App\Models\Jabatan;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        if (Jabatan::count() > 0) return;

        $jabatanList = [
            // === RUMPUN STRUKTURAL (Administrasi Umum) ===
            // Pejabat Administrasi - BUP 58
            ['nama_jabatan' => 'Pengadministrasi Umum', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 5, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Pengadministrasi Kepegawaian', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 5, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Pengadministrasi Keuangan', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 6, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Analis Kebijakan', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 7, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Pengelola Barang Milik Negara', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 6, 'rumpun' => RumpunJabatan::Struktural],
            // Fungsional Ahli Pertama - BUP 58
            ['nama_jabatan' => 'Analis Kepegawaian Ahli Pertama', 'jenis_jabatan' => JenisJabatan::FungsionalAhliPertama, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 8, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Perencana Ahli Pertama', 'jenis_jabatan' => JenisJabatan::FungsionalAhliPertama, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 8, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Pranata Komputer Ahli Pertama', 'jenis_jabatan' => JenisJabatan::FungsionalAhliPertama, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 8, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Auditor Ahli Pertama', 'jenis_jabatan' => JenisJabatan::FungsionalAhliPertama, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 8, 'rumpun' => RumpunJabatan::Struktural],
            // Fungsional Ahli Muda - BUP 58
            ['nama_jabatan' => 'Analis Kepegawaian Ahli Muda', 'jenis_jabatan' => JenisJabatan::FungsionalAhliMuda, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 9, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Perencana Ahli Muda', 'jenis_jabatan' => JenisJabatan::FungsionalAhliMuda, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 9, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Pranata Komputer Ahli Muda', 'jenis_jabatan' => JenisJabatan::FungsionalAhliMuda, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 10, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Auditor Ahli Muda', 'jenis_jabatan' => JenisJabatan::FungsionalAhliMuda, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 10, 'rumpun' => RumpunJabatan::Struktural],
            // Pejabat Pimpinan Tinggi - BUP 60
            ['nama_jabatan' => 'Kepala Dinas', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 2, 'kelas_jabatan' => 15, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Sekretaris Daerah', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 1, 'kelas_jabatan' => 17, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Inspektur', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 2, 'kelas_jabatan' => 14, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Direktur', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 2, 'kelas_jabatan' => 15, 'rumpun' => RumpunJabatan::Struktural],
            // Fungsional Madya - BUP 60
            ['nama_jabatan' => 'Analis Kepegawaian Ahli Madya', 'jenis_jabatan' => JenisJabatan::FungsionalMadya, 'bup' => 60, 'eselon_level' => 0, 'kelas_jabatan' => 11, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Perencana Ahli Madya', 'jenis_jabatan' => JenisJabatan::FungsionalMadya, 'bup' => 60, 'eselon_level' => 0, 'kelas_jabatan' => 11, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Pranata Komputer Ahli Madya', 'jenis_jabatan' => JenisJabatan::FungsionalMadya, 'bup' => 60, 'eselon_level' => 0, 'kelas_jabatan' => 12, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Auditor Ahli Madya', 'jenis_jabatan' => JenisJabatan::FungsionalMadya, 'bup' => 60, 'eselon_level' => 0, 'kelas_jabatan' => 12, 'rumpun' => RumpunJabatan::Struktural],
            // Fungsional Utama - BUP 65
            ['nama_jabatan' => 'Analis Kepegawaian Ahli Utama', 'jenis_jabatan' => JenisJabatan::FungsionalUtama, 'bup' => 65, 'eselon_level' => 0, 'kelas_jabatan' => 13, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Perencana Ahli Utama', 'jenis_jabatan' => JenisJabatan::FungsionalUtama, 'bup' => 65, 'eselon_level' => 0, 'kelas_jabatan' => 13, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Pranata Komputer Ahli Utama', 'jenis_jabatan' => JenisJabatan::FungsionalUtama, 'bup' => 65, 'eselon_level' => 0, 'kelas_jabatan' => 14, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Auditor Ahli Utama', 'jenis_jabatan' => JenisJabatan::FungsionalUtama, 'bup' => 65, 'eselon_level' => 0, 'kelas_jabatan' => 14, 'rumpun' => RumpunJabatan::Struktural],

            // === RUMPUN IMIGRASI ===
            ['nama_jabatan' => 'Pemeriksa Keimigrasian Ahli Pertama', 'jenis_jabatan' => JenisJabatan::FungsionalAhliPertama, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 8, 'rumpun' => RumpunJabatan::Imigrasi],
            ['nama_jabatan' => 'Pemeriksa Keimigrasian Ahli Muda', 'jenis_jabatan' => JenisJabatan::FungsionalAhliMuda, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 9, 'rumpun' => RumpunJabatan::Imigrasi],
            ['nama_jabatan' => 'Pemeriksa Keimigrasian Ahli Madya', 'jenis_jabatan' => JenisJabatan::FungsionalMadya, 'bup' => 60, 'eselon_level' => 0, 'kelas_jabatan' => 11, 'rumpun' => RumpunJabatan::Imigrasi],
            ['nama_jabatan' => 'Analis Keimigrasian', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 7, 'rumpun' => RumpunJabatan::Imigrasi],
            ['nama_jabatan' => 'Teknisi Keimigrasian', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 5, 'rumpun' => RumpunJabatan::Imigrasi],
            ['nama_jabatan' => 'Kepala Kantor Imigrasi', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 3, 'kelas_jabatan' => 12, 'rumpun' => RumpunJabatan::Imigrasi],

            // === RUMPUN PEMASYARAKATAN ===
            ['nama_jabatan' => 'Pembimbing Kemasyarakatan Ahli Pertama', 'jenis_jabatan' => JenisJabatan::FungsionalAhliPertama, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 8, 'rumpun' => RumpunJabatan::Pemasyarakatan],
            ['nama_jabatan' => 'Pembimbing Kemasyarakatan Ahli Muda', 'jenis_jabatan' => JenisJabatan::FungsionalAhliMuda, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 9, 'rumpun' => RumpunJabatan::Pemasyarakatan],
            ['nama_jabatan' => 'Pembimbing Kemasyarakatan Ahli Madya', 'jenis_jabatan' => JenisJabatan::FungsionalMadya, 'bup' => 60, 'eselon_level' => 0, 'kelas_jabatan' => 11, 'rumpun' => RumpunJabatan::Pemasyarakatan],
            ['nama_jabatan' => 'Penjaga Tahanan', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 4, 'rumpun' => RumpunJabatan::Pemasyarakatan],
            ['nama_jabatan' => 'Pengamat Pemasyarakatan', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 6, 'rumpun' => RumpunJabatan::Pemasyarakatan],
            ['nama_jabatan' => 'Kepala Lembaga Pemasyarakatan', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 3, 'kelas_jabatan' => 12, 'rumpun' => RumpunJabatan::Pemasyarakatan],
        ];

        foreach ($jabatanList as $item) {
            Jabatan::create($item);
        }
    }
}
