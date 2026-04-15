<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Runtime\Demo;

use App\Services\Runtime\Demo\DemoAgentRuntimeProvisioner;
use App\Services\Runtime\TenantHandle;
use App\Services\Runtime\TenantSpec;
use App\Services\Runtime\TenantStatus;
use Illuminate\Support\Str;
use Tests\TestCase;

final class DemoAgentRuntimeProvisionerTest extends TestCase
{
    private function spec(): TenantSpec
    {
        return new TenantSpec(
            agentName: 'demo-agent',
            personality: 'helpful',
            telegramBotToken: '123:abc',
            llmApiKey: 'sk-test',
            imageTag: 'kiloclaw:latest',
            webhookBaseDomain: 'agents.thespawn.io',
        );
    }

    public function test_create_returns_handle_with_demo_prefix_provider_ref(): void
    {
        $provisioner = new DemoAgentRuntimeProvisioner();

        $handle = $provisioner->create($this->spec());

        $this->assertStringStartsWith('demo_', $handle->providerRef);
        $this->assertSame(17, strlen($handle->providerRef));
    }

    public function test_create_fqdn_uses_spec_webhook_base_domain(): void
    {
        $provisioner = new DemoAgentRuntimeProvisioner();

        $handle = $provisioner->create($this->spec());

        $this->assertMatchesRegularExpression(
            '#^https://[0-9a-f]{12}\.agents\.thespawn\.io$#',
            $handle->fqdn,
        );
    }

    public function test_create_handle_id_is_uuid(): void
    {
        $provisioner = new DemoAgentRuntimeProvisioner();

        $handle = $provisioner->create($this->spec());

        $this->assertTrue(Str::isUuid($handle->id));
    }

    public function test_status_always_returns_running(): void
    {
        $provisioner = new DemoAgentRuntimeProvisioner();
        $handle = $provisioner->create($this->spec());

        $snapshot = $provisioner->status($handle);

        $this->assertSame(TenantStatus::Running, $snapshot->status);
        $this->assertSame($handle->fqdn, $snapshot->fqdn);
        $this->assertNull($snapshot->error);
    }

    public function test_start_stop_destroy_upgrade_are_idempotent_noops(): void
    {
        $provisioner = new DemoAgentRuntimeProvisioner();
        $handle = $provisioner->create($this->spec());

        $provisioner->start($handle);
        $provisioner->start($handle);
        $provisioner->stop($handle);
        $provisioner->stop($handle);
        $provisioner->destroy($handle);
        $provisioner->destroy($handle);
        $provisioner->upgrade($handle, 'kiloclaw:v2');
        $provisioner->upgrade($handle, 'kiloclaw:v2');

        $snapshot = $provisioner->status($handle);
        $this->assertSame(TenantStatus::Running, $snapshot->status);
        $this->assertSame($handle->fqdn, $snapshot->fqdn);
    }

    public function test_create_two_tenants_produces_distinct_ids(): void
    {
        $provisioner = new DemoAgentRuntimeProvisioner();

        $a = $provisioner->create($this->spec());
        $b = $provisioner->create($this->spec());

        $this->assertNotSame($a->id, $b->id);
        $this->assertNotSame($a->providerRef, $b->providerRef);
    }
}
