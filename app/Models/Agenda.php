<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Agenda extends Model
{
    protected $table = 'agenda';

    protected $fillable = [
        'judul', 'deskripsi', 'tanggal', 'waktu_mulai', 'waktu_selesai',
        'lokasi', 'kategori', 'is_public', 'created_by',
        'jenis_jadwal', 'hari_minggu', 'tanggal_akhir',
        'target_audience', 'target_user_ids',
    ];

    protected $casts = [
        'tanggal'         => 'date:Y-m-d',
        'tanggal_akhir'   => 'date:Y-m-d',
        'is_public'       => 'boolean',
        'hari_minggu'     => 'array',
        'target_user_ids' => 'array',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusAttribute(): string
    {
        $today = now()->toDateString();
        if ($this->tanggal->toDateString() > $today) return 'akan_datang';
        if ($this->tanggal->toDateString() === $today) return 'hari_ini';
        return 'selesai';
    }

    public function isRecurring(): bool
    {
        return !in_array($this->jenis_jadwal ?? 'sekali', ['sekali', null], true);
    }
}
