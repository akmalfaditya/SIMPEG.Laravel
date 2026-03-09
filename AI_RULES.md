# AI_RULES.md — Coding Standards & AI Constraints

> Aturan ketat untuk AI assistant saat bekerja di codebase SIMPEG.  
> Semua perubahan kode **harus** mengikuti aturan ini tanpa pengecualian.

---

## 1. Coding Standards

### 1.1 PHP / Laravel

- **PHP 8.2+** — Gunakan fitur modern: typed properties, match expressions, named arguments, readonly properties, first-class callables, backed enums.
- **Strict Types** — Semua file PHP sudah **tidak** menggunakan `declare(strict_types=1)`. Jangan tambahkan kecuali diminta.
- **Return Types** — Semua public/protected method harus memiliki return type declaration.
- **Formatting** — Ikuti Laravel Pint (PSR-12 base). Gunakan `composer run test` untuk check.
    - 4 spasi indentasi
    - Opening brace di baris yang sama untuk class/method
    - Spasi setelah control structure keywords (`if (`, `foreach (`)
    - Trailing comma di array multi-line
- **Imports** — Selalu gunakan `use` statement lengkap, jangan FQCN inline.
- **Enum** — Gunakan PHP 8.1 Backed Enum untuk data statis. Setiap enum **harus** memiliki method `label(): string`.
- **Model Conventions**:
    - `$fillable` array (bukan `$guarded`)
    - `casts()` method (bukan property `$casts`)
    - Date fields di-cast ke `'date'`
    - Relationship methods: tipe eksplisit (`HasMany`, `BelongsTo`)
    - Trait `LogsActivity` (Spatie) pada semua model yang menyimpan data penting
- **Controller** — Thin controller. Hanya: validate → call service → return view/redirect. **Tidak ada** business logic di controller.
- **Service Layer** — Semua business logic di `app/Services/`. Satu service per domain entity.
- **DTO Pattern** — Gunakan DTO untuk transfer data dari request ke service. Method: `fromRequest(array)`, `toArray()`.
- **FormRequest** — Setiap store/update harus memiliki dedicated FormRequest. Penamaan: `Store{Entity}Request`, `Update{Entity}Request`.

### 1.2 Database / Migration

- **SQLite** — Database utama. Jangan gunakan fitur MySQL/PostgreSQL-specific.
- **Foreign Keys** — Selalu gunakan `->constrained()` dan `->cascadeOnDelete()` pada foreignId.
- **Migration Naming** — Format: `YYYY_MM_DD_NNNNNN_description.php`.
- **Seeder** — Semua data master di-seed. Seeder harus idempotent.

### 1.3 Frontend / Blade

- **Tailwind CSS v4** via `@tailwindcss/vite`. Jangan gunakan `@apply` atau custom CSS kecuali benar-benar diperlukan.
- **Blade Templates** — Gunakan layouts (`@extends`, `@section`) dan components.
- **Bahasa Indonesia** — Semua label, placeholder, error message untuk user menggunakan Bahasa Indonesia.
- **No inline JS framework** — Gunakan vanilla JS atau Axios (sudah include). Chart.js via CDN untuk chart.

---

## 2. AI Constraints (Wajib Dipatuhi)

### 2.1 Dependency Management

- **DILARANG** menambah package/library baru (Composer atau NPM) tanpa izin eksplisit dari user.
- **DILARANG** mengupgrade versi package yang sudah ada tanpa izin.
- **WAJIB** periksa apakah dependency yang dibutuhkan sudah tersedia sebelum mengusulkan package baru.

### 2.2 Code Reuse

- **WAJIB** gunakan Service class yang sudah ada. Jangan buat logic duplikat.
- **WAJIB** gunakan DTO yang sudah ada untuk transfer data request → service.
- **WAJIB** gunakan Enum yang sudah ada (`app/Enums/`) untuk mapping nilai statis.
- **WAJIB** gunakan `DocumentUploadService` untuk semua operasi file upload/delete.
- **WAJIB** cek existing methods di Service sebelum menulis method baru.
- **DILARANG** membuat helper function global atau utility class baru tanpa alasan kuat.

### 2.3 Architecture Compliance

- **DILARANG** meletakkan business logic di Controller, Model, atau Blade view.
- **DILARANG** menambah method ke Model yang seharusnya di Service (kecuali accessor/scope).
- **WAJIB** ikuti pattern: Controller → Service → Model. Tidak ada shortcut.
- **WAJIB** gunakan FormRequest untuk validasi, bukan manual validation di controller.
- **DILARANG** menggunakan raw SQL queries. Gunakan Eloquent ORM atau Query Builder.

### 2.4 Naming Conventions

| Element         | Convention                      | Contoh                     |
| --------------- | ------------------------------- | -------------------------- |
| Model           | PascalCase singular             | `RiwayatPangkat`           |
| Controller      | PascalCase + Controller         | `KGBController`            |
| Service         | PascalCase + Service            | `KGBCalculationService`    |
| FormRequest     | Store/Update + Entity + Request | `StorePangkatRequest`      |
| DTO             | Entity + DTO                    | `RiwayatPangkatDTO`        |
| Migration       | snake_case verb + table         | `create_jabatans_table`    |
| Enum            | PascalCase                      | `JenisSanksi`              |
| Blade View      | kebab-case/snake_case           | `create-pangkat.blade.php` |
| Route Names     | dot.notation                    | `riwayat.pangkat.store`    |
| Database Table  | snake_case plural               | `riwayat_pangkats`         |
| Database Column | snake_case                      | `tmt_pangkat`              |

### 2.5 Safety

- **DILARANG** menjalankan `migrate:fresh` atau `migrate:rollback` tanpa konfirmasi user.
- **DILARANG** menghapus file atau migration yang sudah ada tanpa konfirmasi.
- **WAJIB** backup context sebelum refactoring besar — baca semua file terkait terlebih dahulu.
- **WAJIB** validasi semua input dari user di boundary (FormRequest). Trust internal code.

---

## 3. File Reference

| Dokumen                 | Isi                                               |
| ----------------------- | ------------------------------------------------- |
| `README.md`             | Overview, instalasi, fitur utama                  |
| `TUTORIAL.md`           | Step-by-step tutorial pembuatan (termasuk ERD)    |
| `ARCHITECTURE.md`       | Arsitektur teknis, tech stack, struktur direktori |
| `STATE.md`              | Status development saat ini                       |
| `FUTURE_DEVELOPMENT.md` | Gap analysis PRD vs codebase, roadmap             |
