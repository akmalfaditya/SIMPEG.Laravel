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
7. [Membuat FormRequest (Validasi)](#7-membuat-formrequest-validasi)
8. [Membuat DTO (Data Transfer Object)](#8-membuat-dto-data-transfer-object)
9. [Membuat Service Layer (Business Logic)](#9-membuat-service-layer-business-logic)
10. [Membuat API Resource](#10-membuat-api-resource)
11. [Membuat Controller](#11-membuat-controller)
12. [Mendefinisikan Routes](#12-mendefinisikan-routes)
13. [Membuat Views (Blade + Tailwind)](#13-membuat-views-blade--tailwind)
14. [Menambahkan Export PDF & Excel](#14-menambahkan-export-pdf--excel)
15. [Menambahkan Activity Log](#15-menambahkan-activity-log)
16. [Build & Menjalankan Aplikasi](#16-build--menjalankan-aplikasi)

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
import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
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
    --font-sans: "Inter", ui-sans-serif, system-ui, sans-serif;
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

SIMPEG memiliki 10 PHP Enum yang merepresentasikan data master. Buat folder `app/Enums/` dan buat file-file berikut:

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
    case LakiLaki = 1;
    case Perempuan = 2;

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
    case Islam = 1;
    case Kristen = 2;
    case Katolik = 3;
    case Hindu = 4;
    case Budha = 5;
    case Konghucu = 6;

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
    case A = 1;
    case B = 2;
    case AB = 3;
    case O = 4;

    public function label(): string
    {
        return $this->name;
    }
}
```

### ~~3.4 GolonganRuang~~ → Digantikan oleh Tabel `golongan_pangkats`

> **DEPRECATED:** Enum `GolonganRuang` telah **dihapus** dan digantikan oleh tabel database `golongan_pangkats` (model `GolonganPangkat`). Lihat [Section 4.7](#47-tabel-golongan-pangkats) untuk migration dan [Section 5.6](#56-model-golonganpangkat) untuk model. Semua referensi `golongan_ruang` (tinyInteger) di tabel lain telah diganti menjadi `golongan_id` (foreignId) yang merujuk ke `golongan_pangkats.id`.

### 3.5 JenisJabatan

Buat file `app/Enums/JenisJabatan.php`:

```php
<?php

namespace App\Enums;

enum JenisJabatan: int
{
    case PejabatAdministrasi = 1;
    case FungsionalAhliPertama = 2;
    case FungsionalAhliMuda = 3;
    case PejabatPimpinanTinggi = 4;
    case FungsionalMadya = 5;
    case FungsionalUtama = 6;

    public function label(): string
    {
        return match ($this) {
            self::PejabatAdministrasi => 'Pejabat Administrasi',
            self::FungsionalAhliPertama => 'Fungsional Ahli Pertama',
            self::FungsionalAhliMuda => 'Fungsional Ahli Muda',
            self::PejabatPimpinanTinggi => 'Pejabat Pimpinan Tinggi',
            self::FungsionalMadya => 'Fungsional Madya',
            self::FungsionalUtama => 'Fungsional Utama',
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
    case BelumMenikah = 1;
    case Menikah = 2;
    case CeraiHidup = 3;
    case CeraiMati = 4;

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
    case Ringan = 1;
    case Sedang = 2;
    case Berat = 3;

    public function label(): string
    {
        return $this->name;
    }
}
```

### 3.8 JenisSanksi (PP 94/2021)

Buat file `app/Enums/JenisSanksi.php`:

```php
<?php

namespace App\Enums;

enum JenisSanksi: int
{
    case PenundaanKgb = 1;
    case PenundaanPangkat = 2;
    case PenurunanPangkat = 3;
    case PenurunanJabatan = 4;
    case PembebasanJabatan = 5;
    case Pemberhentian = 6;

    public function label(): string
    {
        return match ($this) {
            self::PenundaanKgb => 'Penundaan KGB',
            self::PenundaanPangkat => 'Penundaan Pangkat',
            self::PenurunanPangkat => 'Penurunan Pangkat',
            self::PenurunanJabatan => 'Penurunan Jabatan',
            self::PembebasanJabatan => 'Pembebasan Jabatan',
            self::Pemberhentian => 'Pemberhentian',
        };
    }
}
```

### 3.9 StatusHukdis

Buat file `app/Enums/StatusHukdis.php`:

```php
<?php

namespace App\Enums;

enum StatusHukdis: string
{
    case Aktif = 'aktif';
    case Selesai = 'selesai';
    case Dipulihkan = 'dipulihkan';

    public function label(): string
    {
        return match ($this) {
            self::Aktif => 'Aktif',
            self::Selesai => 'Selesai',
            self::Dipulihkan => 'Dipulihkan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Aktif => 'bg-red-100 text-red-700',
            self::Selesai => 'bg-slate-100 text-slate-700',
            self::Dipulihkan => 'bg-green-100 text-green-700',
        };
    }
}
```

### 3.10 RumpunJabatan

Buat file `app/Enums/RumpunJabatan.php`:

```php
<?php

namespace App\Enums;

enum RumpunJabatan: int
{
    case Imigrasi = 1;
    case Pemasyarakatan = 2;
    case Struktural = 3;

    public function label(): string
    {
        return match ($this) {
            self::Imigrasi => 'Imigrasi',
            self::Pemasyarakatan => 'Pemasyarakatan',
            self::Struktural => 'Struktural',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Imigrasi => 'blue',
            self::Pemasyarakatan => 'amber',
            self::Struktural => 'slate',
        };
    }
}
```

---

## 4. Membuat Database Migration

Kita akan membuat 12 file migrasi custom (+ 3 migrasi default Laravel). Gunakan penamaan dengan prefix tanggal agar urutan eksekusi benar.

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
            $table->tinyInteger('jenis_jabatan');      // JenisJabatan enum
            $table->integer('bup')->default(58);       // Batas Usia Pensiun
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
            $table->string('foto_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('gaji_pokok', 15, 2)->default(0);
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
            $table->foreignId('golongan_id')->constrained('golongan_pangkats')->cascadeOnDelete();
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
            $table->tinyInteger('tingkat_hukuman');    // TingkatHukuman enum
            $table->tinyInteger('jenis_sanksi');       // JenisSanksi enum
            $table->integer('durasi_tahun')->nullable();
            $table->string('nomor_sk')->nullable();
            $table->date('tanggal_sk')->nullable();
            $table->date('tmt_hukuman');
            $table->date('tmt_selesai_hukuman')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('file_pdf_path')->nullable();
            $table->string('google_drive_link')->nullable();
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
            $table->integer('tahun');
            $table->integer('milestone')->default(0);
            $table->string('nomor_sk')->nullable();
            $table->date('tanggal_sk')->nullable();
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
            $table->string('file_pdf_path')->nullable();
            $table->string('google_drive_link')->nullable();
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

### 4.5 Tabel Gaji PP 15/2019

Buat file `database/migrations/2024_01_01_000005_create_tabel_gajis_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tabel_gajis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('golongan_id')->constrained('golongan_pangkats')->cascadeOnDelete();
            $table->integer('masa_kerja_tahun');       // 0, 2, 4, 6, ...
            $table->decimal('gaji_pokok', 15, 2);
            $table->timestamps();

            $table->unique(['golongan_id', 'masa_kerja_tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tabel_gajis');
    }
};
```

> **Catatan:** Tabel ini digunakan oleh `KGBCalculationService` untuk lookup gaji baru berdasarkan golongan dan masa kerja saat membuat riwayat KGB. Data di-seed melalui `TabelGajiSeeder`.

### 4.6 Tambah Rumpun ke Jabatans

Buat file `database/migrations/2024_01_01_000011_add_rumpun_to_jabatans.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jabatans', function (Blueprint $table) {
            $table->tinyInteger('rumpun')->default(3)->after('kelas_jabatan'); // RumpunJabatan enum
        });
    }

    public function down(): void
    {
        Schema::table('jabatans', function (Blueprint $table) {
            $table->dropColumn('rumpun');
        });
    }
};
```

### 4.7 Tambah Status & Pemulihan ke Hukuman Disiplin

Buat file `database/migrations/2024_01_01_000012_add_hukdis_status_and_pemulihan_fields.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('riwayat_hukuman_disiplins', function (Blueprint $table) {
            $table->string('status')->default('aktif')->after('google_drive_link');
            $table->string('nomor_sk_pemulihan')->nullable()->after('status');
            $table->date('tanggal_pemulihan')->nullable()->after('nomor_sk_pemulihan');
            $table->string('file_sk_pemulihan_path')->nullable()->after('tanggal_pemulihan');
        });
    }

    public function down(): void
    {
        Schema::table('riwayat_hukuman_disiplins', function (Blueprint $table) {
            $table->dropColumn(['status', 'nomor_sk_pemulihan', 'tanggal_pemulihan', 'file_sk_pemulihan_path']);
        });
    }
};
```

### 4.8 Tambah Flag Demotion ke Riwayat Pangkat & Jabatan

Buat file `database/migrations/2024_01_01_000013_add_is_hukdis_demotion_to_riwayat_tables.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('riwayat_pangkats', function (Blueprint $table) {
            $table->boolean('is_hukdis_demotion')->default(false)->after('google_drive_link');
        });

        Schema::table('riwayat_jabatans', function (Blueprint $table) {
            $table->boolean('is_hukdis_demotion')->default(false)->after('google_drive_link');
        });
    }

    public function down(): void
    {
        Schema::table('riwayat_pangkats', function (Blueprint $table) {
            $table->dropColumn('is_hukdis_demotion');
        });
        Schema::table('riwayat_jabatans', function (Blueprint $table) {
            $table->dropColumn('is_hukdis_demotion');
        });
    }
};
```

> **Catatan:** Flag `is_hukdis_demotion` digunakan oleh `RiwayatService` untuk menandai record pangkat/jabatan yang dibuat karena hukuman disiplin (Type 2: Penurunan Pangkat/Jabatan/Pembebasan). Saat pemulihan atau penghapusan hukuman, record dengan flag ini akan dihapus dan digantikan record pemulihan.

### 4.9 Migrasi Activity Log (Spatie)

```bash
composer require spatie/laravel-activitylog
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
php artisan migrate
```

Ini akan membuat 3 migrasi otomatis:

- `create_activity_log_table` — Tabel utama activity log
- `add_event_column_to_activity_log_table` — Kolom event
- `add_batch_uuid_column_to_activity_log_table` — Kolom batch UUID

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
use App\Enums\RumpunJabatan;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $fillable = ['nama_jabatan', 'jenis_jabatan', 'bup', 'eselon_level', 'kelas_jabatan', 'rumpun'];

    protected function casts(): array
    {
        return [
            'jenis_jabatan' => JenisJabatan::class,
            'rumpun' => RumpunJabatan::class,
        ];
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
        'tmt_cpns', 'tmt_pns', 'foto_path', 'is_active', 'gaji_pokok',
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

use Illuminate\Database\Eloquent\Model;

class RiwayatPangkat extends Model
{
    protected $fillable = ['pegawai_id', 'golongan_id', 'nomor_sk', 'tmt_pangkat', 'tanggal_sk', 'file_pdf_path', 'google_drive_link', 'is_hukdis_demotion'];

    protected function casts(): array
    {
        return [
            'tmt_pangkat' => 'date',
            'tanggal_sk' => 'date',
            'is_hukdis_demotion' => 'boolean',
        ];
    }

    public function pegawai() { return $this->belongsTo(Pegawai::class); }
    public function golongan() { return $this->belongsTo(GolonganPangkat::class, 'golongan_id'); }
}
```

> **Buat model serupa untuk:** `RiwayatJabatan`, `RiwayatKgb`, `RiwayatHukumanDisiplin`, `RiwayatPendidikan`, `RiwayatLatihanJabatan`, `RiwayatPenghargaan`, `PenilaianKinerja`.
>
> Setiap model harus memiliki:
>
> - `$fillable` sesuai kolom di migration
> - `casts()` untuk date dan enum
> - `belongsTo(Pegawai::class)` relationship
> - `RiwayatJabatan` juga punya `belongsTo(Jabatan::class)` dan cast `is_hukdis_demotion` (boolean)
> - `RiwayatHukumanDisiplin` harus cast `jenis_sanksi` → `JenisSanksi`, `status` → `StatusHukdis`, dan memiliki method:
>     - `isAktif(): bool` — `status === StatusHukdis::Aktif && (tmt_selesai_hukuman === null || tmt_selesai_hukuman >= today())`
>     - `isType2(): bool` — `jenis_sanksi` adalah PenurunanPangkat/PenurunanJabatan/PembebasanJabatan
> - Semua model riwayat + Pegawai menggunakan trait `Spatie\Activitylog\Traits\LogsActivity`

### 5.5 Model TabelGaji

Buat file `app/Models/TabelGaji.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TabelGaji extends Model
{
    protected $fillable = ['golongan_id', 'masa_kerja_tahun', 'gaji_pokok'];

    protected function casts(): array
    {
        return [
            'gaji_pokok' => 'decimal:2',
        ];
    }

    public function golongan() { return $this->belongsTo(GolonganPangkat::class, 'golongan_id'); }
}
```

> Model ini digunakan oleh `KGBCalculationService` untuk lookup gaji berdasarkan golongan dan masa kerja.

### 5.6 Model GolonganPangkat

Buat file `app/Models/GolonganPangkat.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GolonganPangkat extends Model
{
    protected $fillable = ['golongan_ruang', 'label', 'pangkat', 'golongan_group', 'min_pendidikan', 'keterangan', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function riwayatPangkat() { return $this->hasMany(RiwayatPangkat::class, 'golongan_id'); }
    public function tabelGaji() { return $this->hasMany(TabelGaji::class, 'golongan_id'); }
}
```

> Model master data golongan/pangkat. Menggantikan Enum `GolonganRuang` agar data bisa dikelola secara dinamis melalui CRUD.

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
            'name' => 'Super Admin Kemenipas',
            'email' => 'superadmin@kemenipas.go.id',
            'password' => Hash::make('password'),
            'role' => 'SuperAdmin',
        ]);

        User::create([
            'name' => 'Staf Kepegawaian Kemenipas',
            'email' => 'hr@kemenipas.go.id',
            'password' => Hash::make('password'),
            'role' => 'HR',
        ]);
    }
}
```

### 6.2 MasterDataSeeder

Buat file `database/seeders/MasterDataSeeder.php` yang berisi daftar jabatan beserta jenis, BUP, eselon, kelas jabatan, dan **rumpun jabatan** (`RumpunJabatan` enum). Isi dengan data jabatan ASN Kemenipas dari 3 rumpun:

- **Struktural** (25 jabatan): Pejabat Administrasi, Fungsional Ahli Pertama/Muda/Madya/Utama, Pejabat Pimpinan Tinggi
- **Imigrasi** (6 jabatan): Pemeriksa Keimigrasian, Analis Keimigrasian, Teknisi Keimigrasian, Kepala Kantor Imigrasi
- **Pemasyarakatan** (6 jabatan): Pembimbing Kemasyarakatan, Penjaga Tahanan, Pengamat Pemasyarakatan, Kepala Lembaga Pemasyarakatan

Total: **37 jabatan** master data.

### 6.3 PegawaiSeeder

Buat file `database/seeders/PegawaiSeeder.php` untuk generate ~100 pegawai dengan data random menggunakan Faker (`id_ID` locale). Seeder ini harus membuat:

- Data pegawai (NIP, nama, biodata, gelar depan/belakang, bagian, tipe pegawai, status kepegawaian)
- Riwayat pangkat (progresi otomatis setiap 4 tahun)
- Riwayat jabatan
- Riwayat KGB
- Riwayat pendidikan & latihan
- Penilaian kinerja (SKP)
- Sebagian pegawai mendapat riwayat hukuman disiplin (durasi_tahun=1 untuk Sedang/Berat per PP 94/2021)

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
            TabelGajiSeeder::class,
            PegawaiSeeder::class,
        ]);
    }
}
```

