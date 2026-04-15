<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Runtime;

use App\Services\Runtime\TenantHandle;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class TenantHandleTest extends TestCase
{
    public function testHappyPathExposesAllFields(): void
    {
        $handle = new TenantHandle(
            id: '11111111-2222-3333-4444-555555555555',
            providerRef: 'coolify-app-uuid-abc',
            fqdn: 'https://abc.agents.thespawn.io',
        );

        $this->assertSame('11111111-2222-3333-4444-555555555555', $handle->id);
        $this->assertSame('coolify-app-uuid-abc', $handle->providerRef);
        $this->assertSame('https://abc.agents.thespawn.io', $handle->fqdn);
    }

    public function testEmptyIdRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new TenantHandle(
            id: '',
            providerRef: 'ref',
            fqdn: 'https://x.example.com',
        );
    }

    public function testEmptyProviderRefRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new TenantHandle(
            id: 'id-1',
            providerRef: '',
            fqdn: 'https://x.example.com',
        );
    }

    public function testEmptyFqdnRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new TenantHandle(
            id: 'id-1',
            providerRef: 'ref',
            fqdn: '',
        );
    }

    public function testNonHttpsFqdnRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new TenantHandle(
            id: 'id-1',
            providerRef: 'ref',
            fqdn: 'http://x.example.com',
        );
    }
}
