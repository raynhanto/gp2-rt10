<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Helpers\Upload;
use App\Http\Controllers\Controller;
use App\Models\Anggaran;
use App\Models\Kas;
use App\Models\Pengeluaran;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengeluaranController extends Controller
{
    use LogsAdminActivity;
    public function index(Request $request): JsonResponse
    {
        $perPage = min(100, max(10, (int) ($request->query('per_page', 50))));
        $page    = max(1, (int) ($request->query('page', 1)));

        $q = DB::table('pengeluaran as p')
            ->leftJoin('kampanye as k', 'k.id', '=', 'p.kampanye_id')
            ->leftJoin('users as u', 'u.id', '=', 'p.created_by')
            ->leftJoin('kategori_keuangan as kat', 'kat.id', '=', 'p.kategori_id')
            ->leftJoin('anggaran as a', 'a.id', '=', 'p.anggaran_id')
            ->select(
                'p.*',
                'k.judul as kampanye_judul',
                'u.nama as created_by_nama',
                'kat.nama as kategori_nama',
                'kat.warna as kategori_warna',
                'a.pos as anggaran_pos'
            )
            ->orderByDesc('p.tanggal')
            ->orderByDesc('p.created_at');

        if ($request->query('kampanye_id')) $q->where('p.kampanye_id', $request->query('kampanye_id'));
        if ($request->query('kategori_id')) $q->where('p.kategori_id', $request->query('kategori_id'));
        if ($request->query('dari'))        $q->where('p.tanggal', '>=', $request->query('dari'));
        if ($request->query('sampai'))      $q->where('p.tanggal', '<=', $request->query('sampai'));
        if ($request->query('search')) {
            $q->where('p.keterangan', 'like', '%' . $request->query('search') . '%');
        }

        $total = (clone $q)->count();
        $data  = $q->offset(($page - 1) * $perPage)->limit($perPage)->get();

        if ($data->isNotEmpty()) {
            $ids      = $data->pluck('id')->all();
            $lampiran = DB::table('lampiran')
                ->where('attachable_type', 'pengeluaran')
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
            'meta'    => ['total' => $total, 'page' => $page, 'per_page' => $perPage,
                          'pages' => (int) ceil($total / $perPage)],
        ]);
    }

    public function publicList(): JsonResponse
    {
        $list = DB::table('pengeluaran as p')
            ->leftJoin('kampanye as k', 'k.id', '=', 'p.kampanye_id')
            ->select('p.keterangan', 'p.nominal', 'p.tanggal', 'k.judul')
            ->orderByDesc('p.tanggal')
            ->limit(20)
            ->get();
        return response()->json(['success' => true, 'data' => $list]);
    }

    public function store(Request $request): JsonResponse
    {
        $body   = $request->all();
        $errors = [];
        if (empty($body['keterangan']))   $errors['keterangan'] = 'Keterangan wajib diisi.';
        if ((int) ($body['nominal'] ?? 0) < 1) $errors['nominal'] = 'Nominal tidak valid.';
        if (empty($body['tanggal']))      $errors['tanggal']    = 'Tanggal wajib diisi.';
        if ($errors) return response()->json(['success' => false, 'message' => 'Data tidak valid.', 'errors' => $errors], 422);

        // Legacy single-file upload (bukti_url field)
        $buktiUrl = null;
        if ($request->hasFile('bukti')) {
            try {
                $buktiUrl = Upload::save($request->file('bukti'), 'pengeluaran');
            } catch (\RuntimeException $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
        }

        DB::transaction(function () use ($body, $buktiUrl) {
            $p = Pengeluaran::create([
                'kampanye_id' => $body['kampanye_id'] ?? null,
                'anggaran_id' => $body['anggaran_id'] ?? null,
                'kategori_id' => $body['kategori_id'] ?? null,
                'keterangan'  => $body['keterangan'],
                'nominal'     => (int) $body['nominal'],
                'tanggal'     => $body['tanggal'],
                'bukti_url'   => $buktiUrl,
                'created_by'  => Auth::id(),
            ]);

            Kas::create([
                'tanggal'     => $p->tanggal,
                'jenis'       => 'keluar',
                'nominal'     => $p->nominal,
                'keterangan'  => $p->keterangan,
                'kategori_id' => $p->kategori_id,
                'kampanye_id' => $p->kampanye_id,
                'created_by'  => Auth::id(),
            ]);

            if (!empty($body['anggaran_id'])) {
                $angId = (int) $body['anggaran_id'];
                Anggaran::where('id', $angId)->update([
                    'realisasi' => Pengeluaran::where('anggaran_id', $angId)->sum('nominal'),
                ]);
            }
        });

        $this->logActivity('create_pengeluaran', "Catat pengeluaran Rp " . number_format((int)$body['nominal'], 0, ',', '.') . " — {$body['keterangan']}");

        return response()->json(['success' => true, 'message' => 'Pengeluaran berhasil dicatat.'], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $p = Pengeluaran::find($id);
        if (!$p) return response()->json(['success' => false, 'message' => 'Pengeluaran tidak ditemukan.'], 404);

        $body   = $request->all();
        $errors = [];
        if (isset($body['keterangan']) && empty($body['keterangan'])) $errors['keterangan'] = 'Keterangan wajib diisi.';
        if (isset($body['nominal']) && (int) $body['nominal'] < 1)    $errors['nominal']     = 'Nominal tidak valid.';
        if ($errors) return response()->json(['success' => false, 'message' => 'Data tidak valid.', 'errors' => $errors], 422);

        $old = $p->nominal;

        DB::transaction(function () use ($p, $body, &$old) {
            $p->update(array_filter([
                'keterangan'  => $body['keterangan']  ?? null,
                'nominal'     => isset($body['nominal']) ? (int) $body['nominal'] : null,
                'tanggal'     => $body['tanggal']     ?? null,
                'kategori_id' => array_key_exists('kategori_id', $body) ? ($body['kategori_id'] ?: null) : null,
                'kampanye_id' => array_key_exists('kampanye_id', $body) ? ($body['kampanye_id'] ?: null) : null,
                'anggaran_id' => array_key_exists('anggaran_id', $body) ? ($body['anggaran_id'] ?: null) : null,
            ], fn($v) => $v !== null));

            if ($p->anggaran_id) {
                Anggaran::where('id', $p->anggaran_id)->update([
                    'realisasi' => Pengeluaran::where('anggaran_id', $p->anggaran_id)->sum('nominal'),
                ]);
            }
        });

        $this->logActivity('update_pengeluaran', "Edit pengeluaran #{$id}", Pengeluaran::class, $id);

        return response()->json(['success' => true, 'message' => 'Pengeluaran berhasil diperbarui.', 'data' => $p]);
    }

    public function destroy(int $id): JsonResponse
    {
        $p = Pengeluaran::find($id);
        if (!$p) return response()->json(['success' => false, 'message' => 'Pengeluaran tidak ditemukan.'], 404);

        DB::transaction(function () use ($p) {
            if ($p->anggaran_id) {
                Anggaran::where('id', $p->anggaran_id)->update([
                    'realisasi' => Pengeluaran::where('anggaran_id', $p->anggaran_id)
                        ->where('id', '!=', $p->id)->sum('nominal'),
                ]);
            }
            $p->delete();
        });

        $this->logActivity('delete_pengeluaran', "Hapus pengeluaran #{$id} — {$p->keterangan}", Pengeluaran::class, $id);

        return response()->json(['success' => true, 'message' => 'Pengeluaran berhasil dihapus.']);
    }
}
