<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GsheetConfig extends Model
{
    protected $table = 'gsheet_config';

    protected $fillable = [
        'spreadsheet_id',
        'tab_kas', 'tab_iuran', 'tab_pengeluaran', 'tab_donasi', 'tab_summary',
        'auto_sync', 'credentials_json', 'last_synced_at',
    ];

    protected function casts(): array
    {
        return [
            'auto_sync'      => 'boolean',
            'last_synced_at' => 'datetime',
        ];
    }

    public static function singleton(): self
    {
        return self::firstOrCreate(['id' => 1]);
    }
}
