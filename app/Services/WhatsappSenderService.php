<?php

namespace App\Services;

use App\Models\WhatsappInstance;

/**
 * Dispatcher unificado de envio de mensagens WhatsApp (Evolution API QR Code + Meta Cloud API Oficial).
 */
class WhatsappSenderService
{
    public function __construct(protected EvolutionApiService $evo) {}

    /**
     * Verifica se o provedor da instância está configurado no sistema.
     */
    public function isInstanceReady(WhatsappInstance $instance): bool
    {
        if ($instance->isCloudApi()) {
            return !empty($instance->phone_number_id) && !empty($instance->graph_access_token);
        }

        return $this->evo->isConfigured();
    }

    /**
     * Envia mensagem de texto usando automaticamente o provedor da instância (Evolution ou Cloud API).
     *
     * @return array{ok: bool, message_id: ?string, error: ?string, details?: mixed, raw?: array}
     */
    public function sendText(WhatsappInstance $instance, string $toPhone, string $message): array
    {
        if ($instance->isCloudApi()) {
            $meta = MetaCloudApiService::forInstance($instance);
            $raw = $meta->sendTextMessage($toPhone, $message);

            if (isset($raw['error'])) {
                return [
                    'ok'         => false,
                    'message_id' => null,
                    'error'      => is_array($raw['error']) ? ($raw['error']['message'] ?? 'Erro Meta Cloud API') : (string) $raw['error'],
                    'details'    => $raw['details'] ?? null,
                    'raw'        => $raw,
                ];
            }

            $msgId = $raw['messages'][0]['id'] ?? null;

            return [
                'ok'         => $msgId !== null,
                'message_id' => $msgId,
                'error'      => $msgId === null ? 'Resposta da Meta sem ID de mensagem.' : null,
                'raw'        => $raw,
            ];
        }

        // Provedor padrão: Evolution API (QR Code)
        $raw = $this->evo->sendText($instance->instance_name, $toPhone, $message);

        if (isset($raw['error'])) {
            return [
                'ok'         => false,
                'message_id' => null,
                'error'      => (string) $raw['error'],
                'details'    => $raw['details'] ?? null,
                'raw'        => $raw,
            ];
        }

        $msgId = $raw['key']['id'] ?? ($raw['messageId'] ?? null);

        return [
            'ok'         => true,
            'message_id' => $msgId,
            'error'      => null,
            'raw'        => $raw,
        ];
    }

    /**
     * Envia Template Oficial da Meta (exclusivo para instâncias Cloud API).
     */
    public function sendTemplate(
        WhatsappInstance $instance,
        string $toPhone,
        string $templateName,
        string $languageCode = 'pt_BR',
        array $variables = []
    ): array {
        if (!$instance->isCloudApi()) {
            return [
                'ok'         => false,
                'message_id' => null,
                'error'      => 'Envio de Templates Oficiais requer conexão via API Oficial da Meta.',
            ];
        }

        $meta = MetaCloudApiService::forInstance($instance);
        $raw = $meta->sendTemplateMessage($toPhone, $templateName, $languageCode, $variables);

        if (isset($raw['error'])) {
            return [
                'ok'         => false,
                'message_id' => null,
                'error'      => is_array($raw['error']) ? ($raw['error']['message'] ?? 'Erro ao enviar template') : (string) $raw['error'],
                'raw'        => $raw,
            ];
        }

        $msgId = $raw['messages'][0]['id'] ?? null;

        return [
            'ok'         => $msgId !== null,
            'message_id' => $msgId,
            'error'      => null,
            'raw'        => $raw,
        ];
    }
}
