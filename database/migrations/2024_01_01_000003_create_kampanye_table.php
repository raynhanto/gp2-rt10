<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kampanye', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 200);
            $table->text('deskripsi');
            $table->unsignedBigInteger('target')->comment('In rupiah');
            $table->unsignedBigInteger('terkumpul')->default(0)->comment('Derived from verified donasi');
            $table->enum('status', ['aktif', 'urgent', 'selesai', 'arsip'])->default('aktif');
            $table->string('foto_url', 255)->nullable();
            $table->date('deadline')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kampanye');
    }
};
