<?php
declare(strict_types=1);

namespace App\Services;

use App\Services\KiloClawClientService;
use App\Services\OnChainOSPaymentService;
use App\Services\TelegramBotRegistrarService;

final class AgentDeployerService
{
    public const DEFAULT_PERSONALITY = 'A general helpful assistant specialized in wallet operations.';

    public function __construct(
        private readonly OnChainOSPaymentService $payment,
        private readonly KiloClawClientService $kiloclaw,
        private readonly TelegramBotRegistrarService $telegram,
    ) {
    }

    public function deploy(array $request): array
    {
        $request['personality'] = $this->normalizePersonality($request['personality'] ?? null);

        $missing = [];
        foreach (['agent_name', 'personality', 'telegram_bot_token', 'amount_usd'] as $key) {
            if (!array_key_exists($key, $request)) {
                $missing[] = $key;
                continue;
            }
            if ($key === 'amount_usd') {
                if (!is_int($request[$key]) || $request[$key] <= 0) {
                    $missing[] = $key;
                }
                continue;
            }
            if (!is_string($request[$key]) || trim($request[$key]) === '') {
                $missing[] = $key;
            }
        }

        $agentName = is_string($request['agent_name'] ?? null) ? $request['agent_name'] : '';

        if ($missing !== []) {
            return [
                'status' => 'invalid_request',
                'stage' => 'validate',
                'agent_name' => $agentName,
                'error' => 'missing or invalid: ' . implode(', ', $missing),
                'kiloclaw_id' => null,
                'session_id' => null,
            ];
        }

        if (!$this->telegram->validateToken($request['telegram_bot_token'])) {
            return [
                'status' => 'telegram_invalid',
                'stage' => 'telegram_validate',
                'agent_name' => $agentName,
                'error' => 'telegram token invalid',
                'kiloclaw_id' => null,
                'session_id' => null,
            ];
        }

        $charge = $this->payment->createCharge($request['amount_usd'], $request['agent_name']);

        if (($charge['status'] ?? null) !== 'pending') {
            return [
                'status' => 'payment_failed',
                'stage' => 'payment',
                'agent_name' => $agentName,
                'error' => $charge['error'] ?? 'payment not pending',
                'kiloclaw_id' => null,
                'session_id' => $charge['session_id'] ?? null,
            ];
        }

        $manifest = [
            'id' => 'spawn.' . strtolower($request['agent_name']),
            'name' => $request['agent_name'],
            'version' => '0.1.0',
            'skills' => [],
        ];

        $install = $this->kiloclaw->install($manifest);

        if (!in_array($install['status'] ?? null, ['ready', 'booting'], true)) {
            return [
                'status' => 'install_failed',
                'stage' => 'install',
                'agent_name' => $agentName,
                'error' => $install['error'] ?? 'install failed',
                'kiloclaw_id' => $install['kiloclaw_id'] ?? null,
                'session_id' => $charge['session_id'],
            ];
        }

        return [
            'status' => 'deployed',
            'stage' => 'complete',
            'agent_name' => $request['agent_name'],
            'error' => null,
            'kiloclaw_id' => $install['kiloclaw_id'],
            'session_id' => $charge['session_id'],
        ];
    }

    private function normalizePersonality(mixed $personality): string
    {
        if (!is_string($personality) || trim($personality) === '') {
            return self::DEFAULT_PERSONALITY;
        }

        return $personality;
    }
}
