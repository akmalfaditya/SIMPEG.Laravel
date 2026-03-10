# SIMPEG - Sistem Informasi Manajemen Pegawai

Aplikasi manajemen kepegawaian berbasis web untuk **Kementerian Imigrasi dan Pemasyarakatan (Kemenipas)** yang dibangun menggunakan **Laravel 12**, **SQLite**, dan **Tailwind CSS v4**.

## Fitur Utama

- **Dashboard** — Ringkasan data pegawai dengan chart distribusi (golongan, gender, usia, unit kerja) dan alert KGB/Pensiun
- **Manajemen Pegawai** — CRUD lengkap dengan pencarian AJAX, paginasi server-side, validasi format NIP, dan **One-Stop Creation Flow** (pilih pangkat & jabatan saat create → gaji otomatis + auto-generate RiwayatPangkat & RiwayatJabatan)
- **Biodata Pegawai** — Gelar depan/belakang, bagian (5 seksi Kanim), tipe pegawai (PNS/CPNS/PPPK), status kepegawaian, unit kerja
- **Riwayat Kepegawaian** — 7 modul riwayat (Pangkat, Jabatan, KGB, Hukuman Disiplin, Pendidikan, Latihan Jabatan, Penilaian Kinerja)
- **Monitoring KGB** — Alert otomatis pegawai yang mendekati/eligible kenaikan gaji berkala (siklus 2 tahun), kalkulasi gaji baru otomatis berdasarkan PP 15/2019, integrasi hukuman disiplin (penundaan KGB)
- **Kenaikan Pangkat** — Analisis eligibilitas berdasarkan syarat masa kerja, SKP, latihan, dan hukuman disiplin
- **Hukuman Disiplin Hybrid (PP 94/2021)** — Sistem hukdis lengkap dengan 3 status (Aktif/Selesai/Dipulihkan), 6 jenis sanksi, mekanisme Type 2 hard-update (penurunan pangkat/jabatan/pembebasan), pemulihan (pemulihan pangkat, jabatan, dan gaji otomatis), serta integrasi blokir KGB dan kenaikan pangkat
- **Alert Pensiun** — Monitoring pensiun berdasarkan BUP dengan level alert (Hijau/Kuning/Merah/Hitam)
- **DUK** — Daftar Urut Kepangkatan dengan ranking otomatis sesuai hierarki BKN
- **Satyalencana** — Identifikasi kandidat penghargaan Satyalencana Karya Satya (10/20/30 tahun)
- **Master Data** — CRUD Jabatan (dengan rumpun jabatan), Tabel Gaji (PP 15/2019), dan referensi Golongan Ruang
- **Export PDF & Excel** — Semua laporan (KGB, Pensiun, DUK, Kenaikan Pangkat, Satyalencana) bisa diekspor ke PDF dan Excel
- **Activity Log** — Pencatatan otomatis setiap perubahan data pegawai dan riwayat menggunakan Spatie Activity Log
- **Document Management** — Upload dan manajemen file SK (PDF, maks 5MB) dengan penamaan bermakna (`NIP_Module_Timestamp_NamaAsli.pdf`), inline PDF preview di browser, link Google Drive opsional
- **UX: Tab Retention & Flash Messages** — Setelah CRUD riwayat, halaman otomatis kembali ke tab yang aktif; alert deskriptif dengan icon, judul, pesan detail, dan tombol dismiss
- **Profil & Ganti Password** — Manajemen profil user dan update password
- **Autentikasi** — Login/logout dengan role-based access (SuperAdmin, HR)

## Persyaratan Sistem

| Komponen | Versi Minimum |
| -------- | ------------- |
| PHP      | 8.2+          |
| Composer | 2.x           |
| Node.js  | 18+           |
| NPM      | 9+            |

## Instalasi & Setup

### 1. Clone Repository

```bash
git clone <repository-url>
cd SIMPEG.Laravel
```

### 2. Install Dependensi

```bash
composer install
npm install
```

### 3. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Pastikan konfigurasi `.env` berikut:

```env
APP_NAME=SIMPEG
APP_LOCALE=id
APP_FAKER_LOCALE=id_ID
DB_CONNECTION=sqlite
```

### 4. Buat Database SQLite

```bash
# Linux/Mac
touch database/database.sqlite

# Windows (PowerShell)
New-Item database/database.sqlite -ItemType File
```

### 5. Jalankan Migrasi & Seeder

```bash
php artisan migrate --seed
```

Ini akan membuat semua tabel dan mengisi data sampel:

- 2 user (superadmin@kemenipas.go.id / password)
- 17 golongan/pangkat (I/a s.d IV/e)
- 37 jabatan master data (3 rumpun: Struktural, Imigrasi, Pemasyarakatan)
- 100 pegawai dengan riwayat lengkap
- Tabel gaji PP 15/2019 (untuk kalkulasi KGB otomatis)

