<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fatura;

class WebhookController extends Controller
{
        // Validar a assinatura do Webhook para segurança
        $signature = $request->header('x-webhook-signature') ?? $request->header('x-signature');
        $secret = \App\Models\Configuracao::get('abacatepay_webhook_secret', env('ABACATEPAY_WEBHOOK_SECRET'));

        if ($secret && $signature) {
            $payloadString = $request->getContent();
            $expectedSignature = hash_hmac('sha256', $payloadString, $secret);

            if (!hash_equals($expectedSignature, $signature)) {
                \Log::warning("AbacatePay Webhook: Tentativa de fraude detectada. Assinatura inválida.");
                return response()->json(['error' => 'Invalid signature'], 403);
            }
        } elseif ($secret && !$signature) {
            \Log::warning("AbacatePay Webhook: Requisição sem assinatura.");
            return response()->json(['error' => 'Missing signature'], 403);
        }
        
        $payload = $request->all();
        
        // Verifica o tipo de evento
        if (($payload['event'] ?? '') === 'checkout.completed') {
            
            $data = $payload['data'] ?? [];
            $metadata = $data['metadata'] ?? [];
            
            if (isset($metadata['fatura_id'])) {
                $fatura = Fatura::find($metadata['fatura_id']);
                
                if ($fatura && $fatura->status !== 'pago') {
                    $fatura->update(['status' => 'pago']);
                    \Log::info("AbacatePay Webhook: Fatura {$fatura->id} marcada como PAGA.");
                }
            }
        }
        
        return response()->json(['success' => true]);
    }
}
