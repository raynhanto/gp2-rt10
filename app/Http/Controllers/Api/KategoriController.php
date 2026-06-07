<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriKeuangan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KategoriController extends Controller
{
    public function index(): JsonResponse
    {
        $data = KategoriKeuangan::orderBy('urutan')->orderBy('nama')->get();
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function store(Request $request): JsonResponse
    {
        $body   = $request->json()->all();
        $errors = [];
        if (empty($body['nama']))  $errors['nama']  = 'Nama kategori wajib diisi.';
        if (empty($body['jenis'])) $errors['jenis'] = 'Jenis wajib dipilih.';
        if (!in_array($body['jenis'] ?? '', ['masuk', 'keluar', 'keduanya'])) {
            $errors['jenis'] = 'Jenis tidak valid.';
        }
        if ($errors) return response()->json(['success' => false, 'message' => 'Data tidak valid.', 'errors' => $errors], 422);

        $kategori = KategoriKeuangan::create([
            'nama'       => $body['nama'],
            'jenis'      => $body['jenis'],
            'warna'      => $body['warna'] ?? '#1A3D2B',
            'ikon'       => $body['ikon']  ?? 'fa-tag',
            'urutan'     => (int) ($body['urutan'] ?? 0),
            'created_by' => Auth::id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Kategori berhasil ditambahkan.', 'data' => $kategori], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $kategori = KategoriKeuangan::find($id);
        if (!$kategori) return response()->json(['success' => false, 'message' => 'Kategori tidak ditemukan.'], 404);

        $body = $request->json()->all();
        if (empty($body['nama']))  return response()->json(['success' => false, 'message' => 'Nama kategori wajib diisi.'], 422);

        $kategori->update([
            'nama'   => $body['nama'],
            'jenis'  => $body['jenis']  ?? $kategori->jenis,
            'warna'  => $body['warna']  ?? $kategori->warna,
            'ikon'   => $body['ikon']   ?? $kategori->ikon,
            'urutan' => (int) ($body['urutan'] ?? $kategori->urutan),
        ]);

        return response()->json(['success' => true, 'message' => 'Kategori berhasil diperbarui.', 'data' => $kategori]);
    }

    public function destroy(int $id): JsonResponse
    {
        $kategori = KategoriKeuangan::find($id);
        if (!$kategori) return response()->json(['success' => false, 'message' => 'Kategori tidak ditemukan.'], 404);

        $kategori->delete();
        return response()->json(['success' => true, 'message' => 'Kategori berhasil dihapus.']);
    }
}
