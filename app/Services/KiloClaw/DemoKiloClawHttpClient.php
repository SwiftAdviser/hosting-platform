<?php

declare(strict_types=1);

namespace App\Services\KiloClaw;

final class DemoKiloClawHttpClient implements KiloClawHttpClient
{
    public function install(array $manifest, string $idempotencyKey): array
    {
        $manifestId = is_string($manifest['id'] ?? null) ? $manifest['id'] : 'unknown';

        return [
            'kiloclaw_id' => 'demo_'.substr(sha1($manifestId.':'.$idempotencyKey), 0, 16),
            'status' => 'ready',
        ];
    }
}
