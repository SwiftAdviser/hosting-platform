<?php

declare(strict_types=1);

namespace App\Services\KiloClaw;

use Illuminate\Support\Facades\Http;
use Throwable;

final class HttpKiloClawClient implements KiloClawHttpClient
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $apiKey,
    ) {
    }

    public function install(array $manifest, string $idempotencyKey): array
    {
        $base = trim($this->baseUrl);
        if ($base === '') {
            throw new KiloClawException('kiloclaw base url not configured');
        }

        $url = rtrim($base, '/').'/v1/install';

        $headers = [
            'Idempotency-Key' => $idempotencyKey,
        ];
        if ($this->apiKey !== '') {
            $headers['Authorization'] = 'Bearer '.$this->apiKey;
        }

        try {
            $response = Http::timeout(15)
                ->acceptJson()
                ->withHeaders($headers)
                ->post($url, $manifest);
        } catch (Throwable $e) {
            throw new KiloClawException('install transport failure: '.$e->getMessage(), 0, $e);
        }

        $decoded = $response->json();
        if (!is_array($decoded)) {
            throw new KiloClawException('install non-json response');
        }

        return $decoded;
    }
}
