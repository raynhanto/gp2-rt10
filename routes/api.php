<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;

// ── Auth (JSON) ───────────────────────────────────────────────
Route::get('/auth/me', [Api\AuthController::class, 'me'])->middleware('login');

// ── User ──────────────────────────────────────────────────────
Route::middleware('login')->group(function () {
    Route::put('/user/profile', [Api\UserController::class, 'updateProfile']);
    Route::get('/user/donasi',  [Api\UserController::class, 'myDonasi']);
});

// ── Kampanye (public GET) ─────────────────────────────────────
Route::get('/kampanye',      [Api\KampanyeController::class, 'index']);
Route::get('/kampanye/{id}', [Api\KampanyeController::class, 'show'])->where('id', '[0-9]+');

// ── Kampanye (manage: bendahara, admin, super_admin) ──────────
Route::middleware('admin:bendahara,admin,super_admin')->group(function () {
    Route::post('/kampanye',           [Api\KampanyeController::class, 'store']);
    Route::put('/kampanye/{id}',       [Api\KampanyeController::class, 'update'])->where('id', '[0-9]+');
    Route::post('/kampanye/{id}/foto', [Api\KampanyeController::class, 'uploadFoto'])->where('id', '[0-9]+');
});

// ── Donasi ────────────────────────────────────────────────────
Route::get('/donasi', [Api\DonasiController::class, 'index']);
Route::middleware('login')->group(function () {
    Route::post('/donasi',                [Api\DonasiController::class, 'store']);
    Route::patch('/donasi/{id}/metode',   [Api\DonasiController::class, 'updateMetode'])->where('id', '[0-9]+');
    Route::post('/donasi/{id}/bukti',     [Api\DonasiController::class, 'uploadBukti'])->where('id', '[0-9]+');
});
// Verifikasi & admin input donasi: bendahara, admin, super_admin
Route::middleware('admin:bendahara,admin,super_admin')->group(function () {
    Route::post('/donasi/admin',       [Api\DonasiController::class, 'adminStore']);
    Route::post('/donasi/{id}/verify', [Api\DonasiController::class, 'verify'])->where('id', '[0-9]+');
});

// ── Kas (bendahara, admin, super_admin) ───────────────────────
Route::get('/kas/summary', [Api\KasController::class, 'summary']);
Route::middleware('admin:bendahara,admin,super_admin')->group(function () {
    Route::get('/kas',         [Api\KasController::class, 'index']);
    Route::post('/kas',        [Api\KasController::class, 'store']);
    Route::put('/kas/{id}',    [Api\KasController::class, 'update'])->where('id', '[0-9]+');
    Route::delete('/kas/{id}', [Api\KasController::class, 'destroy'])->where('id', '[0-9]+');
});

// ── Anggaran (bendahara, admin, super_admin) ──────────────────
Route::middleware('admin:bendahara,admin,super_admin')->group(function () {
    Route::get('/anggaran',         [Api\AnggaranController::class, 'index']);
    Route::post('/anggaran',        [Api\AnggaranController::class, 'store']);
    Route::put('/anggaran/{id}',    [Api\AnggaranController::class, 'update'])->where('id', '[0-9]+');
    Route::delete('/anggaran/{id}', [Api\AnggaranController::class, 'destroy'])->where('id', '[0-9]+');
});

// ── Pengeluaran (bendahara, admin, super_admin) ───────────────
Route::get('/pengeluaran/public', [Api\PengeluaranController::class, 'publicList']);
Route::middleware('admin:bendahara,admin,super_admin')->group(function () {
    Route::get('/pengeluaran',         [Api\PengeluaranController::class, 'index']);
    Route::post('/pengeluaran',        [Api\PengeluaranController::class, 'store']);
    Route::put('/pengeluaran/{id}',    [Api\PengeluaranController::class, 'update'])->where('id', '[0-9]+');
    Route::delete('/pengeluaran/{id}', [Api\PengeluaranController::class, 'destroy'])->where('id', '[0-9]+');
});

// ── Kategori Keuangan (bendahara, admin, super_admin) ─────────
Route::middleware('admin:bendahara,admin,super_admin')->group(function () {
    Route::get('/kategori',         [Api\KategoriController::class, 'index']);
    Route::post('/kategori',        [Api\KategoriController::class, 'store']);
    Route::put('/kategori/{id}',    [Api\KategoriController::class, 'update'])->where('id', '[0-9]+');
    Route::delete('/kategori/{id}', [Api\KategoriController::class, 'destroy'])->where('id', '[0-9]+');
});

