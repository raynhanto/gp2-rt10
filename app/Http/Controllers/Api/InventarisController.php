<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventaris;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventarisController extends Controller
{
    use LogsAdminActivity;

    public function index(Request $request): JsonResponse
    {
        $q = Inventaris::orderBy('kategori')->orderBy('nama');

        if ($k = $request->query('kategori')) $q->where('kategori', $k);
        if ($c = $request->query('kondisi'))  $q->where('kondisi', $c);
        if ($s = $request->query('search'))   $q->where(function ($q) use ($s) {
            $q->where('nama', 'like', "%{$s}%")->orWhere('kode', 'like', "%{$s}%");
        });

        return response()->json(['success' => true, 'data' => $q->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $body = $request->json()->all();

        $nama = trim($body['nama'] ?? '');
        if (strlen($nama) < 2) {
            return response()->json(['success' => false, 'message' => 'Nama aset wajib diisi.'], 422);
        }
        if (!($body['kategori'] ?? '')) {
            return response()->json(['success' => false, 'message' => 'Kategori wajib dipilih.'], 422);
        }

        $item = Inventaris::create([
            'nama'        => $nama,
            'kode'        => $body['kode'] ?? null,
            'kategori'    => $body['kategori'],
            'jumlah'      => max(1, (int) ($body['jumlah'] ?? 1)),
            'kondisi'     => in_array($body['kondisi'] ?? '', ['baik', 'rusak_ringan', 'rusak_berat'])
                              ? $body['kondisi'] : 'baik',
            'lokasi'      => $body['lokasi'] ?? null,
            'tanggal_beli' => $body['tanggal_beli'] ?? null,
            'harga_beli'  => $body['harga_beli'] ?? null,
            'keterangan'  => $body['keterangan'] ?? null,
            'created_by'  => Auth::id(),
        ]);

        $this->logActivity('create_inventaris', "Tambah inventaris: {$nama}", Inventaris::class, $item->id);

        return response()->json(['success' => true, 'data' => $item], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $item = Inventaris::findOrFail($id);
        $body = $request->json()->all();

        $item->update(array_filter([
            'nama'        => isset($body['nama']) ? trim($body['nama']) : null,
            'kode'        => $body['kode'] ?? null,
            'kategori'    => $body['kategori'] ?? null,
            'jumlah'      => isset($body['jumlah']) ? max(1, (int) $body['jumlah']) : null,
            'kondisi'     => $body['kondisi'] ?? null,
            'lokasi'      => $body['lokasi'] ?? null,
            'tanggal_beli' => $body['tanggal_beli'] ?? null,
            'harga_beli'  => $body['harga_beli'] ?? null,
            'keterangan'  => $body['keterangan'] ?? null,
        ], fn($v) => $v !== null));

        return response()->json(['success' => true]);
    }

    public function destroy(int $id): JsonResponse
    {
        $item = Inventaris::findOrFail($id);
        $this->logActivity('delete_inventaris', "Hapus inventaris #{$id} ({$item->nama})", Inventaris::class, $id);
        $item->delete();
        return response()->json(['success' => true]);
    }
}
