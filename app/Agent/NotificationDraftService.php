<?php

declare(strict_types=1);

namespace App\Agent;

use App\Models\AgentNotificationDraft;
use App\Agent\Enums\AgentNotificationStatus;
use Illuminate\Support\Facades\Log;
use Exception;
use Throwable;

class NotificationDraftService
{
    /**
     * O Agente (via análise periódica ou via BruceIA) chama este método 
     * quando identifica uma oportunidade real (ex: cliente sumido, aniversário).
     */
    public function createDraft(
        int $tenantId,
        ?int $clienteFinalId,
        string $trigger,
        string $subject,
        string $body,
        string $channel = 'email'
    ): AgentNotificationDraft {
        return AgentNotificationDraft::create([
            'cliente_id' => $tenantId,
            'cliente_final_id' => $clienteFinalId,
            'trigger' => $trigger,
            'channel' => $channel,
            'subject' => $subject,
            'body' => $body,
            'status' => AgentNotificationStatus::RASCUNHO,
        ]);
    }

    /**
     * Ação disparada pelo botão "Aprovar e Enviar" no painel do usuário.
     */
    public function approveAndSend(int $draftId, int $approverUserId): bool
    {
        $draft = AgentNotificationDraft::findOrFail($draftId);

        if ($draft->status !== AgentNotificationStatus::RASCUNHO) {
            throw new Exception('Apenas rascunhos novos podem ser aprovados.');
        }

        // 1. Audita quem aprovou (requisito de segurança)
        $draft->update([
            'status' => AgentNotificationStatus::APROVADO,
            'approved_by_user_id' => $approverUserId,
        ]);

        // 2. Roteia para o envio
        try {
            $this->dispatchToChannel($draft);

            // 3. Marca como concluído
            $draft->update([
                'status' => AgentNotificationStatus::ENVIADO,
                'sent_at' => now(),
            ]);

            return true;
        } catch (Throwable $e) {
            Log::error("[NotificationDraftService] Falha ao enviar rascunho [{$draft->id}]: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Ação disparada pelo botão "Descartar/Ignorar" no painel do usuário.
     */
    public function discard(int $draftId): void
    {
        $draft = AgentNotificationDraft::findOrFail($draftId);
        $draft->update(['status' => AgentNotificationStatus::DESCARTADO]);
    }

    /**
     * Encapsula a inteligência de envio via Provedores (Mail, WhatsApp, SMS).
     */
    private function dispatchToChannel(AgentNotificationDraft $draft): void
    {
        Log::info("Disparando mensagem [{$draft->id}] via [{$draft->channel}] para o cliente_final_id [{$draft->cliente_final_id}]");
        
        // Exemplo da integração real que conectaremos no futuro:
        /*
        if ($draft->channel === 'email' && $draft->clienteFinal) {
            Mail::to($draft->clienteFinal->email)->send(new AgentDraftMail($draft));
        } elseif ($draft->channel === 'whatsapp') {
            ZApi::sendMessage($draft->clienteFinal->telefone, $draft->body);
        }
        */
    }
}
