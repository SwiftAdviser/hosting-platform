<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Services\AgentDeployerService;
use App\Services\KiloClaw\KiloClawHttpClient;
use App\Services\OnChainOS\OnChainOSClient;
use App\Services\Telegram\TelegramHttpClient;
use Tests\TestCase;

final class DeployContainerResolutionTest extends TestCase
{
    public function test_container_resolves_onchainos_client(): void
    {
        $client = $this->app->make(OnChainOSClient::class);
        $this->assertInstanceOf(OnChainOSClient::class, $client);
    }

    public function test_container_resolves_kiloclaw_http_client(): void
    {
        $client = $this->app->make(KiloClawHttpClient::class);
        $this->assertInstanceOf(KiloClawHttpClient::class, $client);
    }

    public function test_container_resolves_telegram_http_client(): void
    {
        $client = $this->app->make(TelegramHttpClient::class);
        $this->assertInstanceOf(TelegramHttpClient::class, $client);
    }

    public function test_container_resolves_agent_deployer_service(): void
    {
        $service = $this->app->make(AgentDeployerService::class);
        $this->assertInstanceOf(AgentDeployerService::class, $service);
    }

    public function test_deploy_endpoint_does_not_500_with_default_bindings(): void
    {
        $response = $this->postJson('/api/deploys', [
            'agent_name' => 'probe',
            'personality' => 'terse',
            'telegram_bot_token' => '000000:invalid_but_well_formed',
            'amount_usd' => 10,
        ]);

        $this->assertNotSame(500, $response->status(), 'POST /api/deploys must not 500 under default container bindings');
    }

    public function test_deploy_endpoint_accepts_form_encoded_string_amount(): void
    {
        $this->bindWorkingFakes();

        $response = $this->post('/api/deploys', [
            'agent_name' => 'atlas',
            'personality' => 'helpful analyst',
            'telegram_bot_token' => '123456:ABCDEF',
            'amount_usd' => '10',
        ], [
            'Accept' => 'application/json',
        ]);

        $this->assertNotSame(422, $response->status(), 'String amount_usd from HTML form POST must not be rejected as invalid_request');
        $response->assertStatus(201);
        $response->assertJson(['status' => 'deployed']);
    }

    private function bindWorkingFakes(): void
    {
        $this->app->instance(OnChainOSClient::class, new class implements OnChainOSClient {
            public function createCharge(int $amountUsd, string $agentName, string $idempotencyKey): array
            {
                return ['session_id' => 'sess_form', 'status' => 'pending', 'expires_at' => null];
            }
        });

        $this->app->instance(KiloClawHttpClient::class, new class implements KiloClawHttpClient {
            public function install(array $manifest, string $idempotencyKey): array
            {
                return ['kiloclaw_id' => 'kc_form', 'status' => 'ready'];
            }
        });

        $this->app->instance(TelegramHttpClient::class, new class implements TelegramHttpClient {
            public function getMe(string $token): array
            {
                return ['ok' => true, 'result' => ['id' => 1]];
            }

            public function setWebhook(string $token, string $webhookUrl): array
            {
                return ['ok' => true];
            }
        });
    }
}
