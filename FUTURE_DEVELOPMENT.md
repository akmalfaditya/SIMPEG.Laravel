# FUTURE_DEVELOPMENT.md — Gap Analysis PRD v1.3 vs Codebase Aktual

> Dokumen ini dihasilkan dari pemindaian menyeluruh codebase SIMPEG.Laravel  
> terhadap Product Requirements Document (PRD) v1.3 Sistem Informasi Manajemen Kepegawaian Kemenipas.  
> Setiap gap diklasifikasikan menurut prioritas: 🔴 **Kritis**, 🟡 **Sedang**, 🟢 **Rendah**.

---

## 1. RBAC & Otorisasi (PRD §2)

### 🔴 GAP-01: Spatie Permission Belum Diimplementasikan

- **PRD**: RBAC diimplementasikan melalui `spatie/laravel-permission`.
- **Aktual**: Package `spatie/laravel-permission` **tidak ada** di `composer.json`. User model hanya memiliki kolom `role` (string) tanpa trait `HasRoles`. Tidak ada migration untuk tabel `roles/permissions`.
- **Dampak**: Tidak ada granular permission, hanya string `role` sederhana pada tabel `users`.
- **Aksi**: Install `spatie/laravel-permission`, buat migration, seeder role (SuperAdmin, HR, Pegawai), terapkan trait `HasRoles` di User model.

### 🔴 GAP-02: Middleware Role-Based Route Protection Belum Ada

- **PRD**: SuperAdmin → akses penuh; HR → CRUD pegawai & laporan; Pegawai → read-only profil sendiri.
- **Aktual**: Seluruh route hanya dilindungi middleware `auth`. **Tidak ada** middleware `role:` atau `permission:` yang membatasi akses per-role. Semua user yang login bisa mengakses semua fitur.
- **Aksi**: Terapkan middleware Spatie (`role:SuperAdmin`, `role:HR`) di route group. Pisahkan route Pegawai (self-service) dari route HR/Admin.

### 🔴 GAP-03: Policy Classes Kosong

- **PRD**: Otorisasi file/dokumen dan aksi CRUD dilindungi policy.
- **Aktual**: Folder `app/Policies/` **kosong**. Tidak ada class Policy untuk model apapun. FormRequest `authorize()` selalu return `true`.
- **Aksi**: Buat Policy untuk Pegawai, Riwayat, Jabatan, TabelGaji. Terapkan logic: HR hanya kelola pegawai, Pegawai hanya lihat data sendiri.

### 🟡 GAP-04: Role "Pegawai" (User Self-Service) Belum Tersedia

- **PRD**: Aktor "Pegawai" bisa melihat profil sendiri, estimasi KGB & Pensiun, dan mengunduh SK pribadinya.
- **Aktual**: Tidak ada akun Pegawai di seeder. Tidak ada route self-service. Tidak ada relasi `User ↔ Pegawai`.
- **Aksi**: Tambah kolom `pegawai_id` di tabel `users`, buat seeder akun Pegawai, buat route `/self-service/*` dengan akses terbatas ke data diri.

---

## 2. Manajemen Hukuman Disiplin (PRD §3.1)

### ~~🔴 GAP-05: Field `jenis_sanksi` (Enum Dropdown) Belum Ada — Masih Free-Text~~ ✅ RESOLVED

- **Status**: Enum `JenisSanksi` sudah dibuat dengan opsi: PenundaanKgb, PenundaanPangkat, PenurunanPangkat, PenurunanJabatan, PembebasanJabatan, Pemberhentian. Migration, DTO, form request, dan Blade view sudah di-update ke dropdown.

### ~~🔴 GAP-06: Field `durasi_tahun` (Durasi Hukuman) Tidak Ada di Database~~ ✅ RESOLVED

- **Status**: Kolom `durasi_tahun` (integer, nullable) sudah ditambahkan di migration. Model `$fillable`, DTO, dan form sudah di-update.

### ~~🟡 GAP-07: Upload SK Hukdis Belum Diimplementasikan~~ ✅ RESOLVED

- **Status**: Semua form riwayat (Pangkat, Jabatan, KGB, Hukdis, Pendidikan, Latihan, Penghargaan, SKP) sudah memiliki input file upload SK (PDF, maks 5MB) + Google Drive link opsional. `DocumentUploadService` terintegrasi. SK CPNS & SK PNS tersedia di form pegawai.

- **PRD**: Form hukdis harus memiliki upload SK opsional (`dokumen_sk_path`).
- **Aktual**: Migration `riwayat_hukuman_disiplins` **tidak punya** kolom `file_pdf_path` atau `google_drive_link` (berbeda dengan riwayat lain yang sudah punya). Form create-hukuman juga tidak memiliki file upload.
- **Aksi**: Tambah kolom `file_pdf_path` dan `google_drive_link` di migration. Update form Blade dengan input file opsional.

---

## 3. Kalkulasi Dinamis Hukdis → KGB (PRD §3.2)

### ~~🔴 GAP-08: KGB Service Tidak Memperhitungkan Intervensi Hukdis (Penundaan KGB)~~ ✅ RESOLVED

- **Status**: `KGBService::getAllKGBStatus()` sekarang eager-load `riwayatHukumanDisiplin`, cek sanksi aktif bertipe `PenundaanKgb`, geser `jatuhTempo += durasi_tahun`. Output memiliki `hukdis_flag`, `hukdis_note`, dan status `Ditunda`.

### ~~🟡 GAP-09: Badge Visual "Jadwal Disesuaikan" Belum Ada di View KGB~~ ✅ RESOLVED

- **Status**: View KGB sekarang menampilkan status `Ditunda` dengan badge dan `hukdis_note` dari service. Kolom status sudah mencakup informasi hukdis.

---

## 4. Kalkulasi Dinamis Hukdis → Kenaikan Pangkat (PRD §3.3)

### ~~🔴 GAP-10: Penundaan Pangkat — Masa Eligibility Tidak Digeser~~ ✅ RESOLVED

- **Status**: `KenaikanPangkatService` sekarang mendeteksi sanksi `PenundaanPangkat`, menghitung total durasi, dan menambahkan `penundaanBulan` ke masa kerja requirement. Output memiliki `hukdis_pangkat_flag` dan `hukdis_pangkat_note`.

### ~~🔴 GAP-11: Penurunan Pangkat — Reset Perhitungan 4 Tahun Belum Ada~~ ✅ RESOLVED

- **Status**: `KenaikanPangkatService` sekarang mendeteksi sanksi `PenurunanPangkat`, menurunkan golongan (`golLevel - 1`), reset TMT pangkat ke `tmt_hukuman`, dan menghitung ulang masa kerja dari tanggal tersebut.

