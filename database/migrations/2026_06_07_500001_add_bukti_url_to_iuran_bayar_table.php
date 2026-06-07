<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('iuran_bayar', function (Blueprint $table) {
            $table->string('bukti_url')->nullable()->after('catatan');
        });
    }

    public function down(): void
    {
        Schema::table('iuran_bayar', function (Blueprint $table) {
            $table->dropColumn('bukti_url');
        });
    }
};
