<?php

use App\Http\Controllers\Api\DeployController;
use App\Http\Controllers\Api\OnChainOSWebhookController;
use App\Http\Controllers\Api\TelegramWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/deploys', [DeployController::class, 'store']);
Route::post('/webhooks/onchainos', [OnChainOSWebhookController::class, 'handle']);
Route::post('/telegram/webhook/{agentId}', [TelegramWebhookController::class, 'handle']);
