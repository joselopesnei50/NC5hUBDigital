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
            'tom_de_voz' => ['nullable', 'string', 'max:500'],
            'historia_negocio' => ['nullable', 'string', 'max:4000'],
            'diferenciais' => ['nullable', 'string', 'max:4000'],
            'processo_producao' => ['nullable', 'string', 'max:8000'],
            'publico_alvo' => ['nullable', 'string', 'max:2000'],
            'catalogo_texto' => ['nullable', 'string', 'max:12000'],
            'politicas' => ['nullable', 'string', 'max:4000'],
            'faq_custom' => ['nullable', 'string', 'max:8000'],
            'regras_proibicoes' => ['nullable', 'string', 'max:4000'],
            'cases_exemplo' => ['nullable', 'string', 'max:8000'],
            'reminder_hours_before' => ['nullable', 'integer', 'min:1', 'max:48'],
            'enable_second_reminder' => ['nullable', 'boolean'],
            'reminder_hours_before_second' => ['nullable', 'integer', 'min:1', 'max:168'],
            'duracao_padrao_atendimento_min' => ['nullable', 'integer', 'min:5', 'max:1440'],
            'horario_comercial' => ['nullable', 'array'],
            'horario_comercial.*.aberto' => ['nullable', 'boolean'],
            'horario_comercial.*.abre' => ['nullable', 'date_format:H:i'],
            'horario_comercial.*.fecha' => ['nullable', 'date_format:H:i'],
            'resource_label' => ['nullable', 'string', 'max:40'],
        ]);

        $horario = [];
        foreach (\App\Models\WhatsappBotTraining::DIAS_SEMANA as $dia) {
            $entry = $validated['horario_comercial'][$dia] ?? [];
            $horario[$dia] = [
                'aberto' => (bool) ($entry['aberto'] ?? false),
                'abre' => $entry['abre'] ?? '09:00',
                'fecha' => $entry['fecha'] ?? '18:00',
            ];
        }

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
                'tom_de_voz' => $validated['tom_de_voz'] ?? null,
                'historia_negocio' => $validated['historia_negocio'] ?? null,
                'diferenciais' => $validated['diferenciais'] ?? null,
                'processo_producao' => $validated['processo_producao'] ?? null,
                'publico_alvo' => $validated['publico_alvo'] ?? null,
                'catalogo_texto' => $validated['catalogo_texto'] ?? null,
                'politicas' => $validated['politicas'] ?? null,
                'faq_custom' => $validated['faq_custom'] ?? null,
                'regras_proibicoes' => $validated['regras_proibicoes'] ?? null,
                'cases_exemplo' => $validated['cases_exemplo'] ?? null,
                'reminder_hours_before' => $validated['reminder_hours_before'] ?? 2,
                'enable_second_reminder' => (bool) ($validated['enable_second_reminder'] ?? false),
                'reminder_hours_before_second' => $validated['reminder_hours_before_second'] ?? 24,
                'duracao_padrao_atendimento_min' => $validated['duracao_padrao_atendimento_min'] ?? 60,
                'horario_comercial' => $horario,
                'resource_label' => trim($validated['resource_label'] ?? '') ?: 'Profissional',
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
