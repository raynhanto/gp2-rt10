<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiInstan extends Model
{
    protected $table = 'transaksi_instan';

    protected $fillable = [
        'tanggal', 'nominal', 'pembayar_nama', 'pembayar_user_id',
        'tujuan', 'keterangan', 'kas_masuk_id', 'kas_keluar_id', 'created_by',
    ];

    protected function casts(): array
    {
        return ['tanggal' => 'date'];
    }

    public function pembayarUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pembayar_user_id');
    }

    public function kasMasuk(): BelongsTo
    {
        return $this->belongsTo(Kas::class, 'kas_masuk_id');
    }

    public function kasKeluar(): BelongsTo
    {
        return $this->belongsTo(Kas::class, 'kas_keluar_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
