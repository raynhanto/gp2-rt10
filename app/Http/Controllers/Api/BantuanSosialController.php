<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BantuanSosial;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BantuanSosialController extends Controller
{
    use LogsAdminActivity;

    public function index(Request $request): JsonResponse
    {
        $q = BantuanSosial::with(['unitRumah', 'createdBy:id,nama'])
            ->orderByDesc('tanggal')
            ->orderByDesc('created_at');

        if ($request->query('jenis'))  $q->where('jenis', $request->query('jenis'));
        if ($request->query('status')) $q->where('status', $request->query('status'));
        if ($s = $request->query('search')) {
            $q->where('nama_penerima', 'like', "%{$s}%");
        }

        return response()->json(['success' => true, 'data' => $q->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $body = $request->json()->all();

        $nama = trim($body['nama_penerima'] ?? '');
        $jenis = $body['jenis'] ?? '';
        $tanggal = $body['tanggal'] ?? '';

        if (strlen($nama) < 2) {
            return response()->json(['success' => false, 'message' => 'Nama penerima wajib diisi.'], 422);
        }
        if (!array_key_exists($jenis, BantuanSosial::JENIS)) {
            return response()->json(['success' => false, 'message' => 'Jenis bantuan tidak valid.'], 422);
        }
        if (!$tanggal) {
            return response()->json(['success' => false, 'message' => 'Tanggal wajib diisi.'], 422);
        }

        $item = BantuanSosial::create([
            'unit_rumah_id' => $body['unit_rumah_id'] ?? null,
            'nama_penerima' => $nama,
            'jenis'         => $jenis,
            'keterangan'    => $body['keterangan'] ?? null,
            'tanggal'       => $tanggal,
            'nominal'       => $jenis === 'uang_tunai' ? ($body['nominal'] ?? null) : null,
            'status'        => 'aktif',
            'created_by'    => Auth::id(),
        ]);

        $this->logActivity('create_bantuan', "Tambah bantuan sosial: {$nama} ({$jenis})", BantuanSosial::class, $item->id);

        return response()->json(['success' => true, 'data' => $item], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $item = BantuanSosial::findOrFail($id);
        $body = $request->json()->all();

        $item->update([
            'status'     => $body['status'] ?? $item->status,
            'keterangan' => $body['keterangan'] ?? $item->keterangan,
            'nominal'    => $body['nominal'] ?? $item->nominal,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(int $id): JsonResponse
    {
        $item = BantuanSosial::findOrFail($id);
        $this->logActivity('delete_bantuan', "Hapus bantuan sosial #{$id}", BantuanSosial::class, $id);
        $item->delete();
        return response()->json(['success' => true]);
    }
}