> **TabelGajiSeeder** berisi data tabel gaji PNS berdasarkan PP 15/2019 yang digunakan oleh `KGBCalculationService` untuk menghitung gaji baru secara otomatis saat pembuatan riwayat KGB. Seeder ini membuat record untuk setiap kombinasi golongan dan masa kerja.

---

## 7. Membuat FormRequest (Validasi)

> **Prinsip:** Controller **tidak boleh** mengandung rule validasi secara langsung (`$request->validate([...])`). Semua validasi dipindahkan ke FormRequest class.

Buat folder `app/Http/Requests/` beserta sub-folder `Riwayat/` dan `Auth/`.

### 7.1 Pegawai FormRequest

**`app/Http/Requests/StorePegawaiRequest.php`**:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePegawaiRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nip' => 'required|string|unique:pegawais,nip',
            'nama_lengkap' => 'required|string|max:255',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|integer',
            // ... field lainnya
        ];
    }
}
```

**`app/Http/Requests/UpdatePegawaiRequest.php`** — sama tetapi NIP unique rule meng-exclude record saat ini:

```php
'nip' => 'required|string|unique:pegawais,nip,' . $this->route('pegawai')->id,
```

### 7.2 Riwayat FormRequest

Buat Store/Update pair untuk setiap tipe riwayat di `app/Http/Requests/Riwayat/`:

| File                                                 | Untuk                    |
| ---------------------------------------------------- | ------------------------ |
| `StorePangkatRequest` / `UpdatePangkatRequest`       | Riwayat Pangkat          |
| `StoreJabatanRequest` / `UpdateJabatanRequest`       | Riwayat Jabatan          |
| `StoreKGBRequest` / `UpdateKGBRequest`               | Riwayat KGB              |
| `StoreHukumanRequest` / `UpdateHukumanRequest`       | Riwayat Hukuman Disiplin |
| `StorePendidikanRequest` / `UpdatePendidikanRequest` | Riwayat Pendidikan       |
| `StoreLatihanRequest` / `UpdateLatihanRequest`       | Riwayat Latihan Jabatan  |
| `StoreSKPRequest` / `UpdateSKPRequest`               | Penilaian Kinerja (SKP)  |

> **Pola:** Store request memiliki `'pegawai_id' => 'required|exists:pegawais,id'`, Update request tidak karena pegawai_id sudah ada di model.

### 7.3 Auth FormRequest

**`app/Http/Requests/Auth/LoginRequest.php`**:

```php
public function rules(): array
{
    return [
        'email' => 'required|email',
        'password' => 'required',
    ];
}
```

---

## 8. Membuat DTO (Data Transfer Object)

> **Prinsip:** Jangan pass associative array (`$validated`) dari Controller ke Service. Gunakan class DTO yang strongly-typed.

Buat folder `app/DTOs/` dan `app/DTOs/Riwayat/`.

### 8.1 PegawaiDTO

**`app/DTOs/PegawaiDTO.php`**:

```php
<?php

