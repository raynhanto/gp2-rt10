<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Helpers\Upload;
use App\Http\Controllers\Controller;
use App\Models\Lampiran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LampiranController extends Controller
{
    private const MAX_PER_RECORD = 5;

    private const ALLOWED_TYPES = ['kas', 'pengeluaran', 'donasi', 'iuran_bayar'];

    public function index(Request $request): JsonResponse
    {
        $type = $request->query('type');
        $id   = (int) $request->query('id');

        if (!in_array($type, self::ALLOWED_TYPES, true) || $id < 1) {
            return response()->json(['success' => false, 'message' => 'Parameter tidak valid.'], 422);
        }

        $items = Lampiran::where('attachable_type', $type)
            ->where('attachable_id', $id)
            ->orderBy('created_at')
            ->get();

        return response()->json(['success' => true, 'data' => $items]);
    }

    public function store(Request $request): JsonResponse
    {
        $type = $request->input('type');
        $id   = (int) $request->input('id');

        if (!in_array($type, self::ALLOWED_TYPES, true) || $id < 1) {
            return response()->json(['success' => false, 'message' => 'Parameter tidak valid.'], 422);
        }

        $existing = Lampiran::where('attachable_type', $type)
            ->where('attachable_id', $id)
            ->count();

        if ($existing >= self::MAX_PER_RECORD) {
            return response()->json([
                'success' => false,
                'message' => "Maksimal " . self::MAX_PER_RECORD . " lampiran per transaksi.",
            ], 422);
        }

        if (!$request->hasFile('file')) {
            return response()->json(['success' => false, 'message' => 'File wajib diunggah.'], 422);
        }

        try {
            $file = $request->file('file');
            $url  = Upload::save($file, "lampiran/{$type}");

            $lampiran = Lampiran::create([
                'attachable_type' => $type,
                'attachable_id'   => $id,
                'nama_asli'       => $file->getClientOriginalName(),
                'url'             => $url,
                'mime'            => $file->getMimeType(),
                'ukuran_kb'       => (int) ceil($file->getSize() / 1024),
                'created_by'      => Auth::id(),
            ]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true, 'message' => 'Lampiran berhasil diunggah.', 'data' => $lampiran], 201);
    }

    public function destroy(int $id): JsonResponse
    {
        $lampiran = Lampiran::find($id);
        if (!$lampiran) return response()->json(['success' => false, 'message' => 'Lampiran tidak ditemukan.'], 404);

        $path = storage_path(ltrim($lampiran->url, '/'));
        if (file_exists($path)) @unlink($path);

        $lampiran->delete();
        return response()->json(['success' => true, 'message' => 'Lampiran berhasil dihapus.']);
    }
}
