<?php

namespace App\Services;

use App\Models\Configuracao;
use App\Models\WhatsappInstance;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaCloudApiService
{
    protected ?string $wabaId = null;
    protected ?string $phoneNumberId = null;
    protected ?string $accessToken = null;
    protected string $apiVersion;

    public function __construct(?WhatsappInstance $instance = null)
    {
        $this->apiVersion = (string) config('whatsapp.meta_api_version', 'v22.0');

        if ($instance) {
            $this->wabaId = $instance->waba_id;
            $this->phoneNumberId = $instance->phone_number_id;
            $this->accessToken = $instance->graph_access_token;
        }
    }

    public static function forInstance(WhatsappInstance $instance): self
    {
        return new self($instance);
    }

    public static function isAppConfigured(): bool
    {
        $appId = (string) (Configuracao::get('meta_app_id') ?: config('whatsapp.meta_cloud.app_id', ''));
        $appSecret = (string) (Configuracao::get('meta_app_secret') ?: config('whatsapp.meta_cloud.app_secret', ''));

        return $appId !== '' && $appSecret !== '';
    }

    public function isConfigured(): bool
    {
        return !empty($this->phoneNumberId) && !empty($this->accessToken);
    }

    /**
     * Envia mensagem de texto livre (janela de 24h aberta).
     */
    public function sendTextMessage(string $to, string $text): array
    {
        if (!$this->isConfigured()) {
            return ['error' => 'Credenciais da Meta Cloud API ausentes na instância.'];
        }

        $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/messages";

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type'    => 'individual',
            'to'                => $this->formatNumber($to),
            'type'              => 'text',
            'text'              => [
                'preview_url' => false,
                'body'        => $text,
            ],
        ];

        return $this->dispatchRequest($url, $payload);
    }

    /**
     * Envia um Template Oficial aprovado pela Meta.
     */
    public function sendTemplateMessage(
        string $to,
        string $templateName,
        string $languageCode = 'pt_BR',
        array $variables = [],
        ?array $header = null
    ): array {
        if (!$this->isConfigured()) {
            return ['error' => 'Credenciais da Meta Cloud API ausentes na instância.'];
        }

        $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/messages";
        $components = [];

        if (!empty($header)) {
            $type = $header['type'] ?? 'text';
            if ($type === 'text') {
                $components[] = [
                    'type'       => 'header',
                    'parameters' => [['type' => 'text', 'text' => (string) ($header['value'] ?? '')]],
                ];
            } elseif (in_array($type, ['image', 'video', 'document'], true)) {
                $components[] = [
                    'type'       => 'header',
                    'parameters' => [[
                        'type' => $type,
                        $type  => ['link' => (string) ($header['value'] ?? '')],
                    ]],
                ];
            }
        }

        if (!empty($variables)) {
            $components[] = [
                'type'       => 'body',
                'parameters' => array_map(fn ($v) => ['type' => 'text', 'text' => (string) $v], array_values($variables)),
            ];
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to'                => $this->formatNumber($to),
            'type'              => 'template',
            'template'          => [
                'name'       => $templateName,
                'language'   => ['code' => $languageCode],
                'components' => $components,
            ],
        ];

        return $this->dispatchRequest($url, $payload);
    }

    /**
     * Envia mídia (image, video, audio, document) via URL pública ou media_id.
     */
    public function sendMedia(string $to, string $type, string $mediaUrlOrId, string $caption = ''): array
    {
        if (!$this->isConfigured()) {
            return ['error' => 'Credenciais da Meta Cloud API ausentes na instância.'];
        }

        $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/messages";
        $mediaField = str_starts_with($mediaUrlOrId, 'http') ? ['link' => $mediaUrlOrId] : ['id' => $mediaUrlOrId];

        if ($caption !== '' && $type !== 'audio') {
            $mediaField['caption'] = $caption;
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to'                => $this->formatNumber($to),
            'type'              => $type,
            $type               => $mediaField,
        ];

        return $this->dispatchRequest($url, $payload);
    }

    /**
     * Marca mensagem recebida como lida (Read Receipt).
     */
    public function markMessageAsRead(string $messageId): array
    {
        if (!$this->isConfigured()) {
            return ['error' => 'Credenciais ausentes'];
        }

        $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/messages";

        return $this->dispatchRequest($url, [
            'messaging_product' => 'whatsapp',
            'status'            => 'read',
            'message_id'        => $messageId,
        ]);
    }

    /**
     * Lista os templates da WABA na Meta.
     */
    public function getTemplates(): array
    {
        if (empty($this->wabaId) || empty($this->accessToken)) {
            return ['error' => 'WABA ID ou Access Token ausentes.'];
        }

        $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->wabaId}/message_templates";

        try {
            $response = Http::timeout(12)
                ->withToken($this->accessToken)
                ->get($url, [
                    'limit'  => 100,
                    'fields' => 'id,name,status,category,language,components',
                ]);

            if ($response->successful()) {
                return $response->json() ?? ['data' => []];
            }

            Log::error('Meta getTemplates falhou', ['body' => $response->body()]);
            return ['error' => 'Falha ao buscar templates na Meta.', 'details' => $response->body()];
        } catch (\Throwable $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function verifyWebhookSignature(string $rawBody, string $signature, string $appSecret): bool
    {
        if (empty($appSecret) || empty($signature)) {
            return false;
        }

        $expected = 'sha256=' . hash_hmac('sha256', $rawBody, $appSecret);

        return hash_equals($expected, $signature);
    }

    protected function dispatchRequest(string $url, array $payload): array
    {
        try {
            $response = Http::timeout(12)
                ->withToken($this->accessToken)
                ->post($url, $payload);

            if ($response->failed()) {
                $body     = $response->json() ?? [];
                $metaErr  = $body['error'] ?? [];
                $metaMsg  = trim((string) ($metaErr['message'] ?? ''));
                $metaCode = isset($metaErr['code']) ? (int) $metaErr['code'] : null;

                Log::error('Meta Cloud API Error', [
                    'status'  => $response->status(),
                    'code'    => $metaCode,
                    'message' => $metaMsg,
                    'body'    => $body ?: $response->body(),
                ]);

                $friendly = match ($metaCode) {
                    131030         => 'Este número não está na lista de destinatários de teste da Meta. Adicione-o no painel da Meta ou use uma WABA de produção.',
                    131047, 131051 => 'A janela de 24 horas do WhatsApp fechou para este contato. Envie um Template Oficial aprovado para reabrir a conversa.',
                    131026         => 'A Meta não conseguiu entregar a mensagem. Verifique se o número possui WhatsApp ativo.',
                    131056         => 'Limite temporário de mensagens para este destinatário atingido. Aguarde alguns minutos.',
                    132000, 132001 => 'Template inexistente ou variáveis incompatíveis. Confira o nome e o idioma do template.',
                    default        => null,
                };

                $displayMsg = $friendly
                    ?? ($metaMsg !== ''
                        ? $metaMsg . ($metaCode ? " (Meta code {$metaCode})" : '')
                        : 'Falha ao enviar mensagem pela API Oficial.');

                return [
                    'error'   => $displayMsg,
                    'code'    => $metaCode,
                    'status'  => $response->status(),
                    'details' => $response->body(),
                ];
            }

            return $response->json() ?? [];
        } catch (\Throwable $e) {
            Log::error('Meta Cloud API Exception: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    protected function formatNumber(string $number): string
    {
        $clean = preg_replace('/@.*/', '', trim($number));
        return preg_replace('/\D/', '', $clean);
    }
}
