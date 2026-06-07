<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kepala_keluarga', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_rumah_id')->nullable()->constrained('unit_rumah')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('no_kk', 16)->nullable();
            $table->string('nama', 100);
            $table->string('nik', 16)->nullable()->unique();
            $table->string('tempat_lahir', 50)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->enum('agama', ['islam', 'kristen', 'katolik', 'hindu', 'budha', 'konghucu'])->nullable();
            $table->enum('pendidikan', ['sd', 'smp', 'sma', 'd3', 's1', 's2', 's3', 'lainnya'])->nullable();
            $table->string('pekerjaan', 50)->nullable();
            $table->string('no_wa', 20)->nullable();
            $table->enum('status_perkawinan', ['belum_kawin', 'kawin', 'cerai_hidup', 'cerai_mati'])->nullable();
            $table->enum('status_tinggal', ['tetap', 'kontrak', 'kos', 'lainnya'])->default('tetap');
            $table->string('foto_kk_url', 255)->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index('unit_rumah_id');
            $table->index('nama');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kepala_keluarga');
    }
};