### ~~🟡 GAP-12: Proyeksi Kenaikan Pangkat Periode April/Oktober Belum Ada~~ ✅ RESOLVED

- **Status**: `KenaikanPangkatService` sudah menghitung proyeksi periode April/Oktober berdasarkan TMT pangkat terakhir. Kolom "Proyeksi Periode" ditampilkan di view.

- **PRD**: Output dasbor ditampilkan sebagai "Proyeksi Kenaikan Pangkat Periode April/Oktober" dengan label status jelas.
- **Aktual**: View hanya menampilkan tabel semua/eligible tanpa konteks periode.
- **Aksi**: Tambah filter/label periode (April/Oktober) berdasarkan bulan TMT pangkat terakhir.

---

## 5. Manajemen Dokumen SK Terproteksi (PRD §3.1)

### ~~🔴 GAP-13: DocumentController untuk Secure Download Tidak Ada~~ ✅ RESOLVED

- **Status**: `DocumentController` sudah tersedia dengan `TYPE_MODEL_MAP` untuk semua jenis dokumen. Route `GET /dokumen/{type}/{id}` melayani inline PDF preview + meaningful filenames. Mendukung: pangkat, jabatan, kgb, hukuman, pendidikan, latihan, penghargaan, skp, sk_cpns, sk_pns, pensiun.

- **PRD**: Endpoint khusus `/dokumen/sk/{id}` dilindungi middleware Auth + otorisasi pengguna. Menggunakan `Storage::download()`.
- **Aktual**: **Tidak ada** `DocumentController` di codebase. Tidak ada route `/dokumen/sk/{id}`. `DocumentUploadService` ada tapi tidak dipakai di controller manapun.
- **Aksi**: Buat `DocumentController` dengan method `download($type, $id)`, implementasi otorisasi (Policy), return `Storage::disk('local')->download()`.

### ~~🟡 GAP-14: File Storage pada Disk "documents" Bukan Private Path per PRD~~ ✅ RESOLVED

- **Status**: File SK disimpan di disk `documents` (storage/app/documents) dengan akses melalui `DocumentController` (autentikasi required). Tidak bisa diakses langsung via URL publik.

- **PRD**: File harus disimpan di `storage/app/private/sk_documents/`.
- **Aktual**: Config filesystems punya disk `documents` dengan root `storage/app/documents/` (bukan `private/sk_documents/`).
- **Dampak**: Minor — path berbeda dari PRD spec tapi masih private (tidak di `public/`). Cukup aman.
- **Aksi**: Pertimbangkan ubah path disk ke `storage/app/private/sk_documents/` agar konsisten PRD.

### ~~🟡 GAP-15: Form Upload File PDF Tidak Terpasang di Semua Riwayat~~ ✅ RESOLVED

- **Status**: Semua 8 form riwayat (create & edit) sudah memiliki input file PDF + Google Drive link field. `enctype="multipart/form-data"` terpasang. `DocumentUploadService` diintegrasikan di `RiwayatService`.

- **PRD**: Kolom unggahan PDF dan Google Drive bersifat opsional di semua riwayat.
- **Aktual**: Migration riwayat pangkat/jabatan/kgb/pendidikan/latihan/penghargaan sudah punya kolom `file_pdf_path` dan `google_drive_link`. **Namun**, form Blade untuk CRUD riwayat **tidak menampilkan** input file upload atau Google Drive link field.
- **Aksi**: Tambahkan input `file` dan `google_drive_link` opsional di semua form riwayat (create & edit). Integrasikan `DocumentUploadService` di `RiwayatService`.

---

## 6. Modul Manajemen Master Data / Admin Setting (PRD §3.6)

### ~~🔴 GAP-16: CRUD Master Jabatan Belum Ada (UI)~~ ✅ RESOLVED

- **Status**: `JabatanController` dengan full CRUD (index, create, store, edit, update, destroy) sudah tersedia. Route, form Blade, dan view sudah dibuat.

### ~~🔴 GAP-17: CRUD Master Tabel Gaji Berkala Belum Ada (UI)~~ ✅ RESOLVED

- **Status**: `TabelGajiController` dengan CRUD sudah tersedia. View matriks gaji dan route sudah dibuat.

### ~~🔴 GAP-18: CRUD Master Golongan & Pangkat Belum Ada (UI)~~ ✅ RESOLVED

- **Status**: Golongan/Pangkat sudah dimigrasi dari Enum ke tabel master `golongan_pangkats` (FK-based). `GolonganController` dengan full CRUD sudah tersedia. 8 atribut master data total sudah dinormalisasi.

### ~~🟡 GAP-19: Field "Rumpun" pada Jabatan Tidak Ada~~ ✅ RESOLVED

- **Status**: `RumpunJabatan` telah di-refactor dari Enum ke tabel database `rumpun_jabatans`. Migration mengkonversi `jabatans.rumpun` (tinyInteger) ke `rumpun_jabatan_id` (FK). CRUD via `MasterDataController`. Seeder: Struktural, JFT, JFU, PPPK, Imigrasi, Pemasyarakatan.

- **PRD**: Jabatan harus memiliki parameter Rumpun (Imigrasi/Pemasyarakatan/Struktural).
- **Aktual**: Migration `jabatans` punya `nama_jabatan`, `jenis_jabatan`, `bup`, `eselon_level`, `kelas_jabatan` — **tidak ada** kolom `rumpun`.
- **Aksi**: Tambah kolom `rumpun` (enum/string) di migration jabatans. Update model, seeder, dan form.

---

## 7. Seeder & Data Demo (PRD §6)

### ~~🟡 GAP-20: Email Seeder Tidak Sesuai Domain Kemenipas~~ ✅ RESOLVED

- **Status**: Seeder menggunakan `superadmin@kemenipas.go.id` dan `hr@kemenipas.go.id` sesuai domain Kemenipas.

- **PRD**: Akun seeder harus `superadmin@kemenipas.go.id` dan `hr@kemenipas.go.id`.
- **Aktual**: Seeder menggunakan `admin@simpeg.go.id` dan `hr@simpeg.go.id`.
- **Aksi**: Update email di `UserSeeder.php`.

### 🟡 GAP-21: Role Seeder Spatie Belum Dipanggil

- **PRD**: `DatabaseSeeder` memanggil role seeder (Spatie) terlebih dahulu.
- **Aktual**: `DatabaseSeeder` memanggil `UserSeeder → MasterDataSeeder → PegawaiSeeder → TabelGajiSeeder`. **Tidak ada** Spatie role seeder.
- **Aksi**: Buat `RoleAndPermissionSeeder`, panggil di awal `DatabaseSeeder`.

### 🟡 GAP-22: Akun Role "Pegawai" Tidak Ada di Seeder