namespace App\DTOs;

class PegawaiDTO
{
    public function __construct(
        public readonly string $nip,
        public readonly string $namaLengkap,
        public readonly ?string $tempatLahir,
        public readonly string $tanggalLahir,
        public readonly int $jenisKelamin,
        // ... field lainnya
        public readonly bool $isActive = true,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            nip: $validated['nip'],
            namaLengkap: $validated['nama_lengkap'],
            tempatLahir: $validated['tempat_lahir'] ?? null,
            tanggalLahir: $validated['tanggal_lahir'],
            jenisKelamin: (int) $validated['jenis_kelamin'],
            // ...
        );
    }

    public function toArray(): array
    {
        return [
            'nip' => $this->nip,
            'nama_lengkap' => $this->namaLengkap,
            // ...
        ];
    }
}
```

### 8.2 Riwayat DTOs

Buat DTO untuk setiap tipe riwayat di `app/DTOs/Riwayat/`:

| DTO Class                   | Untuk                    |
| --------------------------- | ------------------------ |
| `RiwayatPangkatDTO`         | Riwayat Pangkat          |
| `RiwayatJabatanDTO`         | Riwayat Jabatan          |
| `RiwayatKgbDTO`             | Riwayat KGB              |
| `RiwayatHukumanDisiplinDTO` | Riwayat Hukuman Disiplin |
| `RiwayatPendidikanDTO`      | Riwayat Pendidikan       |
| `RiwayatLatihanJabatanDTO`  | Riwayat Latihan Jabatan  |
| `PenilaianKinerjaDTO`       | Penilaian Kinerja (SKP)  |

Setiap DTO memiliki:

- Constructor dengan `readonly` typed properties
- `fromRequest(array $validated): self` — factory method
- `toArray(): array` — convert kembali ke snake_case untuk Eloquent

---

## 9. Membuat Service Layer (Business Logic)

Buat folder `app/Services/` dan buat 12 service class:

### 9.1 PegawaiService

```php
// app/Services/PegawaiService.php
// Menerima PegawaiDTO, bukan array
// Semua operasi dibungkus DB::transaction()
// Berisi: getAll(), getById(), search(), create(PegawaiDTO), update(Pegawai, PegawaiDTO), delete(Pegawai)
```

### 9.2 RiwayatService

```php
// app/Services/RiwayatService.php
// Menerima DTO per tipe riwayat
// Semua operasi dibungkus DB::transaction()
// storeKgb() juga mengupdate gaji_pokok Pegawai dalam satu transaksi
// Hukdis Hybrid Logic:
//   - Type 2 (Penurunan Pangkat/Jabatan/Pembebasan): insert record demotion
//     ke riwayat_pangkats/jabatans dengan is_hukdis_demotion=true
//   - Pemulihan (pulihkanHukuman): set status=Dipulihkan, insert record
//     pemulihan pangkat/jabatan, rekalkulasi gaji otomatis
//   - Delete hukuman: revert demotion records + rekalkulasi gaji
// Inject: KGBCalculationService (untuk rekalkulasi gaji pada demotion/pemulihan)
// Berisi: store/update/delete untuk setiap tipe riwayat + pulihkanHukuman()
```

### 9.3 JabatanService

```php
// app/Services/JabatanService.php
// CRUD master data jabatan dengan filter rumpun
// Berisi: getAll(), getAllOrderedByName(), create(), update(), delete()
```

### 9.4 TabelGajiService

```php
// app/Services/TabelGajiService.php
// CRUD tabel gaji PP 15/2019
// Mengelola data gaji per golongan dan masa kerja
// Berisi: getByGolongan(), store(), update(), delete()
```

### 9.5 KGBService

```php
// app/Services/KGBService.php
// Business logic:
// - KGB jatuh tempo setiap 2 tahun dari TMT KGB terakhir
// - Status: 'Eligible' (jatuh tempo ≤ 0 hari), 'H-60' (≤ 60 hari), 'Mendekati' (lainnya)
// - Integrasi hukdis: pegawai dengan hukuman aktif PenundaanKGB ($h->isAktif()) masuk tab 'Ditunda'
// Berisi: getAllKGBStatus(), getUpcomingKGB(), getEligiblePegawai(), getDitundaPegawai()
```

### 9.6 KGBCalculationService

```php
// app/Services/KGBCalculationService.php
// Menghitung gaji baru berdasarkan tabel gaji PP 15/2019
// Lookup dari model TabelGaji berdasarkan golongan dan masa kerja
// Berisi: getNextKGBSalary(Pegawai) → ['gaji_lama', 'gaji_baru', 'golongan', 'masa_kerja_tahun', 'masa_kerja_total_tahun']
```

### 9.7 PensiunService

```php
// app/Services/PensiunService.php
// Business logic:
// - Tanggal pensiun = tanggal lahir + BUP (dari jabatan terakhir)
// - Alert level: Hitam (≤0 bulan), Merah (≤6), Kuning (≤12), Hijau (≤24)
// Berisi: getPensiunAlerts()
```

### 9.8 KenaikanPangkatService

```php
// app/Services/KenaikanPangkatService.php
// Business logic (syarat kenaikan pangkat):
// 1. Masa kerja golongan ≥ 48 bulan (4 tahun)
// 2. SKP 2 tahun terakhir minimal "Baik"
// 3. Memiliki riwayat latihan jabatan
// 4. Tidak sedang menjalani hukuman disiplin aktif ($h->isAktif())
// Berisi: getEligiblePegawai(), getAll() + filter ditunda
```

### 9.9 SatyalencanaService

```php
// app/Services/SatyalencanaService.php
// Business logic:
// - Milestone: 10, 20, 30 tahun masa kerja
// - Diskualifikasi: punya hukuman Sedang/Berat
// - Cek belum pernah menerima penghargaan milestone yang sama
// Berisi: getEligibleCandidates(), getCandidatesByMilestone()
```

### 9.10 DUKService

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

### 9.11 DashboardService

```php
// app/Services/DashboardService.php
// Menggabungkan data dari KGB, Pensiun, Satyalencana service
// plus chart data (distribusi golongan, gender, usia, unit kerja)
// Berisi: getDashboardData()
```

### 9.12 DocumentUploadService

```php
// app/Services/DocumentUploadService.php
// Menangani upload dan manajemen file dokumen SK, ijazah, dll
// Berisi: upload($file, $subfolder, $fileName), delete($path), getUrl($path)
// upload() mendukung storeAs() untuk penamaan file bermakna (NIP_Module_Timestamp_NamaAsli.pdf)
```

---

## 10. Membuat API Resource

> **Prinsip:** Jangan return raw model atau array tanpa key dari controller. Gunakan API Resource untuk membentuk response yang eksplisit.

**`app/Http/Resources/PegawaiResource.php`**:

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PegawaiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nip' => $this->nip,
            'nama_lengkap' => $this->nama_lengkap,
            'pangkat_terakhir' => $this->pangkat_terakhir ?? '-',
            'jabatan_terakhir' => $this->jabatan_terakhir ?? '-',
            'masa_kerja' => $this->masa_kerja ?? '-',
        ];
    }
}
```

