<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Services\OnChainOS\WebhookSignatureVerifier;
use Tests\TestCase;

final class OnChainOSWebhookControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->bind(WebhookSignatureVerifier::class, fn () => new WebhookSignatureVerifier('test-secret'));
    }

    public function test_missing_signature_header_returns_400(): void
    {
        $response = $this->postJson('/api/webhooks/onchainos', ['event' => 'payment.settled']);

        $response->assertStatus(400);
        $response->assertJson(['status' => 'error', 'error' => 'missing signature']);
    }

    public function test_invalid_signature_returns_401(): void
    {
        $response = $this->postJson(
            '/api/webhooks/onchainos',
            ['event' => 'payment.settled'],
            ['X-OnChainOS-Signature' => 'not-the-secret'],
        );

        $response->assertStatus(401);
        $response->assertJson(['status' => 'error', 'error' => 'invalid signature']);
    }

    public function test_valid_signature_returns_200(): void
    {
        $response = $this->postJson(
            '/api/webhooks/onchainos',
            ['event' => 'payment.settled'],
            ['X-OnChainOS-Signature' => 'test-secret'],
        );

        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok']);
    }
}
