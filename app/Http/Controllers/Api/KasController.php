<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donasi;
use App\Models\Kas;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KasController extends Controller
{
    use LogsAdminActivity;
    public function index(Request $request): JsonResponse
    {
        $perPage = min(100, max(10, (int) ($request->query('per_page', 50))));
        $page    = max(1, (int) ($request->query('page', 1)));

        $q = DB::table('kas as k')
            ->leftJoin('users as u', 'u.id', '=', 'k.created_by')
            ->leftJoin('kampanye as ka', 'ka.id', '=', 'k.kampanye_id')
            ->leftJoin('kategori_keuangan as kat', 'kat.id', '=', 'k.kategori_id')
            ->select(
                'k.*',
                'u.nama as created_by_nama',
                'ka.judul as kampanye_judul',
                'kat.nama as kategori_nama',
                'kat.warna as kategori_warna',
                'kat.ikon as kategori_ikon'
            )
            ->whereNull('k.deleted_at')
            ->orderByRaw('COALESCE(k.tanggal, DATE(k.created_at)) DESC')
            ->orderByDesc('k.created_at');

        if ($request->query('jenis'))       $q->where('k.jenis', $request->query('jenis'));
        if ($request->query('kategori_id')) $q->where('k.kategori_id', $request->query('kategori_id'));
        if ($request->query('kampanye_id')) $q->where('k.kampanye_id', $request->query('kampanye_id'));
        if ($request->query('dari'))        $q->where(DB::raw('COALESCE(k.tanggal, DATE(k.created_at))'), '>=', $request->query('dari'));
        if ($request->query('sampai'))      $q->where(DB::raw('COALESCE(k.tanggal, DATE(k.created_at))'), '<=', $request->query('sampai'));
        if ($request->query('search')) {
            $s = '%' . $request->query('search') . '%';
            $q->where('k.keterangan', 'like', $s);
        }

        $total = (clone $q)->count();
        $data  = $q->offset(($page - 1) * $perPage)->limit($perPage)->get();

        // Attach lampiran for each entry
        if ($data->isNotEmpty()) {
            $ids      = $data->pluck('id')->all();
            $lampiran = DB::table('lampiran')
                ->where('attachable_type', 'kas')
                ->whereIn('attachable_id', $ids)
                ->get()
                ->groupBy('attachable_id');

            $data = $data->map(function ($row) use ($lampiran) {
                $row->lampiran = $lampiran[$row->id] ?? collect();
                return $row;
            });
        }

        return response()->json([
            'success' => true,
            'data'    => $data,
            'meta'    => [
                'total'    => $total,
                'page'     => $page,
                'per_page' => $perPage,
                'pages'    => (int) ceil($total / $perPage),
            ],
        ]);
    }

    public function summary(): JsonResponse
    {
        $row = DB::table('kas')
            ->whereNull('deleted_at')
            ->selectRaw("
                COALESCE(SUM(CASE WHEN jenis='masuk' THEN nominal ELSE 0 END),0) as masuk,
                COALESCE(SUM(CASE WHEN jenis='keluar' THEN nominal ELSE 0 END),0) as keluar,
                COALESCE(SUM(CASE WHEN jenis='masuk' THEN nominal ELSE -nominal END),0) as saldo,
                COALESCE(SUM(CASE WHEN jenis='masuk' AND MONTH(COALESCE(tanggal,DATE(created_at)))=MONTH(NOW()) AND YEAR(COALESCE(tanggal,DATE(created_at)))=YEAR(NOW()) THEN nominal ELSE 0 END),0) as masuk_bulan,
                COALESCE(SUM(CASE WHEN jenis='keluar' AND MONTH(COALESCE(tanggal,DATE(created_at)))=MONTH(NOW()) AND YEAR(COALESCE(tanggal,DATE(created_at)))=YEAR(NOW()) THEN nominal ELSE 0 END),0) as keluar_bulan
            ")
            ->first();

        $row->donasi_count = Donasi::where('status', 'verified')->count();

        return response()->json(['success' => true, 'data' => $row]);
    }

    public function store(Request $request): JsonResponse
    {
        $body = $request->json()->all();
        if (!in_array($body['jenis'] ?? '', ['masuk', 'keluar'])) {
            return response()->json(['success' => false, 'message' => 'Jenis tidak valid.'], 422);
        }
        if (empty($body['keterangan'])) {
            return response()->json(['success' => false, 'message' => 'Keterangan wajib diisi.'], 422);
        }
        if ((int) ($body['nominal'] ?? 0) < 1) {
            return response()->json(['success' => false, 'message' => 'Nominal tidak valid.'], 422);
        }

        $kas = Kas::create([
            'tanggal'     => $body['tanggal'] ?? now()->toDateString(),
            'jenis'       => $body['jenis'],
            'nominal'     => (int) $body['nominal'],
            'keterangan'  => $body['keterangan'],
            'kategori_id' => $body['kategori_id'] ?? null,
            'kampanye_id' => $body['kampanye_id'] ?? null,
            'created_by'  => Auth::id(),
        ]);

        $this->logActivity('create_kas', "Tambah kas {$kas->jenis} Rp " . number_format($kas->nominal, 0, ',', '.') . " — {$kas->keterangan}", Kas::class, $kas->id);

        return response()->json(['success' => true, 'message' => 'Entri kas berhasil disimpan.', 'data' => $kas], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $kas = Kas::find($id);
        if (!$kas) return response()->json(['success' => false, 'message' => 'Entri tidak ditemukan.'], 404);

        // Auto-generated entries from donasi/iuran cannot be freely edited
        if ($kas->donasi_id) {
            return response()->json(['success' => false, 'message' => 'Entri donasi tidak bisa diedit langsung.'], 422);
        }

        $body = $request->json()->all();
        if (!empty($body['nominal']) && (int) $body['nominal'] < 1) {
            return response()->json(['success' => false, 'message' => 'Nominal tidak valid.'], 422);
        }

        $kas->update(array_filter([
            'tanggal'     => $body['tanggal']     ?? null,
            'keterangan'  => $body['keterangan']  ?? null,
            'nominal'     => isset($body['nominal']) ? (int) $body['nominal'] : null,
            'kategori_id' => array_key_exists('kategori_id', $body) ? ($body['kategori_id'] ?: null) : null,
            'kampanye_id' => array_key_exists('kampanye_id', $body) ? ($body['kampanye_id'] ?: null) : null,
        ], fn($v) => $v !== null));

        $this->logActivity('update_kas', "Edit kas #{$id}", Kas::class, $id);

        return response()->json(['success' => true, 'message' => 'Entri kas berhasil diperbarui.', 'data' => $kas]);
    }

    public function destroy(int $id): JsonResponse
    {
        $kas = Kas::find($id);
        if (!$kas) return response()->json(['success' => false, 'message' => 'Entri tidak ditemukan.'], 404);

        if ($kas->donasi_id) {
            return response()->json(['success' => false, 'message' => 'Entri otomatis dari donasi tidak bisa dihapus.'], 422);
        }

        $this->logActivity('delete_kas', "Hapus kas #{$id} — {$kas->keterangan}", Kas::class, $id);

        $kas->delete();
        return response()->json(['success' => true, 'message' => 'Entri kas berhasil dihapus.']);
    }
}