Digunakan di `PegawaiController@getPaginated` untuk menggantikan mapping array manual.

---

## 11. Membuat Controller

Buat 16 controller di `app/Http/Controllers/`:

| Controller                  | Fungsi                                                                                                                                                        |
| --------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `AuthController`            | `showLogin()`, `login(LoginRequest)`, `logout()`                                                                                                              |
| `DashboardController`       | `index()`, `exportPdf()` — inject `DashboardService`                                                                                                          |
| `PegawaiController`         | CRUD + `getPaginated()` + One-Stop Creation Flow — menggunakan `StorePegawaiRequest`, `UpdatePegawaiRequest`, `PegawaiDTO`, `PegawaiResource`. `create()` passes golongan & jabatan options, `store()` auto-creates riwayat via `PegawaiService` |
| `RiwayatController`         | CRUD untuk 7 jenis riwayat + `pulihkanHukuman()` — inject `RiwayatService`, `JabatanService`, `KGBCalculationService`, menggunakan FormRequest + DTO per tipe |
| `KGBController`             | `index()`, `upcoming()`, `eligible()`, `ditunda()`                                                                                                            |
| `KenaikanPangkatController` | `index()`, `eligible()`, `ditunda()`                                                                                                                          |
| `PensiunController`         | `index()`                                                                                                                                                     |
| `DUKController`             | `index()`                                                                                                                                                     |
| `SatyalencanaController`    | `index()` dengan filter milestone, `award()` untuk pencatatan penghargaan                                                                                     |
| `ExportController`          | `export($type, $format)` — export PDF (DomPDF) dan Excel (Maatwebsite) untuk 5 jenis laporan                                                                  |
| `DocumentController`        | `download($type, $id)` — inline preview file dokumen SK di browser (pangkat, jabatan, kgb, hukuman, pendidikan, latihan, skp)                                 |
| `JabatanController`         | CRUD master data jabatan (admin) — dengan filter rumpun                                                                                                       |
| `TabelGajiController`       | CRUD tabel gaji PP 15/2019 (admin) — per golongan dan masa kerja                                                                                              |
| `GolonganController`        | `index()` — referensi golongan ruang dengan statistik gaji                                                                                                    |
| `ActivityLogController`     | `index()` — menampilkan riwayat aktivitas dari Spatie Activity Log                                                                                            |
| `ProfileController`         | `show()`, `updatePassword()` — profil user dan ganti password                                                                                                 |

