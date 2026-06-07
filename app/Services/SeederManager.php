<?php

declare(strict_types=1);

namespace App\Services;

use Database\Seeders\AgendaSeeder;
use Database\Seeders\GaleriSeeder;
use Database\Seeders\InformasiSeeder;
use Illuminate\Support\Facades\DB;

class SeederManager
{
    /**
     * Registry of all managed seeders. Every new feature seeder must be added here.
     *
     * Each entry:
     *   key         – unique snake_case identifier
     *   label       – display name (Bahasa Indonesia)
     *   description – one-line description of what gets seeded
     *   group       – sidebar group label
     *   class       – fully-qualified seeder class
     *   tables      – tables whose rows the seeder inserts (used for ID snapshot + rollback)
     *   depends_on  – keys of seeders that must be applied first
     *   warning     – optional caution shown in the UI
     */
    public function definitions(): array
    {
        return [
            [
                'key'         => 'informasi',
                'label'       => 'Konten Informasi',
                'description' => '4 berita, 11 tata tertib, 11 program kerja, dan 3 pengumuman contoh.',
                'group'       => 'Sample Data',
                'class'       => InformasiSeeder::class,
                'tables'      => ['berita', 'tata_tertib', 'program_kerja', 'pengumuman'],
                'depends_on'  => [],
                'warning'     => null,
            ],
            [
                'key'         => 'galeri',
                'label'       => 'Album Galeri',
                'description' => '8 album foto kegiatan warga: kerja bakti, HUT RI, posyandu, fasilitas umum, sosial, rapat, dll.',
                'group'       => 'Sample Data',
                'class'       => GaleriSeeder::class,
                'tables'      => ['galeri', 'galeri_foto'],
                'depends_on'  => [],
                'warning'     => 'Membutuhkan minimal satu user dengan role admin atau super_admin.',
            ],
            [
                'key'         => 'agenda',
                'label'       => 'Agenda & Jadwal Kegiatan',
                'description' => '13 agenda kegiatan warga: rapat bulanan, kerja bakti, posyandu, arisan, olahraga, pengajian, dan kegiatan insidental.',
                'group'       => 'Sample Data',
                'class'       => AgendaSeeder::class,
                'tables'      => ['agenda'],
                'depends_on'  => [],
                'warning'     => 'Membutuhkan minimal satu user dengan role admin atau super_admin.',
            ],
        ];
    }

    public function find(string $key): ?array
    {
        foreach ($this->definitions() as $def) {
            if ($def['key'] === $key) {
                return $def;
            }
        }
        return null;
    }

    /** Snapshot the current max ID for each table before running a seeder. */
    public function snapshotBefore(array $tables): array
    {
        $snapshot = [];
        foreach ($tables as $table) {
            $snapshot[$table] = (int) (DB::table($table)->max('id') ?? 0);
        }
        return $snapshot;
    }

    /** Collect IDs inserted since the snapshot. */
    public function captureInserted(array $snapshot): array
    {
        $inserted = [];
        foreach ($snapshot as $table => $maxBefore) {
            $ids = DB::table($table)
                ->where('id', '>', $maxBefore)
                ->pluck('id')
                ->all();
            if (!empty($ids)) {
                $inserted[$table] = $ids;
            }
        }
        return $inserted;
    }

    /** Delete all rows tracked in a seeded_ids payload (reverse order for FK safety). */
    public function rollbackIds(array $seededIds): void
    {
        foreach (array_reverse($seededIds) as $table => $ids) {
            DB::table($table)->whereIn('id', $ids)->delete();
        }
    }
}
