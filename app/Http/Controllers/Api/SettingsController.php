<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Helpers\Upload;
use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => AppSetting::allPublic()]);
    }

    public function update(Request $request): JsonResponse
    {
        $allowed = ['bank_name', 'bank_account', 'bank_holder', 'whatsapp', 'rt_name', 'rt_address'];

        foreach ($allowed as $key) {
            if ($request->has($key)) {
                AppSetting::set($key, $request->input($key) ?: null);
            }
        }

        if ($request->hasFile('qris')) {
            try {
                $url = Upload::save($request->file('qris'), 'settings');
                AppSetting::set('qris_url', $url);
            } catch (\RuntimeException $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
        }

        return response()->json(['success' => true, 'message' => 'Pengaturan berhasil disimpan.', 'data' => AppSetting::allPublic()]);
    }
}
