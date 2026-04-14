<?php
declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\AgentDeployerService;
use App\Services\KiloClaw\KiloClawHttpClient;
use App\Services\KiloClawClientService;
use App\Services\OnChainOS\OnChainOSClient;
use App\Services\OnChainOSPaymentService;
use App\Services\Telegram\TelegramHttpClient;
use App\Services\TelegramBotRegistrarService;
use PHPUnit\Framework\TestCase;

final class AgentDeployerServiceTest extends TestCase
{
    /**
     * @return array{0: FakeAgentDeployerOnChainOSClient, 1: FakeAgentDeployerKiloClawHttpClient, 2: FakeAgentDeployerTelegramHttpClient, 3: AgentDeployerService}
     */
    private function makeStack(): array
    {
        $onchainosFake = new FakeAgentDeployerOnChainOSClient();
        $kiloclawFake = new FakeAgentDeployerKiloClawHttpClient();
        $telegramFake = new FakeAgentDeployerTelegramHttpClient();

        $payment = new OnChainOSPaymentService($onchainosFake);
        $kiloclaw = new KiloClawClientService($kiloclawFake);
        $telegram = new TelegramBotRegistrarService($telegramFake);

        $deployer = new AgentDeployerService($payment, $kiloclaw, $telegram);

        return [$onchainosFake, $kiloclawFake, $telegramFake, $deployer];
    }

    private function validRequest(): array
    {
        return [
            'agent_name' => 'atlas',
            'personality' => 'A laconic agent that ships code.',
            'telegram_bot_token' => '123:abc',
            'amount_usd' => 10,
            'allowlist' => '',
        ];
    }

    public function test_missing_required_field_returns_invalid_request(): void
    {
        [$onchainosFake, $kiloclawFake, $telegramFake, $deployer] = $this->makeStack();

        $request = $this->validRequest();
        unset($request['personality']);

        $result = $deployer->deploy($request);

        $this->assertSame('invalid_request', $result['status']);
        $this->assertSame('validate', $result['stage']);
        $this->assertSame('atlas', $result['agent_name']);
        $this->assertStringContainsString('personality', $result['error']);
        $this->assertNull($result['kiloclaw_id']);
        $this->assertNull($result['session_id']);
        $this->assertSame(0, $onchainosFake->callCount);
        $this->assertSame(0, $kiloclawFake->callCount);
        $this->assertSame(0, $telegramFake->getMeCallCount);
    }

    public function test_empty_agent_name_returns_invalid_request(): void
    {
        [$onchainosFake, $kiloclawFake, $telegramFake, $deployer] = $this->makeStack();

        $request = $this->validRequest();
        $request['agent_name'] = '';

        $result = $deployer->deploy($request);

        $this->assertSame('invalid_request', $result['status']);
        $this->assertSame('validate', $result['stage']);
        $this->assertStringContainsString('agent_name', $result['error']);
        $this->assertNull($result['kiloclaw_id']);
        $this->assertNull($result['session_id']);
        $this->assertSame(0, $onchainosFake->callCount);
        $this->assertSame(0, $kiloclawFake->callCount);
        $this->assertSame(0, $telegramFake->getMeCallCount);
    }

    public function test_zero_amount_returns_invalid_request(): void
    {
        [$onchainosFake, $kiloclawFake, $telegramFake, $deployer] = $this->makeStack();

        $request = $this->validRequest();
        $request['amount_usd'] = 0;

        $result = $deployer->deploy($request);

        $this->assertSame('invalid_request', $result['status']);
        $this->assertSame('validate', $result['stage']);
        $this->assertStringContainsString('amount_usd', $result['error']);
        $this->assertNull($result['kiloclaw_id']);
        $this->assertNull($result['session_id']);
        $this->assertSame(0, $onchainosFake->callCount);
        $this->assertSame(0, $kiloclawFake->callCount);
        $this->assertSame(0, $telegramFake->getMeCallCount);
    }

    public function test_telegram_token_invalid_short_circuits_at_telegram_validate(): void
    {
        [$onchainosFake, $kiloclawFake, $telegramFake, $deployer] = $this->makeStack();
        $telegramFake->getMeNextResponse = ['ok' => false, 'error_code' => 401];

        $result = $deployer->deploy($this->validRequest());

        $this->assertSame('telegram_invalid', $result['status']);
        $this->assertSame('telegram_validate', $result['stage']);
        $this->assertSame('atlas', $result['agent_name']);
        $this->assertSame('telegram token invalid', $result['error']);
        $this->assertNull($result['kiloclaw_id']);
        $this->assertNull($result['session_id']);
        $this->assertSame(1, $telegramFake->getMeCallCount);
        $this->assertSame(0, $onchainosFake->callCount);
        $this->assertSame(0, $kiloclawFake->callCount);
    }

