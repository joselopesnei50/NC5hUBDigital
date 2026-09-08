# Brief de Ajustes — Site NC5 Hub Digital
### v2 — reposicionamento de ecossistema

**Projeto:** nc5hubdigital.com.br
**Objetivo:** reposicionar o site de "catálogo de serviços" para "ecossistema de crescimento", adicionar prova real e aumentar conversão do público PME para o diagnóstico gratuito.

---

## Como usar este documento

Brief de execução. Cada item tem prioridade (P0/P1/P2), o estado atual e o que deve mudar. Itens `[PREENCHER]` dependem de informação do cliente — pergunte, não invente. **Não crie depoimentos, números, logos ou cases fictícios em nenhuma hipótese.**

Ordem: P0 → P1 → P2.

---

## 0. Posicionamento — leia antes de escrever qualquer linha

O site hoje se apresenta como uma lista de cinco serviços numerados. Isso comunica "agência que faz várias coisas" e coloca a NC5 na mesma prateleira de qualquer freelancer ou agência de bairro, competindo por preço item a item.

A NC5 não é isso. Ela tem duas peças que quase nenhuma agência brasileira tem: **tecnologia proprietária** (BruceIA) e **um produto SaaS em operação** (Vivensi). E entrega marca, site, infraestrutura, redes sociais, automação e mídia com o mesmo time.

**A narrativa correta é um ciclo fechado, não um menu:**

```
        ┌──────────────────────────────────────┐
        │                                      │
        ▼                                      │
   INTELIGÊNCIA  ──►  CONSTRUÇÃO  ──►  OPERAÇÃO
   (BruceIA)          (marca, web,      (redes, conteúdo,
   diagnóstico,        infra)            automação, mídia, CRM)
   dados, decisão                              │
        ▲                                      │
        │        a operação gera dados         │
        └──────────────────────────────────────┘
```

O argumento central: **serviço avulso não faz negócio crescer porque ninguém fecha o ciclo.** A agência entrega o site e some. O social media posta sem saber o que vende. O tráfego roda sem saber para onde manda. A NC5 opera as três camadas e usa o dado de uma para alimentar a outra.

Toda copy nova deve reforçar isso. Serviços continuam existindo no site — mas **subordinados às camadas**, nunca como protagonistas numerados.

**Regra prática:** se uma seção pode ser lida como "veja tudo o que sabemos fazer", ela está errada. Se é lida como "veja como o crescimento acontece aqui", está certa.

---

## 0.1 Direção de design — moderno, minimalista, orientado a uso

### O conflito que precisa ser resolvido primeiro

Este brief adiciona quatro seções novas à home (tensão, ciclo, tecnologia própria, prova). Somar seções e pedir minimalismo ao mesmo tempo não fecha. **Minimalismo aqui não é usar menos seções — é cada seção fazer uma única coisa e sair do caminho.**

Regra de corte: se uma seção não muda a decisão de quem está lendo, ela sai. Aplicar isso sem dó na metodologia de 4 fases e no bloco de insights, que são os candidatos naturais a encolher.

### Princípios

**Uma ideia por rolagem.** Cada seção comunica exatamente um argumento e tem um único ponto de foco visual. Se o visitante precisa decidir para onde olhar, a seção falhou.

**Hierarquia por escala e espaço, não por caixa e borda.** O reflexo padrão é embrulhar tudo em card com sombra. Modernidade minimalista vem do oposto: tipografia grande, muito espaço em branco, divisores de 1px. Card só onde existe comparação real entre itens paralelos — no ciclo e na tecnologia própria, sim; na seção de tensão, não.

**Menos superfícies.** Um valor de border-radius no site inteiro. Sombra em no máximo um tipo de elemento. Nenhum gradiente decorativo.

**Movimento só onde ensina.** Animação de entrada em fade+translate curto (200–300ms), escalonada, e nada mais. Sem parallax, sem contador animado, sem elemento flutuante. Excesso de animação é o que mais faz um site parecer template.

### Sistema

