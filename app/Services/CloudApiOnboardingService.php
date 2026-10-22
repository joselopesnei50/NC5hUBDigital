<?php

namespace App\Services;

use App\Models\Configuracao;
use App\Models\WhatsappInstance;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Orquestração de Onboarding na Meta WhatsApp Business Cloud API (API Oficial).
 * Suporta:
 *  1) Embedded Signup (FB.login com config_id)
 *  2) Conexão Manual Assistida (WABA ID + Phone Number ID + System User Access Token)
 */
class CloudApiOnboardingService
{
    private string $apiVersion;
    private string $appId;
    private string $appSecret;
    private string $configId;

    public function __construct()
    {
        $this->apiVersion = (string) config('whatsapp.meta_api_version', 'v22.0');
        $this->appId      = (string) (Configuracao::get('meta_app_id') ?: config('whatsapp.meta_cloud.app_id', ''));
        $this->appSecret  = (string) (Configuracao::get('meta_app_secret') ?: config('whatsapp.meta_cloud.app_secret', ''));
        $this->configId   = (string) (Configuracao::get('meta_cloud_config_id') ?: config('whatsapp.meta_cloud.config_id', ''));
    }

    public function getAppId(): string
    {
        return $this->appId;
    }

    public function getConfigId(): string
    {
        return $this->configId;
    }

    public function isConfigured(): bool
    {
        return $this->appId !== '' && $this->appSecret !== '';
    }

    public function isEmbeddedSignupConfigured(): bool
    {
        return $this->isConfigured() && $this->configId !== '';
    }

    /**
     * Fluxo 1: Embedded Signup via popup FB.login
     */
    public function completeSignup(
        int $clienteId,
        string $code,
        string $wabaId,
        string $phoneNumberId,
        string $registrationPin
    ): WhatsappInstance {
        if (!$this->isConfigured()) {
            throw new RuntimeException('Credenciais do Meta App (App ID / App Secret) não configuradas no painel Admin.');
        }

        $this->assertNumberNotHijacked($clienteId, $phoneNumberId);

        $accessToken = $this->exchangeCodeForToken($code);
        $this->registerPhoneNumber($phoneNumberId, $accessToken, $registrationPin);
        $this->subscribeAppToWaba($wabaId, $accessToken);
        $displayPhone = $this->fetchDisplayPhoneNumber($phoneNumberId, $accessToken);

        return $this->persistInstance($clienteId, $wabaId, $phoneNumberId, $accessToken, $displayPhone);
    }

    /**
     * Fluxo 2: Conexão Manual Assistida (WABA ID + Phone Number ID + System User Access Token)
     */
    public function completeManualSignup(
        int $clienteId,
        string $wabaId,
        string $phoneNumberId,
        string $accessToken,
        ?string $registrationPin = null
    ): WhatsappInstance {
        $this->assertNumberNotHijacked($clienteId, $phoneNumberId);

        // 1. Valida o token contra a WABA na Graph API
        $probe = Http::timeout(12)->withToken($accessToken)
            ->get("https://graph.facebook.com/{$this->apiVersion}/{$wabaId}", [
                'fields' => 'id,name,currency,timezone_id',
            ]);

        if (!$probe->successful()) {
            $err     = (array) $probe->json('error', []);
            $errMsg  = trim((string) ($err['message'] ?? ''));
            $errCode = isset($err['code']) ? (int) $err['code'] : null;

            Log::warning('CloudApi Manual Signup: validação da WABA falhou', [
                'status'  => $probe->status(),
                'code'    => $errCode,
                'message' => $errMsg,
            ]);

            $friendly = match ($errCode) {
                190     => 'Token inválido ou expirado. Gere um novo System User Access Token no Business Manager da Meta e cole aqui.',
                100     => 'WABA ID não encontrado ou o token não tem permissão para acessar essa conta.',
                default => 'Não foi possível validar suas credenciais na Meta.' . ($errMsg !== '' ? " Detalhe: {$errMsg}" : ''),
            };

            throw new RuntimeException($friendly);
        }

        // 2. Se forneceu PIN, registra o número na Cloud API
        if ($registrationPin !== null && $registrationPin !== '') {
            $this->registerPhoneNumber($phoneNumberId, $accessToken, $registrationPin);
        }

        // 3. Inscreve o aplicativo na WABA para habilitar recebimento de webhooks
        $this->subscribeAppToWaba($wabaId, $accessToken);

        // 4. Obtém o número formatado da linha
        $displayPhone = $this->fetchDisplayPhoneNumber($phoneNumberId, $accessToken);

        return $this->persistInstance($clienteId, $wabaId, $phoneNumberId, $accessToken, $displayPhone);
    }

