<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    public function send(string $targets, string $message, string $countryCode = '62'): array
    {
        $token = config('services.fonnte.token', '');

        if (!$token) {
            Log::warning('FonnteService: FONNTE_TOKEN not configured');
            return ['status' => false, 'reason' => 'Token not configured'];
        }

        try {
            $response = Http::withHeaders(['Authorization' => $token])
                ->post('https://api.fonnte.com/send', [
                    'target'      => $targets,
                    'message'     => $message,
                    'countryCode' => $countryCode,
                ]);

            $result = $response->json() ?? [];
            if (!($result['status'] ?? false)) {
                Log::warning('FonnteService: send failed', $result);
            }
            return $result;
        } catch (\Throwable $e) {
            Log::error('FonnteService: ' . $e->getMessage());
            return ['status' => false, 'reason' => $e->getMessage()];
        }
    }
}