**Antes de escrever CSS:** extrair os tokens que já existem no site (cores, fontes, escala, radius) e listá-los no PR. O reposicionamento é de conteúdo e estrutura — a identidade visual da NC5 permanece. Não inventar paleta nova.

Restrições a aplicar sobre o que já existe:

- **Tipografia:** no máximo 2 famílias e 3 pesos no site inteiro. Escala tipográfica definida e respeitada — nada de tamanho arbitrário por seção.
- **Cor:** neutros carregam 90% da página. Uma única cor de destaque, usada só em CTA primário e em números de resultado. Cor não pode ser o único meio de transmitir informação.
- **Espaçamento:** escala de 8px, sem exceção. O ritmo vertical entre seções é o que mais comunica "caro e bem feito" — mais do que qualquer efeito.
- **Radius:** um valor só, aplicado em tudo.

### Decisões de UX concretas

**Navegação:** hoje são 5 itens mais 2 botões. Reduzir para 4 links e 1 CTA. Menu que cabe na cabeça de quem lê.

**Um CTA primário por tela de rolagem.** O "Falar com o time" (item P1.6) é secundário e visualmente subordinado — outline, nunca preenchido. Dois botões com o mesmo peso anulam os dois.

**Formulário do diagnóstico é o gargalo real de conversão.** Cada campo derruba conclusão. Pedir o mínimo para gerar a análise e deixar o resto para depois do resultado — o visitante entrega dados com muito mais facilidade depois de ver valor. Se hoje há mais de 4 campos antes do primeiro resultado, cortar.

**Estados são parte do design, não sobra.** Especificar e implementar: carregamento do diagnóstico (com indicação real de progresso — "menos de 1 minuto" precisa parecer menos de 1 minuto), erro de envio com instrução do que fazer, e o que aparece quando a análise não retorna dados suficientes. Erro nunca pede desculpas e nunca é vago sobre o que aconteceu.

**Mobile é o padrão, não a adaptação.** O tráfego de anúncio para PME chega quase todo pelo celular. Projetar cada seção nova pensando na largura pequena primeiro; o desktop é a expansão.

**Piso de qualidade, sem alarde:** contraste mínimo 4.5:1 em texto, foco de teclado visível, alvos de toque de 44px, `prefers-reduced-motion` respeitado, fontes com `font-display: swap`, imagens em WebP com `width`/`height` declarados para não haver salto de layout.

### O elemento de assinatura

Gastar a ousadia em um lugar só: **o ciclo**. É o argumento central do reposicionamento e deve ser a coisa que a pessoa lembra do site. Merece execução caprichada — o retorno visual da operação para a inteligência, feito em CSS puro, sem biblioteca.

Todo o resto ao redor fica quieto e disciplinado. Se algo compete com o ciclo por atenção, esse algo é que está errado.

---

## P0 — Bloqueadores

### P0.1 — Links legais vazios + LGPD

**Hoje:** rodapé com "Privacidade" e "Termos" apontando para `#`.
**Problema:** o site coleta dados pessoais no diagnóstico gratuito. Página de privacidade ausente é exigência legal não cumprida, além de sinal negativo de credibilidade.

**Fazer:**
- Criar `/privacidade` e `/termos` como páginas reais.
- Na privacidade cobrir: dados coletados no diagnóstico, finalidade, retenção, compartilhamento com terceiros (incluir provedores de IA se os dados passarem por eles), direitos do titular, contato do encarregado.
- Aviso de consentimento no formulário do diagnóstico, com link para a política.
- Se houver pixel Meta ou GA, banner de consentimento de cookies.

> Gerar estrutura e texto-base, mas sinalizar no PR que o conteúdo jurídico precisa de revisão por advogado antes de publicar.

### P0.2 — Dados institucionais no rodapé

**Hoje:** só logo, links e copyright.
**Fazer:** razão social, CNPJ, endereço e telefone/WhatsApp. Para PME brasileira, a ausência disso levanta suspeita.

`[PREENCHER]` — razão social, CNPJ, endereço, telefone.

---

## P1 — Reestruturação da home

### Nova arquitetura de seções

