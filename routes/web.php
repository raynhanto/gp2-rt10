<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;

// ── Auth ─────────────────────────────────────────────────────
Route::get('/auth/google',   [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/callback', [GoogleController::class, 'callback'])->name('auth.callback');
Route::post('/auth/logout',  [GoogleController::class, 'logout'])->name('auth.logout');

// ── Public pages ─────────────────────────────────────────────
Route::view('/',         'home')->name('home');
Route::view('/kampanye', 'kampanye')->name('kampanye');
Route::view('/donasi',   'donasi')->middleware('login')->name('donasi');
Route::view('/donatur',  'donatur')->name('donatur');
Route::view('/login',    'login')->name('login');

Route::get('/kampanye/{id}', fn(int $id) => view('kampanye-detail', ['kampanyeId' => $id]))
    ->where('id', '[0-9]+')
    ->name('kampanye.detail');

// ── Onboarding (login required, incomplete profile allowed) ──
Route::get('/onboarding', fn() => view('onboarding'))->middleware('login')->name('onboarding');

// ── Warga dashboard ───────────────────────────────────────────
Route::view('/agenda',    'agenda')->name('agenda');
Route::view('/galeri',    'galeri')->name('galeri');
Route::view('/informasi', 'informasi')->name('informasi');
Route::view('/laporan',   'laporan')->middleware('login')->name('laporan');

Route::middleware('login')->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::view('/',           'dashboard.index')->name('index');
    Route::view('/riwayat',    'dashboard.riwayat')->name('riwayat');
    Route::view('/bayar',      'dashboard.bayar')->name('bayar');
    Route::view('/konfirmasi', 'dashboard.konfirmasi')->name('konfirmasi');
    Route::view('/profil',     'dashboard.profil')->name('profil');
    Route::view('/iuran',      'dashboard.iuran')->name('iuran');
    Route::view('/surat',      'dashboard.surat')->name('surat');
});

// ── Admin pages ───────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {

    // All admin roles: dashboard
    Route::middleware('admin')->group(function () {
        Route::view('/', 'admin.dashboard')->name('dashboard');
    });

    // Financial roles: kampanye, verifikasi, keuangan
    Route::middleware('admin:bendahara,admin,super_admin')->group(function () {
        Route::view('/verifikasi',  'admin.verifikasi')->name('verifikasi');
        Route::view('/kampanye',    'admin.kampanye')->name('kampanye');
        Route::view('/kas',         'admin.kas')->name('kas');
        Route::view('/anggaran',    'admin.anggaran')->name('anggaran');
        Route::view('/pengeluaran', 'admin.pengeluaran')->name('pengeluaran');
        Route::view('/laporan',     'admin.laporan')->name('laporan');

        Route::prefix('keuangan')->name('keuangan.')->group(function () {
            Route::view('/',                   'admin.keuangan.dashboard')->name('dashboard');
            Route::view('/kas',                'admin.keuangan.kas')->name('kas');
            Route::view('/transaksi-instan',   'admin.keuangan.transaksi-instan')->name('transaksi-instan');
            Route::view('/pengeluaran',        'admin.keuangan.pengeluaran')->name('pengeluaran');
            Route::view('/anggaran',           'admin.keuangan.anggaran')->name('anggaran');
            Route::view('/kategori',           'admin.keuangan.kategori')->name('kategori');
            Route::view('/iuran',              'admin.keuangan.iuran')->name('iuran');
            Route::view('/iuran/matrix',       'admin.keuangan.matrix')->name('matrix');
            Route::view('/laporan',            'admin.keuangan.laporan')->name('laporan');
            Route::view('/gsheet',             'admin.keuangan.gsheet')->name('gsheet');
        });
    });

    // Community roles: warga, pengumuman, kependudukan, surat
    Route::middleware('admin:sekretaris,admin,super_admin')->group(function () {
        Route::view('/warga',      'admin.warga')->name('warga');
        Route::view('/pengumuman', 'admin.pengumuman')->name('pengumuman');
        Route::view('/surat',      'admin.surat')->name('surat');

        Route::prefix('kelembagaan')->name('kelembagaan.')->group(function () {
            Route::view('/agenda',        'admin.kelembagaan.agenda')->name('agenda');
            Route::view('/galeri',        'admin.kelembagaan.galeri')->name('galeri');
            Route::view('/berita',        'admin.kelembagaan.berita')->name('berita');
            Route::view('/tata-tertib',   'admin.kelembagaan.tata-tertib')->name('tata-tertib');
            Route::view('/program-kerja', 'admin.kelembagaan.program-kerja')->name('program-kerja');
            Route::view('/saran',         'admin.kelembagaan.saran')->name('saran');
        });

        Route::prefix('kependudukan')->name('kependudukan.')->group(function () {
            Route::view('/',           'admin.kependudukan.index')->name('index');
            Route::view('/warga',      'admin.kependudukan.warga')->name('warga');
            Route::get('/warga/{id}',  fn(int $id) => view('admin.kependudukan.detail'))
                ->where('id', '[0-9]+')->name('detail');
            Route::view('/kendaraan',  'admin.kependudukan.kendaraan')->name('kendaraan');
        });
    });

    // Admin + super_admin only: activity log + settings
    Route::middleware('admin:admin,super_admin')->group(function () {
        Route::view('/aktivitas',   'admin.aktivitas')->name('aktivitas');
        Route::view('/pengaturan',  'admin.pengaturan')->name('pengaturan');
    });

    // Super admin only: seeder management
    Route::middleware('admin:super_admin')->group(function () {
        Route::view('/seeder', 'admin.seeder')->name('seeder');
    });
});
