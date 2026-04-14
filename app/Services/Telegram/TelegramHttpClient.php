<?php
declare(strict_types=1);

namespace App\Services\Telegram;

interface TelegramHttpClient
{
    /**
     * GET https://api.telegram.org/bot<token>/getMe
     *
     * @return array decoded JSON payload as an associative array
     * @throws TelegramTransportException on transport-level failure
     */
    public function getMe(string $token): array;

    /**
     * POST https://api.telegram.org/bot<token>/setWebhook with body { url }.
     *
     * @return array decoded JSON payload
     * @throws TelegramTransportException on transport-level failure
     */
    public function setWebhook(string $token, string $webhookUrl): array;
}