| # | Seção | Status | Fundo |
|---|---|---|---|
| 1 | Hero | reescrever headline | claro + painel escuro |
| 2 | A tensão — por que serviço solto não cresce | **nova** | claro |
| 3 | O Ciclo NC5 — três camadas | **substitui a lista de serviços** | escuro |
| 4 | Tecnologia própria — BruceIA + Vivensi | **nova** | azul institucional |
| 5 | Metodologia (4 fases) | manter, ancorar nas camadas | claro |
| 6 | Prova / Cliente | **nova** | creme |
| 7 | Insights (blog) | manter | claro |
| 8 | CTA final | adicionar CTA secundário | escuro |
| 9 | Footer | dados institucionais + legal | escuro |

---

### P1.1 — Hero: nova headline

**Hoje:**
- H1: "Estratégia. Design. Escala."
- Sub: "Alinhamos posicionamento de marca, produção de conteúdo e inteligência artificial em uma esteira única de alta performance."

**Problema:** três substantivos abstratos que servem para qualquer agência do país. Não dizem o que a NC5 faz, para quem, nem por que ela é diferente.

Implementar a **Opção A**; deixar B e C comentadas no código para teste posterior.

**Opção A — recomendada (ecossistema)**
```
H1:  Seu crescimento não cabe
     em um serviço avulso.
Sub: A NC5 reúne inteligência de dados, construção de marca e site, e operação
     diária de redes, automação e mídia paga — no mesmo ciclo, com o mesmo time.
```

**Opção B — direta ao caixa**
```
H1:  Marketing que aparece no caixa,
     não só no engajamento.
Sub: Diagnóstico com IA, marca e site construídos do zero, redes e WhatsApp
     operando todo dia. Um ciclo único para PMEs que precisam de venda previsível.
```

**Opção C — institucional**
```
H1:  Do dado à venda.
     E de volta ao dado.
Sub: O ciclo NC5 conecta inteligência, construção e operação para que cada
     campanha alimente a próxima decisão.
```

Manter os dois selos do hero (`API WhatsApp Business` / `BruceIA Engine`) — são prova concreta e funcionam bem onde estão.

### P1.2 — Nova seção: a tensão

Bloco curto entre o hero e o ciclo. Existe para criar o problema que o ecossistema resolve. Sem ele, as três camadas parecem apenas uma forma bonita de listar serviços.

Eyebrow: `POR QUE NEGÓCIO BOM TRAVA`

```
H2: O problema quase nunca é falta de esforço.
    É que ninguém fecha o ciclo.

Corpo:
Uma agência entrega o site e some. O social media posta sem saber o que vende.
O tráfego roda sem saber para onde está mandando gente. Cada fornecedor
enxerga um pedaço, ninguém enxerga o todo — e o dono do negócio vira o
integrador de três fornecedores que não conversam.

A NC5 opera as três camadas do crescimento com o mesmo time. O que a operação
aprende volta como decisão. O que a decisão define vira execução na semana seguinte.
```

Fundo claro, sem cards, texto centralizado com largura máxima de ~720px. Deve ser lido rápido.

### P1.3 — O Ciclo NC5 (substitui a lista 01–05 de serviços)

**Hoje:** `/servicos` e a home apresentam 5 serviços numerados: Automação WhatsApp, Desenvolvimento Web, Desenvolvimento de Marca, Servidor AWS, CRM Customizado.

**Problema:** vira vitrine. E deixa de fora duas coisas que a NC5 faz e vende — gestão de redes sociais e mídia paga.

**Fazer:** substituir por três camadas. Cada camada é um card grande com título, promessa e as capacidades listadas dentro dela como texto corrido ou tags — **nunca numeradas, nunca com peso visual igual ao da camada**.

Eyebrow: `O CICLO NC5`
H2: `Três camadas. Um time. Um ciclo que não para.`

