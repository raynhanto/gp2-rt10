<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IuranTagihan extends Model
{
    public $timestamps = false;

    protected $table = 'iuran_tagihan';

    protected $fillable = ['unit_rumah_id', 'iuran_periode_id', 'status'];

    public function unitRumah(): BelongsTo
    {
        return $this->belongsTo(UnitRumah::class, 'unit_rumah_id');
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(IuranPeriode::class, 'iuran_periode_id');
    }

    public function bayar(): HasMany
    {
        return $this->hasMany(IuranBayar::class, 'iuran_tagihan_id');
    }
}
