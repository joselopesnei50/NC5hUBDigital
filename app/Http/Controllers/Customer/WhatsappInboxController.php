<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\WhatsappChat;
use App\Models\WhatsappInstance;
use App\Models\WhatsappMessage;
use App\Services\EvolutionApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WhatsappInboxController extends Controller
{
    public function __construct(protected EvolutionApiService $evo) {}

    public function index(Request $request)
    {
        $cliente = Auth::user()->cliente;
        $instance = WhatsappInstance::where('cliente_id', $cliente->id)->first();

        $chats = WhatsappChat::where('cliente_id', $cliente->id)
            ->with('latestMessage', 'clienteFinal')
            ->orderByDesc('last_message_at')
            ->paginate(30);

        return view('customer.whatsapp.inbox', compact('instance', 'chats'));
    }

    public function show(Request $request, WhatsappChat $chat)
    {
        $cliente = Auth::user()->cliente;
        abort_unless((int) $chat->cliente_id === (int) $cliente->id, 404);

        $instance = WhatsappInstance::where('cliente_id', $cliente->id)->first();

        $messages = $chat->messages()->orderBy('created_at')->paginate(100);

        $chat->update(['unread_count' => 0]);

        $chats = WhatsappChat::where('cliente_id', $cliente->id)
            ->with('latestMessage', 'clienteFinal')
            ->orderByDesc('last_message_at')
            ->paginate(30);

        return view('customer.whatsapp.chat', compact('instance', 'chat', 'messages', 'chats'));
    }

    public function send(Request $request, WhatsappChat $chat)
    {
        $cliente = Auth::user()->cliente;
        abort_unless((int) $chat->cliente_id === (int) $cliente->id, 404);

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:4000'],
        ]);

        $instance = WhatsappInstance::where('cliente_id', $cliente->id)->firstOrFail();

        if (!$instance->isConnected()) {
            return back()->with('error', 'WhatsApp não está conectado.');
        }

        if (!$this->evo->isConfigured()) {
            return back()->with('error', 'Evolution API não configurada.');
        }

        $result = $this->evo->sendText($instance->instance_name, $chat->contact_phone ?: $chat->wa_id, $validated['content']);

        if (isset($result['error'])) {
            WhatsappMessage::create([
                'cliente_id' => $cliente->id,
                'chat_id' => $chat->id,
                'direction' => 'outbound',
                'type' => 'chat',
                'content' => $validated['content'],
                'status' => 'failed',
                'sent_by_user_id' => Auth::id(),
            ]);
            return back()->with('error', 'Falha ao enviar: ' . ($result['error'] ?? 'erro desconhecido'));
        }

        $waId = $result['key']['id'] ?? ($result['messageId'] ?? null);

        WhatsappMessage::create([
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

        return back();
    }

    public function feedInbox(Request $request)
    {
        $cliente = Auth::user()->cliente;

        $chats = WhatsappChat::where('cliente_id', $cliente->id)
            ->with(['latestMessage', 'clienteFinal'])
            ->orderByDesc('last_message_at')
            ->limit(100)
            ->get();

        $data = $chats->map(fn ($c) => [
            'id' => $c->id,
            'name' => $c->clienteFinal?->nome_responsavel ?: ($c->contact_name ?: $c->contact_phone ?: 'Sem nome'),
            'phone' => $c->contact_phone ?: $c->wa_id,
            'last_at_iso' => optional($c->last_message_at)->toIso8601String(),
            'unread' => (int) $c->unread_count,
            'last_text' => $c->latestMessage?->content ? mb_substr((string) $c->latestMessage->content, 0, 120) : null,
            'last_direction' => $c->latestMessage?->direction,
            'last_type' => $c->latestMessage?->type,
            'bot_active' => (bool) $c->is_bot_active,
            'is_group' => (bool) $c->is_group,
            'url' => route('customer.whatsapp.chat', $c),
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
        $cliente = Auth::user()->cliente;
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
