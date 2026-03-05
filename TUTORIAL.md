# Tutorial: Membangun SIMPEG dari Awal (Step by Step)

Panduan lengkap untuk membuat Sistem Informasi Manajemen Pegawai (SIMPEG) menggunakan Laravel 12, SQLite, dan Tailwind CSS — dari nol hingga selesai.

---

## Daftar Isi

1. [Persiapan & Inisialisasi Project](#1-persiapan--inisialisasi-project)
2. [Konfigurasi Environment](#2-konfigurasi-environment)
3. [Membuat Enum](#3-membuat-enum)
4. [Membuat Database Migration](#4-membuat-database-migration)
5. [Membuat Eloquent Model](#5-membuat-eloquent-model)
6. [Membuat Database Seeder](#6-membuat-database-seeder)
7. [Membuat Service Layer (Business Logic)](#7-membuat-service-layer-business-logic)
8. [Membuat Controller](#8-membuat-controller)
9. [Mendefinisikan Routes](#9-mendefinisikan-routes)
10. [Membuat Views (Blade + Tailwind)](#10-membuat-views-blade--tailwind)
11. [Build & Menjalankan Aplikasi](#11-build--menjalankan-aplikasi)

---

## 1. Persiapan & Inisialisasi Project

### Prasyarat

Pastikan sudah terinstall:
- **PHP 8.2+** dengan extension `sqlite3`, `mbstring`, `openssl`, `pdo_sqlite`
- **Composer 2.x**
- **Node.js 18+** dan **NPM 9+**

### Buat Project Laravel Baru

```bash
composer create-project laravel/laravel SIMPEG.Laravel
cd SIMPEG.Laravel
```

### Install Tailwind CSS (via Vite Plugin)

```bash
npm install -D @tailwindcss/vite
```

Edit `vite.config.js`:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
```

Edit `resources/css/app.css`:

```css
@import "tailwindcss";

@source "../views/**/*.blade.php";
@source "../../app/**/*.php";

@theme {
    --font-sans: 'Inter', ui-sans-serif, system-ui, sans-serif;
}
```

---

## 2. Konfigurasi Environment

Edit file `.env`:

```env
APP_NAME=SIMPEG
APP_LOCALE=id
APP_FAKER_LOCALE=id_ID
DB_CONNECTION=sqlite
```

Buat file database SQLite kosong:

```bash
# Linux/Mac
touch database/database.sqlite

# Windows (PowerShell)
New-Item database/database.sqlite -ItemType File
```

> **Catatan:** Hapus konfigurasi `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` yang tidak diperlukan untuk SQLite.

---

## 3. Membuat Enum

SIMPEG memiliki 7 PHP Enum yang merepresentasikan data master. Buat folder `app/Enums/` dan buat file-file berikut:

### 3.1 JenisKelamin

```bash
# Buat folder Enums
mkdir app/Enums
```

Buat file `app/Enums/JenisKelamin.php`:

```php
<?php

namespace App\Enums;

enum JenisKelamin: int
{
    case LakiLaki = 0;
    case Perempuan = 1;

    public function label(): string
    {
        return match ($this) {
            self::LakiLaki => 'Laki-Laki',
            self::Perempuan => 'Perempuan',
        };
    }
}
```

### 3.2 Agama

Buat file `app/Enums/Agama.php`:

```php
<?php

namespace App\Enums;

enum Agama: int
{
    case Islam = 0;
    case Kristen = 1;
    case Katolik = 2;
    case Hindu = 3;
    case Buddha = 4;
    case Konghucu = 5;

    public function label(): string
    {
        return $this->name;
    }
}
```

### 3.3 GolonganDarah

Buat file `app/Enums/GolonganDarah.php`:

```php
<?php

namespace App\Enums;

enum GolonganDarah: int
{
    case A = 0;
    case B = 1;
    case AB = 2;
    case O = 3;

    public function label(): string
    {
        return $this->name;
    }
}
```

### 3.4 GolonganRuang

Buat file `app/Enums/GolonganRuang.php`:

```php
<?php

namespace App\Enums;

enum GolonganRuang: int
{
    case I_a = 0;
    case I_b = 1;
    case I_c = 2;
    case I_d = 3;
    case II_a = 4;
    case II_b = 5;
    case II_c = 6;
    case II_d = 7;
    case III_a = 8;
    case III_b = 9;
    case III_c = 10;
    case III_d = 11;
    case IV_a = 12;
    case IV_b = 13;
    case IV_c = 14;
    case IV_d = 15;
    case IV_e = 16;

    public function label(): string
    {
        $parts = explode('_', $this->name);
        return $parts[0] . '/' . $parts[1];
    }
}
```

### 3.5 JenisJabatan

Buat file `app/Enums/JenisJabatan.php`:

```php
<?php

namespace App\Enums;

enum JenisJabatan: int
{
    case PejabatAdministrasi = 0;
    case FungsionalAhliPertama = 1;
    case FungsionalAhliMuda = 2;
    case FungsionalMadya = 3;
    case FungsionalUtama = 4;
    case PejabatPimpinanTinggi = 5;

    public function label(): string
    {
        return match ($this) {
            self::PejabatAdministrasi => 'Pejabat Administrasi',
            self::FungsionalAhliPertama => 'Fungsional Ahli Pertama',
            self::FungsionalAhliMuda => 'Fungsional Ahli Muda',
            self::FungsionalMadya => 'Fungsional Madya',
            self::FungsionalUtama => 'Fungsional Utama',
            self::PejabatPimpinanTinggi => 'Pejabat Pimpinan Tinggi',
        };
    }
}
```

### 3.6 StatusPernikahan

Buat file `app/Enums/StatusPernikahan.php`:

```php
<?php

namespace App\Enums;

enum StatusPernikahan: int
{
    case BelumMenikah = 0;
    case Menikah = 1;
    case CeraiHidup = 2;
    case CeraiMati = 3;

    public function label(): string
    {
        return match ($this) {
            self::BelumMenikah => 'Belum Menikah',
            self::Menikah => 'Menikah',
            self::CeraiHidup => 'Cerai Hidup',
            self::CeraiMati => 'Cerai Mati',
        };
    }
}
```

### 3.7 TingkatHukuman

Buat file `app/Enums/TingkatHukuman.php`:

```php
<?php

namespace App\Enums;

enum TingkatHukuman: int
{
    case Ringan = 0;
    case Sedang = 1;
    case Berat = 2;

    public function label(): string
    {
        return $this->name;
    }
}
```

---

## 4. Membuat Database Migration

Kita akan membuat 4 file migrasi. Gunakan penamaan dengan prefix tanggal agar urutan eksekusi benar.

### 4.1 Tabel Jabatans

Buat file `database/migrations/2024_01_01_000001_create_jabatans_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jabatans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jabatan');
            $table->integer('jenis_jabatan');         // JenisJabatan enum
            $table->integer('bup')->default(58);      // Batas Usia Pensiun
            $table->integer('eselon_level')->default(0);
            $table->integer('kelas_jabatan')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jabatans');
    }
};
```

### 4.2 Tabel Pegawais

Buat file `database/migrations/2024_01_01_000002_create_pegawais_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawais', function (Blueprint $table) {
            $table->id();
            // Data Pokok
            $table->string('nip')->unique();
            $table->string('nama_lengkap');
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir');
            $table->integer('jenis_kelamin');           // JenisKelamin enum
            $table->text('alamat')->nullable();
            $table->string('no_telepon')->nullable();
            $table->string('email')->nullable();
            // ASN
            $table->date('tmt_cpns');
            $table->date('tmt_pns')->nullable();
            $table->decimal('gaji_pokok', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            // Data Tambahan
            $table->integer('agama');
            $table->integer('status_pernikahan');
            $table->integer('golongan_darah');
            $table->string('npwp')->nullable();
            $table->string('no_karpeg')->nullable();
            $table->string('no_taspen')->nullable();
            $table->string('unit_kerja')->nullable();
            // Soft Delete & Timestamps
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};
```

### 4.3 Tabel-tabel Riwayat

Buat file `database/migrations/2024_01_01_000003_create_riwayat_tables.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Riwayat Pangkat
        Schema::create('riwayat_pangkats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->integer('golongan_ruang');       // GolonganRuang enum
            $table->string('nomor_sk')->nullable();
            $table->date('tmt_pangkat');
            $table->date('tanggal_sk');
            $table->string('file_pdf_path')->nullable();
            $table->string('google_drive_link')->nullable();
            $table->timestamps();
        });

        // Riwayat Jabatan
        Schema::create('riwayat_jabatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->foreignId('jabatan_id')->constrained('jabatans');
            $table->string('nomor_sk')->nullable();
            $table->date('tmt_jabatan');
            $table->date('tanggal_sk');
            $table->string('file_pdf_path')->nullable();
            $table->string('google_drive_link')->nullable();
            $table->timestamps();
        });

        // Riwayat KGB
        Schema::create('riwayat_kgbs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->string('nomor_sk')->nullable();
            $table->date('tmt_kgb');
            $table->decimal('gaji_lama', 15, 2)->default(0);
            $table->decimal('gaji_baru', 15, 2)->default(0);
            $table->integer('masa_kerja_golongan_tahun')->default(0);
            $table->integer('masa_kerja_golongan_bulan')->default(0);
            $table->string('file_pdf_path')->nullable();
            $table->string('google_drive_link')->nullable();
            $table->timestamps();
        });

        // Riwayat Hukuman Disiplin
        Schema::create('riwayat_hukuman_disiplins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->integer('tingkat_hukuman');       // TingkatHukuman enum
            $table->string('jenis_hukuman');
            $table->string('nomor_sk')->nullable();
            $table->date('tanggal_sk')->nullable();
            $table->date('tmt_hukuman');
            $table->date('tmt_selesai_hukuman')->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // Riwayat Pendidikan
        Schema::create('riwayat_pendidikans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->string('tingkat_pendidikan');
            $table->string('institusi');
            $table->string('jurusan');
            $table->integer('tahun_lulus');
            $table->string('no_ijazah')->nullable();
            $table->date('tanggal_ijazah')->nullable();
            $table->string('file_pdf_path')->nullable();
            $table->string('google_drive_link')->nullable();
            $table->timestamps();
        });

        // Riwayat Latihan Jabatan
        Schema::create('riwayat_latihan_jabatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->string('nama_latihan');
            $table->integer('tahun_pelaksanaan');
            $table->integer('jumlah_jam')->default(0);
            $table->string('penyelenggara')->nullable();
            $table->string('tempat_pelaksanaan')->nullable();
            $table->string('no_sertifikat')->nullable();
            $table->string('file_pdf_path')->nullable();
            $table->string('google_drive_link')->nullable();
            $table->timestamps();
        });

        // Riwayat Penghargaan
        Schema::create('riwayat_penghargaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->string('nama_penghargaan');
            $table->integer('milestone')->default(0);
            $table->date('tanggal_penghargaan')->nullable();
            $table->string('instansi_pemberi')->nullable();
            $table->string('file_pdf_path')->nullable();
            $table->string('google_drive_link')->nullable();
            $table->timestamps();
        });

        // Penilaian Kinerja (SKP)
        Schema::create('penilaian_kinerjas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->integer('tahun');
            $table->string('nilai_skp');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_kinerjas');
        Schema::dropIfExists('riwayat_penghargaans');
        Schema::dropIfExists('riwayat_latihan_jabatans');
        Schema::dropIfExists('riwayat_pendidikans');
        Schema::dropIfExists('riwayat_hukuman_disiplins');
        Schema::dropIfExists('riwayat_kgbs');
        Schema::dropIfExists('riwayat_jabatans');
        Schema::dropIfExists('riwayat_pangkats');
    }
};
```

### 4.4 Tambah Role ke Users

Buat file `database/migrations/2024_01_01_000004_add_role_to_users_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('HR')->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
```

---

## 5. Membuat Eloquent Model

### 5.1 Update User Model

Edit `app/Models/User.php`, tambahkan `'role'` ke array `$fillable`:

```php
protected $fillable = [
    'name',
    'email',
    'password',
    'role',
];
```

### 5.2 Model Jabatan

Buat file `app/Models/Jabatan.php`:

```php
<?php

namespace App\Models;

use App\Enums\JenisJabatan;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $fillable = ['nama_jabatan', 'jenis_jabatan', 'bup', 'eselon_level', 'kelas_jabatan'];

    protected function casts(): array
    {
        return ['jenis_jabatan' => JenisJabatan::class];
    }

    public function riwayatJabatan()
    {
        return $this->hasMany(RiwayatJabatan::class);
    }
}
```

### 5.3 Model Pegawai (Model Utama)

Buat file `app/Models/Pegawai.php`:

```php
<?php

namespace App\Models;

use App\Enums\Agama;
use App\Enums\GolonganDarah;
use App\Enums\JenisKelamin;
use App\Enums\StatusPernikahan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pegawai extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nip', 'nama_lengkap', 'tempat_lahir', 'tanggal_lahir',
        'jenis_kelamin', 'alamat', 'no_telepon', 'email',
        'tmt_cpns', 'tmt_pns', 'gaji_pokok', 'is_active',
        'agama', 'status_pernikahan', 'golongan_darah',
        'npwp', 'no_karpeg', 'no_taspen', 'unit_kerja',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'tmt_cpns' => 'date',
            'tmt_pns' => 'date',
            'gaji_pokok' => 'decimal:2',
            'is_active' => 'boolean',
            'jenis_kelamin' => JenisKelamin::class,
            'agama' => Agama::class,
            'status_pernikahan' => StatusPernikahan::class,
            'golongan_darah' => GolonganDarah::class,
        ];
    }

    // ===== Relationships =====
    public function riwayatPangkat() { return $this->hasMany(RiwayatPangkat::class); }
    public function riwayatJabatan() { return $this->hasMany(RiwayatJabatan::class); }
    public function riwayatKgb() { return $this->hasMany(RiwayatKgb::class); }
    public function riwayatHukumanDisiplin() { return $this->hasMany(RiwayatHukumanDisiplin::class); }
    public function riwayatPendidikan() { return $this->hasMany(RiwayatPendidikan::class); }
    public function riwayatLatihanJabatan() { return $this->hasMany(RiwayatLatihanJabatan::class); }
    public function riwayatPenghargaan() { return $this->hasMany(RiwayatPenghargaan::class); }
    public function penilaianKinerja() { return $this->hasMany(PenilaianKinerja::class); }

    // ===== Computed Attributes =====
    public function getMasaKerjaAttribute(): string
    {
        $today = today();
        $total = (($today->year - $this->tmt_cpns->year) * 12) + $today->month - $this->tmt_cpns->month;
        return intdiv($total, 12) . ' Tahun ' . ($total % 12) . ' Bulan';
    }

    public function getPangkatTerakhirAttribute(): ?string
    {
        $last = $this->riwayatPangkat->sortByDesc('tmt_pangkat')->first();
        return $last?->golongan_ruang?->label();
    }

    public function getJabatanTerakhirAttribute(): ?string
    {
        $last = $this->riwayatJabatan->sortByDesc('tmt_jabatan')->first();
        return $last?->jabatan?->nama_jabatan;
    }
}
```

### 5.4 Model Riwayat (8 Model)

Buat setiap model riwayat. Berikut contoh pola yang sama untuk semuanya:

**`app/Models/RiwayatPangkat.php`**:

```php
<?php

