<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kas;
use App\Models\TransaksiInstan;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransaksiInstanController extends Controller
{
    use LogsAdminActivity;

    public function index(Request $request): JsonResponse
    {
        $q = DB::table('transaksi_instan as ti')
            ->leftJoin('users as u', 'u.id', '=', 'ti.created_by')
            ->select('ti.*', 'u.nama as dibuat_oleh')
            ->orderByDesc('ti.tanggal')
            ->orderByDesc('ti.id');

        if ($request->query('search')) {
            $search = '%' . $request->query('search') . '%';
            $q->where(function ($w) use ($search) {
                $w->where('ti.pembayar_nama', 'like', $search)
                  ->orWhere('ti.tujuan', 'like', $search);
            });
        }

        $page    = max(1, (int) ($request->query('page', 1)));
        $perPage = 30;
        $total   = (clone $q)->count();
        $data    = $q->offset(($page - 1) * $perPage)->limit($perPage)->get();

        return response()->json([
            'success' => true,
            'data'    => $data,
            'meta'    => ['total' => $total, 'page' => $page, 'per_page' => $perPage],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $body   = $request->json()->all();
        $errors = [];

        $nominal      = (int) ($body['nominal'] ?? 0);
        $pembayarNama = trim($body['pembayar_nama'] ?? '');
        $tujuan       = trim($body['tujuan'] ?? '');
        $tanggal      = $body['tanggal'] ?? now()->toDateString();
        $keterangan   = $body['keterangan'] ?? null;

        if ($nominal < 1)       $errors['nominal']       = 'Nominal tidak valid.';
        if (empty($pembayarNama)) $errors['pembayar_nama'] = 'Nama pembayar wajib diisi.';
        if (empty($tujuan))       $errors['tujuan']        = 'Tujuan wajib diisi.';

        if ($errors) {
            return response()->json(['success' => false, 'message' => 'Data tidak valid.', 'errors' => $errors], 422);
        }

        $result = DB::transaction(function () use ($nominal, $pembayarNama, $tujuan, $tanggal, $keterangan, $body) {
            $kasMasuk = Kas::create([
                'jenis'      => 'masuk',
                'nominal'    => $nominal,
                'keterangan' => "Titipan dari {$pembayarNama} — {$tujuan}",
                'tanggal'    => $tanggal,
                'created_by' => Auth::id(),
            ]);

            $kasKeluar = Kas::create([
                'jenis'      => 'keluar',
                'nominal'    => $nominal,
                'keterangan' => "Bayar {$tujuan} (titipan {$pembayarNama})",
                'tanggal'    => $tanggal,
                'created_by' => Auth::id(),
            ]);

            $ti = TransaksiInstan::create([
                'tanggal'          => $tanggal,
                'nominal'          => $nominal,
                'pembayar_nama'    => $pembayarNama,
                'pembayar_user_id' => isset($body['pembayar_user_id']) ? (int) $body['pembayar_user_id'] : null,
                'tujuan'           => $tujuan,
                'keterangan'       => $keterangan,
                'kas_masuk_id'     => $kasMasuk->id,
                'kas_keluar_id'    => $kasKeluar->id,
                'created_by'       => Auth::id(),
            ]);

            return $ti;
        });

        $this->logActivity(
            'create_transaksi_instan',
            "Transaksi instan Rp " . number_format($nominal, 0, ',', '.') . " — {$tujuan}",
            TransaksiInstan::class,
            $result->id,
            ['nominal' => $nominal, 'tujuan' => $tujuan]
        );

        return response()->json([
            'success' => true,
            'message' => 'Transaksi instan berhasil dicatat.',
            'data'    => $result,
        ], 201);
    }

    public function destroy(int $id): JsonResponse
    {
        $ti = TransaksiInstan::find($id);
        if (!$ti) return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);

        DB::transaction(function () use ($ti) {
            Kas::whereIn('id', array_filter([$ti->kas_masuk_id, $ti->kas_keluar_id]))->delete();
            $ti->delete();
        });

        return response()->json(['success' => true, 'message' => 'Transaksi instan dihapus.']);
    }
}
