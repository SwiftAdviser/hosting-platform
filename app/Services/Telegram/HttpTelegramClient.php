<?php

declare(strict_types=1);

namespace App\Services\Telegram;

use Illuminate\Support\Facades\Http;
use Throwable;

final class HttpTelegramClient implements TelegramHttpClient
{
    public function getMe(string $token): array
    {
        $trimmed = trim($token);
        if ($trimmed === '') {
            throw new TelegramTransportException('empty token');
        }

        try {
            $response = Http::timeout(10)
                ->acceptJson()
                ->get("https://api.telegram.org/bot{$trimmed}/getMe");
        } catch (Throwable $e) {
            throw new TelegramTransportException('getMe transport failure: '.$e->getMessage(), 0, $e);
        }

        $decoded = $response->json();
        if (!is_array($decoded)) {
            throw new TelegramTransportException('getMe non-json response');
        }

        return $decoded;
    }

    public function setWebhook(string $token, string $webhookUrl): array
    {
        $trimmed = trim($token);
        if ($trimmed === '') {
            throw new TelegramTransportException('empty token');
        }

        try {
            $response = Http::timeout(10)
                ->asForm()
                ->acceptJson()
                ->post("https://api.telegram.org/bot{$trimmed}/setWebhook", [
                    'url' => $webhookUrl,
                ]);
        } catch (Throwable $e) {
            throw new TelegramTransportException('setWebhook transport failure: '.$e->getMessage(), 0, $e);
        }

        $decoded = $response->json();
        if (!is_array($decoded)) {
            throw new TelegramTransportException('setWebhook non-json response');
        }

        return $decoded;
    }
}
