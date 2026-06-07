<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kendaraan extends Model
{
    protected $table = 'kendaraan';

    protected $fillable = [
        'kepala_keluarga_id', 'jenis', 'merek', 'model',
        'warna', 'plat_nomor', 'tahun',
    ];

    public function kepalaKeluarga(): BelongsTo
    {
        return $this->belongsTo(KepalaKeluarga::class);
    }
}