### 6. Build Assets Frontend

```bash
npm run build
```

### 7. Jalankan Aplikasi

```bash
php artisan serve
```

Akses di: **http://localhost:8000**

**Login sebagai admin:**

- Email: `superadmin@kemenipas.go.id`
- Password: `password`

## Struktur Project

```
app/
├── DTOs/               # 8 DTO class (PegawaiDTO + 7 Riwayat DTO)
├── Enums/              # 9 PHP Enum (JenisKelamin, Agama, JenisSanksi, StatusHukdis, RumpunJabatan, dll)
├── Exports/            # 5 Excel Export class (KGB, Pensiun, DUK, Kenaikan Pangkat, Satyalencana)
├── Http/
│   ├── Controllers/    # 16 Controller (Auth, Dashboard, Pegawai, Riwayat, Export, Jabatan, TabelGaji, dll)
│   ├── Requests/       # 19 FormRequest (Store/Update untuk setiap entitas)
│   └── Resources/      # 1 API Resource (PegawaiResource)
├── Models/             # 13 Eloquent Model (Pegawai, GolonganPangkat, Jabatan, TabelGaji, dll)
├── Providers/          # AppServiceProvider
└── Services/           # 13 Service Class (business logic layer)
database/
├── factories/          # Model factories (UserFactory, PegawaiFactory)
├── migrations/         # 19 migration files
├── seeders/            # 6 seeder (User, MasterData, GolonganPangkat, Pegawai, TabelGaji, Database)
resources/views/
├── layouts/app.blade.php          # Layout utama dengan responsive sidebar
├── auth/login.blade.php           # Halaman login
├── dashboard/index.blade.php      # Dashboard dengan chart
├── pegawai/                       # 5 view (index, show, create, edit, _form)
├── riwayat/                       # 14 view (create/edit untuk 7 riwayat)
├── kgb/                           # Monitoring KGB
├── kenaikan-pangkat/              # Eligibilitas kenaikan pangkat
├── pensiun/                       # Alert pensiun
├── duk/                           # Daftar Urut Kepangkatan
├── satyalencana/                  # Kandidat Satyalencana
├── admin/                         # 4 view (CRUD Jabatan, Tabel Gaji, Golongan)
├── exports/                       # 6 template PDF (dashboard, duk, kgb, pensiun, kenaikan-pangkat, satyalencana)
├── activity-log/                  # Riwayat aktivitas sistem
└── profile/                       # Profil & ganti password
```

## Teknologi

| Kategori         | Teknologi                          |
| ---------------- | ---------------------------------- |
| **Backend**      | Laravel 12 (PHP 8.2+)              |
| **Database**     | SQLite                             |
| **Frontend**     | Blade Templates + Tailwind CSS 4.x |
| **Build Tool**   | Vite                               |
| **Charts**       | Chart.js 4 (CDN)                   |
| **Font**         | Inter (Google Fonts)               |
| **PDF Export**   | barryvdh/laravel-dompdf 3.x        |
| **Excel Export** | maatwebsite/excel 3.x              |
| **Activity Log** | spatie/laravel-activitylog 4.x     |

## Skema Database

| Tabel                       | Deskripsi                                                                       |
| --------------------------- | ------------------------------------------------------------------------------- |
| `users`                     | Data user login dengan role (SuperAdmin, HR)                                    |
| `golongan_pangkats`         | Master data golongan/pangkat (label, pangkat, group, min_pendidikan, is_active) |
| `jabatans`                  | Master data jabatan (nama, jenis, BUP, eselon, kelas, rumpun, is_active)        |
| `pegawais`                  | Data utama pegawai (NIP, biodata, gelar, bagian, tipe pegawai, status kepegawaian, ASN, gaji pokok) |
| `riwayat_pangkats`          | Riwayat kenaikan pangkat (FK ke `golongan_pangkats`, flag `is_hukdis_demotion`) |
| `riwayat_jabatans`          | Riwayat penempatan jabatan (dengan flag `is_hukdis_demotion`)                   |
| `riwayat_kgbs`              | Riwayat KGB (gaji lama/baru, masa kerja golongan)                               |
| `riwayat_hukuman_disiplins` | Riwayat hukuman disiplin (status aktif/selesai/dipulihkan, data pemulihan)      |
| `riwayat_pendidikans`       | Riwayat pendidikan formal                                                       |
| `riwayat_latihan_jabatans`  | Riwayat diklat/pelatihan                                                        |
| `riwayat_penghargaans`      | Riwayat penghargaan (Satyalencana, dll)                                         |
| `penilaian_kinerjas`        | Penilaian kinerja (SKP) dengan dokumen                                          |
| `tabel_gajis`               | Tabel gaji PNS PP 15/2019 (FK ke `golongan_pangkats` × masa kerja)              |
| `activity_log`              | Log aktivitas perubahan data (Spatie)                                           |

