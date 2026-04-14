<?php
declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\OnChainOS\OnChainOSClient;
use App\Services\OnChainOS\OnChainOSException;
use App\Services\OnChainOSPaymentService;
use PHPUnit\Framework\TestCase;

final class OnChainOSPaymentServiceTest extends TestCase
{
    private function makeFake(): FakeOnChainOSClient
    {
        return new FakeOnChainOSClient();
    }

    public function test_empty_agent_name_returns_invalid_and_does_not_call_client(): void
    {
        $fake = $this->makeFake();
        $service = new OnChainOSPaymentService($fake);

        $result = $service->createCharge(10, '');

        $this->assertSame('invalid', $result['status']);
        $this->assertSame(0, $fake->callCount);
    }

    public function test_whitespace_only_agent_name_returns_invalid_and_does_not_call_client(): void
    {
        $fake = $this->makeFake();
        $service = new OnChainOSPaymentService($fake);

        $result = $service->createCharge(10, '   ');

        $this->assertSame('invalid', $result['status']);
        $this->assertSame(0, $fake->callCount);
    }

    public function test_zero_amount_returns_invalid_and_does_not_call_client(): void
    {
        $fake = $this->makeFake();
        $service = new OnChainOSPaymentService($fake);

        $result = $service->createCharge(0, 'spawn-bot');

        $this->assertSame('invalid', $result['status']);
        $this->assertSame(0, $fake->callCount);
    }

    public function test_negative_amount_returns_invalid_and_does_not_call_client(): void
    {
        $fake = $this->makeFake();
        $service = new OnChainOSPaymentService($fake);

        $result = $service->createCharge(-5, 'spawn-bot');

        $this->assertSame('invalid', $result['status']);
        $this->assertSame(0, $fake->callCount);
    }

    public function test_happy_path_returns_canonical_shape(): void
    {
        $fake = $this->makeFake();
        $fake->nextResponse = [
            'session_id' => 'sess_abc',
            'status' => 'pending',
            'expires_at' => '2026-04-14T10:00:00Z',
        ];
        $service = new OnChainOSPaymentService($fake);

        $result = $service->createCharge(10, 'spawn-bot');

        $this->assertSame('pending', $result['status']);
        $this->assertSame('sess_abc', $result['session_id']);
        $this->assertSame(10, $result['amount_usd']);
        $this->assertSame('spawn-bot', $result['agent_name']);
        $this->assertSame('2026-04-14T10:00:00Z', $result['expires_at']);
        $this->assertSame(1, $fake->callCount);
    }

    public function test_client_exception_returns_failed_without_propagating(): void
    {
        $fake = $this->makeFake();
        $fake->nextException = new OnChainOSException('transport boom');
        $service = new OnChainOSPaymentService($fake);

        try {
            $result = $service->createCharge(10, 'spawn-bot');
        } catch (\Throwable $e) {
            $this->fail('exception leaked: ' . $e->getMessage());
        }

        $this->assertSame('failed', $result['status']);
        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('transport boom', $result['error']);
    }

    public function test_malformed_response_without_session_id_returns_failed(): void
    {
        $fake = $this->makeFake();
        $fake->nextResponse = ['status' => 'pending'];
        $service = new OnChainOSPaymentService($fake);

        $result = $service->createCharge(10, 'spawn-bot');

        $this->assertSame('failed', $result['status']);
        $this->assertArrayHasKey('error', $result);
    }

    public function test_idempotency_key_is_stable_across_two_calls_same_utc_day(): void
    {
        $fake = $this->makeFake();
        $fake->nextResponse = [
            'session_id' => 'sess_abc',
            'status' => 'pending',
            'expires_at' => '2026-04-14T10:00:00Z',
        ];
        $service = new OnChainOSPaymentService($fake);

        $service->createCharge(10, 'spawn-bot');
        $firstKey = $fake->lastArgs[2];

        $service->createCharge(10, 'spawn-bot');
        $secondKey = $fake->lastArgs[2];

        $this->assertSame($firstKey, $secondKey);
        $this->assertStringStartsWith('spawn-', $firstKey);
        $this->assertSame(2, $fake->callCount);
    }
}

final class FakeOnChainOSClient implements OnChainOSClient
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
