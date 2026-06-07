<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SaranKeluhan;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaranKeluhanController extends Controller
{
    use LogsAdminActivity;

    public function index(Request $request): JsonResponse
    {
        $q = SaranKeluhan::with(['user:id,nama,email', 'respondedBy:id,nama'])
            ->orderByRaw("FIELD(status,'baru','dibaca','diproses','selesai')")
            ->orderByDesc('created_at');

        if ($request->query('status'))   $q->where('status', $request->query('status'));
        if ($request->query('kategori')) $q->where('kategori', $request->query('kategori'));

        $data = $q->get()->map(fn($s) => array_merge($s->toArray(), [
            'nama_display'    => $s->nama_pengirim_display,
            'tanggapan_oleh_nama' => $s->respondedBy?->nama,
        ]));

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function mine(): JsonResponse
    {
        $data = SaranKeluhan::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function store(Request $request): JsonResponse
    {
        $body = $request->json()->all();

        $judul    = trim($body['judul'] ?? '');
        $isi      = trim($body['isi'] ?? '');
        $kategori = $body['kategori'] ?? 'saran';
        $isAnonym = !empty($body['is_anonym']);
        $noWa     = trim($body['no_wa'] ?? '');

        if (strlen($judul) < 3)  return response()->json(['success' => false, 'message' => 'Judul minimal 3 karakter.'], 422);
        if (strlen($isi) < 10)   return response()->json(['success' => false, 'message' => 'Isi minimal 10 karakter.'], 422);
        if (!in_array($kategori, ['saran', 'keluhan', 'pertanyaan'])) {
            return response()->json(['success' => false, 'message' => 'Kategori tidak valid.'], 422);
        }

        $nama = null;
        if (!Auth::check()) {
            $nama = trim($body['nama'] ?? '');
            if (!$isAnonym && strlen($nama) < 2) {
                return response()->json(['success' => false, 'message' => 'Nama pengirim wajib diisi.'], 422);
            }
        }

        SaranKeluhan::create([
            'user_id'       => Auth::id(),
            'nama_pengirim' => Auth::check() ? Auth::user()->nama : ($isAnonym ? null : $nama),
            'no_wa'         => $noWa ?: null,
            'kategori'      => $kategori,
            'judul'         => $judul,
            'isi'           => $isi,
            'is_anonym'     => $isAnonym,
            'status'        => 'baru',
        ]);

        return response()->json(['success' => true, 'message' => 'Pesan berhasil dikirim. Terima kasih!'], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $saran    = SaranKeluhan::findOrFail($id);
        $body     = $request->json()->all();
        $status   = $body['status'] ?? $saran->status;
        $tanggapan = trim($body['tanggapan'] ?? '');

        if (!in_array($status, ['baru', 'dibaca', 'diproses', 'selesai'])) {
            return response()->json(['success' => false, 'message' => 'Status tidak valid.'], 422);
        }

        $update = ['status' => $status];
        if ($tanggapan !== '') {
            $update['tanggapan']      = $tanggapan;
            $update['tanggapan_at']   = now();
            $update['tanggapan_oleh'] = Auth::id();
        }

        $saran->update($update);

        $this->logActivity(
            'update_saran_keluhan',
            "Update saran #{$id} → {$status}",
            SaranKeluhan::class,
            $id,
            ['status' => $status]
        );

        return response()->json(['success' => true, 'message' => 'Berhasil diperbarui.']);
    }

    public function destroy(int $id): JsonResponse
    {
        $saran = SaranKeluhan::findOrFail($id);
        $saran->delete();
        $this->logActivity('delete_saran_keluhan', "Hapus saran #{$id}", SaranKeluhan::class, $id);
        return response()->json(['success' => true, 'message' => 'Dihapus.']);
    }
}
