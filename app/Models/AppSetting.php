<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AppSetting extends Model
{
    protected $table      = 'app_settings';
    protected $primaryKey = 'key';
    public    $incrementing = false;
    protected $keyType    = 'string';
    protected $fillable   = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrInsert(['key' => $key], ['value' => $value, 'updated_at' => now()]);
    }

    public static function allPublic(): array
    {
        return static::whereIn('key', [
            'qris_url', 'bank_name', 'bank_account', 'bank_holder',
            'whatsapp', 'rt_name', 'rt_address',
        ])->pluck('value', 'key')->toArray();
    }
}
