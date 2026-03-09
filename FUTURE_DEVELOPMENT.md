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

### 🔴 GAP-05: Field `jenis_sanksi` (Enum Dropdown) Belum Ada — Masih Free-Text
- **PRD**: `jenis_sanksi` harus berupa **dropdown** dengan opsi fixed: `penundaan_kgb`, `penundaan_pangkat`, `penurunan_pangkat`, `penurunan_jabatan`, `pembebasan_jabatan`, `pemberhentian`.
- **Aktual**: Field `jenis_hukuman` di migration/model/DTO/form adalah **free-text** (`string`), bukan enum/dropdown.
- **Dampak**: Service layer tidak bisa melakukan matching sanksi secara programatik untuk kalkulasi KGB/Pangkat.
- **Aksi**: Buat Enum `JenisSanksi`, ubah migration jadi `tinyInteger`, update DTO, form request, dan Blade view ke dropdown.

### 🔴 GAP-06: Field `durasi_tahun` (Durasi Hukuman) Tidak Ada di Database
- **PRD**: Tabel `riwayat_hukuman_disiplin` harus memiliki `durasi_tahun` (integer) untuk kalkulasi penundaan.
- **Aktual**: Migration hanya punya `tmt_hukuman` dan `tmt_selesai_hukuman`. **Tidak ada** field `durasi_tahun` eksplisit.
- **Dampak**: Kalkulasi penundaan KGB/Pangkat harus menghitung selisih tanggal secara manual (kurang presisi jika TMT selesai kosong).
- **Aksi**: Tambah kolom `durasi_tahun` (integer, nullable) pada migration. Update model `$fillable`, DTO, form.

### 🟡 GAP-07: Upload SK Hukdis Belum Diimplementasikan
- **PRD**: Form hukdis harus memiliki upload SK opsional (`dokumen_sk_path`).
- **Aktual**: Migration `riwayat_hukuman_disiplins` **tidak punya** kolom `file_pdf_path` atau `google_drive_link` (berbeda dengan riwayat lain yang sudah punya). Form create-hukuman juga tidak memiliki file upload.
- **Aksi**: Tambah kolom `file_pdf_path` dan `google_drive_link` di migration. Update form Blade dengan input file opsional.

---

## 3. Kalkulasi Dinamis Hukdis → KGB (PRD §3.2)

### 🔴 GAP-08: KGB Service Tidak Memperhitungkan Intervensi Hukdis (Penundaan KGB)
- **PRD**: Saat render dasbor KGB, service harus **Left Join** ke `riwayat_hukuman_disiplin` dan jika ada sanksi "Penundaan KGB" aktif, jadwal jatuh tempo otomatis digeser sebesar durasi hukuman. Badge merah "Ditunda X Tahun (Status Hukdis)" ditempelkan.
- **Aktual**: `KGBService::getAllKGBStatus()` hanya menghitung `tmt_kgb + 2 tahun`. **Tidak ada** pengecekan tabel hukdis sama sekali. Tidak ada badge penundaan.
- **Dampak**: Pegawai yang terkena sanksi penundaan KGB tetap muncul sebagai "Eligible" — salah secara hukum.
- **Aksi**: Di `KGBService`, eager-load `riwayatHukumanDisiplin`, cek sanksi aktif bertipe `penundaan_kgb`, geser `jatuhTempo += durasi_tahun`, tambahkan field `hukdis_flag` dan `hukdis_note` di array output. Update view KGB untuk menampilkan badge merah.

### 🟡 GAP-09: Badge Visual "Jadwal Disesuaikan" Belum Ada di View KGB
- **PRD**: Tabel KGB harus menampilkan badge merah tebal: _"Jadwal disesuaikan: Ditunda X Tahun (Status Hukdis)"_.
- **Aktual**: View `kgb/index.blade.php` tidak menampilkan informasi hukdis apapun.
- **Aksi**: Tambahkan conditional badge di kolom Status pada view KGB.

---

## 4. Kalkulasi Dinamis Hukdis → Kenaikan Pangkat (PRD §3.3)

