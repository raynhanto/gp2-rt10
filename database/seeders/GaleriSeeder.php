<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GaleriSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::whereIn('role', ['admin', 'super_admin'])->value('id')
            ?? User::value('id');

        $now = now();

        $albums = [
            [
                'judul'      => 'Kerja Bakti RT 10 — Januari 2026',
                'deskripsi'  => 'Kegiatan kerja bakti rutin warga RT 10 membersihkan lingkungan dan fasilitas umum perumahan.',
                'kategori'   => 'kegiatan',
                'tanggal'    => '2026-01-19',
                'is_featured' => true,
                'fotos' => [
                    ['https://images.pexels.com/photos/6647010/pexels-photo-6647010.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1', 'Warga bergotong royong membersihkan jalan utama'],
                    ['https://images.pexels.com/photos/6646918/pexels-photo-6646918.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1', 'Pengecatan tembok fasilitas umum'],
                    ['https://images.pexels.com/photos/6647050/pexels-photo-6647050.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1', 'Pembersihan saluran air'],
                ],
            ],
            [
                'judul'      => 'Peringatan HUT RI ke-80 — Agustus 2025',
                'deskripsi'  => 'Rangkaian kegiatan peringatan Hari Kemerdekaan RI ke-80 bersama seluruh warga RT 10.',
                'kategori'   => 'kegiatan',
                'tanggal'    => '2025-08-17',
                'is_featured' => true,
                'fotos' => [
                    ['https://images.pexels.com/photos/3532557/pexels-photo-3532557.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1', 'Upacara pengibaran bendera merah putih'],
                    ['https://images.pexels.com/photos/1190297/pexels-photo-1190297.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1', 'Lomba 17-an warga RT 10'],
                    ['https://images.pexels.com/photos/3171837/pexels-photo-3171837.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1', 'Penampilan seni anak-anak warga'],
                    ['https://images.pexels.com/photos/976866/pexels-photo-976866.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1', 'Pembagian hadiah pemenang lomba'],
                ],
            ],
            [
                'judul'      => 'Posyandu & PKK Bulan Maret 2026',
                'deskripsi'  => 'Kegiatan Posyandu bulanan dan pertemuan PKK RT 10 Golden Park 2.',
                'kategori'   => 'sosial',
                'tanggal'    => '2026-03-15',
                'is_featured' => false,
                'fotos' => [
                    ['https://images.pexels.com/photos/5327656/pexels-photo-5327656.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1', 'Penimbangan dan pemeriksaan balita'],
                    ['https://images.pexels.com/photos/5327580/pexels-photo-5327580.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1', 'Pemberian vitamin dan imunisasi'],
                    ['https://images.pexels.com/photos/6647012/pexels-photo-6647012.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1', 'Pertemuan rutin ibu-ibu PKK'],
                ],
            ],
            [
                'judul'      => 'Fasilitas Umum RT 10',
                'deskripsi'  => 'Dokumentasi kondisi terkini fasilitas umum perumahan Golden Park 2 RT 10.',
                'kategori'   => 'fasilitas',
                'tanggal'    => '2026-05-01',
                'is_featured' => false,
                'fotos' => [
                    ['https://images.pexels.com/photos/1838741/pexels-photo-1838741.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1', 'Area taman bermain fasilitas umum 1'],
                    ['https://images.pexels.com/photos/358238/pexels-photo-358238.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1', 'Kondisi jalan lingkungan perumahan'],
                    ['https://images.pexels.com/photos/280222/pexels-photo-280222.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1', 'Pos keamanan perumahan'],
                ],
            ],
            [
                'judul'      => 'Santunan Anak Yatim — Ramadhan 2026',
                'deskripsi'  => 'Kegiatan sosial penyerahan santunan kepada anak yatim di lingkungan RT 10 dalam rangka bulan Ramadhan.',
                'kategori'   => 'sosial',
                'tanggal'    => '2026-03-25',
                'is_featured' => true,
                'fotos' => [
                    ['https://images.pexels.com/photos/6646917/pexels-photo-6646917.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1', 'Penyerahan santunan kepada anak yatim'],
                    ['https://images.pexels.com/photos/6646956/pexels-photo-6646956.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1', 'Foto bersama pengurus RT dan anak-anak'],
                ],
            ],
            [
                'judul'      => 'Rapat Warga — Perencanaan Program 2026',
                'deskripsi'  => 'Rapat umum warga RT 10 untuk membahas rencana program kerja dan anggaran tahun 2026.',
                'kategori'   => 'dokumentasi',
                'tanggal'    => '2026-01-05',
                'is_featured' => false,
                'fotos' => [
                    ['https://images.pexels.com/photos/1181406/pexels-photo-1181406.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1', 'Suasana rapat warga RT 10'],
                    ['https://images.pexels.com/photos/3182773/pexels-photo-3182773.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1', 'Ketua RT mempresentasikan rencana anggaran'],
                ],
            ],
            [
                'judul'      => 'Penanaman Pohon Fasilitas Umum',
                'deskripsi'  => 'Kegiatan penanaman 20 pohon peneduh di sepanjang jalan utama perumahan sebagai program lingkungan hijau.',
                'kategori'   => 'kegiatan',
                'tanggal'    => '2026-01-22',
                'is_featured' => false,
                'fotos' => [
                    ['https://images.pexels.com/photos/6913433/pexels-photo-6913433.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1', 'Proses penanaman pohon peneduh'],
                    ['https://images.pexels.com/photos/1072824/pexels-photo-1072824.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1', 'Anak-anak ikut berpartisipasi'],
                    ['https://images.pexels.com/photos/776656/pexels-photo-776656.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1', 'Hasil penanaman di sepanjang jalan utama'],
                ],
            ],
            [
                'judul'      => 'Olahraga Bersama — Minggu Pagi',
                'deskripsi'  => 'Kegiatan olahraga bersama warga RT 10 setiap Minggu pagi di area fasilitas umum.',
                'kategori'   => 'lainnya',
                'tanggal'    => '2026-04-06',
                'is_featured' => false,
                'fotos' => [
                    ['https://images.pexels.com/photos/1552252/pexels-photo-1552252.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1', 'Senam bersama warga RT 10'],
                    ['https://images.pexels.com/photos/2294350/pexels-photo-2294350.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1', 'Badminton warga RT 10'],
                ],
            ],
        ];

        foreach ($albums as $album) {
            $fotos = $album['fotos'];
            unset($album['fotos']);

            $albumId = DB::table('galeri')->insertGetId([
                ...$album,
                'cover_url'  => $fotos[0][0],
                'is_public'  => true,
                'created_by' => $adminId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($fotos as $i => [$url, $ket]) {
                DB::table('galeri_foto')->insert([
                    'galeri_id'   => $albumId,
                    'foto_url'    => $url,
                    'keterangan'  => $ket,
                    'urutan'      => $i,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }
        }
    }
}
