<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('kampanye_id')->constrained('kampanye');
            $table->unsignedBigInteger('nominal')->comment('In rupiah');
            $table->enum('metode', ['qris', 'transfer', 'gopay', 'ovo', 'dana', 'lainnya'])->default('qris');
            $table->boolean('is_anonym')->default(false)->comment('Tampil sebagai Donatur Anonim di publik');
            $table->string('bukti_url', 255)->nullable();
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->string('catatan_admin', 255)->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('user_id');
            $table->index('kampanye_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donasi');
    }
};
