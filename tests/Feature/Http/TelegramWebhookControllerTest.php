<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use Tests\TestCase;

final class TelegramWebhookControllerTest extends TestCase
{
    public function test_post_returns_ok_true(): void
    {
        $response = $this->postJson('/api/telegram/webhook/42', []);

        $response->assertStatus(200);
        $response->assertExactJson(['ok' => true]);
    }
}