// ── Lampiran (bendahara, admin, super_admin) ──────────────────
Route::middleware('admin:bendahara,admin,super_admin')->group(function () {
    Route::get('/lampiran',         [Api\LampiranController::class, 'index']);
    Route::post('/lampiran',        [Api\LampiranController::class, 'store']);
    Route::delete('/lampiran/{id}', [Api\LampiranController::class, 'destroy'])->where('id', '[0-9]+');
});

// ── Iuran Warga (login, whitelist-gated inside controller) ───
Route::middleware('login')->group(function () {
    Route::get('/iuran/warga/tagihan',          [Api\IuranWargaController::class, 'tagihanIndex']);
    Route::post('/iuran/warga/bayar',           [Api\IuranWargaController::class, 'bayar']);
    Route::post('/iuran/warga/bayar/{id}/bukti', [Api\IuranWargaController::class, 'uploadBukti'])->where('id', '[0-9]+');
});

// ── Iuran (bendahara, admin, super_admin) ─────────────────────
Route::middleware('admin:bendahara,admin,super_admin')->group(function () {
    Route::get('/iuran/periode',                       [Api\IuranController::class, 'periodeIndex']);
    Route::post('/iuran/periode',                      [Api\IuranController::class, 'periodeStore']);
    Route::delete('/iuran/periode/{id}',               [Api\IuranController::class, 'periodeDelete'])->where('id', '[0-9]+');

    Route::get('/iuran/tagihan',                       [Api\IuranController::class, 'tagihanIndex']);
    Route::post('/iuran/tagihan/{id}/generate',        [Api\IuranController::class, 'tagihanGenerate'])->where('id', '[0-9]+');
    Route::put('/iuran/tagihan/{id}/dispensasi',       [Api\IuranController::class, 'tagihanDispensasi'])->where('id', '[0-9]+');

    Route::get('/iuran/bayar',                         [Api\IuranController::class, 'bayarIndex']);
    Route::post('/iuran/bayar',                        [Api\IuranController::class, 'bayarStore']);
    Route::post('/iuran/bayar/{id}/verify',            [Api\IuranController::class, 'bayarVerify'])->where('id', '[0-9]+');

    Route::get('/iuran/matrix',                        [Api\IuranController::class, 'matrix']);
});

// ── Statistik (bendahara, admin, super_admin) ─────────────────
Route::middleware('admin:bendahara,admin,super_admin')->group(function () {
    Route::get('/statistik/dashboard',        [Api\StatistikController::class, 'dashboard']);
    Route::get('/statistik/bulanan',          [Api\StatistikController::class, 'bulanan']);
    Route::get('/statistik/kategori',         [Api\StatistikController::class, 'kategori']);
    Route::get('/statistik/iuran-compliance', [Api\StatistikController::class, 'iuranCompliance']);
    Route::get('/statistik/saldo-trend',      [Api\StatistikController::class, 'saldoTrend']);
});

// ── Laporan & Export (bendahara, admin, super_admin) ──────────
Route::middleware('admin:bendahara,admin,super_admin')->group(function () {
    Route::get('/laporan/arus-kas',           [Api\LaporanKeuanganController::class, 'arusKas']);
    Route::get('/laporan/pengeluaran',        [Api\LaporanKeuanganController::class, 'pengeluaranReport']);
    Route::get('/laporan/iuran',              [Api\LaporanKeuanganController::class, 'iuranReport']);
    Route::get('/laporan/neraca',             [Api\LaporanKeuanganController::class, 'neraca']);
    Route::get('/laporan/export/kas',         [Api\LaporanKeuanganController::class, 'exportKas']);
    Route::get('/laporan/export/iuran',       [Api\LaporanKeuanganController::class, 'exportIuran']);
    Route::get('/laporan/export/pengeluaran', [Api\LaporanKeuanganController::class, 'exportPengeluaran']);
});

