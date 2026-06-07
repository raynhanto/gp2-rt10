<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InformasiSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // ── Berita ──────────────────────────────────────────────
        DB::table('berita')->insert([
            [
                'judul'        => 'Selamat Datang di Portal RT 10 Golden Park 2',
                'slug'         => 'selamat-datang-portal-rt10',
                'isi'          => "Dengan bangga kami meluncurkan portal digital RT 10 Golden Park 2. Melalui portal ini, warga dapat memantau kegiatan, berdonasi, membayar iuran, dan mendapatkan informasi terbaru dari pengurus RT.\n\nKami berharap portal ini dapat mempererat komunikasi antara pengurus dan seluruh warga RT 10. Silakan login menggunakan akun Google untuk mengakses fitur lengkap.",
                'cover_url'    => null,
                'kategori'     => 'informasi',
                'is_published' => true,
                'is_pinned'    => true,
                'created_by'   => null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'judul'        => 'Kerja Bakti Rutin Setiap Minggu Ketiga',
                'slug'         => 'kerja-bakti-rutin-minggu-ketiga',
                'isi'          => "Pengurus RT 10 mengumumkan bahwa kegiatan Kerja Bakti akan dilaksanakan secara rutin setiap Minggu ketiga setiap bulannya, mulai pukul 07.00 WIB.\n\nSeluruh warga diharapkan dapat berpartisipasi aktif. Peralatan kebersihan (sapu, cangkul, dll) dapat disiapkan sendiri atau menggunakan inventaris RT yang tersedia di pos keamanan.\n\nKegiatan ini bertujuan menjaga kebersihan dan keindahan lingkungan perumahan kita bersama.",
                'cover_url'    => null,
                'kategori'     => 'kegiatan',
                'is_published' => true,
                'is_pinned'    => false,
                'created_by'   => null,
                'created_at'   => $now->copy()->subDays(7),
                'updated_at'   => $now->copy()->subDays(7),
            ],
            [
                'judul'        => 'Jadwal Ronda Malam Bulan Ini',
                'slug'         => 'jadwal-ronda-malam-bulan-ini',
                'isi'          => "Berikut jadwal ronda malam untuk bulan ini. Setiap warga (kepala keluarga) wajib berpartisipasi minimal sekali dalam sebulan.\n\nJadwal dibagi per blok dan akan dirotasi setiap bulannya. Warga yang tidak dapat hadir diharapkan menghubungi ketua RT minimal H-1 untuk penjadwalan ulang.\n\nJam ronda: 22.00 – 01.00 WIB",
                'cover_url'    => null,
                'kategori'     => 'pengumuman',
                'is_published' => true,
                'is_pinned'    => false,
                'created_by'   => null,
                'created_at'   => $now->copy()->subDays(14),
                'updated_at'   => $now->copy()->subDays(14),
            ],
            [
                'judul'        => 'Prosedur Pengajuan Surat Keterangan Domisili',
                'slug'         => 'prosedur-surat-keterangan-domisili',
                'isi'          => "Bagi warga yang membutuhkan Surat Keterangan Domisili, berikut prosedur pengajuannya:\n\n1. Hubungi Sekretaris RT melalui WhatsApp atau datang langsung\n2. Siapkan fotokopi KTP dan KK\n3. Surat akan diproses maksimal 2 hari kerja\n4. Surat ditandatangani oleh Ketua RT\n\nUntuk informasi lebih lanjut silakan menghubungi Sekretaris RT.",
                'cover_url'    => null,
                'kategori'     => 'informasi',
                'is_published' => true,
                'is_pinned'    => false,
                'created_by'   => null,
                'created_at'   => $now->copy()->subDays(30),
                'updated_at'   => $now->copy()->subDays(30),
            ],
        ]);

        // ── Tata Tertib ─────────────────────────────────────────
        $rules = [
            // Keamanan
            ['Keamanan', 'Pasal 1', 'Kewajiban Ronda Malam', 'Setiap kepala keluarga wajib mengikuti kegiatan ronda malam sesuai jadwal yang telah ditetapkan oleh pengurus RT.', 1],
            ['Keamanan', 'Pasal 2', 'Tamu Menginap', 'Tamu yang menginap lebih dari 2 malam wajib dilaporkan kepada pengurus RT dan dicatat di pos keamanan.', 2],
            ['Keamanan', 'Pasal 3', 'Akses Kendaraan', 'Kendaraan yang masuk ke area perumahan wajib melalui pintu masuk utama dan melapor kepada petugas keamanan.', 3],
            // Kebersihan
            ['Kebersihan', 'Pasal 4', 'Jadwal Pengumpulan Sampah', 'Sampah rumah tangga wajib diletakkan di tempat sampah depan rumah setiap pagi sebelum pukul 06.00 WIB pada hari Senin, Rabu, dan Jumat.', 1],
            ['Kebersihan', 'Pasal 5', 'Larangan Membuang Sampah Sembarangan', 'Dilarang keras membuang sampah di jalanan, saluran air, atau area umum perumahan.', 2],
            ['Kebersihan', 'Pasal 6', 'Perawatan Taman Depan', 'Setiap warga bertanggung jawab menjaga kebersihan dan kerapian taman atau area depan rumah masing-masing.', 3],
            // Parkir
            ['Parkir', 'Pasal 7', 'Parkir di Badan Jalan', 'Dilarang memarkirkan kendaraan di badan jalan utama perumahan sehingga menghalangi akses warga lain, kecuali dalam kondisi darurat.', 1],
            ['Parkir', 'Pasal 8', 'Kendaraan Tamu', 'Kendaraan tamu yang berkunjung lebih dari 2 jam wajib diparkir di area parkir tamu yang telah disediakan.', 2],
            // Kebisingan
            ['Kebisingan', 'Pasal 9', 'Jam Tenang', 'Setiap warga wajib menjaga ketenangan lingkungan, terutama pada jam 22.00 – 05.00 WIB. Kegiatan yang menimbulkan kebisingan dilarang pada jam tersebut.', 1],
            ['Kebisingan', 'Pasal 10', 'Acara / Hajatan', 'Warga yang akan mengadakan acara yang berpotensi menimbulkan keramaian wajib memberitahukan kepada pengurus RT minimal 3 hari sebelumnya.', 2],
            // Hewan Peliharaan
            ['Hewan Peliharaan', 'Pasal 11', 'Anjing dan Kucing', 'Hewan peliharaan yang dibawa keluar rumah wajib menggunakan tali atau kandang. Pemilik bertanggung jawab atas kebersihan kotoran hewan di area umum.', 1],
        ];

        foreach ($rules as $i => [$kategori, $pasal, $judul, $isi, $urutan]) {
            DB::table('tata_tertib')->insert([
                'kategori'   => $kategori,
                'pasal'      => $pasal,
                'judul'      => $judul,
                'isi'        => $isi,
                'urutan'     => $urutan,
                'is_active'  => true,
                'created_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // ── Program Kerja ────────────────────────────────────────
        $tahun = (int) date('Y');
        $programs = [
            // Keamanan
            [$tahun, 'Keamanan', 'Pemasangan CCTV Tambahan di Pintu Masuk', 'Penambahan 2 unit CCTV di area pintu masuk utama dan pintu belakang.', 'selesai', "$tahun-01-15", "$tahun-02-28", 1],
            [$tahun, 'Keamanan', 'Renovasi Pos Keamanan', 'Renovasi dan pengecatan ulang pos keamanan perumahan.', 'berjalan', "$tahun-04-01", "$tahun-06-30", 2],
            [$tahun, 'Keamanan', 'Pelatihan Petugas Keamanan', 'Pelatihan dasar keamanan dan pertolongan pertama untuk petugas ronda.', 'rencana', "$tahun-07-01", "$tahun-07-31", 3],
            // Sosial
            [$tahun, 'Sosial', 'Santunan Anak Yatim Hari Raya', 'Pengumpulan dan penyaluran santunan kepada anak yatim di lingkungan RT 10.', 'selesai', "$tahun-03-01", "$tahun-04-10", 1],
            [$tahun, 'Sosial', 'Bazar Warga RT 10', 'Penyelenggaraan bazar UMKM warga sebagai sarana promosi produk lokal.', 'rencana', "$tahun-08-17", "$tahun-08-17", 2],
            [$tahun, 'Sosial', 'Program Beasiswa Anak Berprestasi', 'Seleksi dan pemberian beasiswa kepada anak warga RT 10 yang berprestasi.', 'rencana', "$tahun-09-01", "$tahun-10-31", 3],
            // Lingkungan
            [$tahun, 'Lingkungan', 'Penanaman Pohon di Area Fasilitas Umum', 'Penanaman 20 pohon peneduh di sepanjang jalan utama perumahan.', 'selesai', "$tahun-01-22", "$tahun-02-05", 1],
            [$tahun, 'Lingkungan', 'Perbaikan Drainase Blok B', 'Pembersihan dan perbaikan saluran drainase di Blok B yang sering tersumbat.', 'berjalan', "$tahun-05-01", "$tahun-07-31", 2],
            [$tahun, 'Lingkungan', 'Taman Bermain Anak', 'Pemasangan peralatan bermain anak di area fasilitas umum perumahan.', 'rencana', "$tahun-09-01", "$tahun-11-30", 3],
            // Administrasi
            [$tahun, 'Administrasi', 'Digitalisasi Data Kependudukan', 'Pembaruan dan digitalisasi data KK seluruh warga RT 10 ke dalam sistem portal.', 'berjalan', "$tahun-03-01", "$tahun-12-31", 1],
            [$tahun, 'Administrasi', 'Rapat Tahunan Warga', 'Penyelenggaraan rapat tahunan untuk evaluasi program kerja dan perencanaan tahun depan.', 'rencana', "$tahun-12-01", "$tahun-12-15", 2],
        ];

        foreach ($programs as [$tahunPk, $bidang, $nama, $deskripsi, $status, $mulai, $selesai, $urutan]) {
            DB::table('program_kerja')->insert([
                'tahun'          => $tahunPk,
                'bidang'         => $bidang,
                'nama'           => $nama,
                'deskripsi'      => $deskripsi,
                'status'         => $status,
                'target_mulai'   => $mulai,
                'target_selesai' => $selesai,
                'urutan'         => $urutan,
                'created_by'     => null,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
        }

        // ── Pengumuman ───────────────────────────────────────────
        $uid = DB::table('users')->value('id');
        if ($uid) {
            DB::table('pengumuman')->insert([
                [
                    'judul'      => 'Iuran Bulan ' . now()->locale('id')->monthName . ' ' . now()->year . ' Sudah Dapat Dibayar',
                    'isi'        => "Yth. Seluruh Warga RT 10,\n\nIuran bulanan sudah dapat dibayarkan. Batas pembayaran adalah tanggal 20.\n\nPembayaran dapat dilakukan melalui portal warga atau langsung ke bendahara RT.\n\nTerima kasih.",
                    'target'     => 'semua',
                    'created_by' => $uid,
                    'created_at' => $now,
                ],
                [
                    'judul'      => 'Jadwal Kerja Bakti Bulan Ini',
                    'isi'        => "Warga RT 10 yang terhormat,\n\nKegiatan Kerja Bakti akan dilaksanakan pada Minggu ketiga bulan ini pukul 07.00 – 10.00 WIB.\n\nMohon kehadiran seluruh warga. Harap membawa peralatan kebersihan masing-masing.",
                    'target'     => 'semua',
                    'created_by' => $uid,
                    'created_at' => $now->copy()->subDays(5),
                ],
                [
                    'judul'      => 'Informasi Perbaikan Saluran Air Blok B',
                    'isi'        => "Diberitahukan kepada warga bahwa akan dilaksanakan perbaikan saluran air di Blok B dalam waktu dekat.\n\nMohon maaf atas ketidaknyamanan yang ditimbulkan. Informasi lebih lanjut hubungi pengurus RT.",
                    'target'     => 'semua',
                    'created_by' => $uid,
                    'created_at' => $now->copy()->subDays(12),
                ],
            ]);
        }
    }
}
