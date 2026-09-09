<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\AnalysisCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class NotifyTenantOfAnalysis implements ShouldQueue
{
    /**
     * Processa o evento de notificação em background.
     */
    public function handle(AnalysisCompleted $event): void
    {
        $run = $event->run;
        
        Log::info("[Agente de Negócios] Notificando Tenant {$run->cliente_id} que a Análise #{$run->id} está pronta.");
        
        // Futuro (Etapa 8/Painel):
        // Notification::send($run->cliente->user, new AgentAnalysisReadyNotification($run));
    }
}
