<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessCloudApiWebhook;
use App\Models\Configuracao;
use App\Models\WhatsappInstance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Webhook Oficial da Meta WhatsApp Business Cloud API (App NC5HUBDIGITAL-EMP).
 *
 * URLs suportadas:
 *   GET/POST /api/whatsapp/cloud-webhook
 *   GET/POST /api/webhooks/meta-whatsapp
 *
 * Possui suporte a Bridge/Proxy inteligente por phone_number_id caso o mesmo
 * App da Meta compartilhe eventos entre NC5 Hub Digital e Vivensi.
 */
class CloudApiWebhookController extends Controller
{
    public function verify(Request $request)
    {
        $mode      = $request->query('hub_mode') ?? $request->query('hub.mode');
        $token     = $request->query('hub_verify_token') ?? $request->query('hub.verify_token');
        $challenge = $request->query('hub_challenge') ?? $request->query('hub.challenge', '');

        $expected = (string) (Configuracao::get('meta_webhook_verify_token') ?: config('whatsapp.meta_cloud.verify_token', ''));

        if ($mode !== 'subscribe' || $token === null || $expected === '' || !hash_equals($expected, (string) $token)) {
            Log::warning('Meta CloudApi Webhook: handshake rejeitado', [
                'mode' => $mode,
                'ip'   => $request->ip(),
            ]);
            return response()->json(['error' => 'Invalid verify token'], 403);
        }

        return response((string) $challenge, 200, ['Content-Type' => 'text/plain']);
    }

    public function handle(Request $request)
    {
        $appSecret = (string) (Configuracao::get('meta_app_secret') ?: config('whatsapp.meta_cloud.app_secret', ''));
        if ($appSecret === '') {
            Log::error('Meta CloudApi Webhook: meta_app_secret não configurado', ['ip' => $request->ip()]);
            return response()->json(['error' => 'Webhook not configured'], 503);
        }

        $signature = (string) $request->header('X-Hub-Signature-256', '');
        if ($signature === '') {
            return response()->json(['error' => 'Missing signature'], 401);
        }

        $rawBody  = $request->getContent();
        $expected = 'sha256=' . hash_hmac('sha256', $rawBody, $appSecret);

        if (!hash_equals($expected, $signature)) {
            Log::warning('Meta CloudApi Webhook: assinatura HMAC inválida', ['ip' => $request->ip()]);
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $payload = $request->all();

        // Verifica se o evento pertence a alguma instância local ou se deve ser repassado (Bridge Vivensi <-> NC5)
        $hasLocalMatch = false;
        $hasUnknownPhone = false;

        foreach ($payload['entry'] ?? [] as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                $phoneNumberId = $change['value']['metadata']['phone_number_id'] ?? null;
                if ($phoneNumberId) {
                    $exists = WhatsappInstance::where('provider', WhatsappInstance::PROVIDER_CLOUD_API)
                        ->where('phone_number_id', $phoneNumberId)
                        ->exists();
                    if ($exists) {
                        $hasLocalMatch = true;
                    } else {
                        $hasUnknownPhone = true;
                    }
                }
            }
        }

        if ($hasLocalMatch || !$hasUnknownPhone) {
            ProcessCloudApiWebhook::dispatch($payload);
        }

        // Se houver phone_number_id não pertencente ao banco local e uma URL de Bridge estiver configurada
        // (ex: compartilhando o mesmo App NC5HUBDIGITAL-EMP com a Vivensi), repassa com a assinatura original.
        if ($hasUnknownPhone && !$request->header('X-NC5-Forwarded')) {
            $forwardUrl = trim((string) (Configuracao::get('meta_webhook_forward_url') ?: config('whatsapp.meta_cloud.forward_url', '')));
            if ($forwardUrl !== '' && filter_var($forwardUrl, FILTER_VALIDATE_URL)) {
                try {
                    Http::timeout(5)
                        ->withHeaders([
                            'Content-Type'       => 'application/json',
                            'X-Hub-Signature-256'=> $signature,
                            'X-NC5-Forwarded'    => '1',
                        ])
                        ->withBody($rawBody, 'application/json')
                        ->post($forwardUrl);
                } catch (\Throwable $e) {
                    Log::warning('Meta CloudApi Webhook Bridge forward falhou', [
                        'url'   => $forwardUrl,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return response()->json(['status' => 'queued'], 200);
    }
}
