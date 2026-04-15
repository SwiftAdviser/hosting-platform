<?php

declare(strict_types=1);

namespace App\Services\OnChainOS\XLayer;

use Illuminate\Support\Facades\Http;
use Throwable;

final class HttpXLayerTransport implements XLayerHttpTransport
{
    public function __construct(
        private readonly string $baseUrl,
    ) {
    }

    public function post(string $path, array $body, array $headers): array
    {
        $base = trim($this->baseUrl);
        if ($base === '') {
            throw new XLayerHttpException('xlayer base url not configured');
        }

        $url = rtrim($base, '/').'/'.ltrim($path, '/');

        try {
            $response = Http::timeout(15)
                ->withHeaders($headers)
                ->acceptJson()
                ->post($url, $body);
        } catch (Throwable $e) {
            throw new XLayerHttpException('xlayer transport failure: '.$e->getMessage(), 0, $e);
        }

        $status = $response->status();
        if ($status >= 400) {
            throw new XLayerHttpException("xlayer http {$status}");
        }

        $decoded = $response->json();
        if (!is_array($decoded)) {
            throw new XLayerHttpException('xlayer non-json response');
        }

        return $decoded;
    }
}
