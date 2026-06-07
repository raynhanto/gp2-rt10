<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Anggaran extends Model
{
    protected $table = 'anggaran';

    protected $fillable = [
        'kampanye_id', 'tahun', 'sumber_dana', 'pos', 'estimasi', 'realisasi', 'catatan', 'created_by',
    ];

    public function kampanye(): BelongsTo
    {
        return $this->belongsTo(Kampanye::class);
    }
}
