<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengumumanController extends Controller
{
    public function index(): JsonResponse
    {
        $list = DB::table('pengumuman as p')
            ->leftJoin('users as u', 'u.id', '=', 'p.created_by')
            ->select('p.*', 'u.nama as created_by_nama')
            ->orderByDesc('p.created_at')
            ->limit(20)
            ->get();
        return response()->json(['success' => true, 'data' => $list]);
    }

    public function store(Request $request): JsonResponse
    {
        $body = $request->json()->all();
        if (empty($body['judul'])) return response()->json(['success' => false, 'message' => 'Judul wajib diisi.'], 422);
        if (empty($body['isi']))   return response()->json(['success' => false, 'message' => 'Isi wajib diisi.'], 422);

        Pengumuman::create([
            'judul'      => $body['judul'],
            'isi'        => $body['isi'],
            'target'     => $body['target'] ?? 'semua',
            'created_by' => Auth::id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Pengumuman berhasil dikirim.'], 201);
    }
}
