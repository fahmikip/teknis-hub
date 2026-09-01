# TeknisHub

**Sistem Manajemen Dokumen & Informasi Divisi Teknis**

Pusat Dokumen dan Informasi Divisi Teknis — aplikasi untuk mengelola, menyimpan,
mencari, dan mengarsipkan dokumen resmi secara terpusat, aman, dan mudah ditemukan.

---

## Gambaran Umum

TeknisHub dibangun sebagai sistem dokumentasi internal Divisi Teknis KPU (Kabupaten)
dengan fokus pada kejelasan, konsistensi, kegunaan, aksesibilitas, arsitektur
informasi, performa, dan keamanan.

Desain mengadopsi warna khas KPU (merah `#C8102E`, putih, abu-abu netral) dengan
aksen emas `#C9A227`, tanpa gradien maupun efek dekoratif berlebihan.

### Status Pengembangan

Project dikerjakan secara bertahap. Saat ini berada pada **Fase 8 (Dashboard Statistik)** — seluruh
fase inti (1–8) beserta modul pendukung (Favorit, Audit Log, Pengaturan) telah **selesai**, teruji
(158 test pass), dan item Fase 9–11 (penyempurnaan UI/UX, keamanan, dan kesiapan produksi) sudah
dilakukan sebagian: otorisasi per-permision, validasi menyeluruh, dan panduan deployment.

### Fase 1 (Foundation)
- [x] Setup Laravel + MySQL + Authentication
- [x] Base layout TeknisHub (sidebar, topbar, content area)
- [x] Design system (warna KPU, tipografi, komponen)
- [x] Skeleton Dashboard
- [x] Navigasi responsive (drawer pada mobile)
- [x] Migration dasar + seeder roles/permissions/admin

### Fase 2 (Database Architecture & Core Data Model)
- [x] Skema database dokumen (jenis, kategori, tahapan, dokumen, versi, favorit, audit, setting)
- [x] Enum nilai status & akses dokumen (Draft/Aktif/Diubah/Tidak Berlaku/Diarsipkan; Internal/Terbatas/Publik)
- [x] Model Eloquent + relasi lengkap (polimorfik audit, hierarki tahapan, soft delete dokumen)
- [x] Seeder terpisah (permission, role, jenis dokumen, kategori, tahapan, setting)
- [x] Factory untuk pengujian & data dummy

### Fase 3 (Document Management, Upload & Metadata)
- [x] Document CRUD (list, detail, create, edit, archive/soft-delete)
- [x] Upload PDF (MIME + size validation, secure filename, private storage)
- [x] Metadata lengkap + validasi server-side (Form Request)
- [x] Private file storage (`storage/app/private`, tanpa URL publik langsung)
- [x] Otorisasi via `DocumentPolicy` + permission Phase 2
- [x] Audit log (create/update/archive)
- [x] Listing: pagination, sorting (whitelist), filter (tahun/kategori/jenis/tahapan/status/akses/tanggal), pencarian deskriptif
- [x] Versi awal (v1) dibuat saat dokumen baru

### Fase 4 (Search & Filter)
- [x] Pencarian lanjutan lintas kolom: judul, nomor, deskripsi, kata kunci, kategori, jenis, tahapan
- [x] Filter kombinasi: tahun, kategori, jenis dokumen, tahapan (dikelompokkan per jenis pemilihan), status, access level, rentang tanggal
- [x] Export CSV dari hasil pencarian/filter (streaming, charset UTF-8)
- [x] Sort whitelist (created_at / title / year / document_date) + arah urut
- [x] Navigasi stripe/pagination + "per page" (15/25/50/100)
- [x] Uji otomatis lengkap (`DocumentSearchFilterTest`)

### Fase 5 (Preview & Download Aman)
- [x] Route & controller `documents.download` dan `documents.preview`
- [x] Permission `download_documents` & `preview_documents` + `DocumentPolicy`
- [x] Streaming file dari private disk (tanpa URL publik) + `Content-Disposition`
- [x] 404 tertangani bila file tidak ada; file versi tertentu dapat diunduh
- [x] Tombol Preview (tab baru) & Download pada detail dokumen

