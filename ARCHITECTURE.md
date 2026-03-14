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
| **Searchable Select** | TomSelect                      | CDN v2.4.3               |
| **Input Masking**     | IMask.js                       | CDN v7.6.1               |
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
3. **PHP Enums** — Data master statis hukdis (JenisSanksi, TingkatHukuman, StatusHukdis) menggunakan PHP 8.1 Backed Enum.
    - `JenisJabatan` (Administrasi, Pimpinan Tinggi, Fungsional)
    - `TingkatPendidikan` (SD, SMP, SMA, D3, S1, S2, S3)
4. **Dynamic Master Data** — Data master yang perlu CRUD (Golongan/Pangkat, Jabatan, Tabel Gaji, **serta 9 tabel referensi pegawai/riwayat**:
    - **`tipe_pegawais`**: Tipe Pegawai (PNS, CPNS, PPPK, PPNPN)
    - **`rumpun_jabatans`**: Kategori/Rumpun Jabatan (Struktural, Imigrasi, Pemasyarakatan, dll).
    - **`master_pendidikans`**: Jenjang Pendidikan (S3=6, S2=5, S1=4, dll) digunakan untuk pembobotan DUK.
    - `status_kepegawaians`, `bagians`, `unit_kerjas`, `jenis_kelamins`, `agamas`, `status_pernikahans`, `golongan_darahs`) disimpan di tabel database, dilayani oleh dedicated Service/Controller.
5. **Activity Logging** — Semua perubahan data pegawai dan riwayat dicatat otomatis via Spatie `LogsActivity` trait.
6. **Tab Retention via URL Fragment** — Redirect dari `RiwayatController` menyertakan `#tab-{type}` fragment. JavaScript di `show.blade.php` membaca `window.location.hash` pada `DOMContentLoaded` dan mengaktifkan tab yang sesuai.
7. **Descriptive Flash Messages** — Semua flash message `success`/`error` harus deskriptif (menyebut nama modul + aksi + info dokumen jika ada). Layout (`app.blade.php`) menampilkan alert dengan icon, judul bold, pesan detail, dan tombol dismiss.
8. **Model Observers — "Tongkat Estafet TMT"** — Kolom denormalized `pegawais.gaji_pokok` disinkronisasi otomatis via Laravel Observers (`RiwayatKgbObserver`, `RiwayatPangkatObserver`) menggunakan event `saved` (created + updated) dan `deleted`. Logika inti: siapapun yang memegang TMT terbaru (KGB atau Pangkat) menjadi penentu gaji pokok saat ini. Semua sync di-delegasi ke `SalaryCalculatorService::syncCurrentSalary()`. `PegawaiObserver` menangani invalidasi cache dashboard.
9. **PPPK Business Rules (BKN Compliance)** — Pegawai PPPK (Pegawai Pemerintah dengan Perjanjian Kerja) **tidak memiliki hak Kenaikan Pangkat** sesuai PP No. 49 Tahun 2018 tentang Manajemen PPPK. Enforcement tiga lapis:
    - **Backend**: `StorePangkatRequest::authorize()` dan `UpdatePangkatRequest::authorize()` memeriksa rumpun jabatan terakhir pegawai. Jika rumpun = 'PPPK', `abort(403)` dengan pesan BKN.
    - **Controller**: `RiwayatController::createPangkat()` mem-redirect PPPK sebelum menampilkan form create.
    - **UI**: Tab Pangkat di `show.blade.php` menampilkan banner informasi (biru) alih-alih tombol "+ Tambah" untuk pegawai PPPK.
    - **Catatan**: JFT (Jabatan Fungsional Tertentu) **tetap memiliki hak kenaikan pangkat** melalui Angka Kredit. Semua pegawai (termasuk PPPK) **tetap menerima KGB** (Kenaikan Gaji Berkala).
    - **Seeder**: `PegawaiSeeder` Group 6 membuat 10 pegawai PPPK dengan tipe_pegawai PPPK, hanya initial pangkat (tanpa progression), dan KGB normal. `PegawaiFactory::afterCreating()` mendeteksi PPPK dan skip pangkat progression.
