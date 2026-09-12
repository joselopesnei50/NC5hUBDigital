<?php

declare(strict_types=1);

namespace App\Agent\Chat;

use App\Agent\Contracts\LlmDriver;
use App\Agent\DTOs\PromptPayload;
use App\Models\AgentConversation;
use Exception;
use Illuminate\Support\Facades\Log;

class BruceConversation
{
    public function __construct(
        private LlmDriver $driver,
        private ConversationManager $memory,
        private ToolRegistry $tools
    ) {
    }

    /**
     * Recebe a mensagem do usuário e orquestra todo o turno de ferramentas e pensamentos.
     */
    public function handleTurn(AgentConversation $conversation, string $userMessage): string
    {
        // ==========================================
        // GUARDRAIL #1: ISOLAMENTO ABSOLUTO DE TENANT
        // O tenantId vem cravado na model do banco de dados, nunca da sessão HTTP
        // e nunca do prompt do usuário. 
        // ==========================================
        $tenantId = $conversation->cliente_id;

        // 1. O usuário falou: Gravemos na memória.
        $this->memory->addMessage($conversation, 'user', $userMessage);

        $systemPrompt = config('agent.prompts.system_bruce');
        $availableTools = $this->tools->getToolsForLlm();

        $turnCount = 0;
        $toolsAlreadyRan = false;

        // GUARDRAIL #2: LIMITE DE RECURSÃO (CUSTO)
        // Impede que o modelo entre num loop infinito conversando consigo mesmo.
        $maxTurns = 6;

        while ($turnCount < $maxTurns) {
            $turnCount++;

            // 2. Compila as memórias curtas (Janela Deslizante) para o LLM
            $history = $this->memory->buildContext($conversation);

            // GUARDRAIL #3: NO SEGUNDO TURNO EM DIANTE, PROIBE NOVAS TOOL CALLS.
            // Se o modelo ja consumiu uma rodada de tools, forcamos texto final.
            // Isso quebra qualquer tentativa de loop (chamar tool > ver resultado >
            // chamar tool de novo) direto na API, sem depender do prompt.
            $toolChoice = $toolsAlreadyRan ? 'none' : null;

            $payload = new PromptPayload(
                systemPrompt: $systemPrompt,
                userPrompt: "",
                tools: empty($availableTools) ? null : $availableTools,
                history: $history,
                toolChoice: $toolChoice
            );

            // 3. Bate na API de Inteligência
            $response = $this->driver->complete($payload);

            // Contagem de tokens: tokensIn eh o prompt inteiro (system+history+tools),
            // recompilado a cada volta. Somar tokensIn a cada rodada infla o acumulado
            // porque contamos o mesmo historico varias vezes. Contamos tokensIn apenas
            // na primeira chamada API do turno; nas seguintes, so tokensOut (o novo
            // conteudo gerado). O 'cost' financeiro permanece por chamada.
            $tokensGravar = $turnCount === 1
                ? $response->tokensIn + $response->tokensOut
                : $response->tokensOut;

            // 4. Decisão de Ação (Tool Calling)
            if (!empty($response->toolCalls)) {
                $toolsAlreadyRan = true;

                // O assistente decidiu chamar uma ou mais ferramentas. Gravamos essa intenção.
                $this->memory->addMessage(
                    conversation: $conversation,
                    role: 'assistant',
                    content: null,
                    toolCalls: $response->toolCalls,
                    tokens: $tokensGravar,
                    cost: $response->estimatedCost
                );

                // Disparamos cada ferramenta solicitada paralelamente
                foreach ($response->toolCalls as $toolCall) {
                    $toolName = $toolCall['function']['name'] ?? '';
                    $toolArgsRaw = $toolCall['function']['arguments'] ?? '{}';
                    $toolCallId = $toolCall['id'] ?? '';

                    $tool = $this->tools->getTool($toolName);

                    if (!$tool) {
                        $this->memory->addMessage(
                            $conversation, 'tool', json_encode(['error' => 'A ferramenta solicitada não existe no painel.']), null, $toolCallId
                        );
                        continue;
                    }

                    try {
                        $args = json_decode($toolArgsRaw, true) ?? [];
                        
                        // Executa a função passando o TenantId de forma selada!
                        $toolResult = $tool->execute($tenantId, $args);
                        
                        // Grava a resposta numérica (JSON) no banco com a role "tool" 
                        // para que o LLM a leia na próxima iteração do while.
                        $this->memory->addMessage(
                            $conversation, 'tool', json_encode($toolResult, JSON_UNESCAPED_UNICODE), null, $toolCallId
                        );

                    } catch (Exception $e) {
                        Log::error("[Bruce Orquestrador] Falha na Tool", ['tool' => $toolName, 'erro' => $e->getMessage()]);
                        $this->memory->addMessage(
                            $conversation, 'tool', json_encode(['error' => 'Dados temporariamente indisponíveis no banco de dados.']), null, $toolCallId
                        );
                    }
                }

                // O continue reinicia o WHILE. 
                // A IA verá os resultados das ferramentas e vai nos dar o texto humano final.
                continue;
            }

            // 5. Caso final: O assistente enviou um TEXTO final para o cliente. Fim de turno.
            $this->memory->addMessage(
                conversation: $conversation,
                role: 'assistant',
                content: $response->content,
                tokens: $tokensGravar,
                cost: $response->estimatedCost
            );

            return $response->content;
        }

        // Chegou ao teto de rodadas sem gerar texto final — nao deveria mais acontecer
        // porque toolChoice=none forca o texto no 2o turno. Se cair aqui, e sinal de
        // que a API devolveu vazio; logamos e devolvemos mensagem util.
        Log::warning('[Bruce Orquestrador] Bateu maxTurns sem texto final', [
            'conversation_id' => $conversation->id,
            'turns' => $turnCount,
        ]);
        $fallback = "Não consegui montar uma resposta agora. Reformule a pergunta ou tente novamente em alguns segundos.";
        $this->memory->addMessage($conversation, 'assistant', $fallback);

        return $fallback;
    }
}
