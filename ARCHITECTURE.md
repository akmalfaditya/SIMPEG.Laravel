# ARCHITECTURE.md — SIMPEG Kemenipas

> Dokumentasi arsitektur teknis Sistem Informasi Manajemen Pegawai (SIMPEG)  
> Kementerian Imigrasi dan Pemasyarakatan (Kemenipas).

---

## 1. Tech Stack & Versi

| Layer                 | Teknologi                      | Versi                    |
| --------------------- | ------------------------------ | ------------------------ |
| **Backend Framework** | Laravel                        | ^12.0                    |
| **Language**          | PHP                            | ^8.2                     |
| **Database**          | SQLite                         | (bundled via pdo_sqlite) |
| **Frontend**          | Blade Templates + Tailwind CSS | v4.0                     |
| **Build Tool**        | Vite                           | ^7.0                     |
| **Vite Plugin**       | laravel-vite-plugin            | ^2.0                     |
| **CSS Framework**     | @tailwindcss/vite              | ^4.0                     |
| **Charts**            | Chart.js 4                     | CDN                      |
| **Font**              | Inter                          | Google Fonts CDN         |
| **PDF Export**        | barryvdh/laravel-dompdf        | ^3.1                     |
| **Excel Export**      | maatwebsite/excel              | ^3.1                     |
| **Activity Log**      | spatie/laravel-activitylog     | ^4.12                    |
| **Dev: Linter**       | laravel/pint                   | ^1.24                    |
| **Dev: Testing**      | phpunit/phpunit                | ^11.5.3                  |
| **Dev: Mocking**      | mockery/mockery                | ^1.6                     |

### Runtime Config

| Setting      | Value             |
| ------------ | ----------------- |
| Timezone     | `Asia/Jakarta`    |
| Locale       | `id` (Indonesian) |
| Faker Locale | `id_ID`           |
| Encryption   | AES-256-CBC       |

---

## 2. Arsitektur & Design Pattern

**Architectural Style: Service-Layered MVC**

Aplikasi mengikuti pola **MVC** standar Laravel dengan tambahan **Service Layer** dan **DTO Pattern** untuk memisahkan business logic dari controller.

```
Request → Route → Controller → Service → Model → Database
                      ↑              ↑
                 FormRequest        DTO
                 (Validation)    (Data Transfer)
```

### Prinsip Utama

1. **Thin Controller, Fat Service** — Controller hanya menerima request, memanggil service, dan mengembalikan response/view. Semua business logic ada di Service.
2. **DTO Pattern** — Data dari FormRequest di-transform ke DTO sebelum masuk Service, memastikan type-safety dan decoupling.
3. **PHP Enums** — Data master statis (Agama, JenisKelamin, JenisSanksi, dll) menggunakan PHP 8.1 Backed Enum.
4. **Dynamic Master Data** — Data master yang perlu CRUD (Golongan/Pangkat, Jabatan, Tabel Gaji) disimpan di tabel database, dilayani oleh dedicated Service.
5. **Activity Logging** — Semua perubahan data pegawai dan riwayat dicatat otomatis via Spatie `LogsActivity` trait.
6. **Tab Retention via URL Fragment** — Redirect dari `RiwayatController` menyertakan `#tab-{type}` fragment. JavaScript di `show.blade.php` membaca `window.location.hash` pada `DOMContentLoaded` dan mengaktifkan tab yang sesuai.
7. **Descriptive Flash Messages** — Semua flash message `success`/`error` harus deskriptif (menyebut nama modul + aksi + info dokumen jika ada). Layout (`app.blade.php`) menampilkan alert dengan icon, judul bold, pesan detail, dan tombol dismiss.

---

## 3. Struktur Direktori