10. **Employee Lifecycle State Transitions** — Pegawai memiliki 3 status: Aktif, Tidak Aktif, Pensiun. Transisi:
    - **Aktif → Tidak Aktif**: via `PegawaiService::delete()` (soft-delete + `is_active=false`). Reversible via `reactivate()`.
    - **Aktif → Pensiun**: via `PensiunService::processPensiun()` (set status, `is_active=false`, record 4 field SK pensiun + opsional `file_sk_pensiun_path` dan `link_sk_pensiun_gdrive`). Reversible via `PegawaiService::cancelPensiun()` (nullify 6 field + delete file upload + restore).
    - Halaman index pegawai menggunakan **Tabbed UI** dengan data isolation via `getByStatus()`: Aktif (`is_active=true`), Tidak Aktif (`is_active=false AND tmt_pensiun IS NULL`), Pensiun (`is_active=false AND tmt_pensiun IS NOT NULL`). Masing-masing tab memiliki aksi kontekstual (Detail/Edit/Hapus, Aktifkan Kembali, Batalkan Pensiun).
11. **Server-Side Pagination (Monitoring Pages)** — Semua halaman monitoring (KGB, Kenaikan Pangkat, Pensiun, Satyalencana, DUK) menggunakan server-side pagination via `LengthAwarePaginator`. Karena Service-layer menjalankan kalkulasi bisnis kompleks (eligibilitas, hukdis, gaji) yang tidak bisa dipindahkan ke SQL, pattern-nya: Service mengembalikan array penuh → Controller menerapkan `?search=` filter → `PaginatesArray` trait memotong per halaman (15 item) → View merender hanya 1 halaman + `{{ $data->links() }}`. Trait `PaginatesArray` ada di `app/Http/Controllers/Traits/`. Pegawai list menggunakan DB-level `->paginate()` via `PegawaiService::getPaginatedByStatus()`.
12. **Cache Layer** — Dua domain menggunakan `Cache::remember()` dengan TTL 5 menit:
    - **Dashboard**: `DashboardService` meng-cache `getDashboardData()` (key per filter hash) dan `getFilterOptions()`. Invalidasi via `DashboardService::clearCache()` yang dipanggil oleh semua 3 Observer (`PegawaiObserver`, `RiwayatKgbObserver`, `RiwayatPangkatObserver`) + 6 model event listeners.
    - **Career Timeline**: `PegawaiService::getCareerTimeline()` meng-cache timeline gabungan per pegawai (key: `career_timeline_{id}`). Invalidasi via `PegawaiService::clearTimelineCache()` yang dipanggil oleh semua Observer + model event listeners di `AppServiceProvider`.
