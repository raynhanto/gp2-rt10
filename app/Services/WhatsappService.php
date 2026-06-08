<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    private string $token;
    private string $endpoint = 'https://api.fonnte.com/send';

    public function __construct()
    {
        $this->token = config('services.fonnte.token', '');
    }

    public function isEnabled(): bool
    {
        return !empty($this->token);
    }

    /** Send to a single number. Returns true on success. */
    public function send(string $noWa, string $message): bool
    {
        if (!$this->isEnabled()) return false;

        $number = $this->normalizeNumber($noWa);
        if (!$number) return false;

        try {
            $response = Http::withHeaders(['Authorization' => $this->token])
                ->asForm()
                ->post($this->endpoint, [
                    'target'      => $number,
                    'message'     => $message,
                    'countryCode' => '62',
                ]);

            if (!$response->successful()) {
                Log::warning('WhatsApp send failed', ['status' => $response->status(), 'body' => $response->body()]);
                return false;
            }

            $json = $response->json();
            return $json['status'] ?? false;
        } catch (\Throwable $e) {
            Log::error('WhatsApp send exception: ' . $e->getMessage());
            return false;
        }
    }

    /** Blast to multiple numbers. Returns count of successfully queued. */
    public function blast(array $numbers, string $message): int
    {
        if (!$this->isEnabled() || empty($numbers)) return 0;

        $normalized = array_filter(array_map([$this, 'normalizeNumber'], $numbers));
        if (empty($normalized)) return 0;

        // Fonnte supports comma-separated targets in one request
        $chunks = array_chunk($normalized, 50);
        $count  = 0;

        foreach ($chunks as $chunk) {
            try {
                $response = Http::withHeaders(['Authorization' => $this->token])
                    ->asForm()
                    ->post($this->endpoint, [
                        'target'      => implode(',', $chunk),
                        'message'     => $message,
                        'countryCode' => '62',
                    ]);

                if ($response->successful()) {
                    $count += count($chunk);
                } else {
                    Log::warning('WhatsApp blast chunk failed', ['status' => $response->status()]);
                }
            } catch (\Throwable $e) {
                Log::error('WhatsApp blast exception: ' . $e->getMessage());
            }
        }

        return $count;
    }

    private function normalizeNumber(string $no): ?string
    {
        $no = preg_replace('/\D/', '', $no);
        if (strlen($no) < 8) return null;
        // Convert 08xx → 628xx
        if (str_starts_with($no, '0')) $no = '62' . substr($no, 1);
        return $no;
    }
}
