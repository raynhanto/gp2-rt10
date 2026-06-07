<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Extend role enum on users
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('warga','admin','super_admin','bendahara','sekretaris') NOT NULL DEFAULT 'warga'");

        Schema::create('admin_activity_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('action', 100);
            $table->string('model_type', 60)->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->text('description')->nullable();
            $table->json('context')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('user_id');
            $table->index('action');
            $table->index('created_at');
            $table->index(['model_type', 'model_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_activity_log');
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('warga','admin') NOT NULL DEFAULT 'warga'");
    }
};
