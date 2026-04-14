<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class TelegramWebhookController extends Controller
{
    public function handle(int $agentId, Request $request): JsonResponse
    {
        return response()->json(['ok' => true], 200);
    }
}
