# SIMPEG - Sistem Informasi Manajemen Pegawai

Aplikasi manajemen kepegawaian berbasis web yang dibangun menggunakan **Laravel 12**, **SQLite**, dan **Tailwind CSS**.

## Fitur Utama

- **Dashboard** — Ringkasan data pegawai dengan chart distribusi (golongan, gender, usia, unit kerja) dan alert KGB/Pensiun
- **Manajemen Pegawai** — CRUD lengkap dengan pencarian dan paginasi server-side
- **Riwayat Kepegawaian** — 7 modul riwayat (Pangkat, Jabatan, KGB, Hukuman Disiplin, Pendidikan, Latihan Jabatan, Penilaian Kinerja)
- **Monitoring KGB** — Alert otomatis pegawai yang mendekati/eligible kenaikan gaji berkala (siklus 2 tahun)
- **Kenaikan Pangkat** — Analisis eligibilitas berdasarkan syarat masa kerja, SKP, latihan, dan hukuman disiplin
- **Alert Pensiun** — Monitoring pensiun berdasarkan BUP dengan level alert (Hijau/Kuning/Merah/Hitam)
- **DUK** — Daftar Urut Kepangkatan dengan ranking otomatis sesuai hierarki BKN
- **Satyalencana** — Identifikasi kandidat penghargaan Satyalencana Karya Satya (10/20/30 tahun)
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
# Buat file database jika belum ada
touch database/database.sqlite   # Linux/Mac
# atau buat file kosong di database/database.sqlite di Windows
```

### 5. Jalankan Migrasi & Seeder

```bash
php artisan migrate --seed
```

Ini akan membuat semua tabel dan mengisi data sampel:
- 2 user (admin@simpeg.go.id / password)
- 25 jabatan master data
- 100 pegawai dengan riwayat lengkap

### 6. Build Assets Frontend

```bash
npm install
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
├── Enums/              # 7 PHP Enum (JenisKelamin, Agama, GolonganRuang, dll)
├── Http/Controllers/   # 9 Controller (Auth, Dashboard, Pegawai, Riwayat, dll)
├── Models/             # 10 Eloquent Model (Pegawai, Jabatan, Riwayat*, dll)
├── Services/           # 7 Service Class (business logic layer)
database/
├── migrations/         # 4 migration files (10+ tabel)
├── seeders/            # 3 seeder (MasterData, Pegawai, User)
resources/views/
├── layouts/app.blade.php          # Layout utama dengan sidebar
├── auth/login.blade.php           # Halaman login
├── dashboard/index.blade.php      # Dashboard dengan chart
├── pegawai/                       # 5 view (index, show, create, edit, _form)
├── riwayat/                       # 14 view (create/edit untuk 7 riwayat)
├── kgb/                           # Monitoring KGB
├── kenaikan-pangkat/              # Eligibilitas kenaikan pangkat
├── pensiun/                       # Alert pensiun
├── duk/                           # Daftar Urut Kepangkatan
└── satyalencana/                  # Kandidat Satyalencana
```

## Teknologi

- **Backend**: Laravel 12 (PHP 8.2+)
- **Database**: SQLite
- **Frontend**: Blade Templates + Tailwind CSS 4.x
- **Build Tool**: Vite
- **Charts**: Chart.js 4 (CDN)
- **Font**: Inter (Google Fonts)

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