namespace App\Models;

use App\Enums\GolonganRuang;
use Illuminate\Database\Eloquent\Model;

class RiwayatPangkat extends Model
{
    protected $fillable = ['pegawai_id', 'golongan_ruang', 'nomor_sk', 'tmt_pangkat', 'tanggal_sk', 'file_pdf_path', 'google_drive_link'];

    protected function casts(): array
    {
        return [
            'golongan_ruang' => GolonganRuang::class,
            'tmt_pangkat' => 'date',
            'tanggal_sk' => 'date',
        ];
    }

    public function pegawai() { return $this->belongsTo(Pegawai::class); }
}
```

> **Buat model serupa untuk:** `RiwayatJabatan`, `RiwayatKgb`, `RiwayatHukumanDisiplin`, `RiwayatPendidikan`, `RiwayatLatihanJabatan`, `RiwayatPenghargaan`, `PenilaianKinerja`.
>
> Setiap model harus memiliki:
> - `$fillable` sesuai kolom di migration
> - `casts()` untuk date dan enum
> - `belongsTo(Pegawai::class)` relationship
> - `RiwayatJabatan` juga punya `belongsTo(Jabatan::class)`

---

## 6. Membuat Database Seeder

### 6.1 UserSeeder

Buat file `database/seeders/UserSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (User::count() > 0) return;

        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@simpeg.go.id',
            'password' => Hash::make('password'),
            'role' => 'SuperAdmin',
        ]);

        User::create([
            'name' => 'HR Staff',
            'email' => 'hr@simpeg.go.id',
            'password' => Hash::make('password'),
            'role' => 'HR',
        ]);
    }
}
```

### 6.2 MasterDataSeeder

Buat file `database/seeders/MasterDataSeeder.php` yang berisi daftar jabatan beserta jenis, BUP, eselon, dan kelas jabatan. Isi dengan data jabatan ASN standar (25+ record).

### 6.3 PegawaiSeeder

Buat file `database/seeders/PegawaiSeeder.php` untuk generate ~100 pegawai dengan data random menggunakan Faker (`id_ID` locale). Seeder ini harus membuat:
- Data pegawai (NIP, nama, biodata)
- Riwayat pangkat (progresi otomatis setiap 4 tahun)
- Riwayat jabatan
- Riwayat KGB
- Riwayat pendidikan & latihan
- Penilaian kinerja (SKP)
- Sebagian pegawai mendapat riwayat hukuman disiplin

> **Tip:** Bagi pegawai ke 5 grup: (1) mendekati pensiun, (2) KGB akan datang, (3) eligible Satyalencana, (4) punya hukuman disiplin, (5) reguler. Ini memastikan semua fitur report terisi data.

### 6.4 Update DatabaseSeeder

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            MasterDataSeeder::class,
            PegawaiSeeder::class,
        ]);
    }
}
```

