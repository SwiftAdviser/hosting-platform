<?php

declare(strict_types=1);

namespace Tests\Feature\Services\Runtime;

use App\Services\Runtime\AgentRuntimeProvisioner;
use App\Services\Runtime\Coolify\CoolifyAgentRuntimeProvisioner;
use App\Services\Runtime\Demo\DemoAgentRuntimeProvisioner;
use Tests\TestCase;

final class AgentRuntimeProvisionerBindingTest extends TestCase
{
    public function test_demo_mode_resolves_demo_provisioner(): void
    {
        config(['services.demo.enabled' => true]);
        $this->app->forgetInstance(AgentRuntimeProvisioner::class);

        $provisioner = $this->app->make(AgentRuntimeProvisioner::class);

        $this->assertInstanceOf(DemoAgentRuntimeProvisioner::class, $provisioner);
    }

    public function test_real_mode_resolves_coolify_provisioner(): void
    {
        config(['services.demo.enabled' => false]);
        $this->app->forgetInstance(AgentRuntimeProvisioner::class);

        $provisioner = $this->app->make(AgentRuntimeProvisioner::class);

        $this->assertInstanceOf(CoolifyAgentRuntimeProvisioner::class, $provisioner);
    }
}
