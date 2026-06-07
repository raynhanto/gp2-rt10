<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IuranBayar;
use App\Models\IuranPeriode;
use App\Models\IuranTagihan;
use App\Models\Kas;
use App\Models\UnitRumah;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IuranController extends Controller
{
    use LogsAdminActivity;
    // ── Periode ──────────────────────────────────────────────────

    public function periodeIndex(): JsonResponse
    {
        $list = IuranPeriode::orderByDesc('tahun')->orderByDesc('bulan')->get()
            ->map(function ($p) {
                $p->nama_bulan   = $p->nama_bulan;
                $p->total_unit   = IuranTagihan::where('iuran_periode_id', $p->id)->count();
                $p->lunas_count  = IuranTagihan::where('iuran_periode_id', $p->id)->where('status', 'lunas')->count();
                $p->belum_count  = IuranTagihan::where('iuran_periode_id', $p->id)->where('status', 'belum')->count();
                $p->pending_count = IuranTagihan::where('iuran_periode_id', $p->id)->where('status', 'pending')->count();
                return $p;
            });
        return response()->json(['success' => true, 'data' => $list]);
    }

    public function periodeStore(Request $request): JsonResponse
    {
        $body   = $request->json()->all();
        $errors = [];
        $bulan  = (int) ($body['bulan'] ?? 0);
        $tahun  = (int) ($body['tahun'] ?? 0);
        if ($bulan < 1 || $bulan > 12) $errors['bulan'] = 'Bulan tidak valid.';
        if ($tahun < 2020)             $errors['tahun'] = 'Tahun tidak valid.';
        if ((int) ($body['nominal'] ?? 0) < 1) $errors['nominal'] = 'Nominal tidak valid.';
        if (empty($body['jatuh_tempo'])) $errors['jatuh_tempo'] = 'Jatuh tempo wajib diisi.';
        if ($errors) return response()->json(['success' => false, 'message' => 'Data tidak valid.', 'errors' => $errors], 422);

        if (IuranPeriode::where('bulan', $bulan)->where('tahun', $tahun)->exists()) {
            return response()->json(['success' => false, 'message' => "Periode {$bulan}/{$tahun} sudah ada."], 422);
        }

        $periode = IuranPeriode::create([
            'bulan'        => $bulan,
            'tahun'        => $tahun,
            'nominal'      => (int) $body['nominal'],
            'jatuh_tempo'  => $body['jatuh_tempo'],
            'keterangan'   => $body['keterangan'] ?? null,
            'created_by'   => Auth::id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Periode iuran berhasil dibuat.', 'data' => $periode], 201);
    }

    public function periodeDelete(int $id): JsonResponse
    {
        $periode = IuranPeriode::find($id);
        if (!$periode) return response()->json(['success' => false, 'message' => 'Periode tidak ditemukan.'], 404);

        if (IuranTagihan::where('iuran_periode_id', $id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Tidak bisa dihapus, sudah ada tagihan.'], 422);
        }

        $periode->delete();
        return response()->json(['success' => true, 'message' => 'Periode berhasil dihapus.']);
    }

    // ── Tagihan ───────────────────────────────────────────────────

    public function tagihanGenerate(int $periodeId): JsonResponse
    {
        $periode = IuranPeriode::find($periodeId);
        if (!$periode) return response()->json(['success' => false, 'message' => 'Periode tidak ditemukan.'], 404);

        $units   = UnitRumah::all();
        $created = 0;

        foreach ($units as $unit) {
            $exists = IuranTagihan::where('unit_rumah_id', $unit->id)
                ->where('iuran_periode_id', $periodeId)
                ->exists();
            if (!$exists) {
                IuranTagihan::create([
                    'unit_rumah_id'    => $unit->id,
                    'iuran_periode_id' => $periodeId,
                    'status'           => 'belum',
                ]);
                $created++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "{$created} tagihan berhasil digenerate.",
            'created' => $created,
        ]);
    }

    public function tagihanIndex(Request $request): JsonResponse
    {
        $q = DB::table('iuran_tagihan as t')
            ->join('iuran_periode as p', 'p.id', '=', 't.iuran_periode_id')
            ->join('unit_rumah as u', 'u.id', '=', 't.unit_rumah_id')
            ->leftJoin('users as usr', function ($j) {
                $j->on('usr.id', '=', DB::raw(
                    '(SELECT created_by FROM iuran_bayar WHERE iuran_tagihan_id = t.id AND status = "verified" LIMIT 1)'
                ));
            })
            ->select(
                't.id', 'u.blok', 'u.nomor', 'p.bulan', 'p.tahun', 'p.nominal',
                'p.jatuh_tempo', 't.status', 't.created_at'
            )
            ->orderBy('p.tahun')->orderBy('p.bulan')->orderBy('u.blok')->orderBy('u.nomor');

        if ($request->query('periode_id')) $q->where('t.iuran_periode_id', $request->query('periode_id'));
        if ($request->query('status'))     $q->where('t.status', $request->query('status'));
        if ($request->query('blok'))       $q->where('u.blok', $request->query('blok'));

        $page    = max(1, (int) ($request->query('page', 1)));
        $perPage = 50;
        $total   = (clone $q)->count();
        $data    = $q->offset(($page - 1) * $perPage)->limit($perPage)->get();

        return response()->json([
            'success' => true,
            'data'    => $data,
            'meta'    => ['total' => $total, 'page' => $page, 'per_page' => $perPage],
        ]);
    }

    public function tagihanDispensasi(Request $request, int $id): JsonResponse
    {
        $tagihan = IuranTagihan::find($id);
        if (!$tagihan) return response()->json(['success' => false, 'message' => 'Tagihan tidak ditemukan.'], 404);

        $tagihan->update(['status' => 'dispensasi']);
        return response()->json(['success' => true, 'message' => 'Status diubah ke dispensasi.']);
    }

    // ── Bayar ─────────────────────────────────────────────────────

    public function bayarStore(Request $request): JsonResponse
    {
        $body = $request->json()->all();

        $tagihan = IuranTagihan::find($body['iuran_tagihan_id'] ?? 0);
        if (!$tagihan) return response()->json(['success' => false, 'message' => 'Tagihan tidak ditemukan.'], 404);
        if ($tagihan->status === 'lunas') {
            return response()->json(['success' => false, 'message' => 'Tagihan sudah lunas.'], 422);
        }

        $bayar = IuranBayar::create([
            'iuran_tagihan_id' => $tagihan->id,
            'nominal'          => (int) ($body['nominal'] ?? $tagihan->periode->nominal),
            'metode'           => $body['metode'] ?? 'tunai',
            'catatan'          => $body['catatan'] ?? null,
            'status'           => 'pending',
            'created_by'       => Auth::id(),
        ]);

        $tagihan->update(['status' => 'pending']);

        return response()->json(['success' => true, 'message' => 'Pembayaran berhasil dicatat.', 'data' => $bayar], 201);
    }

    public function bayarVerify(Request $request, int $id): JsonResponse
    {
        $bayar = IuranBayar::with('tagihan.periode')->find($id);
        if (!$bayar) return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);

        $body   = $request->json()->all();
        $action = $body['action'] ?? 'verify';

        if ($action === 'reject') {
            $bayar->update(['status' => 'rejected']);
            $bayar->tagihan->update(['status' => 'belum']);
            return response()->json(['success' => true, 'message' => 'Pembayaran ditolak.']);
        }

        DB::transaction(function () use ($bayar) {
            $bayar->update([
                'status'      => 'verified',
                'verified_by' => Auth::id(),
                'verified_at' => now(),
            ]);

            $bayar->tagihan->update(['status' => 'lunas']);

            $periode = $bayar->tagihan->periode;
            $unit    = $bayar->tagihan->unitRumah;
            $label   = "Iuran {$periode->nama_bulan} - Unit {$unit->blok}{$unit->nomor}";

            Kas::create([
                'tanggal'    => now()->toDateString(),
                'jenis'      => 'masuk',
                'nominal'    => $bayar->nominal,
                'keterangan' => $label,
                'created_by' => Auth::id(),
            ]);
        });

        $this->logActivity('verify_iuran', "Verifikasi iuran bayar #{$id}", IuranBayar::class, $id, ['nominal' => $bayar->nominal]);

        return response()->json(['success' => true, 'message' => 'Pembayaran terverifikasi.']);
    }

    public function bayarIndex(Request $request): JsonResponse
    {
        $q = DB::table('iuran_bayar as b')
            ->join('iuran_tagihan as t', 't.id', '=', 'b.iuran_tagihan_id')
            ->join('iuran_periode as p', 'p.id', '=', 't.iuran_periode_id')
            ->join('unit_rumah as u', 'u.id', '=', 't.unit_rumah_id')
            ->leftJoin('users as usr', 'usr.id', '=', 'b.created_by')
            ->select('b.id', 'b.iuran_tagihan_id', 'b.nominal', 'b.metode', 'b.catatan', 'b.bukti_url',
                     'b.status', 'b.verified_by', 'b.verified_at', 'b.created_by', 'b.created_at', 'b.updated_at',
                     'u.blok', 'u.nomor', 'p.bulan', 'p.tahun', 'usr.nama as bayar_oleh')
            ->orderByDesc('b.created_at');

        if ($request->query('status'))     $q->where('b.status', $request->query('status'));
        if ($request->query('periode_id')) $q->where('t.iuran_periode_id', $request->query('periode_id'));

        return response()->json(['success' => true, 'data' => $q->limit(200)->get()]);
    }

    // ── Matrix ────────────────────────────────────────────────────

    public function matrix(Request $request): JsonResponse
    {
        $tahun = (int) ($request->query('tahun') ?? date('Y'));

        $tagihan = DB::table('iuran_tagihan as t')
            ->join('iuran_periode as p', 'p.id', '=', 't.iuran_periode_id')
            ->join('unit_rumah as u', 'u.id', '=', 't.unit_rumah_id')
            ->leftJoin('users as usr', function ($j) {
                $j->on('usr.id', '=', DB::raw(
                    '(SELECT created_by FROM iuran_bayar WHERE iuran_tagihan_id = t.id AND status = "verified" LIMIT 1)'
                ));
            })
            ->select('u.blok', 'u.nomor', 'p.bulan', 'p.nominal', 't.status', 't.id as tagihan_id')
            ->where('p.tahun', $tahun)
            ->orderBy('u.blok')->orderBy('u.nomor')->orderBy('p.bulan')
            ->get();

        $matrix = [];
        $bulanData = [];

        foreach ($tagihan as $t) {
            $key = $t->blok . str_pad((string) $t->nomor, 2, '0', STR_PAD_LEFT);
            if (!isset($matrix[$key])) {
                $matrix[$key] = ['blok' => $t->blok, 'nomor' => $t->nomor, 'bulan' => []];
            }
            $matrix[$key]['bulan'][$t->bulan] = [
                'status'     => $t->status,
                'tagihan_id' => $t->tagihan_id,
                'nominal'    => $t->nominal,
            ];
            $bulanData[$t->bulan] = true;
        }

        ksort($matrix);
        ksort($bulanData);

        $periodes = IuranPeriode::where('tahun', $tahun)
            ->orderBy('bulan')
            ->get(['bulan', 'nominal', 'jatuh_tempo'])
            ->keyBy('bulan');

        return response()->json([
            'success'  => true,
            'data'     => array_values($matrix),
            'bulan'    => array_keys($bulanData),
            'periodes' => $periodes,
            'tahun'    => $tahun,
        ]);
    }
}
