<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iuran_bayar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('iuran_tagihan_id')->constrained('iuran_tagihan');
            $table->unsignedBigInteger('nominal')->comment('In rupiah');
            $table->enum('metode', ['tunai', 'transfer', 'qris', 'gopay', 'ovo', 'dana'])->default('tunai');
            $table->string('catatan', 255)->nullable();
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index('iuran_tagihan_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iuran_bayar');
    }
};
