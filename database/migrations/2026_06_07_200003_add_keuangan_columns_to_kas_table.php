<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kas', function (Blueprint $table) {
            $table->date('tanggal')->nullable()->after('id');
            $table->foreignId('kategori_id')
                ->nullable()
                ->after('keterangan')
                ->constrained('kategori_keuangan')
                ->nullOnDelete();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('kas', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropConstrainedForeignId('kategori_id');
            $table->dropColumn('tanggal');
        });
    }
};