### Fase 6 (Versioning & Arsip)
- [x] Riwayat versi dengan penomoran otomatis (v1, v2, ...) pada detail dokumen
- [x] Unggah versi baru (`documents.versions.store`) + catatan versi, terbatas permission `manage_document_versions`
- [x] Hapus versi (`documents.versions.destroy`); **v1 dilindungi**
- [x] Unduh per versi (`documents.versions.download`)
- [x] Arsip (soft delete) + daftar arsip + restore
- [x] `DocumentVersionTest` lengkap (9 kasus)

### Fase 7 (User, Role & Permission Management)
- [x] Manajemen pengguna: list (cari + filter role/status), tambah, edit, nonaktifkan
- [x] Permission `view_users` / `create_users` / `edit_users` / `deactivate_users` + `UserPolicy`
- [x] Proteksi admin: tidak dapat menonaktifkan diri sendiri maupun SUPER ADMIN
- [x] Manajemen role & permission: CRUD role + sinkronisasi permission (checkbox per grup)
- [x] Permission `manage_roles` + `RolePolicy`; role sistem (SUPER ADMIN/ADMIN/OPERATOR/VIEWER) tidak dapat dihapus
- [x] Role yang masih dipakai pengguna tidak dapat dihapus; penghapusan memakai soft delete
- [x] `UserCrudTest` (11) & `RoleCrudTest` (9)

### Fase 8 (Dashboard Statistik)
- [x] KPI: total dokumen, dokumen tahun berjalan, dokumen aktif, baru bulan ini, versi, arsip, jenis, jumlah favorit
- [x] Statistik per status dokumen
- [x] Dokumen terbaru (list + link detail)
- [x] Aktivitas terbaru dari audit log
- [x] Dokumen favorit pengguna
- [x] `DashboardController` + `DashboardTest`

### Modul Pendukung
- [x] **Favorit** — toggle bintang (daftar & detail dokumen), halaman favorit sendiri, hapus/masuk-sendiri via `FavoriteController` + `FavoritePolicy`
- [x] **Aktivitas (Audit Log)** — halaman riwayat dengan filter aksi/pengguna/pencarian; hanya `view_audit_logs`
- [x] **Pengaturan** — identitas aplikasi/instansi, teks footer, ukuran upload maksimal; hanya `manage_settings` (SUPER ADMIN); nilai tersimpan pada tabel `settings`
- [x] **Sidebar berbasis permission** — menu tersembunyi bila pengguna tidak punya hak akses
- [x] Footer attribusi pengembang (github.com/fahmikip)

---

## Alur Aplikasi (Flowchart)

Diagram di bawah menggambarkan alur utama pengguna dalam TeknisHub — mulai dari
autentikasi, navigasi, hingga manajemen dokumen dan kategori.

