<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatistikController extends Controller
{
    public function bulanan(Request $request): JsonResponse
    {
        $tahun = (int) ($request->query('tahun') ?? date('Y'));

        $rows = DB::table('kas')
            ->whereNull('deleted_at')
            ->whereYear(DB::raw('COALESCE(tanggal, DATE(created_at))'), $tahun)
            ->selectRaw("
                MONTH(COALESCE(tanggal, DATE(created_at))) as bulan,
                SUM(CASE WHEN jenis='masuk' THEN nominal ELSE 0 END) as masuk,
                SUM(CASE WHEN jenis='keluar' THEN nominal ELSE 0 END) as keluar
            ")
            ->groupByRaw('MONTH(COALESCE(tanggal, DATE(created_at)))')
            ->orderBy('bulan')
            ->get()
            ->keyBy('bulan');

        $bulanLabel = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
                       'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $result = [];
        for ($b = 1; $b <= 12; $b++) {
            $result[] = [
                'bulan'  => $b,
                'label'  => $bulanLabel[$b],
                'masuk'  => $rows[$b]->masuk  ?? 0,
                'keluar' => $rows[$b]->keluar ?? 0,
            ];
        }

        return response()->json(['success' => true, 'data' => $result, 'tahun' => $tahun]);
    }

    public function kategori(Request $request): JsonResponse
    {
        $dari   = $request->query('dari')   ?? date('Y-01-01');
        $sampai = $request->query('sampai') ?? date('Y-m-d');

        $data = DB::table('pengeluaran as p')
            ->leftJoin('kategori_keuangan as k', 'k.id', '=', 'p.kategori_id')
            ->select(
                DB::raw('COALESCE(k.nama, "Lainnya") as kategori'),
                DB::raw('COALESCE(k.warna, "#9D9080") as warna'),
                DB::raw('SUM(p.nominal) as total')
            )
            ->whereBetween('p.tanggal', [$dari, $sampai])
            ->groupBy('p.kategori_id', 'k.nama', 'k.warna')
            ->orderByDesc('total')
            ->get();

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function iuranCompliance(Request $request): JsonResponse
    {
        $tahun = (int) ($request->query('tahun') ?? date('Y'));

        $data = DB::table('iuran_tagihan as t')
            ->join('iuran_periode as p', 'p.id', '=', 't.iuran_periode_id')
            ->select(
                'p.bulan',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN t.status='lunas' THEN 1 ELSE 0 END) as lunas")
            )
            ->where('p.tahun', $tahun)
            ->groupBy('p.bulan')
            ->orderBy('p.bulan')
            ->get()
            ->map(function ($r) {
                $r->pct = $r->total > 0 ? round($r->lunas / $r->total * 100, 1) : 0;
                return $r;
            });

        return response()->json(['success' => true, 'data' => $data, 'tahun' => $tahun]);
    }

    public function saldoTrend(Request $request): JsonResponse
    {
        $bulan = max(3, min(24, (int) ($request->query('bulan') ?? 12)));

        $rows = DB::table('kas')
            ->whereNull('deleted_at')
            ->where(DB::raw('COALESCE(tanggal, DATE(created_at))'), '>=',
                    DB::raw("DATE_SUB(CURDATE(), INTERVAL {$bulan} MONTH)"))
            ->selectRaw("
                DATE_FORMAT(COALESCE(tanggal, DATE(created_at)), '%Y-%m') as periode,
                SUM(CASE WHEN jenis='masuk' THEN nominal ELSE -nominal END) as net
            ")
            ->groupBy('periode')
            ->orderBy('periode')
            ->get();

        $saldo  = 0;
        $result = $rows->map(function ($r) use (&$saldo) {
            $saldo      += $r->net;
            $r->saldo    = $saldo;
            return $r;
        });

        return response()->json(['success' => true, 'data' => $result]);
    }

    public function dashboard(): JsonResponse
    {
        $tahun = (int) date('Y');
        $bulan = (int) date('n');

        $kas = DB::table('kas')
            ->whereNull('deleted_at')
            ->selectRaw("
                SUM(CASE WHEN jenis='masuk' THEN nominal ELSE 0 END) as total_masuk,
                SUM(CASE WHEN jenis='keluar' THEN nominal ELSE 0 END) as total_keluar,
                SUM(CASE WHEN jenis='masuk' THEN nominal ELSE -nominal END) as saldo,
                SUM(CASE WHEN jenis='masuk' AND YEAR(COALESCE(tanggal,DATE(created_at)))=? AND MONTH(COALESCE(tanggal,DATE(created_at)))=? THEN nominal ELSE 0 END) as masuk_bulan,
                SUM(CASE WHEN jenis='keluar' AND YEAR(COALESCE(tanggal,DATE(created_at)))=? AND MONTH(COALESCE(tanggal,DATE(created_at)))=? THEN nominal ELSE 0 END) as keluar_bulan
            ", [$tahun, $bulan, $tahun, $bulan])
            ->first();

        $iuran = DB::table('iuran_tagihan as t')
            ->join('iuran_periode as p', 'p.id', '=', 't.iuran_periode_id')
            ->where('p.tahun', $tahun)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN t.status='lunas' THEN 1 ELSE 0 END) as lunas
            ")
            ->first();

        $donasi = DB::table('donasi')
            ->where('status', 'verified')
            ->selectRaw('COUNT(*) as count, SUM(nominal) as total')
            ->first();

        $iuranPct = ($iuran->total ?? 0) > 0
            ? round(($iuran->lunas ?? 0) / $iuran->total * 100, 1)
            : 0;

        return response()->json([
            'success' => true,
            'data'    => [
                'saldo'             => (int) ($kas->saldo ?? 0),
                'masuk_ytd'         => (int) ($kas->total_masuk ?? 0),
                'keluar_ytd'        => (int) ($kas->total_keluar ?? 0),
                'masuk_bulan'       => (int) ($kas->masuk_bulan ?? 0),
                'keluar_bulan'      => (int) ($kas->keluar_bulan ?? 0),
                'iuran_compliance'  => $iuranPct,
                'iuran_lunas'       => (int) ($iuran->lunas ?? 0),
                'iuran_total'       => (int) ($iuran->total ?? 0),
                'donasi_count'      => (int) ($donasi->count ?? 0),
                'donasi_total'      => (int) ($donasi->total ?? 0),
            ],
        ]);
    }
}
