<?php

declare(strict_types=1);

namespace App\Agent\Metrics;

use App\Agent\Contracts\MetricInterface;
use App\Models\Cliente;
use App\Services\GoogleBusinessProfileService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

class GoogleBusinessMetric implements MetricInterface
{
    public function __construct(
        private GoogleBusinessProfileService $service
    ) {
    }

    public function getName(): string
    {
        return 'google_business';
    }

    /**
     * Coleta metricas do Google Meu Negocio pra alimentar as regras proativas.
     * Sempre devolve array — nunca lanca. Se o cliente nao conectou, ou se
     * a API falhar, retorna com `conectado`/`ficha_selecionada` false pra
     * as regras se auto-inibirem.
     */
    public function calculate(int $tenantId, int $periodDays): array
    {
        $base = [
            'conectado'         => false,
            'ficha_selecionada' => false,
            'media_estrelas'    => 0.0,
            'total_reviews'     => 0,
            'posts_30d'         => 0,
            'delta_impressoes_pct'         => null,
            'anterior_impressoes'          => 0,
            'reviews_sem_resposta_dias_max'=> 0,
            'reviews_sem_resposta_qtd'     => 0,
        ];

        $cliente = Cliente::find($tenantId);
        if (!$cliente || empty($cliente->google_refresh_token)) {
            return $base;
        }
        $base['conectado'] = true;

        if (empty($cliente->google_location_id)) {
            return $base;
        }
        $base['ficha_selecionada'] = true;

        $v4Name = $cliente->google_location_id;

        // Reviews — media, total, dias sem resposta
        try {
            $reviews = $this->service->listReviews($cliente, $v4Name);
            $base['media_estrelas'] = (float) ($reviews['media'] ?? 0);
            $base['total_reviews']  = (int)   ($reviews['total'] ?? 0);

            $maxDias = 0;
            $qtdSemResposta = 0;
            $agora = Carbon::now();
            foreach (($reviews['reviews'] ?? []) as $r) {
                if (!empty($r['reviewReply']['comment'] ?? null)) continue;
                $qtdSemResposta++;
                $updateTime = $r['updateTime'] ?? null;
                if (!$updateTime) continue;
                $dias = Carbon::parse($updateTime)->diffInDays($agora);
                if ($dias > $maxDias) $maxDias = $dias;
            }
            $base['reviews_sem_resposta_dias_max'] = $maxDias;
            $base['reviews_sem_resposta_qtd']      = $qtdSemResposta;
        } catch (Throwable $e) {
            Log::warning('[GoogleBusinessMetric] reviews falhou cliente=' . $tenantId . ' erro=' . $e->getMessage());
        }

        // Posts nos ultimos $periodDays
        try {
            $posts = $this->service->listPosts($cliente, $v4Name);
            $limite = Carbon::now()->subDays(max(1, $periodDays));
            $count = 0;
            foreach ($posts as $p) {
                $createTime = $p['createTime'] ?? null;
                if ($createTime && Carbon::parse($createTime)->greaterThanOrEqualTo($limite)) {
                    $count++;
                }
            }
            $base['posts_30d'] = $count;
        } catch (Throwable $e) {
            Log::warning('[GoogleBusinessMetric] posts falhou cliente=' . $tenantId . ' erro=' . $e->getMessage());
        }

        // Comparativo de impressoes vs periodo anterior
        try {
            $cmp = $this->service->getPerformanceComparison($cliente, $v4Name, max(1, $periodDays));
            $base['delta_impressoes_pct'] = $cmp['delta']['impressoes'] ?? null;
            $base['anterior_impressoes']  = (int) ($cmp['anterior']['impressoes'] ?? 0);
        } catch (Throwable $e) {
            Log::warning('[GoogleBusinessMetric] comparison falhou cliente=' . $tenantId . ' erro=' . $e->getMessage());
        }

        return $base;
    }
}
