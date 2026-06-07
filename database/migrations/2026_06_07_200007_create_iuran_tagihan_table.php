<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iuran_tagihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_rumah_id')->constrained('unit_rumah');
            $table->foreignId('iuran_periode_id')->constrained('iuran_periode');
            $table->enum('status', ['belum', 'pending', 'lunas', 'dispensasi'])->default('belum');
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['unit_rumah_id', 'iuran_periode_id']);
            $table->index('status');
            $table->index('iuran_periode_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iuran_tagihan');
    }
};
