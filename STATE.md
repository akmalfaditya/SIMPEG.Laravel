# STATE.md — Development Status

> Status perkembangan aplikasi SIMPEG Kemenipas.  
> Terakhir diperbarui: **14 Maret 2026**

---

## Current Status — Fitur yang Sudah Selesai

### Core Data & Models

- [x] 21 Eloquent Models dengan relationships lengkap (termasuk 8 model master data pegawai baru)
- [x] 5 PHP Enums aktif (JenisJabatan, JenisSanksi, RumpunJabatan, StatusHukdis, TingkatHukuman) — 4 enum biodata (Agama, GolonganDarah, JenisKelamin, StatusPernikahan) deprecated, diganti master data tables
- [x] 23 database migrations (termasuk normalisasi FK pegawai + dokumen dasar SK CPNS/PNS)
- [x] 6 seeders (User, MasterData, GolonganPangkat, Pegawai, TabelGaji, Database)
- [x] PegawaiFactory (with afterCreating hook for auto riwayat)
- [x] Activity logging (Spatie) pada semua model utama — **Narrative Audit Logging** dalam Bahasa Indonesia (deskripsi human-readable dengan konteks pegawai/modul)
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
- [x] **Dokumen Dasar (SK CPNS & SK PNS)** — Upload file SK CPNS dan SK PNS pada form create/edit pegawai + tombol "Lihat SK CPNS" dan "Lihat SK PNS" di halaman profil (Data Kepegawaian) + penamaan file `NIP_SK_CPNS/PNS_Timestamp.pdf` + penggantian file otomatis saat update
- [x] **UX: Tab Retention** — Setelah CRUD riwayat, halaman profil pegawai otomatis kembali ke tab yang sedang aktif via URL fragment (`#tab-{type}`)
- [x] **UX: Flash Messages** — Alert sukses/error yang deskriptif dengan icon, judul, pesan detail (termasuk info dokumen yang diunggah), dan tombol dismiss

### Monitoring & Laporan

- [x] **Dashboard** — Ringkasan data pegawai + chart distribusi (golongan, gender, usia, unit kerja) + alert KGB/Pensiun
- [x] **Monitoring KGB** — Alert jatuh tempo, eligibilitas, kalkulasi gaji otomatis (PP 15/2019), integrasi hukdis (penundaan KGB)
- [x] **Kenaikan Pangkat** — Analisis eligibilitas 4 syarat (masa kerja, SKP, latihan, hukuman disiplin), proyeksi periode April/Oktober, integrasi hukdis (penundaan + penurunan pangkat)
- [x] **Alert Pensiun** — Level alert (Hijau/Kuning/Merah/Hitam) berdasarkan BUP + **Workflow Proses Pensiun** (alert → proses SK → nonaktifkan pegawai) + **Dokumen SK Pensiun** (upload PDF + tautan Google Drive fallback)
- [x] **DUK** — Ranking otomatis sesuai hierarki BKN
- [x] **Satyalencana (Reset Argo)** — Identifikasi kandidat 10/20/30 tahun masa kerja + **Reset Argo**: hukdis Sedang/Berat yang selesai me-reset counter masa kerja murni (start date = `tmt_selesai_hukuman`), Ringan tidak me-reset. PPPK excluded. Export Excel + PDF.

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

### UX Overhaul (Frontend Standardization)

- [x] **TomSelect (CDN v2.4.3)** — Searchable dropdown pada semua select Jabatan, Golongan, Unit Kerja, Bagian, serta demotion/restoration selects di form Hukdis. Class marker: `.searchable-select`.
- [x] **IMask.js (CDN v7.6.1)** — Input mask NIP (`00000000 000000 0 000`, auto-strip spasi sebelum submit) dan Gaji Pokok (prefix `Rp`, separator `.`). Data attribute marker: `data-mask="currency"`.
- [x] **Smart Date Defaults** — Semua `<input type="date">` untuk TMT, tanggal_lahir, tanggal_sk, tanggal_ijazah otomatis mendapat `max=today` untuk mencegah input tanggal di masa depan.
- [x] **Anti-Double Submit** — Global listener pada semua `<form>` submit event: disable tombol submit, ubah teks ke "Memproses..." dengan spinner, cegah re-submit.
- [x] **HTML5 `<dialog>` Modals** — Semua modal konfirmasi (delete, PATCH reactivate/cancel-pensiun, pemulihan hukdis) dimigrasi dari `<div>` kustom ke native `<dialog>` element. Backdrop click to close. Semua native `confirm()` dieliminasi.
- [x] **Sticky Table Headers** — Header tabel (`<th>`) di pegawai index (JS-rendered) dan DUK menggunakan `sticky top-0 bg-slate-50 z-10`. Kolom "Aksi" juga `sticky right-0`.
- [x] **Global Command Palette (Ctrl+K)** — Input pencarian di navbar header. `Ctrl+K`/`Cmd+K` fokus input. AJAX debounced (300ms) ke `pegawai.data` endpoint, menampilkan 5 hasil cepat (avatar, nama, NIP) sebagai dropdown link.
- [x] **Blade Components** — `<x-empty-state>` (contextual empty state dengan icon, judul, pesan per tab riwayat — 8 instances di show.blade.php). `<x-tooltip>` (CSS-only tooltip dengan `group-hover`, diterapkan pada semua label TMT di form pegawai, riwayat, dan process).

