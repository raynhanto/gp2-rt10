<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Helpers\Upload;
use App\Http\Controllers\Controller;
use App\Models\Galeri;
use App\Models\GaleriFoto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GaleriController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = Galeri::withCount('fotos')
            ->orderByDesc('is_featured')
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        if (!Auth::check() || !Auth::user()->isAdmin()) {
            $q->where('is_public', true);
        }

        if ($request->query('kategori')) {
            $q->where('kategori', $request->query('kategori'));
        }

        return response()->json(['success' => true, 'data' => $q->get()]);
    }

    public function show(int $id): JsonResponse
    {
        $galeri = Galeri::with('fotos')->find($id);
        if (!$galeri) {
            return response()->json(['success' => false, 'message' => 'Album tidak ditemukan.'], 404);
        }
        if (!$galeri->is_public && (!Auth::check() || !Auth::user()->isAdmin())) {
            return response()->json(['success' => false, 'message' => 'Album tidak tersedia.'], 403);
        }

        return response()->json(['success' => true, 'data' => $galeri]);
    }

    public function store(Request $request): JsonResponse
    {
        if (empty($request->input('judul'))) {
            return response()->json(['success' => false, 'message' => 'Judul wajib diisi.'], 422);
        }
        if (empty($request->input('tanggal'))) {
            return response()->json(['success' => false, 'message' => 'Tanggal wajib diisi.'], 422);
        }

        $galeri = Galeri::create([
            'judul'       => $request->input('judul'),
            'deskripsi'   => $request->input('deskripsi') ?: null,
            'cover_url'   => null,
            'kategori'    => $request->input('kategori', 'kegiatan'),
            'tanggal'     => $request->input('tanggal'),
            'is_featured' => (bool) $request->input('is_featured', false),
            'is_public'   => (bool) $request->input('is_public', true),
            'created_by'  => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Album berhasil dibuat.',
            'data'    => $galeri,
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $galeri = Galeri::find($id);
        if (!$galeri) return response()->json(['success' => false, 'message' => 'Album tidak ditemukan.'], 404);

        $galeri->update([
            'judul'       => $request->input('judul', $galeri->judul),
            'deskripsi'   => $request->input('deskripsi') ?: null,
            'kategori'    => $request->input('kategori', $galeri->kategori),
            'tanggal'     => $request->input('tanggal', $galeri->tanggal->toDateString()),
            'is_featured' => $request->has('is_featured') ? (bool) $request->input('is_featured') : $galeri->is_featured,
            'is_public'   => $request->has('is_public')   ? (bool) $request->input('is_public')   : $galeri->is_public,
        ]);

        return response()->json(['success' => true, 'message' => 'Album berhasil diperbarui.', 'data' => $galeri->fresh()]);
    }

    public function addFotos(Request $request, int $id): JsonResponse
    {
        $galeri = Galeri::find($id);
        if (!$galeri) return response()->json(['success' => false, 'message' => 'Album tidak ditemukan.'], 404);

        $fotos = $request->file('fotos', []);
        if (empty($fotos)) {
            return response()->json(['success' => false, 'message' => 'Upload minimal 1 foto.'], 422);
        }

        $maxUrutan = GaleriFoto::where('galeri_id', $id)->max('urutan') ?? -1;
        $added     = 0;

        foreach ($fotos as $i => $foto) {
            try {
                $url = Upload::save($foto, 'galeri');
            } catch (\RuntimeException) {
                continue;
            }
            GaleriFoto::create([
                'galeri_id' => $id,
                'foto_url'  => $url,
                'urutan'    => $maxUrutan + $i + 1,
            ]);
            $added++;
        }

        if (!$galeri->cover_url) $galeri->refreshCover();

        return response()->json([
            'success' => true,
            'message' => "{$added} foto berhasil ditambahkan.",
            'data'    => $galeri->load('fotos'),
        ]);
    }

    public function deleteFoto(int $fotoId): JsonResponse
    {
        $foto = GaleriFoto::find($fotoId);
        if (!$foto) return response()->json(['success' => false, 'message' => 'Foto tidak ditemukan.'], 404);

        $galeri   = Galeri::find($foto->galeri_id);
        $wasCover = $galeri && $galeri->cover_url === $foto->foto_url;
        $fileUrl  = $foto->foto_url;

        $foto->delete();
        $this->deleteFile($fileUrl);

        if ($wasCover && $galeri) $galeri->refreshCover();

        return response()->json(['success' => true, 'message' => 'Foto berhasil dihapus.']);
    }

    public function destroy(int $id): JsonResponse
    {
        $galeri = Galeri::find($id);
        if (!$galeri) return response()->json(['success' => false, 'message' => 'Album tidak ditemukan.'], 404);

        $fileUrls = $galeri->fotos()->pluck('foto_url')->all();

        $galeri->delete(); // cascades to galeri_foto via FK

        foreach ($fileUrls as $url) {
            $this->deleteFile($url);
        }

        return response()->json(['success' => true, 'message' => 'Album berhasil dihapus.']);
    }

    private function deleteFile(?string $url): void
    {
        if (!$url) return;
        $path = public_path(ltrim($url, '/'));
        if (file_exists($path)) {
            @unlink($path);
        }
    }
}
