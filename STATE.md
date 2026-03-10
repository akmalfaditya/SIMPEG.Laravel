# STATE.md — Development Status

> Status perkembangan aplikasi SIMPEG Kemenipas.  
> Terakhir diperbarui: **10 Maret 2026**

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

### CRUD & Manajemen

- [x] **Pegawai** — CRUD lengkap + pencarian AJAX + paginasi server-side + validasi NIP 18 digit + **One-Stop Creation Flow** (auto gaji lookup, auto RiwayatPangkat & RiwayatJabatan) + **Tabbed Index** (Aktif / Tidak Aktif / Pensiun) dengan aksi kontekstual per tab
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

- [x] Export PDF (barryvdh/dompdf) untuk Dashboard, DUK, KGB, Pensiun, Kenaikan Pangkat, Satyalencana
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

- **Pagination & Search Halaman Master Data + Activity Log + Button Fix** — MasterDataController di-upgrade dari `::get()` ke `->paginate(15)->withQueryString()` dengan filter `?search=` pada kolom nama. View `master-data/index` mendapat form pencarian, row numbering berbasis `firstItem() + $loop->index`, dan `{{ $items->links() }}`. ActivityLogController mendapat filter `?search=` pada deskripsi. View `activity-log/index` mendapat form pencarian dan info count. Tombol "Proses" di halaman Kenaikan Pangkat distandarkan ke `bg-emerald-600 text-white` dengan SVG checkmark icon (konsisten dengan KGB dan Pensiun). CSS di-rebuild via `npm run build`.
- **Server-Side Pagination (GAP-41)** — Semua 5 halaman monitoring (KGB, Kenaikan Pangkat, Pensiun, Satyalencana, DUK) dikonversi dari client-side JS pagination ke server-side pagination via `LengthAwarePaginator`. Trait `PaginatesArray` (di `app/Http/Controllers/Traits/`) menerima array hasil kalkulasi Service, menerapkan filter `?search=` (NIP/Nama), lalu memotong per halaman (15 item). View menggunakan `{{ $data->links() }}` (Tailwind). Filter tabs (level, milestone) mempertahankan search parameter. Client-side JS pagination dihapus seluruhnya.
- **Dokumen SK Pensiun (GDrive Fallback)** — Dual-document support pada form proses pensiun: upload file PDF SK (max 5MB via `DocumentUploadService`) dan/atau tautan Google Drive. Kolom baru: `file_sk_pensiun_path`, `link_sk_pensiun_gdrive`. Tab Pensiun di index pegawai menampilkan kolom "Dokumen SK" dengan tombol dinamis (Lihat PDF / Google Drive / Tidak ada dokumen). `cancelPensiun()` otomatis menghapus file yang di-upload. Route `dokumen/pensiun/{id}` ditambahkan ke `DocumentController`.
- **Tabbed Employee Index + Fallback Mechanisms** — Halaman index pegawai sekarang memiliki 3 tab (Aktif / Tidak Aktif / Pensiun) dengan data isolation via `getByStatus()`. Tab Aktif: Detail/Edit/Hapus. Tab Tidak Aktif: Aktifkan Kembali (reactivate). Tab Pensiun: Batalkan Pensiun (cancelPensiun → nullify 4 kolom SK + restore). AJAX pagination per tab dengan search. PATCH confirmation modal.
- **Pensiun Processing Workflow (GAP-35)** — `PensiunService::processPensiun()` mengeset status_kepegawaian → Pensiun, is_active → false, dan merekam 4 field SK pensiun. Form proses dengan data pre-filled dari alert. Tombol "Proses" di baris alert Hitam/Merah.
- **Salary Calculator Service & Observer Pattern — Tongkat Estafet TMT** — `SalaryCalculatorService` sebagai single source of truth untuk salary resolution (TabelGaji lookup dengan fallback ke closest lower MKG). Method `syncCurrentSalary()` mengimplementasikan logika "Tongkat Estafet TMT": bandingkan TMT terbaru antara RiwayatPangkat dan RiwayatKgb, yang paling recent menentukan `gaji_pokok`. `RiwayatKgbObserver` dan `RiwayatPangkatObserver` menggunakan event `saved` (created+updated) dan `deleted` untuk trigger sync otomatis. Manual gaji update dihapus dari semua Controllers/Services. `calculateNextKgbDate()` menghitung estimasi KGB selanjutnya: MAX(tmt_kgb, tmt_pangkat) + 2 tahun.
- **Observer-Driven Seeding** — `PegawaiFactory` dan `PegawaiSeeder` tidak lagi meng-hardcode `gaji_pokok`. Factory membuat logical timeline (Pangkat awal + KGB setiap 2 tahun), Seeder membuat Pangkat progression + KGB timeline penuh. Observer fires pada setiap record → gaji_pokok akhir otomatis sesuai MKG aktual. `KGBCalculationService` pre-fill MKG form: lastKgb.mkg+2, atau total masa kerja jika belum ada KGB.
- **Normalisasi 8 Atribut Pegawai ke Master Data Tables** — Tipe Pegawai, Status Kepegawaian, Bagian, Unit Kerja, Jenis Kelamin, Agama, Status Pernikahan, Golongan Darah. Semuanya sekarang disimpan di tabel terpisah dengan FK constraint. CRUD generik via `MasterDataController`. 4 Enum biodata (Agama, JenisKelamin, StatusPernikahan, GolonganDarah) deprecated, diganti 8 model master data baru. Status Kepegawaian ditampilkan sebagai badge di halaman profil pegawai.

---

## Next Steps

-
