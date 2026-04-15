<?php

declare(strict_types=1);

namespace App\Services\Telegram;

final class DemoTelegramHttpClient implements TelegramHttpClient
{
    public function getMe(string $token): array
    {
        return [
            'ok' => true,
            'result' => [
                'id' => 0,
                'is_bot' => true,
                'username' => 'demo_bot',
                'first_name' => 'Demo',
            ],
        ];
    }

    public function setWebhook(string $token, string $webhookUrl): array
    {
        return [
            'ok' => true,
            'result' => true,
            'description' => 'demo webhook accepted',
        ];
    }
}