- **PRD**: Menciptakan lingkungan demo interaktif termasuk akun Pegawai.
- **Aktual**: Hanya ada SuperAdmin dan HR. Tidak ada akun demo Pegawai.
- **Aksi**: Tambah 1-2 akun Pegawai di `UserSeeder`, link ke `pegawai_id`.

---

## 8. Struktur Data — Model & Migration Gaps (PRD §5)

### 🟡 GAP-23: Tabel `riwayat_sk` Tidak Ada

- **PRD**: Terdapat entitas `riwayat_sk` (id, pegawai_id, nomor_sk, tanggal_sk, file_pdf_path, google_drive_link).
- **Aktual**: **Tidak ada** tabel `riwayat_sk` terpisah. Nomor SK dan file SK tersebar di masing-masing tabel riwayat.
- **Dampak**: Desain saat ini melekatkan SK per-riwayat (bisa lebih baik daripada tabel SK terpisah). Perlu keputusan: apakah tabel SK terpisah diperlukan atau cukup per-riwayat.
- **Aksi**: Evaluasi ulang — jika tabel SK terpusat diperlukan untuk memudahkan pencarian SK lintas riwayat, buat migration dan model baru.

### 🟡 GAP-24: Field `tanggal_mulai` / `tanggal_selesai` vs `tmt_hukuman` / `tmt_selesai_hukuman`

- **PRD**: Hukdis punya field `tanggal_mulai` dan `tanggal_selesai`.
- **Aktual**: Migration menggunakan `tmt_hukuman` dan `tmt_selesai_hukuman`.
- **Dampak**: Minor — penamaan berbeda tapi fungsi sama. TMT (Terhitung Mulai Tanggal) adalah konvensi ASN yang lebih tepat.
- **Aksi**: Tidak perlu diubah — penamaan saat ini sudah sesuai konvensi ASN.

---

## 9. Keamanan & Kualitas (PRD §4)

### ~~🟡 GAP-25: NIP Validasi 18 Digit Belum Diterapkan Ketat~~ ✅ RESOLVED

- **Status**: `StorePegawaiRequest` dan `UpdatePegawaiRequest` sudah menerapkan validasi `'digits:18'` untuk field NIP.

- **PRD**: NIP harus unik dan terdiri dari **18 digit angka**.
- **Aktual**: `StorePegawaiRequest` validasi: `'nip' => ['required', 'string', 'unique:pegawais,nip']`. **Tidak ada** validasi `digits:18` atau regex digit-only.
- **Aksi**: Tambah rule `'digits:18'` atau `'regex:/^\d{18}$/'` di Store dan Update request.

### 🟡 GAP-26: Unit Test & Feature Test Kosong

- **PRD**: Tidak secara eksplisit mewajibkan, namun critical business logic tanpa test berisiko tinggi.
- **Aktual**: `tests/Feature/ExampleTest.php` dan `tests/Unit/ExampleTest.php` hanya berisi test bawaan Laravel.
- **Aksi**: Buat test untuk:
    - `KGBServiceTest` — siklus normal, intervensi hukdis
    - `KenaikanPangkatServiceTest` — eligibility, penundaan, penurunan
    - `PensiunServiceTest` — 4 level alert
    - `SatyalencanaServiceTest` — milestone & disqualification
    - `DUKServiceTest` — sorting hierarchy
    - Feature test CRUD Pegawai, auth, RBAC

### ~~🟢 GAP-27: Audit Trail Belum Lengkap~~ ✅ RESOLVED

- **Status**: Semua 20 model (1 Pegawai + 8 Riwayat + 11 Master Data) memiliki trait `LogsActivity` dengan `setDescriptionForEvent()` naratif Bahasa Indonesia. Activity Log view tersedia.

- **PRD**: SuperAdmin bisa melihat audit trail.
- **Aktual**: Spatie Activity Log sudah terpasang di `Pegawai`, `RiwayatPangkat`, `RiwayatJabatan`, `RiwayatKgb`, `RiwayatPenghargaan`. View `activity-log/index.blade.php` ada.
- **Dampak**: Model `RiwayatHukumanDisiplin`, `RiwayatPendidikan`, `RiwayatLatihanJabatan`, `PenilaianKinerja` **belum** memiliki trait `LogsActivity`.
- **Aksi**: Tambahkan `LogsActivity` trait di model yang belum.

### ~~🟢 GAP-28: CSRF & Form Enkripsi File — Multipart Form Belum~~ ✅ RESOLVED

- **Status**: Semua form yang memiliki file input menggunakan `enctype="multipart/form-data"`. CSRF token terpasang via `@csrf`.

- **Aktual**: Form riwayat menggunakan `method="POST"` dengan `@csrf` (baik). Namun tidak ada `enctype="multipart/form-data"` karena file upload belum diimplementasikan.
- **Aksi**: Saat implementasi upload file (GAP-15), pastikan semua form yang punya file input menggunakan `enctype="multipart/form-data"`.

---

## 10. Fitur UI/UX Tambahan

### 🟡 GAP-29: Pensiun Alert Level "Hijau" — Threshold Inkonsisten

- **PRD**: Hijau (Aman) = sisa waktu > 1 tahun (artinya semua yang >12 bulan). Filtering hanya menampilkan yang ≤24 bulan.
- **Aktual**: `PensiunService` menampilkan Hijau jika `≤ 24 bulan` dan skip yang `> 24 bulan`.
- **Dampak**: Sesuai PRD (hanya tampilkan alert ≤ 24 bulan). Tapi PRD bilang Hijau = > 1 tahun, sementara service bilang Hijau = 12-24 bulan. Perlu klarifikasi.
- **Aksi**: Klarifikasi dengan stakeholder apakah Hijau harus mencakup semua pegawai > 12 bulan (termasuk yang > 24 bulan) atau hanya 12-24 bulan.

### ~~🟡 GAP-30: Dashboard KGB — Kalkulasi Gaji Baru Otomatis Belum Ditampilkan~~ ✅ RESOLVED

- **Status**: `KGBCalculationService::getNextKGBSalary()` sudah diintegrasikan di `KGBService`. Output array memiliki field `est_gaji_baru`. View KGB menampilkan kolom estimasi gaji baru.

### ~~🟡 GAP-31: Sidebar Belum Ada Menu "Admin Setting" / Master Data~~ ✅ RESOLVED

- **Status**: Sidebar sekarang memiliki section "Master Data Pegawai" dengan 8 link dinamis + section "Admin" untuk Jabatan, Golongan, Tabel Gaji.

### ~~🟢 GAP-32: Satyalencana — Filter Berdasarkan Milestone Belum Ada di UI~~ ✅ RESOLVED

