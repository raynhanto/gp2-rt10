<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Anggaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnggaranController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = Anggaran::with('kampanye:id,judul')
            ->orderByDesc('tahun')
            ->orderBy('kampanye_id')
            ->orderBy('created_at');

        if ($request->query('kampanye_id')) $q->where('kampanye_id', $request->query('kampanye_id'));
        if ($request->query('tahun'))       $q->where('tahun', $request->query('tahun'));

        return response()->json(['success' => true, 'data' => $q->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $body   = $request->json()->all();
        $errors = [];
        if (empty($body['pos']))          $errors['pos']      = 'Nama pos anggaran wajib diisi.';
        if ((int) ($body['estimasi'] ?? 0) < 1) $errors['estimasi'] = 'Estimasi tidak valid.';
        if ($errors) return response()->json(['success' => false, 'message' => 'Data tidak valid.', 'errors' => $errors], 422);

        $anggaran = Anggaran::create([
            'kampanye_id' => $body['kampanye_id'] ?? null,
            'tahun'       => $body['tahun']       ?? null,
            'sumber_dana' => $body['sumber_dana'] ?? 'campuran',
            'pos'         => $body['pos'],
            'estimasi'    => (int) $body['estimasi'],
            'catatan'     => $body['catatan'] ?? null,
            'created_by'  => Auth::id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Pos anggaran berhasil ditambahkan.', 'data' => $anggaran], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $anggaran = Anggaran::find($id);
        if (!$anggaran) return response()->json(['success' => false, 'message' => 'Anggaran tidak ditemukan.'], 404);

        $body = $request->json()->all();
        if (isset($body['estimasi']) && (int) $body['estimasi'] < 1) {
            return response()->json(['success' => false, 'message' => 'Estimasi tidak valid.'], 422);
        }

        $anggaran->update(array_filter([
            'kampanye_id' => array_key_exists('kampanye_id', $body) ? ($body['kampanye_id'] ?: null) : null,
            'tahun'       => $body['tahun']       ?? null,
            'sumber_dana' => $body['sumber_dana'] ?? null,
            'pos'         => $body['pos']         ?? null,
            'estimasi'    => isset($body['estimasi']) ? (int) $body['estimasi'] : null,
            'catatan'     => $body['catatan']     ?? null,
        ], fn($v) => $v !== null));

        return response()->json(['success' => true, 'message' => 'Anggaran berhasil diperbarui.', 'data' => $anggaran]);
    }

    public function destroy(int $id): JsonResponse
    {
        $anggaran = Anggaran::find($id);
        if (!$anggaran) return response()->json(['success' => false, 'message' => 'Anggaran tidak ditemukan.'], 404);

        if ($anggaran->realisasi > 0) {
            return response()->json(['success' => false, 'message' => 'Tidak bisa dihapus, sudah ada realisasi.'], 422);
        }

        $anggaran->delete();
        return response()->json(['success' => true, 'message' => 'Anggaran berhasil dihapus.']);
    }
}
