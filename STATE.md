# STATE.md — Development Status

> Status perkembangan aplikasi SIMPEG Kemenipas.  
> Terakhir diperbarui: **10 Maret 2026**

---

## Current Status — Fitur yang Sudah Selesai

### Core Data & Models

- [x] 13 Eloquent Models dengan relationships lengkap
- [x] 9 PHP Enums (Agama, GolonganDarah, JenisJabatan, JenisKelamin, JenisSanksi, RumpunJabatan, StatusHukdis, StatusPernikahan, TingkatHukuman)
- [x] 19 database migrations
- [x] 6 seeders (User, MasterData, GolonganPangkat, Pegawai, TabelGaji, Database)
- [x] PegawaiFactory (with afterCreating hook for auto riwayat)
- [x] Activity logging (Spatie) pada semua model utama

### CRUD & Manajemen

- [x] **Pegawai** — CRUD lengkap + pencarian AJAX + paginasi server-side + validasi NIP 18 digit + **One-Stop Creation Flow** (auto gaji lookup, auto RiwayatPangkat & RiwayatJabatan)
- [x] **Biodata Pegawai** — Gelar depan/belakang, bagian (5 seksi Kanim), tipe pegawai (PNS/CPNS/PPPK), status kepegawaian (Aktif/Tidak Aktif), unit kerja default Kanim Jakut
- [x] **7 Riwayat Kepegawaian** — CRUD untuk Pangkat, Jabatan, KGB, Hukuman Disiplin, Pendidikan, Latihan Jabatan, Penilaian Kinerja
- [x] **Master Data Jabatan** — CRUD + filter rumpun + toggle active (SuperAdmin only)
- [x] **Master Data Golongan/Pangkat** — CRUD + toggle active (SuperAdmin only) — _refactor dari Enum ke tabel database_
- [x] **Master Data Tabel Gaji** — CRUD per golongan × masa kerja (SuperAdmin only)
- [x] **Document Management** — Upload file SK (PDF, maks 5MB) + link Google Drive opsional + inline PDF preview di browser + penamaan file bermakna (`NIP_Module_Timestamp_NamaAsli.pdf`) + kolom "Dokumen" di semua tab riwayat (show.blade.php) + link "Lihat Dokumen" di semua form edit
- [x] **UX: Tab Retention** — Setelah CRUD riwayat, halaman profil pegawai otomatis kembali ke tab yang sedang aktif via URL fragment (`#tab-{type}`)
- [x] **UX: Flash Messages** — Alert sukses/error yang deskriptif dengan icon, judul, pesan detail (termasuk info dokumen yang diunggah), dan tombol dismiss

### Monitoring & Laporan

- [x] **Dashboard** — Ringkasan data pegawai + chart distribusi (golongan, gender, usia, unit kerja) + alert KGB/Pensiun
- [x] **Monitoring KGB** — Alert jatuh tempo, eligibilitas, kalkulasi gaji otomatis (PP 15/2019), integrasi hukdis (penundaan KGB)
- [x] **Kenaikan Pangkat** — Analisis eligibilitas 4 syarat (masa kerja, SKP, latihan, hukuman disiplin), proyeksi periode April/Oktober, integrasi hukdis (penundaan + penurunan pangkat)
- [x] **Alert Pensiun** — Level alert (Hijau/Kuning/Merah/Hitam) berdasarkan BUP
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

---

## Next Steps

-
