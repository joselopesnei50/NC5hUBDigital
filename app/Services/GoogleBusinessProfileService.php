<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class GoogleBusinessProfileService
{
    protected $client;

    public function __construct()
    {
        $this->client = new GoogleClient();
        // As credenciais vêm do banco de dados (Configurações Globais)
        $this->client->setClientId(\App\Models\Configuracao::get('google_client_id'));
        $this->client->setClientSecret(\App\Models\Configuracao::get('google_client_secret'));
        $this->client->setRedirectUri(\App\Models\Configuracao::get('google_redirect_uri'));
        
        // Escopo necessário para gerenciar a conta de negócios
        $this->client->addScope('https://www.googleapis.com/auth/business.manage');
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent'); // Força a pedir o refresh token
    }

    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    public function authenticateAndSaveTokens($code, $cliente)
    {
        $token = $this->client->fetchAccessTokenWithAuthCode($code);
        
        if (isset($token['error'])) {
            throw new \Exception("Erro de autenticação Google: " . $token['error']);
        }

        // Criptografar tokens sensíveis antes de salvar no banco
        $updateData = [
            'google_access_token' => Crypt::encryptString($token['access_token']),
            'google_token_expires_at' => Carbon::now()->addSeconds($token['expires_in']),
        ];

        if (isset($token['refresh_token'])) {
            $updateData['google_refresh_token'] = Crypt::encryptString($token['refresh_token']);
        }

        $cliente->update($updateData);
    }

    protected function setClientForCliente($cliente)
    {
        if (!$cliente->google_access_token) {
            throw new \Exception("Conta Google não conectada.");
        }

        try {
            $accessToken = Crypt::decryptString($cliente->google_access_token);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('[GoogleBusiness] Falha ao descriptografar access_token do cliente ' . $cliente->id);
            throw new \Exception("Não conseguimos ler suas credenciais salvas. Desconecte e reconecte sua conta Google.");
        }

        $this->client->setAccessToken($accessToken);

        // Se expirou e temos refresh token, renova e salva no banco
        if ($this->client->isAccessTokenExpired() && $cliente->google_refresh_token) {
            try {
                $refreshToken = Crypt::decryptString($cliente->google_refresh_token);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('[GoogleBusiness] Falha ao descriptografar refresh_token do cliente ' . $cliente->id);
                throw new \Exception("Não conseguimos renovar seu acesso. Desconecte e reconecte sua conta Google.");
            }

            $newToken = $this->client->fetchAccessTokenWithRefreshToken($refreshToken);

            if (isset($newToken['error'])) {
                \Illuminate\Support\Facades\Log::warning('[GoogleBusiness] Refresh falhou para cliente ' . $cliente->id . ': ' . ($newToken['error'] ?? ''));
                throw new \Exception("Sua sessão do Google expirou. Reconecte a conta para continuar.");
            }

            $cliente->update([
                'google_access_token' => Crypt::encryptString($newToken['access_token']),
                'google_token_expires_at' => Carbon::now()->addSeconds($newToken['expires_in']),
            ]);
        }
    }

    public function getLocations($cliente)
    {
        $this->setClientForCliente($cliente);

        $httpClient = $this->client->authorize();

        try {
            $accountResponse = $httpClient->get('https://mybusinessaccountmanagement.googleapis.com/v1/accounts');
            $accounts = json_decode((string) $accountResponse->getBody(), true) ?? [];
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('[GoogleBusiness] Falha ao listar contas do cliente ' . $cliente->id . ': ' . $e->getMessage());
            throw new \Exception("Não conseguimos consultar suas contas do Google. Verifique se as APIs estão ativadas no Google Cloud.");
        }

        if (empty($accounts['accounts'])) {
            \Illuminate\Support\Facades\Log::info('[GoogleBusiness] Cliente ' . $cliente->id . ' autenticou mas não tem contas associadas.');
            throw new \Exception("Nenhuma conta do Google Meu Negócio encontrada nesta conta Google.");
        }

        $locations = [];
        foreach ($accounts['accounts'] as $account) {
            $accountId = $account['name'] ?? null;
            if (!$accountId) continue;

            try {
                $locResponse = $httpClient->get("https://mybusinessbusinessinformation.googleapis.com/v1/{$accountId}/locations?readMask=name,title,storeCode");
                $locData = json_decode((string) $locResponse->getBody(), true) ?? [];

                if (!empty($locData['locations'])) {
                    $locations = array_merge($locations, $locData['locations']);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("[GoogleBusiness] Falha em locations de {$accountId} (cliente {$cliente->id}): " . $e->getMessage());
            }
        }

        return $locations;
    }

    public function getPerformanceMetrics($cliente, $locationName)
    {
        $this->setClientForCliente($cliente);
        $httpClient = $this->client->authorize();

        $url = "https://businessprofileperformance.googleapis.com/v1/{$locationName}:fetchMultiDailyMetricsTimeSeries";
        
        // Retornando mock temporário
        return []; 
    }

    public function createPost($cliente, $locationName, $content)
    {
        $this->setClientForCliente($cliente);
        $httpClient = $this->client->authorize();

        $postData = [
            'languageCode' => 'pt-BR',
            'summary' => $content,
            'topicType' => 'STANDARD',
        ];

        $url = "https://mybusiness.googleapis.com/v4/{$locationName}/localPosts";
        // mock
    }
}
