# SIMPEG - Sistem Informasi Manajemen Pegawai

Aplikasi manajemen kepegawaian berbasis web yang dibangun menggunakan **Laravel 12**, **SQLite**, dan **Tailwind CSS v4**.

## Fitur Utama

- **Dashboard** — Ringkasan data pegawai dengan chart distribusi (golongan, gender, usia, unit kerja) dan alert KGB/Pensiun
- **Manajemen Pegawai** — CRUD lengkap dengan pencarian AJAX dan paginasi server-side
- **Riwayat Kepegawaian** — 7 modul riwayat (Pangkat, Jabatan, KGB, Hukuman Disiplin, Pendidikan, Latihan Jabatan, Penilaian Kinerja)
- **Monitoring KGB** — Alert otomatis pegawai yang mendekati/eligible kenaikan gaji berkala (siklus 2 tahun), kalkulasi gaji baru otomatis berdasarkan PP 15/2019
- **Kenaikan Pangkat** — Analisis eligibilitas berdasarkan syarat masa kerja, SKP, latihan, dan hukuman disiplin
- **Alert Pensiun** — Monitoring pensiun berdasarkan BUP dengan level alert (Hijau/Kuning/Merah/Hitam)
- **DUK** — Daftar Urut Kepangkatan dengan ranking otomatis sesuai hierarki BKN
- **Satyalencana** — Identifikasi kandidat penghargaan Satyalencana Karya Satya (10/20/30 tahun)
- **Export PDF & Excel** — Semua laporan (KGB, Pensiun, DUK, Kenaikan Pangkat, Satyalencana) bisa diekspor ke PDF dan Excel
- **Activity Log** — Pencatatan otomatis setiap perubahan data pegawai dan riwayat menggunakan Spatie Activity Log
- **Profil & Ganti Password** — Manajemen profil user dan update password
- **Autentikasi** — Login/logout dengan role-based access (SuperAdmin, HR)

## Persyaratan Sistem

| Komponen | Versi Minimum |
|---|---|
| PHP | 8.2+ |
| Composer | 2.x |
| Node.js | 18+ |
| NPM | 9+ |

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
- 2 user (admin@simpeg.go.id / password)
- 25 jabatan master data
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
- Email: `admin@simpeg.go.id`
- Password: `password`

## Struktur Project

```
app/
├── DTOs/               # 8 DTO class (PegawaiDTO + 7 Riwayat DTO)
├── Enums/              # 7 PHP Enum (JenisKelamin, Agama, GolonganRuang, dll)
├── Exports/            # 5 Excel Export class (KGB, Pensiun, DUK, dll)
├── Http/
│   ├── Controllers/    # 13 Controller (Auth, Dashboard, Pegawai, Export, dll)
│   ├── Requests/       # 19 FormRequest (Store/Update untuk setiap entitas)
│   └── Resources/      # 1 API Resource (PegawaiResource)
├── Models/             # 12 Eloquent Model (Pegawai, Jabatan, TabelGaji, dll)
├── Providers/          # AppServiceProvider
└── Services/           # 11 Service Class (business logic layer)
database/
├── migrations/         # 11 migration files (15+ tabel)
├── seeders/            # 5 seeder (User, MasterData, Pegawai, TabelGaji, Database)
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
├── exports/                       # 5 template PDF (duk, kgb, pensiun, dll)
├── activity-log/                  # Riwayat aktivitas sistem
└── profile/                       # Profil & ganti password
```

## Teknologi

| Kategori | Teknologi |
|---|---|
| **Backend** | Laravel 12 (PHP 8.2+) |
| **Database** | SQLite |
| **Frontend** | Blade Templates + Tailwind CSS 4.x |
| **Build Tool** | Vite |
| **Charts** | Chart.js 4 (CDN) |
| **Font** | Inter (Google Fonts) |
| **PDF Export** | barryvdh/laravel-dompdf 3.x |
| **Excel Export** | maatwebsite/excel 3.x |
| **Activity Log** | spatie/laravel-activitylog 4.x |

## Skema Database

| Tabel | Deskripsi |
|---|---|
| `users` | Data user login dengan role |
| `jabatans` | Master data jabatan (BUP, eselon, kelas) |
| `pegawais` | Data utama pegawai (NIP, biodata, ASN) |
| `riwayat_pangkats` | Riwayat kenaikan pangkat |
| `riwayat_jabatans` | Riwayat penempatan jabatan |
| `riwayat_kgbs` | Riwayat KGB (gaji lama/baru) |
| `riwayat_hukuman_disiplins` | Riwayat hukuman disiplin |
| `riwayat_pendidikans` | Riwayat pendidikan formal |
| `riwayat_latihan_jabatans` | Riwayat diklat/pelatihan |
| `riwayat_penghargaans` | Riwayat penghargaan |
| `penilaian_kinerjas` | Penilaian kinerja (SKP) |
| `tabel_gajis` | Tabel gaji PNS PP 15/2019 |
| `activity_log` | Log aktivitas perubahan data (Spatie) |

## Service Layer

Semua business logic dipisahkan ke Service class untuk menjaga controller tetap tipis:

| Service | Tanggung Jawab |
|---|---|
| `PegawaiService` | CRUD pegawai, pencarian, paginasi |
| `RiwayatService` | CRUD 7 jenis riwayat dengan DB transaction |
| `JabatanService` | Query master data jabatan |
| `KGBService` | Monitoring status KGB, jatuh tempo, eligibilitas |
| `KGBCalculationService` | Kalkulasi gaji baru berdasarkan tabel gaji PP 15/2019 |
| `KenaikanPangkatService` | Analisis syarat kenaikan pangkat |
| `PensiunService` | Alert pensiun berdasarkan BUP |
| `DUKService` | Ranking DUK sesuai hierarki BKN |
| `SatyalencanaService` | Identifikasi kandidat Satyalencana |
| `DashboardService` | Agregasi data dashboard + chart |
| `DocumentUploadService` | Upload dan manajemen file dokumen |

## Akun Default

| Role | Email | Password |
|---|---|---|
| SuperAdmin | admin@simpeg.go.id | password |
| HR | hr@simpeg.go.id | password |

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
