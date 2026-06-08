<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventaris extends Model
{
    protected $table = 'inventaris';

    protected $fillable = [
        'nama', 'kode', 'kategori', 'jumlah', 'kondisi',
        'lokasi', 'tanggal_beli', 'harga_beli', 'keterangan',
        'foto_url', 'created_by',
    ];

    protected $casts = [
        'tanggal_beli' => 'date:Y-m-d',
        'jumlah'       => 'integer',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
