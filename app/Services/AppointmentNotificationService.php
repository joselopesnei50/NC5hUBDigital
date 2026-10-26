<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AgentAlert;
use App\Models\Appointment;
use App\Models\WhatsappBotTraining;
use App\Models\WhatsappInstance;
use Illuminate\Support\Facades\Log;
use Throwable;

class AppointmentNotificationService
{
    public function __construct(
        protected WhatsappSenderService $sender
    ) {}

    /**
     * Registra notificação no painel e dispara alerta no WhatsApp do gestor quando um agendamento é criado.
     */
    public function notifyCreated(Appointment $apt): void
    {
        $apt->loadMissing(['clienteFinal', 'resource']);

        $nomeCliente = $apt->clienteFinal?->nome_responsavel
            ?: ($apt->contato_nome ?: 'Cliente');
        $telefoneCliente = $apt->contato_telefone ?: '—';
        $dataHora = $apt->scheduled_at->format('d/m/Y \à\s H:i');
        $recurso = $apt->resource?->nome;

        // 1. Notificação no painel (Sininho e Central de Alertas)
        $this->createPanelAlert(
            clienteId: (int) $apt->cliente_id,
            tipo: 'agenda_novo',
            severidade: 'info',
            titulo: "📅 Novo Agendamento: {$apt->titulo}",
            mensagem: "{$nomeCliente} ({$telefoneCliente}) agendou \"{$apt->titulo}\" para {$dataHora}" . ($recurso ? " com {$recurso}." : '.'),
            detalhes: [
                'appointment_id' => $apt->id,
                'evento' => 'criado',
                'cliente_nome' => $nomeCliente,
                'cliente_telefone' => $telefoneCliente,
                'scheduled_at' => $apt->scheduled_at->toIso8601String(),
                'data_hora_legivel' => $dataHora,
                'recurso' => $recurso,
            ]
        );

        // 2. Disparo no WhatsApp do gestor (se configurado)
        $training = WhatsappBotTraining::where('cliente_id', $apt->cliente_id)->first();
        if (!$training || !$this->shouldSendWhatsApp($training, 'booking')) {
            return;
        }

        $linhas = [
            "📅 *[Agenda BruceIA] Novo Agendamento*",
            "👤 *Cliente:* {$nomeCliente}",
            "📲 *WhatsApp:* {$telefoneCliente}",
            "📌 *Compromisso:* {$apt->titulo}",
            "🕒 *Data/Hora:* {$dataHora} ({$apt->duration_minutes} min)",
        ];
        if ($recurso) {
            $linhas[] = "👨‍💼 *Recurso:* {$recurso}";
        }
        $linhas[] = "⏳ *Status:* Agendado (o robô enviará lembrete de confirmação antes do horário)";

        $this->sendToManagerPhone((int) $apt->cliente_id, (string) $training->manager_notification_phone, implode("\n", $linhas));
    }

    /**
     * Registra notificação no painel e dispara alerta no WhatsApp do gestor quando o cliente confirma presença.
     */
    public function notifyConfirmed(Appointment $apt): void
    {
        $apt->loadMissing(['clienteFinal', 'resource']);

        $nomeCliente = $apt->clienteFinal?->nome_responsavel
            ?: ($apt->contato_nome ?: 'Cliente');
        $telefoneCliente = $apt->contato_telefone ?: '—';
        $dataHora = $apt->scheduled_at->format('d/m/Y \à\s H:i');
        $recurso = $apt->resource?->nome;

        // 1. Notificação no painel
        $this->createPanelAlert(
            clienteId: (int) $apt->cliente_id,
            tipo: 'agenda_confirmado',
            severidade: 'info',
            titulo: "✅ Agendamento Confirmado: {$apt->titulo}",
            mensagem: "{$nomeCliente} confirmou presença para {$dataHora}" . ($recurso ? " ({$recurso})." : '.'),
            detalhes: [
                'appointment_id' => $apt->id,
                'evento' => 'confirmado',
                'cliente_nome' => $nomeCliente,
                'cliente_telefone' => $telefoneCliente,
                'scheduled_at' => $apt->scheduled_at->toIso8601String(),
                'data_hora_legivel' => $dataHora,
                'recurso' => $recurso,
            ]
        );

        // 2. Disparo no WhatsApp do gestor
        $training = WhatsappBotTraining::where('cliente_id', $apt->cliente_id)->first();
        if (!$training || !$this->shouldSendWhatsApp($training, 'confirm')) {
            return;
        }

        $linhas = [
            "✅ *[Agenda BruceIA] Presença Confirmada!*",
            "👤 *Cliente:* {$nomeCliente} ({$telefoneCliente})",
            "📌 *Compromisso:* {$apt->titulo}",
            "🕒 *Data/Hora:* {$dataHora}",
        ];
        if ($recurso) {
            $linhas[] = "👨‍💼 *Recurso:* {$recurso}";
        }
        $linhas[] = "🎯 O cliente confirmou pelo WhatsApp e o horário está mantido na agenda!";

        $this->sendToManagerPhone((int) $apt->cliente_id, (string) $training->manager_notification_phone, implode("\n", $linhas));
    }