    public function test_payment_not_pending_short_circuits_at_payment(): void
    {
        [$onchainosFake, $kiloclawFake, $telegramFake, $deployer] = $this->makeStack();
        $telegramFake->getMeNextResponse = ['ok' => true, 'result' => ['id' => 1]];
        $onchainosFake->nextException = new \App\Services\OnChainOS\OnChainOSException('transport boom');

        $result = $deployer->deploy($this->validRequest());

        $this->assertSame('payment_failed', $result['status']);
        $this->assertSame('payment', $result['stage']);
        $this->assertSame('atlas', $result['agent_name']);
        $this->assertStringContainsString('transport boom', $result['error']);
        $this->assertNull($result['kiloclaw_id']);
        $this->assertNull($result['session_id']);
        $this->assertSame(1, $telegramFake->getMeCallCount);
        $this->assertSame(1, $onchainosFake->callCount);
        $this->assertSame(0, $kiloclawFake->callCount);
    }

    public function test_install_not_ready_short_circuits_at_install(): void
    {
        [$onchainosFake, $kiloclawFake, $telegramFake, $deployer] = $this->makeStack();
        $telegramFake->getMeNextResponse = ['ok' => true, 'result' => ['id' => 1]];
        $onchainosFake->nextResponse = [
            'session_id' => 'sess_abc',
            'status' => 'pending',
            'expires_at' => '2026-04-14T10:00:00Z',
        ];
        $kiloclawFake->nextException = new \App\Services\KiloClaw\KiloClawException('transport boom');

        $result = $deployer->deploy($this->validRequest());

        $this->assertSame('install_failed', $result['status']);
        $this->assertSame('install', $result['stage']);
        $this->assertSame('atlas', $result['agent_name']);
        $this->assertStringContainsString('transport boom', $result['error']);
        $this->assertSame('sess_abc', $result['session_id']);
        $this->assertSame(1, $telegramFake->getMeCallCount);
        $this->assertSame(1, $onchainosFake->callCount);
        $this->assertSame(1, $kiloclawFake->callCount);
    }

    public function test_happy_path_deployed_returns_canonical_shape(): void
    {
        [$onchainosFake, $kiloclawFake, $telegramFake, $deployer] = $this->makeStack();
        $telegramFake->getMeNextResponse = ['ok' => true, 'result' => ['id' => 1]];
        $onchainosFake->nextResponse = [
            'session_id' => 'sess_abc',
            'status' => 'pending',
            'expires_at' => '2026-04-14T10:00:00Z',
        ];
        $kiloclawFake->nextResponse = [
            'kiloclaw_id' => 'kc_abc',
            'status' => 'ready',
        ];

        $request = [
            'agent_name' => 'atlas',
            'personality' => 'A laconic agent that ships code.',
            'telegram_bot_token' => '123:abc',
            'amount_usd' => 10,
            'allowlist' => '',
        ];

        $result = $deployer->deploy($request);

        $expected = [
            'status' => 'deployed',
            'stage' => 'complete',
            'agent_name' => 'atlas',
            'error' => null,
            'kiloclaw_id' => 'kc_abc',
            'session_id' => 'sess_abc',
        ];

        $this->assertSame($expected, $result);
        $this->assertSame(1, $telegramFake->getMeCallCount);
        $this->assertSame(1, $onchainosFake->callCount);
        $this->assertSame(1, $kiloclawFake->callCount);
    }
}

final class FakeAgentDeployerOnChainOSClient implements OnChainOSClient
{
    public int $callCount = 0;
    public array $lastArgs = [];
    public array $nextResponse = [];
    public ?\Throwable $nextException = null;

    public function createCharge(int $amountUsd, string $agentName, string $idempotencyKey): array
    {
        $this->callCount++;
        $this->lastArgs = [$amountUsd, $agentName, $idempotencyKey];
        if ($this->nextException !== null) {
            throw $this->nextException;
        }
        return $this->nextResponse;
    }
}

final class FakeAgentDeployerKiloClawHttpClient implements KiloClawHttpClient
{
    public int $callCount = 0;
    public array $lastArgs = [];
    public array $nextResponse = [];
    public ?\Throwable $nextException = null;

    public function install(array $manifest, string $idempotencyKey): array
    {
        $this->callCount++;
        $this->lastArgs = [$manifest, $idempotencyKey];
        if ($this->nextException !== null) {
            throw $this->nextException;
        }
        return $this->nextResponse;
    }
}

final class FakeAgentDeployerTelegramHttpClient implements TelegramHttpClient
{
    public int $getMeCallCount = 0;
    public array $getMeNextResponse = [];
    public ?\Throwable $getMeNextException = null;

    public int $setWebhookCallCount = 0;
    public array $setWebhookNextResponse = [];
    public ?\Throwable $setWebhookNextException = null;

    public function getMe(string $token): array
    {
        $this->getMeCallCount++;
        if ($this->getMeNextException !== null) {
            throw $this->getMeNextException;
        }
        return $this->getMeNextResponse;
    }

    public function setWebhook(string $token, string $webhookUrl): array
    {
        $this->setWebhookCallCount++;
        if ($this->setWebhookNextException !== null) {
            throw $this->setWebhookNextException;
        }
        return $this->setWebhookNextResponse;
    }
}
