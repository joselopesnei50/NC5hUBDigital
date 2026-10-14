<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\WhatsappBotTraining;
use App\Models\WhatsappInstance;
use App\Models\WhatsappMessage;
use App\Services\EvolutionApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';
    protected $description = 'Envia lembretes WhatsApp pros agendamentos N horas antes conforme config do cliente';

    public function handle(EvolutionApiService $evo): int
    {
        if (!$evo->isConfigured()) {
            $this->warn('Evolution nao configurada — pulando.');
            return self::SUCCESS;
        }

        $windowStart = now()->subMinutes(5);
        $windowEnd = now()->addMinutes(20);
        $sent = 0;

        $trainings = WhatsappBotTraining::query()
            ->select('cliente_id', 'reminder_hours_before', 'business_name')
            ->get()
            ->keyBy('cliente_id');

        $defaultHours = 2;
        $maxLookahead = (int) ($trainings->max('reminder_hours_before') ?: $defaultHours) + 1;
        $horizonEnd = now()->addHours($maxLookahead);

        $candidates = Appointment::query()
            ->whereIn('status', ['agendado', 'confirmado'])
            ->whereNull('reminder_sent_at')
            ->whereBetween('scheduled_at', [now(), $horizonEnd])
            ->get();

        foreach ($candidates as $apt) {
            try {
                $training = $trainings->get($apt->cliente_id);
                $hoursBefore = (int) ($training?->reminder_hours_before ?? $defaultHours);
                $sendAt = $apt->scheduled_at->copy()->subHours($hoursBefore);

                if ($sendAt->lt($windowStart) || $sendAt->gt($windowEnd)) {
                    continue;
                }

                $phone = $apt->contato_telefone ?: ($apt->chat?->contact_phone ?: $apt->chat?->wa_id);
                if (!$phone) {
                    continue;
                }

                $instance = WhatsappInstance::where('cliente_id', $apt->cliente_id)
                    ->where('status', 'open')
                    ->first();
                if (!$instance) {
                    continue;
                }

                $msg = $this->buildReminderMessage($apt, $training?->business_name);
                $result = $evo->sendText($instance->instance_name, $phone, $msg);

                if (isset($result['error'])) {
                    Log::warning('appointments:send-reminders falha envio', [
                        'appointment_id' => $apt->id,
                        'details' => $result['details'] ?? null,
                    ]);
                    continue;
                }

                $apt->update(['reminder_sent_at' => now()]);

                if ($apt->chat_id) {
                    $outboundId = $result['key']['id'] ?? ('reminder_' . Str::random(16));
                    WhatsappMessage::create([
                        'cliente_id' => $apt->cliente_id,
                        'chat_id' => $apt->chat_id,
                        'wa_message_id' => $outboundId,
                        'direction' => 'outbound',
                        'type' => 'chat',
                        'content' => $msg,
                        'status' => 'sent',
                    ]);
                }

                $sent++;
            } catch (Throwable $e) {
                Log::warning('appointments:send-reminders excecao', [
                    'appointment_id' => $apt->id,
                    'msg' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Lembretes enviados: {$sent}");
        return self::SUCCESS;
    }

    protected function buildReminderMessage(Appointment $apt, ?string $businessName): string
    {
        $hora = $apt->scheduled_at->format('H:i');
        $data = $apt->scheduled_at->format('d/m');
        $nome = $apt->contato_nome ?: ($apt->clienteFinal?->nome_responsavel ?: 'tudo bem');
        $titulo = $apt->titulo;
        $prefix = $businessName ? "Oi {$nome}! Aqui e do {$businessName}." : "Oi {$nome}!";

        return "{$prefix} Passando pra lembrar: {$titulo} em {$data} as {$hora}. Qualquer coisa so chamar aqui. Ate la!";
    }
}