- **Status**: View `satyalencana/index.blade.php` memiliki 4 tombol filter (Semua, 10 Tahun, 20 Tahun, 30 Tahun). Controller menerima parameter `?milestone=`. Server-side pagination mempertahankan filter.

- **PRD**: Filter berdasarkan milestone (10/20/30 tahun).
- **Aktual**: `SatyalencanaService::getCandidatesByMilestone()` sudah ada. Controller `SatyalencanaController` sudah memiliki parameter `milestone`. Tapi perlu verifikasi bahwa UI menampilkan tombol filter milestone.
- **Aksi**: Verifikasi view `satyalencana/index.blade.php` sudah memiliki filter buttons (10/20/30).

---

## 11. Business Process & UX Gap Analysis

> Hasil analisis mendalam terhadap seluruh Service, Controller, dan View.  
> Fokus: workflow yang belum komplit sehingga UX terasa terputus — HR harus melakukan workaround manual.

### ~~🔴 GAP-33: Workflow Proses KGB Tidak Ada — Monitoring Only~~ ✅ RESOLVED

- **Masalah**: KGBController hanya memiliki 4 aksi read-only: `index`, `upcoming`, `eligible`, `ditunda`. Tidak ada method `store`/`process`/`approve`. HR melihat daftar pegawai yang eligible KGB tapi **tidak bisa memproses** dari halaman tersebut.
- **Dampak UX**: HR harus: (1) catat NIP dari halaman KGB → (2) navigasi ke Pegawai → (3) buka tab Riwayat KGB → (4) klik Tambah → (5) isi manual semua field (gaji baru harus hitung sendiri). Alur ini sangat rentan human error dan membuang waktu.
- **Aksi**: Tambah `processKGB(Request $request)` di KGBController. Buat form modal/halaman "Proses KGB" yang pre-fill data dari KGBService (gaji_baru dari TabelGaji lookup, TMT KGB baru = jatuh tempo). Saat submit: otomatis buat RiwayatKgb record + update `gaji_pokok` di Pegawai. Tambahkan tombol "Proses" di setiap baris tabel eligible.

### ~~🔴 GAP-34: Workflow Proses Kenaikan Pangkat Tidak Ada — Eligibility Only~~ ✅ RESOLVED

- **Masalah**: KenaikanPangkatController hanya memiliki `index`, `eligible`, `ditunda` (read-only). Tidak ada method untuk memproses kenaikan pangkat. Halaman hanya menampilkan checklist 4 kriteria (✓/✗) dan proyeksi pangkat berikutnya.
- **Dampak UX**: HR melihat pegawai eligible beserta proyeksi golongan, tapi harus keluar, cari pegawai, tambah RiwayatPangkat manual. Tidak ada koneksi antara halaman monitoring dan aksi.
- **Aksi**: Tambah `processKenaikan(Request $request)` di KenaikanPangkatController. Pre-fill: golongan berikutnya (dari proyeksi service), TMT pangkat (April/Oktober), gaji pokok baru (dari TabelGaji lookup). Saat submit: buat RiwayatPangkat + update `gaji_pokok` + update `golongan_id` di Pegawai.

### ~~🔴 GAP-35: Workflow Proses Pensiun Tidak Ada — Alert Only~~ ✅ RESOLVED

- **Status**: `PensiunController` sekarang memiliki `showProcessForm($pegawaiId)` + `process(ProcessPensiunRequest)`. `PensiunService` menambahkan `getProcessData()` (pre-fill form) dan `processPensiun()` (update status_kepegawaian → Pensiun, set is_active = false, simpan SK pensiun di field pegawais). Migration menambahkan kolom `sk_pensiun_nomor`, `sk_pensiun_tanggal`, `tmt_pensiun`, `catatan_pensiun` di tabel pegawais. View `pensiun/process.blade.php` menampilkan info card + form. Tombol "Proses" muncul di baris Hitam/Merah di index. Pegawai yang sudah diproses otomatis hilang dari alert list (is_active = false).

### 🔴 GAP-36: Workflow CPNS → PNS Transition Tidak Ada

- **Masalah**: Form Pegawai memiliki field `tmt_pns` tapi **tidak ada** workflow/state machine untuk transisi CPNS → PNS. Tidak ada validasi bisnis (misal: lulus Prajabatan sebagai syarat pengangkatan PNS). Tidak ada notifikasi atau monitoring untuk CPNS yang mendekati batas waktu pengangkatan.
- **Dampak UX**: Status kepegawaian diubah manual tanpa enforcement aturan. CPNS bisa "stuck" tanpa ada reminder.
- **Aksi**: Buat monitoring "CPNS Mendekati Pengangkatan" (misal ≥ 1 tahun masa CPNS). Tambah validasi: `tmt_pns` hanya bisa diisi jika ada riwayat latihan Prajabatan. Buat workflow: saat diproses, auto-set `status_kepegawaian` → PNS, catat `tmt_pns`.

### 🔴 GAP-37: Workflow Off-boarding (Resign/Berhenti/Meninggal) Tidak Ada

- **Masalah**: Tidak ada proses formal untuk pegawai resign, diberhentikan, atau meninggal dunia. Hanya bisa diubah via Edit Pegawai → toggle `is_active` ke false.
- **Dampak UX**: Tidak ada pencatatan alasan, tanggal efektif, atau SK pemberhentian. Data bisa diubah tanpa audit trail yang jelas.
- **Aksi**: Buat `OffboardingController` dengan form: jenis off-boarding (Resign/Diberhentikan/Meninggal/Pensiun), tanggal efektif, nomor SK, alasan. Saat submit: update status, set `is_active = false`, catat di tabel riwayat.

### ~~🟡 GAP-38: Satyalencana Service Bug — `golongan_ruang?->label()` Error~~ ✅ RESOLVED

- **Masalah**: `SatyalencanaService` baris 57 menggunakan `$pangkat?->golonSatyalencanaService` baris 36-40 men-disqualify kandidat berdasarkan **seluruh riwayat** hukdis Sedang/Berat, tanpa memfilter `isAktif()`.
- **Dampak UX**: Halaman Satyalencana kemungkinan error saat menampilkan kolom "Pangkat Terakhir", atau menampilkan "-" default untuk semua pegawai.
- **Aksi**: Ubah `$pangkat?->golongan_ruang?->label()` menjadi `$pangkat?->golongan?->label ?? '-'`. Tambahkan `riwayatPangkat.golongan` di eager-load query (baris 13) untuk menghindari N+1.

### ~~🟡 GAP-39: Satyalencana Hukdis Check Terlalu Luas — Tidak Cek Status Aktif~~ ✅ RESOLVED