### 🔴 GAP-10: Penundaan Pangkat — Masa Eligibility Tidak Digeser
- **PRD**: Jika terkena sanksi "Penundaan Pangkat", masa eligibility bergeser ditambah durasi hukuman (misal +1 tahun) dengan catatan merah di dasbor.
- **Aktual**: `KenaikanPangkatService` hanya mengecek apakah hukdis aktif ada (`$activeHukuman->isEmpty()`). Jika aktif → langsung tidak eligible. **Tidak ada** logic geser masa kerja berdasarkan durasi hukuman spesifik "penundaan pangkat".
- **Aksi**: Deteksi tipe sanksi `penundaan_pangkat`, tambahkan durasi ke `masaKerjaGolBulan`, output field `hukdis_pangkat_note`.

### 🔴 GAP-11: Penurunan Pangkat — Reset Perhitungan 4 Tahun Belum Ada
- **PRD**: Jika terkena sanksi "Penurunan Pangkat", pangkat saat ini turun dan perhitungan 4 tahun dimulai ulang dari masa pemulihan.
- **Aktual**: Tidak ada logic penurunan pangkat. Service hanya membaca `riwayatPangkat->sortByDesc()` tanpa memperhitungkan penurunan.
- **Aksi**: Cek `penurunan_pangkat` aktif → turunkan `golSaatIni`, reset `tmt_pangkat` ke `tanggal_mulai` sanksi untuk perhitungan ulang.

### 🟡 GAP-12: Proyeksi Kenaikan Pangkat Periode April/Oktober Belum Ada
- **PRD**: Output dasbor ditampilkan sebagai "Proyeksi Kenaikan Pangkat Periode April/Oktober" dengan label status jelas.
- **Aktual**: View hanya menampilkan tabel semua/eligible tanpa konteks periode.
- **Aksi**: Tambah filter/label periode (April/Oktober) berdasarkan bulan TMT pangkat terakhir.

---

## 5. Manajemen Dokumen SK Terproteksi (PRD §3.1)

### 🔴 GAP-13: DocumentController untuk Secure Download Tidak Ada
- **PRD**: Endpoint khusus `/dokumen/sk/{id}` dilindungi middleware Auth + otorisasi pengguna. Menggunakan `Storage::download()`.
- **Aktual**: **Tidak ada** `DocumentController` di codebase. Tidak ada route `/dokumen/sk/{id}`. `DocumentUploadService` ada tapi tidak dipakai di controller manapun.
- **Aksi**: Buat `DocumentController` dengan method `download($type, $id)`, implementasi otorisasi (Policy), return `Storage::disk('local')->download()`.

### 🟡 GAP-14: File Storage pada Disk "documents" Bukan Private Path per PRD
- **PRD**: File harus disimpan di `storage/app/private/sk_documents/`.
- **Aktual**: Config filesystems punya disk `documents` dengan root `storage/app/documents/` (bukan `private/sk_documents/`).
- **Dampak**: Minor — path berbeda dari PRD spec tapi masih private (tidak di `public/`). Cukup aman.
- **Aksi**: Pertimbangkan ubah path disk ke `storage/app/private/sk_documents/` agar konsisten PRD.

### 🟡 GAP-15: Form Upload File PDF Tidak Terpasang di Semua Riwayat
- **PRD**: Kolom unggahan PDF dan Google Drive bersifat opsional di semua riwayat.
- **Aktual**: Migration riwayat pangkat/jabatan/kgb/pendidikan/latihan/penghargaan sudah punya kolom `file_pdf_path` dan `google_drive_link`. **Namun**, form Blade untuk CRUD riwayat **tidak menampilkan** input file upload atau Google Drive link field.
- **Aksi**: Tambahkan input `file` dan `google_drive_link` opsional di semua form riwayat (create & edit). Integrasikan `DocumentUploadService` di `RiwayatService`.

---

## 6. Modul Manajemen Master Data / Admin Setting (PRD §3.6)

