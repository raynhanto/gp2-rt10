<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_keuangan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->enum('jenis', ['masuk', 'keluar', 'keduanya'])->default('keduanya');
            $table->char('warna', 7)->default('#1A3D2B');
            $table->string('ikon', 50)->default('fa-tag');
            $table->unsignedTinyInteger('urutan')->default(0);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_keuangan');
    }
};