- **Masalah**: `Pegawai yang hukdisnya sudah selesai (status Selesai) tetap didiskualifikasi permanen.
- **Dampak UX**: Pegawai yang pernah kena hukdis Sedang 10 tahun lalu (sudah selesai dan dipulihkan) tidak akan pernah muncul di daftar kandidat Satyalencana.
- **Aksi**: Tambah filter `->filter(fn($h) => $h->isAktif())` sebelum cek tingkat hukuman, atau tambah parameter time-window (misal 5 tahun terakhir) sesuai regulasi.
  gan_ruang?->label()`. Setelah normalisasi, `golongan_ruang`bukan lagi Enum object — field lama sudah diganti dengan FK`golongan_id`. Kode akan menghasilkan error karena `golongan_ruang`bernilai`null` (field tidak ada) atau integer.

### ~~🟡 GAP-40: No "Quick Action" Buttons dari Halaman Monitoring~~ ✅ RESOLVED

- **Status**: Semua halaman monitoring (KGB, Kenaikan Pangkat, Pensiun, Satyalencana) memiliki tombol "Proses" di setiap baris eligible. KGB dan Kenaikan Pangkat mengarah ke form pre-filled. Pensiun mengarah ke form proses pensiun. Satyalencana memiliki "Berikan Penghargaan".

- **Masalah**: Semua halaman monitoring (KGB, Kenaikan Pangkat, Pensiun, Satyalencana) menampilkan data dalam tabel tapi **tidak ada tombol aksi** (selain Satyalencana yang punya "Berikan Penghargaan"). HR harus meninggalkan halaman untuk melakukan tindak lanjut.
- **Dampak UX**: Informasi dan aksi terpisah. HR harus bolak-balik antara halaman monitoring dan halaman pegawai.
- **Aksi**: Setelah GAP-33/34/35 diimplementasikan, tambahkan tombol "Proses" di setiap baris tabel eligible. Tombol bisa membuka modal form atau redirect ke halaman proses dengan data pre-filled.

### ~~🟡 GAP-41: Client-Side Pagination — Semua Data Di-render ke HTML~~ ✅ RESOLVED

- **Masalah**: Seluruh halaman monitoring (KGB, Kenaikan Pangkat, Pensiun, Satyalencana) menggunakan pola: server kirim **semua** data → render semua ke HTML → JavaScript sembunyikan/tampilkan 15 baris per halaman. Juga berlaku untuk `PegawaiService::getAll()` yang load seluruh collection ke PHP memory.
- **Dampak UX**: Dengan 50 pegawai demo, tidak terasa. Dengan 500+ pegawai produksi: page load lambat, browser lag, memory usage tinggi. Seluruh NIP/nama pegawai terekspos di HTML source.
- **Solusi**: Implementasi server-side pagination via `PaginatesArray` trait + `LengthAwarePaginator`. Controller menerima `?search=` dan `?page=` dari query string. View menggunakan `{{ $data->links() }}` (Tailwind pagination). Search memfilter array berdasarkan NIP/Nama sebelum paginasi. Filter tabs (level, milestone) mempertahankan search parameter. Client-side JS pagination dihapus dari semua 5 modul monitoring (KGB, Kenaikan Pangkat, Pensiun, Satyalencana, DUK).

### ~~🟡 GAP-42: Dashboard Tidak Ada Caching — Query Aggregasi Berat~~ ✅ RESOLVED

- **Masalah**: `DashboardService` menjalankan multiple aggregate queries (count by status, chart data, KGB alerts, pensiun alerts) setiap kali halaman di-load. Tidak ada caching.
- **Dampak UX**: Dashboard load time akan bertambah seiring data bertambah. Semua user yang akses dashboard trigger query yang sama.
- **Aksi**: Implementasi `Cache::remember('dashboard_stats', 300, fn() => ...)` untuk data yang jarang berubah (distribusi per golongan, per jabatan). Invalidate cache saat Pegawai di-create/update/delete.

### 🟡 GAP-43: Dashboard Tidak Ada Widget "Perlu Tindakan"

- **Masalah**: Dashboard menampilkan statistik umum (jumlah pegawai, distribusi) dan alert KGB/Pensiun, tapi **tidak ada** ringkasan "Perlu Segera Diproses" yang actionable: berapa KGB jatuh tempo bulan ini, berapa pegawai eligible kenaikan pangkat periode depan, berapa mendekati BUP.
- **Dampak UX**: HR harus mengecek masing-masing modul monitoring untuk tahu apa yang perlu dikerjakan hari ini. Tidak ada "one-glance" overview.
- **Aksi**: Tambah section "Perlu Tindakan" di atas dashboard: card KGB (X eligible, Y jatuh tempo ≤30 hari), card Pangkat (X eligible April/Oktober), card Pensiun (X level Hitam/Merah). Setiap card link ke halaman monitoring terkait.

### 🟡 GAP-44: Tidak Ada Bulk/Batch Processing

- **Masalah**: Semua proses kepegawaian bersifat one-by-one. Tidak ada cara memilih multiple pegawai eligible dan memprosesnya sekaligus (misal: proses KGB 10 pegawai sekaligus dengan 1 SK kolektif).
- **Dampak UX**: Untuk 50 pegawai yang KGB-nya jatuh tempo bersamaan, HR harus klik "Proses" 50× secara terpisah.
- **Aksi**: Setelah workflow individual (GAP-33/34/35) tersedia, tambahkan checkbox multi-select + tombol "Proses Semua Terpilih". Buat batch processing method di service.

### ~~🟠 GAP-45: Form Edit Pegawai Tidak Ada Guidance untuk Field Read-Only Kontekstual~~ ✅ RESOLVED

- **Masalah**: Field `gaji_pokok`, golongan (terakhir), dan jabatan (terakhir) di halaman pegawai hanya bisa diubah melalui penambahan Riwayat (Pangkat, Jabatan, KGB). Namun form edit tidak memberikan penjelasan bahwa field ini dikelola via riwayat, bukan langsung di-edit.
- **Dampak UX**: HR baru mungkin bingung kenapa tidak bisa mengubah golongan atau gaji di form edit, atau malah mengubah `gaji_pokok` langsung tanpa melalui proses KGB.
- **Aksi**: Tambahkan tooltip/info text di form: "Golongan dan gaji dikelola otomatis melalui Riwayat Pangkat & KGB". Pertimbangkan membuat field `gaji_pokok` di form edit sebagai read-only.

### ~~🟠 GAP-46: Tidak Ada Data Completeness Indicator di Profil Pegawai~~ ✅ RESOLVED

