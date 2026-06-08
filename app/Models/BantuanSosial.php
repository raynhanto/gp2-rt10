<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BantuanSosial extends Model
{
    protected $table = 'bantuan_sosial';

    protected $fillable = [
        'unit_rumah_id', 'nama_penerima', 'jenis', 'keterangan',
        'tanggal', 'nominal', 'status', 'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
    ];

    public const JENIS = [
        'sembako'    => 'Sembako',
        'uang_tunai' => 'Uang Tunai',
        'kesehatan'  => 'Kesehatan',
        'pendidikan' => 'Pendidikan',
        'lainnya'    => 'Lainnya',
    ];

    public function unitRumah(): BelongsTo
    {
        return $this->belongsTo(UnitRumah::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
