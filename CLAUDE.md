# CLAUDE.md — RT 10 Golden Park 2 Platform (Laravel)

> Last updated: 2026-06-07
> Live URL: https://gp2rt10.com
> Legacy (vanilla PHP): `/Users/galanteo/Desktop/Trash/code_project/rt10-platform`

---

## Project Overview
Full-featured RT management platform for **RT 10, Golden Park 2, Serang, Banten**.
Covers crowdfunding/donasi, kas & iuran bulanan, data kependudukan, surat administrasi, keamanan lingkungan, dan kelembagaan RT.

This is the **Laravel 13 migration** of the original vanilla PHP app. All existing features are ported; the framework upgrade is a prerequisite for developing Phases 10–16.

---

## Tech Stack
| Layer | Tool | Notes |
|---|---|---|
| Frontend | Blade + Vanilla JS | No npm/Vite, styles inline in views |
| Backend | Laravel 13, PHP ^8.3 | Eloquent ORM, Socialite, Middleware |
| Database | MySQL | Shared hosting (cPanel) |
| Auth | Google OAuth 2.0 | Laravel Socialite |
| Hosting | cPanel | gp2r2845 account |

**Domain:** `gp2rt10.com`
**Document root:** `public_html/` (FTP root = document root)
**PHP version:** 8.4 (production), 8.3/8.5 locally
**FTP host:** `ftp.gp2rt10.com` — credentials in `secret/deploy.json` (gitignored)
**DB credentials:** in `secret/deploy.json` (gitignored)