- **Masalah**: Halaman show pegawai menampilkan 8 tab riwayat tapi tidak ada indikator apakah data sudah lengkap. Pegawai tanpa riwayat pendidikan, latihan, atau SKP tidak diberi warning.
- **Dampak UX**: HR tidak tahu pegawai mana yang data-nya belum lengkap. Baru ketahuan saat dibutuhkan (misal: kenaikan pangkat gagal karena belum ada latihan jabatan).
- **Aksi**: Tambah badge/progress bar "Kelengkapan Data: 6/8 riwayat terisi" di halaman show. Warning icon di tab yang masih kosong.

### ~~🟠 GAP-47: Tidak Ada Export PDF Profil Individual Pegawai~~ ✅ RESOLVED

- **Masalah**: Export yang tersedia (DUK, KGB, Kenaikan Pangkat, Pensiun, Satyalencana) semuanya bersifat daftar/kolektif. Tidak ada fitur export profil lengkap satu pegawai (biodata + seluruh riwayat) sebagai PDF.
- **Dampak UX**: Untuk keperluan mutasi, promosi, atau arsip, HR harus screenshot/print manual halaman profil pegawai.
- **Aksi**: Buat `PegawaiProfileExport` menggunakan DomPDF. Tambahkan tombol "Export PDF" di halaman show pegawai. Template: biodata + tabel ringkas setiap riwayat.

### ~~🟠 GAP-48: Tidak Ada Career Timeline View~~ ✅ RESOLVED

- **Masalah**: Halaman show pegawai menampilkan riwayat dalam 8 tab terpisah (tabel per jenis). Tidak ada visualisasi kronologis gabungan yang menampilkan seluruh perjalanan karir dalam satu timeline.
- **Dampak UX**: Untuk memahami perjalanan karir seorang pegawai, HR harus membuka 8 tab secara bergantian dan menyusun kronologi secara mental.
- **Aksi**: Tambahkan tab "Timeline Karir" yang merge semua riwayat (pangkat, jabatan, KGB, hukdis, pendidikan, latihan, penghargaan, SKP) ke dalam satu timeline kronologis. Tampilkan sebagai vertical timeline card.

### ~~🟠 GAP-49: Proyeksi Kenaikan Pangkat Belum Grouped per Periode April/Oktober~~ ✅ RESOLVED

- **Status**: `KenaikanPangkatService` sudah menghitung proyeksi periode April/Oktober. Tampilan di view sudah menampilkan kolom "Proyeksi Periode" dan bisa difilter.

- **Masalah**: Halaman kenaikan pangkat menampilkan semua pegawai eligible dalam satu tabel flat. Tidak ada pengelompokan atau filter berdasarkan periode kenaikan (April atau Oktober).
- **Dampak UX**: HR tidak langsung tahu pegawai mana yang naik pangkat April vs Oktober. Harus menghitung dari TMT pangkat terakhir secara manual.
- **Aksi**: Tambah filter/tab "Periode April" dan "Periode Oktober". Tambah kolom "Periode Proyeksi" di tabel. Logic: bulan TMT 1-6 → Oktober, bulan TMT 7-12 → April tahun berikutnya (sesuai aturan ASN).

---

## Ringkasan Prioritas

| Prioritas   | Aktif | Resolved | ID Aktif                              |
| ----------- | ----- | -------- | ------------------------------------- |
| 🔴 Kritis   | 3     | 11       | GAP-01, 02, 03, 36, 37                |
| 🟡 Sedang   | 4     | 16       | GAP-04, 21, 22, 23, 26, 29, 43, 44    |
| 🟠 Menengah | 0     | 5        | ~~45, 46, 47, 48, 49~~ semua resolved |
| 🟢 Rendah   | 1     | 3        | GAP-24                                |

### ✅ Total Resolved: 35 GAP (dari 49)

> GAP-05, 06, 07, 08, 09, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 25, 27, 28, 30, 31, 32, 33, 34, 35, 38, 39, 40, 41, 42, 45, 46, 47, 48, 49

---

## Urutan Pengerjaan yang Disarankan

### Phase 1 — Foundation (Prasyarat)

1. **GAP-01** Spatie Permission setup
2. **GAP-02** Middleware RBAC di routes
3. **GAP-03** Policy classes
4. ~~**GAP-05** Enum `JenisSanksi`~~ ✅
5. ~~**GAP-06** Kolom `durasi_tahun`~~ ✅

### Phase 2 — Core Business Logic Fix ✅ COMPLETED

> ~~GAP-08, 09, 10, 11~~ — Semua intervensi hukdis di KGBService dan KenaikanPangkatService sudah diimplementasikan.

### Phase 3 — Admin Setting UI ✅ COMPLETED

> ~~GAP-16, 17, 18, 31~~ — CRUD Jabatan, Tabel Gaji, Golongan, dan Sidebar Admin Setting sudah tersedia. 6. **GAP-19** Field Rumpun di Jabatan (belum)

### Phase 4 — Core Workflow Implementation ✅ MOSTLY COMPLETED

> ~~GAP-33, 34, 35, 38, 39, 40~~ — Workflow KGB, Kenaikan Pangkat, Pensiun, Satyalencana bug fixes, dan Quick Action buttons semua resolved.
> **Masih aktif**: GAP-36 (CPNS → PNS), GAP-37 (Off-boarding), ~~GAP-13~~ (DocumentController ✅), ~~GAP-19~~ (Rumpun ✅)

### Phase 5 — Performance & UX Improvement ✅ MOSTLY COMPLETED

> ~~GAP-41, 42, 49~~ — Server-side pagination, dashboard caching, dan proyeksi April/Oktober semua resolved.
> **Masih aktif**: GAP-43 (Widget "Perlu Tindakan"), GAP-44 (Bulk Processing)

### Phase 6 — Polish & Completeness ✅ MOSTLY COMPLETED

> ~~GAP-07, 12, 13, 14, 15, 19, 20, 25, 27, 28, 30, 32, 45, 46, 47, 48~~ — Semua resolved.
> **Masih aktif**: GAP-04 (Role Pegawai), GAP-21/22 (Seeder Spatie role), GAP-26 (Unit tests)
> **Low priority**: GAP-23 (tabel riwayat_sk terpisah), GAP-24 (penamaan field — sudah tepat, no action), GAP-29 (threshold Hijau)

---

## 12. PRD Compliance Check — Versi Final (14 Maret 2026)

> Perbandingan terhadap **PRD Portal Kepegawaian** (9 menu fitur) yang diberikan stakeholder.

### Requirement 1: Dashboard