---

## 7. Membuat Service Layer (Business Logic)

Buat folder `app/Services/` dan buat 7 service class:

### 7.1 PegawaiService

```php
// app/Services/PegawaiService.php
// Berisi: getAll(), getById(), search(), create(), update(), delete()
// - Eager loading relationships
// - Search by NIP, nama, unit kerja
// - Soft delete (set is_active = false, lalu delete)
```

### 7.2 KGBService

```php
// app/Services/KGBService.php
// Business logic:
// - KGB jatuh tempo setiap 2 tahun dari TMT KGB terakhir
// - Status: 'Eligible' (jatuh tempo ≤ 0 hari), 'H-60' (≤ 60 hari), 'Mendekati' (lainnya)
// Berisi: getAllKGBStatus(), getUpcomingKGB(), getEligiblePegawai()
```

### 7.3 PensiunService

```php
// app/Services/PensiunService.php
// Business logic:
// - Tanggal pensiun = tanggal lahir + BUP (dari jabatan terakhir)
// - Alert level: Hitam (≤0 bulan), Merah (≤6), Kuning (≤12), Hijau (≤24)
// Berisi: getPensiunAlerts()
```

### 7.4 KenaikanPangkatService

```php
// app/Services/KenaikanPangkatService.php
// Business logic (syarat kenaikan pangkat):
// 1. Masa kerja golongan ≥ 48 bulan (4 tahun)
// 2. SKP 2 tahun terakhir minimal "Baik"
// 3. Memiliki riwayat latihan jabatan
// 4. Tidak sedang menjalani hukuman disiplin aktif
// Berisi: getEligiblePegawai()
```

