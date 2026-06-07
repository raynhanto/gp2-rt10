<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kas', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis', ['masuk', 'keluar']);
            $table->unsignedBigInteger('nominal')->comment('In rupiah');
            $table->string('keterangan', 255);
            $table->foreignId('kampanye_id')->nullable()->constrained('kampanye');
            $table->foreignId('donasi_id')->nullable()->constrained('donasi');
            $table->string('bukti_url', 255)->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('created_at')->useCurrent();

            $table->index('jenis');
            $table->index('kampanye_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kas');
    }
};
