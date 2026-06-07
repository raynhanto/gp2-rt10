<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Helpers\Upload;
use App\Http\Controllers\Controller;
use App\Models\IuranBayar;
use App\Models\IuranTagihan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IuranWargaController extends Controller
{
    private function checkWhitelist(): bool
    {
        $raw = config('app.iuran_whitelist', '');
        if (empty($raw)) return false;
        $list = array_map('trim', explode(',', $raw));
        return in_array(Auth::user()?->email, $list, true);
    }

    // GET /api/iuran/warga/tagihan?tahun=2025
    public function tagihanIndex(Request $request): JsonResponse
    {
        if (!$this->checkWhitelist()) {
            return response()->json(['success' => false, 'message' => 'Fitur ini belum tersedia.'], 403);
        }

        $user = Auth::user();
        $tahun = (int) ($request->query('tahun') ?? date('Y'));

        // Get all unit_rumah owned or used by this user
        $unitIds = DB::table('unit_rumah')
            ->where('user_id', $user->id)
            ->pluck('id')
            ->toArray();

        if (empty($unitIds)) {
            return response()->json([
                'success' => true,
                'data'    => [],
                'tahun'   => $tahun,
                'unit_ids' => [],
            ]);
        }

        $tagihan = DB::table('iuran_tagihan as t')
            ->join('iuran_periode as p', 'p.id', '=', 't.iuran_periode_id')
            ->join('unit_rumah as u', 'u.id', '=', 't.unit_rumah_id')
            ->whereIn('t.unit_rumah_id', $unitIds)
            ->where('p.tahun', $tahun)
            ->orderBy('p.bulan')
            ->orderBy('u.blok')
            ->orderBy('u.nomor')
            ->select('t.id', 'u.blok', 'u.nomor', 'p.bulan', 'p.tahun', 'p.nominal', 'p.jatuh_tempo', 't.status')
            ->get();

        // Attach active bayar (pending/verified) for each tagihan
        $tagihanIds = $tagihan->pluck('id')->toArray();
        $bayarMap   = [];
        if (!empty($tagihanIds)) {
            $bayarRows = DB::table('iuran_bayar')
                ->whereIn('iuran_tagihan_id', $tagihanIds)
                ->whereIn('status', ['pending', 'verified'])
                ->orderBy('id')
                ->get();
            foreach ($bayarRows as $b) {
                if (!isset($bayarMap[$b->iuran_tagihan_id])) {
                    $bayarMap[$b->iuran_tagihan_id] = $b;
                }
            }
        }

        $result = $tagihan->map(function ($t) use ($bayarMap) {
            $b = $bayarMap[$t->id] ?? null;
            return array_merge((array) $t, [
                'bayar_id'      => $b?->id,
                'metode'        => $b?->metode,
                'bukti_url'     => $b?->bukti_url,
                'bayar_status'  => $b?->status,
                'bayar_nominal' => $b?->nominal,
                'bayar_at'      => $b?->created_at,
            ]);
        });

        $availableYears = DB::table('iuran_periode')
            ->selectRaw('DISTINCT tahun')
            ->orderByDesc('tahun')
            ->pluck('tahun')
            ->toArray();

        return response()->json([
            'success'         => true,
            'data'            => $result,
            'tahun'           => $tahun,
            'available_years' => $availableYears,
        ]);
    }

    // POST /api/iuran/warga/bayar (multipart/form-data)
    public function bayar(Request $request): JsonResponse
    {
        if (!$this->checkWhitelist()) {
            return response()->json(['success' => false, 'message' => 'Fitur ini belum tersedia.'], 403);
        }

        $user = Auth::user();
        $tagihanId = (int) ($request->input('iuran_tagihan_id') ?? 0);

        $tagihan = IuranTagihan::with('unitRumah')->find($tagihanId);
        if (!$tagihan) {
            return response()->json(['success' => false, 'message' => 'Tagihan tidak ditemukan.'], 404);
        }

        // Make sure this tagihan belongs to the user's unit
        $userUnitIds = DB::table('unit_rumah')->where('user_id', $user->id)->pluck('id')->toArray();
        if (!in_array($tagihan->unit_rumah_id, $userUnitIds)) {
            return response()->json(['success' => false, 'message' => 'Tagihan bukan milik Anda.'], 403);
        }

        if ($tagihan->status === 'lunas') {
            return response()->json(['success' => false, 'message' => 'Tagihan sudah lunas.'], 422);
        }
        if ($tagihan->status === 'pending') {
            return response()->json(['success' => false, 'message' => 'Tagihan sudah dalam proses verifikasi.'], 422);
        }

        $metode = $request->input('metode', 'transfer');
        $catatan = $request->input('catatan');
        $buktiUrl = null;

        if ($request->hasFile('bukti')) {
            try {
                $buktiUrl = Upload::save($request->file('bukti'), 'bukti_iuran');
            } catch (\RuntimeException $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
        }

        $periodeNominal = DB::table('iuran_periode')
            ->where('id', $tagihan->iuran_periode_id)
            ->value('nominal');

        $bayar = IuranBayar::create([
            'iuran_tagihan_id' => $tagihan->id,
            'nominal'          => (int) ($request->input('nominal') ?: $periodeNominal),
            'metode'           => $metode,
            'catatan'          => $catatan,
            'bukti_url'        => $buktiUrl,
            'status'           => 'pending',
            'created_by'       => $user->id,
        ]);

        $tagihan->update(['status' => 'pending']);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dikirim, menunggu verifikasi admin.',
            'data'    => $bayar,
        ], 201);
    }

    // POST /api/iuran/warga/bayar/{id}/bukti
    public function uploadBukti(Request $request, int $id): JsonResponse
    {
        if (!$this->checkWhitelist()) {
            return response()->json(['success' => false, 'message' => 'Fitur ini belum tersedia.'], 403);
        }

        $user  = Auth::user();
        $bayar = IuranBayar::with('tagihan.unitRumah')->find($id);
        if (!$bayar) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
        }

        // Ownership check
        $userUnitIds = DB::table('unit_rumah')->where('user_id', $user->id)->pluck('id')->toArray();
        if (!in_array($bayar->tagihan->unit_rumah_id, $userUnitIds)) {
            return response()->json(['success' => false, 'message' => 'Bukan milik Anda.'], 403);
        }

        if ($bayar->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Tidak bisa mengubah bukti pembayaran yang sudah diproses.'], 422);
        }

        if (!$request->hasFile('bukti')) {
            return response()->json(['success' => false, 'message' => 'File bukti tidak ditemukan.'], 422);
        }

        try {
            $buktiUrl = Upload::save($request->file('bukti'), 'bukti_iuran');
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        $bayar->update(['bukti_url' => $buktiUrl]);

        return response()->json(['success' => true, 'message' => 'Bukti berhasil diunggah.', 'bukti_url' => $buktiUrl]);
    }
}
