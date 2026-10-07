<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessEvolutionWebhook;
use App\Models\WhatsappInstance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsappWebhookController extends Controller
{
    public function handle(Request $request, string $token)
    {
        $instance = WhatsappInstance::where('instance_token', $token)->first();
        if (!$instance) {
            Log::warning('Evolution webhook: token inválido', [
                'token_prefix' => substr($token, 0, 8),
                'ip' => $request->ip(),
            ]);
            return response()->json(['status' => 'ignored'], 200);
        }

        $secret = (string) config('whatsapp.evolution.webhook_secret');
        if ($secret !== '') {
            $sig = $request->header('x-webhook-hmac')
                ?: $request->header('x-hub-signature-256')
                ?: $request->header('x-signature');
            if (!$sig) {
                return response()->json(['status' => 'unauthorized'], 401);
            }
            $expected = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secret);
            if (!hash_equals($expected, $sig)) {
                Log::warning('Evolution webhook: HMAC inválido', ['instance_id' => $instance->id]);
                return response()->json(['status' => 'unauthorized'], 401);
            }
        }

        $payload = $request->all();
        $event = $payload['event'] ?? ($payload['type'] ?? 'unknown');

        ProcessEvolutionWebhook::dispatch($instance->id, $event, $payload);

        return response()->json(['status' => 'queued'], 200);
    }
}
