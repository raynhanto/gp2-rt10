<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
            $table->enum('jenis_jadwal', ['sekali', 'mingguan', 'bulanan', 'dua_bulanan'])
                  ->default('sekali')
                  ->after('is_public');
            $table->json('hari_minggu')->nullable()->after('jenis_jadwal');   // [0..6] for weekly
            $table->date('tanggal_akhir')->nullable()->after('hari_minggu');  // recurrence end

            $table->enum('target_audience', ['semua', 'staff', 'personal'])
                  ->default('semua')
                  ->after('tanggal_akhir');
            $table->json('target_user_ids')->nullable()->after('target_audience'); // user IDs for personal
        });
    }

    public function down(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
            $table->dropColumn(['jenis_jadwal', 'hari_minggu', 'tanggal_akhir', 'target_audience', 'target_user_ids']);
        });
    }
};