```
SIMPEG.Laravel/
├── app/
│   ├── DTOs/                          # Data Transfer Objects
│   │   ├── PegawaiDTO.php             #   DTO untuk CRUD pegawai
│   │   └── Riwayat/                   #   7 DTO untuk setiap jenis riwayat
│   │       ├── PenilaianKinerjaDTO.php
│   │       ├── RiwayatHukumanDisiplinDTO.php
│   │       ├── RiwayatJabatanDTO.php
│   │       ├── RiwayatKgbDTO.php
│   │       ├── RiwayatLatihanJabatanDTO.php
│   │       ├── RiwayatPangkatDTO.php
│   │       └── RiwayatPendidikanDTO.php
│   │
│   ├── Enums/                         # PHP 8.1 Backed Enums (data statis)
│   │   ├── Agama.php                  #   6 agama
│   │   ├── GolonganDarah.php          #   A, B, AB, O
│   │   ├── JenisJabatan.php           #   6 jenis jabatan ASN
│   │   ├── JenisKelamin.php           #   Laki-laki, Perempuan
│   │   ├── JenisSanksi.php            #   6 jenis sanksi hukdis (PP 94/2021)
│   │   ├── RumpunJabatan.php          #   Imigrasi, Pemasyarakatan, Struktural
│   │   ├── StatusHukdis.php           #   Aktif, Selesai, Dipulihkan
│   │   ├── StatusPernikahan.php       #   4 status
│   │   └── TingkatHukuman.php         #   Ringan, Sedang, Berat
│   │
│   ├── Exports/                       # Maatwebsite Excel export classes
│   │   ├── DUKExport.php
│   │   ├── KenaikanPangkatExport.php
│   │   ├── KGBExport.php
│   │   ├── PensiunExport.php
│   │   └── SatyalencanaExport.php
│   │
│   ├── Http/
│   │   ├── Controllers/               # 17 Controllers (thin, delegasi ke Service)
│   │   │   ├── ActivityLogController.php
│   │   │   ├── AuthController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── DocumentController.php
│   │   │   ├── DUKController.php
│   │   │   ├── ExportController.php
│   │   │   ├── GolonganController.php
│   │   │   ├── JabatanController.php
│   │   │   ├── KenaikanPangkatController.php
│   │   │   ├── KGBController.php
│   │   │   ├── PegawaiController.php
│   │   │   ├── PensiunController.php
│   │   │   ├── ProfileController.php
│   │   │   ├── RiwayatController.php
│   │   │   ├── SatyalencanaController.php
│   │   │   └── TabelGajiController.php
│   │   │
│   │   ├── Requests/                  # FormRequest validation classes
│   │   │   ├── Auth/
│   │   │   │   └── LoginRequest.php
│   │   │   ├── Riwayat/              #   15 Store/Update requests untuk riwayat
│   │   │   │   ├── Store{Type}Request.php
│   │   │   │   └── Update{Type}Request.php
│   │   │   ├── StorePegawaiRequest.php
│   │   │   ├── UpdatePegawaiRequest.php
│   │   │   └── UpdatePasswordRequest.php
│   │   │
│   │   └── Resources/
│   │       └── PegawaiResource.php    #   API Resource (JSON transform)
│   │
│   ├── Models/                        # 13 Eloquent Models
│   │   ├── GolonganPangkat.php        #   Master golongan/pangkat (ex-Enum)
│   │   ├── Jabatan.php                #   Master jabatan
│   │   ├── Pegawai.php                #   Data pegawai (central entity)
│   │   ├── PenilaianKinerja.php       #   SKP/kinerja
│   │   ├── RiwayatHukumanDisiplin.php #   Hukdis + isAktif() + isType2()
│   │   ├── RiwayatJabatan.php
│   │   ├── RiwayatKgb.php
│   │   ├── RiwayatLatihanJabatan.php
│   │   ├── RiwayatPangkat.php
│   │   ├── RiwayatPendidikan.php
│   │   ├── RiwayatPenghargaan.php
│   │   ├── TabelGaji.php              #   Tabel gaji PP 15/2019
│   │   └── User.php
│   │
│   ├── Providers/
│   │   └── AppServiceProvider.php
│   │
│   └── Services/                      # 13 Service classes (business logic)
│       ├── DashboardService.php       #   Agregasi dashboard + chart data
│       ├── DocumentUploadService.php  #   Upload/delete file SK
│       ├── DUKService.php             #   Ranking DUK per aturan BKN
│       ├── GolonganPangkatService.php #   CRUD master golongan/pangkat
│       ├── JabatanService.php         #   CRUD master jabatan
│       ├── KenaikanPangkatService.php #   Analisis eligibilitas kenaikan pangkat
│       ├── KGBCalculationService.php  #   Kalkulasi gaji baru via tabel PP 15/2019
│       ├── KGBService.php             #   Monitoring KGB (jatuh tempo, eligibilitas)
│       ├── PegawaiService.php         #   CRUD pegawai + One-Stop Creation Flow (auto gaji, riwayat)
│       ├── PensiunService.php         #   Alert pensiun (level Hijau-Hitam)
│       ├── RiwayatService.php         #   CRUD 7 jenis riwayat + hukdis logic (durasi enforced)
│       ├── SatyalencanaService.php    #   Kandidat penghargaan Satyalencana
│       └── TabelGajiService.php       #   CRUD tabel gaji PP 15/2019
│
├── bootstrap/                         # Laravel bootstrap
├── config/                            # Konfigurasi (app, auth, database, dll)
├── database/
│   ├── factories/                     # Model factories (UserFactory, PegawaiFactory)
│   ├── migrations/                    # 19 migration files
│   └── seeders/                       # 6 seeders (User, MasterData, Golongan, Pegawai, TabelGaji, Database)
│
├── public/                            # Entry point + compiled assets
│   └── build/                         #   Vite build output
│
├── resources/
│   ├── css/app.css                    # Tailwind CSS entry
│   ├── js/app.js                      # JS entry (Axios)
│   └── views/                         # Blade templates
│       ├── layouts/app.blade.php      #   Layout utama (responsive sidebar)
│       ├── auth/                      #   Login
│       ├── dashboard/                 #   Dashboard + chart
│       ├── pegawai/                   #   CRUD pegawai (5 views)
│       ├── riwayat/                   #   CRUD 7 riwayat (14 views)
│       ├── kgb/                       #   Monitoring KGB
│       ├── kenaikan-pangkat/          #   Eligibilitas kenaikan pangkat
│       ├── pensiun/                   #   Alert pensiun
│       ├── duk/                       #   Daftar Urut Kepangkatan
│       ├── satyalencana/              #   Kandidat Satyalencana
│       ├── admin/                     #   Master data (Jabatan, Tabel Gaji, Golongan)
│       ├── exports/                   #   6 template PDF
│       ├── activity-log/              #   Audit trail
│       └── profile/                   #   Profil & ganti password
│
├── routes/
│   └── web.php                        # Semua route (Auth, CRUD, Reports, Admin)
│
├── storage/                           # Upload, cache, logs
├── tests/                             # PHPUnit tests
└── vendor/                            # Composer dependencies
```