**Pola Controller (setelah refactor):**

```
Request masuk → FormRequest (validasi otomatis)
              → Controller (orkestrasi saja)
                  → DTO::fromRequest($request->validated())
                  → Service->method($dto)   // dibungkus DB::transaction()
                  → return redirect/view
```

**One-Stop Creation Flow (PegawaiController@store):**

```
StorePegawaiRequest → PegawaiDTO::fromRequest()
                    → PegawaiService->create($dto, golonganId, jabatanId)
                        // Inside DB::transaction:
                        // 1. Lookup gaji_pokok from TabelGaji
                        // 2. Create Pegawai
                        // 3. Auto-create RiwayatPangkat
                        // 4. Auto-create RiwayatJabatan
                    → redirect('pegawai.index')
```

**Aturan ketat:**

- Controller **TIDAK BOLEH** mengandung `$request->validate([...])`
- Controller **TIDAK BOLEH** memanggil Eloquent secara langsung (kecuali route model binding)
- Magic number (page default, limit) dipindah ke `private const`
- Inject service via constructor dependency injection
- Pass enum `::cases()` ke view untuk form select options

---

## 12. Mendefinisikan Routes

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
    Route::get('/dashboard/export-pdf', ...)->name('dashboard.export-pdf');

    // Pegawai CRUD (resource route)
    Route::resource('pegawai', PegawaiController::class);
    Route::get('/pegawai-data', ...)->name('pegawai.data');

    // Riwayat CRUD (manual routes per tipe)
    // Pattern: GET create/{pegawaiId}, POST store, GET edit/{model}, PUT update/{model}, DELETE destroy/{model}
    // Untuk: pangkat, jabatan, kgb, hukuman, pendidikan, latihan, skp
    // Khusus hukuman: POST /riwayat/hukuman/{id}/pulihkan (pemulihan hukdis)

    // Report pages
    Route::get('/kgb', ...)->name('kgb.index');
    Route::get('/kgb/upcoming', ...)->name('kgb.upcoming');
    Route::get('/kgb/eligible', ...)->name('kgb.eligible');
    Route::get('/kgb/ditunda', ...)->name('kgb.ditunda');
    Route::get('/kenaikan-pangkat', ...)->name('kenaikan-pangkat.index');
    Route::get('/kenaikan-pangkat/eligible', ...)->name('kenaikan-pangkat.eligible');
    Route::get('/kenaikan-pangkat/ditunda', ...)->name('kenaikan-pangkat.ditunda');
    Route::get('/pensiun', ...)->name('pensiun.index');
    Route::get('/duk', ...)->name('duk.index');
    Route::get('/satyalencana', ...)->name('satyalencana.index');
    Route::post('/satyalencana/award', ...)->name('satyalencana.award');

    // Export PDF & Excel
    Route::get('/export/{type}/{format}', [ExportController::class, 'export'])->name('export');

    // Document Download
    Route::get('/dokumen/{type}/{id}', [DocumentController::class, 'download'])->name('dokumen.download');

    // Admin — Master Data
    Route::resource('jabatan', JabatanController::class)->except(['show']);
    Route::prefix('admin')->group(function () {
        Route::get('/tabel-gaji', ...)->name('admin.tabel-gaji.index');
        // CRUD tabel gaji (store, update, destroy)
        Route::get('/golongan', ...)->name('admin.golongan.index');
    });

    // Profile & Password
    Route::get('/profile', ...)->name('profile.show');
    Route::put('/profile/password', ...)->name('profile.password.update');

    // Activity Log
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
});
```

> **Total route:** ~75+ route (7 resource pegawai + 35 riwayat CRUD + report/export/admin/document/profile/auth)

---

## 13. Membuat Views (Blade + Tailwind)

### 13.1 Struktur View

```
resources/views/
├── layouts/
│   └── app.blade.php          # Layout utama (responsive sidebar + content area)
├── auth/
│   └── login.blade.php        # Halaman login (standalone, tanpa layout)
├── dashboard/
│   └── index.blade.php        # Dashboard + Chart.js
├── pegawai/
│   ├── index.blade.php        # Tabel + pagination + search (AJAX)
│   ├── show.blade.php         # Detail + 8 tab riwayat + hukdis pemulihan modal + delete modal
│   ├── create.blade.php       # Form tambah
│   ├── edit.blade.php         # Form edit
│   └── _form.blade.php        # Partial form (shared create/edit)
├── riwayat/
│   ├── create-pangkat.blade.php
│   ├── edit-pangkat.blade.php
│   ├── create-jabatan.blade.php
│   ├── edit-jabatan.blade.php
│   ├── create-kgb.blade.php   # Auto-kalkulasi gaji baru dari PP 15/2019
│   ├── edit-kgb.blade.php
│   ├── create-hukuman.blade.php  # Form hukdis dengan field demotion dinamis
│   ├── edit-hukuman.blade.php    # Edit hukdis + toggle demotion sections
│   ├── create-pendidikan.blade.php
│   ├── edit-pendidikan.blade.php
│   ├── create-latihan.blade.php
│   ├── edit-latihan.blade.php
│   ├── create-skp.blade.php
│   └── edit-skp.blade.php
├── kgb/
│   └── index.blade.php        # + search/filter/pagination + tab Ditunda + export PDF/Excel
├── kenaikan-pangkat/
│   └── index.blade.php        # + search/filter/pagination + tab Ditunda + export
├── pensiun/
│   └── index.blade.php        # + search/pagination + export
├── duk/
│   └── index.blade.php        # + search/pagination + export
├── satyalencana/
│   └── index.blade.php        # + search/filter milestone + export
├── admin/                         # [ADMIN MASTER DATA]
│   ├── jabatan/
│   │   ├── index.blade.php    # Tabel jabatan + filter rumpun + CRUD
│   │   └── form.blade.php     # Form create/edit jabatan
│   ├── tabel-gaji/
│   │   ├── index.blade.php    # Daftar golongan → tabel gaji
│   │   └── show.blade.php     # Detail tabel gaji per golongan (CRUD entries)
│   └── golongan/
│       └── index.blade.php    # Referensi golongan ruang + statistik gaji
├── exports/
│   ├── dashboard-pdf.blade.php # Template PDF dashboard summary
│   ├── duk-pdf.blade.php      # Template PDF untuk DUK
│   ├── kgb-pdf.blade.php      # Template PDF untuk KGB
│   ├── pensiun-pdf.blade.php  # Template PDF untuk Pensiun
│   ├── kenaikan-pangkat-pdf.blade.php
│   └── satyalencana-pdf.blade.php
├── activity-log/
│   └── index.blade.php        # Riwayat perubahan data (tabel + pagination)
└── profile/
    └── index.blade.php        # Profil user + form ganti password
