<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramKerja extends Model
{
    protected $table = 'program_kerja';

    protected $fillable = [
        'tahun', 'bidang', 'nama', 'deskripsi',
        'status', 'target_mulai', 'target_selesai', 'urutan', 'created_by',
    ];

    protected $casts = [
        'target_mulai'   => 'date:Y-m-d',
        'target_selesai' => 'date:Y-m-d',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
