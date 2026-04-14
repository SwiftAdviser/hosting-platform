<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Services\AgentDeployerService;
use App\Services\KiloClaw\KiloClawHttpClient;
use App\Services\KiloClawClientService;
use App\Services\OnChainOS\OnChainOSClient;
use App\Services\OnChainOSPaymentService;
use App\Services\Telegram\TelegramHttpClient;
use App\Services\TelegramBotRegistrarService;
use Tests\TestCase;

final class DeployControllerTest extends TestCase
{
    private function validPayload(): array
    {
        return [
            'agent_name' => 'atlas',
            'personality' => 'helpful analyst',
            'telegram_bot_token' => '123456:ABCDEF',
            'amount_usd' => 10,
        ];
    }

    private function bindDeployer(
        OnChainOSClient $onchainos,
        KiloClawHttpClient $kiloclaw,
        TelegramHttpClient $telegram,
    ): void {
        $this->app->instance(AgentDeployerService::class, new AgentDeployerService(
            new OnChainOSPaymentService($onchainos),
            new KiloClawClientService($kiloclaw),
            new TelegramBotRegistrarService($telegram),
        ));
    }

    public function test_happy_path_deployed_returns_201(): void
    {
        $this->bindDeployer(
            new class implements OnChainOSClient {
                public function createCharge(int $amountUsd, string $agentName, string $idempotencyKey): array
                {
                    return [
                        'session_id' => 'sess_abc',
                        'status' => 'pending',
                        'expires_at' => '2026-04-14T10:00:00Z',
                    ];
                }
            },
            new class implements KiloClawHttpClient {
                public function install(array $manifest, string $idempotencyKey): array
                {
                    return ['kiloclaw_id' => 'kc_abc', 'status' => 'ready'];
                }
            },
            new class implements TelegramHttpClient {
                public function getMe(string $token): array
                {
                    return ['ok' => true, 'result' => ['id' => 1, 'username' => 'atlas_bot']];
                }

                public function setWebhook(string $token, string $webhookUrl): array
                {
                    return ['ok' => true, 'result' => true];
                }
            },
        );

        $response = $this->postJson('/api/deploys', $this->validPayload());

        $response->assertStatus(201);
        $response->assertJson([
            'status' => 'deployed',
            'stage' => 'complete',
            'agent_name' => 'atlas',
            'error' => null,
            'kiloclaw_id' => 'kc_abc',
            'session_id' => 'sess_abc',
        ]);
    }

    public function test_missing_personality_returns_422(): void
    {
        $this->bindDeployer(
            new class implements OnChainOSClient {
                public function createCharge(int $amountUsd, string $agentName, string $idempotencyKey): array
                {
                    return ['session_id' => 'unused', 'status' => 'pending'];
                }
            },
            new class implements KiloClawHttpClient {
                public function install(array $manifest, string $idempotencyKey): array
                {
                    return ['kiloclaw_id' => 'unused', 'status' => 'ready'];
                }
            },
            new class implements TelegramHttpClient {
                public function getMe(string $token): array
                {
                    return ['ok' => true, 'result' => ['id' => 99]];
                }

                public function setWebhook(string $token, string $webhookUrl): array
                {
                    return ['ok' => true];
                }
            },
        );

        $payload = $this->validPayload();
        unset($payload['personality']);

        $response = $this->postJson('/api/deploys', $payload);

        $response->assertStatus(422);
    }

    public function test_telegram_invalid_returns_422(): void
    {
        $this->bindDeployer(
            new class implements OnChainOSClient {
                public function createCharge(int $amountUsd, string $agentName, string $idempotencyKey): array
                {
                    return ['session_id' => 'sess_x', 'status' => 'pending'];
                }
            },
            new class implements KiloClawHttpClient {
                public function install(array $manifest, string $idempotencyKey): array
                {
                    return ['kiloclaw_id' => 'kc_x', 'status' => 'ready'];
                }
            },
            new class implements TelegramHttpClient {
                public function getMe(string $token): array
                {
                    return ['ok' => false];
                }

                public function setWebhook(string $token, string $webhookUrl): array
                {
                    return ['ok' => false];
                }
            },
        );

        $response = $this->postJson('/api/deploys', $this->validPayload());

        $response->assertStatus(422);
        $response->assertJson(['status' => 'telegram_invalid']);
    }

    public function test_payment_failed_returns_402(): void
    {
        $this->bindDeployer(
            new class implements OnChainOSClient {
                public function createCharge(int $amountUsd, string $agentName, string $idempotencyKey): array
                {
                    return ['session_id' => null, 'status' => 'failed'];
                }
            },
            new class implements KiloClawHttpClient {
                public function install(array $manifest, string $idempotencyKey): array
                {
                    return ['kiloclaw_id' => 'kc_x', 'status' => 'ready'];
                }
            },
            new class implements TelegramHttpClient {
                public function getMe(string $token): array
                {
                    return ['ok' => true, 'result' => ['id' => 2]];
                }

                public function setWebhook(string $token, string $webhookUrl): array
                {
                    return ['ok' => true];
                }
            },
        );

        $response = $this->postJson('/api/deploys', $this->validPayload());

        $response->assertStatus(402);
        $response->assertJson(['status' => 'payment_failed']);
    }

    public function test_install_failed_returns_502(): void
    {
        $this->bindDeployer(
            new class implements OnChainOSClient {
                public function createCharge(int $amountUsd, string $agentName, string $idempotencyKey): array
                {
                    return [
                        'session_id' => 'sess_ok',
                        'status' => 'pending',
                    ];
                }
            },
            new class implements KiloClawHttpClient {
                public function install(array $manifest, string $idempotencyKey): array
                {
                    return ['kiloclaw_id' => null, 'status' => 'failed'];
                }
            },
            new class implements TelegramHttpClient {
                public function getMe(string $token): array
                {
                    return ['ok' => true, 'result' => ['id' => 3]];
                }

                public function setWebhook(string $token, string $webhookUrl): array
                {
                    return ['ok' => true];
                }
            },
        );

        $response = $this->postJson('/api/deploys', $this->validPayload());

        $response->assertStatus(502);
        $response->assertJson(['status' => 'install_failed']);
    }
}
