<?php

namespace App\Services;

use App\Models\Configuracao;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EvolutionApiService
{
    protected string $baseUrl;
    protected string $globalKey;
    protected int $timeout;
    protected int $timeoutMedia;

    public function __construct()
    {
        $url = (string) config('whatsapp.evolution.base_url');
        if ($url === '') {
            $url = (string) Configuracao::get('evolution_api_url', '');
        }
        $this->baseUrl = rtrim($url, '/');

        $key = (string) config('whatsapp.evolution.global_key');
        if ($key === '') {
            $key = (string) Configuracao::get('evolution_global_key', '');
        }
        $this->globalKey = $key;

        $this->timeout = (int) config('whatsapp.evolution.timeout', 15);
        $this->timeoutMedia = (int) config('whatsapp.evolution.timeout_media', 45);
    }

    public function isConfigured(): bool
    {
        return $this->baseUrl !== '' && $this->globalKey !== '';
    }

    protected function http(int $timeout = null): PendingRequest
    {
        return Http::withHeaders(['apikey' => $this->globalKey])
            ->timeout($timeout ?? $this->timeout)
            ->acceptJson();
    }

    public function createInstance(string $instanceName, string $instanceToken): array
    {
        $webhookUrl = rtrim(config('app.url'), '/') . '/api/webhooks/evolution/' . $instanceToken;

        $payload = [
            'instanceName' => $instanceName,
            'integration' => 'WHATSAPP-BAILEYS',
            'webhook' => [
                'enabled' => true,
                'url' => $webhookUrl,
                'byEvents' => false,
                'base64' => true,
                'events' => config('whatsapp.webhook.events'),
            ],
        ];

        try {
            $resp = $this->http(45)->post("{$this->baseUrl}/instance/create", $payload);
            if ($resp->successful()) {
                return $resp->json() ?? [];
            }
            Log::error('EVO createInstance failed', [
                'status' => $resp->status(),
                'body' => $resp->body(),
            ]);
            return ['error' => 'Falha ao criar instância', 'details' => $resp->body()];
        } catch (\Throwable $e) {
            Log::error('EVO createInstance exception', ['msg' => $e->getMessage()]);
            return ['error' => 'Evolution API indisponível: ' . $e->getMessage()];
        }
    }

    public function fetchQrCode(string $instanceName): array
    {
        try {
            $resp = $this->http(8)->get("{$this->baseUrl}/instance/connect/{$instanceName}");
            if (!$resp->successful()) {
                return ['error' => 'Evolution retornou ' . $resp->status(), 'status' => 'generating'];
            }
            $data = $resp->json() ?? [];
            $qr = $data['base64']
                ?? ($data['qrcode']['base64'] ?? null)
                ?? ($data['code'] ?? null)
                ?? (is_string($data['qrcode'] ?? null) ? $data['qrcode'] : null);

            if ($qr) {
                return ['qrcode' => $qr, 'status' => 'connecting'];
            }
            return ['status' => 'generating', 'error' => 'QR ainda não disponível'];
        } catch (\Throwable $e) {
            return ['error' => $e->getMessage(), 'status' => 'generating'];
        }
    }

    public function getConnectionState(string $instanceName): array
    {
        try {
            $resp = $this->http(5)->get("{$this->baseUrl}/instance/connectionState/{$instanceName}");
            return $resp->json() ?? ['error' => 'Resposta vazia'];
        } catch (\Throwable $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function sendText(string $instanceName, string $toPhone, string $message): array
    {
        // Se for grupo (@g.us), preserva o JID. Se for número de usuário, extrai apenas dígitos.
        $cleanNumber = trim($toPhone);
        if (str_contains($cleanNumber, '@g.us')) {
            $cleanNumber = preg_replace('/[^0-9@g\.us]/', '', $cleanNumber);
        } else {
            $cleanNumber = preg_replace('/@.*/', '', $cleanNumber);
            $cleanNumber = preg_replace('/\D/', '', $cleanNumber);
        }

        $payload = [
            'number' => $cleanNumber,
            'text' => $message,
            'delay' => 0,
        ];
        try {
            $resp = $this->http()->post("{$this->baseUrl}/message/sendText/{$instanceName}", $payload);
            if ($resp->failed()) {
                Log::error('EVO sendText failed', [
                    'instance' => $instanceName,
                    'number' => $cleanNumber,
                    'status' => $resp->status(),
                    'body' => $resp->body()
                ]);
                return ['error' => 'Falha ao enviar: ' . $resp->body(), 'details' => $resp->body()];
            }
            return $resp->json() ?? [];
        } catch (\Throwable $e) {
            Log::error('EVO sendText exception', ['error' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }

    public function sendMedia(string $instanceName, string $toPhone, string $media, string $mimetype, string $caption = '', string $fileName = 'arquivo'): array
    {
        $mediaType = explode('/', $mimetype)[0];
        if (!in_array($mediaType, ['image', 'video', 'audio'])) {
            $mediaType = 'document';
        }

        $cleanMedia = str_starts_with($media, 'http')
            ? $media
            : preg_replace('/^data:[^;]+;base64,/', '', $media);

        $payload = [
            'number' => $toPhone,
            'mediatype' => $mediaType,
            'mimetype' => $mimetype,
            'caption' => $caption,
            'media' => $cleanMedia,
            'fileName' => $fileName,
        ];

        try {
            $resp = $this->http($this->timeoutMedia)
                ->post("{$this->baseUrl}/message/sendMedia/{$instanceName}", $payload);
            if ($resp->failed()) {
                Log::error('EVO sendMedia failed', ['status' => $resp->status(), 'body' => $resp->body()]);
                return ['error' => 'Falha ao enviar mídia', 'details' => $resp->body()];
            }
            return $resp->json() ?? [];
        } catch (\Throwable $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function logout(string $instanceName): array
    {
        try {
            $resp = $this->http(5)->delete("{$this->baseUrl}/instance/logout/{$instanceName}");
            return $resp->json() ?? [];
        } catch (\Throwable $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function deleteInstance(string $instanceName): array
    {
        try {
            $resp = $this->http(10)->delete("{$this->baseUrl}/instance/delete/{$instanceName}");
            return $resp->json() ?? [];
        } catch (\Throwable $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