---

## Current Focus

- [x] **Rumpun Jabatan Refactoring & Sidebar Filtering**: Refactor `RumpunJabatan` (enum → DB table), update master data seeder, admin CRUD, and add "Rumpun Jabatan" navigation links to the Sidebar (Struktural, JFT, JFU, PPPK) to filter employees index view dynamically.
- [x] **PPPK Strict Block — Riwayat Pangkat**: Blokir pembuatan/update Riwayat Pangkat untuk pegawai PPPK sesuai ketentuan BKN. Implementasi di `StorePangkatRequest` dan `UpdatePangkatRequest` (authorize → abort 403), `RiwayatController::createPangkat()` (redirect with error), dan conditional UI rendering di `show.blade.php` (banner info + button hide). Seeder diperbarui dengan 5 jabatan PPPK + 10 pegawai PPPK (hanya initial pangkat, tanpa progression).
- [x] **Satyalencana Reset Argo (PP 94/2021)**: Rewrite `SatyalencanaService` dengan algoritma Reset Argo — hukdis Sedang/Berat yang sudah selesai me-reset counter masa kerja murni ke `tmt_selesai_hukuman`. Ringan diabaikan. PPPK excluded. Kolom baru: "Tgl Mulai Hitung" (dengan badge RESET) dan "Masa Kerja Murni". Info banner PP 94/2021. `SatyalencanaEdgeCaseSeeder` (3 test cases). Export Excel diperbarui.
- [ ] **Dashboard Analytics**: Create summary charts (`Chart.js` / ApexCharts) and stat cards using `PegawaiService` methods.
- [ ] **Export & Reporting**: PDF/Excel export using Spatie Laravel PDF or Laravel Excel.

---

## Recently Completed

- **2026-03-14** (Session 2):
    - **UX Overhaul — 4 Usability Heuristics**:
        - **Data Entry Efficiency**: TomSelect (CDN v2.4.3) searchable dropdowns pada 12 select elements (jabatan, golongan, unit kerja, bagian, demotion, restoration). IMask.js (CDN v7.6.1) NIP pattern mask (`00000000 000000 0 000`) dengan auto-strip spasi. Smart date defaults (max=today) pada semua input date TMT/tanggal_lahir/tanggal_sk.
        - **Error Prevention**: Global anti-double-submit (spinner + disable tombol + prevent re-submit). Migrasi semua modal dari `<div>` kustom ke native HTML5 `<dialog>` (delete modal global, PATCH modal index, pemulihan hukdis dialog). Eliminasi 4 native `confirm()` calls (tabel-gaji, master-data, jabatan, golongan).
        - **Findability**: Sticky table headers (`sticky top-0`) di pegawai index (JS) dan DUK. Global Command Palette (Ctrl+K) — AJAX search di navbar header, 5 hasil cepat (nama+NIP) dari existing `pegawai.data` endpoint.
        - **Contextual Help**: `<x-tooltip>` component (CSS-only, group-hover) diterapkan pada 12 label TMT di seluruh form. `<x-empty-state>` component (icon + judul + pesan kontekstual) menggantikan 8 teks "Belum ada data" generik di show.blade.php dengan pesan bermakna per riwayat.
    - **Files modified**: `layouts/app.blade.php` (CDN, global JS), `pegawai/_form.blade.php`, `pegawai/index.blade.php`, `pegawai/show.blade.php`, `duk/index.blade.php`, 6 riwayat form views, 2 process views, 4 admin views.
    - **Files created**: `components/empty-state.blade.php`, `components/tooltip.blade.php`.

