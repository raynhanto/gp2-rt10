<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengeluaran', function (Blueprint $table) {
            $table->foreignId('kategori_id')
                ->nullable()
                ->after('keterangan')
                ->constrained('kategori_keuangan')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pengeluaran', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kategori_id');
        });
    }
};