### 🔴 GAP-16: CRUD Master Jabatan Belum Ada (UI)
- **PRD**: SuperAdmin bisa menambah, mengedit, dan menonaktifkan nomenklatur jabatan (nama, rumpun, BUP).
- **Aktual**: `JabatanService` hanya punya `getAllOrderedByName()`. **Tidak ada** controller, route, atau view untuk CRUD Jabatan. Data Jabatan hanya dikelola via seeder.
- **Aksi**: Buat `JabatanController` (CRUD), route, form Blade, dan method di `JabatanService`.

### 🔴 GAP-17: CRUD Master Tabel Gaji Berkala Belum Ada (UI)
- **PRD**: SuperAdmin bisa memperbarui tabel matriks gaji pokok.
- **Aktual**: `TabelGaji` hanya dikelola via seeder. **Tidak ada** controller, route, atau view untuk CRUD.
- **Aksi**: Buat `TabelGajiController`, view matriks (golongan × MKG), route, dan service.

### 🔴 GAP-18: CRUD Master Golongan & Pangkat Belum Ada (UI)
- **PRD**: SuperAdmin bisa mengelola daftar hierarki kepangkatan (I/a hingga IV/e).
- **Aktual**: Golongan/Pangkat hanya berupa Enum `GolonganRuang` yang di-hardcode di PHP. **Tidak bisa** dikelola dari UI tanpa mengubah kode sumber.
- **Aksi**: Pertimbangkan apakah golongan tetap enum (karena jarang berubah) atau perlu jadi tabel master. Jika harus CRUD, migrasi ke tabel `master_golongan` dengan seeder dari enum.

### 🟡 GAP-19: Field "Rumpun" pada Jabatan Tidak Ada
- **PRD**: Jabatan harus memiliki parameter Rumpun (Imigrasi/Pemasyarakatan/Struktural).
- **Aktual**: Migration `jabatans` punya `nama_jabatan`, `jenis_jabatan`, `bup`, `eselon_level`, `kelas_jabatan` — **tidak ada** kolom `rumpun`.
- **Aksi**: Tambah kolom `rumpun` (enum/string) di migration jabatans. Update model, seeder, dan form.

---

## 7. Seeder & Data Demo (PRD §6)

### 🟡 GAP-20: Email Seeder Tidak Sesuai Domain Kemenipas
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

### 🟡 GAP-25: NIP Validasi 18 Digit Belum Diterapkan Ketat
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

### 🟢 GAP-27: Audit Trail Belum Lengkap
- **PRD**: SuperAdmin bisa melihat audit trail.
- **Aktual**: Spatie Activity Log sudah terpasang di `Pegawai`, `RiwayatPangkat`, `RiwayatJabatan`, `RiwayatKgb`, `RiwayatPenghargaan`. View `activity-log/index.blade.php` ada.
- **Dampak**: Model `RiwayatHukumanDisiplin`, `RiwayatPendidikan`, `RiwayatLatihanJabatan`, `PenilaianKinerja` **belum** memiliki trait `LogsActivity`.
- **Aksi**: Tambahkan `LogsActivity` trait di model yang belum.

### 🟢 GAP-28: CSRF & Form Enkripsi File — Multipart Form Belum
- **Aktual**: Form riwayat menggunakan `method="POST"` dengan `@csrf` (baik). Namun tidak ada `enctype="multipart/form-data"` karena file upload belum diimplementasikan.
- **Aksi**: Saat implementasi upload file (GAP-15), pastikan semua form yang punya file input menggunakan `enctype="multipart/form-data"`.

---

## 10. Fitur UI/UX Tambahan

### 🟡 GAP-29: Pensiun Alert Level "Hijau" — Threshold Inkonsisten
- **PRD**: Hijau (Aman) = sisa waktu > 1 tahun (artinya semua yang >12 bulan). Filtering hanya menampilkan yang ≤24 bulan.
- **Aktual**: `PensiunService` menampilkan Hijau jika `≤ 24 bulan` dan skip yang `> 24 bulan`.
- **Dampak**: Sesuai PRD (hanya tampilkan alert ≤ 24 bulan). Tapi PRD bilang Hijau = > 1 tahun, sementara service bilang Hijau = 12-24 bulan. Perlu klarifikasi.
- **Aksi**: Klarifikasi dengan stakeholder apakah Hijau harus mencakup semua pegawai > 12 bulan (termasuk yang > 24 bulan) atau hanya 12-24 bulan.

