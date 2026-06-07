<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kampanye extends Model
{
    protected $table = 'kampanye';

    protected $fillable = [
        'judul', 'deskripsi', 'target', 'status',
        'foto_url', 'deadline', 'created_by',
    ];

    protected function casts(): array
    {
        return ['deadline' => 'date'];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function donasi(): HasMany
    {
        return $this->hasMany(Donasi::class);
    }

    public function anggaran(): HasMany
    {
        return $this->hasMany(Anggaran::class);
    }

    public function refreshTerkumpul(): void
    {
        $this->terkumpul = $this->donasi()->where('status', 'verified')->sum('nominal');
        $this->save();
    }
}
