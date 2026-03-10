<?php

namespace Database\Seeders;

use App\Enums\JenisJabatan;
use App\Enums\RumpunJabatan;
use App\Models\AgamaMaster;
use App\Models\Bagian;
use App\Models\GolonganDarahMaster;
use App\Models\Jabatan;
use App\Models\JenisKelaminMaster;
use App\Models\StatusKepegawaian;
use App\Models\StatusPernikahanMaster;
use App\Models\TipePegawai;
use App\Models\UnitKerja;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Nomenklatur jabatan berdasarkan peraturan KEMENIPAS
     * (Kementerian Imigrasi dan Pemasyarakatan) RI.
     *
     * Dasar hukum:
     * - Perpres No. 73 Tahun 2024 tentang Kementerian Imigrasi dan Pemasyarakatan
     * - Permenkumham No. 29 Tahun 2015 tentang Organisasi dan Tata Kerja Kementerian Hukum dan HAM (sebagai dasar historis)
     * - PP No. 11 Tahun 2017 tentang Manajemen PNS
     * - PP No. 17 Tahun 2020 tentang Perubahan atas PP No. 11 Tahun 2017
     * - PermenPANRB No. 1 Tahun 2023 tentang Jabatan Fungsional
     *
     * BUP (Batas Usia Pensiun):
     * - 58 tahun: Pejabat Administrasi & Fungsional Ahli Pertama/Muda (PP 11/2017 Pasal 239)
     * - 60 tahun: Pejabat Pimpinan Tinggi & Fungsional Madya (PP 11/2017 Pasal 239)
     * - 65 tahun: Fungsional Ahli Utama (PP 11/2017 Pasal 239)
     */
    public function run(): void
    {
        // Seed normalized master data tables
        $this->seedSimpleMaster(TipePegawai::class, ['PNS', 'CPNS', 'PPPK']);
        $this->seedSimpleMaster(StatusKepegawaian::class, ['Aktif', 'Tidak Aktif', 'Pensiun']);
        $this->seedSimpleMaster(Bagian::class, ['Tata Usaha', 'Tikim', 'Lantaskim', 'Inteldakim', 'Intaltuskim']);
        $this->seedSimpleMaster(UnitKerja::class, ['Kanim Jakut']);
        $this->seedSimpleMaster(JenisKelaminMaster::class, ['Laki-laki', 'Perempuan']);
        $this->seedSimpleMaster(AgamaMaster::class, ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu']);
        $this->seedSimpleMaster(StatusPernikahanMaster::class, ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']);
        $this->seedSimpleMaster(GolonganDarahMaster::class, ['A', 'B', 'AB', 'O']);

        if (Jabatan::count() > 0) return;

        $jabatanList = [
            // ═══════════════════════════════════════════════════════
            // RUMPUN STRUKTURAL (Administrasi Umum / Sekretariat)
            // ═══════════════════════════════════════════════════════

            // --- Pelaksana / Pejabat Administrasi - BUP 58 ---
            ['nama_jabatan' => 'Pengadministrasi Umum', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 5, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Pengadministrasi Kepegawaian', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 5, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Pengadministrasi Keuangan', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 6, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Pengadministrasi Persuratan', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 5, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Pengelola Barang Milik Negara', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 6, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Analis Kebijakan', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 7, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Pengelola Data dan Informasi', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 6, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Pengemudi', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 3, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Pranata Kearsipan', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 5, 'rumpun' => RumpunJabatan::Struktural],

            // --- Fungsional Ahli Pertama - BUP 58 ---
            ['nama_jabatan' => 'Analis Kepegawaian Ahli Pertama', 'jenis_jabatan' => JenisJabatan::FungsionalAhliPertama, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 8, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Perencana Ahli Pertama', 'jenis_jabatan' => JenisJabatan::FungsionalAhliPertama, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 8, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Pranata Komputer Ahli Pertama', 'jenis_jabatan' => JenisJabatan::FungsionalAhliPertama, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 8, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Auditor Ahli Pertama', 'jenis_jabatan' => JenisJabatan::FungsionalAhliPertama, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 8, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Analis Hukum Ahli Pertama', 'jenis_jabatan' => JenisJabatan::FungsionalAhliPertama, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 8, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Pengelola Pengadaan Barang/Jasa Ahli Pertama', 'jenis_jabatan' => JenisJabatan::FungsionalAhliPertama, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 8, 'rumpun' => RumpunJabatan::Struktural],

            // --- Fungsional Ahli Muda - BUP 58 ---
            ['nama_jabatan' => 'Analis Kepegawaian Ahli Muda', 'jenis_jabatan' => JenisJabatan::FungsionalAhliMuda, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 9, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Perencana Ahli Muda', 'jenis_jabatan' => JenisJabatan::FungsionalAhliMuda, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 9, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Pranata Komputer Ahli Muda', 'jenis_jabatan' => JenisJabatan::FungsionalAhliMuda, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 10, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Auditor Ahli Muda', 'jenis_jabatan' => JenisJabatan::FungsionalAhliMuda, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 10, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Analis Hukum Ahli Muda', 'jenis_jabatan' => JenisJabatan::FungsionalAhliMuda, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 9, 'rumpun' => RumpunJabatan::Struktural],

            // --- Fungsional Madya - BUP 60 ---
            ['nama_jabatan' => 'Analis Kepegawaian Ahli Madya', 'jenis_jabatan' => JenisJabatan::FungsionalMadya, 'bup' => 60, 'eselon_level' => 0, 'kelas_jabatan' => 11, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Perencana Ahli Madya', 'jenis_jabatan' => JenisJabatan::FungsionalMadya, 'bup' => 60, 'eselon_level' => 0, 'kelas_jabatan' => 11, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Pranata Komputer Ahli Madya', 'jenis_jabatan' => JenisJabatan::FungsionalMadya, 'bup' => 60, 'eselon_level' => 0, 'kelas_jabatan' => 12, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Auditor Ahli Madya', 'jenis_jabatan' => JenisJabatan::FungsionalMadya, 'bup' => 60, 'eselon_level' => 0, 'kelas_jabatan' => 12, 'rumpun' => RumpunJabatan::Struktural],

            // --- Fungsional Utama - BUP 65 ---
            ['nama_jabatan' => 'Analis Kepegawaian Ahli Utama', 'jenis_jabatan' => JenisJabatan::FungsionalUtama, 'bup' => 65, 'eselon_level' => 0, 'kelas_jabatan' => 13, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Perencana Ahli Utama', 'jenis_jabatan' => JenisJabatan::FungsionalUtama, 'bup' => 65, 'eselon_level' => 0, 'kelas_jabatan' => 13, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Pranata Komputer Ahli Utama', 'jenis_jabatan' => JenisJabatan::FungsionalUtama, 'bup' => 65, 'eselon_level' => 0, 'kelas_jabatan' => 14, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Auditor Ahli Utama', 'jenis_jabatan' => JenisJabatan::FungsionalUtama, 'bup' => 65, 'eselon_level' => 0, 'kelas_jabatan' => 14, 'rumpun' => RumpunJabatan::Struktural],

            // --- Pejabat Pimpinan Tinggi Struktural - BUP 60 ---
            ['nama_jabatan' => 'Sekretaris Jenderal', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 1, 'kelas_jabatan' => 17, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Inspektur Jenderal', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 1, 'kelas_jabatan' => 17, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Kepala Biro Kepegawaian', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 2, 'kelas_jabatan' => 15, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Kepala Biro Keuangan', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 2, 'kelas_jabatan' => 15, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Kepala Biro Perencanaan', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 2, 'kelas_jabatan' => 15, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Kepala Biro Umum', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 2, 'kelas_jabatan' => 14, 'rumpun' => RumpunJabatan::Struktural],
            ['nama_jabatan' => 'Inspektur', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 2, 'kelas_jabatan' => 14, 'rumpun' => RumpunJabatan::Struktural],

            // ═══════════════════════════════════════════════════════
            // RUMPUN IMIGRASI
            // Berdasarkan Perpres 73/2024 & Peraturan Ditjen Imigrasi
            // ═══════════════════════════════════════════════════════

            // --- Pelaksana Imigrasi - BUP 58 ---
            ['nama_jabatan' => 'Teknisi Keimigrasian', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 5, 'rumpun' => RumpunJabatan::Imigrasi],
            ['nama_jabatan' => 'Analis Keimigrasian', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 7, 'rumpun' => RumpunJabatan::Imigrasi],
            ['nama_jabatan' => 'Operator Paspor', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 5, 'rumpun' => RumpunJabatan::Imigrasi],
            ['nama_jabatan' => 'Petugas Tempat Pemeriksaan Imigrasi', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 6, 'rumpun' => RumpunJabatan::Imigrasi],
            ['nama_jabatan' => 'Pengelola Izin Tinggal', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 6, 'rumpun' => RumpunJabatan::Imigrasi],

            // --- Fungsional Imigrasi Ahli Pertama - BUP 58 ---
            ['nama_jabatan' => 'Pemeriksa Keimigrasian Ahli Pertama', 'jenis_jabatan' => JenisJabatan::FungsionalAhliPertama, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 8, 'rumpun' => RumpunJabatan::Imigrasi],
            ['nama_jabatan' => 'Intelijen Keimigrasian Ahli Pertama', 'jenis_jabatan' => JenisJabatan::FungsionalAhliPertama, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 8, 'rumpun' => RumpunJabatan::Imigrasi],
            ['nama_jabatan' => 'Penyidik Pegawai Negeri Sipil (PPNS) Imigrasi Ahli Pertama', 'jenis_jabatan' => JenisJabatan::FungsionalAhliPertama, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 8, 'rumpun' => RumpunJabatan::Imigrasi],

            // --- Fungsional Imigrasi Ahli Muda - BUP 58 ---
            ['nama_jabatan' => 'Pemeriksa Keimigrasian Ahli Muda', 'jenis_jabatan' => JenisJabatan::FungsionalAhliMuda, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 9, 'rumpun' => RumpunJabatan::Imigrasi],
            ['nama_jabatan' => 'Intelijen Keimigrasian Ahli Muda', 'jenis_jabatan' => JenisJabatan::FungsionalAhliMuda, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 9, 'rumpun' => RumpunJabatan::Imigrasi],
            ['nama_jabatan' => 'PPNS Imigrasi Ahli Muda', 'jenis_jabatan' => JenisJabatan::FungsionalAhliMuda, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 10, 'rumpun' => RumpunJabatan::Imigrasi],

            // --- Fungsional Imigrasi Ahli Madya - BUP 60 ---
            ['nama_jabatan' => 'Pemeriksa Keimigrasian Ahli Madya', 'jenis_jabatan' => JenisJabatan::FungsionalMadya, 'bup' => 60, 'eselon_level' => 0, 'kelas_jabatan' => 11, 'rumpun' => RumpunJabatan::Imigrasi],
            ['nama_jabatan' => 'Intelijen Keimigrasian Ahli Madya', 'jenis_jabatan' => JenisJabatan::FungsionalMadya, 'bup' => 60, 'eselon_level' => 0, 'kelas_jabatan' => 11, 'rumpun' => RumpunJabatan::Imigrasi],

            // --- Pimpinan Tinggi Imigrasi - BUP 60 ---
            ['nama_jabatan' => 'Direktur Jenderal Imigrasi', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 1, 'kelas_jabatan' => 17, 'rumpun' => RumpunJabatan::Imigrasi],
            ['nama_jabatan' => 'Kepala Kantor Imigrasi Kelas I', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 3, 'kelas_jabatan' => 12, 'rumpun' => RumpunJabatan::Imigrasi],
            ['nama_jabatan' => 'Kepala Kantor Imigrasi Kelas II', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 3, 'kelas_jabatan' => 11, 'rumpun' => RumpunJabatan::Imigrasi],
            ['nama_jabatan' => 'Kepala Kantor Imigrasi Kelas III', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 4, 'kelas_jabatan' => 10, 'rumpun' => RumpunJabatan::Imigrasi],
            ['nama_jabatan' => 'Kepala Rumah Detensi Imigrasi', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 3, 'kelas_jabatan' => 11, 'rumpun' => RumpunJabatan::Imigrasi],
            ['nama_jabatan' => 'Direktur Lalu Lintas Keimigrasian', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 2, 'kelas_jabatan' => 15, 'rumpun' => RumpunJabatan::Imigrasi],
            ['nama_jabatan' => 'Direktur Izin Tinggal dan Status Keimigrasian', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 2, 'kelas_jabatan' => 15, 'rumpun' => RumpunJabatan::Imigrasi],
            ['nama_jabatan' => 'Direktur Intelijen Keimigrasian', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 2, 'kelas_jabatan' => 15, 'rumpun' => RumpunJabatan::Imigrasi],
            ['nama_jabatan' => 'Direktur Pengawasan dan Penindakan Keimigrasian', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 2, 'kelas_jabatan' => 15, 'rumpun' => RumpunJabatan::Imigrasi],

            // ═══════════════════════════════════════════════════════
            // RUMPUN PEMASYARAKATAN
            // Berdasarkan Perpres 73/2024 & Peraturan Ditjen Pemasyarakatan
            // ═══════════════════════════════════════════════════════

            // --- Pelaksana Pemasyarakatan - BUP 58 ---
            ['nama_jabatan' => 'Penjaga Tahanan', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 4, 'rumpun' => RumpunJabatan::Pemasyarakatan],
            ['nama_jabatan' => 'Pengamat Pemasyarakatan', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 6, 'rumpun' => RumpunJabatan::Pemasyarakatan],
            ['nama_jabatan' => 'Petugas Pengamanan Lapas', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 5, 'rumpun' => RumpunJabatan::Pemasyarakatan],
            ['nama_jabatan' => 'Petugas Registrasi Warga Binaan', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 5, 'rumpun' => RumpunJabatan::Pemasyarakatan],
            ['nama_jabatan' => 'Pengelola Pembinaan Narapidana', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 6, 'rumpun' => RumpunJabatan::Pemasyarakatan],
            ['nama_jabatan' => 'Petugas Bimbingan Kerja', 'jenis_jabatan' => JenisJabatan::PejabatAdministrasi, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 6, 'rumpun' => RumpunJabatan::Pemasyarakatan],

            // --- Fungsional Pemasyarakatan Ahli Pertama - BUP 58 ---
            ['nama_jabatan' => 'Pembimbing Kemasyarakatan Ahli Pertama', 'jenis_jabatan' => JenisJabatan::FungsionalAhliPertama, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 8, 'rumpun' => RumpunJabatan::Pemasyarakatan],
            ['nama_jabatan' => 'Penilik Pemasyarakatan Ahli Pertama', 'jenis_jabatan' => JenisJabatan::FungsionalAhliPertama, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 8, 'rumpun' => RumpunJabatan::Pemasyarakatan],
            ['nama_jabatan' => 'Perawat Tahanan Ahli Pertama', 'jenis_jabatan' => JenisJabatan::FungsionalAhliPertama, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 8, 'rumpun' => RumpunJabatan::Pemasyarakatan],

            // --- Fungsional Pemasyarakatan Ahli Muda - BUP 58 ---
            ['nama_jabatan' => 'Pembimbing Kemasyarakatan Ahli Muda', 'jenis_jabatan' => JenisJabatan::FungsionalAhliMuda, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 9, 'rumpun' => RumpunJabatan::Pemasyarakatan],
            ['nama_jabatan' => 'Penilik Pemasyarakatan Ahli Muda', 'jenis_jabatan' => JenisJabatan::FungsionalAhliMuda, 'bup' => 58, 'eselon_level' => 0, 'kelas_jabatan' => 9, 'rumpun' => RumpunJabatan::Pemasyarakatan],

            // --- Fungsional Pemasyarakatan Ahli Madya - BUP 60 ---
            ['nama_jabatan' => 'Pembimbing Kemasyarakatan Ahli Madya', 'jenis_jabatan' => JenisJabatan::FungsionalMadya, 'bup' => 60, 'eselon_level' => 0, 'kelas_jabatan' => 11, 'rumpun' => RumpunJabatan::Pemasyarakatan],
            ['nama_jabatan' => 'Penilik Pemasyarakatan Ahli Madya', 'jenis_jabatan' => JenisJabatan::FungsionalMadya, 'bup' => 60, 'eselon_level' => 0, 'kelas_jabatan' => 11, 'rumpun' => RumpunJabatan::Pemasyarakatan],

            // --- Pimpinan Tinggi Pemasyarakatan - BUP 60 ---
            ['nama_jabatan' => 'Direktur Jenderal Pemasyarakatan', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 1, 'kelas_jabatan' => 17, 'rumpun' => RumpunJabatan::Pemasyarakatan],
            ['nama_jabatan' => 'Kepala Lembaga Pemasyarakatan Kelas I', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 3, 'kelas_jabatan' => 12, 'rumpun' => RumpunJabatan::Pemasyarakatan],
            ['nama_jabatan' => 'Kepala Lembaga Pemasyarakatan Kelas II A', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 3, 'kelas_jabatan' => 11, 'rumpun' => RumpunJabatan::Pemasyarakatan],
            ['nama_jabatan' => 'Kepala Lembaga Pemasyarakatan Kelas II B', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 4, 'kelas_jabatan' => 10, 'rumpun' => RumpunJabatan::Pemasyarakatan],
            ['nama_jabatan' => 'Kepala Rumah Tahanan Negara Kelas I', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 3, 'kelas_jabatan' => 12, 'rumpun' => RumpunJabatan::Pemasyarakatan],
            ['nama_jabatan' => 'Kepala Rumah Tahanan Negara Kelas II', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 4, 'kelas_jabatan' => 10, 'rumpun' => RumpunJabatan::Pemasyarakatan],
            ['nama_jabatan' => 'Kepala Balai Pemasyarakatan (Bapas)', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 3, 'kelas_jabatan' => 11, 'rumpun' => RumpunJabatan::Pemasyarakatan],
            ['nama_jabatan' => 'Direktur Pembinaan Narapidana dan Latihan Kerja', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 2, 'kelas_jabatan' => 15, 'rumpun' => RumpunJabatan::Pemasyarakatan],
            ['nama_jabatan' => 'Direktur Bimbingan Kemasyarakatan', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 2, 'kelas_jabatan' => 15, 'rumpun' => RumpunJabatan::Pemasyarakatan],
            ['nama_jabatan' => 'Direktur Keamanan dan Ketertiban', 'jenis_jabatan' => JenisJabatan::PejabatPimpinanTinggi, 'bup' => 60, 'eselon_level' => 2, 'kelas_jabatan' => 15, 'rumpun' => RumpunJabatan::Pemasyarakatan],
        ];

        foreach ($jabatanList as $item) {
            Jabatan::create($item);
        }
    }

    private function seedSimpleMaster(string $modelClass, array $names): void
    {
        foreach ($names as $name) {
            $modelClass::firstOrCreate(['nama' => $name]);
        }
    }
}
