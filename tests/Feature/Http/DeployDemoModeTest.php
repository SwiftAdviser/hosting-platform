<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Services\KiloClaw\KiloClawHttpClient;
use App\Services\OnChainOS\OnChainOSClient;
use App\Services\Telegram\TelegramHttpClient;
use Tests\TestCase;

final class DeployDemoModeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['services.demo.enabled' => true]);
        $this->app->forgetInstance(OnChainOSClient::class);
        $this->app->forgetInstance(KiloClawHttpClient::class);
        $this->app->forgetInstance(TelegramHttpClient::class);
    }

    public function test_demo_mode_completes_deploy_with_fake_token_and_empty_config(): void
    {
        $response = $this->postJson('/api/deploys', [
            'agent_name' => 'demo-atlas',
            'personality' => 'terse',
            'telegram_bot_token' => '000000:fake_demo_token',
            'amount_usd' => 10,
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'status' => 'deployed',
            'stage' => 'complete',
            'agent_name' => 'demo-atlas',
            'error' => null,
        ]);

        $json = $response->json();
        $this->assertIsString($json['kiloclaw_id']);
        $this->assertNotSame('', $json['kiloclaw_id']);
        $this->assertIsString($json['session_id']);
        $this->assertNotSame('', $json['session_id']);
    }

    public function test_demo_mode_binds_demo_clients(): void
    {
        $onchainos = $this->app->make(OnChainOSClient::class);
        $kiloclaw = $this->app->make(KiloClawHttpClient::class);
        $telegram = $this->app->make(TelegramHttpClient::class);

        $this->assertStringContainsString('Demo', $onchainos::class);
        $this->assertStringContainsString('Demo', $kiloclaw::class);
        $this->assertStringContainsString('Demo', $telegram::class);
    }

    public function test_demo_mode_disabled_falls_back_to_http_clients(): void
    {
        config(['services.demo.enabled' => false]);
        $this->app->forgetInstance(OnChainOSClient::class);
        $this->app->forgetInstance(KiloClawHttpClient::class);
        $this->app->forgetInstance(TelegramHttpClient::class);

        $onchainos = $this->app->make(OnChainOSClient::class);
        $kiloclaw = $this->app->make(KiloClawHttpClient::class);
        $telegram = $this->app->make(TelegramHttpClient::class);

        $this->assertStringContainsString('Http', $telegram::class);
        $this->assertStringContainsString('Http', $kiloclaw::class);
        $this->assertStringContainsString('XLayer', $onchainos::class);
    }
}
