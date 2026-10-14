<?php

namespace App\Services;

use App\Agent\DTOs\PromptPayload;
use App\Agent\LlmManager;
use App\Models\WhatsappBotProfile;
use App\Models\WhatsappBotTraining;
use App\Models\WhatsappChat;
use App\Models\WhatsappMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class WhatsappBotService
{
    protected const HISTORY_LIMIT = 6;
    protected const ESCALATION_SENTINEL = 'ESCALAR_HUMANO';

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

        $systemPrompt = $this->composeSystemPrompt($preset, $training);
        $history = $this->buildHistory($chat);

        try {
            $response = $this->llm->driver('deepseek')->complete(new PromptPayload(
                systemPrompt: $systemPrompt,
                userPrompt: $inboundText,
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

    protected function composeSystemPrompt(array $preset, ?WhatsappBotTraining $training): string
    {
        $parts = [];

        $parts[] = "PERSONA:\n" . ($preset['persona'] ?? '');
        $parts[] = "INSTRUCOES DO NICHO:\n" . ($preset['prompt_base'] ?? '');

        if ($training) {
            $parts[] = $this->renderTrainingBlock($training);
        }

        if (!empty($preset['exemplos_resposta'])) {
            $exemplos = [];
            foreach ($preset['exemplos_resposta'] as $ex) {
                $exemplos[] = "Cliente: {$ex['user']}\nBot: {$ex['bot']}";
            }
            $parts[] = "EXEMPLOS DE TOM E RESPOSTA:\n" . implode("\n\n", $exemplos);
        }

        $parts[] = "REGRAS GERAIS:\n"
            . "- Responda em portugues brasileiro, informal mas respeitoso.\n"
            . "- Mensagens curtas (ate 3 frases por resposta sempre que possivel).\n"
            . "- Nao use markdown pesado (sem **negrito** ou listas longas). WhatsApp mostra texto puro.\n"
            . "- Se o cliente pedir atendente humano, retorne apenas: " . self::ESCALATION_SENTINEL;

        return implode("\n\n", $parts);
    }

    protected function renderTrainingBlock(WhatsappBotTraining $t): string
    {
        $lines = ['DADOS DO NEGOCIO DESTE CLIENTE:'];

        if ($t->business_name) $lines[] = "- Nome: {$t->business_name}";
        if ($t->horario_atendimento) $lines[] = "- Horario de atendimento: {$t->horario_atendimento}";
        if ($t->endereco) $lines[] = "- Endereco: {$t->endereco}";
        if ($t->catalogo_texto) $lines[] = "\nCATALOGO / MENU / SERVICOS:\n{$t->catalogo_texto}";
        if ($t->faq_custom) $lines[] = "\nFAQ ESPECIFICA:\n{$t->faq_custom}";
        if ($t->regras_proibicoes) $lines[] = "\nREGRAS E PROIBICOES:\n{$t->regras_proibicoes}";

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
