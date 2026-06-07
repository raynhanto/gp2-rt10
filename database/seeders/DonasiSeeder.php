<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Kampanye;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DonasiSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->orWhere('role', 'super_admin')->firstOrFail();
        $campaigns = Kampanye::pluck('id', 'judul');

        $now = now();

        // Each entry: [donatur_nama, kampanye_judul|null, nominal, metode, is_anonym, days_ago]
        $donations = [
            ['Ahmad Taufiq',        'Balai Warga Fasum 1',      500_000,  'transfer', false, 1],
            ['Siti Rahayu',         'Balai Warga Fasum 1',      200_000,  'qris',     false, 1],
            ['Budi Santoso',        'Lighting Underpass',        300_000,  'qris',     false, 2],
            ['Dewi Lestari',        'Balai Warga Fasum 1',      150_000,  'qris',     false, 2],
            ['Rudi Hartono',        'Pos RT',                   500_000,  'transfer', false, 3],
            ['Nurul Hidayah',       'Area Simpan APAR Underpass',100_000, 'qris',     false, 3],
            ['Hendra Wijaya',       'Kolam Ikan Fasum 1',       250_000,  'transfer', false, 4],
            ['Rina Susanti',        'Urban Farming',            100_000,  'qris',     false, 4],
            ['Agus Prasetyo',       'Balai Warga Fasum 1',    1_000_000,  'transfer', false, 5],
            ['Fitri Handayani',     'Kamar Mandi Fasum 1',      200_000,  'qris',     false, 5],
            ['Teguh Wibowo',        'Lighting Underpass',        150_000,  'qris',     false, 6],
            ['Yanti Permatasari',   'Kaos Warga',               400_000,  'transfer', false, 6],
            ['Doni Setiawan',       'Pos RT',                   300_000,  'qris',     false, 7],
            ['Lina Marlina',        'Balai Warga Fasum 1',      500_000,  'transfer', false, 7],
            ['Wahyu Kurniawan',     'Kolam Ikan Fasum 1',       200_000,  'qris',     false, 8],
            ['Endah Purwanti',      'Urban Farming',            100_000,  'qris',     false, 8],
            ['Bambang Supriyadi',   'Balai Warga Fasum 1',      750_000,  'transfer', false, 9],
            ['Mei Sulistyowati',    'Pompa Air Ikan Fasum 1',   200_000,  'qris',     false, 9],
            ['Irfan Hakim',         'Lighting Underpass',        250_000,  'qris',     false, 10],
            [null,                  'Balai Warga Fasum 1',      100_000,  'qris',     true,  10],
            ['Santika Dewi',        'Kamar Mandi Fasum 1',      300_000,  'transfer', false, 11],
            ['Eko Prasetyo',        'Pos RT',                   500_000,  'transfer', false, 12],
            [null,                  'Kaos Warga',               200_000,  'qris',     true,  12],
            ['Novi Ratnasari',      'Area Simpan APAR Underpass',150_000, 'qris',     false, 13],
            ['Fajar Nugroho',       'Balai Warga Fasum 1',      250_000,  'qris',     false, 14],
        ];

        $rows = [];
        foreach ($donations as $d) {
            [$nama, $judulKampanye, $nominal, $metode, $isAnonym, $daysAgo] = $d;
            $kampanyeId = $judulKampanye ? ($campaigns[$judulKampanye] ?? null) : null;
            $ts = $now->copy()->subDays($daysAgo)->subMinutes(rand(0, 1440));

            $rows[] = [
                'user_id'      => null,
                'donatur_nama' => $nama,
                'kampanye_id'  => $kampanyeId,
                'nominal'      => $nominal,
                'metode'       => $metode,
                'is_anonym'    => $isAnonym,
                'status'       => 'verified',
                'verified_by'  => $admin->id,
                'verified_at'  => $ts,
                'created_at'   => $ts,
                'updated_at'   => $ts,
            ];
        }

        DB::table('donasi')->insert($rows);

        // Recalculate terkumpul for each affected campaign
        foreach ($campaigns as $judul => $id) {
            $total = DB::table('donasi')
                ->where('kampanye_id', $id)
                ->where('status', 'verified')
                ->sum('nominal');
            DB::table('kampanye')->where('id', $id)->update(['terkumpul' => $total]);
        }
    }
}
