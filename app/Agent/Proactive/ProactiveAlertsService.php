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
            if ($topId && in_array($topId, $sumidosIds, true)) {
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
