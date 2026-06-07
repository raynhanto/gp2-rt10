<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ExcelExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanKeuanganController extends Controller
{
    public function arusKas(Request $request): JsonResponse
    {
        $dari   = $request->query('dari');
        $sampai = $request->query('sampai');
        $tahun  = (int) ($request->query('tahun') ?? date('Y'));

        $q = DB::table('kas')
            ->whereNull('deleted_at')
            ->selectRaw("
                YEAR(COALESCE(tanggal, DATE(created_at))) as tahun,
                MONTH(COALESCE(tanggal, DATE(created_at))) as bulan,
                SUM(CASE WHEN jenis='masuk' THEN nominal ELSE 0 END) as masuk,
                SUM(CASE WHEN jenis='keluar' THEN nominal ELSE 0 END) as keluar,
                COUNT(*) as transaksi
            ")
            ->groupByRaw("YEAR(COALESCE(tanggal, DATE(created_at))), MONTH(COALESCE(tanggal, DATE(created_at)))")
            ->orderByRaw("tahun, bulan");

        if ($dari)   $q->where(DB::raw('COALESCE(tanggal, DATE(created_at))'), '>=', $dari);
        if ($sampai) $q->where(DB::raw('COALESCE(tanggal, DATE(created_at))'), '<=', $sampai);
        if (!$dari && !$sampai) $q->whereYear(DB::raw('COALESCE(tanggal, DATE(created_at))'), $tahun);

        $rows    = $q->get();
        $saldo   = 0;
        $result  = $rows->map(function ($r) use (&$saldo) {
            $saldo     += $r->masuk - $r->keluar;
            $r->saldo   = $saldo;
            $r->saldo_running = $saldo;
            return $r;
        });

        $totals = DB::table('kas')
            ->whereNull('deleted_at')
            ->selectRaw("
                SUM(CASE WHEN jenis='masuk' THEN nominal ELSE 0 END) as total_masuk,
                SUM(CASE WHEN jenis='keluar' THEN nominal ELSE 0 END) as total_keluar
            ")
            ->first();

        return response()->json([
            'success' => true,
            'data'    => $result,
            'totals'  => $totals,
        ]);
    }

    public function pengeluaranReport(Request $request): JsonResponse
    {
        $tahun      = (int) ($request->query('tahun') ?? date('Y'));
        $kategoriId = $request->query('kategori_id');

        $byKategori = DB::table('pengeluaran as p')
            ->leftJoin('kategori_keuangan as k', 'k.id', '=', 'p.kategori_id')
            ->select(
                DB::raw('COALESCE(k.nama, "Lainnya") as kategori'),
                DB::raw('COALESCE(k.warna, "#6B6050") as warna'),
                DB::raw('SUM(p.nominal) as total'),
                DB::raw('COUNT(*) as jumlah')
            )
            ->whereYear('p.tanggal', $tahun)
            ->when($kategoriId, fn($q) => $q->where('p.kategori_id', $kategoriId))
            ->groupBy('p.kategori_id', 'k.nama', 'k.warna')
            ->orderByDesc('total')
            ->get();

        $byBulan = DB::table('pengeluaran')
            ->selectRaw('MONTH(tanggal) as bulan, SUM(nominal) as total')
            ->whereYear('tanggal', $tahun)
            ->when($kategoriId, fn($q) => $q->where('kategori_id', $kategoriId))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->keyBy('bulan');

        $detail = DB::table('pengeluaran as p')
            ->leftJoin('kategori_keuangan as k', 'k.id', '=', 'p.kategori_id')
            ->leftJoin('kampanye as ka', 'ka.id', '=', 'p.kampanye_id')
            ->leftJoin('users as u', 'u.id', '=', 'p.created_by')
            ->select('p.*', 'k.nama as kategori', 'k.warna', 'ka.judul', 'u.nama as oleh')
            ->whereYear('p.tanggal', $tahun)
            ->when($kategoriId, fn($q) => $q->where('p.kategori_id', $kategoriId))
            ->orderByDesc('p.tanggal')
            ->limit(100)
            ->get();

        return response()->json([
            'success'     => true,
            'by_kategori' => $byKategori,
            'by_bulan'    => $byBulan,
            'detail'      => $detail,
        ]);
    }

    public function iuranReport(Request $request): JsonResponse
    {
        $tahun = (int) ($request->query('tahun') ?? date('Y'));

        $summary = DB::table('iuran_tagihan as t')
            ->join('iuran_periode as p', 'p.id', '=', 't.iuran_periode_id')
            ->select(
                'p.bulan',
                DB::raw('p.nominal'),
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN t.status='lunas' THEN 1 ELSE 0 END) as lunas"),
                DB::raw("SUM(CASE WHEN t.status='pending' THEN 1 ELSE 0 END) as pending"),
                DB::raw("SUM(CASE WHEN t.status='belum' THEN 1 ELSE 0 END) as belum"),
                DB::raw("SUM(CASE WHEN t.status='dispensasi' THEN 1 ELSE 0 END) as dispensasi"),
                DB::raw("SUM(CASE WHEN t.status='lunas' THEN p.nominal ELSE 0 END) as terkumpul")
            )
            ->where('p.tahun', $tahun)
            ->groupBy('p.bulan', 'p.nominal')
            ->orderBy('p.bulan')
            ->get();

        $totalTagihan = $summary->sum('total');
        $totalLunas   = $summary->sum('lunas');
        $totalTerkumpul = $summary->sum('terkumpul');

        return response()->json([
            'success'         => true,
            'data'            => $summary,
            'total_tagihan'   => $totalTagihan,
            'total_lunas'     => $totalLunas,
            'compliance_pct'  => $totalTagihan > 0 ? round($totalLunas / $totalTagihan * 100, 1) : 0,
            'total_terkumpul' => $totalTerkumpul,
        ]);
    }

    public function neraca(Request $request): JsonResponse
    {
        $per = $request->query('per') ?? now()->toDateString();

        $kas = DB::table('kas')
            ->whereNull('deleted_at')
            ->where(DB::raw('COALESCE(tanggal, DATE(created_at))'), '<=', $per)
            ->selectRaw("
                SUM(CASE WHEN jenis='masuk' THEN nominal ELSE 0 END) as total_masuk,
                SUM(CASE WHEN jenis='keluar' THEN nominal ELSE 0 END) as total_keluar,
                SUM(CASE WHEN jenis='masuk' THEN nominal ELSE -nominal END) as saldo
            ")
            ->first();

        $iuranTertunggak = DB::table('iuran_tagihan as t')
            ->join('iuran_periode as p', 'p.id', '=', 't.iuran_periode_id')
            ->where('t.status', 'belum')
            ->sum('p.nominal');

        $kampanye = DB::table('kampanye')
            ->selectRaw('SUM(target - terkumpul) as kekurangan')
            ->where('status', '!=', 'arsip')
            ->first();

        return response()->json([
            'success'           => true,
            'per_tanggal'       => $per,
            'kas'               => $kas,
            'iuran_tertunggak'  => $iuranTertunggak,
            'kekurangan_target' => $kampanye->kekurangan ?? 0,
        ]);
    }

    public function exportKas(Request $request): StreamedResponse
    {
        return app(ExcelExportService::class)->exportKas(
            $request->query('dari'),
            $request->query('sampai')
        );
    }

    public function exportIuran(Request $request): StreamedResponse
    {
        $tahun = (int) ($request->query('tahun') ?? date('Y'));
        return app(ExcelExportService::class)->exportIuran($tahun);
    }

    public function exportPengeluaran(Request $request): StreamedResponse
    {
        return app(ExcelExportService::class)->exportPengeluaran(
            $request->query('tahun') ? (int) $request->query('tahun') : null,
            $request->query('kategori_id') ? (int) $request->query('kategori_id') : null
        );
    }
}
