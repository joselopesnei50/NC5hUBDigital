<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\AgentKnowledge;
use App\Models\ProdutoServico;
use App\Models\WhatsappBotProfile;
use App\Models\WhatsappBotTraining;
use App\Models\WhatsappChat;
use App\Services\WhatsappBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WhatsappBotController extends Controller
{
    public function index()
    {
        $cliente = Auth::user()->cliente;

        $profile = WhatsappBotProfile::firstOrNew(['cliente_id' => $cliente->id]);
        $training = WhatsappBotTraining::firstOrNew(['cliente_id' => $cliente->id]);

        // Preenche defaults a partir do DNA da empresa caso ainda não estejam salvos no bot
        if (empty($training->business_name)) {
            $training->business_name = $cliente->nome_fantasia ?: $cliente->razao_social;
        }
        if (empty($training->tom_de_voz) && !empty($cliente->tom_voz)) {
            $training->tom_de_voz = $cliente->tom_voz;
        }
        if (empty($training->diferenciais) && !empty($cliente->diferenciais_competitivos)) {
            $training->diferenciais = $cliente->diferenciais_competitivos;
        }
        if (empty($training->publico_alvo) && !empty($cliente->publico_alvo)) {
            $training->publico_alvo = $cliente->publico_alvo;
        }

        $segments = config('whatsapp-bot-segments');
        if (empty($cliente->is_system) && $profile->segment !== 'nc5_hub_closer') {
            unset($segments['nc5_hub_closer']);
        }
        $catalogCount = ProdutoServico::where('cliente_id', $cliente->id)->count();
        $knowledgeCount = AgentKnowledge::where('cliente_id', $cliente->id)->where('ativo', true)->count();
        $readiness = $training->calculateReadiness($profile, $catalogCount, $knowledgeCount);
        $quickTemplates = WhatsappBotTraining::getSegmentQuickTemplates();

        return view('customer.whatsapp.bot', compact(
            'profile',
            'training',
            'segments',
            'catalogCount',
            'knowledgeCount',
            'readiness',
            'quickTemplates'
        ));
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
            'manager_notification_phone' => ['nullable', 'string', 'max:32'],
            'notify_manager_on_booking' => ['nullable', 'boolean'],
            'notify_manager_on_confirm' => ['nullable', 'boolean'],
            'notify_manager_on_cancel' => ['nullable', 'boolean'],
        ]);

        $horario = [];
        foreach (WhatsappBotTraining::DIAS_SEMANA as $dia) {
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
                'manager_notification_phone' => preg_replace('/\D+/', '', (string) ($validated['manager_notification_phone'] ?? '')) ?: null,
                'notify_manager_on_booking' => (bool) ($validated['notify_manager_on_booking'] ?? false),
                'notify_manager_on_confirm' => (bool) ($validated['notify_manager_on_confirm'] ?? false),
                'notify_manager_on_cancel' => (bool) ($validated['notify_manager_on_cancel'] ?? false),
            ]
        );

        // Sincroniza também com o DNA do Cliente para que o BruceIA e o Motor de Crescimento compartilhem os mesmos dados
        $clienteUpdates = [];
        if (!empty($validated['tom_de_voz'])) {
            $clienteUpdates['tom_voz'] = mb_substr($validated['tom_de_voz'], 0, 40);
        }
        if (!empty($validated['diferenciais'])) {
            $clienteUpdates['diferenciais_competitivos'] = mb_substr($validated['diferenciais'], 0, 2000);
        }
        if (!empty($validated['publico_alvo'])) {
            $clienteUpdates['publico_alvo'] = mb_substr($validated['publico_alvo'], 0, 2000);
        }
        if (!empty($clienteUpdates)) {
            try {
                $cliente->update($clienteUpdates);
            } catch (\Throwable) {
                // Ignora caso alguma coluna opcional não exista no ambiente
            }
        }

        return redirect()->route('customer.whatsapp.bot.index')->with('success', 'Treinamento do Agente salvo e sincronizado com o BruceOS!');
    }

    /**
     * Importa e sincroniza automaticamente o Catálogo (Produtos/Serviços) e o DNA do BruceOS para os campos do Bot.
     */
    public function syncFromBruceOs()
    {
        $cliente = Auth::user()->cliente;
        $training = WhatsappBotTraining::firstOrNew(['cliente_id' => $cliente->id]);

        if (empty($training->business_name)) {
            $training->business_name = $cliente->nome_fantasia ?: $cliente->razao_social;
        }
        if (empty($training->tom_de_voz) && !empty($cliente->tom_voz)) {
            $training->tom_de_voz = $cliente->tom_voz;
        }
        if (empty($training->diferenciais) && !empty($cliente->diferenciais_competitivos)) {
            $training->diferenciais = $cliente->diferenciais_competitivos;
        }
        if (empty($training->publico_alvo) && !empty($cliente->publico_alvo)) {
            $training->publico_alvo = $cliente->publico_alvo;
        }

        // Compila todos os itens do Catálogo de Produtos e Serviços
        $itens = ProdutoServico::where('cliente_id', $cliente->id)
            ->orderBy('tipo')
            ->orderBy('nome')
            ->get();

        if ($itens->isNotEmpty()) {
            $linhas = [];
            foreach ($itens as $item) {
                $preco = 'R$ ' . number_format((float) $item->preco_padrao, 2, ',', '.');
                $un = $item->unidade_medida ? "/{$item->unidade_medida}" : '';
                $tipo = $item->tipo ? "[{$item->tipo}] " : '';
                $desc = $item->descricao ? " — {$item->descricao}" : '';
                $linhas[] = "• {$tipo}{$item->nome}: {$preco}{$un}{$desc}";
            }
            $training->catalogo_texto = implode("\n", $linhas);
        }

        $training->save();

        return redirect()->route('customer.whatsapp.bot.index')
            ->with('success', 'Catálogo e DNA do BruceOS importados com sucesso para o treinamento do Agente!');
    }

    /**
     * Simulador Ao Vivo (Playground) para testar respostas do Agente antes de ativar em produção.
     */
    public function simulate(Request $request, WhatsappBotService $botService)
    {
        $cliente = Auth::user()->cliente;

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
            'history' => ['nullable', 'array', 'max:10'],
            'segment' => ['nullable', 'string'],
        ]);

        try {
            $result = $botService->simulateReply(
                (int) $cliente->id,
                $validated['message'],
                $validated['history'] ?? [],
                $validated['segment'] ?? null
            );

            return response()->json($result);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'reply' => 'Erro ao simular resposta: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function toggleChatBot(WhatsappChat $chat)
    {
        $cliente = Auth::user()->cliente;
        abort_unless((int) $chat->cliente_id === (int) $cliente->id, 404);

        $chat->update(['is_bot_active' => !$chat->is_bot_active]);

        return back()->with('success', $chat->is_bot_active ? 'Bot ativado nesta conversa.' : 'Bot pausado nesta conversa.');
    }
}
