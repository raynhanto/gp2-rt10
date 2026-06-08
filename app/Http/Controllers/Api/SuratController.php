<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SuratPermohonan;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuratController extends Controller
{
    use LogsAdminActivity;

    public function index(Request $request): JsonResponse
    {
        $q = SuratPermohonan::with(['user:id,nama,email', 'processedBy:id,nama'])
            ->orderByRaw("FIELD(status,'menunggu','diproses','selesai','ditolak')")
            ->orderByDesc('created_at');

        if ($request->query('status'))      $q->where('status', $request->query('status'));
        if ($request->query('jenis_surat')) $q->where('jenis_surat', $request->query('jenis_surat'));

        return response()->json([
            'success' => true,
            'data'    => $q->get()->map(fn($s) => array_merge($s->toArray(), [
                'jenis_label' => $s->jenis_label,
            ])),
        ]);
    }

    public function mine(): JsonResponse
    {
        $data = SuratPermohonan::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($s) => array_merge($s->toArray(), ['jenis_label' => $s->jenis_label]));

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function store(Request $request): JsonResponse
    {
        $body = $request->json()->all();

        $jenis    = $body['jenis_surat'] ?? '';
        $atasNama = trim($body['atas_nama'] ?? '');
        $keperluan = trim($body['keperluan'] ?? '');

        if (!array_key_exists($jenis, SuratPermohonan::JENIS)) {
            return response()->json(['success' => false, 'message' => 'Jenis surat tidak valid.'], 422);
        }
        if (strlen($atasNama) < 2) {
            return response()->json(['success' => false, 'message' => 'Nama wajib diisi.'], 422);
        }
        if (strlen($keperluan) < 5) {
            return response()->json(['success' => false, 'message' => 'Keperluan wajib diisi.'], 422);
        }

        $surat = SuratPermohonan::create([
            'user_id'       => Auth::id(),
            'jenis_surat'   => $jenis,
            'atas_nama'     => $atasNama,
            'nik'           => $body['nik'] ?? null,
            'alamat'        => $body['alamat'] ?? null,
            'keperluan'     => $keperluan,
            'data_tambahan' => $body['data_tambahan'] ?? null,
            'status'        => 'menunggu',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permohonan surat berhasil dikirim.',
            'data'    => $surat,
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $surat  = SuratPermohonan::findOrFail($id);
        $body   = $request->json()->all();
        $status = $body['status'] ?? $surat->status;

        if (!in_array($status, ['menunggu', 'diproses', 'selesai', 'ditolak'])) {
            return response()->json(['success' => false, 'message' => 'Status tidak valid.'], 422);
        }

        $update = [
            'status'        => $status,
            'catatan_admin' => $body['catatan_admin'] ?? $surat->catatan_admin,
            'nomor_surat'   => $body['nomor_surat'] ?? $surat->nomor_surat,
            'tanggal_surat' => $body['tanggal_surat'] ?? $surat->tanggal_surat,
        ];

        if (in_array($status, ['selesai', 'ditolak']) && !$surat->processed_at) {
            $update['processed_by'] = Auth::id();
            $update['processed_at'] = now();
        }

        $surat->update($update);

        $this->logActivity(
            'update_surat',
            "Update permohonan surat #{$id} ({$surat->jenis_label}) → {$status}",
            SuratPermohonan::class,
            $id,
            ['status' => $status, 'nomor' => $update['nomor_surat']]
        );

        return response()->json(['success' => true, 'message' => 'Berhasil diperbarui.']);
    }

    public function destroy(int $id): JsonResponse
    {
        $surat = SuratPermohonan::findOrFail($id);
        $surat->delete();
        $this->logActivity('delete_surat', "Hapus surat #{$id}", SuratPermohonan::class, $id);
        return response()->json(['success' => true]);
    }

    public function jenisList(): JsonResponse
    {
        $list = array_map(
            fn($key, $label) => ['key' => $key, 'label' => $label],
            array_keys(SuratPermohonan::JENIS),
            array_values(SuratPermohonan::JENIS)
        );
        return response()->json(['success' => true, 'data' => $list]);
    }
}
