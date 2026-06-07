<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GaleriFoto extends Model
{
    protected $table = 'galeri_foto';

    protected $fillable = ['galeri_id', 'foto_url', 'keterangan', 'urutan'];

    public function galeri(): BelongsTo
    {
        return $this->belongsTo(Galeri::class);
    }
}