### 🟡 GAP-30: Dashboard KGB — Kalkulasi Gaji Baru Otomatis Belum Ditampilkan
- **PRD**: Dasbor KGB harus menampilkan estimasi gaji baru dari lookup `TabelGaji`.
- **Aktual**: `KGBCalculationService` sudah ada method `getNextKGBSalary()`, tapi **tidak dipanggil** di `KGBController` atau view `kgb/index.blade.php`. Tabel hanya menampilkan TMT dan status tanpa estimasi gaji.
- **Aksi**: Integrasikan `KGBCalculationService` ke `KGBController`, tambahkan kolom "Est. Gaji Baru" di view.

### 🟡 GAP-31: Sidebar Belum Ada Menu "Admin Setting" / Master Data
- **PRD**: SuperAdmin punya menu CRUD Master Data (Jabatan, Gaji, Golongan).
- **Aktual**: Sidebar `layouts/app.blade.php` tidak punya menu group "Admin Setting" atau link ke master data.
- **Aksi**: Tambah section "Admin Setting" di sidebar, conditionally visible hanya untuk role SuperAdmin.

### 🟢 GAP-32: Satyalencana — Filter Berdasarkan Milestone Belum Ada di UI
- **PRD**: Filter berdasarkan milestone (10/20/30 tahun).
- **Aktual**: `SatyalencanaService::getCandidatesByMilestone()` sudah ada. Controller `SatyalencanaController` sudah memiliki parameter `milestone`. Tapi perlu verifikasi bahwa UI menampilkan tombol filter milestone.
- **Aksi**: Verifikasi view `satyalencana/index.blade.php` sudah memiliki filter buttons (10/20/30).

---

## Ringkasan Prioritas

| Prioritas | Count | ID |
|-----------|-------|----|
| 🔴 Kritis | 11 | GAP-01, 02, 03, 05, 06, 08, 10, 11, 13, 16, 17, 18 |
| 🟡 Sedang | 14 | GAP-04, 07, 09, 12, 14, 15, 19, 20, 21, 22, 23, 25, 26, 29, 30, 31 |
| 🟢 Rendah | 4 | GAP-24, 27, 28, 32 |

---

## Urutan Pengerjaan yang Disarankan

### Phase 1 — Foundation (Prasyarat)
1. **GAP-01** Spatie Permission setup
2. **GAP-02** Middleware RBAC di routes
3. **GAP-03** Policy classes
4. **GAP-05** Enum `JenisSanksi` + migrasi dropdown
5. **GAP-06** Kolom `durasi_tahun` di hukdis

### Phase 2 — Core Business Logic Fix
6. **GAP-08** Intervensi Hukdis di KGB Service
7. **GAP-09** Badge visual hukdis di view KGB
8. **GAP-10** Penundaan Pangkat shift di KenaikanPangkatService
9. **GAP-11** Penurunan Pangkat reset
10. **GAP-13** DocumentController secure download

### Phase 3 — Admin Setting UI
11. **GAP-16** CRUD Jabatan
12. **GAP-17** CRUD Tabel Gaji
13. **GAP-18** CRUD Golongan (evaluasi)
14. **GAP-19** Field Rumpun di Jabatan
15. **GAP-31** Sidebar Admin Setting

### Phase 4 — Polish & Completeness
16. **GAP-04** Role Pegawai self-service
17. **GAP-07** Upload SK Hukdis
18. **GAP-15** Upload file di semua form riwayat
19. **GAP-12** Proyeksi April/Oktober
20. **GAP-30** Estimasi gaji baru di KGB view
21. **GAP-20, 21, 22** Seeder fixes
22. **GAP-25** Validasi NIP 18 digit
23. **GAP-26** Unit & Feature tests
24. **GAP-27** Audit trail lengkap
25. Sisanya (GAP-14, 23, 24, 28, 29, 32)