```mermaid
flowchart TD
    Start(["Mulai / Buka Aplikasi"])
    Start --> IsGuest{"Apakah sudah login?"}

    IsGuest -- "Belum" --> LoginPage["Halaman Login"]
    LoginPage --> CekLogin{"Kredensial valid?"}
    CekLogin -- "Tidak" --> LoginError["Tampilkan error login"]
    LoginError --> LoginPage
    CekLogin -- "Ya" --> Dashboard["Dashboard"]

    IsGuest -- "Sudah" --> Dashboard

    Dashboard --> Menu{"Pilih menu"}
    Menu -- "Dokumen" --> DocIndex["Daftar Dokumen"]
    Menu -- "Kategori" --> CatIndex["Kategori"]
    Menu -- "Referensi lain" --> Placeholder["Halaman placeholder fase berikutnya"]
    Menu -- "Profil / Keluar" --> Profile["Edit profil / logout"]

    %% ===== Dokumen =====
    DocIndex --> DShow["Detail Dokumen"]
    DocIndex -- "Tambah" --> DCreate{"Punya permisi create_documents?"}
    DCreate -- "Tidak" --> D403A["403 Forbidden"]
    DCreate -- "Ya" --> DForm["Form Tambah + Upload PDF"]
    DForm --> CekFile{"Upload PDF valid?"}
    CekFile -- "Tidak" --> FErr["Tampilkan error validasi"]
    FErr --> DForm
    CekFile -- "Ya" --> DStore["Simpan + versi v1 + audit log"]
    DStore --> DShow

    DShow -- "Edit" --> DEdit["Form Edit Metadata"]
    DEdit -- "Simpan" --> DUpdate{"Punya permisi edit_documents?"}
    DUpdate -- "Tidak" --> D403B["403 Forbidden"]
    DUpdate -- "Ya" --> DUpdOK["Perbarui metadata"]
    DUpdOK --> DShow

    DShow -- "Arsipkan" --> DArch{"Punya permisi archive_documents?"}
    DArch -- "Tidak" --> D403C["403 Forbidden"]
    DArch -- "Ya" --> DArchOK["Hapus (soft delete) + audit log"]
    DArchOK --> DocIndex

    %% ===== Kategori =====
    CatIndex -- "Tambah" --> CatCreate{"Punya permisi manage_categories?"}
    CatCreate -- "Tidak" --> C403A["403 Forbidden"]
    CatCreate -- "Ya" --> CatForm["Form Tambah Kategori"]
    CatForm -- "Simpan" --> CatStore["Simpan + slug unik"]
    CatStore --> CatIndex

    CatIndex -- "Edit" --> CatEdit["Form Edit"]
    CatEdit -- "Simpan" --> CatUpd["Perbarui kategori"]
    CatUpd --> CatIndex

    CatIndex -- "Hapus" --> CatDel{"Masih dipakai dokumen?"}
    CatDel -- "Ya" --> CatBlocked["Tolak: kategori terpakai"]
    CatBlocked --> CatIndex
    CatDel -- "Tidak" --> CatGone["Hapus kategori"]
    CatGone --> CatIndex

    classDef page fill:#fff7ed,stroke:#c9a227,stroke-width:1px;
    classDef action fill:#fef2f2,stroke:#c8102e,stroke-width:1px;
    classDef decision fill:#ffffff,stroke:#6b7280,stroke-width:1px;
    class LoginPage,Dashboard,DocIndex,CatIndex,DShow,DEdit,DForm,DStore,DUpdOK,DArchOK,CatForm,CatStore,CatEdit,CatUpd,CatGone,CatBlocked,Placeholder,Profile page;
    class LoginError,D403A,D403B,D403C,FErr,C403A action;
    class IsGuest,CekLogin,Menu,DCreate,CekFile,DUpdate,DArch,CatCreate,CatDel decision;
```

### Keterangan alur

1. **Autentikasi** — Halaman `/` mengarahkan ke login bila belum masuk. Setelah masuk, pengguna mendarat di Dashboard.
2. **Otorisasi** — Setiap aksi diperiksa lewat `Policy` + `Permission`:
   - Dokumen: `view_documents`, `create_documents`, `edit_documents`, `archive_documents`, `restore_documents`, `download_documents`, `preview_documents`, `manage_document_versions`
   - Referensi: `manage_categories`, `manage_stages`, `manage_document_types`
   - Pengguna & role: `view_users`, `create_users`, `edit_users`, `deactivate_users`, `manage_roles`
   - Sistem: `view_audit_logs`, `manage_settings`
   - Tanpa permisi → respons `403 Forbidden`, tombol disembunyikan via `@can(...)`, dan menu disembunyikan di sidebar.
3. **Upload PDF** — Dipvalidasi (MIME `application/pdf` + ukuran maks) sebelum disimpan ke `storage/app/private`. Versi awal (v1) dan audit log dibuat dalam satu transaksi.
4. **Hapus kategori** — Ditolak bila masih digunakan oleh dokumen.
 5. **Arsip dokumen** — Menggunakan soft delete; file fisik tidak dihapus sehingga dokumen tetap dapat dipulihkan.

---

## Tampilan Aplikasi

| Halaman Login | Dashboard |
|---|---|
| <img src="docs/screenshots/login.png" alt="Halaman Login" width="420"> | <img src="docs/screenshots/dashboard.png" alt="Dashboard" width="420"> |

