<?php

namespace App\Jobs;

use App\Models\ClienteFinal;
use App\Models\WhatsappChat;
use App\Models\WhatsappInstance;
use App\Models\WhatsappMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessEvolutionWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public int $instanceId,
        public string $event,
        public array $payload,
    ) {}

    public function handle(): void
    {
        $instance = WhatsappInstance::find($this->instanceId);
        if (!$instance) {
            return;
        }

        match ($this->event) {
            'messages.upsert', 'MESSAGES_UPSERT' => $this->handleInboundMessage($instance),
            'connection.update', 'CONNECTION_UPDATE' => $this->handleConnectionUpdate($instance),
            default => null,
        };
    }

    protected function handleConnectionUpdate(WhatsappInstance $instance): void
    {
        $data = $this->payload['data'] ?? $this->payload;
        $state = $data['state'] ?? ($data['instance']['state'] ?? null);
        if (!$state) return;

        $instance->update([
            'status' => in_array($state, ['open', 'connecting', 'close']) ? $state : $instance->status,
            'phone_number' => $data['phone'] ?? ($data['wuid'] ?? $instance->phone_number),
            'connected_at' => $state === 'open' && !$instance->connected_at ? now() : $instance->connected_at,
            'last_status_check_at' => now(),
        ]);
    }

    protected function handleInboundMessage(WhatsappInstance $instance): void
    {
        $data = $this->payload['data'] ?? $this->payload;
        $key = $data['key'] ?? [];
        $messageId = $key['id'] ?? null;
        $fromMe = (bool) ($key['fromMe'] ?? false);

        if ($fromMe || !$messageId) {
            return;
        }

        if (WhatsappMessage::where('cliente_id', $instance->cliente_id)
            ->where('wa_message_id', $messageId)->exists()) {
            return;
        }

        $remoteJid = $key['remoteJid'] ?? '';
        $phone = preg_replace('/@.*/', '', $remoteJid);
        $isGroup = str_contains($remoteJid, '@g.us');

        if (!$phone) return;

        $extracted = $this->extractMessage($data);
        $pushName = $data['pushName'] ?? null;

        $chat = $this->findOrCreateChat($instance, $phone, $remoteJid, $pushName, $isGroup);

        WhatsappMessage::create([
            'cliente_id' => $instance->cliente_id,
            'chat_id' => $chat->id,
            'wa_message_id' => $messageId,
            'direction' => 'inbound',
            'type' => $extracted['type'],
            'content' => $extracted['content'],
            'media_mimetype' => $extracted['mimetype'],
            'media_caption' => $extracted['caption'],
            'status' => 'delivered',
        ]);

        $chat->increment('unread_count');
        $chat->update(['last_message_at' => now()]);
    }

    protected function findOrCreateChat(WhatsappInstance $instance, string $phone, string $jid, ?string $pushName, bool $isGroup): WhatsappChat
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

        $clienteFinal = $isGroup ? null : $this->matchClienteFinal($instance->cliente_id, $phone);

        return WhatsappChat::create([
            'cliente_id' => $instance->cliente_id,
            'cliente_final_id' => $clienteFinal?->id,
            'wa_id' => $phone,
            'contact_name' => $pushName,
            'contact_phone' => $phone,
            'is_group' => $isGroup,
            'last_message_at' => now(),
        ]);
    }

    protected function matchClienteFinal(int $clienteId, string $phone): ?ClienteFinal
    {
        $digits = preg_replace('/\D/', '', $phone);
        if (!$digits) return null;

        return ClienteFinal::where('cliente_id', $clienteId)
            ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(telefone, ' ', ''), '-', ''), '(', ''), ')', '') LIKE ?", ['%' . substr($digits, -9) . '%'])
            ->first();
    }

    protected function extractMessage(array $data): array
    {
        $msg = $data['message'] ?? [];
        $type = $data['messageType'] ?? 'chat';

        $content = null;
        $mimetype = null;
        $caption = null;

        if (isset($msg['conversation'])) {
            $content = $msg['conversation'];
            $type = 'chat';
        } elseif (isset($msg['extendedTextMessage']['text'])) {
            $content = $msg['extendedTextMessage']['text'];
            $type = 'chat';
        } elseif (isset($msg['imageMessage'])) {
            $type = 'image';
            $caption = $msg['imageMessage']['caption'] ?? null;
            $mimetype = $msg['imageMessage']['mimetype'] ?? 'image/jpeg';
            $content = $caption;
        } elseif (isset($msg['videoMessage'])) {
            $type = 'video';
            $caption = $msg['videoMessage']['caption'] ?? null;
            $mimetype = $msg['videoMessage']['mimetype'] ?? 'video/mp4';
            $content = $caption;
        } elseif (isset($msg['audioMessage'])) {
            $type = 'audio';
            $mimetype = $msg['audioMessage']['mimetype'] ?? 'audio/ogg';
        } elseif (isset($msg['documentMessage'])) {
            $type = 'document';
            $caption = $msg['documentMessage']['fileName'] ?? null;
            $mimetype = $msg['documentMessage']['mimetype'] ?? null;
            $content = $caption;
        } elseif (isset($msg['stickerMessage'])) {
            $type = 'sticker';
        }

        if (!in_array($type, ['chat', 'image', 'video', 'audio', 'document', 'sticker'])) {
            $type = 'other';
        }

        return [
            'type' => $type,
            'content' => $content,
            'mimetype' => $mimetype,
            'caption' => $caption,
        ];
    }
}
