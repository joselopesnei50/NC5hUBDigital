<?php

namespace App\Jobs;

use App\Models\ClienteFinal;
use App\Models\WhatsappChat;
use App\Models\WhatsappInstance;
use App\Models\WhatsappMessage;
use App\Services\MetaCloudApiService;
use App\Services\WhatsappBotService;
use App\Services\WhatsappSenderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Processa eventos de Webhook da Meta WhatsApp Business Cloud API (API Oficial).
 * Salva conversas e mensagens no mesmo schema do BruceOS e aciona a BruceIA automaticamente.
 */
class ProcessCloudApiWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(public array $payload) {}

    public function handle(): void
    {
        foreach ($this->payload['entry'] ?? [] as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                $field = $change['field'] ?? '';
                $value = $change['value'] ?? [];

                if ($field !== 'messages') {
                    continue;
                }

                $phoneNumberId = $value['metadata']['phone_number_id'] ?? null;
                if (!$phoneNumberId) {
                    continue;
                }

                $instance = WhatsappInstance::where('provider', WhatsappInstance::PROVIDER_CLOUD_API)
                    ->where('phone_number_id', $phoneNumberId)
                    ->first();

                if (!$instance) {
                    Log::warning('CloudApi Webhook Job: phone_number_id não encontrado no BruceOS', [
                        'phone_number_id' => $phoneNumberId,
                    ]);
                    continue;
                }

                foreach ($value['messages'] ?? [] as $msg) {
                    $this->persistInbound($instance, $msg, $value['contacts'] ?? []);
                }

                foreach ($value['statuses'] ?? [] as $status) {
                    $this->applyStatus($instance, $status);
                }
            }
        }
    }

    protected function persistInbound(WhatsappInstance $instance, array $msg, array $contacts): void
    {
        $messageId = $msg['id'] ?? null;
        $waId      = $msg['from'] ?? null;
        $rawType   = $msg['type'] ?? 'text';

        if (!$messageId || !$waId) {
            return;
        }

        // Idempotência por cliente + wa_message_id
        if (WhatsappMessage::where('cliente_id', $instance->cliente_id)
            ->where('wa_message_id', $messageId)
            ->exists()) {
            return;
        }

        $phone = preg_replace('/\D/', '', (string) $waId);
        if (!$phone) {
            return;
        }

        $senderName = null;
        foreach ($contacts as $contact) {
            if (($contact['wa_id'] ?? null) === $waId) {
                $senderName = $contact['profile']['name'] ?? null;
                break;
            }
        }

        [$type, $content, $caption, $mimetype] = $this->extractMessage($rawType, $msg);

        $chat = $this->findOrCreateChat($instance, $phone, $senderName);

        WhatsappMessage::create([
            'cliente_id'     => $instance->cliente_id,
            'chat_id'        => $chat->id,
            'wa_message_id'  => $messageId,
            'direction'      => 'inbound',
            'type'           => $type,
            'content'        => $content,
            'media_mimetype' => $mimetype,
            'media_caption'  => $caption,
            'status'         => 'delivered',
        ]);

        $chat->increment('unread_count');
        $chat->update(['last_message_at' => now()]);

        Cache::forget("wa_bot_ctx:{$chat->id}");

        // Envia confirmação de leitura (Read Receipt) na API Oficial
        try {
            MetaCloudApiService::forInstance($instance)->markMessageAsRead($messageId);
        } catch (Throwable) {
        }

        $this->maybeReplyWithBot($instance, $chat, $type, $content, $messageId);
    }

    protected function maybeReplyWithBot(
        WhatsappInstance $instance,
        WhatsappChat $chat,
        string $type,
        ?string $content,
        ?string $inboundMessageId = null
    ): void {
        if ($type !== 'chat' || empty(trim((string) $content))) {
            return;
        }
        if ($chat->is_group) {
            return;
        }

        try {
            /** @var WhatsappBotService $bot */
            $bot = app(WhatsappBotService::class);
            $reply = $bot->generateReply($chat->fresh(), (string) $content, $inboundMessageId);
            if (!$reply) {
                return;
            }

            /** @var WhatsappSenderService $sender */
            $sender = app(WhatsappSenderService::class);
            $result = $sender->sendText($instance, $chat->contact_phone ?: $chat->wa_id, $reply);

            if (!$result['ok']) {
                $bot->registerOutbound(null, true);
                Log::warning('WhatsappBot (Cloud API): envio falhou', [
                    'chat_id' => $chat->id,
                    'error'   => $result['error'] ?? null,
                ]);
                return;
            }

            $outboundId = $result['message_id'] ?: ('bot_cloud_' . Str::random(16));

            WhatsappMessage::create([
                'cliente_id'    => $instance->cliente_id,
                'chat_id'       => $chat->id,
                'wa_message_id' => $outboundId,
                'direction'     => 'outbound',
                'type'          => 'chat',
                'content'       => $reply,
                'status'        => 'sent',
            ]);

            $bot->registerOutbound($outboundId);
            $chat->update(['last_message_at' => now()]);
        } catch (Throwable $e) {
            Log::error('WhatsappBot (Cloud API): exceção no auto-reply', [
                'chat_id' => $chat->id,
                'msg'     => $e->getMessage(),
            ]);
        }
    }

    protected function findOrCreateChat(WhatsappInstance $instance, string $phone, ?string $pushName): WhatsappChat
    {
        $chat = WhatsappChat::where('cliente_id', $instance->cliente_id)
            ->where('wa_id', $phone)
            ->first();

        if ($chat) {
            if ($pushName && !$chat->contact_name) {
                $chat->update(['contact_name' => $pushName]);
            }
            return $chat;
        }

        $clienteFinal = $this->matchClienteFinal($instance->cliente_id, $phone);

        return WhatsappChat::create([
            'cliente_id'       => $instance->cliente_id,
            'cliente_final_id' => $clienteFinal?->id,
            'wa_id'            => $phone,
            'contact_name'     => $pushName,
            'contact_phone'    => $phone,
            'is_group'         => false,
            'last_message_at'  => now(),
        ]);
    }

    protected function matchClienteFinal(int $clienteId, string $phone): ?ClienteFinal
    {
        $digits = preg_replace('/\D/', '', $phone);
        if (!$digits) {
            return null;
        }

        return ClienteFinal::where('cliente_id', $clienteId)
            ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(telefone, ' ', ''), '-', ''), '(', ''), ')', '') LIKE ?", ['%' . substr($digits, -9) . '%'])
            ->first();
    }

    /**
     * Normaliza os tipos da Cloud API para o vocabulário de WhatsappMessage do BruceOS ('chat', 'image', etc.).
     */
    protected function extractMessage(string $rawType, array $msg): array
    {
        return match ($rawType) {
            'text'        => ['chat', (string) ($msg['text']['body'] ?? ''), null, null],
            'button'      => ['chat', (string) ($msg['button']['text'] ?? ''), null, null],
            'interactive' => ['chat', (string) ($msg['interactive']['button_reply']['title'] ?? $msg['interactive']['list_reply']['title'] ?? ''), null, null],
            'image'       => ['image', (string) ($msg['image']['caption'] ?? ''), $msg['image']['caption'] ?? null, $msg['image']['mime_type'] ?? 'image/jpeg'],
            'video'       => ['video', (string) ($msg['video']['caption'] ?? ''), $msg['video']['caption'] ?? null, $msg['video']['mime_type'] ?? 'video/mp4'],
            'audio'       => ['audio', null, null, $msg['audio']['mime_type'] ?? 'audio/ogg'],
            'document'    => ['document', (string) ($msg['document']['caption'] ?? ($msg['document']['filename'] ?? '')), $msg['document']['caption'] ?? null, $msg['document']['mime_type'] ?? 'application/pdf'],
            'sticker'     => ['sticker', null, null, 'image/webp'],
            default       => ['other', null, null, null],
        };
    }

    protected function applyStatus(WhatsappInstance $instance, array $status): void
    {
        $messageId = $status['id'] ?? null;
        $newStatus = $status['status'] ?? null;

        if (!$messageId || !$newStatus) {
            return;
        }

        $ranking = ['sent' => 1, 'delivered' => 2, 'read' => 3, 'failed' => 99];

        $msg = WhatsappMessage::where('cliente_id', $instance->cliente_id)
            ->where('wa_message_id', $messageId)
            ->first();

        if (!$msg) {
            return;
        }

        $currentRank = $ranking[$msg->status] ?? 0;
        $newRank     = $ranking[$newStatus] ?? 0;

        if ($newRank > $currentRank && in_array($newStatus, ['sent', 'delivered', 'read', 'failed'], true)) {
            $msg->update(['status' => $newStatus]);
        }
    }
}