13. **Career Timeline View** — Tab "Timeline Karir" di halaman profil pegawai menampilkan gabungan kronologis seluruh 8 jenis riwayat dalam satu vertical timeline. Data di-merge dan di-sort descending di `PegawaiService::buildCareerTimeline()`. Setiap item memiliki type, color, icon, title, subtitle, dan detail. Grouped by year dengan separator.
14. **Data Completeness Indicator** — Halaman profil pegawai menampilkan progress bar kelengkapan data (8 jenis riwayat) dengan badge per kategori (hijau ✓ / kuning ⚠). Tab kosong juga mendapat dot warning kuning.
15. **Export PDF Profil Pegawai** — `PegawaiController::exportPdf()` menggunakan DomPDF untuk generate PDF profil individual (biodata + semua 8 riwayat dalam tabel). Template di `exports/pegawai-profile-pdf.blade.php`.
16. **Edit Form Guidance** — Form edit pegawai menampilkan banner informasi bahwa gaji pokok, golongan, dan jabatan dikelola otomatis. Field `gaji_pokok` ditampilkan readonly.
17. **Narrative Audit Logging (Bahasa Indonesia)** — Semua `setDescriptionForEvent()` menggunakan deskripsi naratif Bahasa Indonesia yang human-readable, bukan default Spatie (e.g. "created", "updated"). Tiga kategori format:
    - **Category A (Pegawai)**: `"{Aksi} data pegawai #{id} atas nama {nama_lengkap}"` — contoh: "Mengubah data pegawai #53 atas nama Yanto"
    - **Category B (Riwayat)**: `"{Aksi} {NamaModul} untuk pegawai #{pegawai_id} atas nama {nama_pegawai}"` — contoh: "Menambah Riwayat KGB untuk pegawai #53 atas nama Yanto"
    - **Category C (Master Data)**: `"{Aksi} Master {NamaModel} #{id} ({nama_atau_keterangan})"` — contoh: "Menghapus Master Jabatan #2 (Polsuspas)"
    - **State Transitions (Controller)**: Aksi non-CRUD seperti pensiun di-log eksplisit via `activity()->performedOn()->log()` di Controller — contoh: "Memproses pensiun untuk pegawai #5 atas nama Budi"
    - Kata kerja: `Menambah` (created), `Mengubah` (updated), `Menghapus` (deleted)
18. **Satyalencana Reset Argo (PP 94/2021)** — `SatyalencanaService::getEligibleCandidates()` menerapkan aturan "Reset Argo": hukuman disiplin tingkat **Sedang/Berat** yang sudah selesai me-reset counter masa kerja murni. `startDate` di-override dari `tmt_cpns` ke `tmt_selesai_hukuman` terbaru. Hukdis **Ringan** diabaikan (tidak me-reset). Pegawai **PPPK** di-exclude seluruhnya. Jika pegawai masih menjalani hukdis Sedang/Berat aktif, di-skip dari kandidat. Output menyertakan `tanggal_mulai_hitung`, `is_reset`, dan `masa_kerja_tahun` (= masa kerja murni). Edge-case verification via `SatyalencanaEdgeCaseSeeder` (3 test cases).
19. **UX Overhaul — Frontend Standardization** — Empat pilar usability diterapkan secara global:
    - **Data Entry Efficiency**: TomSelect (CDN v2.4.3) pada class `.searchable-select` di semua dropdown Jabatan, Golongan, Unit Kerja, Bagian, dan demotion/restoration selects. IMask.js (CDN v7.6.1) pada input NIP (pattern `00000000 000000 0 000`, unmasked sebelum submit) dan input gaji (`data-mask="currency"`). Smart date defaults via regex `tanggal_lahir|tmt_|tanggal_sk|tanggal_ijazah|tanggal_pemulihan` → `max=today`.
    - **Error Prevention**: Global anti-double-submit pada semua `<form>` (disable button + spinner "Memproses..." + `dataset.submitting` guard). Semua dialog konfirmasi menggunakan native HTML5 `<dialog>` (bukan `<div>` overlay) — `confirmDelete()` di layout, `confirmPatch()` di pegawai index, `openPulihkanModal()` di show. Zero native `confirm()` calls remaining.
    - **Findability**: Sticky table headers (`sticky top-0 bg-slate-50 z-10`) di pegawai index dan DUK. Global Command Palette di navbar (`Ctrl+K` / `Cmd+K` focus): debounced AJAX (300ms) ke `pegawai.data` endpoint, renders 5 hasil cepat (avatar, nama, NIP) sebagai dropdown link.
    - **Contextual Help**: `<x-tooltip text="...">` component (Tailwind CSS `group-hover`, positioned bottom-full → up) pada 12 label TMT di form pegawai, riwayat, dan process. `<x-empty-state title="..." message="..." colspan="N">` component menggantikan 8 generic "Belum ada data" texts di show.blade.php (e.g., "Pegawai ini memiliki rekam jejak bersih tanpa hukuman disiplin.").
