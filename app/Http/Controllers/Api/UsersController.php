<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\UnitRumah;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class UsersController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::with('units:user_id,blok,nomor,is_primary')
            ->select('id', 'email', 'nama', 'no_wa', 'role', 'profil_lengkap', 'avatar_url', 'created_at')
            ->orderByDesc('created_at')
            ->get();

        return Response::json(['success' => true, 'data' => $users]);
    }

    public function show(int $userId): JsonResponse
    {
        $user = User::with('units:user_id,blok,nomor,is_primary')
            ->select('id', 'email', 'nama', 'no_wa', 'role', 'profil_lengkap', 'avatar_url', 'created_at')
            ->findOrFail($userId);

        return Response::json(['success' => true, 'data' => $user]);
    }

    public function update(Request $request, int $userId): JsonResponse
    {
        $actor = auth()->user();

        // Must be sekretaris, admin, or super_admin
        if (!in_array($actor->role, ['sekretaris', 'admin', 'super_admin'], true)) {
            return Response::json(['success' => false, 'message' => 'Akses tidak diizinkan'], 403);
        }

        $user     = User::with('units')->findOrFail($userId);
        $actorIdx = array_search($actor->role, User::ROLES, true);
        $targetIdx = array_search($user->role, User::ROLES, true);

        // Hierarchy check: can only edit users strictly below your own level
        if ($actor->role !== 'super_admin' && $targetIdx >= $actorIdx) {
            return Response::json(['success' => false, 'message' => 'Tidak dapat mengedit pengguna setingkat atau lebih tinggi'], 403);
        }

        $nama  = trim((string) $request->input('nama', $user->nama ?? ''));
        $noWa  = trim((string) $request->input('no_wa', '')) ?: null;
        $units = $request->input('units');

        $errors = [];

        if (strlen($nama) < 2) {
            $errors['nama'] = 'Nama minimal 2 karakter.';
        }

        if ($units !== null) {
            if (!is_array($units) || count($units) === 0) {
                $errors['units'] = 'Minimal 1 unit rumah.';
            } elseif (count($units) > 5) {
                $errors['units'] = 'Maksimal 5 unit per akun.';
            } else {
                foreach ($units as $i => $unit) {
                    $blok  = strtoupper(trim((string) ($unit['blok'] ?? '')));
                    $nomor = (int) ($unit['nomor'] ?? 0);
                    if (!preg_match('/^[A-Z]$/', $blok)) {
                        $errors["units.{$i}.blok"] = 'Blok tidak valid.';
                    }
                    if ($nomor < 1 || $nomor > 99) {
                        $errors["units.{$i}.nomor"] = 'Nomor harus 1–99.';
                    }
                }
            }
        }

        if ($errors) {
            return Response::json(['success' => false, 'message' => 'Data tidak valid.', 'errors' => $errors], 422);
        }

        $oldNama  = $user->nama;
        $oldNoWa  = $user->no_wa;
        $oldUnits = $user->units->map(fn($u) => "Blok {$u->blok}/{$u->nomor}")->implode(', ');

        $user->update([
            'nama'            => $nama,
            'no_wa'           => $noWa,
            'profil_lengkap'  => $units !== null ? true : $user->profil_lengkap,
        ]);

        if ($units !== null) {
            UnitRumah::syncForUser($user->id, $units);
        }

        $changes = [];
        if ($oldNama !== $nama)   $changes[] = "nama: '{$oldNama}' → '{$nama}'";
        if ($oldNoWa !== $noWa)   $changes[] = "no_wa: '{$oldNoWa}' → '{$noWa}'";
        if ($units !== null) {
            $newUnits = $user->fresh()->load('units')->units->map(fn($u) => "Blok {$u->blok}/{$u->nomor}")->implode(', ');
            if ($oldUnits !== $newUnits) $changes[] = "unit: '{$oldUnits}' → '{$newUnits}'";
        }

        if ($changes) {
            AdminActivityLog::record(
                'update_user',
                "Edit data warga {$user->email}: " . implode('; ', $changes),
                User::class,
                $user->id,
                ['changes' => $changes]
            );
        }

        return Response::json([
            'success' => true,
            'message' => 'Data warga berhasil diperbarui.',
            'data'    => $user->fresh()->load('units'),
        ]);
    }
}