### 7.5 SatyalencanaService

```php
// app/Services/SatyalencanaService.php
// Business logic:
// - Milestone: 10, 20, 30 tahun masa kerja
// - Diskualifikasi: punya hukuman Sedang/Berat
// - Cek belum pernah menerima penghargaan milestone yang sama
// Berisi: getEligibleCandidates(), getCandidatesByMilestone()
```

### 7.6 DUKService

```php
// app/Services/DUKService.php
// Hierarki ranking DUK (sesuai BKN):
// 1. Golongan ruang tertinggi
// 2. Jabatan
// 3. Masa kerja terlama
// 4. Latihan jabatan terbaru, total jam terbanyak
// 5. Pendidikan tertinggi
// 6. Usia (tanggal lahir terlebih dulu)
// Berisi: getDUK()
```

### 7.7 DashboardService

```php
// app/Services/DashboardService.php
// Menggabungkan data dari KGB, Pensiun, Satyalencana service
// plus chart data (distribusi golongan, gender, usia, unit kerja)
// Berisi: getDashboardData()
```

---

## 8. Membuat Controller

Buat 9 controller di `app/Http/Controllers/`:

| Controller | Fungsi |
|---|---|
| `AuthController` | `showLogin()`, `login()`, `logout()` |
| `DashboardController` | `index()` — inject `DashboardService` |
| `PegawaiController` | CRUD + `getPaginated()` untuk API pagination |
| `RiwayatController` | CRUD untuk 7 jenis riwayat (Pangkat, Jabatan, KGB, Hukuman, Pendidikan, Latihan, SKP) |
| `KGBController` | `index()`, `upcoming()`, `eligible()` |
| `KenaikanPangkatController` | `index()`, `eligible()` |
| `PensiunController` | `index()` |
| `DUKController` | `index()` |
| `SatyalencanaController` | `index()` dengan filter milestone |