---

## Requirements

- PHP **^8.2** (direkomendasikan 8.2+)
- Composer 2.x
- MySQL / MariaDB 10.4+
- Node.js 20+ & npm (untuk asset Vite)

---

## Installation

```bash
# 1. Clone / salin project ke web server (mis. XAMPP htdocs)

# 2. Install dependensi PHP
composer install

# 3. Install dependensi frontend
npm install

# 4. Salin file environment
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Siapkan database MySQL
```

## Environment

Salin `.env.example` menjadi `.env` lalu sesuaikan. Konfigurasi penting:

```dotenv
APP_NAME=TeknisHub
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=teknishub
DB_USERNAME=root
DB_PASSWORD=

# Admin awal (khusus development, JANGAN untuk produksi)
ADMIN_EMAIL=admin@teknishub.test
ADMIN_PASSWORD=password
ADMIN_NAME=Administrator
```

> **Keamanan**: Ubah `ADMIN_PASSWORD` dan selalu gunakan environment variable.
> Jangan pernah menaruh password produksi secara hardcoded.

## Database Setup

```bash
# Pastikan MySQL aktif, lalu buat database
CREATE DATABASE teknishub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Jalankan migration
php artisan migrate

# Jalankan seeder (permission, role, master data, admin, setting)
php artisan db:seed
```

Untuk membangun ulang dari awal saat development:

```bash
php artisan migrate:fresh --seed
```

## Storage Setup

Dokumen internal disimpan pada filesystem `local` (privat, tidak berada di direktori
publik). Jalankan symlink storage untuk asset publik bila diperlukan:

```bash
php artisan storage:link
```

## Menjalankan Development

```bash
# Terminal 1 — Vite dev server
npm run dev

# Terminal 2 — Aplikasi Laravel
php artisan serve
```

Akses aplikasi di `http://localhost:8000`. Login admin default (development):
`admin@teknishub.test` / `password`.

## Build

```bash
# Build asset untuk produksi
npm run build
```

## Testing

Aplikasi menggunakan PHPUnit. Konfigurasi `phpunit.xml` memakai SQLite in-memory
agar tidak mengganggu data development.

```bash
php artisan test
# atau
vendor/bin/phpunit
```

Ruang lingkup test saat ini: authentication (Breeze), RBAC seeder, render dashboard, relasi
database (dokumen↔kategori/jenis/versi, favorit unik, audit polimorfik, hierarki tahapan,
soft delete), manajemen dokumen (CRUD, otorisasi Viewer/Operator/Admin, upload/validasi PDF,
pencarian & filter, export CSV), download/preview/versioning dokumen, manajemen pengguna &
role/permission, favorit, audit log, dan pengaturan — **158 test pass**.

Lihat `docs/database.md` untuk dokumentasi skema database lengkap.

## Module Dokumen (Fase 3–6)

Routes (resource `documents`):

| Method | URI | Nama | Deskripsi |
|---|---|---|---|
| GET | `/documents` | documents.index | Daftar + filter + pencarian + pagination |
| GET | `/documents/create` | documents.create | Form tambah |
| POST | `/documents` | documents.store | Simpan + upload + versi awal |
| GET | `/documents/{document}` | documents.show | Detail + info file + riwayat versi |
| GET | `/documents/{document}/edit` | documents.edit | Form edit metadata |
| PUT | `/documents/{document}` | documents.update | Simpan perubahan |
| DELETE | `/documents/{document}` | documents.destroy | Arsip (soft delete) |
| GET | `/documents/{document}/download` | documents.download | Download PDF (stream privat) |
| GET | `/documents/{document}/preview` | documents.preview | Preview PDF inline |
| POST | `/documents/{document}/versions` | documents.versions.store | Unggah versi baru |
| DELETE | `/documents/{document}/versions/{version}` | documents.versions.destroy | Hapus versi (v1 dilindungi) |
| GET | `/documents/{document}/versions/{version}/download` | documents.versions.download | Download versi tertentu |
| GET | `/documents/recent` | documents.recent | Daftar dokumen terbaru |
| GET | `/documents/archived` | documents.archived | Daftar arsip (soft-deleted) |
| PUT | `/documents/{document}/restore` | documents.restore | Pulihkan dari arsip |
| GET | `/documents/export` | documents.export | Export CSV hasil filter |