- **2026-03-14** (Session 1):
    - **Satyalencana Reset Argo (PP 94/2021)**:
        - `SatyalencanaService::getEligibleCandidates()` di-rewrite total dengan algoritma 6-step: (F-1) Exclude PPPK, (A) startDate=tmt_cpns, (B/C) cari hukdis Sedang/Berat yang sudah selesai → override startDate ke `tmt_selesai_hukuman` terbaru, skip jika masih menjalani hukdis aktif Sedang/Berat, (D) masaKerjaMurni=diffInYears, (E) milestone 10/20/30, (F-2) already awarded check.
        - Output array baru: `tanggal_mulai_hitung` (formatted dd/mm/yyyy), `is_reset` (boolean).
        - View `satyalencana/index.blade.php`: Info banner PP 94/2021, kolom "Tgl Mulai Hitung" dengan badge merah "RESET", kolom renamed "Masa Kerja Murni".
        - `SatyalencanaExport.php`: Kolom "Tgl Mulai Hitung" + "(RESET)" suffix + renamed "Masa Kerja Murni (Tahun)".
        - `SatyalencanaEdgeCaseSeeder` (3 cases): Case 1 clean (eligible 10yr), Case 2 Ringan (eligible 10yr, no reset), Case 3 Sedang (NOT eligible, masa_kerja_murni=4).
    - **PPPK Strict Block — Riwayat Pangkat (BKN Rules)**:
        - Backend: `StorePangkatRequest::authorize()` dan `UpdatePangkatRequest::authorize()` memeriksa jabatan terakhir pegawai via `riwayatJabatan.jabatan.rumpunJabatan`. Jika rumpun = 'PPPK', abort 403 dengan pesan ketentuan BKN.
        - Controller: `RiwayatController::createPangkat()` juga mem-block PPPK dengan redirect + flash error sebelum menampilkan form.
        - UI: Tab Pangkat di `show.blade.php` menampilkan banner info biru (bukan tombol "+ Tambah") untuk pegawai PPPK.
        - Seeder: 5 jabatan PPPK baru di `MasterDataSeeder`. Group 6 di `PegawaiSeeder`: 10 pegawai PPPK dengan tipe_pegawai PPPK, hanya initial pangkat (tanpa progression), tetap menerima KGB.
        - Factory: `PegawaiFactory::afterCreating()` mendeteksi PPPK dan skip pangkat progression.
    - **DUK Sorting Engine (BKN Rules) & Master Pendidikan**:
        - Migrated `RiwayatPendidikan` to use a dynamic `MasterPendidikan` table (`pendidikan_id` FK).
        - Seeded 6 education levels with sorting weights (`bobot`): SMP (1) to S3 (6).
        - Registered `tingkat-pendidikan` into the MasterData CRUD system for SuperAdmins.
        - Refactored `DUKService::getDUK()` to strictly follow the 6-tier BKN sorting hierarchy: Golongan (1) > Jabatan Eselon/Jenis (2) > MKG Total Bulan (3) > Latihan JP (4) > Pendidikan Bobot (5) > Tanggal Lahir/Usia (6).
        - Added a Tailwind Alert Banner to `duk/index.blade.php` explaining the 6 sorting rules and added a "Diklat (JP)" column to the table.
    - **Rumpun Jabatan Refactoring**:
        - Migrated `RumpunJabatan` from PHP Backed Enum to dynamic `rumpun_jabatans` master data table.
        - Created migration to convert `jabatans.rumpun` (tinyInteger) to `rumpun_jabatan_id` (FK), maintaining existing data integrity.
        - Added "Rumpun Jabatan" section in Sidebar navigation.
        - Implemented dynamic AJAX filtering by `rumpun` in `PegawaiService` (`whereHas` traversal) and `PegawaiController`.
        - Registered `rumpun-jabatan` in `MasterDataController` to provide out-of-the-box SuperAdmin CRUD capabilities.
        - Updated `MasterDataSeeder` to use Rumpun Jabatan FKs and seeded new categories (Struktural, JFT, JFU, PPPK, Imigrasi, Pemasyarakatan).
    - **Core HR Document Uploads**:
        - Added `sk_cpns_path` and `sk_pns_path` to `pegawais` table and model. Form create/edit pegawai mendapat section "Dokumen Dasar" dengan file input (PDF, maks 5MB). `PegawaiController` menangani upload via `DocumentUploadService` ke disk `documents` subfolder `sk_cpns/` dan `sk_pns/`. `DocumentController` di-extend dengan type mapping `sk_cpns` dan `sk_pns`. Halaman profil (Data Kepegawaian) menampilkan tombol "Lihat SK CPNS" dan "Lihat SK PNS" di samping TMT masing-masing jika dokumen tersedia. File lama otomatis dihapus saat diganti.

