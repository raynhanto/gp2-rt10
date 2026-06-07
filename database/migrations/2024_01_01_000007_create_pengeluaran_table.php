<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kampanye_id')->constrained('kampanye');
            $table->foreignId('anggaran_id')->nullable()->constrained('anggaran');
            $table->string('keterangan', 255);
            $table->unsignedBigInteger('nominal')->comment('In rupiah');
            $table->date('tanggal');
            $table->string('bukti_url', 255)->nullable()->comment('Photo of receipt');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index('kampanye_id');
            $table->index('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengeluaran');
    }
};
