<?php

declare(strict_types=1);

namespace App\Services\Runtime\Demo;

use App\Services\Runtime\AgentRuntimeProvisioner;
use App\Services\Runtime\TenantHandle;
use App\Services\Runtime\TenantSpec;
use App\Services\Runtime\TenantStatus;
use App\Services\Runtime\TenantStatusSnapshot;
use Illuminate\Support\Str;

final class DemoAgentRuntimeProvisioner implements AgentRuntimeProvisioner
{
    public function create(TenantSpec $spec): TenantHandle
    {
        $id = (string) Str::uuid();
        $short = substr(str_replace('-', '', $id), 0, 12);
        $fqdn = 'https://'.$short.'.'.$spec->webhookBaseDomain;
        $providerRef = 'demo_'.$short;

        return new TenantHandle($id, $providerRef, $fqdn);
    }

    public function start(TenantHandle $handle): void
    {
    }

    public function status(TenantHandle $handle): TenantStatusSnapshot
    {
        return new TenantStatusSnapshot(TenantStatus::Running, $handle->fqdn, null);
    }

    public function stop(TenantHandle $handle): void
    {
    }

    public function destroy(TenantHandle $handle): void
    {
    }

    public function upgrade(TenantHandle $handle, string $imageTag): void
    {
    }
}