```

### 13.2 Layout Utama (`layouts/app.blade.php`)

Komponen utama:

- **Responsive Sidebar** (fixed, 256px) — gradient gelap, navigation links dengan icon SVG, active state highlight, user info + logout, toggle mobile/desktop via JavaScript `style.transform`
- **Mobile Overlay** — semi-transparent backdrop saat sidebar terbuka di mobile
- **Main Content** — sticky header dengan backdrop blur, breadcrumb, flash messages (auto-dismiss), `@yield('content')`
- **Delete Confirmation Modal** — shared modal yang dipanggil via `confirmDelete(url, message)` dari seluruh halaman
- Gunakan `@stack('scripts')` dan `@stack('styles')` untuk per-halaman JS/CSS

> **Catatan Tailwind v4:** Untuk elemen yang dibuat secara dinamis via JavaScript (misalnya pagination buttons), gunakan inline styles (`element.style.cssText`) bukan Tailwind classes, karena JIT compiler tidak meng-scan JS-generated class names.

### 13.3 Tips Design Tailwind

```
Warna utama: slate-900 (sidebar), blue-600 (accent), slate-50 (background)
Border radius: rounded-2xl (kartu), rounded-xl (input), rounded-lg (button)
Shadow: shadow-sm (kartu), shadow-2xl (sidebar)
Font: Inter via Google Fonts
Glassmorphism: bg-white/80 backdrop-blur-lg (header)
Badge/pill: px-2 py-1 text-xs rounded-full
```

### 13.4 Dashboard Charts

Gunakan Chart.js 4 via CDN:

```html
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
```

Pass data chart dari controller sebagai JSON ke JavaScript, lalu render 4 canvas:

- Bar chart: Distribusi Golongan, Distribusi Usia
- Doughnut chart: Gender, Unit Kerja

### 13.5 Pegawai Index — Server-Side Pagination

Gunakan fetch API ke endpoint `/pegawai-data?page=X&limit=10&search=keyword` dan render tabel secara dinamis dengan JavaScript. Setiap baris memiliki 3 tombol aksi: **Detail**, **Edit**, **Hapus**.

### 13.6 Pegawai Show — Tab System + Tab Retention + Delete Modal

Gunakan JavaScript sederhana untuk show/hide tab content, dengan dukungan **tab retention** via URL fragment:

```js
function showTab(name) {
    document
        .querySelectorAll(".tab-content")
        .forEach((el) => el.classList.add("hidden"));
    document.querySelectorAll(".tab-btn").forEach((el) => {
        /* toggle classes */
    });
    document.getElementById("tab-" + name).classList.remove("hidden");
}

