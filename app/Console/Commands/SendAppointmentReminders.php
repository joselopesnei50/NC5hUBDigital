<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\WhatsappBotTraining;
use App\Models\WhatsappInstance;
use App\Models\WhatsappMessage;
use App\Services\WhatsappSenderService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';
    protected $description = 'Envia lembretes WhatsApp (ate 2 janelas por agendamento) conforme config do cliente';

    public function handle(WhatsappSenderService $sender): int
    {
        $windowStart = now()->subMinutes(5);
        $windowEnd = now()->addMinutes(20);

        $trainings = WhatsappBotTraining::query()
            ->select('cliente_id', 'reminder_hours_before', 'enable_second_reminder', 'reminder_hours_before_second', 'business_name')
            ->get()
            ->keyBy('cliente_id');

        $maxLookahead = 48;
        foreach ($trainings as $t) {
            $maxLookahead = max($maxLookahead, (int) ($t->reminder_hours_before ?? 2), (int) ($t->reminder_hours_before_second ?? 24));
        }
        $horizonEnd = now()->addHours($maxLookahead + 1);

        $candidates = Appointment::query()
            ->whereIn('status', ['agendado', 'confirmado'])
            ->whereBetween('scheduled_at', [now(), $horizonEnd])
            ->get();

        $sent = 0;

        foreach ($candidates as $apt) {
            try {
                $training = $trainings->get($apt->cliente_id);
                $sent += $this->maybeSend($sender, $apt, $training, $windowStart, $windowEnd, 'primary');

                if ($training && $training->enable_second_reminder) {
                    $sent += $this->maybeSend($sender, $apt, $training, $windowStart, $windowEnd, 'second');
                }
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

    protected function maybeSend(WhatsappSenderService $sender, Appointment $apt, ?WhatsappBotTraining $training, Carbon $windowStart, Carbon $windowEnd, string $slot): int
    {
        if ($slot === 'primary') {
            if ($apt->reminder_sent_at) {
                return 0;
            }
            $hoursBefore = (int) ($training?->reminder_hours_before ?? 2);
            $field = 'reminder_sent_at';
        } else {
            if ($apt->reminder_second_sent_at) {
                return 0;
            }
            $hoursBefore = (int) ($training?->reminder_hours_before_second ?? 24);
            $field = 'reminder_second_sent_at';
        }

        $sendAt = $apt->scheduled_at->copy()->subHours($hoursBefore);
        if ($sendAt->lt($windowStart) || $sendAt->gt($windowEnd)) {
            return 0;
        }

        $phone = $apt->contato_telefone ?: ($apt->chat?->contact_phone ?: $apt->chat?->wa_id);
        if (!$phone) {
            return 0;
        }

        $instance = WhatsappInstance::where('cliente_id', $apt->cliente_id)
            ->where('status', 'open')
            ->first();
        if (!$instance || !$sender->isInstanceReady($instance)) {
            return 0;
        }

        $msg = $this->buildReminderMessage($apt, $training?->business_name, $slot);
        $result = $sender->sendText($instance, $phone, $msg);

        if (!$result['ok']) {
            Log::warning('appointments:send-reminders falha envio', [
                'appointment_id' => $apt->id,
                'slot' => $slot,
                'error' => $result['error'] ?? null,
            ]);
            return 0;
        }

        $apt->update([$field => now()]);

        if ($apt->chat_id) {
            $outboundId = $result['message_id'] ?: ('reminder_' . Str::random(16));
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

        return 1;
    }

    protected function buildReminderMessage(Appointment $apt, ?string $businessName, string $slot): string
    {
        $hora = $apt->scheduled_at->format('H:i');
        $data = $apt->scheduled_at->format('d/m');
        $nome = $apt->contato_nome ?: ($apt->clienteFinal?->nome_responsavel ?: 'tudo bem');
        $titulo = $apt->titulo;
        $prefix = $businessName ? "Oi {$nome}! Aqui e do {$businessName}." : "Oi {$nome}!";

        if ($slot === 'second') {
            return "{$prefix} So passando pra avisar: temos seu agendamento ({$titulo}) marcado pra {$data} as {$hora}. Qualquer coisa so chamar aqui.";
        }

        return "{$prefix} Passando pra lembrar: {$titulo} em {$data} as {$hora}. Qualquer coisa so chamar aqui. Ate la!";
    }
}
