<?php
declare(strict_types=1);

namespace App\Services\OnChainOS\XLayer;

use App\Services\OnChainOS\OnChainOSClient;
use App\Services\OnChainOS\OnChainOSException;

final class XLayerOnChainOSClient implements OnChainOSClient
{
    public function __construct(
        private readonly XLayerHttpTransport $transport,
        private readonly string $apiKey,
        private readonly string $secretKey,
        private readonly string $passphrase,
    ) {
    }

    public function createCharge(int $amountUsd, string $agentName, string $idempotencyKey): array
    {
        $body = [
            'amount_usd' => $amountUsd,
            'agent_name' => $agentName,
        ];

        $headers = [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'X-OKX-SECRET' => $this->secretKey,
            'X-OKX-PASSPHRASE' => $this->passphrase,
            'Idempotency-Key' => $idempotencyKey,
            'Content-Type' => 'application/json',
        ];

        try {
            $response = $this->transport->post('/v1/onchainos/charges', $body, $headers);
        } catch (XLayerHttpException $e) {
            $message = $e->getMessage();
            if (str_contains($message, '401') || str_contains($message, '403') || stripos($message, 'unauthorized') !== false || stripos($message, 'forbidden') !== false) {
                throw new OnChainOSException('auth failure', 0, $e);
            }
            throw new OnChainOSException('transport failure', 0, $e);
        }

        if (!isset($response['session_id']) || !isset($response['status'])) {
            throw new OnChainOSException('malformed xlayer response', 0);
        }

        $status = $response['status'];
        if ($status !== 'pending' && $status !== 'failed' && $status !== 'completed') {
            throw new OnChainOSException('malformed xlayer response status', 0);
        }

        return [
            'session_id' => $response['session_id'],
            'status' => $status,
            'expires_at' => $response['expires_at'] ?? null,
        ];
    }
}