**Pola umum:**
- Inject service via constructor dependency injection
- Validasi input di `store()` dan `update()`
- Return redirect dengan flash message setelah create/update/delete
- Pass enum `::cases()` ke view untuk form select options

---

## 9. Mendefinisikan Routes

Edit `routes/web.php`:

```php
// Auth (tanpa middleware)
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Semua route lain di dalam middleware('auth')
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', ...)->name('dashboard');

    // Pegawai CRUD (resource route)
    Route::resource('pegawai', PegawaiController::class);
    Route::get('/pegawai-data', ...)->name('pegawai.data');

    // Riwayat CRUD (manual routes per tipe)
    // Pattern: GET create/{pegawaiId}, POST store, GET edit/{model}, PUT update/{model}, DELETE destroy/{model}
    // Untuk: pangkat, jabatan, kgb, hukuman, pendidikan, latihan, skp

    // Report pages
    Route::get('/kgb', ...)->name('kgb.index');
    Route::get('/kgb/upcoming', ...)->name('kgb.upcoming');
    Route::get('/kgb/eligible', ...)->name('kgb.eligible');
    Route::get('/kenaikan-pangkat', ...)->name('kenaikan-pangkat.index');
    Route::get('/pensiun', ...)->name('pensiun.index');
    Route::get('/duk', ...)->name('duk.index');
    Route::get('/satyalencana', ...)->name('satyalencana.index');
});
```

