Você está no repositório do NC5 Hub Digital (Laravel 9, PHP 8, Livewire 2).

O chat do BruceIA no painel do cliente está quebrado: o modelo pede ferramenta 4 vezes
seguidas, nunca devolve texto final, e cai no fallback "Desculpe, eu precisei cruzar dados
em muitos relatórios simultâneos". Na tela aparecem 4 bolhas brancas vazias antes dessa
mensagem. Já diagnostiquei a causa raiz — não é a chave da API nem a DeepSeek. São três bugs
independentes que impedem o modelo de enxergar o retorno das tools. Aplique as correções
abaixo exatamente como descrito, sem redesenhar a arquitetura.

Antes de começar, leia os três arquivos envolvidos para confirmar o contexto:
app/Agent/Drivers/DeepSeekDriver.php, app/Agent/Chat/ConversationManager.php,
app/Agent/Chat/BruceConversation.php.

---

## CORREÇÃO 1 — app/Agent/Drivers/DeepSeekDriver.php

No método `complete()`, a última linha da montagem do array `$messages` é:

    $messages[] = ['role' => 'user', 'content' => $payload->userPrompt];

No fluxo de chat o `userPrompt` vem vazio de propósito (a fala real do usuário já está dentro
do `history`), mas o driver anexa mesmo assim. A API recebe `tool(resultado) → user("")` e o
modelo repete a chamada de ferramenta.

Troque por:

    // Só anexa o turno do usuário quando ele existe de fato.
    // No chat o userPrompt vem vazio: a fala já está na última mensagem do history.
    if (trim($payload->userPrompt) !== '') {
        $messages[] = ['role' => 'user', 'content' => $payload->userPrompt];
    }

Não mexa em mais nada desse arquivo.

---

## CORREÇÃO 2 — app/Agent/Chat/ConversationManager.php, método buildContext()

O histórico é ordenado por `created_at`, mas a migration usa `$table->timestamps()` — no MySQL
isso é TIMESTAMP sem fração de segundo. O `assistant(tool_calls)` e os `tool(resultado)` gravam
no mesmo segundo e o MySQL devolve em ordem arbitrária, o que faz o filtro descartar o bloco
inteiro.

Troque `->orderBy('created_at', 'desc')` por `->orderBy('id', 'desc')`.

---

## CORREÇÃO 3 — app/Agent/Chat/ConversationManager.php, filterInvalidToolBlocks()

Bug de lógica: para a SEGUNDA mensagem `tool` consecutiva de um mesmo bloco, `end($safe)` já é
um `tool` e não o `assistant`, então ela é descartada. O payload vai para a API com um assistant
anunciando 2 tool_calls e apenas 1 resposta — erro 400 garantido. Isso acontece sempre que o
Bruce chama get_business_snapshot e get_cash_flow no mesmo turno.

Substitua este bloco:

    if ($item['role'] === 'tool') {
        $last = end($safe);
        if (!$last || $last['role'] !== 'assistant' || empty($last['tool_calls'] ?? null)) {
            $i++;
            continue;
        }
    }

Por:

    if ($item['role'] === 'tool') {
        $last = $safe ? end($safe) : null;
        $okAssistant = $last && $last['role'] === 'assistant' && !empty($last['tool_calls'] ?? null);
        $okTool      = $last && $last['role'] === 'tool';
        if (!$okAssistant && !$okTool) {
            $i++;
            continue;
        }
    }

Depois, acrescente em tests/Unit/Agent/Chat/ um teste que cubra exatamente esse caso:
entrada `user → assistant(tool_calls:[a1,a2]) → tool(a1) → tool(a2)` deve sair com as 4
mensagens intactas. Siga o estilo dos testes que já existem nessa pasta.

---

## CORREÇÃO 4 — app/Agent/Chat/BruceConversation.php

a) Suba `$maxTurns` de 4 para 6.

b) A contagem de tokens está inflada: o ramo de tool_calls grava `tokens: $response->tokensIn`,
e o ramo final grava `tokensIn + tokensOut`. Como `tokensIn` é o prompt inteiro recompilado a
cada volta, o mesmo histórico é contado 3–4 vezes por turno e o guardrail de 200.000 tokens
dispara cedo demais. Some `tokensIn` apenas na primeira volta do turno; nas demais, contabilize
só `tokensOut`. Mantenha o cálculo de `estimatedCost` como está.

---

## CORREÇÃO 5 — resources/views/livewire/bruce-chat.blade.php

Blindagem para não renderizar bolha vazia quando a mensagem for só tool_call. Troque:

    @elseif($msg->role === 'assistant')

por:

    @elseif($msg->role === 'assistant' && filled($msg->content))

---

## CORREÇÃO 6 — config/agent.php

`'timeout' => env('AGENT_TIMEOUT', 120)` combinado com `Http::retry(2, 1000)` dentro de um loop
de até 6 voltas pode segurar a conexão PHP por vários minutos, de forma síncrona pelo Livewire —
o Nginx mata em 60s e o usuário vê 504 sem mensagem. Baixe o default para 45.

---

## CORREÇÃO 7 — app/Models/Configuracao.php

O método `get()` usa `??`, que só cai no default quando o valor é null. Se a linha existir no
banco com valor string vazia (salvar o form de configurações em branco produz isso), o fallback
para o `.env` nunca acontece e a DeepSeek devolve 401.

Troque `return $configs[$chave] ?? $default;` por `return $configs[$chave] ?: $default;`.

---

## LIMPEZA DO REPOSITÓRIO

O repositório é público e tem scripts ad-hoc versionados na raiz. Remova do versionamento:
`senha`, `test.php`, `test2.php`, `test3.php`, `test4.php`, `test_api.php`, `test_view.php`,
`debug_deepseek.php`, `fix_tokens.php`, `create_client.php`, `create_contract.php`,
`create_models.ps1`.

Acrescente um bloco ao `.gitignore` para scripts soltos de raiz, de modo que isso não reincida.

Não tente reescrever o histórico do git — eu faço isso manualmente com o git filter-repo.

---

## COMO VALIDAR

1. Rode `php artisan test --filter=Agent` e garanta que a suíte passa, incluindo o teste novo.
2. Explique em uma frase, por arquivo, o que mudou.
3. NÃO faça commit nem push. Deixe as alterações no working tree para eu revisar.

Se em algum ponto o código real divergir do que descrevi acima, pare e me avise em vez de
adivinhar o que eu quis dizer.