**Inteligência**
```
Decidir com dado, não com achismo.

Antes de produzir qualquer coisa, entendemos onde o seu negócio perde venda.
O BruceIA cruza os dados da sua marca com o comportamento do seu mercado e
aponta a alavanca com maior retorno no menor prazo.

Dentro desta camada: diagnóstico com IA · auditoria de posicionamento ·
otimização de conversão (CRO) · roadmap de crescimento · dashboards e leitura
contínua de resultado
```

**Construção**
```
O ativo digital que sustenta a venda.

Marca, site e infraestrutura construídos para converter e para aguentar volume.
Não é entrega e tchau: o que construímos é o que operamos depois.

Dentro desta camada: posicionamento e identidade visual · sites e landing pages
de alta performance · plataformas sob medida · infraestrutura AWS · CRM com
pipeline visual
```

**Operação**
```
A máquina girando todo dia.

Presença que gera conversa e conversa que vira venda — com atendimento que não
deixa cliente esperando até o dia seguinte.

Dentro desta camada: gestão de redes sociais · produção de conteúdo ·
mídia paga · automação de WhatsApp com API Oficial · gestão do funil no CRM
```

**Fechamento da seção**, logo abaixo dos três cards:
```
E o ciclo recomeça: cada campanha, cada conversa e cada venda vira dado novo
para a próxima decisão.
```

**Sem numeração nas camadas.** O site hoje numera os serviços de 01 a 05, e o reflexo seria numerar as camadas de 01 a 03. Não fazer isso: numeração implica começo e fim, e o argumento aqui é justamente que não há fim. A circularidade é o elemento de assinatura da página — resolver com seta de retorno da Operação para a Inteligência, ou com o próprio arranjo dos blocos. CSS puro, sem biblioteca.

**Página `/servicos`:** aplicar a mesma estrutura de três camadas, com espaço para detalhar cada capacidade dentro da camada. É lá que cabe a descrição técnica que hoje está nos cards 01–05 — todas devem ser preservadas, apenas reposicionadas sob a camada correta.

**Mapa de migração (nada se perde):**

| Serviço atual | Vai para |
|---|---|
| Automação WhatsApp com API Oficial | Camada 03 — Operação |
| Desenvolvimento Web | Camada 02 — Construção |
| Desenvolvimento de Marca | Camada 02 — Construção |
| Servidor AWS | Camada 02 — Construção |
| CRM Customizado | Camada 02 (implantação) + Camada 03 (gestão) |
| *(novo)* Gestão de redes sociais | Camada 03 — Operação |
| *(novo)* Mídia paga | Camada 03 — Operação |
| *(novo)* Diagnóstico BruceIA | Camada 01 — Inteligência |

### P1.4 — Nova seção: Tecnologia própria

Esta é a seção que separa a NC5 de todo concorrente direto. Fundo azul institucional (`#1A4A8A`) para destacar como bloco de autoridade.

**Framing obrigatório:** BruceIA e Vivensi são **produtos da NC5**, não clientes. Apresentar produto próprio como cliente é uma imprecisão que qualquer prospect descobre — e custa mais confiança do que o card gera. A versão verdadeira também é mais forte: agência que constrói e opera produto próprio é raridade no mercado brasileiro.

Eyebrow: `TECNOLOGIA PRÓPRIA`
H2: `Não só prestamos serviço. Construímos e operamos produto.`

Dois cards lado a lado (stack no mobile):

**Card 1 — BruceIA**
```
MOTOR DE INTELIGÊNCIA

BruceIA
O motor que roda a camada de Inteligência. Cruza dados da marca com o
comportamento do mercado e devolve diagnóstico, gargalo e próxima alavanca.
É o mesmo motor que roda no diagnóstico gratuito — você testa antes de contratar
qualquer coisa.

[PREENCHER — 1 métrica: nº de diagnósticos gerados, ou tempo médio de análise]
```

**Card 2 — Vivensi**
```
PLATAFORMA SAAS

Vivensi
Plataforma de gestão para o terceiro setor — ONGs, institutos e associações.
Marca, produto, infraestrutura e aquisição construídos e operados pela NC5,
do zero. É o ciclo completo aplicado em um negócio nosso.

[PREENCHER — 1 métrica real: organizações ativas, tempo de operação ou
volume gerenciado]
```

