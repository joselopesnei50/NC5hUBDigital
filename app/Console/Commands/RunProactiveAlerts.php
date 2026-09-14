<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Agent\Proactive\ProactiveAlertsService;
use App\Models\Cliente;
use Illuminate\Console\Command;
use Throwable;

class RunProactiveAlerts extends Command
{
    protected $signature = 'bruce:proactive-alerts
                            {--cliente= : Rodar apenas para um cliente_id específico}
                            {--days=30 : Janela de análise em dias}';

    protected $description = 'Analisa cada cliente e gera alertas proativos (caixa, cobrança, sumidos, projetos travados).';

    public function handle(ProactiveAlertsService $service): int
    {
        $days = (int) $this->option('days');
        if ($days < 1 || $days > 365) $days = 30;

        $q = Cliente::query();

        if ($clienteId = $this->option('cliente')) {
            $q->where('id', (int) $clienteId);
        }

        $clientes = $q->get(['id', 'razao_social']);

        if ($clientes->isEmpty()) {
            $this->warn('Nenhum cliente encontrado para analisar.');
            return self::SUCCESS;
        }

        $this->info("Analisando {$clientes->count()} cliente(s) — janela {$days} dias.");

        $totalNovos = 0;
        foreach ($clientes as $c) {
            try {
                $criados = $service->analyzeTenant((int) $c->id, $days);
                $totalNovos += $criados;
                if ($criados > 0) {
                    $this->line("  #{$c->id} {$c->razao_social}: {$criados} alerta(s) novo(s)");
                }
            } catch (Throwable $e) {
                $this->error("  #{$c->id} {$c->razao_social}: FALHOU — " . $e->getMessage());
            }
        }

        $this->info("Concluído. Total de novos alertas: {$totalNovos}.");
        return self::SUCCESS;
    }
}
