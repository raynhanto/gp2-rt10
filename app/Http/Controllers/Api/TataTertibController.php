<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TataTertib;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TataTertibController extends Controller
{
    public function index(): JsonResponse
    {
        $q = TataTertib::orderBy('urutan')->orderBy('id');
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            $q->where('is_active', true);
        }
        return response()->json(['success' => true, 'data' => $q->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        if (empty($request->input('judul')) || empty($request->input('isi')) || empty($request->input('kategori'))) {
            return response()->json(['success' => false, 'message' => 'Judul, isi, dan kategori wajib diisi.'], 422);
        }

        $rule = TataTertib::create([
            'kategori'   => $request->input('kategori'),
            'pasal'      => $request->input('pasal') ?: null,
            'judul'      => $request->input('judul'),
            'isi'        => $request->input('isi'),
            'urutan'     => (int) $request->input('urutan', 0),
            'is_active'  => (bool) $request->input('is_active', true),
            'created_by' => Auth::id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Peraturan berhasil ditambahkan.', 'data' => $rule], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $rule = TataTertib::find($id);
        if (!$rule) {
            return response()->json(['success' => false, 'message' => 'Peraturan tidak ditemukan.'], 404);
        }

        $rule->update([
            'kategori'  => $request->input('kategori', $rule->kategori),
            'pasal'     => $request->input('pasal') ?: null,
            'judul'     => $request->input('judul', $rule->judul),
            'isi'       => $request->input('isi', $rule->isi),
            'urutan'    => $request->has('urutan') ? (int) $request->input('urutan') : $rule->urutan,
            'is_active' => $request->has('is_active') ? (bool) $request->input('is_active') : $rule->is_active,
        ]);

        return response()->json(['success' => true, 'message' => 'Peraturan berhasil diperbarui.', 'data' => $rule->fresh()]);
    }

    public function destroy(int $id): JsonResponse
    {
        $rule = TataTertib::find($id);
        if (!$rule) {
            return response()->json(['success' => false, 'message' => 'Peraturan tidak ditemukan.'], 404);
        }
        $rule->delete();
        return response()->json(['success' => true, 'message' => 'Peraturan berhasil dihapus.']);
    }
}
