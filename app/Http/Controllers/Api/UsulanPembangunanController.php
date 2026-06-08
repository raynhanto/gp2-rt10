<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UsulanPembangunan;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsulanPembangunanController extends Controller
{
    use LogsAdminActivity;

    public function index(Request $request): JsonResponse
    {
        $q = UsulanPembangunan::with(['user:id,nama,email', 'respondedBy:id,nama'])
            ->orderByRaw("FIELD(status,'baru','dikaji','disetujui','selesai','ditolak')")
            ->orderByDesc('created_at');

        if ($s = $request->query('status'))   $q->where('status', $s);
        if ($p = $request->query('prioritas')) $q->where('prioritas', $p);

        return response()->json([
            'success' => true,
            'data'    => $q->get()->map(fn($u) => array_merge($u->toArray(), [
                'pengusul_display' => $u->pengusul_display,
            ])),
        ]);
    }

    public function mine(): JsonResponse
    {
        $data = UsulanPembangunan::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($u) => array_merge($u->toArray(), ['pengusul_display' => $u->pengusul_display]));

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function store(Request $request): JsonResponse
    {
        $body = $request->json()->all();

        $judul    = trim($body['judul'] ?? '');
        $deskripsi = trim($body['deskripsi'] ?? '');
        $isAnonym = (bool) ($body['is_anonym'] ?? false);

        if (strlen($judul) < 3) {
            return response()->json(['success' => false, 'message' => 'Judul wajib diisi (min. 3 karakter).'], 422);
        }
        if (strlen($deskripsi) < 10) {
            return response()->json(['success' => false, 'message' => 'Deskripsi wajib diisi (min. 10 karakter).'], 422);
        }

        $usulan = UsulanPembangunan::create([
            'user_id'       => Auth::id(),
            'nama_pengusul' => $isAnonym ? null : (Auth::user()?->nama ?? null),
            'judul'         => $judul,
            'deskripsi'     => $deskripsi,
            'lokasi'        => $body['lokasi'] ?? null,
            'prioritas'     => in_array($body['prioritas'] ?? '', ['rendah', 'sedang', 'tinggi'])
                                ? $body['prioritas'] : 'sedang',
            'is_anonym'     => $isAnonym,
            'status'        => 'baru',
        ]);

        return response()->json(['success' => true, 'message' => 'Usulan berhasil dikirim.', 'data' => $usulan], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $usulan = UsulanPembangunan::findOrFail($id);
        $body   = $request->json()->all();
        $status = $body['status'] ?? $usulan->status;

        $valid = ['baru', 'dikaji', 'disetujui', 'ditolak', 'selesai'];
        if (!in_array($status, $valid)) {
            return response()->json(['success' => false, 'message' => 'Status tidak valid.'], 422);
        }

        $update = [
            'status'     => $status,
            'tanggapan'  => $body['tanggapan'] ?? $usulan->tanggapan,
        ];

        if ($body['tanggapan'] ?? null) {
            $update['tanggapan_at']   = now();
            $update['tanggapan_oleh'] = Auth::id();
        }

        $usulan->update($update);

        $this->logActivity('update_usulan', "Update usulan #{$id} → {$status}", UsulanPembangunan::class, $id);

        return response()->json(['success' => true]);
    }

    public function destroy(int $id): JsonResponse
    {
        $usulan = UsulanPembangunan::findOrFail($id);
        $this->logActivity('delete_usulan', "Hapus usulan #{$id}", UsulanPembangunan::class, $id);
        $usulan->delete();
        return response()->json(['success' => true]);
    }
}
