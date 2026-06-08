<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usulan_pembangunan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_pengusul')->nullable(); // used when anonymous
            $table->string('judul');
            $table->text('deskripsi');
            $table->string('lokasi')->nullable();
            $table->enum('prioritas', ['rendah', 'sedang', 'tinggi'])->default('sedang');
            $table->enum('status', ['baru', 'dikaji', 'disetujui', 'ditolak', 'selesai'])->default('baru');
            $table->boolean('is_anonym')->default(false);
            $table->text('tanggapan')->nullable();
            $table->timestamp('tanggapan_at')->nullable();
            $table->foreignId('tanggapan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usulan_pembangunan');
    }
};
