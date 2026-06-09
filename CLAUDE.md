# CLAUDE.md — RT 10 Golden Park 2 Platform (Laravel)

> Last updated: 2026-06-08
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
│   │   └── Upload.php              ← UUID rename, MIME validate, stores to public/uploads/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   └── GoogleController.php         ← Socialite redirect/callback/logout
│   │   │   └── Api/
│   │   │       ├── AuthController.php            ← me()
│   │   │       ├── UserController.php            ← updateProfile, myDonasi
│   │   │       ├── KampanyeController.php        ← CRUD + uploadFoto
│   │   │       ├── DonasiController.php          ← index, store, verify (+ email/WA notify), uploadBukti
│   │   │       ├── KasController.php             ← index, summary, store (paginated)
│   │   │       ├── AnggaranController.php        ← index, store
│   │   │       ├── PengeluaranController.php     ← index, publicList, store
│   │   │       ├── PengumumanController.php      ← index, store (+ email/WA blast)
│   │   │       ├── UsersController.php           ← index (admin)
│   │   │       ├── IuranController.php           ← admin: periode/tagihan/bayar CRUD + verify (+ email/WA notify)
│   │   │       ├── IuranWargaController.php      ← warga: tagihan list, submit bayar, upload bukti
│   │   │       ├── KependudukanController.php    ← kepala KK/anggota/kendaraan CRUD + laporan + Excel export
│   │   │       ├── SuratController.php           ← permohonan surat CRUD, status update, print
│   │   │       ├── BantuanSosialController.php   ← CRUD
│   │   │       ├── UsulanPembangunanController.php ← CRUD + public submit
│   │   │       └── InventarisController.php      ← CRUD
│   │   └── Middleware/
│   │       ├── RequireLogin.php    ← alias 'login': 401 JSON / redirect /login
│   │       └── RequireAdmin.php    ← alias 'admin': 403 JSON / abort(403)
│   ├── Mail/
│   │   ├── AgendaReminderMail.php   ← agenda reminder (sent by AgendaController)
│   │   ├── DonasiStatusMail.php     ← donasi verified/rejected notification
│   │   ├── IuranStatusMail.php      ← iuran bayar verified/rejected notification
│   │   └── PengumumanMail.php       ← pengumuman blast email
│   ├── Models/
│   │   ├── User.php                ← Authenticatable, upsertFromGoogle, isAdmin()
│   │   ├── UnitRumah.php           ← syncForUser, isTaken, $timestamps=false
│   │   ├── Kampanye.php            ← refreshTerkumpul()
│   │   ├── Donasi.php
│   │   ├── Kas.php                 ← $timestamps=false
│   │   ├── Anggaran.php
│   │   ├── Pengeluaran.php
│   │   ├── Pengumuman.php          ← $timestamps=false
│   │   ├── IuranPeriode.php / IuranTagihan.php / IuranBayar.php
│   │   ├── KepalaKeluarga.php / AnggotaKeluarga.php / Kendaraan.php
│   │   ├── SuratPermohonan.php     ← JENIS const (12 types), data_tambahan JSON
│   │   ├── BantuanSosial.php / UsulanPembangunan.php / Inventaris.php
│   │   └── ...
│   └── Services/
│       ├── ExcelExportService.php  ← PhpSpreadsheet: kas, iuran, pengeluaran, kependudukan exports
│       ├── WhatsappService.php     ← Fonnte API wrapper: send(), blast(); disabled if FONNTE_TOKEN empty
│       ├── SeederManager.php       ← Centralized seeder registry
│       └── GsheetService.php       ← Google Sheets JWT sync
├── bootstrap/app.php               ← API routes enabled, middleware aliases registered
├── config/
│   ├── app.php                     ← timezone=Asia/Jakarta, upload_max_mb
│   └── services.php                ← Google Socialite + Fonnte (services.fonnte.token) block
├── database/migrations/            ← 25+ migrations
├── resources/views/
│   ├── layouts/
│   │   ├── app.blade.php           ← Nav, footer, CSS vars, doLogout() JS
│   │   └── admin.blade.php         ← Dark sidebar (rail + panel), all admin pages
│   ├── emails/
│   │   ├── agenda-reminder.blade.php
│   │   ├── donasi-status.blade.php ← Styled HTML for verify/reject
│   │   ├── iuran-status.blade.php  ← Styled HTML for iuran confirm/reject
│   │   └── pengumuman.blade.php    ← Styled HTML for pengumuman blast
│   ├── home.blade.php / kampanye.blade.php / donasi.blade.php / donatur.blade.php
│   ├── laporan.blade.php / galeri.blade.php / agenda.blade.php / informasi.blade.php
│   ├── login.blade.php / onboarding.blade.php ← Standalone (no layout)
│   ├── dashboard/
│   │   ├── index.blade.php / riwayat.blade.php / konfirmasi.blade.php / profil.blade.php
│   │   ├── iuran.blade.php         ← Warga iuran self-pay (whitelist-gated)
│   │   └── surat.blade.php         ← Warga surat permohonan
│   └── admin/
│       ├── dashboard.blade.php / kampanye.blade.php / verifikasi.blade.php
│       ├── kas.blade.php / anggaran.blade.php / pengeluaran.blade.php / laporan.blade.php
│       ├── warga.blade.php / pengumuman.blade.php / surat.blade.php
│       ├── keuangan/               ← dashboard, kas, transaksi-instan, pengeluaran, anggaran, kategori, iuran, matrix, laporan, gsheet
│       ├── kependudukan/           ← index, warga, detail, kendaraan, peta, laporan
│       └── kelembagaan/            ← agenda, galeri, berita, tata-tertib, program-kerja, saran, usulan, bantuan-sosial, inventaris
├── routes/
│   ├── web.php                     ← Page routes + auth routes (/auth/google etc)
│   └── api.php                     ← All /api/* routes
└── public/uploads/                 ← Uploaded files (bukti donasi, foto nota, foto kampanye) — web-accessible
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
| POST | `/api/pengumuman` | admin | Send announcement (+ optional `kirim_email`/`kirim_wa` blast) |
| GET | `/api/users` | admin | All users with units |
| GET | `/api/kependudukan/laporan` | sekretaris+ | Per-blok kependudukan summary |
| GET | `/api/kependudukan/laporan/detail` | sekretaris+ | Full KK list with anggota+kendaraan (`?blok=`) |
| GET | `/api/kependudukan/laporan/export` | sekretaris+ | Excel export 2-sheet (`?blok=`) |

---

## Page Routes
| URL | View | Auth |
|---|---|---|
| `/` | `home` | Public |
| `/kampanye` | `kampanye` | Public |
| `/kampanye/{id}` | `kampanye-detail` | Public |
| `/donasi` | `donasi` | login |
| `/donatur` | `donatur` | Public |
| `/laporan` | `laporan` | login |
| `/agenda` | `agenda` | Public |
| `/galeri` | `galeri` | Public |
| `/informasi` | `informasi` | Public |
| `/login` | `login` | Public |
| `/onboarding` | `onboarding` | login |
| `/dashboard` | `dashboard.index` | login |
| `/dashboard/riwayat` | `dashboard.riwayat` | login |
| `/dashboard/bayar` | `dashboard.bayar` | login |
| `/dashboard/konfirmasi` | `dashboard.konfirmasi` | login |
| `/dashboard/profil` | `dashboard.profil` | login |
| `/dashboard/iuran` | `dashboard.iuran` | login |
| `/dashboard/surat` | `dashboard.surat` | login |
| `/admin` | `admin.dashboard` | admin |
| `/admin/kampanye` | `admin.kampanye` | bendahara+ |
| `/admin/verifikasi` | `admin.verifikasi` | bendahara+ |
| `/admin/kas` | `admin.kas` | bendahara+ |
| `/admin/anggaran` | `admin.anggaran` | bendahara+ |
| `/admin/pengeluaran` | `admin.pengeluaran` | bendahara+ |
| `/admin/laporan` | `admin.laporan` | bendahara+ |
| `/admin/keuangan` | `admin.keuangan.dashboard` | bendahara+ |
| `/admin/keuangan/kas` | `admin.keuangan.kas` | bendahara+ |
| `/admin/keuangan/transaksi-instan` | `admin.keuangan.transaksi-instan` | bendahara+ |
| `/admin/keuangan/pengeluaran` | `admin.keuangan.pengeluaran` | bendahara+ |
| `/admin/keuangan/anggaran` | `admin.keuangan.anggaran` | bendahara+ |
| `/admin/keuangan/kategori` | `admin.keuangan.kategori` | bendahara+ |
| `/admin/keuangan/iuran` | `admin.keuangan.iuran` | bendahara+ |
| `/admin/keuangan/iuran/matrix` | `admin.keuangan.matrix` | bendahara+ |
| `/admin/keuangan/laporan` | `admin.keuangan.laporan` | bendahara+ |
| `/admin/keuangan/gsheet` | `admin.keuangan.gsheet` | bendahara+ |
| `/admin/warga` | `admin.warga` | sekretaris+ |
| `/admin/pengumuman` | `admin.pengumuman` | sekretaris+ |
| `/admin/surat` | `admin.surat` | sekretaris+ |
| `/admin/kelembagaan/agenda` | `admin.kelembagaan.agenda` | sekretaris+ |
| `/admin/kelembagaan/galeri` | `admin.kelembagaan.galeri` | sekretaris+ |
| `/admin/kelembagaan/berita` | `admin.kelembagaan.berita` | sekretaris+ |
| `/admin/kelembagaan/tata-tertib` | `admin.kelembagaan.tata-tertib` | sekretaris+ |
| `/admin/kelembagaan/program-kerja` | `admin.kelembagaan.program-kerja` | sekretaris+ |
| `/admin/kelembagaan/saran` | `admin.kelembagaan.saran` | sekretaris+ |
| `/admin/kelembagaan/usulan` | `admin.kelembagaan.usulan` | sekretaris+ |
| `/admin/kelembagaan/bantuan-sosial` | `admin.kelembagaan.bantuan-sosial` | sekretaris+ |
| `/admin/kelembagaan/inventaris` | `admin.kelembagaan.inventaris` | sekretaris+ |
| `/admin/kependudukan` | `admin.kependudukan.index` | sekretaris+ |
| `/admin/kependudukan/warga` | `admin.kependudukan.warga` | sekretaris+ |
| `/admin/kependudukan/warga/{id}` | `admin.kependudukan.detail` | sekretaris+ |
| `/admin/kependudukan/kendaraan` | `admin.kependudukan.kendaraan` | sekretaris+ |
| `/admin/kependudukan/peta` | `admin.kependudukan.peta` | sekretaris+ |
| `/admin/kependudukan/laporan` | `admin.kependudukan.laporan` | sekretaris+ |
| `/admin/aktivitas` | `admin.aktivitas` | admin+ |
| `/admin/pengaturan` | `admin.pengaturan` | admin+ |
| `/admin/seeder` | `admin.seeder` | super_admin |

---

## Database Schema
| Table | Key columns |
|---|---|
| `users` | `google_id`, `role` (warga/sekretaris/bendahara/admin/super_admin), `profil_lengkap` bool, `avatar_url` |
| `unit_rumah` | `blok` CHAR(1), `nomor` TINYINT, `is_primary`, `user_id` FK, `lat` decimal(10,8) nullable, `lng` decimal(11,8) nullable |
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
| `seeder_runs` | `seeder_key`, `seeded_ids` JSON, `run_by` FK, `run_at`, `rolled_back_by` FK, `rolled_back_at`, `status` (applied/rolled_back) |
| `app_settings` | `key` unique, `value` text, `type` (string/boolean/integer/json), `label`, `group` |
| `transaksi_instan` | `nama`, `jenis` (masuk/keluar), `nominal`, `kategori_id` FK, `keterangan` |
| `agenda` | `judul`, `deskripsi`, `tanggal`, `waktu_mulai`, `waktu_selesai`, `lokasi`, `audience` (semua/pengurus/warga), `recurrence` (none/weekly/monthly), `color` |
| `galeri` | `nama` (album name), `deskripsi`, `cover_url`, `created_by` FK |
| `galeri_foto` | `galeri_id` FK, `url`, `keterangan`, `urutan`, `created_by` FK |
| `berita` | `judul`, `konten`, `cover_url`, `status` (draft/published), `created_by` FK |
| `tata_tertib` | `judul`, `konten`, `urutan`, `created_by` FK |
| `program_kerja` | `nama`, `deskripsi`, `bidang`, `status` (rencana/berjalan/selesai), `tahun`, `created_by` FK |
| `saran_keluhan` | `nama`, `kontak`, `jenis` (saran/keluhan), `pesan`, `status` (baru/dibaca/ditindaklanjuti/selesai), `catatan_admin`, `user_id` FK nullable |
| `bantuan_sosial` | `nama_penerima`, `unit_rumah_id` FK, `jenis` (sembako/uang_tunai/kesehatan/pendidikan/lainnya), `nominal`, `keterangan`, `tanggal`, `created_by` FK |
| `usulan_pembangunan` | `judul`, `deskripsi`, `lokasi`, `prioritas` (rendah/sedang/tinggi), `status` (baru/dikaji/disetujui/ditolak/selesai), `catatan_admin`, `user_id` FK, `created_by` FK |
| `inventaris` | `nama`, `kode`, `jumlah`, `satuan`, `kondisi` (baik/rusak_ringan/rusak_berat), `lokasi`, `tanggal_beli`, `nilai`, `keterangan`, `created_by` FK |
| `surat_permohonan` | `user_id` FK, `jenis` (12 types), `keperluan`, `status` (menunggu/diproses/selesai/ditolak), `catatan`, `tanggal_surat`, `data_tambahan` JSON, `created_by` FK |

---

## Centralized Seeder Management

Admin panel page: `/admin/seeder` — **super_admin only**  
API: `GET /api/admin/seeder`, `POST /api/admin/seeder/{key}/run`, `POST /api/admin/seeder/{runId}/rollback`

**How it works:**
- `app/Services/SeederManager.php` is the single registry of all managed seeders.
- Each seeder defines: `key`, `label`, `description`, `group`, `class`, `tables` (which DB tables it inserts into), `depends_on` (other seeder keys that must run first), `warning` (optional UI caution).
- When a seeder runs via the panel, `SeederManager` snapshots the max ID of each `tables` entry before running, then captures newly inserted IDs after. These IDs are stored in `seeder_runs.seeded_ids` JSON.
- Rollback deletes all rows in `seeded_ids` and marks the run as `rolled_back`. Dependency order is enforced: dependents must be rolled back before their parent.
- Every new feature that needs seed data must add an entry to `SeederManager::definitions()`. The seeder class itself goes in `database/seeders/` as usual.

**Currently registered seeders (sample/demo content only):**
| Key | Label | Tables | Depends On |
|---|---|---|---|
| `informasi` | Konten Informasi | `berita`, `tata_tertib`, `program_kerja`, `pengumuman` | — |
| `galeri` | Album Galeri | `galeri`, `galeri_foto` | — |
| `agenda` | Agenda & Jadwal | `agenda` | — |

> Operational seeders (kategori_keuangan, kampanye) are run via `php artisan db:seed` only — not exposed in the panel.

**Adding a new seeder:**
1. Create the seeder class in `database/seeders/` (standard Laravel seeder, no changes needed).
2. Add an entry to the `definitions()` array in `app/Services/SeederManager.php`.
3. The panel will automatically pick it up.

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
2. On verify: auto-create `kas` masuk entry + call `kampanye->refreshTerkumpul()` + notify donor via email + WA
3. `kampanye.terkumpul` = SUM of verified donasi (never set manually)
4. On `pengeluaran` save: auto-create `kas` keluar + update `anggaran.realisasi`
5. One unit (blok+nomor) → only ONE user account (unique constraint)
6. Uploads: UUID filename, MIME validated (jpg/png/pdf), max 5MB, stored in `storage/uploads/{subfolder}/`
7. Money: integer Rupiah, never float
8. Timezone: `Asia/Jakarta`
9. Google OAuth: Testing mode — only whitelisted emails can login
10. `iuran_tagihan.status` flow: `belum` → `pending` (on bayar submit) → `lunas` or back to `belum` (on verify/reject)
11. On iuran verify: auto-create `kas` masuk entry + notify warga via email + WA
12. Warga iuran self-pay is whitelist-gated: `IURAN_WHITELIST` env var (comma-separated emails)
13. Notifications (email/WA) are fire-and-forget — failure is logged but never blocks API response
14. WA is disabled silently when `FONNTE_TOKEN` env var is empty; email disabled when `MAIL_HOST` not configured

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
- `php artisan storage:link` — symlinks `public/storage` → `storage/app/public` (not needed for uploads — see below)
- **Uploads:** Files go to `public/uploads/{subfolder}/` via `Upload::save()` — directly web-accessible, no symlink needed
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
SESSION_DRIVER=file
SESSION_ENCRYPT=false

# Iuran warga self-pay whitelist (comma-separated emails, empty = disabled for all warga)
IURAN_WHITELIST=

# Email — cPanel SMTP (port 465 SSL or 587 TLS)
MAIL_MAILER=smtp
MAIL_HOST=mail.gp2rt10.com
MAIL_PORT=465
MAIL_USERNAME=noreply@gp2rt10.com
MAIL_PASSWORD=your_mail_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@gp2rt10.com
MAIL_FROM_NAME="RT 10 Golden Park 2"

# WhatsApp via Fonnte (https://fonnte.com) — leave blank to disable silently
FONNTE_TOKEN=
```

---

## Phase Status

### [DONE] Phase 1–7 — Ported to Laravel (DONE)
All features from the vanilla PHP app have been migrated:
- Infrastructure, auth, public pages, warga dashboard, admin panel, API layer, warga sub-pages

### [PARTIAL] Phase 8 — Polish & Production (mostly done)
- [ ] QRIS image upload by admin
- [x] Notification when donasi verified/rejected — email (`DonasiStatusMail`) + WA (`WhatsappService`)
- [x] Export laporan to Excel — `ExcelExportService` (PhpSpreadsheet), endpoints at `/api/laporan/export/{kas,iuran,pengeluaran}`
- [x] Pagination for kas ledger — smart ellipsis, per-page selector (25/50/100)
- [x] Pagination for donatur list — "load more" for recent list; leaderboard expands inline with "Tampilkan semua" button; stats update on each load
- [x] Error pages (403, 404, 419, 500) — styled Blade views in `resources/views/errors/`
- [x] `public/uploads/` web-accessible — Upload.php stores directly to `public/uploads/{subfolder}/`, returns `/uploads/...` URLs

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
- Admin views: `index` (stats), `warga` (list), `detail` (per-KK detail with anggota + kendaraan), `laporan`

### [DONE] Phase 12 — Iuran Bulanan (DONE — admin complete, warga whitelist-gated)
- Models: `IuranPeriode`, `IuranTagihan`, `IuranBayar`
- `IuranController` (admin): periode CRUD, tagihan generate/index/dispensasi, bayar store/verify/index, matrix
- `IuranWargaController` (warga): tagihan list, submit bayar with bukti, upload bukti
- Admin view: `/admin/keuangan/iuran` (3 tabs: Periode, Tagihan, Pembayaran Masuk)
- Admin view: `/admin/keuangan/iuran/matrix` (compliance matrix with Excel export + print)
- Warga view: `/dashboard/iuran` (tagihan list, payment modal, bukti upload)
- **Warga flow is whitelist-gated**: set `IURAN_WHITELIST=email1@gmail.com,email2@gmail.com` in `.env` to enable per user
- On verify: auto-creates `kas` masuk entry + notifies warga via email + WA

### [DONE] Phase 11 — Laporan Kependudukan (DONE)
- `KependudukanController::laporan()` — per-blok summary (jumlah KK, jiwa, kendaraan, tetap/kontrak/kos) via JOIN aggregates
- `KependudukanController::laporanDetail()` — full KK list eager-loading anggota + kendaraan, filterable by `?blok=`
- `KependudukanController::exportKependudukan()` — 2-sheet Excel: Daftar KK + Anggota Keluarga
- `ExcelExportService::exportKependudukan(?string $blok)` — PhpSpreadsheet export
- API: `GET /api/kependudukan/laporan`, `GET /api/kependudukan/laporan/detail?blok=`, `GET /api/kependudukan/laporan/export?blok=`
- Admin view: `/admin/kependudukan/laporan` — 5 summary cards, 2 tabs (Ringkasan per Blok table + Daftar KK Lengkap grouped by blok), blok filter, print + Excel export

### [DONE] Phase 13 — Kelembagaan & Informasi RT (DONE)
- Tables: `tata_tertib`, `program_kerja`, `agenda`, `galeri`, `galeri_foto`, `berita`, `saran_keluhan`, `bantuan_sosial`, `usulan_pembangunan`, `inventaris`
- Controllers: `AgendaController`, `GaleriController`, `BeritaController`, `TataTertibController`, `ProgramKerjaController`, `SaranController`, `BantuanSosialController`, `UsulanPembangunanController`, `InventarisController`
- Public pages: `/agenda`, `/galeri`, `/informasi` (tabs: berita, pengumuman, tata-tertib, program-kerja, saran, usulan)
- Admin views under `/admin/kelembagaan/`: `agenda`, `galeri`, `berita`, `tata-tertib`, `program-kerja`, `saran`, `usulan`, `bantuan-sosial`, `inventaris`
- `UsulanPembangunan`: warga submit via `/informasi` tab, admin responds via slide-in panel (status: baru→dikaji→disetujui/ditolak/selesai)
- `BantuanSosial`: jenis (sembako/uang_tunai/kesehatan/pendidikan/lainnya), nominal only shown for uang_tunai
- `Inventaris`: asset registry with jumlah, kondisi, tanggal_beli, nilai

### [DONE] Phase 14 — Surat & Dokumen (DONE)
- Table: `surat_permohonan` with `data_tambahan` JSON for jenis-specific extra fields
- `SuratPermohonan::JENIS` const — 12 document types (domisili, ktp_baru, kk_baru, usaha, izin_keramaian, kematian, kelahiran, pindah_masuk, pindah_keluar, tidak_mampu, nikah, lainnya)
- `SuratController` (admin): index/show/updateStatus/cetakSurat; (warga): store/mine/jenis
- Admin view `/admin/surat`: filter pills, list with color-coded left borders, slide-in detail panel with 2×2 solid status action buttons (menunggu/diproses/selesai/ditolak), tanggal surat autofill, letterhead print via `window.print()`
- Warga view `/dashboard/surat`: dynamic extra fields rendered by `EXTRA_FIELDS` const based on jenis selection
- Status buttons: inactive=flat grey, active=solid filled color with white text

### [TODO] Phase 15 — Keamanan Lingkungan (TODO)
New tables: `jadwal_ronda`, `pos_keamanan`, `log_tamu_keamanan`

### [DONE] Phase 16 — Notifikasi & Integrasi (partially done)
**Email (Laravel Mail — SMTP):**
- `DonasiStatusMail` → `emails/donasi-status.blade.php` — sent on donasi verify/reject
- `IuranStatusMail` → `emails/iuran-status.blade.php` — sent on iuran bayar verify/reject
- `PengumumanMail` → `emails/pengumuman.blade.php` — optional blast when creating pengumuman
- `AgendaReminderMail` → `emails/agenda-reminder.blade.php` — sent by AgendaController for upcoming events
- Configure: `MAIL_MAILER=smtp`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`

**WhatsApp (Fonnte — https://fonnte.com):**
- `WhatsappService` — `send(string $noWa, string $message)` for single, `blast(array $numbers, string $message)` for bulk (chunked 50/req)
- Number normalization: `08xxx` → `628xxx` automatically
- Disabled silently when `FONNTE_TOKEN` is empty
- Triggered on: donasi verify/reject, iuran verify/reject, pengumuman store (optional toggle)
- Admin pengumuman view: "Kirim juga via" Email + WhatsApp toggle buttons; response shows count ("42 email terkirim, 38 WA terkirim")
- Configure: `FONNTE_TOKEN=your_token` from fonnte.com

**Pending:**
- [ ] QRIS dinamis via Midtrans/Xendit
