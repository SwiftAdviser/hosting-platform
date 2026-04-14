<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AgentDeployerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class DeployController extends Controller
{
    public function store(Request $request, AgentDeployerService $deployer): JsonResponse
    {
        $request->validate([
            'agent_name' => 'required|string',
            'personality' => 'required|string',
            'telegram_bot_token' => 'required|string',
            'amount_usd' => 'required|integer|min:1',
            'allowlist' => 'nullable|string',
        ]);

        $result = $deployer->deploy($request->all());

        $statusMap = [
            'deployed' => 201,
            'payment_failed' => 402,
            'invalid_request' => 422,
            'telegram_invalid' => 422,
            'install_failed' => 502,
        ];

        $statusCode = $statusMap[$result['status'] ?? ''] ?? 500;

        return response()->json($result, $statusCode);
    }
}