| Sub-fitur                                                         | Status            | Detail Implementasi                                                                                                                                                                                                                                                         |
| ----------------------------------------------------------------- | ----------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Jenis Kelamin                                                     | ✅ Selesai        | Chart "Distribusi Gender" (Laki-laki/Perempuan) via `DashboardService`                                                                                                                                                                                                      |
| Pendidikan (SMP, SMA, S1, S2, S3)                                 | ✅ Selesai        | Chart "Distribusi Pendidikan Terakhir" — menampilkan S3, S2, S1, D4, D3, D2, D1, SMA/SMK, Lainnya                                                                                                                                                                           |
| Nama Jabatan (Struktural, JFT, JFU, PPPK)                         | ⚠️ **GAP-NEW-01** | Chart saat ini: "Distribusi Jenis Jabatan" menampilkan label `JenisJabatan` enum (Administrasi, Fungsional, Pimpinan Tinggi). **Seharusnya**: distribusi per Rumpun Jabatan (Struktural, JFT, JFU, PPPK). Data `rumpun_jabatans` sudah tersedia — tinggal ubah source chart |
| Pangkat                                                           | ✅ Selesai        | Chart "Distribusi Golongan" (I/a s.d IV/e)                                                                                                                                                                                                                                  |
| Pegawai Unit Kerja                                                | ✅ Selesai        | Chart distribusi + tabel ringkasan per unit kerja (total, L/P, rata-rata usia)                                                                                                                                                                                              |
| Sub-unit Urusan (Keuangan, Kepegawaian, Umum) di bawah Tata Usaha | ⚠️ **GAP-NEW-02** | Tabel `bagians` saat ini flat (5 entry singkatan). Belum ada sub-unit "Urusan" di bawah Subbagian Tata Usaha                                                                                                                                                                |

### Requirement 2: Profil Pegawai & Dokumen

| Sub-fitur                                    | Status     | Detail                                                             |
| -------------------------------------------- | ---------- | ------------------------------------------------------------------ |
| Profil Pegawai (Nama, NIP, Pangkat, Jabatan) | ✅ Selesai | Halaman show pegawai lengkap dengan 9 tab + timeline karir         |
| SK CPNS                                      | ✅ Selesai | Upload di form pegawai, preview inline PDF, `sk_cpns_path`         |
| SK PNS                                       | ✅ Selesai | Upload di form pegawai, preview inline PDF, `sk_pns_path`          |
| SK KP (Kenaikan Pangkat)                     | ✅ Selesai | Upload di form riwayat pangkat, `file_pdf_path`                    |
| SK KGB                                       | ✅ Selesai | Upload di form riwayat KGB, `file_pdf_path`                        |
| SK Jabatan (Struktural, JFT, JFU, PPPK)      | ✅ Selesai | Upload di form riwayat jabatan — berlaku untuk semua jenis jabatan |

### Requirement 3: DUK

| Sub-fitur               | Status     | Detail                                                                                            |
| ----------------------- | ---------- | ------------------------------------------------------------------------------------------------- |
| Daftar Urut Kepangkatan | ✅ Selesai | `DUKService` — ranking 6-tier hierarki BKN (Pangkat > Jabatan > MKG > Diklat > Pendidikan > Usia) |

### Requirement 4: KGB (Periode 2 Tahun)

| Sub-fitur           | Status     | Detail                                                                            |
| ------------------- | ---------- | --------------------------------------------------------------------------------- |
| Monitoring KGB      | ✅ Selesai | `KGBService` — siklus 2 tahun, status upcoming/eligible/ditunda                   |
| Kalkulasi gaji baru | ✅ Selesai | `KGBCalculationService` + `SalaryCalculatorService` — lookup TabelGaji PP 15/2019 |
| Integrasi hukdis    | ✅ Selesai | Penundaan KGB dari hukdis aktif                                                   |
| Proses KGB          | ✅ Selesai | Tombol "Proses" → form pre-filled → auto-create RiwayatKgb + sync gaji_pokok      |

### Requirement 5: KP (Kenaikan Pangkat) — Struktural dan JFU

| Sub-fitur               | Status            | Detail                                                                                                                                 |
| ----------------------- | ----------------- | -------------------------------------------------------------------------------------------------------------------------------------- |
| Struktural              | ✅ Selesai        | Eligibilitas 4 syarat (masa kerja 48 bulan, SKP, latihan, hukdis)                                                                      |
| JFU                     | ✅ Selesai        | Sama dengan Struktural                                                                                                                 |
| JFT (Angka Kredit)      | ⚠️ **GAP-NEW-03** | PRD hanya menyebut "Struktural dan JFU". JFT memiliki mekanisme berbeda (angka kredit) yang **belum ada** — tapi **tidak diminta PRD** |
| PPPK exclusion          | ✅ Selesai        | PPPK di-block 3 lapis (authorize, controller, UI)                                                                                      |
| Proyeksi April/Oktober  | ✅ Selesai        | Kolom "Proyeksi Periode" di view                                                                                                       |
| Proses Kenaikan Pangkat | ✅ Selesai        | Tombol "Proses" → form pre-filled → auto-create RiwayatPangkat + sync gaji_pokok                                                       |

### Requirement 6: Pensiun

| Sub-fitur                  | Status     | Detail                                                      |
| -------------------------- | ---------- | ----------------------------------------------------------- |
| Monitoring berdasarkan BUP | ✅ Selesai | `PensiunService` — 4 level alert (Hitam/Merah/Kuning/Hijau) |
| Proses pensiun             | ✅ Selesai | Form proses pensiun dengan SK fields + upload dokumen       |
| SK Pensiun                 | ✅ Selesai | `file_sk_pensiun_path` + `link_sk_pensiun_gdrive`           |

### Requirement 7: Satyalencana (X, XX, XXX Tahun)

| Sub-fitur                 | Status     | Detail                                                                      |
| ------------------------- | ---------- | --------------------------------------------------------------------------- |
| Milestone 10/20/30 tahun  | ✅ Selesai | `SatyalencanaService` — filter 3 tier                                       |
| Reset hukdis Sedang/Berat | ✅ Selesai | Reset Argo: `tmt_selesai_hukuman` sebagai start date baru, Ringan diabaikan |
| PPPK exclusion            | ✅ Selesai | PPPK di-exclude dari skema Satyalencana                                     |
| Award recording           | ✅ Selesai | `awardCandidate()` → RiwayatPenghargaan + duplikasi check                   |

### Requirement 8: Hukdis (Hukuman Disiplin)

| Sub-fitur          | Status     | Detail                                                                                          |
| ------------------ | ---------- | ----------------------------------------------------------------------------------------------- |
| 3 tingkat hukuman  | ✅ Selesai | `TingkatHukuman` enum: Ringan, Sedang, Berat                                                    |
| 6 jenis sanksi     | ✅ Selesai | `JenisSanksi` enum: Penundaan KGB/Pangkat, Penurunan Pangkat/Jabatan, Pembebasan, Pemberhentian |
| 3 status           | ✅ Selesai | `StatusHukdis` enum: Aktif, Selesai, Dipulihkan                                                 |
| Integrasi KGB + KP | ✅ Selesai | Penundaan, penurunan, pemulihan, blokir                                                         |
| Upload SK Hukdis   | ✅ Selesai | `file_pdf_path` + `file_sk_pemulihan_path`                                                      |

