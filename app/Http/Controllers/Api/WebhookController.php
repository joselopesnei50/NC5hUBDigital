<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fatura;

class WebhookController extends Controller
{
    public function handleAbacatePay(Request $request)
    {
        // NOTA: Em produção, validar o hash HMAC no cabeçalho.
        
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
