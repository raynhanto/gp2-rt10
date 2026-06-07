<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_instan', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->unsignedBigInteger('nominal')->comment('In rupiah');
            $table->string('pembayar_nama', 100);
            $table->foreignId('pembayar_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tujuan', 255)->comment('Untuk apa uang ini dipakai');
            $table->text('keterangan')->nullable();
            $table->foreignId('kas_masuk_id')->nullable()->constrained('kas')->nullOnDelete();
            $table->foreignId('kas_keluar_id')->nullable()->constrained('kas')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_instan');
    }
};
