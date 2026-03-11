# STATE.md — Development Status

> Status perkembangan aplikasi SIMPEG Kemenipas.  
> Terakhir diperbarui: **11 Maret 2026**

---

## Current Status — Fitur yang Sudah Selesai

### Core Data & Models

- [x] 21 Eloquent Models dengan relationships lengkap (termasuk 8 model master data pegawai baru)
- [x] 5 PHP Enums aktif (JenisJabatan, JenisSanksi, RumpunJabatan, StatusHukdis, TingkatHukuman) — 4 enum biodata (Agama, GolonganDarah, JenisKelamin, StatusPernikahan) deprecated, diganti master data tables
- [x] 20 database migrations (termasuk normalisasi FK pegawai)
- [x] 6 seeders (User, MasterData, GolonganPangkat, Pegawai, TabelGaji, Database)
- [x] PegawaiFactory (with afterCreating hook for auto riwayat)
- [x] Activity logging (Spatie) pada semua model utama
- [x] **`pegawais.gaji_pokok`** — Kolom denormalized (cache), disinkronisasi otomatis via `RiwayatKgbObserver` dan `RiwayatPangkatObserver` (model events). Bukan manual update.
- [x] **Cache Layer** — `Cache::remember()` (TTL 5 menit) untuk DashboardService (per-filter) dan PegawaiService career timeline (per-pegawai). Invalidasi otomatis via 3 Observer + 6 model event listeners di AppServiceProvider.

### CRUD & Manajemen

- [x] **Pegawai** — CRUD lengkap + pencarian AJAX + paginasi server-side (DB-level) + validasi NIP 18 digit + **One-Stop Creation Flow** (auto gaji lookup, auto RiwayatPangkat & RiwayatJabatan) + **Tabbed Index** (Aktif / Tidak Aktif / Pensiun) dengan aksi kontekstual per tab
- [x] **Profil Pegawai** — Halaman detail dengan 9 tab: **Timeline Karir** (gabungan kronologis semua riwayat, vertical timeline card), Pangkat, Jabatan, KGB, Hukuman, Pendidikan, Latihan, SKP, Penghargaan. Dilengkapi **Data Completeness Indicator** (progress bar + badge per kategori), **Export PDF Profil** (biodata + semua riwayat), dan **Edit Form Guidance** (gaji_pokok readonly + banner info otomatis)
- [x] **Biodata Pegawai** — Gelar depan/belakang, bagian (5 seksi Kanim), tipe pegawai (PNS/CPNS/PPPK), status kepegawaian (Aktif/Tidak Aktif/Pensiun), unit kerja default Kanim Jakut — **semua 8 atribut dinormalisasi ke tabel master data dengan FK**
- [x] **7 Riwayat Kepegawaian** — CRUD untuk Pangkat, Jabatan, KGB, Hukuman Disiplin, Pendidikan, Latihan Jabatan, Penilaian Kinerja
- [x] **Master Data Jabatan** — CRUD + filter rumpun + toggle active (SuperAdmin only)
- [x] **Master Data Golongan/Pangkat** — CRUD + toggle active (SuperAdmin only) — _refactor dari Enum ke tabel database_
- [x] **Master Data Tabel Gaji** — CRUD per golongan × masa kerja (SuperAdmin only)
- [x] **Master Data Pegawai** — CRUD generik untuk 8 tabel referensi (Tipe Pegawai, Status Kepegawaian, Bagian, Unit Kerja, Jenis Kelamin, Agama, Status Pernikahan, Golongan Darah) via `MasterDataController` — sidebar "Master Data Pegawai" dengan 8 link dinamis
- [x] **Document Management** — Upload file SK (PDF, maks 5MB) + link Google Drive opsional + inline PDF preview di browser + penamaan file bermakna (`NIP_Module_Timestamp_NamaAsli.pdf`) + kolom "Dokumen" di semua tab riwayat (show.blade.php) + link "Lihat Dokumen" di semua form edit
- [x] **UX: Tab Retention** — Setelah CRUD riwayat, halaman profil pegawai otomatis kembali ke tab yang sedang aktif via URL fragment (`#tab-{type}`)
- [x] **UX: Flash Messages** — Alert sukses/error yang deskriptif dengan icon, judul, pesan detail (termasuk info dokumen yang diunggah), dan tombol dismiss

### Monitoring & Laporan