---

## 4. Tanggung Jawab Tiap Layer

### Controller (`app/Http/Controllers/`)

- Menerima HTTP request (melalui FormRequest untuk validasi)
- Memanggil Service method yang relevan
- Mengembalikan Blade view atau redirect
- **TIDAK** mengandung business logic

### FormRequest (`app/Http/Requests/`)

- Validasi input (rules, messages, authorize)
- `authorize()` selalu return `true` (belum ada Policy)
- Konvensi penamaan: `Store{Entity}Request`, `Update{Entity}Request`

### DTO (`app/DTOs/`)

- Immutable data container untuk transfer data Request → Service
- Method statis `fromRequest(array $data)` dan `toArray()`
- 1 DTO per entitas (PegawaiDTO + 7 Riwayat DTO)

### Service (`app/Services/`)

- Semua business logic dan kalkulasi
- CRUD operations (create, update, delete) via Eloquent
- Complex calculations: KGB eligibility, kenaikan pangkat, DUK ranking, pensiun alert
- Hukuman disiplin integration (penundaan, penurunan, pemulihan)
- Document upload/delete delegasi

### Model (`app/Models/`)

- Eloquent ORM: `$fillable`, `casts()`, relationships
- Trait `LogsActivity` (Spatie) pada semua model utama
- Accessor/computed attributes (Pegawai: `masa_kerja`, `pangkat_terakhir`, `jabatan_terakhir`)
- Domain methods: `RiwayatHukumanDisiplin::isAktif()`, `isType2()`

### Enum (`app/Enums/`)

- PHP 8.1 Backed Enums untuk data statis
- Setiap enum memiliki method `label(): string` untuk tampilan UI
- Beberapa enum memiliki method tambahan: `color()` (StatusHukdis, RumpunJabatan)

### Export (`app/Exports/`)