### Requirement 9: Page Views (KGB, KP, Pensiun, Satyalencana)

| Sub-fitur                                      | Status     | Detail                                                      |
| ---------------------------------------------- | ---------- | ----------------------------------------------------------- |
| Halaman KGB (`/kgb`)                           | ✅ Selesai | Filter: upcoming, eligible, ditunda. Pagination server-side |
| Halaman Kenaikan Pangkat (`/kenaikan-pangkat`) | ✅ Selesai | Filter: eligible, ditunda. Pagination server-side           |
| Halaman Pensiun (`/pensiun`)                   | ✅ Selesai | Filter: level alert. Pagination server-side                 |
| Halaman Satyalencana (`/satyalencana`)         | ✅ Selesai | Filter: milestone 10/20/30. Pagination server-side          |

---

## 13. GAP Baru dari PRD Final

### ~~⚠️ GAP-NEW-01: Dashboard Chart Jabatan → Rumpun~~ ✅ RESOLVED

- **Status**: Chart dashboard diubah dari `JenisJabatan` enum → `rumpunJabatan->nama` (tabel `rumpun_jabatans`). Eager-load `riwayatJabatan.jabatan.rumpunJabatan`. Label chart: "Distribusi Rumpun Jabatan". Key data: `rumpun_jabatan`.

- **PRD**: "Nama Jabatan — Struktural, JFT, JFU, PPPK"
- **Current**: Chart "Distribusi Jenis Jabatan" mengelompokkan berdasarkan `JenisJabatan` enum (Administrasi, Fungsional, Pimpinan Tinggi) — bukan rumpun organisasi.
- **Expected**: Chart berdasarkan tabel `rumpun_jabatans` (Struktural, JFT, JFU, PPPK, Imigrasi, Pemasyarakatan).
- **Solusi**: Ubah `DashboardService` — eager-load `riwayatJabatan.jabatan.rumpunJabatan`, group by `rumpunJabatan->nama`. Rename label chart.
- **Prioritas**: 🟡 Sedang
- **Kompleksitas**: Rendah (1 method service + 1 label view)

### ⚠️ GAP-NEW-02: Data Master Bagian — Nama Formal + Sub-unit Urusan

- **PRD**: Struktur organisasi Kantor Imigrasi:
    ```
    SUBBAGIAN TATA USAHA
      ├── URUSAN KEUANGAN
      ├── URUSAN KEPEGAWAIAN
      └── URUSAN UMUM
    SEKSI LALU LINTAS KEIMIGRASIAN
    SEKSI INTELIJEN DAN PENINDAKAN KEIMIGRASIAN
    SEKSI TEKNOLOGI INFORMASI DAN KOMUNIKASI KEIMIGRASIAN
    SEKSI IZIN TINGGAL DAN STATUS KEIMIGRASIAN
    ```
- **Current Seeder**: `['Tata Usaha', 'Tikim', 'Lantaskim', 'Inteldakim', 'Intaltuskim']` — nama disingkat, tidak ada sub-unit urusan.
- **Dua bagian masalah**:
    1. **Penamaan**: Ganti singkatan ke nama formal lengkap → update seeder saja.
    2. **Sub-unit Urusan**: 3 urusan di bawah Subbagian Tata Usaha. Opsi:
        - **Opsi A (Simple)**: Tambah 3 entry flat di `bagians`: "Subbag TU — Urusan Keuangan", dll.
        - **Opsi B (Hierarchical)**: Tambah kolom `parent_id` di `bagians` untuk hierarki 2 level.
- **Rekomendasi**: Opsi A untuk iterasi cepat. Opsi B jika dibutuhkan multi-level reporting.
- **Prioritas**: 🟡 Sedang (penamaan) / 🟢 Rendah (hierarki)
- **Kompleksitas**: Rendah s.d Sedang

### ℹ️ GAP-NEW-03: Kenaikan Pangkat JFT via Angka Kredit (NOT in PRD)

- **Catatan**: PRD secara eksplisit hanya menyebut "KP (Struktural dan JFU)". JFT menggunakan mekanisme angka kredit yang berbeda. Ini **bukan gap terhadap PRD** melainkan potensi peningkatan di masa depan.
- **Prioritas**: 🟢 Rendah (future enhancement)
- **Kompleksitas**: Tinggi (migration, service logic, form UI)

---

## 14. Fitur Melampaui PRD (Bonus Implementations)

Fitur-fitur berikut **sudah diimplementasikan** tetapi **tidak diminta** dalam PRD:

| Fitur                                   | Deskripsi                                                  |
| --------------------------------------- | ---------------------------------------------------------- |
| Timeline Karir                          | Gabungan kronologis 8 riwayat dalam vertical timeline card |
| Data Completeness Indicator             | Progress bar kelengkapan data per pegawai                  |
| Export PDF Profil Individual            | PDF profil pegawai (biodata + seluruh riwayat)             |
| Export Excel/PDF Monitoring             | DUK, KGB, Pensiun, Kenaikan Pangkat, Satyalencana          |
| Activity Log (Audit Trail)              | Semua 20 model dengan deskripsi naratif Bahasa Indonesia   |
| Salary Calculator (Tongkat Estafet TMT) | Observer-driven gaji_pokok sync otomatis                   |
| Dashboard Advanced Charts               | KGB trend, Pensiun proyeksi, Masa Kerja, Usia              |
| PPPK Strict Block                       | 3 lapis pencegahan kenaikan pangkat PPPK                   |
| Reset Argo Satyalencana                 | Perhitungan masa kerja murni (PP 94/2021)                  |
| Pemulihan Hukdis                        | Restore pangkat/jabatan + rekalkulasi gaji                 |
| One-Stop Creation Flow                  | Auto-generate riwayat saat create pegawai                  |
| Tab Retention                           | Kembali ke tab aktif setelah CRUD                          |
| Google Drive Link                       | Opsional link GDrive per dokumen SK                        |
| Inline PDF Preview                      | Preview dokumen langsung di browser                        |
| Server-Side Pagination                  | Semua monitoring menggunakan `LengthAwarePaginator`        |
| Caching (5 min TTL)                     | Dashboard + Career Timeline                                |
| Dokumen SK Pensiun                      | Upload PDF + GDrive fallback                               |
| Edge-case Seeder                        | `SatyalencanaEdgeCaseSeeder` (3 mathematical test cases)   |
