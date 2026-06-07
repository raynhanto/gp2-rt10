<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KepalaKeluarga extends Model
{
    protected $table = 'kepala_keluarga';

    protected $fillable = [
        'unit_rumah_id', 'user_id', 'no_kk', 'nama', 'nik',
        'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama',
        'pendidikan', 'pekerjaan', 'no_wa', 'status_perkawinan',
        'status_tinggal', 'foto_kk_url', 'keterangan', 'created_by',
    ];

    protected function casts(): array
    {
        return ['tanggal_lahir' => 'date'];
    }

    public function unitRumah(): BelongsTo
    {
        return $this->belongsTo(UnitRumah::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function anggota(): HasMany
    {
        return $this->hasMany(AnggotaKeluarga::class);
    }

    public function kendaraan(): HasMany
    {
        return $this->hasMany(Kendaraan::class);
    }

    public function totalJiwa(): int
    {
        return 1 + $this->anggota()->count();
    }
}
