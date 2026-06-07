<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IuranPeriode extends Model
{
    public $timestamps = false;

    protected $table = 'iuran_periode';

    protected $fillable = ['bulan', 'tahun', 'nominal', 'jatuh_tempo', 'keterangan', 'created_by'];

    protected function casts(): array
    {
        return ['jatuh_tempo' => 'date'];
    }

    public function tagihan(): HasMany
    {
        return $this->hasMany(IuranTagihan::class, 'iuran_periode_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getNamaBulanAttribute(): string
    {
        $bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        return ($bulan[$this->bulan] ?? '') . ' ' . $this->tahun;
    }
}