// Tab retention: restore active tab from URL hash on page load
document.addEventListener("DOMContentLoaded", function () {
    const hash = window.location.hash;
    if (hash && hash.startsWith("#tab-")) {
        showTab(hash.substring(5));
    }
});
```

**Tab Retention Pattern:** Setiap redirect dari `RiwayatController` menyertakan URL fragment (`#tab-{type}`) sehingga JavaScript otomatis mengaktifkan tab yang sesuai setelah page reload.

```php
// Contoh: redirect setelah store Jabatan
return redirect(route('pegawai.show', $dto->pegawaiId) . '#tab-jabatan')
    ->with('success', 'Riwayat Jabatan baru berhasil ditambahkan.' . $docMsg);
```

**Flash Messages:** Alert menggunakan format deskriptif dengan icon, judul ("Berhasil!"/"Terjadi Kesalahan!"), pesan detail, dan tombol dismiss. Pesan otomatis mencakup info dokumen jika file diunggah.

Semua tombol hapus di 7 tab riwayat menggunakan shared delete modal dari layout:

```html
<button
    type="button"
    onclick="confirmDelete('/riwayat/pangkat/1', 'Hapus data riwayat pangkat ini?')"
    class="... bg-red-50 text-red-600 ..."
>
    Hapus
</button>
```

### 13.7 Tombol Aksi dalam Tabel

**Penting:** Jangan gunakan `flex` langsung pada `<td>`. Bungkus dengan `<div>`:

```html
<td class="px-3 py-2">
    <div class="flex items-center gap-2">
        <a
            href="..."
            class="inline-flex items-center px-2 py-1 bg-blue-50 text-blue-600 text-xs rounded-md"
            >Edit</a
        >
        <button
            type="button"
            onclick="confirmDelete('...', 'Hapus data ini?')"
            class="inline-flex items-center px-2 py-1 bg-red-50 text-red-600 text-xs rounded-md"
        >
            Hapus
        </button>
    </div>
</td>
```

### 13.8 Report Views — Search, Filter, Pagination, Export

Semua halaman report (KGB, Pensiun, DUK, Kenaikan Pangkat, Satyalencana) memiliki fitur:

- **Search**: Filter data berdasarkan NIP/nama menggunakan client-side JavaScript
- **Client-side Pagination**: Data di-render dari Blade, lalu dipaginasi via JS (15 per halaman)
- **Export**: Link ke `/export/{type}/pdf` dan `/export/{type}/excel`
- **Tab Filter**: KGB dan Kenaikan Pangkat punya tab Ditunda (pegawai dengan hukdis aktif), Satyalencana punya filter per milestone

### 13.9 Export PDF Templates

6 template PDF di `resources/views/exports/` menggunakan HTML/CSS inline (tanpa Tailwind) karena DomPDF tidak mendukung Tailwind. Format tabel standar dengan header, border, dan logo.

---

## 14. Menambahkan Export PDF & Excel

### 14.1 Install Package

```bash
composer require barryvdh/laravel-dompdf
composer require maatwebsite/excel
```

### 14.2 Membuat Export Classes

Buat folder `app/Exports/` dan buat 5 export class (satu per jenis laporan):

| File                        | Untuk                   |
| --------------------------- | ----------------------- |
| `KGBExport.php`             | Export monitoring KGB   |
| `PensiunExport.php`         | Export alert pensiun    |
| `DUKExport.php`             | Export DUK              |
| `KenaikanPangkatExport.php` | Export kenaikan pangkat |
| `SatyalencanaExport.php`    | Export Satyalencana     |

Setiap export class mengimplementasikan `FromCollection`, `WithHeadings`, `WithMapping`, dan `WithStyles` dari Maatwebsite/Excel.

### 14.3 Membuat ExportController

```php
// app/Http/Controllers/ExportController.php
// Method export($type, $format) yang menerima tipe laporan dan format (pdf/excel)
// Untuk PDF: gunakan Barryvdh\DomPDF\Facade\Pdf::loadView() dengan template dari views/exports/
// Untuk Excel: gunakan Maatwebsite\Excel\Facades\Excel::download() dengan export class
```

### 14.4 Membuat Template PDF

Buat 6 template PDF di `resources/views/exports/`:

- `dashboard-pdf.blade.php`
- `kgb-pdf.blade.php`
- `pensiun-pdf.blade.php`
- `duk-pdf.blade.php`
- `kenaikan-pangkat-pdf.blade.php`
- `satyalencana-pdf.blade.php`

> **Tips:** Template PDF menggunakan CSS inline (bukan Tailwind) karena DomPDF memiliki keterbatasan CSS. Gunakan `<style>` tag di dalam template.

### 14.5 Menambahkan Tombol Export di View

Tambahkan link export di setiap halaman report:

```html
<a
    href="{{ route('export', ['type' => 'kgb', 'format' => 'pdf']) }}"
    class="px-3 py-1.5 text-xs bg-red-50 text-red-600 rounded-lg"
    >PDF</a
>
<a
    href="{{ route('export', ['type' => 'kgb', 'format' => 'excel']) }}"
    class="px-3 py-1.5 text-xs bg-emerald-50 text-emerald-600 rounded-lg"
    >Excel</a
>
```

---

## 15. Menambahkan Activity Log

### 15.1 Install Spatie Activity Log

```bash
composer require spatie/laravel-activitylog
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
php artisan migrate
```

### 15.2 Menambahkan Trait ke Model

