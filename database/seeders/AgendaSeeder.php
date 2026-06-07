<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AgendaSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::whereIn('role', ['admin', 'super_admin'])->value('id')
            ?? User::firstOrFail()->id;

        $now  = now();
        $year = (int) date('Y');

        // [judul, deskripsi, tanggal, waktu_mulai, waktu_selesai, lokasi, kategori, jenis_jadwal, hari_minggu, tanggal_akhir]
        $events = [
            // ── Rapat ─────────────────────────────────────────────────
            [
                'judul'        => 'Rapat Bulanan Pengurus RT',
                'deskripsi'    => 'Rapat koordinasi pengurus RT membahas perkembangan program kerja, keuangan, dan isu lingkungan bulan berjalan.',
                'tanggal'      => "$year-06-01",
                'waktu_mulai'  => '19:30',
                'waktu_selesai'=> '21:00',
                'lokasi'       => 'Rumah Ketua RT',
                'kategori'     => 'rapat',
                'jenis_jadwal' => 'bulanan',
                'tanggal_akhir'=> "$year-12-31",
            ],
            [
                'judul'        => 'Rapat Umum Warga RT 10',
                'deskripsi'    => 'Rapat tahunan warga untuk evaluasi program kerja, laporan keuangan, dan perencanaan tahun depan. Seluruh kepala keluarga diharapkan hadir.',
                'tanggal'      => "$year-12-07",
                'waktu_mulai'  => '09:00',
                'waktu_selesai'=> '12:00',
                'lokasi'       => 'Fasilitas Umum 1 RT 10',
                'kategori'     => 'rapat',
                'jenis_jadwal' => 'sekali',
                'tanggal_akhir'=> null,
            ],

            // ── Kegiatan rutin ────────────────────────────────────────
            [
                'judul'        => 'Kerja Bakti Rutin',
                'deskripsi'    => 'Kerja bakti membersihkan dan merawat fasilitas umum, drainase, dan lingkungan perumahan. Harap membawa peralatan masing-masing.',
                'tanggal'      => "$year-06-15",
                'waktu_mulai'  => '07:00',
                'waktu_selesai'=> '10:00',
                'lokasi'       => 'Area Fasilitas Umum RT 10',
                'kategori'     => 'kegiatan',
                'jenis_jadwal' => 'bulanan',
                'tanggal_akhir'=> "$year-12-31",
            ],
            [
                'judul'        => 'Olahraga Bersama Warga',
                'deskripsi'    => 'Senam pagi dan olahraga bersama warga RT 10. Terbuka untuk seluruh anggota keluarga. Snack dan minuman tersedia.',
                'tanggal'      => "$year-06-08",
                'waktu_mulai'  => '06:30',
                'waktu_selesai'=> '08:00',
                'lokasi'       => 'Area Fasilitas Umum RT 10',
                'kategori'     => 'olahraga',
                'jenis_jadwal' => 'mingguan',
                'hari_minggu'  => [0], // Minggu
                'tanggal_akhir'=> "$year-12-28",
            ],
            [
                'judul'        => 'Posyandu Balita',
                'deskripsi'    => 'Kegiatan Posyandu bulanan: penimbangan, pengukuran tinggi badan, imunisasi, dan konsultasi gizi untuk balita usia 0–5 tahun.',
                'tanggal'      => "$year-06-14",
                'waktu_mulai'  => '08:00',
                'waktu_selesai'=> '11:00',
                'lokasi'       => 'Rumah Kader Posyandu RT 10',
                'kategori'     => 'sosial',
                'jenis_jadwal' => 'bulanan',
                'tanggal_akhir'=> "$year-12-31",
            ],
            [
                'judul'        => 'Arisan Warga RT 10',
                'deskripsi'    => 'Arisan bulanan warga RT 10. Selain arisan, diisi dengan sharing informasi dan diskusi ringan antar warga.',
                'tanggal'      => "$year-06-20",
                'waktu_mulai'  => '16:00',
                'waktu_selesai'=> '18:00',
                'lokasi'       => 'Bergilir (lihat info WhatsApp grup)',
                'kategori'     => 'sosial',
                'jenis_jadwal' => 'bulanan',
                'tanggal_akhir'=> "$year-12-31",
            ],
            [
                'judul'        => 'Pengajian Warga',
                'deskripsi'    => 'Pengajian dan ceramah agama rutin warga RT 10. Terbuka untuk seluruh warga.',
                'tanggal'      => "$year-06-06",
                'waktu_mulai'  => '18:30',
                'waktu_selesai'=> '20:00',
                'lokasi'       => 'Musholla Terdekat / Bergilir',
                'kategori'     => 'sosial',
                'jenis_jadwal' => 'mingguan',
                'hari_minggu'  => [5], // Jumat
                'tanggal_akhir'=> "$year-12-26",
            ],

            // ── Kegiatan satu kali ────────────────────────────────────
            [
                'judul'        => 'Pemasangan CCTV Tambahan',
                'deskripsi'    => 'Pemasangan 2 unit CCTV baru di pintu masuk utama dan pintu belakang. Warga diharapkan memberikan akses kepada teknisi.',
                'tanggal'      => "$year-07-05",
                'waktu_mulai'  => '09:00',
                'waktu_selesai'=> '15:00',
                'lokasi'       => 'Pintu Masuk Utama & Belakang RT 10',
                'kategori'     => 'kegiatan',
                'jenis_jadwal' => 'sekali',
                'tanggal_akhir'=> null,
            ],
            [
                'judul'        => 'Bazar Warga RT 10',
                'deskripsi'    => 'Bazar UMKM warga RT 10 — ajang promosi produk lokal warga. Pendaftaran booth melalui sekretaris RT. Terbuka untuk umum.',
                'tanggal'      => "$year-08-17",
                'waktu_mulai'  => '08:00',
                'waktu_selesai'=> '17:00',
                'lokasi'       => 'Area Fasilitas Umum 1 RT 10',
                'kategori'     => 'kegiatan',
                'jenis_jadwal' => 'sekali',
                'tanggal_akhir'=> null,
            ],
            [
                'judul'        => 'Vaksinasi & Pemeriksaan Kesehatan Gratis',
                'deskripsi'    => 'Pemeriksaan kesehatan gratis (tensi, gula darah, kolesterol) dan vaksinasi influenza bekerja sama dengan Puskesmas setempat.',
                'tanggal'      => "$year-07-20",
                'waktu_mulai'  => '08:00',
                'waktu_selesai'=> '12:00',
                'lokasi'       => 'Pos RT / Fasilitas Umum 1',
                'kategori'     => 'sosial',
                'jenis_jadwal' => 'sekali',
                'tanggal_akhir'=> null,
            ],
            [
                'judul'        => 'Pelatihan Tanggap Darurat (P3K & APAR)',
                'deskripsi'    => 'Pelatihan pertolongan pertama dan penggunaan Alat Pemadam Api Ringan (APAR) untuk kepala keluarga. Dipandu oleh instruktur bersertifikat.',
                'tanggal'      => "$year-08-02",
                'waktu_mulai'  => '09:00',
                'waktu_selesai'=> '12:00',
                'lokasi'       => 'Area Fasilitas Umum RT 10',
                'kategori'     => 'kegiatan',
                'jenis_jadwal' => 'sekali',
                'tanggal_akhir'=> null,
            ],
            [
                'judul'        => 'Seleksi Beasiswa Anak Berprestasi',
                'deskripsi'    => 'Seleksi beasiswa RT 10 untuk anak warga berprestasi. Berkas meliputi rapor 2 semester, surat keterangan sekolah, dan foto. Pendaftaran di sekretaris RT.',
                'tanggal'      => "$year-09-06",
                'waktu_mulai'  => '09:00',
                'waktu_selesai'=> '12:00',
                'lokasi'       => 'Rumah Sekretaris RT',
                'kategori'     => 'kegiatan',
                'jenis_jadwal' => 'sekali',
                'tanggal_akhir'=> null,
            ],
            [
                'judul'        => 'Perbaikan Drainase Blok B',
                'deskripsi'    => 'Pengerjaan perbaikan saluran drainase di Blok B yang sering tersumbat. Mohon maaf atas ketidaknyamanan selama pengerjaan.',
                'tanggal'      => "$year-05-12",
                'waktu_mulai'  => '07:00',
                'waktu_selesai'=> '16:00',
                'lokasi'       => 'Blok B RT 10',
                'kategori'     => 'kegiatan',
                'jenis_jadwal' => 'sekali',
                'tanggal_akhir'=> null,
            ],
        ];

        foreach ($events as $event) {
            $hariMinggu = $event['hari_minggu'] ?? null;
            DB::table('agenda')->insert([
                'judul'          => $event['judul'],
                'deskripsi'      => $event['deskripsi'],
                'tanggal'        => $event['tanggal'],
                'waktu_mulai'    => $event['waktu_mulai'],
                'waktu_selesai'  => $event['waktu_selesai'] ?? null,
                'lokasi'         => $event['lokasi'] ?? null,
                'kategori'       => $event['kategori'],
                'jenis_jadwal'   => $event['jenis_jadwal'],
                'hari_minggu'    => $hariMinggu ? json_encode($hariMinggu) : null,
                'tanggal_akhir'  => $event['tanggal_akhir'] ?? null,
                'target_audience'=> 'semua',
                'target_user_ids'=> null,
                'is_public'      => true,
                'created_by'     => $adminId,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
        }
    }
}
