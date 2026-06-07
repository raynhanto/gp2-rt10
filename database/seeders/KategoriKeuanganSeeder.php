<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\KategoriKeuangan;
use App\Models\User;
use Illuminate\Database\Seeder;

class KategoriKeuanganSeeder extends Seeder
{
    public function run(): void
    {
        // Use first admin user as created_by, or fall back to first user
        $adminId = User::whereIn('role', ['admin', 'super_admin'])->value('id')
            ?? User::value('id')
            ?? 1;

        $kategori = [
            // ── Pemasukan ────────────────────────────────────────
            ['nama' => 'Iuran Bulanan',        'jenis' => 'masuk',    'warna' => '#1A3D2B', 'ikon' => 'fa-calendar-check', 'urutan' => 1],
            ['nama' => 'Donasi Kampanye',       'jenis' => 'masuk',    'warna' => '#2D5AA8', 'ikon' => 'fa-heart',           'urutan' => 2],
            ['nama' => 'Donasi Warga',          'jenis' => 'masuk',    'warna' => '#3D7A56', 'ikon' => 'fa-hand-holding-heart', 'urutan' => 3],
            ['nama' => 'Dana Sosial Masuk',     'jenis' => 'masuk',    'warna' => '#5C3B8A', 'ikon' => 'fa-people-group',    'urutan' => 4],
            ['nama' => 'Pemasukan Lain',        'jenis' => 'masuk',    'warna' => '#6B6050', 'ikon' => 'fa-circle-plus',     'urutan' => 5],

            // ── Pengeluaran ───────────────────────────────────────
            ['nama' => 'Keamanan & Ronda',      'jenis' => 'keluar',   'warna' => '#B5401A', 'ikon' => 'fa-shield-halved',   'urutan' => 10],
            ['nama' => 'Kebersihan',            'jenis' => 'keluar',   'warna' => '#2D5AA8', 'ikon' => 'fa-broom',           'urutan' => 11],
            ['nama' => 'Perbaikan Fasilitas',   'jenis' => 'keluar',   'warna' => '#9A7818', 'ikon' => 'fa-wrench',          'urutan' => 12],
            ['nama' => 'Acara & Kegiatan',      'jenis' => 'keluar',   'warna' => '#5C3B8A', 'ikon' => 'fa-calendar-star',   'urutan' => 13],
            ['nama' => 'Operasional RT',        'jenis' => 'keluar',   'warna' => '#3D3828', 'ikon' => 'fa-building',        'urutan' => 14],
            ['nama' => 'Listrik & Air',         'jenis' => 'keluar',   'warna' => '#1A3D2B', 'ikon' => 'fa-bolt',            'urutan' => 15],
            ['nama' => 'Administrasi',          'jenis' => 'keluar',   'warna' => '#6B6050', 'ikon' => 'fa-file-pen',        'urutan' => 16],
            ['nama' => 'Dana Sosial Keluar',    'jenis' => 'keluar',   'warna' => '#964B00', 'ikon' => 'fa-hand-holding-dollar', 'urutan' => 17],
            ['nama' => 'Pengeluaran Lain',      'jenis' => 'keluar',   'warna' => '#9D9080', 'ikon' => 'fa-circle-minus',    'urutan' => 18],

            // ── Keduanya ──────────────────────────────────────────
            ['nama' => 'Lain-lain',             'jenis' => 'keduanya', 'warna' => '#9D9080', 'ikon' => 'fa-tag',             'urutan' => 99],
        ];

        foreach ($kategori as $row) {
            KategoriKeuangan::firstOrCreate(
                ['nama' => $row['nama']],
                array_merge($row, ['created_by' => $adminId])
            );
        }
    }
}