**Linha de fechamento da seção:**
```
O que aplicamos no seu negócio é o que rodamos no nosso.
```

### P1.5 — Nova seção: Prova / Cliente

Separada da seção de tecnologia própria. Aqui entram **clientes de verdade** — o site hoje não tem nenhuma prova de resultado, e essa é a primeira coisa que um empreendedor procura antes de entregar o e-mail.

Eyebrow: `QUEM JÁ ESTÁ COM A GENTE`

`[PREENCHER]` — para escrever o card do cliente, são necessários:
- Nome do cliente e autorização para divulgar (sem autorização, usar descrição genérica: "rede de clínicas em SP", "distribuidora no interior de SP")
- Segmento
- Quais camadas do ciclo foram acionadas
- Um resultado mensurável com período — ex.: "tempo de resposta no WhatsApp caiu de 6h para 4min em 60 dias"
- Logo em SVG ou PNG, se autorizado

**Formato do card:**
```
[Logo ou nome]
[Segmento · camadas acionadas]
[O que foi feito — 2 linhas]
[Resultado com número e período — destaque em Playfair]
```

**Se ainda não houver dados consolidados:** publicar a seção com um único card e layout centralizado, ou omiti-la temporariamente. Um card verdadeiro vale mais que três vagos. **Não publicar case sem número.**

### P1.6 — CTA secundário

**Hoje:** todos os botões levam ao diagnóstico. Quem já está pronto para contratar não tem caminho — perde-se o lead mais quente.

**Fazer:** CTA secundário estilo outline ao lado do primário no hero, na seção do ciclo e no CTA final. Texto: **"Falar com o time"**, apontando para `/contato` ou link direto de WhatsApp. O CTA do diagnóstico continua dominante visualmente.

### P1.7 — BruceIA: mostrar, não descrever

**Hoje:** o BruceIA é citado três vezes e nunca é mostrado.
**Problema:** a objeção silenciosa é "isso deve ser isca genérica para pegar meu e-mail". Texto não vence essa objeção — imagem vence.

**Fazer:** no bloco "Quer ver o Bruce analisando sua marca?", adicionar mockup ou print real do relatório com dados sensíveis borrados. Abaixo, 3 bullets curtos: o que ele analisa (fontes de dado), quanto tempo leva, o que você recebe no final.

`[PREENCHER]` — print do relatório e resposta às 3 perguntas.

---

## P2 — Refinamentos

### P2.1 — Metodologia ancorada nas camadas

**Hoje:** Diagnóstico → Estratégia → Execução → Escala, uma linha cada. É a metodologia que toda agência publica.

**Fazer:** conectar cada fase à camada correspondente e adicionar prazo e entregável concreto.

```
Fase 01 · Diagnóstico   → Camada Inteligência   [prazo] [entregável]
Fase 02 · Estratégia    → Inteligência + Construção
Fase 03 · Execução      → Construção + Operação
Fase 04 · Escala        → Operação → volta à Inteligência
```

`[PREENCHER]` — prazo médio e entregável de cada fase. Sem padrão definido, é preferível cortar a seção a mantê-la genérica.

### P2.2 — Sinal de investimento

Nenhuma referência a preço hoje. O pequeno empreendedor abandona por medo de ser caro, sem nunca perguntar.

**Fazer:** bloco curto "Como começamos" antes do CTA final. Não tabela de preços — uma faixa ou modelo de entrada. Ex.: "Começamos sempre por um sprint de diagnóstico de R$ X, abatido no projeto."

`[PREENCHER]` — decidir se haverá sinal de preço e qual. Se não, pular.

### P2.3 — Blog

3 artigos, todos com data 25 Jul 2026 — sinaliza publicação em lote e blog abandonado. Espaçar as datas e definir cadência (sugestão: 2 posts/mês). Adicionar data de atualização visível.

---

## Regras de tom de voz

**Usar:** faturamento, venda, cliente, orçamento, caixa, previsível, resultado, tempo de resposta, funil, ciclo, camada, operação.

