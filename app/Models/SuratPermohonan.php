<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratPermohonan extends Model
{
    protected $table = 'surat_permohonan';

    protected $fillable = [
        'user_id', 'jenis_surat', 'atas_nama', 'nik', 'alamat',
        'keperluan', 'data_tambahan', 'status',
        'nomor_surat', 'tanggal_surat', 'catatan_admin',
        'processed_by', 'processed_at',
    ];

    protected $casts = [
        'data_tambahan' => 'array',
        'tanggal_surat' => 'date:Y-m-d',
        'processed_at'  => 'datetime',
    ];

    public const JENIS = [
        'domisili'       => 'Surat Keterangan Domisili',
        'pengantar_ktp'  => 'Surat Pengantar KTP',
        'pengantar_kk'   => 'Surat Pengantar Kartu Keluarga',
        'sktm'           => 'Surat Keterangan Tidak Mampu',
        'usaha'          => 'Surat Keterangan Usaha',
        'kelahiran'      => 'Surat Keterangan Kelahiran',
        'kematian'       => 'Surat Keterangan Kematian',
        'pindah_masuk'   => 'Surat Keterangan Pindah Masuk',
        'pindah_keluar'  => 'Surat Keterangan Pindah Keluar',
        'izin_keramaian' => 'Surat Izin Keramaian',
        'belum_menikah'  => 'Surat Keterangan Belum Menikah',
        'lainnya'        => 'Surat Keterangan Lainnya',
    ];

    public function getJenisLabelAttribute(): string
    {
        return self::JENIS[$this->jenis_surat] ?? $this->jenis_surat;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
