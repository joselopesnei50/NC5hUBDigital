<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Agent\Proactive\ProactiveAlertsService;
use App\Mail\BruceAlertMail;
use App\Models\Cliente;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class RunProactiveAlerts extends Command
{
    protected $signature = 'bruce:proactive-alerts
                            {--cliente= : Rodar apenas para um cliente_id específico}
                            {--days=30 : Janela de análise em dias}
                            {--no-email : Não enviar e-mail dos alertas críticos, só gravar}';

    protected $description = 'Analisa cada cliente e gera alertas proativos (caixa, cobrança, sumidos, projetos travados). Envia e-mail quando surgem alertas críticos.';

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

        $enviarEmail = !$this->option('no-email');
        $totalNovos = 0;
        $totalEmails = 0;

        foreach ($clientes as $c) {
            try {
                $criados = $service->analyzeTenant((int) $c->id, $days);
                $totalNovos += $criados->count();

                if ($criados->isEmpty()) {
                    continue;
                }

                $this->line("  #{$c->id} {$c->razao_social}: {$criados->count()} alerta(s) novo(s)");

                if (!$enviarEmail) {
                    continue;
                }

                $criticos = $criados->where('severidade', 'critico');
                if ($criticos->isEmpty()) {
                    continue;
                }

                if ($this->enviarEmailAlertas($c, $criticos)) {
                    $totalEmails++;
                    $this->line("     ✉ email crítico enviado ({$criticos->count()} alerta(s))");
                }
            } catch (Throwable $e) {
                $this->error("  #{$c->id} {$c->razao_social}: FALHOU — " . $e->getMessage());
            }
        }

        $this->info("Concluído. Novos alertas: {$totalNovos} · e-mails enviados: {$totalEmails}.");
        return self::SUCCESS;
    }

    /**
     * Envia e-mail de alertas críticos para o gestor. Falha graciosamente:
     * loga o erro e retorna false sem quebrar o cron.
     */
    private function enviarEmailAlertas(Cliente $cliente, $criticos): bool
    {
        $cliente->loadMissing('user');
        $destinatario = $cliente->user?->email;

        if (!$destinatario) {
            Log::warning('[ProactiveAlerts] cliente ' . $cliente->id . ' sem user/email — email crítico não enviado');
            return false;
        }

        try {
            Mail::to($destinatario)->send(new BruceAlertMail($cliente, $criticos));
            return true;
        } catch (Throwable $e) {
            Log::warning('[ProactiveAlerts] falha ao enviar email crítico cliente=' . $cliente->id . ' erro=' . $e->getMessage());
            return false;
        }
    }
}
