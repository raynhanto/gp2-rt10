<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class UnitRumah extends Model
{
    public $timestamps = false;

    protected $table = 'unit_rumah';

    protected $fillable = ['user_id', 'blok', 'nomor', 'is_primary', 'lat', 'lng'];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'lat'        => 'float',
            'lng'        => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kepalaKeluarga(): HasMany
    {
        return $this->hasMany(KepalaKeluarga::class);
    }

    public function format(): string
    {
        return "Blok {$this->blok} / No. {$this->nomor}";
    }

    public static function isTaken(string $blok, int $nomor, int $exceptUserId = 0): bool
    {
        return self::where('blok', strtoupper($blok))
            ->where('nomor', $nomor)
            ->where('user_id', '!=', $exceptUserId)
            ->exists();
    }

    public static function syncForUser(int $userId, array $units): void
    {
        DB::transaction(function () use ($userId, $units) {
            self::where('user_id', $userId)->delete();

            foreach ($units as $i => $unit) {
                $blok  = strtoupper(trim((string) $unit['blok']));
                $nomor = (int) $unit['nomor'];

                if (!preg_match('/^[A-Z]$/', $blok) || $nomor < 1 || $nomor > 99) {
                    throw new \InvalidArgumentException("Unit tidak valid: Blok $blok No. $nomor");
                }

                self::create([
                    'user_id'    => $userId,
                    'blok'       => $blok,
                    'nomor'      => $nomor,
                    'is_primary' => $i === 0,
                ]);
            }
        });
    }
}
