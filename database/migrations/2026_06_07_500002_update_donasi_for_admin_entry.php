<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donasi', function (Blueprint $table) {
            // Allow admin-entered donations with no user account
            $table->dropForeign(['user_id']);
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

            // Donor name for admin-entered donations (cash, etc.)
            $table->string('donatur_nama', 100)->nullable()->after('user_id');
        });

        // Add 'tunai' to metode enum
        DB::statement("ALTER TABLE donasi MODIFY COLUMN metode ENUM('qris','transfer','tunai','gopay','ovo','dana','lainnya') DEFAULT 'qris'");
    }

    public function down(): void
    {
        Schema::table('donasi', function (Blueprint $table) {
            $table->dropColumn('donatur_nama');
            $table->dropForeign(['user_id']);
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users');
        });
        DB::statement("ALTER TABLE donasi MODIFY COLUMN metode ENUM('qris','transfer','gopay','ovo','dana','lainnya') DEFAULT 'qris'");
    }
};
