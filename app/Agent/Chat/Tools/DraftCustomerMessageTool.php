<?php

declare(strict_types=1);

namespace App\Agent\Chat\Tools;

use App\Agent\Chat\Contracts\ToolInterface;
use App\Agent\NotificationDraftService;

class DraftCustomerMessageTool implements ToolInterface
{
    public function __construct(
        private NotificationDraftService $draftService
    ) {
    }

    public function getName(): string
    {
        return 'draft_customer_message';
    }

    public function getDescription(): string
    {
        return 'Redige e SALVA COMO RASCUNHO uma mensagem (email/whatsapp) para clientes finais da empresa. Use isso quando o gestor pedir para você "mandar uma mensagem" ou "criar um aviso". Você nunca envia de verdade, você cria o rascunho para aprovação.';
    }

    public function getParametersSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'cliente_final_id' => [
                    'type' => 'integer', 
                    'description' => 'ID do cliente recebedor. Deixe vazio se for para todos ou se não souber.'
                ],
                'trigger' => [
                    'type' => 'string', 
                    'description' => 'A razão do contato (ex: recompra, abandono, boas-vindas)'
                ],
                'subject' => [
                    'type' => 'string', 
                    'description' => 'Assunto do E-mail (ignorado se for Whatsapp)'
                ],
                'body' => [
                    'type' => 'string', 
                    'description' => 'O texto final da mensagem, redigido de forma atrativa e sem jargões.'
                ],
                'channel' => [
                    'type' => 'string', 
                    'enum' => ['email', 'whatsapp'],
                    'description' => 'Canal de envio pretendido'
                ],
            ],
            'required' => ['trigger', 'subject', 'body', 'channel']
        ];
    }

    public function execute(int $tenantId, array $arguments): array
    {
        $draft = $this->draftService->createDraft(
            $tenantId,
            $arguments['cliente_final_id'] ?? null,
            $arguments['trigger'],
            $arguments['subject'],
            $arguments['body'],
            $arguments['channel']
        );

        return [
            'status' => 'sucesso',
            'mensagem_interna' => "Rascunho ID #{$draft->id} criado! Avise o gestor para ir na aba de rascunhos e aprovar o envio para disparar oficialmente."
        ];
    }
}
