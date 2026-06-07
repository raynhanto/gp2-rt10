<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iuran_periode', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('bulan');
            $table->year('tahun');
            $table->unsignedBigInteger('nominal')->comment('In rupiah');
            $table->date('jatuh_tempo');
            $table->string('keterangan', 255)->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['bulan', 'tahun']);
            $table->index('tahun');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iuran_periode');
    }
};
