# Dokumentasi Database TeknisHub

Arsitektur database Divisi Teknis berbasis MySQL/MariaDB (utf8mb4_unicode_ci).
Skema dibuat dengan Laravel migrations dan seeder terpisah.

---

## Diagram Relasi (Ringkas)

```
users 1──* role_user *──1 roles *──* permission_role *──* permissions
 users 1──* documents (created_by / updated_by)
 document_types 1──* documents
 categories     1──* documents
 stages (self: parent_id) 1──* documents
 documents 1──* document_versions  *──1 users (uploaded_by)
 users 1──* favorites *──1 documents
 users 1──* audit_logs *──(auditable polymorphic) documents
 settings (key-value)
```

---

## Tabel

### users (Fase 1)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK AI | |
| name | string | |
| email | string UNIQUE | |
| password | string | hash |
| username | string | |
| is_active | boolean default true | status aktif |
| last_login_at | timestamp nullable | |
| email_verified_at | timestamp nullable | |
| remember_token | string nullable | |
| timestamps | | |

### roles / permissions (Fase 1 + tambahan Fase 2)
- `roles`: id, name UNIQUE, label, **slug UNIQUE (tambah F2)**, **description (tambah F2)**, timestamps, softDeletes
- `permissions`: id, name UNIQUE, label, group, **slug (tambah F2)**, **description (tambah F2)**, timestamps
- `permission_role` (PIVOT): primary (permission_id, role_id)
- `role_user` (PIVOT): primary (role_id, user_id)

Daftar permission (Fase 2): `view_dashboard`, `view_documents`, `create_documents`,
`edit_documents`, `archive_documents`, `restore_documents`, `download_documents`,
`preview_documents`, `manage_document_versions`, `manage_categories`,
`manage_stages`, `manage_document_types`, `view_users`, `create_users`,
`edit_users`, `deactivate_users`, `manage_users`, `view_audit_logs`,
`manage_roles`, `manage_permissions`, `manage_settings`.

Matrix role — SUPER ADMIN (semua permission), ADMIN (tanpa manajemen role/permission),
OPERATOR (dokumen + versi), VIEWER (baca saja). Lihat `RoleSeeder`.

### document_types (master data)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| name | string | |
| slug | string UNIQUE | |
| description | text nullable | |
| is_active | boolean default true (index) | |
| timestamps | | |

### categories (master data)
id, name, slug UNIQUE, description nullable, is_active bool default true (index), timestamps.

### stages (master data, hierarki)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| name | string | |
| slug | string | |
| election_type | string default 'general' | enum: general/pemilu/pilkada |
| description | text nullable | |
| parent_id | bigint FK → stages nullable, nullOnDelete | hierarki sub-tahapan |
| is_active | boolean default true (index) | |
| sort_order | unsignedInteger default 0 | |
| timestamps | | |

UNIQUE: (slug, election_type)

### documents (Fase 2 — tabel inti)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| title | string (index) | |
| document_number | string nullable (index) | |
| document_type_id | FK → document_types, restrict | |
| category_id | FK → categories, restrict | |
| stage_id | FK → stages nullable, restrict | |
| year | unsignedInteger (index) | |
| document_date | date nullable | |
| status | string default 'draft' (index) | enum: draft/active/revised/invalid/archived |
| access_level | string default 'internal' (index) | enum: internal/restricted/public |
| description | text nullable | |
| keywords | text nullable | |
| created_by | FK → users nullable, nullOnDelete (index) | |
| updated_by | FK → users nullable, nullOnDelete | |
| created_at (index) | | |
| updated_at | | |
| deleted_at | softDeletes | |

Hanya `documents` yang memakai SoftDeletes (arsip tanpa menghapus riwayat).

### document_versions (Fase 2)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| document_id | FK → documents, cascade | |
| version_number | unsignedInteger | |
| file_path | string | path privat |
| original_filename | string nullable | |
| mime_type | string nullable | |
| file_size | unsignedBigInteger nullable | |
| checksum | string nullable (index) | hash integritas file |
| notes | text nullable | |
| uploaded_by | FK → users nullable, nullOnDelete (index) | |
| timestamps | | |

UNIQUE: (document_id, version_number)

### favorites (Fase 2)
id, user_id FK → users cascade, document_id FK → documents cascade, timestamps.
UNIQUE: (user_id, document_id)

### audit_logs (Fase 2 — relasi polimorfik)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| user_id | FK → users nullable, nullOnDelete (index) | null = aksi sistem |
| action | string (index) | |
| auditable_type | string nullable | MORPH |
| auditable_id | bigint nullable | MORPH (index gabungan) |
| description | text nullable | |
| ip_address | string 45 nullable | |
| user_agent | string nullable | |
| metadata | json nullable | muatan tambahan |
| created_at (index) | | |
| updated_at | | |

### settings (Fase 2 — key-value)
id, key string UNIQUE, value text nullable, type string default 'string', description text nullable, timestamps.

---

## Keputusan Desain

- **Cascade foreign key** hanya untuk anak yang tidak punya makna tanpa induk
  (`document_versions`, `favorites`). Master data (`document_types`, `categories`,
  `stages`) memakai `restrict` agar tak terhapus saat masih dipakai.
- **nullOnDelete** pada `created_by`/`updated_by`/`uploaded_by`/`user_id` agar riwayat
  dokumen & audit tersimpan meski user dihapus.
- **Soft delete** hanya di `documents`: arsip = soft delete; versi dipertahankan.
  Hard delete (forceDelete) menghapus anak via cascade.
- Status/akses memakai **string** + **enum PHP** (`App\Enums`), bukan ENUM SQL,
  agar mudah dikembangkan dan dikontrol validasi.
- Seeder **terpisah per domain** dan idempotent (updateOrCreate) untuk keamanan
  re-run.

## Seeder

`DatabaseSeeder` memanggil: `PermissionSeeder`, `RoleSeeder`, `DocumentTypeSeeder`,
`CategorySeeder`, `StageSeeder`, `SettingsSeeder`.

## Factory

`CategoryFactory`, `DocumentTypeFactory`, `StageFactory`, `DocumentFactory`,
`DocumentVersionFactory`, `UserFactory` (sudah ada).
