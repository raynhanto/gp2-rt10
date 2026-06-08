<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsulanPembangunan extends Model
{
    protected $table = 'usulan_pembangunan';

    protected $fillable = [
        'user_id', 'nama_pengusul', 'judul', 'deskripsi', 'lokasi',
        'prioritas', 'status', 'is_anonym',
        'tanggapan', 'tanggapan_at', 'tanggapan_oleh',
    ];

    protected $casts = [
        'is_anonym'    => 'boolean',
        'tanggapan_at' => 'datetime',
    ];

    public function getPengusulDisplayAttribute(): string
    {
        if ($this->is_anonym) return 'Anonim';
        if ($this->user) return $this->user->nama ?: $this->user->email;
        return $this->nama_pengusul ?: 'Anonim';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function respondedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tanggapan_oleh');
    }
}
