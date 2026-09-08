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
            throw new \Exception("Cliente não conectado ao Google.");
        }

        $this->client->setAccessToken(Crypt::decryptString($cliente->google_access_token));

        // Se expirou e temos refresh token, renova e salva no banco
        if ($this->client->isAccessTokenExpired() && $cliente->google_refresh_token) {
            $newToken = $this->client->fetchAccessTokenWithRefreshToken(Crypt::decryptString($cliente->google_refresh_token));
            
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
        // Busca a conta associada ao usuário
        $accountResponse = $httpClient->get('https://mybusinessaccountmanagement.googleapis.com/v1/accounts');
        $accounts = json_decode($accountResponse->getBody(), true);
        
        if (!isset($accounts['accounts'])) {
            return [];
        }

        $locations = [];
        foreach ($accounts['accounts'] as $account) {
            $accountId = $account['name'];
            
            // Busca os locais (locations) desta conta
            $locResponse = $httpClient->get("https://mybusinessbusinessinformation.googleapis.com/v1/{$accountId}/locations?readMask=name,title,storeCode");
            $locData = json_decode($locResponse->getBody(), true);
            
            if (isset($locData['locations'])) {
                $locations = array_merge($locations, $locData['locations']);
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
