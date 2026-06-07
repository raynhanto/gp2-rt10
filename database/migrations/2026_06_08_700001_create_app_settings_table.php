<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed defaults
        $now = now();
        DB::table('app_settings')->insert([
            ['key' => 'qris_url',      'value' => null,              'created_at' => $now, 'updated_at' => $now],
            ['key' => 'bank_name',     'value' => 'BCA',             'created_at' => $now, 'updated_at' => $now],
            ['key' => 'bank_account',  'value' => '1234567890',      'created_at' => $now, 'updated_at' => $now],
            ['key' => 'bank_holder',   'value' => 'Kas RT 10 GP2',   'created_at' => $now, 'updated_at' => $now],
            ['key' => 'whatsapp',      'value' => '6281234567890',   'created_at' => $now, 'updated_at' => $now],
            ['key' => 'rt_name',       'value' => 'RT 10 Golden Park 2', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'rt_address',    'value' => 'Jl. Golden Park 2, Cisauk, Tangerang Selatan', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
