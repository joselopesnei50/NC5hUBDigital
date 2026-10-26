<?php

namespace App\Services;

use App\Agent\Chat\ToolRegistry;
use App\Agent\Chat\Tools\CancelarAgendamentoWhatsappTool;
use App\Agent\Chat\Tools\ConsultarAgendaWhatsappTool;
use App\Agent\Chat\Tools\ConsultarCatalogoWhatsappTool;
use App\Agent\Chat\Tools\CriarAgendamentoWhatsappTool;
use App\Agent\Chat\Tools\CriarOrcamentoWhatsappTool;
use App\Agent\Chat\Tools\ListarMeusAgendamentosWhatsappTool;
use App\Agent\Chat\Tools\ListarRecursosWhatsappTool;
use App\Models\AppointmentResource;
use App\Services\AppointmentNotificationService;
use App\Agent\DTOs\PromptPayload;
use App\Agent\LlmManager;
use App\Models\AgentKnowledge;
use App\Models\Cliente;
use App\Models\ClienteFinal;
use App\Models\ProdutoServico;
use App\Models\WhatsappBotInteraction;
use App\Models\WhatsappBotProfile;
use App\Models\WhatsappBotTraining;
use App\Models\WhatsappChat;
use App\Models\WhatsappMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class WhatsappBotService
{
    protected const HISTORY_LIMIT = 6;
    protected const ESCALATION_SENTINEL = 'ESCALAR_HUMANO';
    protected const CUSTOMER_CONTEXT_TTL = 300;
    protected const MAX_TOOL_ITERATIONS = 3;

    protected ToolRegistry $tools;

    public function __construct(protected LlmManager $llm)
    {
        $this->tools = new ToolRegistry();
        $this->tools->register(new CriarAgendamentoWhatsappTool());
        $this->tools->register(new ConsultarAgendaWhatsappTool());
        $this->tools->register(new ListarMeusAgendamentosWhatsappTool());
        $this->tools->register(new CancelarAgendamentoWhatsappTool());
        $this->tools->register(new \App\Agent\Chat\Tools\ConfirmarAgendamentoWhatsappTool());
        $this->tools->register(new ListarRecursosWhatsappTool());
        $this->tools->register(new ConsultarCatalogoWhatsappTool());
        $this->tools->register(new CriarOrcamentoWhatsappTool());
    }

    public function generateReply(WhatsappChat $chat, string $inboundText, ?string $inboundMessageId = null): ?string
    {
        // Atalho determinístico: resposta direta ao lembrete de confirmação (SIM / CANCELAR)
        if (!$chat->is_group) {
            $quick = $this->handleReminderQuickReply($chat, $inboundText);
            if ($quick !== null) {
                return $quick;
            }
        }

        if (!$this->shouldReply($chat)) {
            return null;
        }

        $profile = WhatsappBotProfile::where('cliente_id', $chat->cliente_id)->first();
        if (!$profile || !$profile->is_active) {
            return null;
        }

        $preset = $profile->segmentPreset();
        if (empty($preset)) {
            Log::warning('WhatsappBot: segmento invalido', ['segment' => $profile->segment]);
            return null;
        }

        if ($this->inboundRequestsHuman($inboundText)) {
            $chat->update(['is_bot_active' => false]);
            $this->recordInteraction($chat, $inboundMessageId, null, null, 0, true, false);
            Log::info('WhatsappBot: escalacao detectada no inbound', [
                'chat_id' => $chat->id,
                'cliente_id' => $chat->cliente_id,
            ]);
            return null;
        }

        $training = WhatsappBotTraining::where('cliente_id', $chat->cliente_id)->first();
        $customerContext = $this->buildCustomerContext($chat);

        $systemPrompt = $this->composeSystemPrompt($preset, $training, $customerContext, (int) $chat->cliente_id);
        $history = $this->buildHistory($chat);

        $startedAt = microtime(true);

        try {
            $response = $this->runLlmWithTools($chat, $systemPrompt, $inboundText, $history, $customerContext, $training);
        } catch (Throwable $e) {
            Log::error('WhatsappBot: LLM falhou', [
                'cliente_id' => $chat->cliente_id,
                'chat_id' => $chat->id,
                'msg' => $e->getMessage(),
            ]);
            $this->recordInteraction($chat, $inboundMessageId, null, null, (int) ((microtime(true) - $startedAt) * 1000), false, true);
            return null;
        }

        $elapsedMs = (int) ((microtime(true) - $startedAt) * 1000);
        $reply = trim($response->content);

        if (Str::contains($reply, self::ESCALATION_SENTINEL)) {
            $chat->update(['is_bot_active' => false]);
            $this->recordInteraction($chat, $inboundMessageId, null, $response, $elapsedMs, true, false);
            Log::info('WhatsappBot: escalacao detectada pelo LLM', [
                'chat_id' => $chat->id,
                'cliente_id' => $chat->cliente_id,
            ]);
            return null;
        }

        if ($reply === '') {
            $this->recordInteraction($chat, $inboundMessageId, null, $response, $elapsedMs, false, true);
            return null;
        }

        $this->pendingInteraction = [
            'chat' => $chat,
            'inbound_message_id' => $inboundMessageId,
            'response' => $response,
            'elapsed_ms' => $elapsedMs,
        ];

        return $reply;
    }

    protected ?array $pendingInteraction = null;
    protected array $pendingToolErrors = [];

    /**
     * Se o cliente respondeu "SIM"/"1" ou "CANCELAR"/"2" a um lembrete de confirmação enviado
     * nas últimas 48h, confirma ou cancela o agendamento imediatamente (sem depender da IA).
     * Retorna a mensagem de resposta, ou null se não se aplica.
     */
    protected function handleReminderQuickReply(WhatsappChat $chat, string $inboundText): ?string
    {
        $normalized = mb_strtolower(trim(Str::ascii($inboundText)));
        $normalized = trim(preg_replace('/[^a-z0-9 ]+/', ' ', $normalized));
        $normalized = preg_replace('/\s+/', ' ', $normalized);
        if ($normalized === '' || mb_strlen($normalized) > 40) {
            return null;
        }

        $confirmWords = ['sim', 's', '1', 'confirmo', 'confirmado', 'confirmar', 'sim confirmo', 'ok', 'okay', 'pode confirmar', 'estarei la', 'vou sim', 'sim vou', 'claro', 'com certeza', 'sim pode confirmar'];
        $cancelWords = ['2', 'cancelar', 'cancela', 'cancelo', 'nao', 'n', 'nao vou', 'nao vou poder', 'nao posso', 'desmarcar', 'desmarca', 'quero cancelar', 'pode cancelar'];

        $isConfirm = in_array($normalized, $confirmWords, true);
        $isCancel = in_array($normalized, $cancelWords, true);
        if (!$isConfirm && !$isCancel) {
            return null;
        }

        $apt = \App\Models\Appointment::where('cliente_id', $chat->cliente_id)
            ->whereIn('status', ['agendado', 'confirmado'])
            ->where('scheduled_at', '>=', now()->subHours(1))
            ->where(function ($q) {
                $q->where('reminder_sent_at', '>=', now()->subHours(48))
                    ->orWhere('reminder_second_sent_at', '>=', now()->subHours(48));
            })
            ->where(function ($q) use ($chat) {
                $q->where('chat_id', $chat->id);
                if ($chat->cliente_final_id) {
                    $q->orWhere('cliente_final_id', $chat->cliente_final_id);
                }
            })
            ->orderBy('scheduled_at')
            ->first();

        if (!$apt) {
            return null;
        }

        $data = $apt->scheduled_at->format('d/m');
        $hora = $apt->scheduled_at->format('H:i');
        $notifier = app(AppointmentNotificationService::class);

        try {
            if ($isConfirm) {
                if ($apt->status !== 'confirmado') {
                    $apt->update(['status' => 'confirmado', 'confirmed_at' => now()]);
                    $notifier->notifyConfirmed($apt);
                }
                return "Perfeito! ✅ Sua presença em {$data} às {$hora} ({$apt->titulo}) está confirmada. Até lá!";
            }

            $apt->update([
                'status' => 'cancelado',
                'cancelled_at' => now(),
                'notes' => trim(($apt->notes ?: '') . "\n[cancelado via resposta ao lembrete] " . $inboundText),
            ]);
            $notifier->notifyCancelled($apt, 'Cliente respondeu ao lembrete: "' . mb_substr($inboundText, 0, 60) . '"');

            return "Tudo bem, cancelei seu horário de {$data} às {$hora} e ele já foi liberado. Quer que eu veja outro dia e horário pra você remarcar?";
        } catch (Throwable $e) {
            Log::warning('WhatsappBot: falha no atalho de confirmacao', ['apt' => $apt->id, 'msg' => $e->getMessage()]);
            return null;
        }
    }

    public function registerOutbound(?string $outboundMessageId, bool $sendFailed = false): void
    {
        if (!$this->pendingInteraction) {
            return;
        }
        $p = $this->pendingInteraction;
        $this->recordInteraction(
            $p['chat'],
            $p['inbound_message_id'],
            $outboundMessageId,
            $p['response'],
            $p['elapsed_ms'],
            false,
            $sendFailed
        );
        $this->pendingInteraction = null;
    }

    protected function recordInteraction(WhatsappChat $chat, ?string $inboundId, ?string $outboundId, $response, int $elapsedMs, bool $escalated, bool $failed): void
    {
        try {
            WhatsappBotInteraction::create([
                'cliente_id' => $chat->cliente_id,
                'chat_id' => $chat->id,
                'inbound_message_id' => $inboundId,
                'outbound_message_id' => $outboundId,
                'tokens_in' => $response->tokensIn ?? 0,
                'tokens_out' => $response->tokensOut ?? 0,
                'cost' => $response->estimatedCost ?? 0,
                'response_time_ms' => $elapsedMs,
                'escalated' => $escalated,
                'failed' => $failed,
                'tool_errors' => !empty($this->pendingToolErrors) ? $this->pendingToolErrors : null,
            ]);
        } catch (Throwable $e) {
            Log::warning('WhatsappBot: falha ao registrar interacao', ['msg' => $e->getMessage()]);
        }
        $this->pendingToolErrors = [];
    }

    protected function shouldReply(WhatsappChat $chat): bool
    {
        if ($chat->is_group) {
            return false;
        }
        return (bool) $chat->is_bot_active;
    }

    protected function runLlmWithTools(WhatsappChat $chat, string $systemPrompt, string $inboundText, array $history, array $customerContext, ?WhatsappBotTraining $training): \App\Agent\DTOs\LlmResponse
    {
        $toolsSchema = $this->tools->getToolsForLlm();
        $driver = $this->llm->driver('deepseek');

        $workingHistory = $history;
        $workingUserPrompt = $inboundText;
        $workingUserAlreadyInjected = false;

        $totalTokensIn = 0;
        $totalTokensOut = 0;
        $totalCost = 0.0;
        $finalContent = '';
        $this->pendingToolErrors = [];

        for ($i = 0; $i < self::MAX_TOOL_ITERATIONS; $i++) {
            $payload = new PromptPayload(
                systemPrompt: $systemPrompt,
                userPrompt: $workingUserAlreadyInjected ? '' : $workingUserPrompt,
                snapshot: $customerContext,
                tools: $toolsSchema,
                history: $workingHistory,
                toolChoice: 'auto',
            );

            $response = $driver->complete($payload);

            $totalTokensIn += $response->tokensIn;
            $totalTokensOut += $response->tokensOut;
            $totalCost += $response->estimatedCost;
            $finalContent = $response->content;

            $toolCalls = $response->toolCalls;
            if (empty($toolCalls)) {
                break;
            }

            if (!$workingUserAlreadyInjected) {
                $workingHistory[] = ['role' => 'user', 'content' => $workingUserPrompt];
                $workingUserAlreadyInjected = true;
            }

            $assistantMsg = ['role' => 'assistant', 'content' => $response->content ?: null, 'tool_calls' => $toolCalls];
            $workingHistory[] = $assistantMsg;

            foreach ($toolCalls as $call) {
                $toolName = $call['function']['name'] ?? '';
                $rawArgs = $call['function']['arguments'] ?? '{}';
                $args = json_decode($rawArgs, true) ?: [];
                $args['_chat_id'] = $chat->id;
                $args['_default_duration'] = (int) ($training?->duracao_padrao_atendimento_min ?: 60);

                $tool = $this->tools->getTool($toolName);
                if (!$tool) {
                    $result = ['erro' => "tool desconhecida: {$toolName}"];
                } else {
                    try {
                        $result = $tool->execute($chat->cliente_id, $args);
                    } catch (Throwable $e) {
                        $result = ['erro' => 'excecao ao executar tool: ' . $e->getMessage()];
                        Log::warning('WhatsappBot: tool falhou', ['tool' => $toolName, 'msg' => $e->getMessage()]);
                    }
                }

                $errCode = $result['erro'] ?? null;
                if ($errCode || (isset($result['ok']) && $result['ok'] === false)) {
                    $this->pendingToolErrors[] = [
                        'tool' => $toolName,
                        'error' => (string) ($errCode ?: 'falha'),
                    ];
                }

                $workingHistory[] = [
                    'role' => 'tool',
                    'tool_call_id' => $call['id'] ?? '',
                    'content' => json_encode($result, JSON_UNESCAPED_UNICODE),
                ];
            }
        }

        return new \App\Agent\DTOs\LlmResponse(
            content: $finalContent,
            tokensIn: $totalTokensIn,
            tokensOut: $totalTokensOut,
            estimatedCost: $totalCost,
            toolCalls: null,
        );
    }

    public function buildCustomerContext(WhatsappChat $chat): array
    {
        return Cache::remember(
            "wa_bot_ctx:{$chat->id}",
            self::CUSTOMER_CONTEXT_TTL,
            fn () => $this->computeCustomerContext($chat)
        );
    }

    protected function computeCustomerContext(WhatsappChat $chat): array
    {
        $outboundCount = WhatsappMessage::where('chat_id', $chat->id)
            ->where('direction', 'outbound')
            ->count();
        $isFirstInteraction = $outboundCount === 0;

        $training = WhatsappBotTraining::where('cliente_id', $chat->cliente_id)->first();

        $context = [
            'hoje' => now()->format('Y-m-d'),
            'dia_da_semana_hoje' => now()->isoFormat('dddd'),
            'horario_comercial' => $this->summarizeBusinessHours($training),
            'duracao_padrao_minutos' => (int) ($training?->duracao_padrao_atendimento_min ?: 60),
            'resource_label' => $training?->resource_label ?: 'Profissional',
            'recursos' => $this->summarizeResources($chat->cliente_id),
            'is_first_interaction' => $isFirstInteraction,
            'is_returning_customer' => false,
            'customer_name' => $chat->contact_name,
            'is_identified_customer' => false,
            'total_pedidos_fechados' => 0,
            'last_order' => null,
            'days_since_last_order' => null,
            'aniversario_hoje' => false,
        ];

        if (!$chat->cliente_final_id) {
            return $context;
        }

        $cf = ClienteFinal::with(['pedidos' => function ($q) {
            $q->orderByDesc('data_pedido')->orderByDesc('id')->limit(1);
        }])->find($chat->cliente_final_id);

        if (!$cf) {
            return $context;
        }

        $context['is_identified_customer'] = true;
        $context['customer_name'] = $cf->nome_responsavel ?: ($cf->nome_empresa ?: $chat->contact_name);

        $fechados = \App\Models\PedidoCliente::where('cliente_final_id', $cf->id)
            ->where('status', 'Concluído')
            ->count();
        $context['total_pedidos_fechados'] = $fechados;
        $context['is_returning_customer'] = $fechados > 0 || !$isFirstInteraction;

        $lastOrder = $cf->pedidos->first();
        if ($lastOrder) {
            $context['last_order'] = [
                'titulo' => $lastOrder->titulo,
                'status' => $lastOrder->status,
                'valor' => (float) $lastOrder->valor,
                'data_pedido' => optional($lastOrder->data_pedido)->format('d/m/Y'),
            ];
            if ($lastOrder->data_pedido) {
                $context['days_since_last_order'] = Carbon::parse($lastOrder->data_pedido)->diffInDays(now());
            }
        }

        $hoje = now()->format('m-d');
        $aniv = $cf->data_aniversario_responsavel ?: $cf->data_aniversario_empresa;
        if ($aniv && Carbon::parse($aniv)->format('m-d') === $hoje) {
            $context['aniversario_hoje'] = true;
        }

        return $context;
    }

    protected function composeSystemPrompt(array $preset, ?WhatsappBotTraining $training, array $ctx, ?int $clienteId = null): string
    {
        $parts = [];

        $cliente = $clienteId ? Cliente::find($clienteId) : null;
        $tomVoz = $training?->tom_de_voz ?: ($cliente?->tom_voz ?? null);

        $parts[] = "PERSONA:\n" . ($preset['persona'] ?? '');
        if ($tomVoz) {
            $parts[] = "AJUSTE DE TOM (prioridade sobre a persona):\n" . $tomVoz;
        }

        $parts[] = "INSTRUCOES DO NICHO:\n" . ($preset['prompt_base'] ?? '');

        $parts[] = $this->renderTrainingBlock($training, $clienteId, $cliente);

        $parts[] = $this->renderApproachBlock($ctx);

        if (!empty($preset['exemplos_resposta'])) {
            $exemplos = [];
            foreach ($preset['exemplos_resposta'] as $ex) {
                $exemplos[] = "Cliente: {$ex['user']}\nBot: {$ex['bot']}";
            }
            $parts[] = "EXEMPLOS DE TOM E RESPOSTA DO NICHO:\n" . implode("\n\n", $exemplos);
        }

        if ($training && $training->cases_exemplo) {
            $parts[] = "EXEMPLOS REAIS DESTE NEGOCIO (seguir fielmente o estilo):\n" . $training->cases_exemplo;
        }

        $parts[] = "CATALOGO E ORCAMENTOS (FERRAMENTAS DISPONIVEIS):\n"
            . "- Tools: consultar_catalogo, criar_orcamento_pedido.\n"
            . "- Sempre que o cliente perguntar sobre produtos, servicos, cardapio ou valores, chame consultar_catalogo para informar os precos oficiais cadastrados no BruceOS.\n"
            . "- Se o cliente quiser receber um orcamento formal, proposta ou fechar um pedido com itens especificos, chame criar_orcamento_pedido passando os itens e quantidades, e envie para ele o link_proposta retornado pela ferramenta.\n\n"
            . "AGENDAMENTOS (FERRAMENTAS DISPONIVEIS):\n"
            . "- Tools: consultar_agenda, criar_agendamento, listar_meus_agendamentos, cancelar_agendamento, confirmar_agendamento, listar_recursos.\n"
            . "- NUNCA proponha ou agende horario fora do horario_comercial do snapshot. Se o cliente pedir fora, informe o horario de funcionamento e sugira o proximo horario valido.\n"
            . "- RECURSOS (profissionais/cadeiras/salas conforme resource_label): veja a lista em recursos do snapshot.\n"
            . "  * Se recursos tem 0 itens: ignore totalmente, agende sem perguntar.\n"
            . "  * Se recursos tem 1 item: nao pergunte, o sistema auto-preenche.\n"
            . "  * Se recursos tem 2+ itens: PERGUNTE ao cliente com qual (use o resource_label pra chamar — ex: \"com qual profissional voce quer?\" ou \"em qual cadeira voce prefere?\"). Passe o resource_id correspondente ao criar.\n"
            . "  * Se o cliente disser \"qualquer um\", \"tanto faz\", \"pode ser qualquer\", passe resource_id=0 — o sistema aloca o primeiro disponivel no horario.\n"
            . "  * Se a tool criar_agendamento retornar erro escolha_recurso_necessaria, voce esqueceu de perguntar; use a lista recursos retornada e pergunte agora.\n"
            . "- Fluxo pra MARCAR:\n"
            . "  1. Entenda o que ele quer agendar e para quando.\n"
            . "  2. Se recursos.length >= 2 e cliente nao disse: PERGUNTE qual recurso.\n"
            . "  3. Opcional: consultar_agenda pra ver ocupacao do dia.\n"
            . "  4. REPITA a proposta: \"Deixa confirmar: X dia Y as Z com {recurso}, pode ser?\". ESPERE o SIM explicito.\n"
            . "  5. Chame criar_agendamento com resource_id. Se a tool retornar erro conflito_de_horario ou horario_fora_do_expediente, OFERECA as sugestoes.\n"
            . "- Fluxo pra CANCELAR ou REMARCAR:\n"
            . "  1. Chame listar_meus_agendamentos pra pegar o id correto.\n"
            . "  2. Se tiver mais de um, pergunte ao cliente QUAL (data+titulo) ele quer alterar.\n"
            . "  3. CONFIRME: \"Vou cancelar o agendamento de X dia Y, confirma?\". ESPERE o SIM.\n"
            . "  4. Chame cancelar_agendamento com o appointment_id.\n"
            . "  5. Se for remarcacao, apos cancelar siga o fluxo pra MARCAR com o novo horario.\n"
            . "- Fluxo de RESPOSTA AO LEMBRETE DE CONFIRMACAO (o sistema envia automaticamente antes do horario pedindo SIM ou CANCELAR):\n"
            . "  * Se a ultima mensagem enviada por nos foi um lembrete pedindo confirmacao e o cliente responder algo como 'sim', 'confirmo', '1', 'ok', 'estarei la', 'pode confirmar': chame IMEDIATAMENTE confirmar_agendamento (sem pedir nova confirmacao) e agradeca de forma curta.\n"
            . "  * Se responder 'cancelar', '2', 'nao vou poder', 'desmarca': chame listar_meus_agendamentos, e entao cancelar_agendamento com o id do agendamento lembrado (a resposta ao lembrete ja vale como confirmacao do cancelamento). Depois informe que o horario foi liberado e OFERECA remarcar.\n"
            . "  * Se pedir pra remarcar: cancele o atual e siga o fluxo pra MARCAR.\n"
            . "- Interprete datas relativas (\"amanha\", \"sexta\", \"dia 15\") pra YYYY-MM-DD baseando-se em hoje do snapshot.";

        $parts[] = "REGRAS GERAIS:\n"
            . "- Responda em portugues brasileiro, humano e natural.\n"
            . "- Mensagens curtas (ate 3 frases por resposta sempre que possivel).\n"
            . "- Nao use markdown pesado (sem **negrito** ou listas longas). WhatsApp mostra texto puro.\n"
            . "- Nunca invente dados que nao foram passados nos blocos acima.\n"
            . "- Se o cliente pedir atendente humano, retorne apenas: " . self::ESCALATION_SENTINEL;

        return implode("\n\n", $parts);
    }

    protected function renderTrainingBlock(?WhatsappBotTraining $t, ?int $clienteId = null, ?Cliente $cliente = null): string
    {
        $lines = ['DADOS E FUNDAMENTOS DO NEGOCIO DESTE CLIENTE (MEMORIA UNIFICADA BRUCEOS):'];

        $nome = $t?->business_name ?: ($cliente?->nome_fantasia ?: $cliente?->razao_social);
        $publico = $t?->publico_alvo ?: ($cliente?->publico_alvo ?? null);
        $diferenciais = $t?->diferenciais ?: ($cliente?->diferenciais_competitivos ?? null);

        if ($nome) $lines[] = "- Nome: {$nome}";
        if ($t?->horario_atendimento) $lines[] = "- Horario de atendimento: {$t->horario_atendimento}";
        if ($t?->endereco) $lines[] = "- Endereco: {$t->endereco}";
        if ($publico) $lines[] = "- Publico alvo: {$publico}";

        if ($t?->historia_negocio) $lines[] = "\nHISTORIA E PROPOSITO:\n{$t->historia_negocio}";
        if ($diferenciais) $lines[] = "\nDIFERENCIAIS (use pra reforcar valor na venda):\n{$diferenciais}";
        if ($t?->processo_producao) $lines[] = "\nCOMO O PRODUTO/SERVICO E FEITO/ENTREGUE:\n{$t->processo_producao}";
        if ($t?->catalogo_texto) $lines[] = "\nCATALOGO / MENU / SERVICOS (TEXTO):\n{$t->catalogo_texto}";

        // Injeta resumo automático do Catálogo Estruturado do BruceOS (produtos_servicos)
        $targetClienteId = $clienteId ?: ($t?->cliente_id ?? null);
        if ($targetClienteId) {
            $itensCatalogo = ProdutoServico::where('cliente_id', $targetClienteId)
                ->orderBy('tipo')
                ->orderBy('nome')
                ->limit(35)
                ->get(['nome', 'tipo', 'preco_padrao', 'unidade_medida', 'descricao']);

            if ($itensCatalogo->isNotEmpty()) {
                $catLines = ["\nCATALOGO OFICIAL SINCRONIZADO DO BRUCEOS:"];
                foreach ($itensCatalogo as $item) {
                    $preco = 'R$ ' . number_format((float) $item->preco_padrao, 2, ',', '.');
                    $un = $item->unidade_medida ? "/{$item->unidade_medida}" : '';
                    $desc = $item->descricao ? ' — ' . Str::limit(trim($item->descricao), 90) : '';
                    $catLines[] = "• [{$item->tipo}] {$item->nome} ({$preco}{$un}){$desc}";
                }
                $lines[] = implode("\n", $catLines);
            }
        }

        if ($t?->politicas) $lines[] = "\nPOLITICAS (troca, cancelamento, garantia):\n{$t->politicas}";
        if ($t?->faq_custom) $lines[] = "\nFAQ ESPECIFICA:\n{$t->faq_custom}";
        if ($t?->regras_proibicoes) $lines[] = "\nREGRAS E PROIBICOES (prioridade sobre tudo):\n{$t->regras_proibicoes}";

        // Injeta documentos ativos da Base de Conhecimento Unificada (agent_knowledge)
        if ($targetClienteId) {
            $knowledgeDocs = AgentKnowledge::where('cliente_id', $targetClienteId)
                ->where('ativo', true)
                ->latest('updated_at')
                ->limit(15)
                ->get(['titulo', 'conteudo']);

            if ($knowledgeDocs->isNotEmpty()) {
                $kLines = ["\nCONHECIMENTO E DIRETRIZES COMPLEMENTARES DO BRUCEOS:"];
                foreach ($knowledgeDocs as $doc) {
                    $kLines[] = "• {$doc->titulo}: " . Str::limit(trim($doc->conteudo), 350);
                }
                $lines[] = implode("\n", $kLines);
            }
        }

        return implode("\n", $lines);
    }

    /**
     * Simula uma resposta do Bot IA no Playground de Treinamento sem precisar de um WhatsappChat real.
     */
    public function simulateReply(int $clienteId, string $inboundText, array $history = [], ?string $overrideSegment = null): array
    {
        $profile = WhatsappBotProfile::where('cliente_id', $clienteId)->first();
        $segments = config('whatsapp-bot-segments', []);
        $segmentKey = $overrideSegment ?: ($profile?->segment ?: 'vendedor_servicos');
        $preset = $segments[$segmentKey] ?? reset($segments) ?: [];

        if ($this->inboundRequestsHuman($inboundText)) {
            return [
                'ok' => true,
                'reply' => '[Escalação Automática] O cliente pediu atendimento humano. Em produção, o bot pausa automaticamente nesta conversa e alerta sua equipe no Inbox.',
                'escalated' => true,
                'elapsed_ms' => 5,
            ];
        }

        $training = WhatsappBotTraining::where('cliente_id', $clienteId)->first();
        $ctx = [
            'hoje' => now()->format('Y-m-d'),
            'dia_da_semana_hoje' => now()->isoFormat('dddd'),
            'horario_comercial' => $this->summarizeBusinessHours($training),
            'duracao_padrao_minutos' => (int) ($training?->duracao_padrao_atendimento_min ?: 60),
            'resource_label' => $training?->resource_label ?: 'Profissional',
            'recursos' => $this->summarizeResources($clienteId),
            'is_first_interaction' => empty($history),
            'is_returning_customer' => false,
            'customer_name' => 'Cliente Teste (Simulador)',
            'is_identified_customer' => false,
            'total_pedidos_fechados' => 0,
            'last_order' => null,
            'days_since_last_order' => null,
            'aniversario_hoje' => false,
        ];

        $systemPrompt = $this->composeSystemPrompt($preset, $training, $ctx, $clienteId);

        // No simulador, expomos as tools de consulta reais (catálogo, agenda, recursos)
        $safeTools = new ToolRegistry();
        $safeTools->register(new ConsultarCatalogoWhatsappTool());
        $safeTools->register(new ConsultarAgendaWhatsappTool());
        $safeTools->register(new ListarRecursosWhatsappTool());

        $driver = $this->llm->driver('deepseek');
        $workingHistory = array_slice($history, -6);
        $workingUserPrompt = $inboundText;
        $workingUserAlreadyInjected = false;
        $finalContent = '';
        $startedAt = microtime(true);

        for ($i = 0; $i < 2; $i++) {
            $payload = new PromptPayload(
                systemPrompt: $systemPrompt,
                userPrompt: $workingUserAlreadyInjected ? '' : $workingUserPrompt,
                snapshot: $ctx,
                tools: $safeTools->getToolsForLlm(),
                history: $workingHistory,
                toolChoice: 'auto',
            );

            $response = $driver->complete($payload);
            $finalContent = trim($response->content);

            if (empty($response->toolCalls)) {
                break;
            }

            if (!$workingUserAlreadyInjected) {
                $workingHistory[] = ['role' => 'user', 'content' => $workingUserPrompt];
                $workingUserAlreadyInjected = true;
            }

            $workingHistory[] = [
                'role' => 'assistant',
                'content' => $response->content ?: null,
                'tool_calls' => $response->toolCalls,
            ];

            foreach ($response->toolCalls as $call) {
                $toolName = $call['function']['name'] ?? '';
                $args = json_decode($call['function']['arguments'] ?? '{}', true) ?: [];
                $args['_default_duration'] = (int) ($training?->duracao_padrao_atendimento_min ?: 60);

                $tool = $safeTools->getTool($toolName);
                if ($tool) {
                    try {
                        $result = $tool->execute($clienteId, $args);
                    } catch (Throwable $e) {
                        $result = ['erro' => $e->getMessage()];
                    }
                } else {
                    $result = [
                        'simulador' => true,
                        'mensagem' => "No WhatsApp real, a ferramenta '{$toolName}' seria executada agora. Confirme ao cliente que o pedido/agendamento foi registrado.",
                    ];
                }

                $workingHistory[] = [
                    'role' => 'tool',
                    'tool_call_id' => $call['id'] ?? '',
                    'content' => json_encode($result, JSON_UNESCAPED_UNICODE),
                ];
            }
        }

        $elapsedMs = (int) ((microtime(true) - $startedAt) * 1000);

        if (Str::contains($finalContent, self::ESCALATION_SENTINEL)) {
            return [
                'ok' => true,
                'reply' => '[Escalação Automática] O agente identificou necessidade de passar para um humano e pausaria o bot na conversa.',
                'escalated' => true,
                'elapsed_ms' => $elapsedMs,
            ];
        }

        return [
            'ok' => true,
            'reply' => $finalContent ?: 'Não consegui gerar uma resposta no momento.',
            'escalated' => false,
            'elapsed_ms' => $elapsedMs,
        ];
    }

    protected function renderApproachBlock(array $ctx): string
    {
        $lines = ['COMO ABORDAR ESTE CLIENTE AGORA:'];

        if ($ctx['aniversario_hoje']) {
            $nome = $ctx['customer_name'] ?: 'o(a) cliente';
            $lines[] = "- E ANIVERSARIO HOJE de {$nome}! Comece parabenizando de forma natural e carinhosa antes de qualquer coisa.";
        }

        if ($ctx['is_first_interaction'] && !$ctx['is_identified_customer']) {
            $lines[] = "- E a PRIMEIRA conversa deste contato. Receba com simpatia e calor, apresente-se brevemente (nome do negocio), e, se fizer sentido no contexto, pergunte educadamente o nome dele(a) pra personalizar o atendimento.";
            $lines[] = "- Nao force a venda logo de cara. Entenda o que ele(a) procura primeiro.";
        } elseif ($ctx['is_first_interaction'] && $ctx['is_identified_customer']) {
            $nome = $ctx['customer_name'] ?: '';
            $lines[] = "- Esta e a primeira mensagem desta conversa, mas voce JA CONHECE este cliente (nome: {$nome}). Cumprimente pelo nome com naturalidade.";
            if ($ctx['last_order']) {
                $lo = $ctx['last_order'];
                $dias = $ctx['days_since_last_order'];
                $quando = $dias !== null ? "ha {$dias} dia(s)" : "recentemente";
                $lines[] = "- Ultimo pedido dele(a): \"{$lo['titulo']}\" ({$lo['status']}, {$quando}). Se o status for Concluido e nao e aniversario, pergunte brevemente como foi a experiencia antes de atender a demanda nova — isso cria conexao.";
            }
            if ($ctx['total_pedidos_fechados'] >= 3) {
                $lines[] = "- Este e um cliente FIEL ({$ctx['total_pedidos_fechados']} pedidos fechados). Trate com um pouco mais de intimidade e reconheca a fidelidade quando for natural.";
            }
        } else {
            $nome = $ctx['customer_name'] ?: '';
            if ($nome) {
                $lines[] = "- Conversa em andamento com {$nome}. Mantenha a continuidade do que ja foi dito no historico abaixo. NAO cumprimente de novo nem se apresente.";
            } else {
                $lines[] = "- Conversa em andamento. Mantenha a continuidade do que ja foi dito no historico. NAO cumprimente de novo.";
            }
            if ($ctx['last_order']) {
                $lines[] = "- Para referencia, o ultimo pedido foi \"{$ctx['last_order']['titulo']}\" ({$ctx['last_order']['status']}). Use so se o cliente perguntar ou se fizer sentido no fluxo.";
            }
        }

        return implode("\n", $lines);
    }

    protected function buildHistory(WhatsappChat $chat): array
    {
        $recent = WhatsappMessage::where('chat_id', $chat->id)
            ->whereIn('type', ['chat'])
            ->orderByDesc('id')
            ->limit(self::HISTORY_LIMIT)
            ->get()
            ->reverse()
            ->values();

        $history = [];
        foreach ($recent as $msg) {
            if (!$msg->content) continue;
            $history[] = [
                'role' => $msg->direction === 'inbound' ? 'user' : 'assistant',
                'content' => (string) $msg->content,
            ];
        }
        return $history;
    }

    protected function summarizeResources(int $clienteId): array
    {
        return AppointmentResource::where('cliente_id', $clienteId)
            ->active()
            ->ordered()
            ->get(['id', 'nome'])
            ->map(fn ($r) => ['id' => $r->id, 'nome' => $r->nome])
            ->values()
            ->all();
    }

    protected function summarizeBusinessHours(?WhatsappBotTraining $training): ?array
    {
        if (!$training || empty($training->horario_comercial)) {
            return null;
        }
        $out = [];
        foreach (WhatsappBotTraining::DIAS_SEMANA as $dia) {
            $h = $training->horario_comercial[$dia] ?? null;
            if (!$h || empty($h['aberto'])) {
                $out[$dia] = 'fechado';
                continue;
            }
            $out[$dia] = ($h['abre'] ?? '09:00') . '-' . ($h['fecha'] ?? '18:00');
        }
        return $out;
    }

    protected function inboundRequestsHuman(string $inboundText): bool
    {
        $normalized = Str::lower(Str::ascii($inboundText));
        $triggers = [
            'falar com atendente',
            'falar com humano',
            'falar com uma pessoa',
            'quero um atendente',
            'quero falar com atendente',
            'me passa pro atendente',
            'passar pro atendente',
            'chama o atendente',
            'chama alguem',
            'nao quero bot',
            'nao quero robo',
            'quero uma pessoa',
        ];
        foreach ($triggers as $t) {
            if (Str::contains($normalized, $t)) {
                return true;
            }
        }
        return false;
    }
}