- Maatwebsite Excel export classes
- Implements `FromArray`, `WithHeadings`
- Digunakan oleh `ExportController` untuk export Excel/PDF

---

## 5. Database Schema Overview

### Entitas Utama

- **`pegawais`** — Central entity, connected to 8 riwayat tables. Fields include gelar_depan, gelar_belakang, bagian, tipe_pegawai (PNS/CPNS/PPPK), status_kepegawaian (Aktif/Tidak Aktif), unit_kerja (default Kanim Jakut)
- **`golongan_pangkats`** — Master golongan/pangkat (17 level I/a – IV/e), FK from `riwayat_pangkats` dan `tabel_gajis`
- **`jabatans`** — Master jabatan, FK from `riwayat_jabatans`
- **`tabel_gajis`** — Lookup salary matrix (golongan × masa_kerja)
- **`users`** — Authentication, simple `role` string (SuperAdmin/HR)

### Relasi (ERD tersedia di TUTORIAL.md §17)

- `pegawais` 1→N semua tabel riwayat
- `golongan_pangkats` 1→N `riwayat_pangkats`, 1→N `tabel_gajis`
- `jabatans` 1→N `riwayat_jabatans`
- `activity_log` polimorfik ke semua model (Spatie)

---

## 6. Routing Structure

| Group        | Prefix                                                           | Middleware        | Controller                                                 |
| ------------ | ---------------------------------------------------------------- | ----------------- | ---------------------------------------------------------- |
| Auth         | `/login`, `/logout`                                              | guest/none        | AuthController                                             |
| Dashboard    | `/dashboard`                                                     | auth              | DashboardController                                        |
| Pegawai      | `/pegawai`                                                       | auth              | PegawaiController (resource)                               |
| Riwayat      | `/riwayat/{type}`                                                | auth              | RiwayatController                                          |
| Reports      | `/kgb`, `/kenaikan-pangkat`, `/pensiun`, `/duk`, `/satyalencana` | auth              | Dedicated controllers                                      |
| Export       | `/export/{type}/{format}`                                        | auth              | ExportController                                           |
| Admin        | `/admin/*`                                                       | auth + superadmin | TabelGajiController, GolonganController, JabatanController |
| Profile      | `/profile`                                                       | auth              | ProfileController                                          |
| Activity Log | `/activity-log`                                                  | auth              | ActivityLogController                                      |
| Document     | `/dokumen/{type}/{id}`                                           | auth              | DocumentController                                         |

---

## 7. Key Integration Points

### Hukuman Disiplin ↔ KGB & Kenaikan Pangkat

- `KGBService`: Cek sanksi `PenundaanKgb` aktif → geser jatuh tempo KGB
- `KenaikanPangkatService`: Cek sanksi `PenundaanPangkat` (geser masa kerja), `PenurunanPangkat` (turunkan golongan + reset TMT), dan semua hukuman aktif → blokir eligibilitas
- `RiwayatService`: Handle Type 2 hukdis (hard-update: insert demotion record ke riwayat_pangkats/jabatans), pemulihan (insert restoration + rekalkulasi gaji)

### Tabel Gaji ↔ KGB

- `KGBCalculationService`: Lookup `tabel_gajis` berdasarkan `golongan_id` dan `masa_kerja_tahun` untuk menghitung gaji baru
- Digunakan saat create riwayat KGB via `RiwayatService`

### One-Stop Pegawai Creation Flow

- `PegawaiService@create`: Menerima `golonganId` dan `jabatanId` bersama `PegawaiDTO`
- Di dalam `DB::transaction`: (1) Lookup `gaji_pokok` dari `tabel_gajis` (masa_kerja_tahun=0), (2) Create Pegawai, (3) Auto-create `RiwayatPangkat` (tmt=tmt_cpns), (4) Auto-create `RiwayatJabatan` (tmt=tmt_cpns)
- `PegawaiFactory` memiliki `afterCreating` hook yang meniru flow ini untuk testing/seeding

### Hukdis Duration Enforcement (PP 94/2021)

- `RiwayatService@storeHukuman` dan `updateHukuman`: Jika tingkat hukuman Sedang atau Berat, `durasi_tahun` di-force ke 1 tahun
- Form hukdis (create & edit) menampilkan field durasi sebagai readonly dengan value=1
