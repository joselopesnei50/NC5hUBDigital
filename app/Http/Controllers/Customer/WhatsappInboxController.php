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
}