- [x] **Dashboard** — Ringkasan data pegawai + chart distribusi (golongan, gender, usia, unit kerja) + alert KGB/Pensiun
- [x] **Monitoring KGB** — Alert jatuh tempo, eligibilitas, kalkulasi gaji otomatis (PP 15/2019), integrasi hukdis (penundaan KGB)
- [x] **Kenaikan Pangkat** — Analisis eligibilitas 4 syarat (masa kerja, SKP, latihan, hukuman disiplin), proyeksi periode April/Oktober, integrasi hukdis (penundaan + penurunan pangkat)
- [x] **Alert Pensiun** — Level alert (Hijau/Kuning/Merah/Hitam) berdasarkan BUP + **Workflow Proses Pensiun** (alert → proses SK → nonaktifkan pegawai) + **Dokumen SK Pensiun** (upload PDF + tautan Google Drive fallback)
- [x] **DUK** — Ranking otomatis sesuai hierarki BKN
- [x] **Satyalencana** — Identifikasi kandidat 10/20/30 tahun masa kerja + filter hukdis

### Hukuman Disiplin (PP 94/2021)

- [x] 3 status (Aktif, Selesai, Dipulihkan) + 6 jenis sanksi + 3 tingkat hukuman
- [x] Type 1 — Penundaan (soft-block): Penundaan KGB + Penundaan Pangkat
- [x] Type 2 — Penurunan (hard-update): Penurunan Pangkat, Penurunan Jabatan, Pembebasan Jabatan → auto-insert demotion records
- [x] Durasi hukuman Sedang/Berat di-hardcode 1 tahun sesuai PP 94/2021 (enforced di service + readonly di form)
- [x] Pemulihan — Restore pangkat/jabatan + rekalkulasi gaji otomatis
- [x] Integrasi blokir KGB dan kenaikan pangkat

### Export & Reporting

- [x] Export PDF (barryvdh/dompdf) untuk Dashboard, DUK, KGB, Pensiun, Kenaikan Pangkat, Satyalencana, **Profil Individual Pegawai**
- [x] Export Excel (Maatwebsite) untuk DUK, KGB, Pensiun, Kenaikan Pangkat, Satyalencana

### Auth & Security

- [x] Login/logout
- [x] Role sederhana (SuperAdmin, HR) via kolom `role` di tabel `users`
- [x] Middleware `superadmin` untuk route admin
- [x] Profil & ganti password
- [x] Activity log (audit trail)

---

## Active Development

_Tidak ada fitur yang sedang aktif dikerjakan saat ini._

### Recently Completed

