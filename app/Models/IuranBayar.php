<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class IuranBayar extends Model
{
    protected $table = 'iuran_bayar';

    protected $fillable = [
        'iuran_tagihan_id', 'nominal', 'metode', 'catatan', 'bukti_url',
        'status', 'verified_by', 'verified_at', 'created_by',
    ];

    protected function casts(): array
    {
        return ['verified_at' => 'datetime'];
    }

    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(IuranTagihan::class, 'iuran_tagihan_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function lampiran(): MorphMany
    {
        return $this->morphMany(Lampiran::class, 'attachable');
    }
}