20. **Design System — Government Authority Palette** — Enterprise-grade UI refactoring menerapkan visual consistency di seluruh aplikasi:
    - **Color Palette**: Primary `bg-blue-800` (hover: `bg-blue-900`), Danger `bg-rose-600` (hover: `bg-rose-700`), Success `bg-emerald-600`, Warning `bg-amber-500`. Surface `bg-white`, Background `bg-slate-50`, Borders `border-slate-200`. Focus ring `focus:ring-blue-800/30 focus:border-blue-800`. Semua `red-*` utility classes dimigrasi ke `rose-*`.
    - **Light Mode Only**: Sidebar dikonversi dari dark gradient (`bg-slate-900`) ke light (`bg-white border-r`). Active nav: `bg-blue-50 text-blue-800 border-r-4 border-blue-800`. Login page dari glass-morphism gelap ke solid white card. Semua `dark:` classes dihapus dari welcome.blade.php. Font diubah ke "Inter".
    - **Blade Component Library**: 8 core components (`<x-card>`, `<x-button>`, `<x-badge>`, `<x-input>`, `<x-select>`, `<x-label>`, `<x-input-error>`, `<x-table-wrapper>`) dengan palette baked-in. `<x-button>` mendukung 5 variant (primary/secondary/danger/outline/ghost) × 4 size (xs/sm/md/lg). `<x-card>` dengan named slots (header, default, footer).
    - **Component Adoption**: `<x-card>` dan `<x-button>` digunakan di semua form views (pegawai create/edit, 14 riwayat forms, 3 admin forms, 3 process views, profile). Eliminasi manual button/card HTML duplications.
    - **Border Radius**: Cards `rounded-xl`, Buttons `rounded-lg`, Inputs `rounded-lg`, Info boxes `rounded-lg`.

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
│   ├── Enums/                         # PHP 8.1 Backed Enums (hukdis & jabatan saja)
│   │   ├── JenisJabatan.php           #   6 jenis jabatan ASN
│   │   ├── JenisSanksi.php            #   6 jenis sanksi hukdis (PP 94/2021)
│   │   ├── RumpunJabatan.php          #   Imigrasi, Pemasyarakatan, Struktural
│   │   ├── StatusHukdis.php           #   Aktif, Selesai, Dipulihkan
│   │   └── TingkatHukuman.php         #   Ringan, Sedang, Berat
│   │   # (Agama, GolonganDarah, JenisKelamin, StatusPernikahan — deprecated, diganti master tables)
│   │
│   ├── Exports/                       # Maatwebsite Excel export classes
│   │   ├── DUKExport.php
│   │   ├── KenaikanPangkatExport.php
│   │   ├── KGBExport.php
│   │   ├── PensiunExport.php
│   │   └── SatyalencanaExport.php
│   │
│   ├── Http/
│   │   ├── Controllers/               # 18 Controllers (thin, delegasi ke Service)
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
│   │   │   ├── MasterDataController.php  #   Generic CRUD untuk 8 master data pegawai
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
│   ├── Observers/                     # Model Observers (event-driven sync)
│   │   ├── PegawaiObserver.php        #   Clear dashboard + timeline cache on pegawai events
│   │   ├── RiwayatKgbObserver.php     #   Sync pegawai.gaji_pokok + clear caches on KGB events
│   │   └── RiwayatPangkatObserver.php #   Sync pegawai.gaji_pokok + clear caches on Pangkat events
│   │
│   ├── Models/                        # 21 Eloquent Models
│   │   ├── AgamaMaster.php            #   Master agama (table: agamas)
│   │   ├── Bagian.php                 #   Master bagian/seksi
│   │   ├── GolonganDarahMaster.php    #   Master golongan darah (table: golongan_darahs)
│   │   ├── GolonganPangkat.php        #   Master golongan/pangkat (ex-Enum)
│   │   ├── Jabatan.php                #   Master jabatan
│   │   ├── JenisKelaminMaster.php     #   Master jenis kelamin (table: jenis_kelamins)
│   │   ├── Pegawai.php                #   Data pegawai (central entity)
│   │   ├── StatusKepegawaian.php      #   Master status kepegawaian
│   │   ├── StatusPernikahanMaster.php #   Master status pernikahan (table: status_pernikahans)
│   │   ├── TipePegawai.php            #   Master tipe pegawai
│   │   ├── UnitKerja.php              #   Master unit kerja
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
│   └── Services/                      # 14 Service classes (business logic)
│       ├── DashboardService.php       #   Agregasi dashboard + chart data (cached 5 min)
│       ├── DocumentUploadService.php  #   Upload/delete file SK
│       ├── DUKService.php             #   Ranking DUK per aturan BKN
│       ├── GolonganPangkatService.php #   CRUD master golongan/pangkat
│       ├── JabatanService.php         #   CRUD master jabatan
│       ├── KenaikanPangkatService.php #   Analisis eligibilitas kenaikan pangkat
│       ├── KGBCalculationService.php  #   Kalkulasi gaji baru (delegates to SalaryCalculatorService)
│       ├── KGBService.php             #   Monitoring KGB (jatuh tempo, eligibilitas)
│       ├── PegawaiService.php         #   CRUD pegawai + One-Stop Creation Flow + Career Timeline (cached 5 min)
│       ├── PensiunService.php         #   Alert pensiun (level Hijau-Hitam)
│       ├── RiwayatService.php         #   CRUD 7 jenis riwayat + hukdis logic (durasi enforced)
│       ├── SalaryCalculatorService.php #   **Single source of truth** untuk salary resolution (TabelGaji lookup + fallback)
│       ├── SatyalencanaService.php    #   Kandidat Satyalencana (Reset Argo: Sedang/Berat reset counter)
│       └── TabelGajiService.php       #   CRUD tabel gaji PP 15/2019
│
├── bootstrap/                         # Laravel bootstrap
├── config/                            # Konfigurasi (app, auth, database, dll)
├── database/
│   ├── factories/                     # Model factories (UserFactory, PegawaiFactory)
│   ├── migrations/                  *   **Migration Count:** 14 files
│   └── seeders/                       # 7 seeders (User, MasterData, Golongan, Pegawai, TabelGaji, SatyalencanaEdgeCase, Database)
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
│       ├── admin/                     #   Master data (Jabatan, Tabel Gaji, Golongan, 8 Master Data Pegawai)
│       ├── components/                #   Blade components (card, button, badge, input, select, label, input-error, table-wrapper, empty-state, tooltip)
│       ├── exports/                   #   7 template PDF (dashboard, duk, kgb, pensiun, kenaikan-pangkat, satyalencana, pegawai-profile)
│       ├── activity-log/              #   Audit trail
│       └── profile/                   #   Profil & ganti password
│
├── routes/
│   └── web.php                        # Semua route (Auth, CRUD, Reports, Admin)
│
├── storage/                           # Upload, cache, logs
├── tests/                             # PHPUnit tests
└── vendor/                            # Composer dependencies

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

