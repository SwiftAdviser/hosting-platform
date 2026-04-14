<?php
declare(strict_types=1);

namespace App\Services\KiloClaw;

interface KiloClawHttpClient
{
    /**
     * Install a KiloClaw/OpenClaw plugin manifest on the host.
     *
     * Reference: https://kiloclaw.example/api/install (v0.1 stubbed).
     *
     * @return array decoded install response as an associative array
     * @throws KiloClawException on transport-level failure
     */
    public function install(array $manifest, string $idempotencyKey): array;
}