* **Narrative Audit Logging (Bahasa Indonesia)** — Semua 20 model (1 Pegawai + 8 Riwayat + 11 Master Data) menggunakan `setDescriptionForEvent()` dengan deskripsi naratif Bahasa Indonesia. Tiga format: Category A "Mengubah data pegawai #53 atas nama Yanto", Category B "Menambah Riwayat KGB untuk pegawai #53 atas nama Yanto", Category C "Menghapus Master Jabatan #2 (Polsuspas)". 11 master data models yang sebelumnya tidak di-log (Jabatan, GolonganPangkat, TabelGaji, TipePegawai, StatusKepegawaian, Bagian, UnitKerja, JenisKelaminMaster, AgamaMaster, StatusPernikahanMaster, GolonganDarahMaster) sekarang memiliki trait `LogsActivity`. `PensiunController::process()` dan `PegawaiController::cancelPensiun()` menambahkan log eksplisit via `activity()->performedOn()->log()`. UI Audit Log diperlebar kolom deskripsi (`min-w-[300px]`).
* **GAP-48: Career Timeline View** — Tab "Timeline Karir" ditambahkan sebagai tab pertama (default aktif) di halaman profil pegawai. Merge kronologis semua 8 riwayat (pangkat, jabatan, KGB, hukdis, pendidikan, latihan, SKP, penghargaan) dalam vertical timeline dengan year separator, color-coded dot markers, dan card per event. Logic di `PegawaiService::getCareerTimeline()` dengan `Cache::remember()` (5 min TTL). Invalidasi via semua 3 Observer + 6 model event listeners di `AppServiceProvider`.
* **GAP-47: Export PDF Profil Individual** — `PegawaiController::exportPdf()` via DomPDF, template `exports/pegawai-profile-pdf.blade.php` (portrait A4, inline CSS). Berisi biodata lengkap + 8 tabel riwayat. Route `GET pegawai/{pegawai}/export-pdf`. Tombol "Export PDF" di header halaman show.
* **GAP-46: Data Completeness Indicator** — Progress bar di halaman profil pegawai menunjukkan kelengkapan data (8 riwayat). Badge hijau✓/kuning⚠ per kategori. Amber dot pada tab yang belum ada datanya.
* **GAP-45: Edit Form Guidance** — Field `gaji_pokok` di form edit pegawai ditampilkan readonly (format Rp). Banner informasi biru menjelaskan bahwa golongan, jabatan, dan gaji pokok dikelola otomatis melalui riwayat.
* **GAP-42: Dashboard Caching** — `DashboardService` menggunakan `Cache::remember()` (5 menit) untuk `getDashboardData()` (key per-filter md5 hash) dan `getFilterOptions()`. Invalidasi via `DashboardService::clearCache()` di semua Observer. Tracked keys pattern karena file cache driver tidak mendukung tags.
* **GAP-41: DB-level Pagination Pegawai** — `PegawaiService::getPaginatedByStatus()` menggunakan `->paginate()` langsung dari database (bukan in-memory slicing). Search filter diterapkan di query level. Pagination JS di index.blade.php menggunakan server-returned `last_page`/`current_page` dengan debounce 300ms.
* **GAP-38 & GAP-39: Satyalencana Bug Fixes** — Fixed `golongan_ruang->label()` error (diganti dengan eager-loaded `golongan->label`). Fixed hukdis check terlalu luas (ditambahkan `->filter(fn($h) => $h->isAktif())` sebelum disqualification).
* **Pagination & Search Halaman Master Data + Activity Log + Button Fix** — MasterDataController di-upgrade dari `::get()` ke `->paginate(15)->withQueryString()` dengan filter `?search=` pada kolom nama. View `master-data/index` mendapat form pencarian, row numbering berbasis `firstItem() + $loop->index`, dan `{{ $items->links() }}`. ActivityLogController mendapat filter `?search=` pada deskripsi. View `activity-log/index` mendapat form pencarian dan info count. Tombol "Proses" di halaman Kenaikan Pangkat distandarkan ke `bg-emerald-600 text-white` dengan SVG checkmark icon (konsisten dengan KGB dan Pensiun). CSS di-rebuild via `npm run build`.
* **Server-Side Pagination (GAP-41 monitoring)** — Semua 5 halaman monitoring (KGB, Kenaikan Pangkat, Pensiun, Satyalencana, DUK) dikonversi dari client-side JS pagination ke server-side pagination via `LengthAwarePaginator`. Trait `PaginatesArray` (di `app/Http/Controllers/Traits/`) menerima array hasil kalkulasi Service, menerapkan filter `?search=` (NIP/Nama), lalu memotong per halaman (15 item). View menggunakan `{{ $data->links() }}` (Tailwind). Filter tabs (level, milestone) mempertahankan search parameter. Client-side JS pagination dihapus seluruhnya.
* **Dokumen SK Pensiun (GDrive Fallback)** — Dual-document support pada form proses pensiun: upload file PDF SK (max 5MB via `DocumentUploadService`) dan/atau tautan Google Drive. Kolom baru: `file_sk_pensiun_path`, `link_sk_pensiun_gdrive`. Tab Pensiun di index pegawai menampilkan kolom "Dokumen SK" dengan tombol dinamis (Lihat PDF / Google Drive / Tidak ada dokumen). `cancelPensiun()` otomatis menghapus file yang di-upload. Route `dokumen/pensiun/{id}` ditambahkan ke `DocumentController`.
* **Tabbed Employee Index + Fallback Mechanisms** — Halaman index pegawai sekarang memiliki 3 tab (Aktif / Tidak Aktif / Pensiun) dengan data isolation via `getByStatus()`. Tab Aktif: Detail/Edit/Hapus. Tab Tidak Aktif: Aktifkan Kembali (reactivate). Tab Pensiun: Batalkan Pensiun (cancelPensiun → nullify 4 kolom SK + restore). AJAX pagination per tab dengan search. PATCH confirmation modal.
* **Pensiun Processing Workflow (GAP-35)** — `PensiunService::processPensiun()` mengeset status_kepegawaian → Pensiun, is_active → false, dan merekam 4 field SK pensiun. Form proses dengan data pre-filled dari alert. Tombol "Proses" di baris alert Hitam/Merah.
* **Salary Calculator Service & Observer Pattern — Tongkat Estafet TMT** — `SalaryCalculatorService` sebagai single source of truth untuk salary resolution (TabelGaji lookup dengan fallback ke closest lower MKG). Method `syncCurrentSalary()` mengimplementasikan logika "Tongkat Estafet TMT": bandingkan TMT terbaru antara RiwayatPangkat dan RiwayatKgb, yang paling recent menentukan `gaji_pokok`. `RiwayatKgbObserver` dan `RiwayatPangkatObserver` menggunakan event `saved` (created+updated) dan `deleted` untuk trigger sync otomatis. Manual gaji update dihapus dari semua Controllers/Services. `calculateNextKgbDate()` menghitung estimasi KGB selanjutnya: MAX(tmt_kgb, tmt_pangkat) + 2 tahun.
* **Observer-Driven Seeding** — `PegawaiFactory` dan `PegawaiSeeder` tidak lagi meng-hardcode `gaji_pokok`. Factory membuat logical timeline (Pangkat awal + KGB setiap 2 tahun), Seeder membuat Pangkat progression + KGB timeline penuh. Observer fires pada setiap record → gaji_pokok akhir otomatis sesuai MKG aktual. `KGBCalculationService` pre-fill MKG form: lastKgb.mkg+2, atau total masa kerja jika belum ada KGB.
* **Normalisasi 8 Atribut Pegawai ke Master Data Tables** — Tipe Pegawai, Status Kepegawaian, Bagian, Unit Kerja, Jenis Kelamin, Agama, Status Pernikahan, Golongan Darah. Semuanya sekarang disimpan di tabel terpisah dengan FK constraint. CRUD generik via `MasterDataController`. 4 Enum biodata (Agama, JenisKelamin, StatusPernikahan, GolonganDarah) deprecated, diganti 8 model master data baru. Status Kepegawaian ditampilkan sebagai badge di halaman profil pegawai.

---

## Next Steps

-
