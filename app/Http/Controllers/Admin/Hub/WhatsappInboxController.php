<?php

namespace App\Http\Controllers\Admin\Hub;

use App\Http\Controllers\Controller;
use App\Models\WhatsappChat;
use App\Models\WhatsappInstance;
use App\Models\WhatsappMessage;
use App\Services\EvolutionApiService;
use App\Services\SystemCliente;
use App\Services\WhatsappSenderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WhatsappInboxController extends Controller
{
    public function __construct(
        protected EvolutionApiService $evo,
        protected WhatsappSenderService $sender,
    ) {}

    protected function buildChatsData(int $clienteId): array
    {
        return WhatsappChat::where('cliente_id', $clienteId)
            ->with(['latestMessage', 'clienteFinal'])
            ->orderByDesc('last_message_at')
            ->limit(100)
            ->get()
            ->map(function (WhatsappChat $c) {
                return [
                    'id' => $c->id,
                    'name' => $c->clienteFinal?->nome_responsavel ?: ($c->contact_name ?: ($c->contact_phone ?: 'Sem nome')),
                    'phone' => $c->contact_phone ?: $c->wa_id,
                    'last_at_iso' => $c->last_message_at?->toIso8601String(),
                    'unread' => (int) $c->unread_count,
                    'last_text' => $c->latestMessage && $c->latestMessage->content
                        ? mb_substr((string) $c->latestMessage->content, 0, 120) : null,
                    'last_direction' => $c->latestMessage?->direction,
                    'last_type' => $c->latestMessage?->type,
                    'bot_active' => (bool) $c->is_bot_active,
                    'is_group' => (bool) $c->is_group,
                    'url' => route('admin.hub.whatsapp.chat', $c),
                ];
            })->values()->all();
    }

    public function index(Request $request)
    {
        $cliente = SystemCliente::get();
        $instance = WhatsappInstance::where('cliente_id', $cliente->id)->first();
        $chatsData = $this->buildChatsData($cliente->id);

        return view('admin.hub.whatsapp.inbox', compact('instance', 'chatsData'));
    }

    public function show(Request $request, WhatsappChat $chat)
    {
        $cliente = SystemCliente::get();
        abort_unless((int) $chat->cliente_id === (int) $cliente->id, 404);

        $instance = WhatsappInstance::where('cliente_id', $cliente->id)->first();
        $messages = $chat->messages()->orderBy('created_at')->paginate(100);
        $chat->update(['unread_count' => 0]);

        $chats = WhatsappChat::where('cliente_id', $cliente->id)
            ->with(['latestMessage', 'clienteFinal'])
            ->orderByDesc('last_message_at')
            ->paginate(30);

        $chatsData = $this->buildChatsData($cliente->id);

        return view('admin.hub.whatsapp.chat', compact('instance', 'chat', 'messages', 'chats', 'chatsData'));
    }

    public function send(Request $request, WhatsappChat $chat)
    {
        $cliente = SystemCliente::get();
        abort_unless((int) $chat->cliente_id === (int) $cliente->id, 404);

        $validated = $request->validate(['content' => ['required', 'string', 'max:4000']]);

        $instance = WhatsappInstance::where('cliente_id', $cliente->id)->firstOrFail();

        // Se for Evolution e localmente não consta como 'open', faz uma checagem rápida em tempo real na Evolution
        if ($instance->isEvolution() && !$instance->isConnected() && $this->evo->isConfigured()) {
            $state = $this->evo->getConnectionState($instance->instance_name);
            $freshState = $state['instance']['state'] ?? ($state['state'] ?? null);
            if ($freshState) {
                $instance->update([
                    'status' => $freshState,
                    'connected_at' => $freshState === 'open' && !$instance->connected_at ? now() : $instance->connected_at,
                    'last_status_check_at' => now(),
                ]);
            }
        }

        if (!$instance->isConnected()) {
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'error' => 'WhatsApp não está conectado. Status atual: ' . ($instance->status ?? 'desconhecido')], 422);
            }
            return back()->with('error', 'WhatsApp não está conectado. Status atual: ' . ($instance->status ?? 'desconhecido'));
        }
        if (!$this->sender->isInstanceReady($instance)) {
            $errMsg = $instance->isCloudApi() ? 'Credenciais da Meta Cloud API incompletas.' : 'Evolution API não configurada.';
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'error' => $errMsg], 422);
            }
            return back()->with('error', $errMsg);
        }

        $result = $this->sender->sendText($instance, $chat->contact_phone ?: $chat->wa_id, $validated['content']);

        if (!$result['ok']) {
            $failedMsg = WhatsappMessage::create([
                'cliente_id' => $cliente->id,
                'chat_id' => $chat->id,
                'direction' => 'outbound',
                'type' => 'chat',
                'content' => $validated['content'],
                'status' => 'failed',
                'sent_by_user_id' => Auth::id(),
            ]);
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'error' => 'Falha ao enviar: ' . ($result['error'] ?? 'erro desconhecido'),
                    'message' => [
                        'id' => $failedMsg->id,
                        'direction' => $failedMsg->direction,
                        'type' => $failedMsg->type,
                        'content' => $failedMsg->content,
                        'status' => $failedMsg->status,
                        'time' => $failedMsg->created_at->format('H:i'),
                    ],
                ], 422);
            }
            return back()->with('error', 'Falha ao enviar: ' . ($result['error'] ?? 'erro desconhecido'));
        }

        $waId = $result['message_id'];

        $msg = WhatsappMessage::create([
            'cliente_id' => $cliente->id,
            'chat_id' => $chat->id,
            'wa_message_id' => $waId,
            'direction' => 'outbound',
            'type' => 'chat',
            'content' => $validated['content'],
            'status' => 'sent',
            'sent_by_user_id' => Auth::id(),
        ]);

        $chat->update(['last_message_at' => now()]);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => [
                    'id' => $msg->id,
                    'direction' => $msg->direction,
                    'type' => $msg->type,
                    'content' => $msg->content,
                    'caption' => $msg->media_caption,
                    'status' => $msg->status,
                    'time' => $msg->created_at->format('H:i'),
                    'created_at' => $msg->created_at->toIso8601String(),
                ],
            ]);
        }

        return back();
    }

    public function feedInbox(Request $request)
    {
        $cliente = SystemCliente::get();

        $chats = WhatsappChat::where('cliente_id', $cliente->id)
            ->with(['latestMessage', 'clienteFinal'])
            ->orderByDesc('last_message_at')
            ->limit(100)
            ->get();

        $data = $chats->map(fn ($c) => [
            'id' => $c->id,
            'name' => $c->clienteFinal?->nome_responsavel ?: ($c->contact_name ?: ($c->contact_phone ?: 'Sem nome')),
            'phone' => $c->contact_phone ?: $c->wa_id,
            'last_at_iso' => $c->last_message_at?->toIso8601String(),
            'unread' => (int) $c->unread_count,
            'last_text' => $c->latestMessage && $c->latestMessage->content ? mb_substr((string) $c->latestMessage->content, 0, 120) : null,
            'last_direction' => $c->latestMessage?->direction,
            'last_type' => $c->latestMessage?->type,
            'bot_active' => (bool) $c->is_bot_active,
            'is_group' => (bool) $c->is_group,
            'url' => route('admin.hub.whatsapp.chat', $c),
        ])->values();

        return response()->json([
            'chats' => $data,
            'server_time' => now()->toIso8601String(),
            'total' => $data->count(),
            'unread_total' => $data->sum('unread'),
        ]);
    }

    public function feedChat(Request $request, WhatsappChat $chat)
    {
        $cliente = SystemCliente::get();
        abort_unless((int) $chat->cliente_id === (int) $cliente->id, 404);

        $afterId = (int) $request->query('after_id', 0);
        $messages = $chat->messages()
            ->when($afterId > 0, fn ($q) => $q->where('id', '>', $afterId))
            ->orderBy('id')
            ->limit(100)
            ->get(['id', 'direction', 'type', 'content', 'media_mimetype', 'media_caption', 'status', 'created_at']);

        if ($messages->isNotEmpty()) {
            $chat->update(['unread_count' => 0]);
        }

        return response()->json([
            'messages' => $messages->map(fn ($m) => [
                'id' => $m->id,
                'direction' => $m->direction,
                'type' => $m->type,
                'content' => $m->content,
                'caption' => $m->media_caption,
                'status' => $m->status,
                'time' => $m->created_at->format('H:i'),
                'created_at' => $m->created_at->toIso8601String(),
            ])->values(),
            'latest_id' => (int) ($messages->last()?->id ?? $afterId),
            'bot_active' => (bool) $chat->is_bot_active,
        ]);
    }
}
