<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Lampiran extends Model
{
    public $timestamps = false;

    protected $table = 'lampiran';

    protected $fillable = [
        'attachable_type', 'attachable_id',
        'nama_asli', 'url', 'mime', 'ukuran_kb',
        'created_by',
    ];

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
