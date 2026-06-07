<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GsheetConfig;
use App\Models\GsheetSyncLog;
use App\Services\GsheetService;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GsheetController extends Controller
{
    use LogsAdminActivity;
    public function status(): JsonResponse
    {
        $config = GsheetConfig::singleton();
        $logs   = GsheetSyncLog::orderByDesc('created_at')->limit(20)->get();

        return response()->json([
            'success' => true,
            'config'  => [
                'spreadsheet_id'  => $config->spreadsheet_id,
                'tab_kas'         => $config->tab_kas,
                'tab_iuran'       => $config->tab_iuran,
                'tab_pengeluaran' => $config->tab_pengeluaran,
                'tab_donasi'      => $config->tab_donasi,
                'tab_summary'     => $config->tab_summary,
                'auto_sync'       => $config->auto_sync,
                'last_synced_at'  => $config->last_synced_at,
                'configured'      => !empty($config->spreadsheet_id) && !empty($config->credentials_json),
            ],
            'logs' => $logs,
        ]);
    }

    public function saveConfig(Request $request): JsonResponse
    {
        $body   = $request->json()->all();
        $config = GsheetConfig::singleton();

        $fields = ['spreadsheet_id', 'tab_kas', 'tab_iuran', 'tab_pengeluaran', 'tab_donasi', 'tab_summary', 'auto_sync'];
        $update = [];
        foreach ($fields as $f) {
            if (array_key_exists($f, $body)) $update[$f] = $body[$f];
        }

        $config->update($update);
        return response()->json(['success' => true, 'message' => 'Konfigurasi berhasil disimpan.']);
    }

    public function uploadCredentials(Request $request): JsonResponse
    {
        if (!$request->hasFile('credentials')) {
            return response()->json(['success' => false, 'message' => 'File credentials wajib diunggah.'], 422);
        }

        $file    = $request->file('credentials');
        $content = file_get_contents($file->getPathname());

        $json = json_decode($content, true);
        if (!$json || !isset($json['client_email'], $json['private_key'])) {
            return response()->json(['success' => false, 'message' => 'File bukan service account JSON yang valid.'], 422);
        }

        GsheetConfig::singleton()->update(['credentials_json' => $content]);
        return response()->json(['success' => true, 'message' => 'Credentials berhasil diunggah.', 'email' => $json['client_email']]);
    }

    public function syncTab(Request $request, string $tab): JsonResponse
    {
        $result = app(GsheetService::class)->syncTab($tab);
        $status = $result['success'] ? 200 : 422;
        return response()->json($result, $status);
    }

    public function syncAll(): JsonResponse
    {
        $results = app(GsheetService::class)->syncAll();
        $anyFail = collect($results)->contains(fn($r) => !$r['success']);
        $this->logActivity('gsheet_sync', 'Sync semua tab Google Sheets', context: ['results' => array_map(fn($r) => $r['success'], $results)]);
        return response()->json(['success' => !$anyFail, 'results' => $results]);
    }

    public function logs(): JsonResponse
    {
        $logs = GsheetSyncLog::orderByDesc('created_at')->limit(100)->get();
        return response()->json(['success' => true, 'data' => $logs]);
    }
}
