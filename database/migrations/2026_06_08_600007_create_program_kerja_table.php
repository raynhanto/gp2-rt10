<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_kerja', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('tahun');
            $table->string('bidang');           // e.g. "Keamanan", "Sosial", "Lingkungan"
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['rencana', 'berjalan', 'selesai', 'batal'])->default('rencana');
            $table->date('target_mulai')->nullable();
            $table->date('target_selesai')->nullable();
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_kerja');
    }
};