**Evitar:** disruptivo, revolucionário, inovador, ecossistema robusto, soluções de ponta, empoderar, game-changer. Também evitar "do briefing ao pixel" como headline — é jargão de agência falando com agência.

**Sobre a palavra "ecossistema":** o conceito deve estar em toda a estrutura do site, mas a palavra em si aparece em quase todo site de agência e perdeu força. Preferir **"ciclo"**, que é mais concreto e descreve melhor o que acontece.

**Princípio:** o tom institucional premium pode ficar. A copy voltada ao pequeno empreendedor precisa falar de dinheiro e tempo, não de conceito. Havendo conflito entre soar sofisticado e soar compreensível, escolher compreensível.

**Proibido:** promessa de resultado numérico não comprovada ("dobre seu faturamento", "3x mais vendas garantido"). Queima confiança e reprova em anúncio na Meta se a copy for reaproveitada.

---

## Checklist de entrega

- [ ] `/privacidade` e `/termos` criadas e linkadas no rodapé
- [ ] Consentimento LGPD no formulário do diagnóstico
- [ ] CNPJ, endereço e contato no rodapé
- [ ] Hero com nova headline (Opção A ativa, B e C comentadas)
- [ ] Seção de tensão criada entre hero e ciclo
- [ ] Lista numerada 01–05 removida da home e substituída pelas três camadas
- [ ] Todos os 5 serviços atuais preservados dentro das camadas (conferir mapa de migração)
- [ ] Gestão de redes sociais e mídia paga incluídas na camada 03
- [ ] Circularidade do ciclo indicada visualmente
- [ ] `/servicos` reestruturada nas mesmas três camadas
- [ ] Seção Tecnologia Própria com BruceIA e Vivensi rotulados como **produto**, nunca como cliente
- [ ] Seção de Prova separada, só com cliente real — ou omitida
- [ ] CTA secundário "Falar com o time" em hero, ciclo e CTA final
- [ ] Bloco do BruceIA com print do relatório e 3 bullets
- [ ] Nenhum depoimento, logo, número ou case inventado

**Design e UX**

- [ ] Tokens existentes extraídos e listados no PR antes de qualquer CSS novo
- [ ] Máximo de 2 famílias e 3 pesos tipográficos no site inteiro
- [ ] Uma única cor de destaque, usada só em CTA primário e números de resultado
- [ ] Escala de espaçamento de 8px respeitada; um único valor de radius
- [ ] Camadas do ciclo **sem** numeração 01/02/03
- [ ] Circularidade do ciclo resolvida em CSS puro, sem biblioteca
- [ ] Navegação reduzida a 4 links + 1 CTA
- [ ] Um único CTA primário por tela de rolagem; secundário sempre outline
- [ ] Formulário do diagnóstico com no máximo 4 campos antes do primeiro resultado
- [ ] Estados de carregamento, erro e dados insuficientes implementados
- [ ] Animações limitadas a fade + translate de 200–300ms; sem parallax nem contador
- [ ] Contraste mínimo 4.5:1, foco de teclado visível, alvos de toque de 44px
- [ ] `prefers-reduced-motion` respeitado
- [ ] Fontes com `font-display: swap`; imagens em WebP com dimensões declaradas
- [ ] Mobile projetado primeiro e verificado abaixo de 860px em todas as seções novas
- [ ] Headings em ordem semântica, alt em todas as imagens

---

## Perguntas a fazer antes de começar

1. Métrica real publicável do Vivensi?
2. Métrica real publicável do BruceIA (diagnósticos gerados, tempo médio)?
3. O cliente autoriza divulgação de nome e logo? Qual o resultado mensurável e o período?
4. Razão social, CNPJ, endereço e telefone para o rodapé?
5. Existe print do relatório do BruceIA que possa ir ao site?
6. Prazo e entregável de cada uma das 4 fases da metodologia?
7. Haverá sinal de preço no site?

Se alguma resposta não vier, implementar o restante e deixar `TODO` visível no código — nunca preencher com conteúdo fictício.
