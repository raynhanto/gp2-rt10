<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Helpers\Upload;
use App\Http\Controllers\Controller;
use App\Models\Donasi;
use App\Models\Kampanye;
use App\Models\Kas;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DonasiController extends Controller
{
    use LogsAdminActivity;
    public function index(Request $request): JsonResponse
    {
        $kampanyeId = $request->query('kampanye_id');

        if (Auth::check() && Auth::user()->isAdmin()) {
            $q = Donasi::with(['user:id,nama,email', 'kampanye:id,judul'])
                ->orderByDesc('created_at');
            if ($request->query('status'))  $q->where('status', $request->query('status'));
            if ($kampanyeId)                $q->where('kampanye_id', $kampanyeId);
            $data = $q->get()->map(fn($d) => array_merge($d->toArray(), [
                'nama'  => $d->user?->nama,
                'email' => $d->user?->email,
                'judul' => $d->kampanye?->judul ?? 'Donasi Umum',
            ]));
            return response()->json(['success' => true, 'data' => $data]);
        }

        if (Auth::check()) {
            $q = Donasi::with('kampanye:id,judul')
                ->where('user_id', Auth::id())
                ->orderByDesc('created_at');
            if ($kampanyeId) $q->where('kampanye_id', $kampanyeId);
            return response()->json(['success' => true, 'data' => $q->get()]);
        }

        // Public: verified only, anonym masked
        $q = DB::table('donasi as d')
            ->leftJoin('users as u', 'u.id', '=', 'd.user_id')
            ->leftJoin('kampanye as k', 'k.id', '=', 'd.kampanye_id')
            ->select(
                'd.id', 'd.kampanye_id', 'd.nominal', 'd.is_anonym', 'd.created_at',
                DB::raw('IF(d.is_anonym=1,"Donatur Anonim",u.nama) as nama'),
                DB::raw('COALESCE(k.judul,"Donasi Umum") as judul')
            )
            ->where('d.status', 'verified')
            ->orderByDesc('d.created_at')
            ->limit(100);
        if ($kampanyeId) $q->where('d.kampanye_id', $kampanyeId);

        return response()->json(['success' => true, 'data' => $q->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $body       = $request->json()->all();
        $nominal    = (int) ($body['nominal'] ?? 0);
        $kampanyeId = isset($body['kampanye_id']) && $body['kampanye_id'] !== null
            ? (int) $body['kampanye_id']
            : null;

        if ($nominal < 1000) return response()->json(['success' => false, 'message' => 'Nominal minimal Rp 1.000.'], 422);
        if ($kampanyeId !== null) {
            Kampanye::where('id', $kampanyeId)->where('status', '!=', 'arsip')->firstOrFail();
        }

        $donasi = Donasi::create([
            'user_id'     => Auth::id(),
            'kampanye_id' => $kampanyeId,
            'nominal'     => $nominal,
            'metode'      => $body['metode'] ?? 'qris',
            'is_anonym'   => !empty($body['is_anonym']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Donasi tercatat. Silakan upload bukti pembayaran.',
            'data'    => $donasi->load('kampanye:id,judul'),
        ], 201);
    }

    public function verify(Request $request, int $id): JsonResponse
    {
        $donasi = Donasi::findOrFail($id);
        if ($donasi->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Donasi sudah diproses.'], 422);
        }

        $body   = $request->json()->all();
        $action = $body['action'] ?? '';
        if (!in_array($action, ['verify', 'reject'])) {
            return response()->json(['success' => false, 'message' => 'Action tidak valid.'], 422);
        }

        $newStatus = $action === 'verify' ? 'verified' : 'rejected';
        $donasi->update([
            'status'        => $newStatus,
            'catatan_admin' => $body['catatan'] ?? null,
            'verified_by'   => Auth::id(),
            'verified_at'   => now(),
        ]);

        if ($newStatus === 'verified') {
            $donasi->kampanye?->refreshTerkumpul();
            Kas::create([
                'jenis'       => 'masuk',
                'nominal'     => $donasi->nominal,
                'keterangan'  => "Donasi terverifikasi #{$id}",
                'kampanye_id' => $donasi->kampanye_id,
                'donasi_id'   => $id,
                'created_by'  => Auth::id(),
            ]);
        }

        $msg = $newStatus === 'verified' ? 'Donasi berhasil diverifikasi.' : 'Donasi ditolak.';

        $logAction = $newStatus === 'verified' ? 'verify_donasi' : 'reject_donasi';
        $this->logActivity(
            $logAction,
            "{$logAction} #{$id} Rp " . number_format($donasi->nominal, 0, ',', '.'),
            Donasi::class,
            $id,
            ['nominal' => $donasi->nominal, 'catatan' => $body['catatan'] ?? null]
        );

        return response()->json(['success' => true, 'message' => $msg]);
    }

    public function updateMetode(Request $request, int $id): JsonResponse
    {
        $donasi = Donasi::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        if ($donasi->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Donasi sudah diproses.'], 422);
        }
        $metode = $request->json('metode');
        if (!in_array($metode, ['qris', 'transfer', 'gopay'], true)) {
            return response()->json(['success' => false, 'message' => 'Metode tidak valid.'], 422);
        }
        $donasi->update(['metode' => $metode]);
        return response()->json(['success' => true]);
    }

    public function uploadBukti(Request $request, int $id): JsonResponse
    {
        $donasi = Donasi::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        if (!$request->hasFile('bukti')) {
            return response()->json(['success' => false, 'message' => 'File bukti wajib diupload.'], 422);
        }

        try {
            $url = Upload::save($request->file('bukti'), 'bukti');
            $donasi->update(['bukti_url' => $url]);
            return response()->json(['success' => true, 'message' => 'Bukti berhasil diupload.', 'data' => ['bukti_url' => $url]]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
