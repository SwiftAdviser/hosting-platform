<?php

declare(strict_types=1);

namespace App\Services\Runtime;

final readonly class TenantHandle
{
    public function __construct(
        public string $id,
        public string $providerRef,
        public string $fqdn,
    ) {
        if (trim($this->id) === '') {
            throw new \InvalidArgumentException('id must not be empty');
        }
        if (trim($this->providerRef) === '') {
            throw new \InvalidArgumentException('providerRef must not be empty');
        }
        if (trim($this->fqdn) === '') {
            throw new \InvalidArgumentException('fqdn must not be empty');
        }
        if (!str_starts_with($this->fqdn, 'https://')) {
            throw new \InvalidArgumentException('fqdn must be an https URL');
        }
    }
}
