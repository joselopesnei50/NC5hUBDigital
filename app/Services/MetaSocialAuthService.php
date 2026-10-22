<?php

namespace App\Services;

use App\Models\Configuracao;
use App\Models\SocialAccount;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaSocialAuthService
{
    private string $appId;
    private string $appSecret;
    private string $redirectUri;
    private string $graphVersion;

    public function __construct()
    {
        $this->graphVersion = (string) config('whatsapp.meta_api_version', 'v22.0');
        $this->appId        = (string) (Configuracao::get('meta_app_id') ?: config('services.meta.app_id', ''));
        $this->appSecret    = (string) (Configuracao::get('meta_app_secret') ?: config('services.meta.app_secret', ''));
        $this->redirectUri  = route('social.facebook.callback');
    }

    public function isConfigured(): bool
    {
        return !empty($this->appId) && !empty($this->appSecret);
    }

    /**
     * Gera URL OAuth da Meta (NC5HUBDIGITAL-EMP) com state identificando contexto (admin ou customer).
     */
    public function getAuthUrl(string $context = 'customer'): string
    {
        $scopes = implode(',', [
            'public_profile',
            'pages_show_list',
            'pages_read_engagement',
            'pages_manage_posts',
            'instagram_basic',
            'instagram_content_publish',
            'business_management',
        ]);

        return "https://www.facebook.com/dialog/oauth?" . http_build_query([
            'client_id'     => $this->appId,
            'redirect_uri'  => $this->redirectUri,
            'scope'         => $scopes,
            'response_type' => 'code',
            'state'         => csrf_token() . '|' . $context,
        ]);
    }

    public function exchangeCodeForToken(string $code): ?string
    {
        $response = Http::timeout(12)->get("https://graph.facebook.com/{$this->graphVersion}/oauth/access_token", [
            'client_id'     => $this->appId,
            'client_secret' => $this->appSecret,
            'redirect_uri'  => $this->redirectUri,
            'code'          => $code,
        ]);

        if (!$response->successful()) {
            Log::error('Meta OAuth token exchange failed', ['body' => $response->body()]);
            return null;
        }

        return $response->json('access_token');
    }

    public function getLongLivedToken(string $shortToken): ?string
    {
        $response = Http::timeout(12)->get("https://graph.facebook.com/{$this->graphVersion}/oauth/access_token", [
            'grant_type'        => 'fb_exchange_token',
            'client_id'         => $this->appId,
            'client_secret'     => $this->appSecret,
            'fb_exchange_token' => $shortToken,
        ]);

        return $response->successful() ? $response->json('access_token') : null;
    }

    public function getUserPages(string $userToken): array
    {
        $response = Http::timeout(15)->get("https://graph.facebook.com/{$this->graphVersion}/me/accounts", [
            'access_token' => $userToken,
            'fields'       => 'id,name,picture,access_token,instagram_business_account',
        ]);

        if (!$response->successful()) {
            Log::error('Meta get pages failed', ['body' => $response->body()]);
            return [];
        }

        return $response->json('data', []);
    }

    public function getInstagramAccount(string $pageId, string $pageToken): ?array
    {
        $response = Http::timeout(10)->get("https://graph.facebook.com/{$this->graphVersion}/{$pageId}", [
            'fields'       => 'instagram_business_account',
            'access_token' => $pageToken,
        ]);

        if (!$response->successful()) {
            return null;
        }

        $igId = $response->json('instagram_business_account.id');
        if (!$igId) {
            return null;
        }

        $igResponse = Http::timeout(10)->get("https://graph.facebook.com/{$this->graphVersion}/{$igId}", [
            'fields'       => 'id,username,profile_picture_url',
            'access_token' => $pageToken,
        ]);

        return $igResponse->successful() ? $igResponse->json() : null;
    }

    public function parseSignedRequest(?string $signedRequest): ?array
    {
        if (empty($signedRequest) || !str_contains($signedRequest, '.')) {
            return null;
        }

        [$encodedSig, $encodedPayload] = explode('.', $signedRequest, 2);

        $sig     = $this->base64UrlDecode($encodedSig);
        $payload = $this->base64UrlDecode($encodedPayload);

        if ($sig === false || $payload === false) {
            return null;
        }

        $data = json_decode($payload, true);
        if (!is_array($data) || (($data['algorithm'] ?? '') !== 'HMAC-SHA256')) {
            return null;
        }

        if (empty($this->appSecret)) {
            return null;
        }

        $expectedSig = hash_hmac('sha256', $encodedPayload, $this->appSecret, true);
        if (!hash_equals($expectedSig, $sig)) {
            return null;
        }

        return $data;
    }

    private function base64UrlDecode(string $input): string|false
    {
        $padded = strtr($input, '-_', '+/');
        $padded .= str_repeat('=', (4 - strlen($padded) % 4) % 4);
        return base64_decode($padded, true);
    }

    public function saveAccounts(array $pages, int $clienteId): int
    {
        $saved = 0;
        foreach ($pages as $page) {
            $pageToken = $page['access_token'] ?? null;
            if (!$pageToken) {
                continue;
            }

            $ig = $this->getInstagramAccount($page['id'], $pageToken);

            SocialAccount::updateOrCreate(
                ['cliente_id' => $clienteId, 'page_id' => $page['id']],
                [
                    'platform'              => 'facebook',
                    'page_name'             => $page['name'],
                    'page_picture'          => $page['picture']['data']['url'] ?? null,
                    'access_token'          => $pageToken,
                    'token_expires_at'      => now()->addDays(60),
                    'instagram_business_id' => $ig['id'] ?? null,
                    'instagram_username'    => $ig['username'] ?? null,
                    'is_active'             => true,
                ]
            );
            $saved++;
        }
        return $saved;
    }
}
