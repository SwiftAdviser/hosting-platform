<?php
declare(strict_types=1);

namespace Tests\Unit\Services\OnChainOS;

use App\Services\OnChainOS\OnChainOSException;
use App\Services\OnChainOS\XLayer\XLayerHttpException;
use App\Services\OnChainOS\XLayer\XLayerHttpTransport;
use App\Services\OnChainOS\XLayer\XLayerOnChainOSClient;
use PHPUnit\Framework\TestCase;

final class XLayerOnChainOSClientTest extends TestCase
{
    private function makeFake(): FakeXLayerHttpTransport
    {
        return new FakeXLayerHttpTransport();
    }

    private function makeClient(FakeXLayerHttpTransport $fake): XLayerOnChainOSClient
    {
        return new XLayerOnChainOSClient(
            $fake,
            'sk-api-key-REDACTED',
            'sk-secret-key-REDACTED',
            'passphrase-REDACTED',
        );
    }

    public function test_happy_path_returns_canonical_shape(): void
    {
        $fake = $this->makeFake();
        $fake->nextResponse = [
            'session_id' => 'sess_abc',
            'status' => 'pending',
            'expires_at' => '2026-04-14T10:00:00Z',
        ];
        $client = $this->makeClient($fake);

        $result = $client->createCharge(10, 'atlas', 'spawn-abc123');

        $this->assertSame([
            'session_id' => 'sess_abc',
            'status' => 'pending',
            'expires_at' => '2026-04-14T10:00:00Z',
        ], $result);
        $this->assertSame(1, $fake->callCount);
        $this->assertIsArray($fake->lastArgs);
        $headers = $fake->lastArgs[2];
        $this->assertArrayHasKey('Idempotency-Key', $headers);
        $this->assertSame('spawn-abc123', $headers['Idempotency-Key']);
    }

    public function test_transport_exception_translates_to_onchainos_exception(): void
    {
        $fake = $this->makeFake();
        $original = new XLayerHttpException('network down');
        $fake->nextException = $original;
        $client = $this->makeClient($fake);

        try {
            $client->createCharge(10, 'atlas', 'spawn-abc123');
            $this->fail('expected OnChainOSException');
        } catch (OnChainOSException $e) {
            $this->assertSame($original, $e->getPrevious());
            $this->assertStringNotContainsString('sk-api-key-REDACTED', $e->getMessage());
            $this->assertStringNotContainsString('sk-secret-key-REDACTED', $e->getMessage());
            $this->assertStringNotContainsString('passphrase-REDACTED', $e->getMessage());
        }
    }

    public function test_malformed_upstream_throws_with_malformed_marker(): void
    {
        $fake = $this->makeFake();
        $fake->nextResponse = ['status' => 'pending'];
        $client = $this->makeClient($fake);

        $this->expectException(OnChainOSException::class);
        $this->expectExceptionMessageMatches('/malformed/i');

        $client->createCharge(10, 'atlas', 'spawn-abc123');
    }

    public function test_auth_failure_throws_without_leaking_credentials(): void
    {
        $fake = $this->makeFake();
        $original = new XLayerHttpException('401 Unauthorized');
        $fake->nextException = $original;
        $client = $this->makeClient($fake);

        try {
            $client->createCharge(10, 'atlas', 'spawn-abc123');
            $this->fail('expected OnChainOSException');
        } catch (OnChainOSException $e) {
            $this->assertMatchesRegularExpression('/auth/i', $e->getMessage());
            $this->assertSame($original, $e->getPrevious());

            $chain = $e;
            while ($chain !== null) {
                $msg = $chain->getMessage();
                $this->assertStringNotContainsString('sk-api-key-REDACTED', $msg);
                $this->assertStringNotContainsString('sk-secret-key-REDACTED', $msg);
                $this->assertStringNotContainsString('passphrase-REDACTED', $msg);
                $chain = $chain->getPrevious();
            }
        }
    }
}

final class FakeXLayerHttpTransport implements XLayerHttpTransport
{
    public int $callCount = 0;
    public array $lastArgs = [];
    public array $nextResponse = [];
    public ?\Throwable $nextException = null;

    public function post(string $path, array $body, array $headers): array
    {
        $this->callCount++;
        $this->lastArgs = [$path, $body, $headers];
        if ($this->nextException !== null) {
            throw $this->nextException;
        }
        return $this->nextResponse;
    }
}
