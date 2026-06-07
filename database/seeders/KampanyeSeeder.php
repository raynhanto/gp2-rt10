<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Kampanye;
use App\Models\User;
use Illuminate\Database\Seeder;

class KampanyeSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $campaigns = [
            [
                'judul'     => 'Lighting Underpass',
                'deskripsi' => 'Program pemasangan lampu LED di area underpass lingkungan RT 10. Penerangan yang memadai akan meningkatkan keamanan dan kenyamanan warga yang melintas di malam hari. Lingkup pekerjaan meliputi pemasangan 6 titik lampu LED tahan cuaca, instalasi kabel, dan penyambungan ke panel listrik fasum.',
                'target'    => 8_000_000,
                'status'    => 'aktif',
                'deadline'  => '2026-09-30',
                'foto_url'  => 'https://images.pexels.com/photos/9962369/pexels-photo-9962369.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
            ],
            [
                'judul'     => 'Area Simpan APAR Underpass',
                'deskripsi' => 'Pengadaan dan pemasangan box penyimpanan Alat Pemadam Api Ringan (APAR) di area underpass. Box berbahan besi dengan kaca pelindung disertai bracket permanen, dilengkapi cat tanda darurat sesuai standar BNPB. Investasi kecil untuk keselamatan warga dari risiko kebakaran.',
                'target'    => 2_500_000,
                'status'    => 'aktif',
                'deadline'  => '2026-08-31',
                'foto_url'  => 'https://images.pexels.com/photos/4805958/pexels-photo-4805958.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
            ],
            [
                'judul'     => 'Pos RT',
                'deskripsi' => 'Pembangunan pos ronda RT 10 yang representatif sebagai pusat kegiatan keamanan lingkungan. Struktur baja ringan berukuran 2×2 meter dengan atap galvalum, dinding panel, dan penerangan. Pos ini akan menjadi titik kumpul kegiatan ronda malam dan pelayanan informasi warga.',
                'target'    => 18_000_000,
                'status'    => 'aktif',
                'deadline'  => '2026-10-31',
                'foto_url'  => 'https://images.pexels.com/photos/31594272/pexels-photo-31594272/free-photo-of-security-guard-at-modern-entrance-booth-outdoors.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
            ],
            [
                'judul'     => 'Kolam Ikan Fasum 1',
                'deskripsi' => 'Pembangunan kolam ikan hias di area fasilitas umum 1 sebagai sarana estetika dan rekreasi warga. Kolam beton ukuran 2×3 meter dengan kedalaman 80 cm, dilengkapi sistem filtrasi air, bebatuan dekoratif, dan populasi ikan koi/mas koki. Hasil panen ikan dapat menjadi pemasukan kas RT.',
                'target'    => 12_000_000,
                'status'    => 'aktif',
                'deadline'  => '2026-11-30',
                'foto_url'  => 'https://images.pexels.com/photos/33432562/pexels-photo-33432562.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
            ],
            [
                'judul'     => 'Balai Warga Fasum 1',
                'deskripsi' => 'Pembangunan balai warga permanen di area fasilitas umum 1 sebagai pusat kegiatan sosial dan musyawarah RT. Struktur bangunan dengan fondasi beton, rangka baja, atap genteng metal, dan lantai keramik. Balai ini akan menampung berbagai kegiatan warga: arisan, rapat RT, posyandu, dan kegiatan sosial lainnya.',
                'target'    => 80_000_000,
                'status'    => 'urgent',
                'deadline'  => '2026-12-31',
                'foto_url'  => 'https://images.pexels.com/photos/35335992/pexels-photo-35335992/free-photo-of-professional-conference-with-audience-listening-to-speaker.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
            ],
            [
                'judul'     => 'Kamar Mandi Fasum 1',
                'deskripsi' => 'Pembangunan fasilitas kamar mandi/toilet umum di area fasilitas umum 1 untuk mendukung kegiatan warga dan kegiatan balai. Meliputi bangunan toilet lengkap dengan kloset duduk, wastafel, keramik anti-selip, instalasi air bersih dan pembuangan, serta sistem septik tank tertutup.',
                'target'    => 22_000_000,
                'status'    => 'aktif',
                'deadline'  => '2026-12-31',
                'foto_url'  => 'https://images.pexels.com/photos/24971993/pexels-photo-24971993/free-photo-of-interior-of-public-toilets.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
            ],
            [
                'judul'     => 'Pompa Air Ikan Fasum 1',
                'deskripsi' => 'Pengadaan dan instalasi sistem pompa air untuk kolam ikan di fasilitas umum 1. Meliputi pompa submersible berkualitas, pipa distribusi, filter mekanikal, dan instalasi kelistrikan yang aman. Sirkulasi air yang baik menjaga kualitas air kolam dan kesehatan ikan agar tetap hidup dan berkembang.',
                'target'    => 4_500_000,
                'status'    => 'aktif',
                'deadline'  => '2026-11-30',
                'foto_url'  => 'https://images.pexels.com/photos/37252656/pexels-photo-37252656.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
            ],
            [
                'judul'     => 'Urban Farming',
                'deskripsi' => 'Program ketahanan pangan lingkungan melalui budidaya tanaman di lahan terbatas. Meliputi pengadaan rak tanaman vertikal, pot/polybag, media tanam organik, benih sayuran (kangkung, bayam, cabai, tomat), dan pupuk kompos. Hasil panen dibagikan kepada warga atau dijual untuk menambah kas RT.',
                'target'    => 7_000_000,
                'status'    => 'aktif',
                'deadline'  => '2026-09-30',
                'foto_url'  => 'https://images.pexels.com/photos/28129609/pexels-photo-28129609/free-photo-of-vertical-hydroponic-farm-with-led-lighting.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
            ],
            [
                'judul'     => 'Kaos Warga',
                'deskripsi' => 'Pengadaan seragam kaos identitas warga RT 10 Golden Park 2 sebagai simbol kebersamaan dan kebanggaan lingkungan. Kaos cotton combed 30s dengan desain khas RT 10, sablon berkualitas, tersedia dalam berbagai ukuran (S–XXL). Estimasi 200 pcs untuk seluruh kepala keluarga dan anggota dewasa.',
                'target'    => 16_000_000,
                'status'    => 'aktif',
                'deadline'  => '2026-08-17',
                'foto_url'  => 'https://images.pexels.com/photos/15738664/pexels-photo-15738664/free-photo-of-photo-of-people-in-studio.png?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
            ],
        ];

        foreach ($campaigns as $data) {
            Kampanye::create([
                ...$data,
                'terkumpul'  => 0,
                'created_by' => $admin->id,
            ]);
        }
    }
}
