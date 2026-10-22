<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/webhooks/abacatepay', [\App\Http\Controllers\Api\WebhookController::class, 'handleAbacatePay']);
Route::post('/webhooks/evolution/{token}', [\App\Http\Controllers\Api\WhatsappWebhookController::class, 'handle']);

// Meta WhatsApp Business Cloud API (API Oficial — App NC5HUBDIGITAL-EMP)
Route::get('/whatsapp/cloud-webhook', [\App\Http\Controllers\Api\CloudApiWebhookController::class, 'verify']);
Route::post('/whatsapp/cloud-webhook', [\App\Http\Controllers\Api\CloudApiWebhookController::class, 'handle']);
Route::get('/webhooks/meta-whatsapp', [\App\Http\Controllers\Api\CloudApiWebhookController::class, 'verify']);
Route::post('/webhooks/meta-whatsapp', [\App\Http\Controllers\Api\CloudApiWebhookController::class, 'handle']);
