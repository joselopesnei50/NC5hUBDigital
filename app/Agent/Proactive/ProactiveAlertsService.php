<?php

declare(strict_types=1);

namespace App\Agent\Proactive;

use App\Agent\SnapshotBuilder;
use App\Models\AgentAlert;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProactiveAlertsService
{
    public function __construct(
        private SnapshotBuilder $snapshotBuilder
    ) {
    }

    /**
     * Roda a analise proativa para UM tenant.
     * Retorna a colecao dos AgentAlert efetivamente criados nesta rodada
     * (apos dedup). Usada pelo command para disparar email quando ha criticos.
     */
    public function analyzeTenant(int $tenantId, int $periodDays = 30): \Illuminate\Support\Collection
    {
        try {
            $snapshot = $this->snapshotBuilder->build($tenantId, $periodDays);
        } catch (Throwable $e) {
            Log::warning('[ProactiveAlerts] snapshot falhou cliente=' . $tenantId . ' erro=' . $e->getMessage());
            return collect();
        }

        $metricas = $snapshot['metricas'] ?? [];
        $candidatos = $this->runRules($metricas);
        $criados = collect();

        foreach ($candidatos as $c) {
            if ($this->jaExiste($tenantId, $c['tipo'])) {
                continue;
            }

            $alerta = AgentAlert::create([
                'cliente_id' => $tenantId,
                'tipo' => $c['tipo'],
                'severidade' => $c['severidade'],
                'titulo' => $c['titulo'],
                'mensagem' => $c['mensagem'],
                'detalhes' => $c['detalhes'] ?? null,
            ]);
            $criados->push($alerta);
        }

        return $criados;
    }

    /**
     * @return array<int, array{tipo:string, severidade:string, titulo:string, mensagem:string, detalhes?:array}>
     */
    private function runRules(array $metricas): array
    {
        $alertas = [];

        $caixa = $metricas['fluxo_caixa'] ?? [];
        $receitas = (float) ($caixa['receitas_realizadas_brl'] ?? 0);
        $saldo = (float) ($caixa['saldo_operacional_brl'] ?? 0);
        $inadimplencia = (float) ($caixa['inadimplencia_estimada_brl'] ?? 0);

        // R1 — Caixa operacional negativo no periodo
        if ($saldo < 0) {
            $alertas[] = [
                'tipo' => 'caixa_negativo',
                'severidade' => 'critico',
                'titulo' => 'Seu caixa operacional está negativo',
                'mensagem' => 'Nos últimos 30 dias, suas despesas superaram as receitas realizadas. Vale revisar cobranças em aberto e priorizar contas a pagar.',
                'detalhes' => ['saldo_brl' => $saldo, 'receitas_brl' => $receitas],
            ];
        }

        // R2 — Inadimplencia > 20% da receita
        if ($receitas > 0 && $inadimplencia > 0 && ($inadimplencia / $receitas) > 0.20) {
            $pct = round(($inadimplencia / $receitas) * 100, 1);
            $alertas[] = [
                'tipo' => 'inadimplencia_alta',
                'severidade' => 'atencao',
                'titulo' => "Inadimplência acima de 20% da receita ({$pct}%)",
                'mensagem' => 'A soma de recebíveis vencidos ultrapassa 20% do que você já recebeu. Ative um mutirão de cobrança e priorize os maiores valores.',
                'detalhes' => ['inadimplencia_brl' => $inadimplencia, 'receitas_brl' => $receitas, 'percentual' => $pct],
            ];
        }

        // R3 — Faturas com a NC5 em atraso
        $faturasNc5 = $metricas['faturas_com_nc5'] ?? [];
        $emAtrasoNc5 = (float) ($faturasNc5['valor_em_atraso_brl'] ?? 0);
        if ($emAtrasoNc5 > 0) {
            $qtd = (int) ($faturasNc5['quantidade_em_atraso'] ?? 0);
            $alertas[] = [
                'tipo' => 'faturas_atraso_nc5',
                'severidade' => 'critico',
                'titulo' => 'Você tem faturas em atraso com a NC5',
                'mensagem' => "Você tem {$qtd} fatura(s) vencida(s) com a NC5, somando R$ " . number_format($emAtrasoNc5, 2, ',', '.') . '. Regularize em Minhas Faturas para não interromper serviços.',
                'detalhes' => ['valor_em_atraso_brl' => $emAtrasoNc5, 'quantidade' => $qtd],
            ];
        }

        // R4 — Cliente do TOP 5 sumiu
        $topClientes = $metricas['top_clientes']['top_clientes_no_periodo'] ?? [];
        $sumidos = $metricas['clientes_sumidos']['exemplos'] ?? [];
        $sumidosIds = array_column($sumidos, 'cliente_final_id');

        foreach ($topClientes as $top) {
            $topId = (int) ($top['cliente_final_id'] ?? 0);
            if ($topId && in_array($topId, $sumidosIds)) {
                $alertas[] = [
                    'tipo' => 'top_cliente_sumido_' . $topId,
                    'severidade' => 'atencao',
                    'titulo' => 'Cliente do seu TOP 5 parou de comprar: ' . ($top['nome'] ?? 'sem nome'),
                    'mensagem' => "O cliente {$top['nome']} apareceu como um dos que mais compraram no período, mas está listado como sumido agora. Considere ligar ou mandar mensagem de reativação.",
                    'detalhes' => ['cliente_final_id' => $topId, 'nome' => $top['nome'] ?? null, 'total_gasto_brl' => $top['total_gasto_brl'] ?? 0],
                ];
                break; // um por rodada para nao explodir
            }
        }

        // R5 — Muitos clientes sumidos
        $qtdSumidos = (int) ($metricas['clientes_sumidos']['quantidade_sumidos'] ?? 0);
        $totalBase = (int) ($metricas['segmentacao_clientes']['total_base_clientes'] ?? 0);
        if ($qtdSumidos >= 5 && $totalBase > 0 && ($qtdSumidos / $totalBase) > 0.30) {
            $pct = round(($qtdSumidos / $totalBase) * 100, 1);
            $alertas[] = [
                'tipo' => 'muitos_sumidos',
                'severidade' => 'atencao',
                'titulo' => "{$qtdSumidos} clientes sumidos ({$pct}% da sua base)",
                'mensagem' => 'Uma fatia relevante da sua base parou de comprar. Vale rodar uma campanha de reativação. Peça ao Bruce: "quais são meus sumidos e redige mensagem pra cada".',
                'detalhes' => ['quantidade' => $qtdSumidos, 'percentual' => $pct, 'total_base' => $totalBase],
            ];
        }

        // R6 — Projetos travados esperando cliente
        $travados = (int) ($metricas['entregas_operacionais']['projetos_travados_pelo_cliente'] ?? 0);
        if ($travados >= 3) {
            $alertas[] = [
                'tipo' => 'projetos_travados',
                'severidade' => 'info',
                'titulo' => "{$travados} projetos aguardando resposta sua",
                'mensagem' => 'Há projetos em andamento esperando uma ação sua para destravar. Verifique Meus Projetos e libere o que puder.',
                'detalhes' => ['quantidade' => $travados],
            ];
        }

        // ==== Google Meu Negócio ================================================
        // Regras se auto-inibem quando o cliente não conectou o Google ou não
        // selecionou uma ficha — evita spamar quem não usa o módulo.
        $gbp = $metricas['google_business'] ?? [];
        if (!empty($gbp['conectado']) && !empty($gbp['ficha_selecionada'])) {
            // R7 — Média de estrelas caiu para menos de 4.0 (com base amostral > 5)
            $media = (float) ($gbp['media_estrelas'] ?? 0);
            $totalReviews = (int) ($gbp['total_reviews'] ?? 0);
            if ($totalReviews >= 5 && $media > 0 && $media < 4.0) {
                $alertas[] = [
                    'tipo' => 'gbp_estrelas_baixas',
                    'severidade' => 'critico',
                    'titulo' => 'Sua média no Google caiu para ' . number_format($media, 1, ',', '.'),
                    'mensagem' => 'A sua ficha do Google Meu Negócio está com média abaixo de 4,0 estrelas (' . number_format($media, 1, ',', '.') . ' em ' . $totalReviews . ' avaliações). Vale rever as últimas avaliações negativas e responder publicamente antes que o ranking caia.',
                    'detalhes' => ['media' => $media, 'total_reviews' => $totalReviews],
                ];
            }

            // R8 — Zero posts publicados no período (janela do próprio snapshot)
            $posts30 = (int) ($gbp['posts_30d'] ?? 0);
            if ($posts30 === 0) {
                $alertas[] = [
                    'tipo' => 'gbp_sem_posts',
                    'severidade' => 'atencao',
                    'titulo' => 'Nenhuma publicação no Google nos últimos 30 dias',
                    'mensagem' => 'Fichas ativas com posts recentes aparecem mais no Google. Publique uma novidade, promoção ou horário especial pela aba Google Meu Negócio.',
                    'detalhes' => ['posts_30d' => $posts30],
                ];
            }

            // R9 — Queda maior que 20% nas visualizações (só faz sentido se
            // havia base — anterior_impressoes > 0)
            $deltaImp = $gbp['delta_impressoes_pct'] ?? null;
            $anteriorImp = (int) ($gbp['anterior_impressoes'] ?? 0);
            if ($deltaImp !== null && $anteriorImp > 0 && $deltaImp <= -20) {
                $absDelta = abs((float) $deltaImp);
                $alertas[] = [
                    'tipo' => 'gbp_visualizacoes_caindo',
                    'severidade' => 'atencao',
                    'titulo' => 'Suas visualizações no Google caíram ' . number_format($absDelta, 1, ',', '.') . '%',
                    'mensagem' => 'Nos últimos 30 dias a sua ficha teve ' . number_format($absDelta, 1, ',', '.') . '% menos visualizações que no período anterior. Confira se algo mudou na sua ficha (horário, fotos, descrição) e publique uma novidade.',
                    'detalhes' => ['delta_pct' => (float) $deltaImp, 'anterior_impressoes' => $anteriorImp],
                ];
            }

            // R10 — Avaliação sem resposta há 3+ dias
            $diasMax = (int) ($gbp['reviews_sem_resposta_dias_max'] ?? 0);
            $qtdSemResp = (int) ($gbp['reviews_sem_resposta_qtd'] ?? 0);
            if ($diasMax >= 3 && $qtdSemResp > 0) {
                $alertas[] = [
                    'tipo' => 'gbp_avaliacao_sem_resposta',
                    'severidade' => 'atencao',
                    'titulo' => 'Você tem avaliação sem resposta há ' . $diasMax . ' dias',
                    'mensagem' => 'Responder avaliações — inclusive as positivas — melhora o ranking da ficha no Google. Você tem ' . $qtdSemResp . ' avaliação(ões) pendente(s) de resposta.',
                    'detalhes' => ['dias_max' => $diasMax, 'quantidade' => $qtdSemResp],
                ];
            }
        }

        return $alertas;
    }

    /**
     * Ja existe um alerta do mesmo tipo ainda nao dispensado?
     * Evita spammar o gestor com o mesmo problema todos os dias.
     */
    private function jaExiste(int $tenantId, string $tipo): bool
    {
        return AgentAlert::query()
            ->doCliente($tenantId)
            ->where('tipo', $tipo)
            ->ativos()
            ->exists();
    }
}
