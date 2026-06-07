<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GsheetConfig;
use App\Models\GsheetSyncLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class GsheetService
{
    private GsheetConfig $config;
    private ?array $cachedSheetNames = null;

    public function __construct()
    {
        $this->config = GsheetConfig::singleton();
    }

    public function isConfigured(): bool
    {
        return !empty($this->config->spreadsheet_id) && !empty($this->config->credentials_json);
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function getAccessToken(): string
    {
        $creds = json_decode($this->config->credentials_json, true);

        $header  = $this->base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $now     = time();
        $payload = $this->base64UrlEncode(json_encode([
            'iss'   => $creds['client_email'],
            'scope' => 'https://www.googleapis.com/auth/spreadsheets',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'iat'   => $now,
            'exp'   => $now + 3600,
        ]));

        $signingInput = "$header.$payload";
        $key = openssl_pkey_get_private($creds['private_key']);
        openssl_sign($signingInput, $signature, $key, OPENSSL_ALGO_SHA256);
        $jwt = "$signingInput." . $this->base64UrlEncode($signature);

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ]);

        if (!$response->ok()) {
            throw new \RuntimeException('Gagal mendapatkan token Google: ' . $response->body());
        }

        return $response->json('access_token');
    }

    private function getSheetNames(string $token): array
    {
        if ($this->cachedSheetNames !== null) {
            return $this->cachedSheetNames;
        }

        $sheetId  = $this->config->spreadsheet_id;
        $response = Http::withToken($token)
            ->get("https://sheets.googleapis.com/v4/spreadsheets/{$sheetId}?fields=sheets.properties.title");

        $this->cachedSheetNames = collect($response->json('sheets', []))
            ->pluck('properties.title')
            ->toArray();

        return $this->cachedSheetNames;
    }

    private function ensureTabExists(string $token, string $tab): void
    {
        if (in_array($tab, $this->getSheetNames($token), true)) {
            return;
        }

        $sheetId = $this->config->spreadsheet_id;
        Http::withToken($token)
            ->post("https://sheets.googleapis.com/v4/spreadsheets/{$sheetId}:batchUpdate", [
                'requests' => [
                    ['addSheet' => ['properties' => ['title' => $tab]]],
                ],
            ])->throw();

        $this->cachedSheetNames[] = $tab;
    }

    private function clearTab(string $token, string $tab): void
    {
        $sheetId = $this->config->spreadsheet_id;
        $range   = urlencode("{$tab}!A:ZZ");
        Http::withToken($token)
            ->post("https://sheets.googleapis.com/v4/spreadsheets/{$sheetId}/values/{$range}:clear");
    }

    private function writeRows(string $token, string $tab, array $rows): int
    {
        if (empty($rows)) return 0;

        $sheetId = $this->config->spreadsheet_id;
        $range   = urlencode("{$tab}!A1");

        Http::withToken($token)
            ->put("https://sheets.googleapis.com/v4/spreadsheets/{$sheetId}/values/{$range}?valueInputOption=USER_ENTERED", [
                'values' => $rows,
            ])->throw();

        return count($rows) - 1; // exclude header
    }

    public function syncTab(string $tabKey): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'Google Sheets belum dikonfigurasi.'];
        }

        $start = microtime(true);

        try {
            $token   = $this->getAccessToken();
            $tabName = match ($tabKey) {
                'kas'         => $this->config->tab_kas,
                'iuran'       => $this->config->tab_iuran,
                'pengeluaran' => $this->config->tab_pengeluaran,
                'donasi'      => $this->config->tab_donasi,
                'summary'     => $this->config->tab_summary,
                default       => throw new \InvalidArgumentException("Tab tidak dikenal: $tabKey"),
            };

            $rows  = $this->fetchRows($tabKey);
            $this->ensureTabExists($token, $tabName);
            $this->clearTab($token, $tabName);
            $count = $this->writeRows($token, $tabName, $rows);
            $ms    = (int) ((microtime(true) - $start) * 1000);

            GsheetSyncLog::create([
                'tab'          => $tabKey,
                'rows_written' => $count,
                'status'       => 'ok',
                'duration_ms'  => $ms,
            ]);

            return ['success' => true, 'rows' => $count, 'ms' => $ms];
        } catch (\Throwable $e) {
            $ms = (int) ((microtime(true) - $start) * 1000);
            GsheetSyncLog::create([
                'tab'         => $tabKey,
                'status'      => 'error',
                'error_msg'   => $e->getMessage(),
                'duration_ms' => $ms,
            ]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function syncAll(): array
    {
        $this->cachedSheetNames = null;
        $results = [];
        foreach (['kas', 'iuran', 'pengeluaran', 'donasi', 'summary'] as $tab) {
            $results[$tab] = $this->syncTab($tab);
        }

        $this->config->update(['last_synced_at' => now()]);

        return $results;
    }

    private function fetchRows(string $tabKey): array
    {
        return match ($tabKey) {
            'kas'         => $this->rowsKas(),
            'iuran'       => $this->rowsIuran(),
            'pengeluaran' => $this->rowsPengeluaran(),
            'donasi'      => $this->rowsDonasi(),
            'summary'     => $this->rowsSummary(),
            default       => [],
        };
    }

    private function rowsKas(): array
    {
        $headers = ['ID', 'Tanggal', 'Jenis', 'Kategori', 'Keterangan', 'Kampanye', 'Nominal', 'Dibuat Oleh'];
        $rows    = [['ID', 'Tanggal', 'Jenis', 'Kategori', 'Keterangan', 'Kampanye', 'Nominal (Rp)', 'Dibuat Oleh']];

        DB::table('kas as k')
            ->leftJoin('kategori_keuangan as kat', 'kat.id', '=', 'k.kategori_id')
            ->leftJoin('kampanye as ka', 'ka.id', '=', 'k.kampanye_id')
            ->leftJoin('users as u', 'u.id', '=', 'k.created_by')
            ->select('k.id', 'k.tanggal', 'k.created_at', 'k.jenis', 'kat.nama as kategori',
                     'k.keterangan', 'ka.judul as kampanye', 'k.nominal', 'u.nama as oleh')
            ->whereNull('k.deleted_at')
            ->orderByDesc('k.tanggal')
            ->orderByDesc('k.created_at')
            ->each(function ($r) use (&$rows) {
                $rows[] = [
                    $r->id,
                    $r->tanggal ?? substr($r->created_at, 0, 10),
                    $r->jenis,
                    $r->kategori ?? '',
                    $r->keterangan,
                    $r->kampanye ?? '',
                    $r->nominal,
                    $r->oleh ?? '',
                ];
            });

        return $rows;
    }

    private function rowsIuran(): array
    {
        $rows = [['ID Tagihan', 'Blok', 'Nomor', 'Bulan', 'Tahun', 'Nominal (Rp)',
                  'Status', 'Metode Bayar', 'Bayar Oleh', 'Tanggal Bayar']];

        DB::table('iuran_tagihan as t')
            ->join('iuran_periode as p', 'p.id', '=', 't.iuran_periode_id')
            ->join('unit_rumah as u', 'u.id', '=', 't.unit_rumah_id')
            ->leftJoin('iuran_bayar as b', function ($j) {
                $j->on('b.iuran_tagihan_id', '=', 't.id')->where('b.status', '=', 'verified');
            })
            ->leftJoin('users as usr', 'usr.id', '=', 'b.created_by')
            ->select('t.id', 'u.blok', 'u.nomor', 'p.bulan', 'p.tahun', 'p.nominal',
                     't.status', 'b.metode', 'usr.nama as bayar_oleh', 'b.updated_at as tgl_bayar')
            ->orderBy('p.tahun')->orderBy('p.bulan')->orderBy('u.blok')->orderBy('u.nomor')
            ->each(function ($r) use (&$rows) {
                $rows[] = [
                    $r->id, $r->blok, $r->nomor, $r->bulan, $r->tahun,
                    $r->nominal, $r->status, $r->metode ?? '', $r->bayar_oleh ?? '',
                    $r->tgl_bayar ? substr($r->tgl_bayar, 0, 10) : '',
                ];
            });

        return $rows;
    }

    private function rowsPengeluaran(): array
    {
        $rows = [['ID', 'Tanggal', 'Keterangan', 'Kategori', 'Kampanye', 'Pos Anggaran', 'Nominal (Rp)', 'Dibuat Oleh']];

        DB::table('pengeluaran as p')
            ->leftJoin('kategori_keuangan as kat', 'kat.id', '=', 'p.kategori_id')
            ->leftJoin('kampanye as k', 'k.id', '=', 'p.kampanye_id')
            ->leftJoin('anggaran as a', 'a.id', '=', 'p.anggaran_id')
            ->leftJoin('users as u', 'u.id', '=', 'p.created_by')
            ->select('p.id', 'p.tanggal', 'p.keterangan', 'kat.nama as kategori',
                     'k.judul', 'a.pos', 'p.nominal', 'u.nama as oleh')
            ->orderByDesc('p.tanggal')
            ->each(function ($r) use (&$rows) {
                $rows[] = [$r->id, $r->tanggal, $r->keterangan, $r->kategori ?? '',
                           $r->judul ?? '', $r->pos ?? '', $r->nominal, $r->oleh ?? ''];
            });

        return $rows;
    }

    private function rowsDonasi(): array
    {
        $rows = [['ID', 'Tanggal', 'Donatur', 'Kampanye', 'Nominal (Rp)', 'Metode', 'Status', 'Anonim']];

        DB::table('donasi as d')
            ->leftJoin('kampanye as k', 'k.id', '=', 'd.kampanye_id')
            ->join('users as u', 'u.id', '=', 'd.user_id')
            ->select('d.id', 'd.created_at', 'u.nama', 'k.judul', 'd.nominal', 'd.metode', 'd.status', 'd.is_anonym')
            ->orderByDesc('d.created_at')
            ->each(function ($r) use (&$rows) {
                $rows[] = [$r->id, substr($r->created_at, 0, 10),
                           $r->is_anonym ? 'Anonim' : $r->nama,
                           $r->judul ?? 'Donasi Umum', $r->nominal, $r->metode, $r->status,
                           $r->is_anonym ? 'Ya' : 'Tidak'];
            });

        return $rows;
    }

    private function rowsSummary(): array
    {
        $kas  = DB::table('kas')->whereNull('deleted_at')
            ->selectRaw("SUM(CASE WHEN jenis='masuk' THEN nominal ELSE 0 END) as masuk,
                         SUM(CASE WHEN jenis='keluar' THEN nominal ELSE 0 END) as keluar")
            ->first();

        $iuran = DB::table('iuran_tagihan')
            ->selectRaw("COUNT(*) as total,
                         SUM(CASE WHEN status='lunas' THEN 1 ELSE 0 END) as lunas,
                         SUM(CASE WHEN status='belum' THEN 1 ELSE 0 END) as belum")
            ->first();

        $saldo = ($kas->masuk ?? 0) - ($kas->keluar ?? 0);

        return [
            ['Ringkasan Keuangan RT 10 Golden Park 2', '', ''],
            ['Diperbarui', now()->format('d/m/Y H:i'), ''],
            ['', '', ''],
            ['KAS', '', ''],
            ['Total Masuk', $kas->masuk ?? 0, 'Rp'],
            ['Total Keluar', $kas->keluar ?? 0, 'Rp'],
            ['Saldo Kas', $saldo, 'Rp'],
            ['', '', ''],
            ['IURAN', '', ''],
            ['Total Tagihan', $iuran->total ?? 0, 'tagihan'],
            ['Lunas', $iuran->lunas ?? 0, 'tagihan'],
            ['Belum Bayar', $iuran->belum ?? 0, 'tagihan'],
        ];
    }
}