// ── Google Sheets (bendahara, admin, super_admin) ─────────────
Route::middleware('admin:bendahara,admin,super_admin')->group(function () {
    Route::get('/gsheet/status',       [Api\GsheetController::class, 'status']);
    Route::post('/gsheet/config',      [Api\GsheetController::class, 'saveConfig']);
    Route::post('/gsheet/credentials', [Api\GsheetController::class, 'uploadCredentials']);
    Route::post('/gsheet/sync/{tab}',  [Api\GsheetController::class, 'syncTab']);
    Route::post('/gsheet/sync-all',    [Api\GsheetController::class, 'syncAll']);
    Route::get('/gsheet/logs',         [Api\GsheetController::class, 'logs']);
});

// ── Transaksi Instan (bendahara, admin, super_admin) ──────────
Route::middleware('admin:bendahara,admin,super_admin')->group(function () {
    Route::get('/transaksi-instan',         [Api\TransaksiInstanController::class, 'index']);
    Route::post('/transaksi-instan',        [Api\TransaksiInstanController::class, 'store']);
    Route::delete('/transaksi-instan/{id}', [Api\TransaksiInstanController::class, 'destroy'])->where('id', '[0-9]+');
});

// ── Pengumuman ────────────────────────────────────────────────
Route::get('/pengumuman', [Api\PengumumanController::class, 'index']);
// Post pengumuman: sekretaris, admin, super_admin
Route::post('/pengumuman', [Api\PengumumanController::class, 'store'])
    ->middleware('admin:sekretaris,admin,super_admin');

// ── Kependudukan ──────────────────────────────────────────────
Route::middleware('admin:sekretaris,admin,super_admin')->group(function () {
    Route::get('/kependudukan/stats',                    [Api\KependudukanController::class, 'stats']);
    Route::get('/kependudukan/units',                    [Api\KependudukanController::class, 'unitList']);
    Route::get('/kependudukan/kepala',                   [Api\KependudukanController::class, 'kepalaIndex']);
    Route::post('/kependudukan/kepala',                  [Api\KependudukanController::class, 'kepalaStore']);
    Route::get('/kependudukan/kepala/{id}',              [Api\KependudukanController::class, 'kepalaShow'])->where('id', '[0-9]+');
    Route::put('/kependudukan/kepala/{id}',              [Api\KependudukanController::class, 'kepalaUpdate'])->where('id', '[0-9]+');
    Route::delete('/kependudukan/kepala/{id}',           [Api\KependudukanController::class, 'kepalaDestroy'])->where('id', '[0-9]+');
    Route::post('/kependudukan/kepala/{kkId}/anggota',   [Api\KependudukanController::class, 'anggotaStore'])->where('kkId', '[0-9]+');
    Route::put('/kependudukan/anggota/{id}',             [Api\KependudukanController::class, 'anggotaUpdate'])->where('id', '[0-9]+');
    Route::delete('/kependudukan/anggota/{id}',          [Api\KependudukanController::class, 'anggotaDestroy'])->where('id', '[0-9]+');
    Route::get('/kependudukan/kendaraan',                [Api\KependudukanController::class, 'kendaraanIndex']);
    Route::post('/kependudukan/kepala/{kkId}/kendaraan', [Api\KependudukanController::class, 'kendaraanStore'])->where('kkId', '[0-9]+');
    Route::put('/kependudukan/kendaraan/{id}',           [Api\KependudukanController::class, 'kendaraanUpdate'])->where('id', '[0-9]+');
    Route::delete('/kependudukan/kendaraan/{id}',        [Api\KependudukanController::class, 'kendaraanDestroy'])->where('id', '[0-9]+');
});

// ── Users (sekretaris, admin, super_admin) ────────────────────
Route::middleware('admin:sekretaris,admin,super_admin')->group(function () {
    Route::get('/users',            [Api\UsersController::class, 'index']);
    Route::get('/users/{id}',       [Api\UsersController::class, 'show'])->where('id', '[0-9]+');
    Route::put('/admin/users/{id}', [Api\UsersController::class, 'update'])->where('id', '[0-9]+');
});

// ── Admin Activity Log & Role Management (admin, super_admin) ─
Route::middleware('admin:admin,super_admin')->group(function () {
    Route::get('/admin/aktivitas',         [Api\AdminActivityController::class, 'index']);
    Route::put('/admin/users/{id}/role',   [Api\AdminActivityController::class, 'updateRole'])->where('id', '[0-9]+');
});
