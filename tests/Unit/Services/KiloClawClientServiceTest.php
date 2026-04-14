<?php
declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\KiloClaw\KiloClawException;
use App\Services\KiloClaw\KiloClawHttpClient;
use App\Services\KiloClawClientService;
use PHPUnit\Framework\TestCase;

final class KiloClawClientServiceTest extends TestCase
{
    private function makeFake(): FakeKiloClawHttpClient
    {
        return new FakeKiloClawHttpClient();
    }

    private function canonicalManifest(): array
    {
        return [
            'id' => 'plugin.atlas',
            'name' => 'atlas',
            'version' => '0.1.0',
            'skills' => [],
        ];
    }

    public function test_manifest_missing_id_returns_invalid_and_does_not_call_client(): void
    {
        $fake = $this->makeFake();
        $service = new KiloClawClientService($fake);

        $result = $service->install([
            'name' => 'atlas',
            'version' => '0.1.0',
            'skills' => [],
        ]);

        $this->assertSame('invalid', $result['status']);
        $this->assertSame(0, $fake->callCount);
    }

    public function test_manifest_missing_name_returns_invalid_and_does_not_call_client(): void
    {
        $fake = $this->makeFake();
        $service = new KiloClawClientService($fake);

        $result = $service->install([
            'id' => 'plugin.atlas',
            'version' => '0.1.0',
            'skills' => [],
        ]);

        $this->assertSame('invalid', $result['status']);
        $this->assertSame(0, $fake->callCount);
    }

    public function test_manifest_missing_version_returns_invalid_and_does_not_call_client(): void
    {
        $fake = $this->makeFake();
        $service = new KiloClawClientService($fake);

        $result = $service->install([
            'id' => 'plugin.atlas',
            'name' => 'atlas',
            'skills' => [],
        ]);

        $this->assertSame('invalid', $result['status']);
        $this->assertSame(0, $fake->callCount);
    }

    public function test_manifest_missing_skills_returns_invalid_and_does_not_call_client(): void
    {
        $fake = $this->makeFake();
        $service = new KiloClawClientService($fake);

        $result = $service->install([
            'id' => 'plugin.atlas',
            'name' => 'atlas',
            'version' => '0.1.0',
        ]);

        $this->assertSame('invalid', $result['status']);
        $this->assertSame(0, $fake->callCount);
    }

    public function test_happy_path_ready_returns_canonical_shape(): void
    {
        $fake = $this->makeFake();
        $fake->nextResponse = [
            'kiloclaw_id' => 'kc_abc',
            'status' => 'ready',
        ];
        $service = new KiloClawClientService($fake);

        $result = $service->install($this->canonicalManifest());

        $this->assertSame([
            'status' => 'ready',
            'kiloclaw_id' => 'kc_abc',
            'manifest_id' => 'plugin.atlas',
            'error' => null,
        ], $result);
        $this->assertSame(1, $fake->callCount);
    }

    public function test_happy_path_booting_returns_canonical_shape(): void
    {
        $fake = $this->makeFake();
        $fake->nextResponse = [
            'kiloclaw_id' => 'kc_def',
            'status' => 'booting',
        ];
        $service = new KiloClawClientService($fake);

        $result = $service->install($this->canonicalManifest());

        $this->assertSame([
            'status' => 'booting',
            'kiloclaw_id' => 'kc_def',
            'manifest_id' => 'plugin.atlas',
            'error' => null,
        ], $result);
        $this->assertSame(1, $fake->callCount);
    }

    public function test_client_exception_returns_failed_without_propagating(): void
    {
        $fake = $this->makeFake();
        $fake->nextException = new KiloClawException('transport boom');
        $service = new KiloClawClientService($fake);

        try {
            $result = $service->install($this->canonicalManifest());
        } catch (\Throwable $e) {
            $this->fail('exception leaked: ' . $e->getMessage());
        }

        $this->assertSame('failed', $result['status']);
        $this->assertNull($result['kiloclaw_id']);
        $this->assertSame('plugin.atlas', $result['manifest_id']);
        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('transport boom', $result['error']);
    }

    public function test_malformed_response_missing_kiloclaw_id_returns_failed(): void
    {
        $fake = $this->makeFake();
        $fake->nextResponse = ['status' => 'ready'];
        $service = new KiloClawClientService($fake);

        $result = $service->install($this->canonicalManifest());

        $this->assertSame('failed', $result['status']);
        $this->assertNull($result['kiloclaw_id']);
        $this->assertSame('plugin.atlas', $result['manifest_id']);
        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('malformed', $result['error']);
    }

    public function test_malformed_response_invalid_status_returns_failed(): void
    {
        $fake = $this->makeFake();
        $fake->nextResponse = [
            'kiloclaw_id' => 'kc_xyz',
            'status' => 'booting_or_something',
        ];
        $service = new KiloClawClientService($fake);

        $result = $service->install($this->canonicalManifest());

        $this->assertSame('failed', $result['status']);
        $this->assertNull($result['kiloclaw_id']);
        $this->assertSame('plugin.atlas', $result['manifest_id']);
        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('malformed', $result['error']);
    }

    public function test_idempotency_key_is_stable_across_two_calls_same_manifest(): void
    {
        $fake = $this->makeFake();
        $fake->nextResponse = [
            'kiloclaw_id' => 'kc_abc',
            'status' => 'ready',
        ];
        $service = new KiloClawClientService($fake);

        $service->install($this->canonicalManifest());
        $capturedFirstKey = $fake->lastArgs[1];

        $service->install($this->canonicalManifest());

        $this->assertSame($capturedFirstKey, $fake->lastArgs[1]);
        $this->assertStringStartsWith('kiloclaw-', $capturedFirstKey);
        $this->assertSame(2, $fake->callCount);
    }
}

final class FakeKiloClawHttpClient implements KiloClawHttpClient
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
