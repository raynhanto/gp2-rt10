<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_rumah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->char('blok', 1)->comment('Uppercase A-Z');
            $table->unsignedTinyInteger('nomor')->comment('1-99');
            $table->boolean('is_primary')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['blok', 'nomor']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_rumah');
    }
};
