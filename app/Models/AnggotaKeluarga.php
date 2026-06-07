<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnggotaKeluarga extends Model
{
    protected $table = 'anggota_keluarga';

    protected $fillable = [
        'kepala_keluarga_id', 'user_id', 'nama', 'nik',
        'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama',
        'pendidikan', 'pekerjaan', 'hubungan', 'status_perkawinan',
    ];

    protected function casts(): array
    {
        return ['tanggal_lahir' => 'date'];
    }

    public function kepalaKeluarga(): BelongsTo
    {
        return $this->belongsTo(KepalaKeluarga::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