- **GAP-48: Career Timeline View** — Tab "Timeline Karir" ditambahkan sebagai tab pertama (default aktif) di halaman profil pegawai. Merge kronologis semua 8 riwayat (pangkat, jabatan, KGB, hukdis, pendidikan, latihan, SKP, penghargaan) dalam vertical timeline dengan year separator, color-coded dot markers, dan card per event. Logic di `PegawaiService::getCareerTimeline()` dengan `Cache::remember()` (5 min TTL). Invalidasi via semua 3 Observer + 6 model event listeners di `AppServiceProvider`.
- **GAP-47: Export PDF Profil Individual** — `PegawaiController::exportPdf()` via DomPDF, template `exports/pegawai-profile-pdf.blade.php` (portrait A4, inline CSS). Berisi biodata lengkap + 8 tabel riwayat. Route `GET pegawai/{pegawai}/export-pdf`. Tombol "Export PDF" di header halaman show.
- **GAP-46: Data Completeness Indicator** — Progress bar di halaman profil pegawai menunjukkan kelengkapan data (8 riwayat). Badge hijau✓/kuning⚠ per kategori. Amber dot pada tab yang belum ada datanya.
- **GAP-45: Edit Form Guidance** — Field `gaji_pokok` di form edit pegawai ditampilkan readonly (format Rp). Banner informasi biru menjelaskan bahwa golongan, jabatan, dan gaji pokok dikelola otomatis melalui riwayat.
- **GAP-42: Dashboard Caching** — `DashboardService` menggunakan `Cache::remember()` (5 menit) untuk `getDashboardData()` (key per-filter md5 hash) dan `getFilterOptions()`. Invalidasi via `DashboardService::clearCache()` di semua Observer. Tracked keys pattern karena file cache driver tidak mendukung tags.
- **GAP-41: DB-level Pagination Pegawai** — `PegawaiService::getPaginatedByStatus()` menggunakan `->paginate()` langsung dari database (bukan in-memory slicing). Search filter diterapkan di query level. Pagination JS di index.blade.php menggunakan server-returned `last_page`/`current_page` dengan debounce 300ms.
- **GAP-38 & GAP-39: Satyalencana Bug Fixes** — Fixed `golongan_ruang->label()` error (diganti dengan eager-loaded `golongan->label`). Fixed hukdis check terlalu luas (ditambahkan `->filter(fn($h) => $h->isAktif())` sebelum disqualification).
- **Pagination & Search Halaman Master Data + Activity Log + Button Fix** — MasterDataController di-upgrade dari `::get()` ke `->paginate(15)->withQueryString()` dengan filter `?search=` pada kolom nama. View `master-data/index` mendapat form pencarian, row numbering berbasis `firstItem() + $loop->index`, dan `{{ $items->links() }}`. ActivityLogController mendapat filter `?search=` pada deskripsi. View `activity-log/index` mendapat form pencarian dan info count. Tombol "Proses" di halaman Kenaikan Pangkat distandarkan ke `bg-emerald-600 text-white` dengan SVG checkmark icon (konsisten dengan KGB dan Pensiun). CSS di-rebuild via `npm run build`.
- **Server-Side Pagination (GAP-41 monitoring)** — Semua 5 halaman monitoring (KGB, Kenaikan Pangkat, Pensiun, Satyalencana, DUK) dikonversi dari client-side JS pagination ke server-side pagination via `LengthAwarePaginator`. Trait `PaginatesArray` (di `app/Http/Controllers/Traits/`) menerima array hasil kalkulasi Service, menerapkan filter `?search=` (NIP/Nama), lalu memotong per halaman (15 item). View menggunakan `{{ $data->links() }}` (Tailwind). Filter tabs (level, milestone) mempertahankan search parameter. Client-side JS pagination dihapus seluruhnya.
- **Dokumen SK Pensiun (GDrive Fallback)** — Dual-document support pada form proses pensiun: upload file PDF SK (max 5MB via `DocumentUploadService`) dan/atau tautan Google Drive. Kolom baru: `file_sk_pensiun_path`, `link_sk_pensiun_gdrive`. Tab Pensiun di index pegawai menampilkan kolom "Dokumen SK" dengan tombol dinamis (Lihat PDF / Google Drive / Tidak ada dokumen). `cancelPensiun()` otomatis menghapus file yang di-upload. Route `dokumen/pensiun/{id}` ditambahkan ke `DocumentController`.
- **Tabbed Employee Index + Fallback Mechanisms** — Halaman index pegawai sekarang memiliki 3 tab (Aktif / Tidak Aktif / Pensiun) dengan data isolation via `getByStatus()`. Tab Aktif: Detail/Edit/Hapus. Tab Tidak Aktif: Aktifkan Kembali (reactivate). Tab Pensiun: Batalkan Pensiun (cancelPensiun → nullify 4 kolom SK + restore). AJAX pagination per tab dengan search. PATCH confirmation modal.
- **Pensiun Processing Workflow (GAP-35)** — `PensiunService::processPensiun()` mengeset status_kepegawaian → Pensiun, is_active → false, dan merekam 4 field SK pensiun. Form proses dengan data pre-filled dari alert. Tombol "Proses" di baris alert Hitam/Merah.
- **Salary Calculator Service & Observer Pattern — Tongkat Estafet TMT** — `SalaryCalculatorService` sebagai single source of truth untuk salary resolution (TabelGaji lookup dengan fallback ke closest lower MKG). Method `syncCurrentSalary()` mengimplementasikan logika "Tongkat Estafet TMT": bandingkan TMT terbaru antara RiwayatPangkat dan RiwayatKgb, yang paling recent menentukan `gaji_pokok`. `RiwayatKgbObserver` dan `RiwayatPangkatObserver` menggunakan event `saved` (created+updated) dan `deleted` untuk trigger sync otomatis. Manual gaji update dihapus dari semua Controllers/Services. `calculateNextKgbDate()` menghitung estimasi KGB selanjutnya: MAX(tmt_kgb, tmt_pangkat) + 2 tahun.
- **Observer-Driven Seeding** — `PegawaiFactory` dan `PegawaiSeeder` tidak lagi meng-hardcode `gaji_pokok`. Factory membuat logical timeline (Pangkat awal + KGB setiap 2 tahun), Seeder membuat Pangkat progression + KGB timeline penuh. Observer fires pada setiap record → gaji_pokok akhir otomatis sesuai MKG aktual. `KGBCalculationService` pre-fill MKG form: lastKgb.mkg+2, atau total masa kerja jika belum ada KGB.
- **Normalisasi 8 Atribut Pegawai ke Master Data Tables** — Tipe Pegawai, Status Kepegawaian, Bagian, Unit Kerja, Jenis Kelamin, Agama, Status Pernikahan, Golongan Darah. Semuanya sekarang disimpan di tabel terpisah dengan FK constraint. CRUD generik via `MasterDataController`. 4 Enum biodata (Agama, JenisKelamin, StatusPernikahan, GolonganDarah) deprecated, diganti 8 model master data baru. Status Kepegawaian ditampilkan sebagai badge di halaman profil pegawai.

---

## Next Steps

-