## Enum

| Enum               | Deskripsi                                                                                                          |
| ------------------ | ------------------------------------------------------------------------------------------------------------------ |
| `Agama`            | 6 agama yang diakui                                                                                                |
| `GolonganDarah`    | A, B, AB, O                                                                                                        |
| `JenisJabatan`     | Pejabat Administrasi, Fungsional, Pimpinan Tinggi                                                                  |
| `JenisKelamin`     | Laki-laki, Perempuan                                                                                               |
| `JenisSanksi`      | 6 jenis: Penundaan KGB, Penundaan Pangkat, Penurunan Pangkat, Penurunan Jabatan, Pembebasan Jabatan, Pemberhentian |
| `RumpunJabatan`    | Imigrasi, Pemasyarakatan, Struktural                                                                               |
| `StatusHukdis`     | Aktif, Selesai, Dipulihkan                                                                                         |
| `StatusPernikahan` | Belum Menikah, Menikah, Cerai Hidup, Cerai Mati                                                                    |
| `TingkatHukuman`   | Ringan, Sedang, Berat                                                                                              |

> **Catatan:** `GolonganRuang` (I/a s.d IV/e) telah dimigrasi dari Enum ke tabel database `golongan_pangkats` (model `GolonganPangkat`) untuk mendukung CRUD dinamis.

## Service Layer

Semua business logic dipisahkan ke Service class untuk menjaga controller tetap tipis:

| Service                  | Tanggung Jawab                                                                           |
| ------------------------ | ---------------------------------------------------------------------------------------- |
| `PegawaiService`         | CRUD pegawai, pencarian, paginasi, One-Stop Creation Flow (auto gaji + riwayat dalam DB::transaction) |
| `RiwayatService`         | CRUD 7 jenis riwayat, hukdis hybrid logic (Type 2 demotion, pemulihan, rekalkulasi gaji), durasi Sedang/Berat enforced 1 tahun |
| `JabatanService`         | CRUD master data jabatan                                                                 |
| `TabelGajiService`       | CRUD tabel gaji PP 15/2019                                                               |
| `KGBService`             | Monitoring status KGB, jatuh tempo, eligibilitas (dengan integrasi hukdis)               |
| `KGBCalculationService`  | Kalkulasi gaji baru berdasarkan tabel gaji PP 15/2019                                    |
| `KenaikanPangkatService` | Analisis syarat kenaikan pangkat (dengan integrasi hukdis)                               |
| `PensiunService`         | Alert pensiun berdasarkan BUP                                                            |
| `DUKService`             | Ranking DUK sesuai hierarki BKN                                                          |
| `SatyalencanaService`    | Identifikasi kandidat Satyalencana                                                       |
| `DashboardService`       | Agregasi data dashboard + chart                                                          |
| `DocumentUploadService`  | Upload dan manajemen file dokumen SK (penamaan bermakna via `storeAs`)                   |
| `GolonganPangkatService` | CRUD master data golongan/pangkat                                                        |

## Hukuman Disiplin — Hybrid Logic (PP 94/2021)

Sistem hukuman disiplin mendukung 3 kategori sanksi dengan mekanisme berbeda:

### Type 1 — Penundaan (Soft-block)

- **Penundaan KGB**: Menunda jatuh tempo KGB sesuai durasi hukuman
- **Penundaan Pangkat**: Menambah syarat masa kerja kenaikan pangkat

### Type 2 — Penurunan (Hard-update)

- **Penurunan Pangkat**: Insert record baru di `riwayat_pangkats` dengan `is_hukdis_demotion=true`, pangkat target harus lebih rendah dari saat ini, gaji otomatis dihitung ulang
- **Penurunan Jabatan**: Insert record baru di `riwayat_jabatans` dengan `is_hukdis_demotion=true`
- **Pembebasan Jabatan**: Membebaskan dari jabatan, memblokir kenaikan pangkat

### Type 3 — Terminal

- **Pemberhentian**: Memblokir semua proses kepegawaian

### Pemulihan (Dipulihkan)

- Mengubah status hukuman menjadi `Dipulihkan`
- Untuk penurunan pangkat: insert record pemulihan pangkat + rekalkulasi gaji otomatis
- Untuk penurunan/pembebasan jabatan: insert record pemulihan jabatan
- Hukuman yang dipulihkan tidak lagi memblokir KGB maupun kenaikan pangkat

## Akun Default

| Role       | Email                      | Password |
| ---------- | -------------------------- | -------- |
| SuperAdmin | superadmin@kemenipas.go.id | password |
| HR         | hr@kemenipas.go.id         | password |

## Pengembangan

```bash
# Jalankan dev server dengan hot-reload
npm run dev          # Terminal 1
php artisan serve    # Terminal 2

# Reset database
php artisan migrate:fresh --seed

# Bersihkan cache
php artisan optimize:clear
```

## Lisensi

MIT License
