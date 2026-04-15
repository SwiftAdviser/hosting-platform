<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Runtime;

use App\Services\Runtime\TenantStatus;
use App\Services\Runtime\TenantStatusSnapshot;
use PHPUnit\Framework\TestCase;

final class TenantStatusSnapshotTest extends TestCase
{
    public function testRunningSnapshotCarriesFqdn(): void
    {
        $snap = new TenantStatusSnapshot(
            status: TenantStatus::Running,
            fqdn: 'https://abc.agents.thespawn.io',
        );

        $this->assertSame(TenantStatus::Running, $snap->status);
        $this->assertSame('https://abc.agents.thespawn.io', $snap->fqdn);
        $this->assertNull($snap->error);
    }

    public function testFailedSnapshotCarriesError(): void
    {
        $snap = new TenantStatusSnapshot(
            status: TenantStatus::Failed,
            fqdn: null,
            error: 'image pull failed',
        );

        $this->assertSame(TenantStatus::Failed, $snap->status);
        $this->assertNull($snap->fqdn);
        $this->assertSame('image pull failed', $snap->error);
    }

    public function testProvisioningSnapshotDefaultsAreNull(): void
    {
        $snap = new TenantStatusSnapshot(status: TenantStatus::Provisioning);

        $this->assertSame(TenantStatus::Provisioning, $snap->status);
        $this->assertNull($snap->fqdn);
        $this->assertNull($snap->error);
    }

    public function testAllStatusCasesPresent(): void
    {
        $values = array_map(fn (TenantStatus $c): string => $c->value, TenantStatus::cases());

        $this->assertSame(
            ['provisioning', 'starting', 'running', 'degraded', 'stopped', 'failed'],
            $values,
        );
    }
}
