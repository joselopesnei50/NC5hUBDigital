<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\WhatsappBotProfile;
use App\Models\WhatsappBotTraining;
use App\Models\WhatsappChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WhatsappBotController extends Controller
{
    public function index()
    {
        $cliente = Auth::user()->cliente;

        $profile = WhatsappBotProfile::firstOrNew(['cliente_id' => $cliente->id]);
        $training = WhatsappBotTraining::firstOrNew(['cliente_id' => $cliente->id]);
        $segments = config('whatsapp-bot-segments');

        return view('customer.whatsapp.bot', compact('profile', 'training', 'segments'));
    }

    public function update(Request $request)
    {
        $cliente = Auth::user()->cliente;

        $validated = $request->validate([
            'segment' => ['required', 'string', 'in:' . implode(',', array_keys(config('whatsapp-bot-segments')))],
            'is_active' => ['nullable', 'boolean'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'horario_atendimento' => ['nullable', 'string', 'max:255'],
            'endereco' => ['nullable', 'string', 'max:255'],
            'faq_custom' => ['nullable', 'string', 'max:8000'],
            'catalogo_texto' => ['nullable', 'string', 'max:12000'],
            'regras_proibicoes' => ['nullable', 'string', 'max:4000'],
        ]);

        WhatsappBotProfile::updateOrCreate(
            ['cliente_id' => $cliente->id],
            [
                'segment' => $validated['segment'],
                'is_active' => (bool) ($validated['is_active'] ?? false),
            ]
        );

        WhatsappBotTraining::updateOrCreate(
            ['cliente_id' => $cliente->id],
            [
                'business_name' => $validated['business_name'] ?? null,
                'horario_atendimento' => $validated['horario_atendimento'] ?? null,
                'endereco' => $validated['endereco'] ?? null,
                'faq_custom' => $validated['faq_custom'] ?? null,
                'catalogo_texto' => $validated['catalogo_texto'] ?? null,
                'regras_proibicoes' => $validated['regras_proibicoes'] ?? null,
            ]
        );

        return redirect()->route('customer.whatsapp.bot.index')->with('success', 'Bot atualizado com sucesso.');
    }

    public function toggleChatBot(WhatsappChat $chat)
    {
        $cliente = Auth::user()->cliente;
        abort_unless((int) $chat->cliente_id === (int) $cliente->id, 404);

        $chat->update(['is_bot_active' => !$chat->is_bot_active]);

        return back()->with('success', $chat->is_bot_active ? 'Bot ativado nesta conversa.' : 'Bot pausado nesta conversa.');
    }
}
