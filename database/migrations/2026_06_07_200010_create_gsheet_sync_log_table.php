<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gsheet_sync_log', function (Blueprint $table) {
            $table->id();
            $table->string('tab', 50);
            $table->unsignedSmallInteger('rows_written')->default(0);
            $table->enum('status', ['ok', 'error'])->default('ok');
            $table->text('error_msg')->nullable();
            $table->unsignedSmallInteger('duration_ms')->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->index('tab');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gsheet_sync_log');
    }
};
