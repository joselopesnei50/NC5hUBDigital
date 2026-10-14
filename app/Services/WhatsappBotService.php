<?php

namespace App\Services;

use App\Agent\Chat\ToolRegistry;
use App\Agent\Chat\Tools\CancelarAgendamentoWhatsappTool;
use App\Agent\Chat\Tools\ConsultarAgendaWhatsappTool;
use App\Agent\Chat\Tools\CriarAgendamentoWhatsappTool;
use App\Agent\Chat\Tools\ListarMeusAgendamentosWhatsappTool;
use App\Agent\DTOs\PromptPayload;
use App\Agent\LlmManager;
use App\Models\ClienteFinal;
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
    }

    public function generateReply(WhatsappChat $chat, string $inboundText, ?string $inboundMessageId = null): ?string
    {
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

        $systemPrompt = $this->composeSystemPrompt($preset, $training, $customerContext);
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
            ]);
        } catch (Throwable $e) {
            Log::warning('WhatsappBot: falha ao registrar interacao', ['msg' => $e->getMessage()]);
        }
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

    protected function composeSystemPrompt(array $preset, ?WhatsappBotTraining $training, array $ctx): string
    {
        $parts = [];

        $parts[] = "PERSONA:\n" . ($preset['persona'] ?? '');
        if ($training && $training->tom_de_voz) {
            $parts[] = "AJUSTE DE TOM (prioridade sobre a persona):\n" . $training->tom_de_voz;
        }

        $parts[] = "INSTRUCOES DO NICHO:\n" . ($preset['prompt_base'] ?? '');

        if ($training) {
            $parts[] = $this->renderTrainingBlock($training);
        }

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

        $parts[] = "AGENDAMENTOS (FERRAMENTAS DISPONIVEIS):\n"
            . "- Tools: consultar_agenda, criar_agendamento, listar_meus_agendamentos, cancelar_agendamento.\n"
            . "- NUNCA proponha ou agende horario fora do horario_comercial do snapshot. Se o cliente pedir fora, informe o horario de funcionamento e sugira o proximo horario valido.\n"
            . "- Fluxo pra MARCAR:\n"
            . "  1. Entenda o que ele quer agendar e para quando.\n"
            . "  2. Opcional: consultar_agenda pra ver ocupacao do dia.\n"
            . "  3. REPITA a proposta: \"Deixa confirmar: X dia Y as Z, pode ser?\". ESPERE o SIM explicito.\n"
            . "  4. Chame criar_agendamento. Se a tool retornar erro conflito_de_horario ou horario_fora_do_expediente, OFERECA pro cliente as sugestoes retornadas pela tool e espere ele escolher uma antes de tentar criar de novo.\n"
            . "- Fluxo pra CANCELAR ou REMARCAR:\n"
            . "  1. Chame listar_meus_agendamentos pra pegar o id correto.\n"
            . "  2. Se tiver mais de um, pergunte ao cliente QUAL (data+titulo) ele quer alterar.\n"
            . "  3. CONFIRME: \"Vou cancelar o agendamento de X dia Y, confirma?\". ESPERE o SIM.\n"
            . "  4. Chame cancelar_agendamento com o appointment_id.\n"
            . "  5. Se for remarcacao, apos cancelar siga o fluxo pra MARCAR com o novo horario.\n"
            . "- Interprete datas relativas (\"amanha\", \"sexta\", \"dia 15\") pra YYYY-MM-DD baseando-se em hoje do snapshot.";

        $parts[] = "REGRAS GERAIS:\n"
            . "- Responda em portugues brasileiro, humano e natural.\n"
            . "- Mensagens curtas (ate 3 frases por resposta sempre que possivel).\n"
            . "- Nao use markdown pesado (sem **negrito** ou listas longas). WhatsApp mostra texto puro.\n"
            . "- Nunca invente dados que nao foram passados nos blocos acima.\n"
            . "- Se o cliente pedir atendente humano, retorne apenas: " . self::ESCALATION_SENTINEL;

        return implode("\n\n", $parts);
    }

    protected function renderTrainingBlock(WhatsappBotTraining $t): string
    {
        $lines = ['DADOS E FUNDAMENTOS DO NEGOCIO DESTE CLIENTE:'];

        if ($t->business_name) $lines[] = "- Nome: {$t->business_name}";
        if ($t->horario_atendimento) $lines[] = "- Horario de atendimento: {$t->horario_atendimento}";
        if ($t->endereco) $lines[] = "- Endereco: {$t->endereco}";
        if ($t->publico_alvo) $lines[] = "- Publico alvo: {$t->publico_alvo}";

        if ($t->historia_negocio) $lines[] = "\nHISTORIA E PROPOSITO:\n{$t->historia_negocio}";
        if ($t->diferenciais) $lines[] = "\nDIFERENCIAIS (use pra reforcar valor na venda):\n{$t->diferenciais}";
        if ($t->processo_producao) $lines[] = "\nCOMO O PRODUTO/SERVICO E FEITO/ENTREGUE:\n{$t->processo_producao}";
        if ($t->catalogo_texto) $lines[] = "\nCATALOGO / MENU / SERVICOS:\n{$t->catalogo_texto}";
        if ($t->politicas) $lines[] = "\nPOLITICAS (troca, cancelamento, garantia):\n{$t->politicas}";
        if ($t->faq_custom) $lines[] = "\nFAQ ESPECIFICA:\n{$t->faq_custom}";
        if ($t->regras_proibicoes) $lines[] = "\nREGRAS E PROIBICOES (prioridade sobre tudo):\n{$t->regras_proibicoes}";

        return implode("\n", $lines);
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
