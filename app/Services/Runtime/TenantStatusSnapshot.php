<?php

declare(strict_types=1);

namespace App\Services\Runtime;

final readonly class TenantStatusSnapshot
{
    public function __construct(
        public TenantStatus $status,
        public ?string $fqdn = null,
        public ?string $error = null,
    ) {
    }
}
