<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donasi extends Model
{
    protected $table = 'donasi';

    protected $fillable = [
        'user_id', 'donatur_nama', 'kampanye_id', 'nominal', 'metode',
        'is_anonym', 'bukti_url', 'status',
        'catatan_admin', 'verified_by', 'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'is_anonym'   => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kampanye(): BelongsTo
    {
        return $this->belongsTo(Kampanye::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
