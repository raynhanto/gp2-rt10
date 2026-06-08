# GP2 RT10 — Platform Digital Warga

Platform manajemen RT berbasis web untuk **RT 10, Golden Park 2, Cisauk, Tangerang Selatan**.

**Live:** [gp2rt10.com](https://gp2rt10.com)

---

## Fitur

### Publik
| Halaman | Deskripsi |
|---|---|
| `/` | Beranda & ringkasan platform |
| `/kampanye` | Daftar kampanye donasi aktif |
| `/donasi` | Form donasi (login required) |
| `/donatur` | Papan donatur & leaderboard |
| `/agenda` | Kalender kegiatan RT |
| `/galeri` | Galeri foto kegiatan |
| `/informasi` | Berita, tata tertib, program kerja |

### Dashboard Warga
- Riwayat donasi & konfirmasi pembayaran
- Ajukan permohonan surat keterangan RT
- Laporan keuangan RT
- Profil & data unit rumah

### Panel Admin
| Modul | Akses |
|---|---|
| Verifikasi Donasi + Input Manual | bendahara, admin, super_admin |
| Kas, Anggaran, Pengeluaran, Iuran | bendahara, admin, super_admin |
| Transaksi Instan | bendahara, admin, super_admin |
| Laporan Keuangan + Export Excel | bendahara, admin, super_admin |
| Data Warga & KK | sekretaris, admin, super_admin |
| Data Kendaraan | sekretaris, admin, super_admin |
| Peta Warga | sekretaris, admin, super_admin |
| Laporan Kependudukan | sekretaris, admin, super_admin |
| Surat & Dokumen | sekretaris, admin, super_admin |
| Agenda, Galeri, Berita, Tata Tertib, Program Kerja | sekretaris, admin, super_admin |
| Saran & Keluhan, Usulan Pembangunan | sekretaris, admin, super_admin |
| Bantuan Sosial, Inventaris RT | sekretaris, admin, super_admin |
| Pengumuman | sekretaris, admin, super_admin |
| Pengaturan Aplikasi | admin, super_admin |
| Log Aktivitas | admin, super_admin |
| Manajemen Seeder | super_admin |

---

## Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | Laravel 13, PHP 8.4 |
| Frontend | Blade + Vanilla JS (no npm/Vite) |
| Database | MySQL (shared hosting cPanel) |
| Auth | Google OAuth 2.0 via Laravel Socialite |
| Notifikasi | Fonnte (WhatsApp), Laravel Mail |
| Hosting | cPanel — gp2rt10.com |

---

## Role System

| Role | Akses |
|---|---|
| `warga` | Dashboard, donasi, surat permohonan |
| `sekretaris` | + Kependudukan, surat, kelembagaan |
| `bendahara` | + Keuangan, verifikasi donasi |
| `admin` | Semua fitur + pengaturan, log aktivitas |
| `super_admin` | Semua fitur + manajemen seeder |

Set role via SQL:
```sql
UPDATE users SET role = 'admin' WHERE email = 'your@email.com';
```

---

## Setup Lokal

```bash
git clone git@github.com:raynhanto/gp2-rt10.git
cd gp2-rt10

cp .env.example .env
# Isi DB_*, GOOGLE_CLIENT_*, FONNTE_TOKEN di .env

composer install
php artisan key:generate
php artisan migrate
php artisan db:seed

php artisan serve
```

Buat symlink storage:
```bash
php artisan storage:link
# atau: ln -s ../storage/uploads public/uploads
```

---

## Deployment (Production)

**Stack:** FTP ke cPanel hosting. FTP root = document root (`public_html/`).

1. Push ke GitHub: `git push origin main`
2. Upload file yang berubah via `lftp` (path relatif sama dengan project)
3. Jika ada migration baru — upload script runner, jalankan, hapus:
   ```bash
   # Upload temp migration runner
   lftp -c "... put /tmp/migrate_now.php -o migrate_now.php"
   curl https://gp2rt10.com/migrate_now.php
   lftp -c "... rm migrate_now.php"
   ```
4. `public/.htaccess` di-upload ke FTP root (bukan `public/`)

Kredensial FTP dan DB ada di `secret/deploy.json` (gitignored).

---

## Struktur Penting

```
app/
  Http/Controllers/Api/   ← Semua API controller
  Models/                 ← Eloquent models
  Services/               ← ExcelExportService, FonnteService, SeederManager
  Helpers/Upload.php      ← UUID rename, MIME validate, upload ke storage/

resources/views/
  layouts/app.blade.php   ← Layout publik (nav, footer, bottom nav mobile)
  layouts/admin.blade.php ← Layout admin (sidebar)
  admin/                  ← Semua halaman admin
  dashboard/              ← Dashboard warga
  errors/                 ← Halaman error 403/404/419/500

routes/
  web.php                 ← Page routes
  api.php                 ← API routes (/api/*)
```

---

## Konvensi Kode

- `declare(strict_types=1)` di semua file PHP
- SQL: Eloquent only — no raw string interpolation
- API response: selalu via `response()->json(['success' => bool, ...])`
- Upload: UUID filename via `Upload::save()`, simpan di `storage/uploads/`
- Uang: integer Rupiah, tidak pernah float
- Timezone: `Asia/Jakarta`
- Pesan error user-facing: Bahasa Indonesia
- Icon: Font Awesome 6 — tidak menggunakan emoji
