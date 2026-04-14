<?php
declare(strict_types=1);

namespace App\Services\OnChainOS;

interface OnChainOSClient
{
    /**
     * Create a payment charge against OnChainOS.
     *
     * Reference: https://onchainos.example/api/charges (v0.1 uses a stubbed implementation).
     *
     * @return array decoded payment intent as an associative array
     * @throws OnChainOSException on transport-level failure
     */
    public function createCharge(int $amountUsd, string $agentName, string $idempotencyKey): array;
}
