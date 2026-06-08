<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventaris', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kode')->nullable(); // e.g. INV-001
            $table->string('kategori'); // kursi, meja, elektronik, peralatan, kendaraan, lainnya
            $table->unsignedSmallInteger('jumlah')->default(1);
            $table->enum('kondisi', ['baik', 'rusak_ringan', 'rusak_berat'])->default('baik');
            $table->string('lokasi')->nullable();
            $table->date('tanggal_beli')->nullable();
            $table->unsignedBigInteger('harga_beli')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('foto_url')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventaris');
    }
};
