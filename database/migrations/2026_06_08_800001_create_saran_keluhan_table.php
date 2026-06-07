<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saran_keluhan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_pengirim')->nullable();
            $table->string('no_wa', 20)->nullable();
            $table->enum('kategori', ['saran', 'keluhan', 'pertanyaan'])->default('saran');
            $table->string('judul');
            $table->text('isi');
            $table->boolean('is_anonym')->default(false);
            $table->enum('status', ['baru', 'dibaca', 'diproses', 'selesai'])->default('baru');
            $table->text('tanggapan')->nullable();
            $table->timestamp('tanggapan_at')->nullable();
            $table->foreignId('tanggapan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saran_keluhan');
    }
};