---

## 10. Membuat Views (Blade + Tailwind)

### 10.1 Struktur View

```
resources/views/
├── layouts/
│   └── app.blade.php          # Layout utama (sidebar + content area)
├── auth/
│   └── login.blade.php        # Halaman login (standalone, tanpa layout)
├── dashboard/
│   └── index.blade.php        # Dashboard + Chart.js
├── pegawai/
│   ├── index.blade.php        # Tabel + pagination + search (AJAX)
│   ├── show.blade.php         # Detail + 7 tab riwayat
│   ├── create.blade.php       # Form tambah
│   ├── edit.blade.php         # Form edit
│   └── _form.blade.php        # Partial form (shared create/edit)
├── riwayat/
│   ├── create-pangkat.blade.php
│   ├── edit-pangkat.blade.php
│   ├── create-jabatan.blade.php
│   ├── edit-jabatan.blade.php
│   ├── create-kgb.blade.php
│   ├── edit-kgb.blade.php
│   ├── create-hukuman.blade.php
│   ├── edit-hukuman.blade.php
│   ├── create-pendidikan.blade.php
│   ├── edit-pendidikan.blade.php
│   ├── create-latihan.blade.php
│   ├── edit-latihan.blade.php
│   ├── create-skp.blade.php
│   └── edit-skp.blade.php
├── kgb/
│   └── index.blade.php
├── kenaikan-pangkat/
│   └── index.blade.php
├── pensiun/
│   └── index.blade.php
├── duk/
│   └── index.blade.php
└── satyalencana/
    └── index.blade.php
```

### 10.2 Layout Utama (`layouts/app.blade.php`)