- **`pegawais`** — Central entity, connected to 8 riwayat tables + 8 master data tables via FK. Fields include gelar_depan, gelar_belakang, jenis_kelamin_id, agama_id, status_pernikahan_id, golongan_darah_id, tipe_pegawai_id, status_kepegawaian_id, bagian_id, unit_kerja_id, sk_cpns_path, sk_pns_path
- **`tipe_pegawais`**, **`status_kepegawaians`**, **`bagians`**, **`unit_kerjas`**, **`jenis_kelamins`**, **`agamas`**, **`status_pernikahans`**, **`golongan_darahs`** — 8 master data tables (id + nama unique), FK from `pegawais`
- **`golongan_pangkats`** — Master golongan/pangkat (17 level I/a – IV/e), FK from `riwayat_pangkats` dan `tabel_gajis`
- **`rumpun_jabatans`** — Master data untuk kategori/rumpun jabatan (Struktural, Imigrasi, Pemasyarakatan, JFT, JFU, PPPK).
- **`jabatans`**: Master Data Jabatan (Reference table for `riwayat_jabatans`).
    *   `nama_jabatan`: Name of passing grade/job.
    *   `eselon_level`: Eselon category (0-4).
    *   `kelas_jabatan`: Grade level used for allowance multiplication.
    *   `bup`: Batas Usia Pensiun.
    *   `rumpun_jabatan_id`: FK to `rumpun_jabatans`.
    *   `jenis_jabatan`: Enum (Administrasi, Pimpinan Tinggi, Fungsional).
    *   `is_active`: Soft switch for deprecating old roles.
