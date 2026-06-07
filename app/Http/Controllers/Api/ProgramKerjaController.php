<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProgramKerja;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgramKerjaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tahun = $request->query('tahun') ?: date('Y');
        $data  = ProgramKerja::where('tahun', $tahun)
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();

        $tahunList = ProgramKerja::selectRaw('DISTINCT tahun')->orderByDesc('tahun')->pluck('tahun');

        return response()->json(['success' => true, 'data' => $data, 'tahun_list' => $tahunList, 'tahun' => (int) $tahun]);
    }

    public function store(Request $request): JsonResponse
    {
        if (empty($request->input('nama')) || empty($request->input('bidang')) || empty($request->input('tahun'))) {
            return response()->json(['success' => false, 'message' => 'Nama, bidang, dan tahun wajib diisi.'], 422);
        }

        $item = ProgramKerja::create([
            'tahun'          => (int) $request->input('tahun'),
            'bidang'         => $request->input('bidang'),
            'nama'           => $request->input('nama'),
            'deskripsi'      => $request->input('deskripsi') ?: null,
            'status'         => $request->input('status', 'rencana'),
            'target_mulai'   => $request->input('target_mulai') ?: null,
            'target_selesai' => $request->input('target_selesai') ?: null,
            'urutan'         => (int) $request->input('urutan', 0),
            'created_by'     => Auth::id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Program kerja berhasil ditambahkan.', 'data' => $item], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $item = ProgramKerja::find($id);
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Program kerja tidak ditemukan.'], 404);
        }

        $item->update([
            'tahun'          => $request->has('tahun')          ? (int) $request->input('tahun')          : $item->tahun,
            'bidang'         => $request->input('bidang',         $item->bidang),
            'nama'           => $request->input('nama',           $item->nama),
            'deskripsi'      => $request->input('deskripsi')     ?: null,
            'status'         => $request->input('status',         $item->status),
            'target_mulai'   => $request->input('target_mulai')  ?: null,
            'target_selesai' => $request->input('target_selesai') ?: null,
            'urutan'         => $request->has('urutan')           ? (int) $request->input('urutan')        : $item->urutan,
        ]);

        return response()->json(['success' => true, 'message' => 'Program kerja berhasil diperbarui.', 'data' => $item->fresh()]);
    }

    public function destroy(int $id): JsonResponse
    {
        $item = ProgramKerja::find($id);
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Program kerja tidak ditemukan.'], 404);
        }
        $item->delete();
        return response()->json(['success' => true, 'message' => 'Program kerja berhasil dihapus.']);
    }
}
