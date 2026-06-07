<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Helpers\Upload;
use App\Http\Controllers\Controller;
use App\Models\Kampanye;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KampanyeController extends Controller
{
    public function index(): JsonResponse
    {
        $list = Kampanye::with('createdBy:id,nama')
            ->where('status', '!=', 'arsip')
            ->orderByRaw("FIELD(status,'urgent','aktif','selesai','arsip')")
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['success' => true, 'data' => $list]);
    }

    public function show(int $id): JsonResponse
    {
        $k = Kampanye::findOrFail($id);
        $k->donatur_count = $k->donasi()->where('status', 'verified')->count();
        return response()->json(['success' => true, 'data' => $k]);
    }

    public function store(Request $request): JsonResponse
    {
        $body   = $request->json()->all();
        $errors = [];
        if (empty($body['judul']))    $errors['judul']    = 'Judul wajib diisi.';
        if (empty($body['deskripsi'])) $errors['deskripsi'] = 'Deskripsi wajib diisi.';
        if ((int) ($body['target'] ?? 0) < 1000) $errors['target'] = 'Target minimal Rp 1.000.';
        if ($errors) return response()->json(['success' => false, 'message' => 'Data tidak valid.', 'errors' => $errors], 422);

        $k = Kampanye::create([
            'judul'      => $body['judul'],
            'deskripsi'  => $body['deskripsi'],
            'target'     => (int) $body['target'],
            'status'     => $body['status'] ?? 'aktif',
            'deadline'   => $body['deadline'] ?? null,
            'created_by' => Auth::id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Kampanye berhasil dibuat.', 'data' => $k], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $k    = Kampanye::findOrFail($id);
        $body = $request->json()->all();

        $k->update([
            'judul'     => $body['judul']     ?? $k->judul,
            'deskripsi' => $body['deskripsi'] ?? $k->deskripsi,
            'target'    => (int) ($body['target'] ?? $k->target),
            'status'    => $body['status']    ?? $k->status,
            'deadline'  => $body['deadline']  ?? $k->deadline,
        ]);

        return response()->json(['success' => true, 'message' => 'Kampanye diperbarui.', 'data' => $k->fresh()]);
    }

    public function uploadFoto(Request $request, int $id): JsonResponse
    {
        $k = Kampanye::findOrFail($id);
        if (!$request->hasFile('foto')) {
            return response()->json(['success' => false, 'message' => 'File foto wajib dipilih.'], 422);
        }
        try {
            $url = Upload::save($request->file('foto'), 'kampanye');
            $k->update(['foto_url' => $url]);
            return response()->json(['success' => true, 'message' => 'Foto berhasil disimpan.', 'data' => ['foto_url' => $url]]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
