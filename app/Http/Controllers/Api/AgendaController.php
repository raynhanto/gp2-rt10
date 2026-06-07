<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\AgendaReminderMail;
use App\Models\Agenda;
use App\Models\User;
use App\Services\FonnteService;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AgendaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $isRaw  = $request->boolean('raw', false);
        $filter = $request->query('filter', '');

        $q = Agenda::orderBy('tanggal');

        if ($request->query('kategori')) {
            $q->where('kategori', $request->query('kategori'));
        }

        // Visibility: what does the current user get to see?
        $user = Auth::user();
        if (!$user) {
            $q->where('target_audience', 'semua');
        } elseif (in_array($user->role, ['admin', 'super_admin'], true)) {
            // sees everything
        } elseif ($user->isAdmin()) {
            // sekretaris / bendahara: semua + staff + personal where included
            $uid = $user->id;
            $q->where(fn($sq) => $sq
                ->whereIn('target_audience', ['semua', 'staff'])
                ->orWhere(fn($sq2) => $sq2
                    ->where('target_audience', 'personal')
                    ->whereJsonContains('target_user_ids', $uid)
                )
            );
        } else {
            // warga: semua + personal where included
            $uid = $user->id;
            $q->where(fn($sq) => $sq
                ->where('target_audience', 'semua')
                ->orWhere(fn($sq2) => $sq2
                    ->where('target_audience', 'personal')
                    ->whereJsonContains('target_user_ids', $uid)
                )
            );
        }

        $agendaList = $q->get();
        $today = now()->toDateString();

        if ($isRaw) {
            // Return template rows (no expansion) for admin management
            $data = $agendaList->map(function (Agenda $a) use ($today) {
                $arr = $a->toArray();
                $arr['status'] = $a->status;
                return $arr;
            });

            if ($filter === 'mendatang') {
                $data = $data->filter(fn($a) => $a['tanggal'] >= $today)->values();
            } elseif ($filter === 'lalu') {
                $data = $data->filter(fn($a) => $a['tanggal'] < $today)->values();
            }

            return response()->json(['success' => true, 'data' => $data->values()]);
        }

        // Expand recurring events within a rolling window
        $windowFrom = now()->subMonths(3)->toDateString();
        $windowTo   = now()->addMonths(12)->toDateString();

        $expanded = [];
        foreach ($agendaList as $agenda) {
            foreach ($this->expandAgenda($agenda, $windowFrom, $windowTo, $today) as $occ) {
                $expanded[] = $occ;
            }
        }

        usort($expanded, fn($a, $b) => strcmp($a['tanggal'], $b['tanggal']));

        if ($filter === 'mendatang') {
            $expanded = array_values(array_filter($expanded, fn($e) => $e['tanggal'] >= $today));
        } elseif ($filter === 'lalu') {
            $expanded = array_values(array_filter($expanded, fn($e) => $e['tanggal'] < $today));
        }

        return response()->json(['success' => true, 'data' => $expanded]);
    }

    public function store(Request $request): JsonResponse
    {
        $body   = $request->json()->all();
        $errors = [];

        if (empty($body['judul']))   $errors['judul']   = 'Judul wajib diisi.';
        if (empty($body['tanggal'])) $errors['tanggal'] = 'Tanggal wajib diisi.';
        if ($errors) return response()->json(['success' => false, 'message' => 'Data tidak valid.', 'errors' => $errors], 422);

        $audience  = $body['target_audience'] ?? 'semua';
        $isPublic  = $audience === 'semua';
        $jadwal    = $body['jenis_jadwal'] ?? 'sekali';

        $agenda = Agenda::create([
            'judul'           => $body['judul'],
            'deskripsi'       => $body['deskripsi'] ?? null,
            'tanggal'         => $body['tanggal'],
            'waktu_mulai'     => $body['waktu_mulai'] ?? null,
            'waktu_selesai'   => $body['waktu_selesai'] ?? null,
            'lokasi'          => $body['lokasi'] ?? null,
            'kategori'        => $body['kategori'] ?? 'lainnya',
            'is_public'       => $isPublic,
            'jenis_jadwal'    => $jadwal,
            'hari_minggu'     => ($jadwal === 'mingguan' && !empty($body['hari_minggu'])) ? $body['hari_minggu'] : null,
            'tanggal_akhir'   => ($jadwal !== 'sekali' && !empty($body['tanggal_akhir'])) ? $body['tanggal_akhir'] : null,
            'target_audience' => $audience,
            'target_user_ids' => ($audience === 'personal' && !empty($body['target_user_ids'])) ? $body['target_user_ids'] : null,
            'created_by'      => Auth::id(),
        ]);

        if (!empty($body['kirim_wa'])) {
            $this->sendWa($agenda);
        }
        if (!empty($body['kirim_email'])) {
            $this->sendEmail($agenda);
        }

        return response()->json(['success' => true, 'message' => 'Agenda berhasil ditambahkan.', 'data' => $agenda], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $agenda = Agenda::find($id);
        if (!$agenda) return response()->json(['success' => false, 'message' => 'Agenda tidak ditemukan.'], 404);

        $body   = $request->json()->all();
        $errors = [];

        if (isset($body['judul'])   && empty($body['judul']))   $errors['judul']   = 'Judul wajib diisi.';
        if (isset($body['tanggal']) && empty($body['tanggal'])) $errors['tanggal'] = 'Tanggal wajib diisi.';
        if ($errors) return response()->json(['success' => false, 'message' => 'Data tidak valid.', 'errors' => $errors], 422);

        $audience = $body['target_audience'] ?? $agenda->target_audience;
        $jadwal   = $body['jenis_jadwal'] ?? $agenda->jenis_jadwal;

        $agenda->update([
            'judul'           => $body['judul'] ?? $agenda->judul,
            'deskripsi'       => $body['deskripsi'] ?? $agenda->deskripsi,
            'tanggal'         => $body['tanggal'] ?? $agenda->tanggal->toDateString(),
            'waktu_mulai'     => array_key_exists('waktu_mulai', $body) ? ($body['waktu_mulai'] ?: null) : $agenda->waktu_mulai,
            'waktu_selesai'   => array_key_exists('waktu_selesai', $body) ? ($body['waktu_selesai'] ?: null) : $agenda->waktu_selesai,
            'lokasi'          => array_key_exists('lokasi', $body) ? ($body['lokasi'] ?: null) : $agenda->lokasi,
            'kategori'        => $body['kategori'] ?? $agenda->kategori,
            'is_public'       => $audience === 'semua',
            'jenis_jadwal'    => $jadwal,
            'hari_minggu'     => ($jadwal === 'mingguan') ? ($body['hari_minggu'] ?? $agenda->hari_minggu) : null,
            'tanggal_akhir'   => ($jadwal !== 'sekali') ? ($body['tanggal_akhir'] ?? $agenda->tanggal_akhir?->toDateString()) : null,
            'target_audience' => $audience,
            'target_user_ids' => ($audience === 'personal') ? ($body['target_user_ids'] ?? $agenda->target_user_ids) : null,
        ]);

        return response()->json(['success' => true, 'message' => 'Agenda berhasil diperbarui.', 'data' => $agenda->fresh()]);
    }

    public function destroy(int $id): JsonResponse
    {
        $agenda = Agenda::find($id);
        if (!$agenda) return response()->json(['success' => false, 'message' => 'Agenda tidak ditemukan.'], 404);

        $agenda->delete();
        return response()->json(['success' => true, 'message' => 'Agenda berhasil dihapus.']);
    }

    public function sendReminder(Request $request, int $id): JsonResponse
    {
        $agenda = Agenda::find($id);
        if (!$agenda) return response()->json(['success' => false, 'message' => 'Agenda tidak ditemukan.'], 404);

        $channel = $request->json('channel', 'wa');

        if ($channel === 'wa') {
            $sent = $this->sendWa($agenda);
            $msg  = $sent > 0 ? "Reminder WA dikirim ke {$sent} penerima." : 'Tidak ada nomor WA yang terdaftar.';
            return response()->json(['success' => true, 'message' => $msg]);
        }

        if ($channel === 'email') {
            $sent = $this->sendEmail($agenda);
            $msg  = $sent > 0 ? "Reminder email dikirim ke {$sent} penerima." : 'Tidak ada email penerima.';
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return response()->json(['success' => false, 'message' => 'Channel tidak valid.'], 422);
    }

    // ── Private helpers ───────────────────────────────────────────

    private function expandAgenda(Agenda $agenda, string $from, string $to, string $today): array
    {
        $base = [
            'id'              => $agenda->id,
            'judul'           => $agenda->judul,
            'deskripsi'       => $agenda->deskripsi,
            'waktu_mulai'     => $agenda->waktu_mulai,
            'waktu_selesai'   => $agenda->waktu_selesai,
            'lokasi'          => $agenda->lokasi,
            'kategori'        => $agenda->kategori,
            'is_public'       => $agenda->is_public,
            'target_audience' => $agenda->target_audience,
            'jenis_jadwal'    => $agenda->jenis_jadwal ?? 'sekali',
            'created_by'      => $agenda->created_by,
        ];

        $jenis = $agenda->jenis_jadwal ?? 'sekali';

        if ($jenis === 'sekali') {
            $d = $agenda->tanggal->toDateString();
            if ($d >= $from && $d <= $to) {
                return [array_merge($base, ['tanggal' => $d, 'status' => $this->calcStatus($d, $today)])];
            }
            return [];
        }

        $startDate = max($from, $agenda->tanggal->toDateString());
        $endDate   = $agenda->tanggal_akhir
            ? min($to, $agenda->tanggal_akhir->toDateString())
            : $to;

        if ($startDate > $endDate) return [];

        $occurrences = [];

        if ($jenis === 'mingguan') {
            $targetDays = $agenda->hari_minggu ?? [];
            if (empty($targetDays)) return [];

            $cur = new DateTime($startDate);
            $end = new DateTime($endDate);
            while ($cur <= $end) {
                if (in_array((int) $cur->format('w'), $targetDays, true)) {
                    $d = $cur->format('Y-m-d');
                    $occurrences[] = array_merge($base, ['tanggal' => $d, 'status' => $this->calcStatus($d, $today)]);
                }
                $cur->modify('+1 day');
            }
        } else {
            $step       = $jenis === 'dua_bulanan' ? 2 : 1;
            $dayOfMonth = $agenda->tanggal->day;
            $ref        = Carbon::parse($agenda->tanggal->toDateString())->startOfMonth();

            // Advance ref to cover startDate
            while ($ref->copy()->day(min($dayOfMonth, $ref->daysInMonth))->toDateString() < $startDate) {
                $ref->addMonths($step);
            }

            $endCarbon = Carbon::parse($endDate);
            while ($ref->lte($endCarbon)) {
                $useDay = min($dayOfMonth, $ref->daysInMonth);
                $d      = $ref->copy()->day($useDay)->toDateString();
                if ($d >= $startDate && $d <= $endDate) {
                    $occurrences[] = array_merge($base, ['tanggal' => $d, 'status' => $this->calcStatus($d, $today)]);
                }
                $ref->addMonths($step);
            }
        }

        return $occurrences;
    }

    private function calcStatus(string $date, string $today): string
    {
        if ($date > $today) return 'akan_datang';
        if ($date === $today) return 'hari_ini';
        return 'selesai';
    }

    private function getTargetUsers(Agenda $agenda): \Illuminate\Support\Collection
    {
        $q = User::query();
        if ($agenda->target_audience === 'staff') {
            $q->whereIn('role', ['sekretaris', 'bendahara', 'admin', 'super_admin']);
        } elseif ($agenda->target_audience === 'personal' && !empty($agenda->target_user_ids)) {
            $q->whereIn('id', $agenda->target_user_ids);
        }
        return $q->get();
    }

    private function buildMessage(Agenda $agenda): string
    {
        $tgl   = Carbon::parse($agenda->tanggal)->locale('id')->isoFormat('dddd, D MMMM Y');
        $waktu = $agenda->waktu_mulai ? substr($agenda->waktu_mulai, 0, 5) : '';
        if ($waktu && $agenda->waktu_selesai) $waktu .= ' – ' . substr($agenda->waktu_selesai, 0, 5);

        $lines = [
            "📅 *Agenda RT 10 Golden Park 2*",
            "",
            "*{$agenda->judul}*",
            "📆 {$tgl}" . ($waktu ? "\n🕐 {$waktu}" : ''),
        ];
        if ($agenda->lokasi) $lines[] = "📍 {$agenda->lokasi}";
        if ($agenda->deskripsi) $lines[] = "\n{$agenda->deskripsi}";
        $lines[] = "\nInfo lebih lanjut di: https://gp2rt10.com/agenda";

        return implode("\n", $lines);
    }

    private function sendWa(Agenda $agenda): int
    {
        $users  = $this->getTargetUsers($agenda);
        $phones = $users->pluck('no_wa')->filter()->map(fn($p) => preg_replace('/\D/', '', $p))->filter()->values();

        if ($phones->isEmpty()) return 0;

        $fonnte = app(FonnteService::class);
        $fonnte->send($phones->implode(','), $this->buildMessage($agenda));

        return $phones->count();
    }

    private function sendEmail(Agenda $agenda): int
    {
        $users = $this->getTargetUsers($agenda)->filter(fn($u) => !empty($u->email));
        $count = 0;
        foreach ($users as $user) {
            try {
                Mail::to($user->email)->send(new AgendaReminderMail($agenda));
                $count++;
            } catch (\Throwable) {}
        }
        return $count;
    }
}
