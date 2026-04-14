<?php
declare(strict_types=1);

namespace App\Services;

use App\Services\Telegram\TelegramHttpClient;
use App\Services\Telegram\TelegramTransportException;

final class TelegramBotRegistrarService
{
    public function __construct(
        private readonly TelegramHttpClient $client,
    ) {
    }

    public function validateToken(string $token): bool
    {
        if (trim($token) === '') {
            return false;
        }

        try {
            $response = $this->client->getMe($token);
        } catch (TelegramTransportException) {
            return false;
        }

        return ($response['ok'] ?? false) === true;
    }

    public function registerWebhook(string $token, string $webhookUrl): array
    {
        $trimmedToken = trim($token);
        if ($trimmedToken === '') {
            return [
                'status' => 'invalid',
                'webhook_url' => null,
                'error' => 'empty token',
            ];
        }

        if (!str_starts_with($webhookUrl, 'https://')) {
            return [
                'status' => 'invalid',
                'webhook_url' => null,
                'error' => 'webhook url must start with https',
            ];
        }

        $host = substr($webhookUrl, 8);
        $slash = strpos($host, '/');
        if ($slash !== false) {
            $host = substr($host, 0, $slash);
        }
        if ($host === '' || strpos($host, '.') === false) {
            return [
                'status' => 'invalid',
                'webhook_url' => null,
                'error' => 'webhook url host invalid',
            ];
        }

        try {
            $response = $this->client->setWebhook($token, $webhookUrl);
        } catch (TelegramTransportException $e) {
            return [
                'status' => 'failed',
                'webhook_url' => $webhookUrl,
                'error' => $e->getMessage(),
            ];
        }

        if (($response['ok'] ?? false) === true) {
            return [
                'status' => 'registered',
                'webhook_url' => $webhookUrl,
                'error' => null,
            ];
        }

        return [
            'status' => 'failed',
            'webhook_url' => $webhookUrl,
            'error' => $response['description'] ?? 'setWebhook rejected',
        ];
    }
}
