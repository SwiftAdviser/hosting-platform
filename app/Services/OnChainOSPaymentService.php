<?php
declare(strict_types=1);

namespace App\Services;

use App\Services\OnChainOS\OnChainOSClient;
use App\Services\OnChainOS\OnChainOSException;

final class OnChainOSPaymentService
{
    public function __construct(
        private readonly OnChainOSClient $client,
    ) {
    }

    public function createCharge(int $amountUsd, string $agentName): array
    {
        if ($amountUsd <= 0 || trim($agentName) === '') {
            return [
                'status' => 'invalid',
                'session_id' => null,
                'amount_usd' => $amountUsd,
                'agent_name' => $agentName,
                'expires_at' => null,
            ];
        }

        $idempotencyKey = 'spawn-' . sha1($agentName . ':' . $amountUsd . ':' . gmdate('Y-m-d'));

        try {
            $response = $this->client->createCharge($amountUsd, $agentName, $idempotencyKey);
        } catch (OnChainOSException $e) {
            return [
                'status' => 'failed',
                'session_id' => null,
                'amount_usd' => $amountUsd,
                'agent_name' => $agentName,
                'expires_at' => null,
                'error' => $e->getMessage(),
            ];
        }

        if (!isset($response['session_id']) || !isset($response['status'])) {
            return [
                'status' => 'failed',
                'session_id' => null,
                'amount_usd' => $amountUsd,
                'agent_name' => $agentName,
                'expires_at' => null,
                'error' => 'malformed response',
            ];
        }

        return [
            'status' => $response['status'],
            'session_id' => $response['session_id'],
            'amount_usd' => $amountUsd,
            'agent_name' => $agentName,
            'expires_at' => $response['expires_at'] ?? null,
        ];
    }
}
