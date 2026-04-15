<?php

declare(strict_types=1);

namespace Tests\Feature\Services\Runtime;

use App\Services\Runtime\Coolify\CoolifyAgentRuntimeProvisioner;
use App\Services\Runtime\Coolify\CoolifyApiException;
use App\Services\Runtime\TenantHandle;
use App\Services\Runtime\TenantSpec;
use App\Services\Runtime\TenantStatus;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class CoolifyAgentRuntimeProvisionerTest extends TestCase
{
    private function makeProvisioner(): CoolifyAgentRuntimeProvisioner
    {
        return new CoolifyAgentRuntimeProvisioner(
            app(HttpFactory::class),
            'https://coolify.test/api/v1',
            'test-token',
            'srv-uuid',
            'dst-uuid',
            'prj-uuid',
            'ghcr.io/test/openclaw-agent',
        );
    }

    private function makeSpec(): TenantSpec
    {
        return new TenantSpec(
            agentName: 'Nova',
            personality: 'friendly helper',
            telegramBotToken: 'tg:test-token',
            llmApiKey: 'sk-test-key',
            imageTag: 'v0.1.0',
            webhookBaseDomain: 'agents.thespawn.io',
        );
    }

    public function test_create_posts_application_and_returns_handle(): void
    {
        Http::fake([
            'coolify.test/api/v1/applications/public' => Http::response(['uuid' => 'app-xyz'], 201),
            'coolify.test/api/v1/applications/app-xyz' => Http::response(['uuid' => 'app-xyz'], 200),
            'coolify.test/api/v1/applications/app-xyz/envs' => Http::response(['ok' => true], 200),
        ]);

        $provisioner = $this->makeProvisioner();
        $handle = $provisioner->create($this->makeSpec());

        $this->assertInstanceOf(TenantHandle::class, $handle);
        $this->assertSame('app-xyz', $handle->providerRef);
        $this->assertMatchesRegularExpression(
            '#^https://[0-9a-f]{12}\.agents\.thespawn\.io$#',
            $handle->fqdn
        );

        Http::assertSent(function (ClientRequest $request) {
            if ($request->method() !== 'POST') {
                return false;
            }
            if (! str_ends_with($request->url(), '/applications/public')) {
                return false;
            }
            $body = $request->data();

            return ($body['docker_registry_image_name'] ?? null) === 'ghcr.io/test/openclaw-agent'
                && ($body['ports_exposes'] ?? null) === '8080'
                && ($body['build_pack'] ?? null) === 'dockerimage'
                && ($body['server_uuid'] ?? null) === 'srv-uuid'
                && ($body['destination_uuid'] ?? null) === 'dst-uuid'
                && ($body['project_uuid'] ?? null) === 'prj-uuid'
                && ($body['docker_registry_image_tag'] ?? null) === 'v0.1.0';
        });
    }

    public function test_create_throws_on_malformed_response(): void
    {
        Http::fake([
            'coolify.test/api/v1/applications/public' => Http::response([], 200),
        ]);

        $this->expectException(CoolifyApiException::class);

        $this->makeProvisioner()->create($this->makeSpec());
    }

    public function test_create_sets_env_vars(): void
    {
        Http::fake([
            'coolify.test/api/v1/applications/public' => Http::response(['uuid' => 'app-xyz'], 201),
            'coolify.test/api/v1/applications/app-xyz' => Http::response(['uuid' => 'app-xyz'], 200),
            'coolify.test/api/v1/applications/app-xyz/envs' => Http::response(['ok' => true], 200),
        ]);

        $this->makeProvisioner()->create($this->makeSpec());

        $expectedKeys = [
            'AGENT_NAME',
            'AGENT_PERSONALITY',
            'TELEGRAM_BOT_TOKEN',
            'LLM_API_KEY',
            'LLM_PROVIDER',
            'WEBHOOK_PUBLIC_URL',
        ];

        foreach ($expectedKeys as $key) {
            Http::assertSent(function (ClientRequest $request) use ($key) {
                return $request->method() === 'PATCH'
                    && str_ends_with($request->url(), '/applications/app-xyz/envs')
                    && ($request->data()['key'] ?? null) === $key;
            });
        }

        $envCallCount = 0;
        foreach (Http::recorded() as $pair) {
            /** @var ClientRequest $request */
            $request = $pair[0];
            if ($request->method() === 'PATCH' && str_ends_with($request->url(), '/applications/app-xyz/envs')) {
                $envCallCount++;
            }
        }
        $this->assertSame(6, $envCallCount);
    }

    public function test_start_triggers_deploy(): void
    {
        Http::fake([
            'coolify.test/api/v1/deploy*' => Http::response([
                'deployments' => [
                    ['deployment_uuid' => 'd1'],
                ],
            ], 200),
        ]);

        $handle = new TenantHandle(
            id: '00000000-0000-0000-0000-000000000001',
            providerRef: 'app-xyz',
            fqdn: 'https://abc.agents.thespawn.io',
        );

        $this->makeProvisioner()->start($handle);

        Http::assertSent(function (ClientRequest $request) {
            return $request->method() === 'POST'
                && str_contains($request->url(), '/deploy')
                && str_contains($request->url(), 'uuid=app-xyz')
                && str_contains($request->url(), 'force=false');
        });
    }

    public function test_status_maps_running_healthy_to_running_enum(): void
    {
        Http::fake([
            'coolify.test/api/v1/applications/app-xyz' => Http::response([
                'status' => 'running:healthy',
                'fqdn' => 'https://abc.agents.thespawn.io',
            ], 200),
        ]);

        $handle = new TenantHandle(
            id: '00000000-0000-0000-0000-000000000001',
            providerRef: 'app-xyz',
            fqdn: 'https://fallback.agents.thespawn.io',
        );

        $snapshot = $this->makeProvisioner()->status($handle);

        $this->assertSame(TenantStatus::Running, $snapshot->status);
        $this->assertSame('https://abc.agents.thespawn.io', $snapshot->fqdn);
    }

    public function test_status_maps_exited_to_stopped(): void
    {
        Http::fake([
            'coolify.test/api/v1/applications/app-xyz' => Http::response([
                'status' => 'exited:unhealthy',
            ], 200),
        ]);

        $handle = new TenantHandle(
            id: '00000000-0000-0000-0000-000000000001',
            providerRef: 'app-xyz',
            fqdn: 'https://abc.agents.thespawn.io',
        );

        $snapshot = $this->makeProvisioner()->status($handle);

        $this->assertSame(TenantStatus::Stopped, $snapshot->status);
    }

    public function test_destroy_is_idempotent(): void
    {
        Http::fake([
            'coolify.test/api/v1/applications/app-xyz' => Http::response(['error' => 'not found'], 404),
        ]);

        $handle = new TenantHandle(
            id: '00000000-0000-0000-0000-000000000001',
            providerRef: 'app-xyz',
            fqdn: 'https://abc.agents.thespawn.io',
        );

        $this->makeProvisioner()->destroy($handle);

        $this->assertTrue(true);
    }
}