    private function assertNumberNotHijacked(int $clienteId, string $phoneNumberId): void
    {
        $conflict = WhatsappInstance::where('phone_number_id', $phoneNumberId)
            ->where('cliente_id', '!=', $clienteId)
            ->first();

        if ($conflict) {
            throw new RuntimeException('Este Phone Number ID já está vinculado a outro cliente no BruceOS.');
        }
    }

    public function exchangeCodeForToken(string $code): string
    {
        $response = Http::timeout(12)->get("https://graph.facebook.com/{$this->apiVersion}/oauth/access_token", [
            'client_id'     => $this->appId,
            'client_secret' => $this->appSecret,
            'code'          => $code,
        ]);

        if (!$response->successful()) {
            Log::error('CloudApi Onboarding: exchange code falhou', ['body' => $response->body()]);
            throw new RuntimeException('Falha ao trocar código de autorização por token na Meta.');
        }

        $token = $response->json('access_token');
        if (empty($token)) {
            throw new RuntimeException('A Meta não retornou um access_token válido.');
        }

        return (string) $token;
    }

    public function registerPhoneNumber(string $phoneNumberId, string $accessToken, string $pin): void
    {
        $response = Http::timeout(15)->withToken($accessToken)
            ->post("https://graph.facebook.com/{$this->apiVersion}/{$phoneNumberId}/register", [
                'messaging_product' => 'whatsapp',
                'pin'               => $pin,
            ]);

        if ($response->successful()) {
            return;
        }

        Log::error('CloudApi Onboarding: /register falhou', [
            'phone_number_id' => $phoneNumberId,
            'body'            => $response->body(),
        ]);

        $err     = (array) $response->json('error', []);
        $errCode = isset($err['code']) ? (int) $err['code'] : null;
        $errMsg  = (string) ($err['message'] ?? '');

        if ($errCode === 133005) {
            throw new RuntimeException('PIN incorreto: este número já possui verificação em duas etapas na Meta com outro PIN.');
        }

        $suffix = $errCode !== null || $errMsg !== ''
            ? sprintf(' (Meta code=%s: %s)', $errCode ?? '?', mb_substr($errMsg, 0, 300))
            : '';

        throw new RuntimeException('Falha ao registrar número na Meta Cloud API.' . $suffix);
    }

    public function subscribeAppToWaba(string $wabaId, string $accessToken): void
    {
        $response = Http::timeout(12)->withToken($accessToken)
            ->post("https://graph.facebook.com/{$this->apiVersion}/{$wabaId}/subscribed_apps");

        if (!$response->successful()) {
            Log::warning('CloudApi Onboarding: subscribed_apps aviso', [
                'waba_id' => $wabaId,
                'body'    => $response->body(),
            ]);
        }
    }

    public function fetchDisplayPhoneNumber(string $phoneNumberId, string $accessToken): ?string
    {
        try {
            $res = Http::timeout(8)->withToken($accessToken)
                ->get("https://graph.facebook.com/{$this->apiVersion}/{$phoneNumberId}", [
                    'fields' => 'display_phone_number,verified_name',
                ]);
            if ($res->successful()) {
                $phone = $res->json('display_phone_number');
                $name  = $res->json('verified_name');
                if ($phone && $name) {
                    return "{$phone} ({$name})";
                }
                return $phone ?: null;
            }
        } catch (\Throwable) {
        }

        return null;
    }

    public function persistInstance(
        int $clienteId,
        string $wabaId,
        string $phoneNumberId,
        string $accessToken,
        ?string $displayPhone = null
    ): WhatsappInstance {
        return WhatsappInstance::updateOrCreate(
            ['cliente_id' => $clienteId],
            [
                'provider'              => WhatsappInstance::PROVIDER_CLOUD_API,
                'waba_id'               => $wabaId,
                'phone_number_id'       => $phoneNumberId,
                'graph_access_token'    => $accessToken,
                'phone_number'          => $displayPhone ?: $phoneNumberId,
                'instance_name'         => 'meta_cloud_' . $clienteId . '_' . substr($phoneNumberId, -6),
                'instance_token'        => Str::random(64),
                'status'                => 'open',
                'connected_at'          => now(),
                'last_status_check_at'  => now(),
            ]
        );
    }
}