- **`tabel_gajis`** — Lookup salary matrix (golongan × masa_kerja)
- **`users`** — Authentication, simple `role` string (SuperAdmin/HR)

### Relasi (ERD tersedia di README.md §Skema Database)

- `pegawais` 1→N semua tabel riwayat
- `tipe_pegawais`, `status_kepegawaians`, `bagians`, `unit_kerjas`, `jenis_kelamins`, `agamas`, `status_pernikahans`, `golongan_darahs` 1→N `pegawais`
- `golongan_pangkats` 1→N `riwayat_pangkats`, 1→N `tabel_gajis`
- `jabatans` 1→N `riwayat_jabatans`
- `activity_log` polimorfik ke semua model (Spatie)

---

## 6. Routing Structure

| Group        | Prefix                                                           | Middleware        | Controller                                                                       |
| ------------ | ---------------------------------------------------------------- | ----------------- | -------------------------------------------------------------------------------- |
| Auth         | `/login`, `/logout`                                              | guest/none        | AuthController                                                                   |
| Dashboard    | `/dashboard`                                                     | auth              | DashboardController                                                              |
| Pegawai      | `/pegawai`                                                       | auth              | PegawaiController (resource)                                                     |
| Riwayat      | `/riwayat/{type}`                                                | auth              | RiwayatController                                                                |
| Reports      | `/kgb`, `/kenaikan-pangkat`, `/pensiun`, `/duk`, `/satyalencana` | auth              | Dedicated controllers                                                            |
| Export       | `/export/{type}/{format}`                                        | auth              | ExportController                                                                 |
| Admin        | `/admin/*`                                                       | auth + superadmin | TabelGajiController, GolonganController, JabatanController, MasterDataController |
| Profile      | `/profile`                                                       | auth              | ProfileController                                                                |
| Activity Log | `/activity-log`                                                  | auth              | ActivityLogController                                                            |
| Document     | `/dokumen/{type}/{id}`                                           | auth              | DocumentController                                                               |

---

## 7. Key Integration Points

### Hukuman Disiplin ↔ KGB & Kenaikan Pangkat

- `KGBService`: Cek sanksi `PenundaanKgb` aktif → geser jatuh tempo KGB
- `KenaikanPangkatService`: Cek sanksi `PenundaanPangkat` (geser masa kerja), `PenurunanPangkat` (turunkan golongan + reset TMT), dan semua hukuman aktif → blokir eligibilitas
- `RiwayatService`: Handle Type 2 hukdis (hard-update: insert demotion record ke riwayat_pangkats/jabatans via model-level operations agar Observer aktif), pemulihan (insert restoration record → Observer otomatis recalculate gaji)

### Tabel Gaji ↔ KGB & Kenaikan Pangkat

