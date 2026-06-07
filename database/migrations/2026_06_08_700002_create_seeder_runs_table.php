<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seeder_runs', function (Blueprint $table) {
            $table->id();
            $table->string('seeder_key');
            $table->json('seeded_ids')->nullable();
            $table->foreignId('run_by')->constrained('users');
            $table->timestamp('run_at');
            $table->foreignId('rolled_back_by')->nullable()->constrained('users');
            $table->timestamp('rolled_back_at')->nullable();
            $table->string('status')->default('applied'); // applied | rolled_back
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seeder_runs');
    }
};
