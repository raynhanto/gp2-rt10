<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bantuan_sosial', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_rumah_id')->nullable()->constrained('unit_rumah')->nullOnDelete();
            $table->string('nama_penerima');
            $table->enum('jenis', ['sembako', 'uang_tunai', 'kesehatan', 'pendidikan', 'lainnya']);
            $table->text('keterangan')->nullable();
            $table->date('tanggal');
            $table->unsignedBigInteger('nominal')->nullable(); // only for uang_tunai
            $table->enum('status', ['aktif', 'selesai'])->default('aktif');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bantuan_sosial');
    }
};
