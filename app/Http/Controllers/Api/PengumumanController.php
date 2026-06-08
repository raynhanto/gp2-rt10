<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\PengumumanMail;
use App\Models\Pengumuman;
use App\Models\User;
use App\Services\WhatsappService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

        $target  = $body['target'] ?? 'semua';
        $kirimWa = !empty($body['kirim_wa']);
        $kirimEmail = !empty($body['kirim_email']);

        $pengumuman = Pengumuman::create([
            'judul'      => $body['judul'],
            'isi'        => $body['isi'],
            'target'     => $target,
            'created_by' => Auth::id(),
        ]);

        $waCount    = 0;
        $emailCount = 0;

        if ($kirimWa || $kirimEmail) {
            $users = $this->targetUsers($target);

            if ($kirimEmail) {
                foreach ($users as $user) {
                    if (!$user->email) continue;
                    try {
                        Mail::to($user->email)->send(new PengumumanMail($pengumuman));
                        $emailCount++;
                    } catch (\Throwable $e) {
                        Log::warning("Pengumuman email fail [{$user->email}]: " . $e->getMessage());
                    }
                }
            }

            if ($kirimWa) {
                $numbers = $users->pluck('no_wa')->filter()->values()->all();
                if (!empty($numbers)) {
                    $waCount = app(WhatsappService::class)->blast(
                        $numbers,
                        "*{$pengumuman->judul}*\n\n{$pengumuman->isi}\n\n-- RT 10 Golden Park 2"
                    );
                }
            }
        }

        $extra = [];
        if ($kirimEmail) $extra[] = "{$emailCount} email terkirim";
        if ($kirimWa)    $extra[] = "{$waCount} WA terkirim";

        $msg = 'Pengumuman berhasil dikirim.' . (count($extra) ? ' (' . implode(', ', $extra) . ')' : '');

        return response()->json(['success' => true, 'message' => $msg], 201);
    }

    private function targetUsers(string $target): \Illuminate\Support\Collection
    {
        $q = User::whereNotNull('email')->where('profil_lengkap', true);
        // 'donatur' = users who have at least one verified donasi
        if ($target === 'donatur') {
            $q->whereExists(fn($s) =>
                $s->from('donasi')->whereColumn('donasi.user_id', 'users.id')->where('donasi.status', 'verified')
            );
        }
        return $q->get(['id', 'email', 'no_wa', 'nama']);
    }
}
