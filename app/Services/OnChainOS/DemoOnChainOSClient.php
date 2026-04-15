<?php

declare(strict_types=1);

namespace App\Services\OnChainOS;

final class DemoOnChainOSClient implements OnChainOSClient
{
    public function createCharge(int $amountUsd, string $agentName, string $idempotencyKey): array
    {
        return [
            'session_id' => 'demo_'.substr(sha1($idempotencyKey), 0, 16),
            'status' => 'pending',
            'expires_at' => gmdate('Y-m-d\TH:i:s\Z', time() + 900),
        ];
    }
}