- **`SalaryCalculatorService`** — Single source of truth untuk salary resolution:
    - `syncCurrentSalary(Pegawai)`: **Tongkat Estafet TMT** — bandingkan `tmt_pangkat` terbaru vs `tmt_kgb` terbaru, yang paling recent menentukan `gaji_pokok`. Jika KGB terbaru → gunakan `gaji_baru`, jika Pangkat terbaru → hitung dari TabelGaji (golongan × MKG).
    - `calculateGaji(golongan_id, mkg_tahun)`: Query `tabel_gajis` dengan exact match, fallback ke closest lower MKG jika exact tidak ditemukan
    - `calculateNextKgbDate(Pegawai)`: Ambil MAX(latest tmt_kgb, latest tmt_pangkat) + 2 tahun. Kenaikan Pangkat me-reset clock KGB.
- `KGBCalculationService`: Delegates `calculateNewSalary()` ke `SalaryCalculatorService`. Menyediakan `getNextKGBSalary()` untuk pre-fill form KGB.
- Digunakan oleh Observers, Controllers, dan monitoring services.

### Data Denormalization — Tongkat Estafet TMT (TMT Relay Baton)

- **`pegawais.gaji_pokok`** adalah kolom denormalized (cache) yang disinkronisasi otomatis oleh Laravel Observers, BUKAN manual di Controllers/Services.
- **Aturan BKN**: Gaji pokok pegawai SELALU ditentukan oleh record terakhir berdasarkan TMT (Terhitung Mulai Tanggal) antara `RiwayatPangkat` dan `RiwayatKgb`. Siapapun yang memegang TMT terbaru menjadi single source of truth.
- **Alur keputusan** (`SalaryCalculatorService::syncCurrentSalary()`):
    1. Ambil `latestPangkat` (order by `tmt_pangkat` DESC) dan `latestKgb` (order by `tmt_kgb` DESC)
    2. Jika keduanya null → `gaji_pokok = 0`
    3. Jika hanya Pangkat → hitung dari TabelGaji (golongan × MKG)
    4. Jika hanya KGB → gunakan `gaji_baru`
    5. Jika keduanya ada → bandingkan TMT: KGB ≥ Pangkat → `gaji_baru`, Pangkat > KGB → hitung dari TabelGaji
- **`RiwayatKgbObserver`** & **`RiwayatPangkatObserver`** (`app/Observers/`):
    - `saved` (fires on create & update): Trigger `syncCurrentSalary()`
    - `deleted`: Trigger `syncCurrentSalary()` — graceful rollback ke record valid sebelumnya
- Observers di-register di `AppServiceProvider@boot`
- Pattern ini menghilangkan duplikasi manual gaji_pokok update di `RiwayatService`, `KenaikanPangkatService`, dan Controllers

### One-Stop Pegawai Creation Flow

- `PegawaiService@create`: Menerima `golonganId` dan `jabatanId` bersama `PegawaiDTO`
- Di dalam `DB::transaction`: (1) Create Pegawai dengan `gaji_pokok=0`, (2) Auto-create `RiwayatPangkat` (tmt=tmt_cpns) → Observer fires → gaji_pokok dihitung dari TabelGaji, (3) Auto-create `RiwayatJabatan` (tmt=tmt_cpns)
- `PegawaiFactory` memiliki `afterCreating` hook yang membuat logical timeline: Initial Pangkat + KGB setiap 2 tahun dari tmt_cpns → Observer fires pada setiap record → gaji_pokok otomatis tersinkronisasi ke level MKG yang benar
- `PegawaiSeeder` membangun timeline realistis: Pangkat progression (setiap 4 tahun naik golongan) + KGB timeline (setiap 2 tahun naik MKG) → dummy data memiliki riwayat yang konsisten dan logis, bukan gaji yang di-hardcode

### Hukdis Duration Enforcement (PP 94/2021)

- `RiwayatService@storeHukuman` dan `updateHukuman`: Jika tingkat hukuman Sedang atau Berat, `durasi_tahun` di-force ke 1 tahun
- Form hukdis (create & edit) menampilkan field durasi sebagai readonly dengan value=1
```