Tambahkan `LogsActivity` trait dan konfigurasi ke model yang ingin di-track:

```php
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Pegawai extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Pegawai di-{$eventName}");
    }
}
```

Tambahkan ke model-model berikut: `Pegawai`, `RiwayatPangkat`, `RiwayatJabatan`, `RiwayatKgb`, `RiwayatHukumanDisiplin`, `RiwayatPendidikan`, `RiwayatLatihanJabatan`, `RiwayatPenghargaan`, `PenilaianKinerja`.

### 15.3 Membuat ActivityLogController

```php
// app/Http/Controllers/ActivityLogController.php
// Method index() mengambil data dari Spatie\Activitylog\Models\Activity
// Tampilkan: waktu, user, deskripsi, subject, perubahan (properties)
```

### 15.4 Membuat View Activity Log

Buat `resources/views/activity-log/index.blade.php` yang menampilkan tabel riwayat aktivitas dengan kolom waktu, user, aksi, dan detail perubahan.

---

## 16. Build & Menjalankan Aplikasi

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

- Email: `superadmin@kemenipas.go.id`
- Password: `password`

---

## Ringkasan Arsitektur

```
Request → Route → FormRequest (validasi) → Controller (orkestrasi)
                                                ↓
                                     DTO::fromRequest($validated)
                                                ↓
                                    Service (DB::transaction)
                                                ↓
                                       Model → Database
                                                ↓
                                 API Resource / View (Blade)
                                                ↓
                                        Response (HTML/JSON)
```

| Layer            | Tanggung Jawab                                                        |
| ---------------- | --------------------------------------------------------------------- |
| **Route**        | URL mapping ke controller                                             |
| **FormRequest**  | Validasi input & otorisasi (controller **tidak** boleh validate)      |
| **Controller**   | Orkestrasi saja: menerima FormRequest, membuat DTO, memanggil Service |
| **DTO**          | Strongly-typed data contract antara Controller ↔ Service              |
| **Service**      | Business logic murni, semua DML dibungkus `DB::transaction()`         |
| **Model**        | Data access, relationships, computed attributes                       |
| **API Resource** | Transformasi model → JSON/array response yang eksplisit               |
| **View**         | Presentasi HTML dengan Tailwind CSS                                   |
| **Enum**         | Tipe data konstan dengan label display                                |
| **Seeder**       | Data awal untuk testing                                               |

### Struktur Folder Lengkap

```
app/
├── DTOs/
│   ├── PegawaiDTO.php
│   └── Riwayat/
│       ├── RiwayatPangkatDTO.php
│       ├── RiwayatJabatanDTO.php
│       ├── RiwayatKgbDTO.php
│       ├── RiwayatHukumanDisiplinDTO.php
│       ├── RiwayatPendidikanDTO.php
│       ├── RiwayatLatihanJabatanDTO.php
│       └── PenilaianKinerjaDTO.php
├── Enums/                      # 10 enum class
├── Exports/
│   ├── DUKExport.php
│   ├── KGBExport.php
│   ├── PensiunExport.php
│   ├── KenaikanPangkatExport.php
│   └── SatyalencanaExport.php
├── Http/
│   ├── Controllers/            # 16 controller
│   ├── Requests/
│   │   ├── StorePegawaiRequest.php
│   │   ├── UpdatePegawaiRequest.php
│   │   ├── UpdatePasswordRequest.php
│   │   ├── Auth/
│   │   │   └── LoginRequest.php
│   │   └── Riwayat/
│   │       ├── StorePangkatRequest.php
│   │       ├── UpdatePangkatRequest.php
│   │       └── ... (16 file total)
│   └── Resources/
│       └── PegawaiResource.php
├── Models/                     # 12 model (termasuk TabelGaji)
└── Services/
    ├── PegawaiService.php
    ├── RiwayatService.php
    ├── JabatanService.php
    ├── KGBService.php
    ├── KGBCalculationService.php
    ├── PensiunService.php
    ├── KenaikanPangkatService.php
    ├── SatyalencanaService.php
    ├── DUKService.php
    ├── DashboardService.php
    ├── DocumentUploadService.php
    └── TabelGajiService.php
database/
├── migrations/                 # 15 migration files
└── seeders/
    ├── DatabaseSeeder.php
    ├── UserSeeder.php
    ├── MasterDataSeeder.php
    ├── PegawaiSeeder.php
    └── TabelGajiSeeder.php     # Data tabel gaji PP 15/2019
resources/views/                # 42 blade view files
```

---

## Checklist Pengerjaan

- [ ] Buat project Laravel baru
- [ ] Konfigurasi SQLite & Tailwind CSS v4
- [ ] Buat 10 Enum (termasuk JenisSanksi, StatusHukdis, RumpunJabatan)
- [ ] Buat 15 Migration (12 custom + 3 Spatie Activity Log)
- [ ] Buat 12 Eloquent Model dengan relationships (termasuk TabelGaji, isAktif/isType2 di Hukdis)
- [ ] Buat 5 Seeder (User, MasterData 37 jabatan, Pegawai 100, TabelGaji, Database)
- [ ] Buat 19 FormRequest (2 Pegawai + 16 Riwayat + 1 Auth)
- [ ] Buat 8 DTO (1 Pegawai + 7 Riwayat)
- [ ] Buat 12 Service class (termasuk TabelGajiService, KGBCalculationService, DocumentUploadService)
- [ ] Buat 1 API Resource (PegawaiResource)
- [ ] Buat 5 Excel Export class
- [ ] Buat 16 Controller (termasuk Admin Jabatan, TabelGaji, Golongan, DocumentController)
- [ ] Definisikan ~75+ Routes (termasuk admin, document download)
- [ ] Buat 1 Layout + 42 Blade Views (termasuk 6 PDF template + 4 admin views)
- [ ] Tambahkan Spatie Activity Log ke 9 model
- [ ] Implementasi Hukdis Hybrid Logic (PP 94/2021) — Type 1/2/3 + Pemulihan
- [ ] Build + Test

**Estimasi waktu pengerjaan:** 14-20 jam untuk programmer yang sudah familiar dengan Laravel.

> **📊 ERD (Entity Relationship Diagram)** — Lihat di [README.md §Skema Database](README.md#skema-database-erd).
