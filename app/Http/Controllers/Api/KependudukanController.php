<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnggotaKeluarga;
use App\Models\KepalaKeluarga;
use App\Models\Kendaraan;
use App\Models\UnitRumah;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KependudukanController extends Controller
{
    use LogsAdminActivity;

    // ── Stats ─────────────────────────────────────────────────────

    public function stats(): JsonResponse
    {
        $totalKK   = KepalaKeluarga::count();
        $totalJiwa = $totalKK + AnggotaKeluarga::count();
        $totalKend = Kendaraan::count();

        $perBlok = DB::table('kepala_keluarga as kk')
            ->join('unit_rumah as u', 'u.id', '=', 'kk.unit_rumah_id')
            ->select('u.blok', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('u.blok')
            ->orderBy('u.blok')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => compact('totalKK', 'totalJiwa', 'totalKend', 'perBlok'),
        ]);
    }

    // ── Kepala Keluarga ───────────────────────────────────────────

    public function kepalaIndex(Request $request): JsonResponse
    {
        $q = KepalaKeluarga::with(['unitRumah:id,blok,nomor'])
            ->orderBy('nama');

        if ($search = $request->query('search')) {
            $q->where(function ($sub) use ($search) {
                $sub->where('nama', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('no_kk', 'like', "%{$search}%");
            });
        }

        if ($blok = $request->query('blok')) {
            $q->whereHas('unitRumah', fn($r) => $r->where('blok', $blok));
        }

        if ($request->query('all') === '1') {
            $items = $q->get();
            return response()->json(['success' => true, 'data' => $items->all(), 'meta' => ['total' => $items->count()]]);
        }

        $perPage = min(50, (int) ($request->query('per_page', 20)));
        $data    = $q->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $data->items(),
            'meta'    => [
                'total'        => $data->total(),
                'per_page'     => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page'    => $data->lastPage(),
            ],
        ]);
    }

    public function kepalaStore(Request $request): JsonResponse
    {
        $body = $request->json()->all();

        if (empty($body['nama'])) {
            return response()->json(['success' => false, 'message' => 'Nama wajib diisi.'], 422);
        }

        if (!empty($body['nik']) && KepalaKeluarga::where('nik', $body['nik'])->exists()) {
            return response()->json(['success' => false, 'message' => 'NIK sudah terdaftar.'], 422);
        }

        $kk = KepalaKeluarga::create([
            'unit_rumah_id'    => $body['unit_rumah_id'] ?? null,
            'user_id'          => $body['user_id'] ?? null,
            'no_kk'            => $body['no_kk'] ?? null,
            'nama'             => $body['nama'],
            'nik'              => $body['nik'] ?? null,
            'tempat_lahir'     => $body['tempat_lahir'] ?? null,
            'tanggal_lahir'    => $body['tanggal_lahir'] ?? null,
            'jenis_kelamin'    => $body['jenis_kelamin'] ?? null,
            'agama'            => $body['agama'] ?? null,
            'pendidikan'       => $body['pendidikan'] ?? null,
            'pekerjaan'        => $body['pekerjaan'] ?? null,
            'no_wa'            => $body['no_wa'] ?? null,
            'status_perkawinan' => $body['status_perkawinan'] ?? null,
            'status_tinggal'   => $body['status_tinggal'] ?? 'tetap',
            'keterangan'       => $body['keterangan'] ?? null,
            'created_by'       => Auth::id(),
        ]);

        $this->logActivity('tambah_kk', "Tambah KK: {$kk->nama}", 'KepalaKeluarga', $kk->id);

        return response()->json(['success' => true, 'data' => $kk->load('unitRumah:id,blok,nomor')], 201);
    }

    public function kepalaShow(int $id): JsonResponse
    {
        $kk = KepalaKeluarga::with([
            'unitRumah:id,blok,nomor',
            'user:id,nama,email,avatar_url',
            'anggota',
            'kendaraan',
        ])->findOrFail($id);

        return response()->json(['success' => true, 'data' => $kk]);
    }

    public function kepalaUpdate(Request $request, int $id): JsonResponse
    {
        $kk   = KepalaKeluarga::findOrFail($id);
        $body = $request->json()->all();

        if (!empty($body['nik']) && $body['nik'] !== $kk->nik &&
            KepalaKeluarga::where('nik', $body['nik'])->where('id', '!=', $id)->exists()) {
            return response()->json(['success' => false, 'message' => 'NIK sudah terdaftar.'], 422);
        }

        $kk->update(array_filter([
            'unit_rumah_id'    => $body['unit_rumah_id'] ?? $kk->unit_rumah_id,
            'no_kk'            => $body['no_kk'] ?? $kk->no_kk,
            'nama'             => $body['nama'] ?? $kk->nama,
            'nik'              => $body['nik'] ?? $kk->nik,
            'tempat_lahir'     => $body['tempat_lahir'] ?? $kk->tempat_lahir,
            'tanggal_lahir'    => $body['tanggal_lahir'] ?? $kk->tanggal_lahir,
            'jenis_kelamin'    => $body['jenis_kelamin'] ?? $kk->jenis_kelamin,
            'agama'            => $body['agama'] ?? $kk->agama,
            'pendidikan'       => $body['pendidikan'] ?? $kk->pendidikan,
            'pekerjaan'        => $body['pekerjaan'] ?? $kk->pekerjaan,
            'no_wa'            => $body['no_wa'] ?? $kk->no_wa,
            'status_perkawinan' => $body['status_perkawinan'] ?? $kk->status_perkawinan,
            'status_tinggal'   => $body['status_tinggal'] ?? $kk->status_tinggal,
            'keterangan'       => $body['keterangan'] ?? $kk->keterangan,
        ], fn($v) => $v !== null));

        $this->logActivity('update_kk', "Update KK: {$kk->nama}", 'KepalaKeluarga', $kk->id);

        return response()->json(['success' => true, 'data' => $kk->fresh('unitRumah:id,blok,nomor')]);
    }

    public function kepalaDestroy(int $id): JsonResponse
    {
        $kk = KepalaKeluarga::findOrFail($id);
        $this->logActivity('hapus_kk', "Hapus KK: {$kk->nama}", 'KepalaKeluarga', $kk->id);
        $kk->delete();

        return response()->json(['success' => true, 'message' => 'Data KK dihapus.']);
    }

    // ── Anggota Keluarga ──────────────────────────────────────────

    public function anggotaStore(Request $request, int $kkId): JsonResponse
    {
        $kk   = KepalaKeluarga::findOrFail($kkId);
        $body = $request->json()->all();

        if (empty($body['nama'])) {
            return response()->json(['success' => false, 'message' => 'Nama wajib diisi.'], 422);
        }
        if (empty($body['hubungan'])) {
            return response()->json(['success' => false, 'message' => 'Hubungan keluarga wajib diisi.'], 422);
        }

        if (!empty($body['nik']) && AnggotaKeluarga::where('nik', $body['nik'])->exists()) {
            return response()->json(['success' => false, 'message' => 'NIK sudah terdaftar.'], 422);
        }

        $anggota = AnggotaKeluarga::create([
            'kepala_keluarga_id' => $kk->id,
            'nama'               => $body['nama'],
            'nik'                => $body['nik'] ?? null,
            'tempat_lahir'       => $body['tempat_lahir'] ?? null,
            'tanggal_lahir'      => $body['tanggal_lahir'] ?? null,
            'jenis_kelamin'      => $body['jenis_kelamin'] ?? null,
            'agama'              => $body['agama'] ?? null,
            'pendidikan'         => $body['pendidikan'] ?? null,
            'pekerjaan'          => $body['pekerjaan'] ?? null,
            'hubungan'           => $body['hubungan'],
            'status_perkawinan'  => $body['status_perkawinan'] ?? null,
        ]);

        return response()->json(['success' => true, 'data' => $anggota], 201);
    }

    public function anggotaUpdate(Request $request, int $id): JsonResponse
    {
        $anggota = AnggotaKeluarga::findOrFail($id);
        $body    = $request->json()->all();

        if (!empty($body['nik']) && $body['nik'] !== $anggota->nik &&
            AnggotaKeluarga::where('nik', $body['nik'])->where('id', '!=', $id)->exists()) {
            return response()->json(['success' => false, 'message' => 'NIK sudah terdaftar.'], 422);
        }

        $anggota->update(array_intersect_key($body, array_flip([
            'nama', 'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
            'agama', 'pendidikan', 'pekerjaan', 'hubungan', 'status_perkawinan',
        ])));

        return response()->json(['success' => true, 'data' => $anggota->fresh()]);
    }

    public function anggotaDestroy(int $id): JsonResponse
    {
        AnggotaKeluarga::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Anggota dihapus.']);
    }

    // ── Kendaraan ─────────────────────────────────────────────────

    public function kendaraanIndex(Request $request): JsonResponse
    {
        $q = Kendaraan::with(['kepalaKeluarga:id,nama,unit_rumah_id', 'kepalaKeluarga.unitRumah:id,blok,nomor'])
            ->orderBy('created_at', 'desc');

        if ($search = $request->query('search')) {
            $q->where(function ($sub) use ($search) {
                $sub->where('plat_nomor', 'like', "%{$search}%")
                    ->orWhere('merek', 'like', "%{$search}%")
                    ->orWhereHas('kepalaKeluarga', fn($r) => $r->where('nama', 'like', "%{$search}%"));
            });
        }

        if ($jenis = $request->query('jenis')) {
            $q->where('jenis', $jenis);
        }

        return response()->json(['success' => true, 'data' => $q->get()]);
    }

    public function kendaraanStore(Request $request, int $kkId): JsonResponse
    {
        $kk   = KepalaKeluarga::findOrFail($kkId);
        $body = $request->json()->all();

        if (empty($body['jenis']) || empty($body['merek'])) {
            return response()->json(['success' => false, 'message' => 'Jenis dan merek wajib diisi.'], 422);
        }

        $kend = Kendaraan::create([
            'kepala_keluarga_id' => $kk->id,
            'jenis'              => $body['jenis'],
            'merek'              => $body['merek'],
            'model'              => $body['model'] ?? null,
            'warna'              => $body['warna'] ?? null,
            'plat_nomor'         => $body['plat_nomor'] ?? null,
            'tahun'              => $body['tahun'] ?? null,
        ]);

        return response()->json(['success' => true, 'data' => $kend], 201);
    }

    public function kendaraanUpdate(Request $request, int $id): JsonResponse
    {
        $kend = Kendaraan::findOrFail($id);
        $body = $request->json()->all();

        $kend->update(array_intersect_key($body, array_flip([
            'jenis', 'merek', 'model', 'warna', 'plat_nomor', 'tahun',
        ])));

        return response()->json(['success' => true, 'data' => $kend->fresh()]);
    }

    public function kendaraanDestroy(int $id): JsonResponse
    {
        Kendaraan::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Kendaraan dihapus.']);
    }

    // ── Unit Rumah CRUD ───────────────────────────────────────────

    public function storeUnit(Request $request): JsonResponse
    {
        $body  = $request->json()->all();
        $blok  = strtoupper(trim((string) ($body['blok'] ?? '')));
        $nomor = (int) ($body['nomor'] ?? 0);

        if (!preg_match('/^[A-Z]$/', $blok)) {
            return response()->json(['success' => false, 'message' => 'Blok tidak valid (harus huruf A–Z).'], 422);
        }
        if ($nomor < 1 || $nomor > 99) {
            return response()->json(['success' => false, 'message' => 'Nomor tidak valid (1–99).'], 422);
        }
        if (UnitRumah::where('blok', $blok)->where('nomor', $nomor)->exists()) {
            return response()->json(['success' => false, 'message' => "Unit Blok {$blok}-{$nomor} sudah ada."], 422);
        }

        $lat = isset($body['lat']) && $body['lat'] !== null ? (float) $body['lat'] : null;
        $lng = isset($body['lng']) && $body['lng'] !== null ? (float) $body['lng'] : null;

        $unit = UnitRumah::create([
            'user_id'    => null,
            'blok'       => $blok,
            'nomor'      => $nomor,
            'is_primary' => false,
            'lat'        => $lat,
            'lng'        => $lng,
        ]);

        $this->logActivity('tambah_unit', "Tambah unit: Blok {$blok}-{$nomor}", 'UnitRumah', $unit->id);

        return response()->json(['success' => true, 'data' => [
            'id'             => $unit->id,
            'label'          => "Blok {$blok}-{$nomor}",
            'blok'           => $unit->blok,
            'nomor'          => $unit->nomor,
            'lat'            => $unit->lat,
            'lng'            => $unit->lng,
            'has_pin'        => $unit->lat !== null && $unit->lng !== null,
            'has_warga'      => false,
            'kk_id'          => null,
            'kk_nama'        => null,
            'status_tinggal' => null,
        ]], 201);
    }

    // ── Peta Warga ────────────────────────────────────────────────

    public function mapData(): JsonResponse
    {
        $units = UnitRumah::with(['kepalaKeluarga:id,unit_rumah_id,nama,status_tinggal'])
            ->select('id', 'blok', 'nomor', 'lat', 'lng')
            ->orderBy('blok')
            ->orderBy('nomor')
            ->get()
            ->map(function (UnitRumah $u): array {
                $kk = $u->kepalaKeluarga->first();
                return [
                    'id'             => $u->id,
                    'label'          => "Blok {$u->blok}-{$u->nomor}",
                    'blok'           => $u->blok,
                    'nomor'          => $u->nomor,
                    'lat'            => $u->lat,
                    'lng'            => $u->lng,
                    'has_pin'        => $u->lat !== null && $u->lng !== null,
                    'has_warga'      => $kk !== null,
                    'kk_id'          => $kk?->id,
                    'kk_nama'        => $kk?->nama,
                    'status_tinggal' => $kk?->status_tinggal,
                ];
            });

        return response()->json(['success' => true, 'data' => $units]);
    }

    public function updateCoordinates(Request $request, int $id): JsonResponse
    {
        $unit = UnitRumah::findOrFail($id);
        $body = $request->json()->all();

        $lat = array_key_exists('lat', $body) ? ($body['lat'] !== null ? (float) $body['lat'] : null) : $unit->lat;
        $lng = array_key_exists('lng', $body) ? ($body['lng'] !== null ? (float) $body['lng'] : null) : $unit->lng;

        if ($lat !== null && ($lat < -90 || $lat > 90)) {
            return response()->json(['success' => false, 'message' => 'Latitude tidak valid.'], 422);
        }
        if ($lng !== null && ($lng < -180 || $lng > 180)) {
            return response()->json(['success' => false, 'message' => 'Longitude tidak valid.'], 422);
        }

        $unit->update(['lat' => $lat, 'lng' => $lng]);

        $this->logActivity(
            'update_koordinat',
            "Update koordinat: Blok {$unit->blok}-{$unit->nomor}",
            'UnitRumah',
            $unit->id
        );

        return response()->json(['success' => true, 'data' => ['id' => $unit->id, 'lat' => $unit->lat, 'lng' => $unit->lng]]);
    }

    // ── Unit Rumah list (untuk dropdown) ─────────────────────────

    public function unitList(): JsonResponse
    {
        $units = UnitRumah::orderBy('blok')->orderBy('nomor')
            ->select('id', 'blok', 'nomor')
            ->get()
            ->map(fn($u) => ['id' => $u->id, 'label' => "Blok {$u->blok}-{$u->nomor}"]);

        return response()->json(['success' => true, 'data' => $units]);
    }
}
