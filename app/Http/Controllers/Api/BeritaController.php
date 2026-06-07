<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Helpers\Upload;
use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BeritaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = Berita::with('createdBy:id,nama')
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at');

        if (!Auth::check() || !Auth::user()->isAdmin()) {
            $q->where('is_published', true);
        }

        if ($request->query('kategori')) {
            $q->where('kategori', $request->query('kategori'));
        }

        return response()->json(['success' => true, 'data' => $q->get()]);
    }

    public function show(int $id): JsonResponse
    {
        $berita = Berita::with('createdBy:id,nama')->find($id);
        if (!$berita) {
            return response()->json(['success' => false, 'message' => 'Berita tidak ditemukan.'], 404);
        }
        if (!$berita->is_published && (!Auth::check() || !Auth::user()->isAdmin())) {
            return response()->json(['success' => false, 'message' => 'Berita tidak tersedia.'], 403);
        }
        return response()->json(['success' => true, 'data' => $berita]);
    }

    public function store(Request $request): JsonResponse
    {
        if (empty($request->input('judul'))) {
            return response()->json(['success' => false, 'message' => 'Judul wajib diisi.'], 422);
        }
        if (empty($request->input('isi'))) {
            return response()->json(['success' => false, 'message' => 'Isi berita wajib diisi.'], 422);
        }

        $coverUrl = null;
        if ($request->hasFile('cover')) {
            try {
                $coverUrl = Upload::save($request->file('cover'), 'berita');
            } catch (\RuntimeException $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
        }

        $berita = Berita::create([
            'judul'        => $request->input('judul'),
            'slug'         => Berita::makeSlug($request->input('judul')),
            'isi'          => $request->input('isi'),
            'cover_url'    => $coverUrl,
            'kategori'     => $request->input('kategori', 'informasi'),
            'is_published' => (bool) $request->input('is_published', true),
            'is_pinned'    => (bool) $request->input('is_pinned', false),
            'created_by'   => Auth::id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Berita berhasil dipublikasikan.', 'data' => $berita], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $berita = Berita::find($id);
        if (!$berita) {
            return response()->json(['success' => false, 'message' => 'Berita tidak ditemukan.'], 404);
        }

        $coverUrl = $berita->cover_url;
        if ($request->hasFile('cover')) {
            try {
                $coverUrl = Upload::save($request->file('cover'), 'berita');
            } catch (\RuntimeException $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
        }

        $berita->update([
            'judul'        => $request->input('judul', $berita->judul),
            'isi'          => $request->input('isi', $berita->isi),
            'cover_url'    => $coverUrl,
            'kategori'     => $request->input('kategori', $berita->kategori),
            'is_published' => $request->has('is_published') ? (bool) $request->input('is_published') : $berita->is_published,
            'is_pinned'    => $request->has('is_pinned')    ? (bool) $request->input('is_pinned')    : $berita->is_pinned,
        ]);

        return response()->json(['success' => true, 'message' => 'Berita berhasil diperbarui.', 'data' => $berita->fresh()]);
    }

    public function destroy(int $id): JsonResponse
    {
        $berita = Berita::find($id);
        if (!$berita) {
            return response()->json(['success' => false, 'message' => 'Berita tidak ditemukan.'], 404);
        }
        $berita->delete();
        return response()->json(['success' => true, 'message' => 'Berita berhasil dihapus.']);
    }
}