    /**
     * Registra notificação no painel e dispara alerta no WhatsApp do gestor quando o agendamento é cancelado.
     */
    public function notifyCancelled(Appointment $apt, ?string $motivo = null): void
    {
        $apt->loadMissing(['clienteFinal', 'resource']);

        $nomeCliente = $apt->clienteFinal?->nome_responsavel
            ?: ($apt->contato_nome ?: 'Cliente');
        $telefoneCliente = $apt->contato_telefone ?: '—';
        $dataHora = $apt->scheduled_at->format('d/m/Y \à\s H:i');
        $recurso = $apt->resource?->nome;
        $motivoTexto = trim((string) $motivo) ?: 'Cancelado pelo cliente no WhatsApp';

        // 1. Notificação no painel
        $this->createPanelAlert(
            clienteId: (int) $apt->cliente_id,
            tipo: 'agenda_cancelado',
            severidade: 'atencao',
            titulo: "❌ Agendamento Cancelado (Horário Liberado)",
            mensagem: "{$nomeCliente} cancelou \"{$apt->titulo}\" de {$dataHora}. O horário já voltou a ficar disponível na agenda.",
            detalhes: [
                'appointment_id' => $apt->id,
                'evento' => 'cancelado',
                'cliente_nome' => $nomeCliente,
                'cliente_telefone' => $telefoneCliente,
                'scheduled_at' => $apt->scheduled_at->toIso8601String(),
                'data_hora_legivel' => $dataHora,
                'recurso' => $recurso,
                'motivo' => $motivoTexto,
            ]
        );

        // 2. Disparo no WhatsApp do gestor
        $training = WhatsappBotTraining::where('cliente_id', $apt->cliente_id)->first();
        if (!$training || !$this->shouldSendWhatsApp($training, 'cancel')) {
            return;
        }

        $linhas = [
            "❌ *[Agenda BruceIA] Agendamento Cancelado*",
            "👤 *Cliente:* {$nomeCliente} ({$telefoneCliente})",
            "📌 *Compromisso:* {$apt->titulo}",
            "🕒 *Horário liberado:* {$dataHora}",
            "📝 *Motivo:* {$motivoTexto}",
            "🔓 *A vaga já foi reaberta automaticamente na agenda para novos clientes!*",
        ];

        $this->sendToManagerPhone((int) $apt->cliente_id, (string) $training->manager_notification_phone, implode("\n", $linhas));
    }

    protected function createPanelAlert(int $clienteId, string $tipo, string $severidade, string $titulo, string $mensagem, array $detalhes = []): void
    {
        try {
            AgentAlert::create([
                'cliente_id' => $clienteId,
                'tipo' => $tipo,
                'severidade' => $severidade,
                'titulo' => $titulo,
                'mensagem' => $mensagem,
                'detalhes' => $detalhes,
            ]);
        } catch (Throwable $e) {
            Log::warning('AppointmentNotificationService: falha ao gravar alerta no painel', [
                'cliente_id' => $clienteId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function shouldSendWhatsApp(WhatsappBotTraining $training, string $event): bool
    {
        $phone = preg_replace('/\D+/', '', (string) $training->manager_notification_phone);
        if (!$phone || strlen($phone) < 10) {
            return false;
        }

        return match ($event) {
            'booking' => (bool) ($training->notify_manager_on_booking ?? true),
            'confirm' => (bool) ($training->notify_manager_on_confirm ?? true),
            'cancel'  => (bool) ($training->notify_manager_on_cancel ?? true),
            default   => true,
        };
    }

    protected function sendToManagerPhone(int $clienteId, string $rawPhone, string $message): void
    {
        try {
            $digits = preg_replace('/\D+/', '', $rawPhone);
            if (!$digits) {
                return;
            }
            if (strlen($digits) === 10 || strlen($digits) === 11) {
                $digits = '55' . $digits;
            }

            $instance = WhatsappInstance::where('cliente_id', $clienteId)
                ->where('status', 'open')
                ->first();

            if (!$instance || !$this->sender->isInstanceReady($instance)) {
                return;
            }

            $this->sender->sendText($instance, $digits, $message);
        } catch (Throwable $e) {
            Log::warning('AppointmentNotificationService: falha ao notificar WhatsApp do gestor', [
                'cliente_id' => $clienteId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
