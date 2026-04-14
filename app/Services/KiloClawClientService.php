<?php
declare(strict_types=1);

namespace App\Services;

use App\Services\KiloClaw\KiloClawException;
use App\Services\KiloClaw\KiloClawHttpClient;

final class KiloClawClientService
{
    public function __construct(
        private readonly KiloClawHttpClient $client,
    ) {
    }

    public function install(array $manifest): array
    {
        $missing = [];
        foreach (['id', 'name', 'version', 'skills'] as $key) {
            if (!array_key_exists($key, $manifest)) {
                $missing[] = $key;
                continue;
            }
            if ($key === 'skills') {
                if (!is_array($manifest[$key])) {
                    $missing[] = $key;
                }
                continue;
            }
            if (!is_string($manifest[$key]) || trim($manifest[$key]) === '') {
                $missing[] = $key;
            }
        }

        if ($missing !== []) {
            return [
                'status' => 'invalid',
                'kiloclaw_id' => null,
                'manifest_id' => null,
                'error' => 'missing or invalid manifest keys: ' . implode(', ', $missing),
            ];
        }

        $idempotencyKey = 'kiloclaw-' . sha1($manifest['id'] . ':' . $manifest['version']);

        try {
            $response = $this->client->install($manifest, $idempotencyKey);
        } catch (KiloClawException $e) {
            return [
                'status' => 'failed',
                'kiloclaw_id' => null,
                'manifest_id' => $manifest['id'],
                'error' => $e->getMessage(),
            ];
        }

        if (
            !isset($response['kiloclaw_id'])
            || !is_string($response['kiloclaw_id'])
            || !isset($response['status'])
            || !in_array($response['status'], ['ready', 'booting', 'failed'], true)
        ) {
            return [
                'status' => 'failed',
                'kiloclaw_id' => null,
                'manifest_id' => $manifest['id'],
                'error' => 'malformed kiloclaw response',
            ];
        }

        return [
            'status' => $response['status'],
            'kiloclaw_id' => $response['kiloclaw_id'],
            'manifest_id' => $manifest['id'],
            'error' => null,
        ];
    }
}
