<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kampanye_id')->constrained('kampanye');
            $table->string('pos', 150)->comment('Budget line name');
            $table->unsignedBigInteger('estimasi')->comment('Planned amount in rupiah');
            $table->unsignedBigInteger('realisasi')->default(0)->comment('Actual spent, derived from pengeluaran');
            $table->string('catatan', 255)->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index('kampanye_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggaran');
    }
};
