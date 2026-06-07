<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tata_tertib', function (Blueprint $table) {
            $table->id();
            $table->string('kategori');         // e.g. "Keamanan", "Kebersihan", "Parkir"
            $table->string('pasal')->nullable(); // e.g. "Pasal 1", "1.1"
            $table->string('judul');
            $table->text('isi');
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tata_tertib');
    }
};
