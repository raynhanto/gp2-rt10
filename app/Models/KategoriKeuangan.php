<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriKeuangan extends Model
{
    protected $table = 'kategori_keuangan';

    protected $fillable = ['nama', 'jenis', 'warna', 'ikon', 'urutan', 'created_by'];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function kasEntries(): HasMany
    {
        return $this->hasMany(Kas::class, 'kategori_id');
    }

    public function pengeluaranEntries(): HasMany
    {
        return $this->hasMany(Pengeluaran::class, 'kategori_id');
    }
}
