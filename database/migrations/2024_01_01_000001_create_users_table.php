<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('google_id', 100)->unique();
            $table->string('email', 150)->unique();
            $table->string('nama', 100)->nullable();
            $table->string('no_wa', 20)->nullable();
            $table->enum('role', ['warga', 'admin'])->default('warga');
            $table->boolean('profil_lengkap')->default(false);
            $table->string('avatar_url', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
