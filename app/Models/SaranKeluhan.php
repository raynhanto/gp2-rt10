<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaranKeluhan extends Model
{
    protected $table = 'saran_keluhan';

    protected $fillable = [
        'user_id', 'nama_pengirim', 'no_wa', 'kategori',
        'judul', 'isi', 'is_anonym', 'status',
        'tanggapan', 'tanggapan_at', 'tanggapan_oleh',
    ];

    protected $casts = [
        'is_anonym'    => 'boolean',
        'tanggapan_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function respondedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tanggapan_oleh');
    }

    public function getNamaPengirimDisplayAttribute(): string
    {
        if ($this->is_anonym) return 'Anonim';
        return $this->nama_pengirim ?? $this->user?->nama ?? 'Tidak diketahui';
    }
}