### Modul lain

| Area | Routes utama | Permission |
|---|---|---|
| Favorit | `favorites.index`, `favorites.toggle/{document}`, `favorites.destroy/{favorite}` | Semua pengguna terautentikasi (favorit milik sendiri) |
| Aktivitas | `audit-logs.index` | `view_audit_logs` |
| Pengguna | `users.index/create/store/edit/update/destroy` | `view_users` / `create_users` / `edit_users` / `deactivate_users` |
| Role & Permission | `roles.index/create/store/edit/update/destroy` | `manage_roles` |
| Pengaturan | `settings.index`, `settings.update` | `manage_settings` |
| Referensi | `categories`, `stages`, `document-types` (resource) | `manage_categories` / `manage_stages` / `manage_document_types` |
| Dashboard | `GET /dashboard` | Terautentikasi |

### Konfigurasi upload (env)

```dotenv
# Maksimum ukuran upload dalam KB (default 20 MB)
DOCUMENT_MAX_UPLOAD_KB=20480

# Disk penyimpanan privat untuk dokumen
DOCUMENT_DISK=local
```

File disimpan privat pada `storage/app/private/documents/{tahun}/{uuid}.pdf`; nama
asli disimpan di `document_versions.original_filename`. Tidak ada URL publik
langsung ke file dokumen.

## Deployment

Aplikasi mendukung berbagai environment:

- **XAMPP / Laragon** — letakkan folder project pada direktori web server.
- **Linux VPS / shared hosting** — arahkan document root ke folder `public/`.

Langkah produksi umum:

```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
npm install && npm run build
```

## Struktur Direktori Utama

```
app/Http/Controllers/   Controller (Dokumen, Kategori, Tahapan, Jenis, Pengguna, Role, Favorit, Aktivitas, Pengaturan, ...)
app/Services/           Logika domain (DocumentService, DocumentFileService)
app/Policies/           Policy per modul (otorisasi berdasarkan permission)
app/Models/             Model (User, Role, Permission, ...)
database/migrations/    Skema database
database/seeders/       Seeder (roles, permissions, admin, master data, setting)
resources/css/          Desain system (warna KPU, komponen Tailwind)
resources/js/           Alpine.js
resources/views/        Layout, komponen Blade, halaman
routes/web.php          Definisi route
```

## Security Notes

- Autentikasi & otorisasi server-side (middleware, policy, gate).
- Password di-hash menggunakan mekanisme Laravel.
- Validasi input, CSRF, XSS, dan SQL injection protection bawaan Laravel.
- File dokumen internal disimpan privat (`storage/app/private`) dan hanya diakses
  lewat controller terotorisasi; tidak ada URL publik langsung.
- Validasi upload PDF menolak non-PDF dan file berukuran melebihi batas konfigurasi.
- Tidak ada data sensitif atau secret yang dicatat ke audit log / log.

## Roadmap

- **Fase 2** — ✅ Arsitektur database dokumen (kategori, tahapan, jenis, versi, favorit, audit) — selesai.
- **Fase 3** — ✅ Manajemen dokumen (CRUD, upload, metadata, validasi, storage, otorisasi) — selesai.
- **Fase 4** — ✅ Pencarian & filter lanjutan + export CSV — selesai.
- **Fase 5** — ✅ Preview & download dengan otorisasi — selesai.
- **Fase 6** — ✅ Versioning & arsip — selesai.
- **Fase 7** — ✅ Manajemen pengguna, role, permission, policy — selesai.
- **Fase 8** — ✅ Dashboard statistik — selesai.
- **Modul pendukung** — ✅ Favorit, Audit Log, Pengaturan — selesai.
- **Fase 9–11** — Penyempurnaan UI/UX, keamanan, dan kesiapan produksi (sebagian sudah diterapkan sepanjang pengembangan; sisanya item lanjutan: notifikasi, backup, dan optimasi lanjutan).
```
