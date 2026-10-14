<?php

namespace App\Services;

use App\Agent\DTOs\PromptPayload;
use App\Agent\LlmManager;
use App\Models\ClienteFinal;
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

    public function __construct(protected LlmManager $llm)
    {
    }

    public function generateReply(WhatsappChat $chat, string $inboundText): ?string
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

        try {
            $response = $this->llm->driver('deepseek')->complete(new PromptPayload(
                systemPrompt: $systemPrompt,
                userPrompt: $inboundText,
                snapshot: $customerContext,
                history: $history,
                toolChoice: 'none',
            ));
        } catch (Throwable $e) {
            Log::error('WhatsappBot: LLM falhou', [
                'cliente_id' => $chat->cliente_id,
                'chat_id' => $chat->id,
                'msg' => $e->getMessage(),
            ]);
            return null;
        }

        $reply = trim($response->content);

        if (Str::contains($reply, self::ESCALATION_SENTINEL)) {
            $chat->update(['is_bot_active' => false]);
            Log::info('WhatsappBot: escalacao detectada pelo LLM', [
                'chat_id' => $chat->id,
                'cliente_id' => $chat->cliente_id,
            ]);
            return null;
        }

        return $reply !== '' ? $reply : null;
    }

    protected function shouldReply(WhatsappChat $chat): bool
    {
        if ($chat->is_group) {
            return false;
        }
        return (bool) $chat->is_bot_active;
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

        $context = [
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
