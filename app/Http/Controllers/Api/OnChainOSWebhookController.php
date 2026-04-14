<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OnChainOS\WebhookSignatureVerifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class OnChainOSWebhookController extends Controller
{
    public function handle(Request $request, WebhookSignatureVerifier $verifier): JsonResponse
    {
        $signature = $request->header('X-OnChainOS-Signature');

        if ($signature === null || $signature === '') {
            return response()->json(['status' => 'error', 'error' => 'missing signature'], 400);
        }

        if (! $verifier->verify($signature, $request->getContent())) {
            return response()->json(['status' => 'error', 'error' => 'invalid signature'], 401);
        }

        return response()->json(['status' => 'ok'], 200);
    }
}
