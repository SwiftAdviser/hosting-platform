<?php
declare(strict_types=1);

namespace App\Services\OnChainOS\XLayer;

interface XLayerHttpTransport
{
    /**
     * POST to a relative path on the OKX OnChainOS REST API.
     *
     * Reference: https://www.okx.com/docs-v5/en/#rest-api-onchainos (v0.1 wraps this seam).
     *
     * @return array decoded JSON response
     * @throws XLayerHttpException on transport or HTTP failure
     */
    public function post(string $path, array $body, array $headers): array;
}
