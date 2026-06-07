<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Run after first Google login and setting admin:
        //   UPDATE users SET role='admin' WHERE email='your-email@gmail.com';
        $this->call([
            KampanyeSeeder::class,
            KategoriKeuanganSeeder::class,
            DonasiSeeder::class,
        ]);
    }
}
