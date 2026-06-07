<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donasi;
use App\Models\UnitRumah;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function updateProfile(Request $request): JsonResponse
    {
        $user  = Auth::user();
        $body  = $request->json()->all();
        $nama  = trim((string) ($body['nama'] ?? ''));
        $noWa  = trim((string) ($body['no_wa'] ?? '')) ?: null;
        $units = $body['units'] ?? [];

        if ($noWa !== null) {
            $noWa = $this->normalizePhone($noWa) ?: null;
        }

        $errors = [];
        if (strlen($nama) < 2) $errors['nama'] = 'Nama minimal 2 karakter.';
        if (empty($units))      $errors['units'] = 'Minimal 1 unit rumah.';
        if (count($units) > 5)  $errors['units'] = 'Maksimal 5 unit per akun.';

        foreach ($units as $i => $unit) {
            $blok  = strtoupper(trim((string) ($unit['blok'] ?? '')));
            $nomor = (int) ($unit['nomor'] ?? 0);
            if (!preg_match('/^[A-Z]$/', $blok))  $errors["units.{$i}.blok"] = 'Blok tidak valid.';
            if ($nomor < 1 || $nomor > 99)         $errors["units.{$i}.nomor"] = 'Nomor harus 1–99.';
            if (UnitRumah::isTaken($blok, $nomor, $user->id)) {
                $errors["units.{$i}"] = "Blok $blok No. $nomor sudah terdaftar ke akun lain.";
            }
        }

        if ($errors) {
            return response()->json(['success' => false, 'message' => 'Data tidak valid.', 'errors' => $errors], 422);
        }

        $user->update(['nama' => $nama, 'no_wa' => $noWa, 'profil_lengkap' => true]);
        UnitRumah::syncForUser($user->id, $units);

        return response()->json(['success' => true, 'message' => 'Profil berhasil disimpan.', 'data' => $user->fresh()->load('units')]);
    }

    public function myDonasi(): JsonResponse
    {
        $list = Donasi::with('kampanye:id,judul')
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($d) => array_merge($d->toArray(), [
                'judul' => $d->kampanye?->judul ?? 'Donasi Umum',
            ]));

        return response()->json(['success' => true, 'data' => $list]);
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[\s\-\.\(\)]+/', '', trim($phone));
        if (str_starts_with($phone, '+62')) $phone = '0' . substr($phone, 3);
        elseif (str_starts_with($phone, '62') && strlen($phone) > 5) $phone = '0' . substr($phone, 2);
        return preg_replace('/[^0-9]/', '', $phone);
    }
}
