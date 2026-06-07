<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kendaraan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kepala_keluarga_id')->constrained('kepala_keluarga')->cascadeOnDelete();
            $table->enum('jenis', ['motor', 'mobil', 'sepeda', 'lainnya']);
            $table->string('merek', 50);
            $table->string('model', 50)->nullable();
            $table->string('warna', 30)->nullable();
            $table->string('plat_nomor', 15)->nullable();
            $table->smallInteger('tahun')->unsigned()->nullable();
            $table->timestamps();

            $table->index('kepala_keluarga_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kendaraan');
    }
};