Komponen utama:
- **Sidebar** (fixed, 256px) — gradient gelap, navigation links dengan icon SVG, active state highlight, user info + logout
- **Main Content** — sticky header dengan backdrop blur, flash messages, `@yield('content')`
- Gunakan `@stack('scripts')` dan `@stack('styles')` untuk per-halaman JS/CSS

### 10.3 Tips Design Tailwind

```
Warna utama: slate-900 (sidebar), blue-600 (accent), slate-50 (background)
Border radius: rounded-2xl (kartu), rounded-xl (input), rounded-lg (button)
Shadow: shadow-sm (kartu), shadow-2xl (sidebar)
Font: Inter via Google Fonts
Glassmorphism: bg-white/80 backdrop-blur-lg (header)
Badge/pill: px-2 py-1 text-xs rounded-full
```

### 10.4 Dashboard Charts

Gunakan Chart.js 4 via CDN:
```html
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
```

Pass data chart dari controller sebagai JSON ke JavaScript, lalu render 4 canvas:
- Bar chart: Distribusi Golongan, Distribusi Usia
- Doughnut chart: Gender, Unit Kerja

### 10.5 Pegawai Index — Server-Side Pagination

Gunakan fetch API ke endpoint `/pegawai-data?page=X&limit=10&search=keyword` dan render tabel secara dinamis dengan JavaScript.

### 10.6 Pegawai Show — Tab System

Gunakan JavaScript sederhana untuk show/hide tab content:
```js
function showTab(name) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(el => { /* toggle classes */ });
    document.getElementById('tab-' + name).classList.remove('hidden');
}
```

### 10.7 Tombol Aksi dalam Tabel

**Penting:** Jangan gunakan `flex` langsung pada `<td>`. Bungkus dengan `<div>`:

```html
<td class="px-3 py-2">
    <div class="flex items-center gap-2">
        <a href="..." class="inline-flex items-center px-2 py-1 bg-blue-50 text-blue-600 text-xs rounded-md">Edit</a>
        <form method="POST" action="..." class="inline">
            @csrf @method('DELETE')
            <button type="submit" class="inline-flex items-center px-2 py-1 bg-red-50 text-red-600 text-xs rounded-md">Hapus</button>
        </form>
    </div>
</td>
```

---

## 11. Build & Menjalankan Aplikasi

### Build Assets

```bash
npm run build
```

### Jalankan Migrasi & Seed

```bash
php artisan migrate:fresh --seed
```

### Jalankan Dev Server

```bash
# Untuk development (dengan hot-reload):
npm run dev          # Terminal 1
php artisan serve    # Terminal 2

# Untuk production:
npm run build
php artisan serve
```

### Akses Aplikasi

Buka browser ke **http://localhost:8000** dan login dengan:
- Email: `admin@simpeg.go.id`
- Password: `password`

---

## Ringkasan Arsitektur

```
Request → Route → Controller → Service → Model → Database
                                   ↓
                             View (Blade)
                                   ↓
                           Response (HTML)
```

| Layer | Tanggung Jawab |
|---|---|
| **Route** | URL mapping ke controller |
| **Controller** | Validasi input, orkestrasi, return view |
| **Service** | Business logic murni (perhitungan KGB, pensiun, dll) |
| **Model** | Data access, relationships, computed attributes |
| **View** | Presentasi HTML dengan Tailwind CSS |
| **Enum** | Tipe data konstan dengan label display |
| **Seeder** | Data awal untuk testing |

---

## Checklist Pengerjaan

- [ ] Buat project Laravel baru
- [ ] Konfigurasi SQLite & Tailwind
- [ ] Buat 7 Enum
- [ ] Buat 4 Migration (11 tabel)
- [ ] Buat 10 Eloquent Model dengan relationships
- [ ] Buat 3 Seeder (User, MasterData, Pegawai)
- [ ] Buat 7 Service class
- [ ] Buat 9 Controller
- [ ] Definisikan 59 Routes
- [ ] Buat 1 Layout + 24 Blade Views
- [ ] Build + Test

**Estimasi waktu pengerjaan:** 8-12 jam untuk programmer yang sudah familiar dengan Laravel.