### Deployment Instructions
To deploy updates to the server, use `lftp` with credentials from `secret/deploy.json`:
```bash
# Upload changed files (example)
lftp -c "open ftp://ftp.gp2rt10.com; user 'gp2rt10@gp2rt10.com' 'PASS'; set ftp:passive-mode yes; set ssl:verify-certificate no; lcd /path/to/rt10-laravel; put app/Http/Controllers/Api/SomeController.php -o app/Http/Controllers/Api/SomeController.php"
```
- **vendor/** — only re-upload if composer dependencies change (zip via `zip -r /tmp/vendor.zip vendor/` then extract via cPanel)
- **After uploading PHP files** — clear cache via `deploy.php` or by deleting `bootstrap/cache/*.php`
- **composer.json platform** is set to `8.4.0` to match production

---

## Project Structure
```
rt10-laravel/
├── CLAUDE.md
├── .env / .env.example
├── app/
│   ├── Helpers/
│   │   └── Upload.php              ← UUID rename, MIME validate, stores to storage/uploads/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   └── GoogleController.php    ← Socialite redirect/callback/logout
│   │   │   └── Api/
│   │   │       ├── AuthController.php      ← me()
│   │   │       ├── UserController.php      ← updateProfile, myDonasi
│   │   │       ├── KampanyeController.php  ← CRUD + uploadFoto
│   │   │       ├── DonasiController.php    ← index, store, verify, uploadBukti
│   │   │       ├── KasController.php       ← index, summary, store
│   │   │       ├── AnggaranController.php  ← index, store
│   │   │       ├── PengeluaranController.php← index, publicList, store
│   │   │       ├── PengumumanController.php ← index, store
│   │   │       └── UsersController.php     ← index (admin)
│   │   └── Middleware/
│   │       ├── RequireLogin.php    ← alias 'login': 401 JSON / redirect /login
│   │       └── RequireAdmin.php    ← alias 'admin': 403 JSON / abort(403)
│   └── Models/
│       ├── User.php                ← Authenticatable, upsertFromGoogle, isAdmin()
│       ├── UnitRumah.php           ← syncForUser, isTaken, $timestamps=false
│       ├── Kampanye.php            ← refreshTerkumpul()
│       ├── Donasi.php
│       ├── Kas.php                 ← $timestamps=false
│       ├── Anggaran.php
│       ├── Pengeluaran.php
│       └── Pengumuman.php          ← $timestamps=false
├── bootstrap/app.php               ← API routes enabled, middleware aliases registered
├── config/
│   ├── app.php                     ← timezone=Asia/Jakarta, upload_max_mb
│   └── services.php                ← Google Socialite block
├── database/migrations/            ← 8 migrations (001–008), includes is_anonym on donasi
├── resources/views/
│   ├── layouts/app.blade.php       ← Nav, footer, CSS vars, doLogout() JS
│   ├── home.blade.php
│   ├── kampanye.blade.php
│   ├── kampanye-detail.blade.php   ← Fetches /api/kampanye/{id}, renders detail
│   ├── donasi.blade.php
│   ├── donatur.blade.php
│   ├── laporan.blade.php
│   ├── login.blade.php             ← Standalone (no layout)
│   ├── onboarding.blade.php        ← Standalone (no layout)
│   ├── dashboard/
│   │   ├── index.blade.php
│   │   ├── riwayat.blade.php
│   │   ├── konfirmasi.blade.php
│   │   └── profil.blade.php
│   └── admin/
│       ├── dashboard.blade.php
│       ├── kampanye.blade.php
│       ├── verifikasi.blade.php
│       ├── kas.blade.php
│       ├── anggaran.blade.php
│       ├── pengeluaran.blade.php
│       ├── laporan.blade.php
│       ├── warga.blade.php
│       └── pengumuman.blade.php
├── routes/
│   ├── web.php                     ← Page routes + auth routes (/auth/google etc)
│   └── api.php                     ← All /api/* routes
└── storage/uploads/                ← Uploaded files (bukti donasi, foto nota, foto kampanye)
```

---

## Key Auth Changes from Vanilla PHP
| Original | Laravel |
|---|---|
| `$_SESSION['user_id']` | `auth()->user()` / `Auth::user()` |
| `$_SESSION['role']` | `auth()->user()->role` |
| `Auth::requireLoginPage()` | `middleware('login')` on route |
| `Auth::requireAdminPage()` | `middleware('admin')` on route |
| `/api/auth/google` | `/auth/google` (web route, not API) |
| `/api/auth/callback` | `/auth/callback` (web route) |
| `APP_URL . '/api/auth/callback'` | `GOOGLE_REDIRECT_URI` in `.env` |
| Manual `$_POST` CSRF | `X-CSRF-TOKEN` header / `_token` field |
| `session_start()` + `$_SESSION` | Laravel session (file driver) |

**IMPORTANT:** Google Console redirect URI must be `https://gp2rt10.com/auth/callback`.

---

## CSRF in JavaScript
All Blade views pass `{{ csrf_token() }}` to JS:
```js
const _csrfToken = '{{ csrf_token() }}';
// JSON POST:
headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': _csrfToken }
// FormData POST:
formData.append('_token', _csrfToken);
```

---

## API Routes
All return JSON via `Response::success/error()`. Prefix: `/api/`

| Method | Route | Auth | Description |
|---|---|---|---|
| GET | `/api/auth/me` | login | Current user + units |
| PUT | `/api/user/profile` | login | Save nama, no_wa, units |
| GET | `/api/user/donasi` | login | Own donation history |
| GET | `/api/kampanye` | — | List all campaigns |
| GET | `/api/kampanye/{id}` | — | Campaign detail |
| POST | `/api/kampanye` | admin | Create campaign |
| PUT | `/api/kampanye/{id}` | admin | Update campaign |
| POST | `/api/kampanye/{id}/foto` | admin | Upload campaign photo |
| GET | `/api/donasi` | login | List (admin=all, warga=own) |
| POST | `/api/donasi` | login | Submit donation |
| POST | `/api/donasi/{id}/verify` | admin | Approve/reject |
| POST | `/api/donasi/{id}/bukti` | login | Upload proof photo |
| GET | `/api/kas` | admin | Cash ledger |
| GET | `/api/kas/summary` | — | Cash totals (public) |
| POST | `/api/kas` | admin | Manual entry |
| GET | `/api/anggaran` | admin | Budget list |
| POST | `/api/anggaran` | admin | Add budget line |
| GET | `/api/pengeluaran` | admin | Spending list |
| GET | `/api/pengeluaran/public` | — | Public spending |
| POST | `/api/pengeluaran` | admin | Record spending |
| GET | `/api/pengumuman` | — | Announcements list |
| POST | `/api/pengumuman` | admin | Send announcement |
| GET | `/api/users` | admin | All users with units |

---

## Page Routes
| URL | View | Auth |
|---|---|---|
| `/` | `home` | Public |
| `/kampanye` | `kampanye` | Public |
| `/kampanye/{id}` | `kampanye-detail` | Public |
| `/donasi` | `donasi` | Public |
| `/donatur` | `donatur` | Public |
| `/laporan` | `laporan` | Public |
| `/login` | `login` | Public |
| `/onboarding` | `onboarding` | login |
| `/dashboard` | `dashboard.index` | login |
| `/dashboard/riwayat` | `dashboard.riwayat` | login |
| `/dashboard/konfirmasi` | `dashboard.konfirmasi` | login |
| `/dashboard/profil` | `dashboard.profil` | login |
| `/admin` | `admin.dashboard` | admin |
| `/admin/kampanye` | `admin.kampanye` | admin |
| `/admin/verifikasi` | `admin.verifikasi` | admin |
| `/admin/kas` | `admin.kas` | admin |
| `/admin/anggaran` | `admin.anggaran` | admin |
| `/admin/pengeluaran` | `admin.pengeluaran` | admin |
| `/admin/laporan` | `admin.laporan` | admin |
| `/admin/warga` | `admin.warga` | admin |
| `/admin/pengumuman` | `admin.pengumuman` | admin |

---

## Database Schema
| Table | Key columns |
|---|---|
| `users` | `google_id`, `role` (warga/sekretaris/bendahara/admin/super_admin), `profil_lengkap` bool, `avatar_url` |
| `unit_rumah` | `blok` CHAR(1), `nomor` TINYINT, `is_primary`, `user_id` FK |
| `kampanye` | `target`/`terkumpul` unsignedBigInt, `status` (aktif/urgent/selesai/arsip) |
| `donasi` | `nominal`, `metode`, `status` (pending/verified/rejected), `is_anonym` |
| `kas` | `jenis` (masuk/keluar), `kategori_id` FK, `created_by` FK, no `updated_at` |
| `anggaran` | `pos`, `estimasi`, `realisasi`, `kampanye_id` FK, `kategori_id` FK |
| `pengeluaran` | `nominal`, `bukti_url`, `tanggal`, `anggaran_id` FK, `kategori_id` FK, `created_by` FK |
| `pengumuman` | `target` (semua/donatur), no `updated_at` |
| `kategori_keuangan` | `nama`, `jenis` (masuk/keluar/keduanya), `warna` |
| `lampiran` | polymorphic: `lampiran_type`, `lampiran_id`, `url`, `nama`, `created_by` FK |
| `admin_activity_log` | `user_id`, `action`, `description`, `model_type`, `model_id`, `meta` JSON |
| `iuran_periode` | `bulan`, `tahun`, `nominal`, `jatuh_tempo`, `keterangan`, `created_by` |
| `iuran_tagihan` | `unit_rumah_id` FK, `iuran_periode_id` FK, `status` (belum/pending/lunas/dispensasi) |
| `iuran_bayar` | `iuran_tagihan_id` FK, `nominal`, `metode`, `bukti_url`, `status` (pending/verified/rejected), `created_by`, `verified_by`, `verified_at` |
| `gsheet_config` | `spreadsheet_id`, `credentials_path`, `enabled` bool |
| `gsheet_sync_log` | `tab`, `status` (success/error), `rows_synced`, `error_message`, `synced_at` |
| `kepala_keluarga` | `unit_rumah_id` FK, `nik`, `nama`, `no_kk`, `no_wa`, `status_tinggal` |
| `anggota_keluarga` | `kepala_keluarga_id` FK, `nik`, `nama`, `hubungan`, `jenis_kelamin`, `tanggal_lahir` |
| `kendaraan` | `kepala_keluarga_id` FK, `jenis`, `merek`, `plat_nomor`, `warna` |

---

## Role System
| Role | Label | Access |
|---|---|---|
| `warga` | Warga | Warga dashboard only |
| `sekretaris` | Sekretaris | Admin panel: warga/kependudukan, pengumuman |
| `bendahara` | Bendahara | Admin panel: full keuangan module |
| `admin` | Admin | Same as super_admin (legacy) |
| `super_admin` | Super Admin | Everything + role management |

**User model helpers:** `isAdmin()`, `isSuperAdmin()`, `isBendahara()`, `isSekretaris()`, `hasRole(string|array)`, `roleLabel()`  
**Middleware:** `middleware('admin')` = any admin role; `middleware('admin:bendahara,admin,super_admin')` = specific roles  
**Set role:** `UPDATE users SET role='super_admin' WHERE email='...'`

---

## Business Rules
1. `donasi.status` flow: `pending` → `verified` or `rejected`
2. On verify: auto-create `kas` masuk entry + call `kampanye->refreshTerkumpul()`
3. `kampanye.terkumpul` = SUM of verified donasi (never set manually)
4. On `pengeluaran` save: auto-create `kas` keluar + update `anggaran.realisasi`
5. One unit (blok+nomor) → only ONE user account (unique constraint)
6. Uploads: UUID filename, MIME validated (jpg/png/pdf), max 5MB, stored in `storage/uploads/{subfolder}/`
7. Money: integer Rupiah, never float
8. Timezone: `Asia/Jakarta`
9. Google OAuth: Testing mode — only whitelisted emails can login
10. `iuran_tagihan.status` flow: `belum` → `pending` (on bayar submit) → `lunas` or back to `belum` (on verify/reject)
11. On iuran verify: auto-create `kas` masuk entry
12. Warga iuran self-pay is whitelist-gated: `IURAN_WHITELIST` env var (comma-separated emails)

---

## Coding Conventions
- PHP: `declare(strict_types=1)`, PHP 8.2 features OK
- SQL: Eloquent only — NO raw string interpolation
- API responses: always via `Response::json/success/error()`
- File uploads: UUID filename via `Upload::save()`, stored under `storage/uploads/`
- Money: integer Rupiah
- Dates: `TIMESTAMP` in DB, display `Asia/Jakarta`
- Error messages: Bahasa Indonesia (user-facing), English (logs)
- `@extends('layouts.app')` for public/warga pages with nav/footer
- `@extends('layouts.admin')` for ALL admin pages (dark sidebar layout)
- Standalone pages (login, onboarding): plain `<!DOCTYPE html>` with inline CSS

---

## Deployment Notes
- Run `php artisan migrate` (runs all migrations — currently 20+)
- Seed: run `database/seeders/` after first Google login + setting admin
- `php artisan storage:link` — symlinks `public/storage` → `storage/app/public` (or configure `public/uploads` directly)
- **Shared hosting note:** `storage/uploads/` needs to be web-accessible. Options:
  1. Symlink: `public/uploads → ../storage/uploads`
  2. Custom disk in `config/filesystems.php` pointing to `public/uploads`
- Update Google Console redirect URI to `https://gp2rt10.vensalor-kingdom.com/auth/callback`
- Session driver: `file` (no Redis needed on shared hosting)
- No npm/Vite build step — all CSS/JS is inline in Blade views

---

## Environment Variables (.env)
```
APP_URL=https://gp2rt10.vensalor-kingdom.com
APP_NAME="Kas RT 10 Golden Park 2"
APP_ENV=production
APP_KEY=base64:...  # php artisan key:generate

DB_HOST=localhost
DB_DATABASE=vens5525_rt10db
DB_USERNAME=vens5525_rt10user
DB_PASSWORD=your_password

GOOGLE_CLIENT_ID=xxx.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-xxx
GOOGLE_REDIRECT_URI=https://gp2rt10.vensalor-kingdom.com/auth/callback

UPLOAD_MAX_MB=5
UPLOAD_PATH=../storage/uploads
SESSION_DRIVER=file
SESSION_ENCRYPT=false

# Iuran warga self-pay whitelist (comma-separated emails, empty = disabled for all warga)
IURAN_WHITELIST=
```

---

## Phase Status

### [DONE] Phase 1–7 — Ported to Laravel (DONE)
All features from the vanilla PHP app have been migrated:
- Infrastructure, auth, public pages, warga dashboard, admin panel, API layer, warga sub-pages

### [TODO] Phase 8 — Polish & Production (TODO)
- [ ] QRIS image upload by admin
- [ ] WhatsApp confirmation link after donasi
- [ ] Notification when donasi verified
- [x] Export laporan to Excel — `ExcelExportService` (PhpSpreadsheet), endpoints at `/api/laporan/export/{kas,iuran,pengeluaran}`
- [ ] Pagination for donatur list and kas ledger
- [ ] Error pages (403, 404, 500) with proper Blade design
- [ ] `storage/uploads/` web-accessible path setup for shared hosting

### [DONE] Phase 10 — Keuangan & Kependudukan (DONE)
**Role system** (5 roles): `warga`, `sekretaris`, `bendahara`, `admin`, `super_admin`
- `admin_activity_log` table + `AdminActivityLog` model + `LogsAdminActivity` trait
- `/admin/aktivitas` page + `AdminActivityController`
- Role management: `PUT /api/admin/users/{id}/role` (super_admin only)

**Keuangan module** (`/admin/keuangan/*`):
- `KategoriKeuangan` model + `/api/kategori` CRUD
- `Lampiran` model (polymorphic attachments) + `/api/lampiran` CRUD
- Extended `Kas`, `Pengeluaran`, `Anggaran` with `kategori_id`, `created_by`
- `StatistikController`: dashboard, bulanan, kategori, iuran-compliance, saldo-trend
- `LaporanKeuanganController`: arus-kas, pengeluaran, iuran, neraca reports + Excel export
- `GsheetConfig` + `GsheetSyncLog` models, `GsheetService` (JWT-based, no google/apiclient)
- `GsheetController`: config, credentials upload, sync per tab / sync-all, logs
- `TransaksiInstanController`: quick kas entries
- Admin views: `dashboard`, `kas`, `transaksi-instan`, `pengeluaran`, `anggaran`, `kategori`, `laporan`, `gsheet`
- All admin views use `layouts.admin` (dark sidebar, 228px)

**Kependudukan module** (`/admin/kependudukan/*`):
- `KependudukanController`: stats, units, kepala KK CRUD, anggota keluarga CRUD, kendaraan CRUD
- Admin views: `index` (stats), `warga` (list), `detail` (per-KK detail with anggota + kendaraan)

### [DONE] Phase 12 — Iuran Bulanan (DONE — admin complete, warga whitelist-gated)
- Models: `IuranPeriode`, `IuranTagihan`, `IuranBayar`
- `IuranController` (admin): periode CRUD, tagihan generate/index/dispensasi, bayar store/verify/index, matrix
- `IuranWargaController` (warga): tagihan list, submit bayar with bukti, upload bukti
- Admin view: `/admin/keuangan/iuran` (3 tabs: Periode, Tagihan, Pembayaran Masuk)
- Admin view: `/admin/keuangan/iuran/matrix` (compliance matrix with Excel export + print)
- Warga view: `/dashboard/iuran` (tagihan list, payment modal, bukti upload)
- **Warga flow is whitelist-gated**: set `IURAN_WHITELIST=email1@gmail.com,email2@gmail.com` in `.env` to enable per user
- On verify: auto-creates `kas` masuk entry

### [TODO] Phase 11 — Laporan Kependudukan (TODO)

### [TODO] Phase 13 — Kelembagaan & Informasi RT (TODO)
New tables: `tata_tertib`, `inventaris`, `program_kerja`, `agenda`, `galeri`, `berita`, `saran_keluhan`, `bantuan_sosial`, `usulan_pembangunan`

### [TODO] Phase 14 — Surat & Dokumen (TODO)
New tables: `surat_permohonan`, `surat_keterangan`, `ttd_digital`

### [TODO] Phase 15 — Keamanan Lingkungan (TODO)
New tables: `jadwal_ronda`, `pos_keamanan`, `log_tamu_keamanan`

### [TODO] Phase 16 — Notifikasi & Integrasi (TODO)
- Email notifications (Laravel Mail)
- WhatsApp blast integration
- QRIS dinamis via Midtrans/Xendit
