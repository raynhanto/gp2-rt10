<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Galeri extends Model
{
    protected $table = 'galeri';

    protected $fillable = [
        'judul', 'deskripsi', 'cover_url', 'kategori',
        'tanggal', 'is_featured', 'is_public', 'created_by',
    ];

    protected $casts = [
        'tanggal'     => 'date:Y-m-d',
        'is_featured' => 'boolean',
        'is_public'   => 'boolean',
    ];

    public function fotos(): HasMany
    {
        return $this->hasMany(GaleriFoto::class)->orderBy('urutan')->orderBy('id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function refreshCover(): void
    {
        $first = $this->fotos()->value('foto_url');
        $this->update(['cover_url' => $first]);
    }
}
