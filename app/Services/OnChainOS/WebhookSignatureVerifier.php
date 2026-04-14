<?php

declare(strict_types=1);

namespace App\Services\OnChainOS;

final class WebhookSignatureVerifier
{
    public function __construct(
        private readonly string $secret,
    ) {
    }

    public function verify(string $signature, string $payload): bool
    {
        return hash_equals($this->secret, $signature);
    }
}
