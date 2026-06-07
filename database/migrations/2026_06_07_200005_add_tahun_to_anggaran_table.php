<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('anggaran', function (Blueprint $table) {
            $table->year('tahun')->nullable()->after('id');
            $table->enum('sumber_dana', ['iuran', 'donasi', 'saldo', 'campuran'])
                ->default('campuran')
                ->after('tahun');
        });
    }

    public function down(): void
    {
        Schema::table('anggaran', function (Blueprint $table) {
            $table->dropColumn(['tahun', 'sumber_dana']);
        });
    }
};
