<?php

declare(strict_types=1);

namespace App\Services\Runtime;

interface AgentRuntimeProvisioner
{
    public function create(TenantSpec $spec): TenantHandle;

    public function start(TenantHandle $handle): void;

    public function status(TenantHandle $handle): TenantStatusSnapshot;

    public function stop(TenantHandle $handle): void;

    public function destroy(TenantHandle $handle): void;

    public function upgrade(TenantHandle $handle, string $imageTag): void;
}
