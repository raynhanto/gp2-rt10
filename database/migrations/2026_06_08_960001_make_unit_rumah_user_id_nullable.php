<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Raw ALTER avoids doctrine/dbal dependency and FK constraint issues
        DB::statement('ALTER TABLE unit_rumah MODIFY user_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE unit_rumah MODIFY user_id BIGINT UNSIGNED NOT NULL');
    }
};
