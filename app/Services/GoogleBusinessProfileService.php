<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GoogleBusinessProfileService
{
    protected GoogleClient $client;

    public function __construct()
    {
        $this->client = new GoogleClient();
        $this->client->setClientId(\App\Models\Configuracao::get('google_client_id'));
        $this->client->setClientSecret(\App\Models\Configuracao::get('google_client_secret'));
        $this->client->setRedirectUri(\App\Models\Configuracao::get('google_redirect_uri'));

        $this->client->addScope('https://www.googleapis.com/auth/business.manage');
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent'); // força retorno do refresh_token
    }

    /**
     * Convenção interna: google_location_id armazena o caminho v4 completo
     * "accounts/{account}/locations/{location}". Este helper devolve só
     * "locations/{location}", usado pela Performance API v1.
     */
    public static function locationOnly(string $v4Name): string
    {
        if (preg_match('#(locations/[^/]+)#', $v4Name, $m)) {
            return $m[1];
        }
        return $v4Name;
    }

    public function getAuthUrl(string $state): string
    {
        $this->client->setState($state);
        return $this->client->createAuthUrl();
    }

    public function authenticateAndSaveTokens(string $code, $cliente): void
    {
        $token = $this->client->fetchAccessTokenWithAuthCode($code);

        if (isset($token['error'])) {
            throw new \Exception('Erro de autenticação Google: ' . $token['error']);
        }

        $updateData = [
            'google_access_token'     => Crypt::encryptString($token['access_token']),
            'google_token_expires_at' => Carbon::now()->addSeconds((int) ($token['expires_in'] ?? 0)),
        ];

        if (!empty($token['refresh_token'])) {
            $updateData['google_refresh_token'] = Crypt::encryptString($token['refresh_token']);
        }

        $cliente->update($updateData);
    }

    protected function setClientForCliente($cliente): void
    {
        if (!$cliente->google_access_token) {
            throw new \Exception('Conta Google não conectada.');
        }

        try {
            $accessToken = Crypt::decryptString($cliente->google_access_token);
        } catch (\Throwable $e) {
            Log::warning('[GoogleBusiness] Falha ao descriptografar access_token do cliente ' . $cliente->id);
            throw new \Exception('Não conseguimos ler suas credenciais salvas. Desconecte e reconecte sua conta Google.');
        }

        // A lib do Google só sabe se o token expirou quando recebe um array.
        // Passando só a string, isAccessTokenExpired() volta sempre true e o
        // refresh dispara em toda chamada (ou nem dispara se não tiver refresh).
        $expiresAt = $cliente->google_token_expires_at
            ? Carbon::parse($cliente->google_token_expires_at)->timestamp
            : time();
        $this->client->setAccessToken([
            'access_token' => $accessToken,
            'created'      => time(),
            'expires_in'   => max(0, $expiresAt - time()),
        ]);

        if (!$this->client->isAccessTokenExpired()) {
            return;
        }

        if (!$cliente->google_refresh_token) {
            throw new \Exception('Sua sessão do Google expirou. Reconecte a conta para continuar.');
        }

        try {
            $refreshToken = Crypt::decryptString($cliente->google_refresh_token);
        } catch (\Throwable $e) {
            Log::warning('[GoogleBusiness] Falha ao descriptografar refresh_token do cliente ' . $cliente->id);
            throw new \Exception('Não conseguimos renovar seu acesso. Desconecte e reconecte sua conta Google.');
        }

        $newToken = $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
        if (isset($newToken['error'])) {
            Log::warning('[GoogleBusiness] Refresh falhou para cliente ' . $cliente->id . ': ' . ($newToken['error'] ?? ''));
            throw new \Exception('Sua sessão do Google expirou. Reconecte a conta para continuar.');
        }

        $cliente->update([
            'google_access_token'     => Crypt::encryptString($newToken['access_token']),
            'google_token_expires_at' => Carbon::now()->addSeconds((int) ($newToken['expires_in'] ?? 0)),
        ]);
    }

    /**
     * Wrapper único de HTTP com tradução de erro do Google pra pt-BR.
     * Todos os endpoints (v1 + v4 + Performance) passam por aqui — se algo
     * quebrar, o log tem status+body pra diagnóstico.
     */
    protected function request($cliente, string $method, string $url, array $options, string $contexto): array
    {
        $http = $this->client->authorize();

        try {
            $response = $http->request($method, $url, $options);
            $body = (string) $response->getBody();
            return $body === '' ? [] : (json_decode($body, true) ?? []);
        } catch (\Throwable $e) {
            $status = 0;
            $body = '';
            $googleMsg = '';
            if ($e instanceof \GuzzleHttp\Exception\RequestException && $e->hasResponse()) {
                $resp = $e->getResponse();
                $status = $resp->getStatusCode();
                $body = (string) $resp->getBody();
                $decoded = json_decode($body, true);
                $googleMsg = $decoded['error']['message'] ?? '';
            }

            Log::error("[GoogleBusiness] {$contexto} falhou para cliente {$cliente->id} status={$status} body={$body}");

            if (stripos($body, 'SERVICE_DISABLED') !== false || stripos($body, 'has not been used in project') !== false) {
                throw new \Exception('A API do Google Meu Negócio não está ativada neste projeto do Cloud Console. Fale com o suporte.');
            }
            if ($status === 429 || stripos($body, 'RESOURCE_EXHAUSTED') !== false || stripos($body, 'Quota exceeded') !== false) {
                throw new \Exception('A cota da API do Google foi atingida ou ainda está zerada para este projeto. Tente novamente mais tarde.');
            }
            if ($status === 401) {
                throw new \Exception('Sua sessão do Google expirou. Reconecte a conta para continuar.');
            }
            if ($status === 403) {
                throw new \Exception('O Google recusou o acesso' . ($googleMsg ? ': ' . $googleMsg : '.'));
            }
            if ($status === 404) {
                throw new \Exception('Recurso não encontrado no Google (' . $contexto . ').');
            }
            if ($status === 400) {
                throw new \Exception('O Google recusou os dados enviados' . ($googleMsg ? ': ' . $googleMsg : '.'));
            }

            throw new \Exception('Falha temporária na comunicação com o Google. Tente novamente em instantes.');
        }
    }

    /**
     * Lista fichas do cliente com paginação em contas e locations.
     * Retorna array de locations, cada uma com _account, _account_name e _v4_name.
     */
    public function getLocations($cliente): array
    {
        $this->setClientForCliente($cliente);

        $accounts = [];
        $pageToken = null;
        do {
            $query = ['pageSize' => 20];
            if ($pageToken) $query['pageToken'] = $pageToken;
            $data = $this->request(
                $cliente,
                'GET',
                'https://mybusinessaccountmanagement.googleapis.com/v1/accounts',
                ['query' => $query],
                'listar contas'
            );
            foreach (($data['accounts'] ?? []) as $acc) {
                $accounts[] = $acc;
            }
            $pageToken = $data['nextPageToken'] ?? null;
        } while ($pageToken);

        if (empty($accounts)) {
            Log::info('[GoogleBusiness] Cliente ' . $cliente->id . ' autenticou mas não tem contas associadas.');
            throw new \Exception('Nenhuma conta do Google Meu Negócio encontrada nesta conta Google.');
        }

        $locations = [];
        foreach ($accounts as $account) {
            $accountPath = $account['name'] ?? null; // "accounts/123"
            if (!$accountPath) continue;

            $locPageToken = null;
            do {
                $query = [
                    'pageSize' => 100,
                    'readMask' => 'name,title,storeCode,storefrontAddress,websiteUri',
                ];
                if ($locPageToken) $query['pageToken'] = $locPageToken;

                $locData = $this->request(
                    $cliente,
                    'GET',
                    "https://mybusinessbusinessinformation.googleapis.com/v1/{$accountPath}/locations",
                    ['query' => $query],
                    "listar fichas de {$accountPath}"
                );

                foreach (($locData['locations'] ?? []) as $loc) {
                    $loc['_account']      = $accountPath;
                    $loc['_account_name'] = $account['accountName'] ?? $accountPath;
                    // v4 name = "accounts/{a}/locations/{l}"
                    $loc['_v4_name']      = $accountPath . '/' . ($loc['name'] ?? '');
                    $locations[] = $loc;
                }

                $locPageToken = $locData['nextPageToken'] ?? null;
            } while ($locPageToken);
        }

        return $locations;
    }

    /**
     * Métricas de desempenho (Business Profile Performance API v1).
     * Se $end não vier, usa ontem (o Google leva 2-3 dias pra fechar o dado atual).
     * Retorna totais, breakdown mobile/desktop, séries diárias por métrica.
     */
    public function getPerformanceMetrics($cliente, string $v4Name, int $dias = 30, ?Carbon $end = null): array
    {
        $this->setClientForCliente($cliente);

        $locOnly = self::locationOnly($v4Name);

        $end   = $end ? $end->copy() : Carbon::yesterday();
        $start = $end->copy()->subDays(max(1, $dias) - 1);

        $metricas = [
            'BUSINESS_IMPRESSIONS_DESKTOP_MAPS',
            'BUSINESS_IMPRESSIONS_DESKTOP_SEARCH',
            'BUSINESS_IMPRESSIONS_MOBILE_MAPS',
            'BUSINESS_IMPRESSIONS_MOBILE_SEARCH',
            'WEBSITE_CLICKS',
            'CALL_CLICKS',
            'BUSINESS_DIRECTION_REQUESTS',
        ];

        // Query string montada NA MÃO: o Guzzle serializaria como
        // dailyMetrics[0]=X&dailyMetrics[1]=Y e a API do Google rejeita.
        // Ela quer dailyMetrics=X&dailyMetrics=Y repetido.
        $params = [];
        foreach ($metricas as $m) {
            $params[] = 'dailyMetrics=' . rawurlencode($m);
        }
        $params[] = 'dailyRange.start_date.year='  . $start->year;
        $params[] = 'dailyRange.start_date.month=' . $start->month;
        $params[] = 'dailyRange.start_date.day='   . $start->day;
        $params[] = 'dailyRange.end_date.year='    . $end->year;
        $params[] = 'dailyRange.end_date.month='   . $end->month;
        $params[] = 'dailyRange.end_date.day='     . $end->day;
        $qs = implode('&', $params);

        $url = "https://businessprofileperformance.googleapis.com/v1/{$locOnly}:fetchMultiDailyMetricsTimeSeries?{$qs}";

        $data = $this->request($cliente, 'GET', $url, [], 'métricas de desempenho');

        // Somas
        $total = array_fill_keys($metricas, 0);
        // Séries por métrica agregada ('Y-m-d' => int)
        $serieImpressoes = [];
        $serieCliques    = [];
        $serieLigacoes   = [];
        $serieRotas      = [];

        foreach (($data['multiDailyMetricTimeSeries'] ?? []) as $multi) {
            foreach (($multi['dailyMetricTimeSeries'] ?? []) as $seriesEntry) {
                $metric = $seriesEntry['dailyMetric'] ?? null;
                $dated  = $seriesEntry['timeSeries']['datedValues'] ?? [];
                foreach ($dated as $dv) {
                    // "value" vem como string; é omitido quando vale 0.
                    $valor = isset($dv['value']) ? (int) $dv['value'] : 0;
                    if ($metric && isset($total[$metric])) {
                        $total[$metric] += $valor;
                    }

                    $d = $dv['date'] ?? null;
                    if (!$d || !isset($d['year'], $d['month'], $d['day'])) continue;
                    $key = sprintf('%04d-%02d-%02d', $d['year'], $d['month'], $d['day']);

                    switch ($metric) {
                        case 'BUSINESS_IMPRESSIONS_DESKTOP_MAPS':
                        case 'BUSINESS_IMPRESSIONS_DESKTOP_SEARCH':
                        case 'BUSINESS_IMPRESSIONS_MOBILE_MAPS':
                        case 'BUSINESS_IMPRESSIONS_MOBILE_SEARCH':
                            $serieImpressoes[$key] = ($serieImpressoes[$key] ?? 0) + $valor;
                            break;
                        case 'WEBSITE_CLICKS':
                            $serieCliques[$key] = ($serieCliques[$key] ?? 0) + $valor;
                            break;
                        case 'CALL_CLICKS':
                            $serieLigacoes[$key] = ($serieLigacoes[$key] ?? 0) + $valor;
                            break;
                        case 'BUSINESS_DIRECTION_REQUESTS':
                            $serieRotas[$key] = ($serieRotas[$key] ?? 0) + $valor;
                            break;
                    }
                }
            }
        }

        ksort($serieImpressoes);
        ksort($serieCliques);
        ksort($serieLigacoes);
        ksort($serieRotas);

        $maps  = $total['BUSINESS_IMPRESSIONS_DESKTOP_MAPS']   + $total['BUSINESS_IMPRESSIONS_MOBILE_MAPS'];
        $busca = $total['BUSINESS_IMPRESSIONS_DESKTOP_SEARCH'] + $total['BUSINESS_IMPRESSIONS_MOBILE_SEARCH'];
        $desktop = $total['BUSINESS_IMPRESSIONS_DESKTOP_MAPS'] + $total['BUSINESS_IMPRESSIONS_DESKTOP_SEARCH'];
        $mobile  = $total['BUSINESS_IMPRESSIONS_MOBILE_MAPS']  + $total['BUSINESS_IMPRESSIONS_MOBILE_SEARCH'];

        return [
            'periodo'          => ['inicio' => $start->toDateString(), 'fim' => $end->toDateString(), 'dias' => $dias],
            'impressoes'       => $maps + $busca,
            'impressoes_maps'  => $maps,
            'impressoes_busca' => $busca,
            'impressoes_desktop' => $desktop,
            'impressoes_mobile'  => $mobile,
            'cliques_site'     => $total['WEBSITE_CLICKS'],
            'ligacoes'         => $total['CALL_CLICKS'],
            'rotas'            => $total['BUSINESS_DIRECTION_REQUESTS'],
            // 'serie' é a de impressões (compat com view antiga)
            'serie'            => $serieImpressoes,
            'serie_impressoes' => $serieImpressoes,
            'serie_cliques'    => $serieCliques,
            'serie_ligacoes'   => $serieLigacoes,
            'serie_rotas'      => $serieRotas,
        ];
    }

    /**
     * Compara $dias atuais (ontem - $dias + 1 .. ontem) com os $dias imediatamente
     * anteriores. Devolve totais, deltas em % (null quando o anterior é 0 e o
     * atual não, pra view renderizar "novo" em vez de infinito).
     */
    public function getPerformanceComparison($cliente, string $v4Name, int $dias = 30): array
    {
        $ontem = Carbon::yesterday();
        $atual    = $this->getPerformanceMetrics($cliente, $v4Name, $dias, $ontem);
        $anterior = $this->getPerformanceMetrics($cliente, $v4Name, $dias, $ontem->copy()->subDays($dias));

        $delta = function ($novo, $velho) {
            if ($velho === 0) {
                return $novo === 0 ? 0.0 : null; // null = "novo"
            }
            return round((($novo - $velho) / $velho) * 100, 1);
        };

        return [
            'atual'    => $atual,
            'anterior' => $anterior,
            'delta'    => [
                'impressoes'   => $delta($atual['impressoes'],   $anterior['impressoes']),
                'cliques_site' => $delta($atual['cliques_site'], $anterior['cliques_site']),
                'ligacoes'     => $delta($atual['ligacoes'],     $anterior['ligacoes']),
                'rotas'        => $delta($atual['rotas'],        $anterior['rotas']),
            ],
        ];
    }

    // -----------------------------------------------------------------
    // Posts (API v4)
    // -----------------------------------------------------------------

    public function listPosts($cliente, string $v4Name): array
    {
        $this->setClientForCliente($cliente);
        $data = $this->request(
            $cliente,
            'GET',
            "https://mybusiness.googleapis.com/v4/{$v4Name}/localPosts",
            ['query' => ['pageSize' => 10]],
            'listar posts'
        );
        return $data['localPosts'] ?? [];
    }

    public function createPost($cliente, string $v4Name, string $summary, array $opts = []): array
    {
        $this->setClientForCliente($cliente);

        $body = [
            'languageCode' => 'pt-BR',
            'summary'      => $summary,
            'topicType'    => 'STANDARD',
        ];

        $ctaType = $opts['cta_type'] ?? null;
        $ctaUrl  = $opts['cta_url']  ?? null;
        if ($ctaType) {
            $cta = ['actionType' => $ctaType];
            if ($ctaType !== 'CALL' && $ctaUrl) {
                $cta['url'] = $ctaUrl;
            }
            $body['callToAction'] = $cta;
        }

        $imageUrl = $opts['image_url'] ?? null;
        if ($imageUrl) {
            $body['media'] = [[
                'mediaFormat' => 'PHOTO',
                'sourceUrl'   => $imageUrl,
            ]];
        }

        return $this->request(
            $cliente,
            'POST',
            "https://mybusiness.googleapis.com/v4/{$v4Name}/localPosts",
            ['json' => $body],
            'criar post'
        );
    }

    public function deletePost($cliente, string $postName): array
    {
        $this->setClientForCliente($cliente);
        return $this->request(
            $cliente,
            'DELETE',
            "https://mybusiness.googleapis.com/v4/{$postName}",
            [],
            'remover post'
        );
    }

    // -----------------------------------------------------------------
    // Avaliações (API v4)
    // -----------------------------------------------------------------

    public function listReviews($cliente, string $v4Name): array
    {
        $this->setClientForCliente($cliente);
        $data = $this->request(
            $cliente,
            'GET',
            "https://mybusiness.googleapis.com/v4/{$v4Name}/reviews",
            ['query' => ['pageSize' => 20, 'orderBy' => 'updateTime desc']],
            'listar avaliações'
        );

        return [
            'media'   => (float) ($data['averageRating'] ?? 0),
            'total'   => (int)   ($data['totalReviewCount'] ?? 0),
            'reviews' => $data['reviews'] ?? [],
        ];
    }

    public function replyReview($cliente, string $reviewName, string $comment): array
    {
        $this->setClientForCliente($cliente);
        return $this->request(
            $cliente,
            'PUT',
            "https://mybusiness.googleapis.com/v4/{$reviewName}/reply",
            ['json' => ['comment' => $comment]],
            'responder avaliação'
        );
    }

    public static function estrelas(?string $starRating): int
    {
        return [
            'ONE'   => 1,
            'TWO'   => 2,
            'THREE' => 3,
            'FOUR'  => 4,
            'FIVE'  => 5,
        ][$starRating ?? ''] ?? 0;
    }
}
