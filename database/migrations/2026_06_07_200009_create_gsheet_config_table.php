<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gsheet_config', function (Blueprint $table) {
            $table->id();
            $table->string('spreadsheet_id', 200)->nullable();
            $table->string('tab_kas', 50)->default('Kas');
            $table->string('tab_iuran', 50)->default('Iuran');
            $table->string('tab_pengeluaran', 50)->default('Pengeluaran');
            $table->string('tab_donasi', 50)->default('Donasi');
            $table->string('tab_summary', 50)->default('Summary');
            $table->boolean('auto_sync')->default(false);
            $table->text('credentials_json')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gsheet_config');
    }
};
