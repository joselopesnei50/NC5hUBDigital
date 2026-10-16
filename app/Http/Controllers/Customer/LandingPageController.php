<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Agent\Contracts\LlmDriver;
use App\Agent\DTOs\PromptPayload;
use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\LandingPage;
use App\Models\ProdutoServico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LandingPageController extends Controller
{
    protected function resolveCliente(): ?Cliente
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }
        return $user->cliente ?? Cliente::where('user_id', $user->id)->first();
    }

    protected function getOrCreateLandingPage(Cliente $cliente): LandingPage
    {
        $lp = LandingPage::where('cliente_id', $cliente->id)->first();
        if ($lp) {
            return $lp;
        }

        $nomeEmpresa = $cliente->nome_fantasia ?: ($cliente->razao_social ?: 'Minha Empresa');
        $baseSlug = Str::slug($nomeEmpresa) ?: ('empresa-' . $cliente->id);
        $slug = $baseSlug;
        $counter = 1;
        while (LandingPage::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        return LandingPage::create([
            'cliente_id' => $cliente->id,
            'slug' => $slug,
            'template' => 'corporativo',
            'is_published' => false,
            'titulo_pagina' => $nomeEmpresa,
            'meta_descricao' => "Conheça {$nomeEmpresa}. Atendimento especializado, qualidade e compromisso com resultados.",
            'cor_primaria' => '#6366f1',
            'cor_secundaria' => '#0f172a',
            'hero_badge' => 'Atendimento Especializado',
            'hero_titulo' => "Soluções completas e sob medida com a {$nomeEmpresa}",
            'hero_subtitulo' => 'Qualidade, agilidade e atendimento personalizado para você ou sua empresa alcançarem os melhores resultados.',
            'hero_cta_texto' => 'Solicitar Orçamento no WhatsApp',
            'sobre_titulo' => "Sobre a {$nomeEmpresa}",
            'sobre_texto' => "Somos referência em qualidade e dedicação no atendimento aos nossos clientes. Nossa equipe combina experiência técnica e atendimento próximo para entregar sempre a melhor solução.",
            'servicos_json' => [
                [
                    'titulo' => 'Consultoria & Atendimento Especializado',
                    'descricao' => 'Análise detalhada da sua necessidade com diagnóstico rápido e transparente.',
                    'preco' => '',
                    'destaque' => 'Mais procurado',
                ],
                [
                    'titulo' => 'Soluções Personalizadas',
                    'descricao' => 'Projetos e serviços executados sob medida com garantia de qualidade.',
                    'preco' => '',
                    'destaque' => '',
                ],
                [
                    'titulo' => 'Suporte e Acompanhamento',
                    'descricao' => 'Equipe pronta para atender você com agilidade em todas as etapas.',
                    'preco' => '',
                    'destaque' => '',
                ],
            ],
            'diferenciais_json' => [
                ['titulo' => 'Atendimento Ágil', 'descricao' => 'Resposta rápida pelo WhatsApp e canais digitais.'],
                ['titulo' => 'Qualidade Garantida', 'descricao' => 'Processos validados e foco total na satisfação do cliente.'],
                ['titulo' => 'Condições Facilitadas', 'descricao' => 'Orçamentos transparentes e ótimo custo-benefício.'],
            ],
            'depoimentos_json' => [
                [
                    'nome' => 'Carlos Mendes',
                    'cargo' => 'Cliente verificado',
                    'texto' => 'Excelente atendimento desde o primeiro contato. Muito profissionais e pontuais!',
                    'estrelas' => 5,
                ],
                [
                    'nome' => 'Fernanda Oliveira',
                    'cargo' => 'Cliente verificada',
                    'texto' => 'Superou nossas expectativas. Recomendo fortemente pela qualidade e transparência.',
                    'estrelas' => 5,
                ],
            ],
            'faq_json' => [
                [
                    'pergunta' => 'Como faço para solicitar um orçamento?',
                    'resposta' => 'Basta clicar no botão do WhatsApp ou preencher o formulário nesta página que nossa equipe entrará em contato rapidamente.',
                ],
                [
                    'pergunta' => 'Qual é o horário de atendimento?',
                    'resposta' => 'Atendemos de segunda a sexta em horário comercial, com plantão de dúvidas via WhatsApp.',
                ],
            ],
            'whatsapp_numero' => $cliente->telefone ?? '',
            'telefone' => $cliente->telefone ?? '',
            'email_contato' => $cliente->user->email ?? '',
            'endereco_texto' => $cliente->endereco ?? '',
            'horario_atendimento' => 'Segunda a Sexta, das 08h às 18h',
        ]);
    }

    public function edit()
    {
        $cliente = $this->resolveCliente();
        if (!$cliente) {
            return redirect()->route('customer.index')
                ->with('error', 'Sua conta ainda não está vinculada a um cadastro empresarial.');
        }

        $landingPage = $this->getOrCreateLandingPage($cliente);
        $templates = LandingPage::TEMPLATES;
        $catalogoCount = ProdutoServico::where('cliente_id', $cliente->id)->count();

        return view('customer.landing-page.edit', compact('landingPage', 'templates', 'cliente', 'catalogoCount'));
    }

    public function update(Request $request)
    {
        $cliente = $this->resolveCliente();
        if (!$cliente) {
            return redirect()->route('customer.index')
                ->with('error', 'Cliente não identificado.');
        }

        $landingPage = $this->getOrCreateLandingPage($cliente);

        $validated = $request->validate([
            'template' => ['required', 'string', Rule::in(array_keys(LandingPage::TEMPLATES))],
            'slug' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('landing_pages', 'slug')->ignore($landingPage->id),
            ],
            'is_published' => 'nullable|boolean',
            'titulo_pagina' => 'required|string|max:160',
            'meta_descricao' => 'nullable|string|max:500',
            'cor_primaria' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'cor_secundaria' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'hero_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'remover_logo' => 'nullable|boolean',
            'remover_hero_image' => 'nullable|boolean',

            'hero_badge' => 'nullable|string|max:100',
            'hero_titulo' => 'required|string|max:220',
            'hero_subtitulo' => 'nullable|string|max:1000',
            'hero_cta_texto' => 'required|string|max:80',

            'sobre_titulo' => 'nullable|string|max:160',
            'sobre_texto' => 'nullable|string|max:3000',

            'servicos' => 'nullable|array|max:24',
            'servicos.*.titulo' => 'nullable|string|max:140',
            'servicos.*.descricao' => 'nullable|string|max:600',
            'servicos.*.preco' => 'nullable|string|max:60',
            'servicos.*.destaque' => 'nullable|string|max:60',

            'diferenciais' => 'nullable|array|max:12',
            'diferenciais.*.titulo' => 'nullable|string|max:120',
            'diferenciais.*.descricao' => 'nullable|string|max:400',

            'depoimentos' => 'nullable|array|max:12',
            'depoimentos.*.nome' => 'nullable|string|max:120',
            'depoimentos.*.cargo' => 'nullable|string|max:120',
            'depoimentos.*.texto' => 'nullable|string|max:800',
            'depoimentos.*.estrelas' => 'nullable|integer|min:1|max:5',

            'faq' => 'nullable|array|max:15',
            'faq.*.pergunta' => 'nullable|string|max:250',
            'faq.*.resposta' => 'nullable|string|max:1200',

            'whatsapp_numero' => 'nullable|string|max:40',
            'telefone' => 'nullable|string|max:40',
            'email_contato' => 'nullable|email|max:160',
            'endereco_texto' => 'nullable|string|max:255',
            'horario_atendimento' => 'nullable|string|max:180',
            'instagram_url' => 'nullable|url|max:255',
            'facebook_url' => 'nullable|url|max:255',
            'google_maps_embed' => 'nullable|string|max:1500',
        ], [
            'slug.regex' => 'O endereço (slug) deve conter apenas letras minúsculas, números e hífens (ex: minha-empresa).',
            'slug.unique' => 'Este endereço (slug) já está em uso por outra página. Escolha outro.',
            'cor_primaria.regex' => 'Escolha uma cor primária válida em formato hexadecimal.',
            'cor_secundaria.regex' => 'Escolha uma cor secundária válida em formato hexadecimal.',
        ]);

        // Limpar arrays estruturados removendo itens vazios
        $servicos = collect($request->input('servicos', []))
            ->filter(fn ($s) => !empty(trim((string) ($s['titulo'] ?? ''))))
            ->map(fn ($s) => [
                'titulo' => trim((string) ($s['titulo'] ?? '')),
                'descricao' => trim((string) ($s['descricao'] ?? '')),
                'preco' => trim((string) ($s['preco'] ?? '')),
                'destaque' => trim((string) ($s['destaque'] ?? '')),
            ])->values()->all();

        $diferenciais = collect($request->input('diferenciais', []))
            ->filter(fn ($d) => !empty(trim((string) ($d['titulo'] ?? ''))))
            ->map(fn ($d) => [
                'titulo' => trim((string) ($d['titulo'] ?? '')),
                'descricao' => trim((string) ($d['descricao'] ?? '')),
            ])->values()->all();

        $depoimentos = collect($request->input('depoimentos', []))
            ->filter(fn ($d) => !empty(trim((string) ($d['nome'] ?? ''))) && !empty(trim((string) ($d['texto'] ?? ''))))
            ->map(fn ($d) => [
                'nome' => trim((string) ($d['nome'] ?? '')),
                'cargo' => trim((string) ($d['cargo'] ?? 'Cliente')),
                'texto' => trim((string) ($d['texto'] ?? '')),
                'estrelas' => max(1, min(5, (int) ($d['estrelas'] ?? 5))),
            ])->values()->all();

        $faq = collect($request->input('faq', []))
            ->filter(fn ($f) => !empty(trim((string) ($f['pergunta'] ?? ''))) && !empty(trim((string) ($f['resposta'] ?? ''))))
            ->map(fn ($f) => [
                'pergunta' => trim((string) ($f['pergunta'] ?? '')),
                'resposta' => trim((string) ($f['resposta'] ?? '')),
            ])->values()->all();

        $logoPath = $landingPage->logo_path;
        if ($request->boolean('remover_logo') && $logoPath) {
            Storage::disk('public')->delete($logoPath);
            $logoPath = null;
        }
        if ($request->hasFile('logo')) {
            if ($logoPath) {
                Storage::disk('public')->delete($logoPath);
            }
            $logoPath = $request->file('logo')->store('landing-pages/logos', 'public');
        }

        $heroImagePath = $landingPage->hero_image_path;
        if ($request->boolean('remover_hero_image') && $heroImagePath) {
            Storage::disk('public')->delete($heroImagePath);
            $heroImagePath = null;
        }
        if ($request->hasFile('hero_image')) {
            if ($heroImagePath) {
                Storage::disk('public')->delete($heroImagePath);
            }
            $heroImagePath = $request->file('hero_image')->store('landing-pages/heroes', 'public');
        }

        $landingPage->update([
            'template' => $validated['template'],
            'slug' => $validated['slug'],
            'is_published' => $request->boolean('is_published'),
            'titulo_pagina' => $validated['titulo_pagina'],
            'meta_descricao' => $validated['meta_descricao'] ?? null,
            'cor_primaria' => $validated['cor_primaria'],
            'cor_secundaria' => $validated['cor_secundaria'],
            'logo_path' => $logoPath,
            'hero_image_path' => $heroImagePath,
            'hero_badge' => $validated['hero_badge'] ?? null,
            'hero_titulo' => $validated['hero_titulo'],
            'hero_subtitulo' => $validated['hero_subtitulo'] ?? null,
            'hero_cta_texto' => $validated['hero_cta_texto'],
            'sobre_titulo' => $validated['sobre_titulo'] ?? null,
            'sobre_texto' => $validated['sobre_texto'] ?? null,
            'servicos_json' => $servicos,
            'diferenciais_json' => $diferenciais,
            'depoimentos_json' => $depoimentos,
            'faq_json' => $faq,
            'whatsapp_numero' => $validated['whatsapp_numero'] ?? null,
            'telefone' => $validated['telefone'] ?? null,
            'email_contato' => $validated['email_contato'] ?? null,
            'endereco_texto' => $validated['endereco_texto'] ?? null,
            'horario_atendimento' => $validated['horario_atendimento'] ?? null,
            'instagram_url' => $validated['instagram_url'] ?? null,
            'facebook_url' => $validated['facebook_url'] ?? null,
            'google_maps_embed' => $validated['google_maps_embed'] ?? null,
        ]);

        return redirect()->route('customer.landing-page.edit')
            ->with('success', 'Sua Landing Page foi atualizada com sucesso!');
    }

    /**
     * Importa os itens do Catálogo (Produtos e Serviços) do cliente para a seção de serviços/vitrine da LP.
     */
    public function importCatalog()
    {
        $cliente = $this->resolveCliente();
        if (!$cliente) {
            return redirect()->route('customer.index')->with('error', 'Cliente não identificado.');
        }

        $landingPage = $this->getOrCreateLandingPage($cliente);
        $itens = ProdutoServico::where('cliente_id', $cliente->id)
            ->orderBy('nome')
            ->limit(18)
            ->get();

        if ($itens->isEmpty()) {
            return redirect()->route('customer.landing-page.edit')
                ->with('error', 'Você ainda não possui itens cadastrados em Produtos e Serviços para importar.');
        }

        $servicos = $itens->map(function (ProdutoServico $item) {
            $precoFormatado = $item->preco_padrao > 0
                ? 'R$ ' . number_format((float) $item->preco_padrao, 2, ',', '.')
                : '';

            return [
                'titulo' => $item->nome,
                'descricao' => $item->descricao ?: 'Entre em contato para mais detalhes e condições especiais.',
                'preco' => $precoFormatado,
                'destaque' => $item->tipo === 'servico' ? 'Serviço' : 'Produto',
            ];
        })->values()->all();

        $landingPage->update(['servicos_json' => $servicos]);

        return redirect()->route('customer.landing-page.edit')
            ->with('success', count($servicos) . ' itens foram importados do seu catálogo para a Landing Page!');
    }

    /**
     * Usa o Bruce IA para sugerir textos persuasivos completos para a Landing Page.
     */
    public function generateWithAi(Request $request, LlmDriver $llm)
    {
        $cliente = $this->resolveCliente();
        if (!$cliente) {
            return response()->json(['error' => 'Cliente não autenticado.'], 401);
        }

        $validated = $request->validate([
            'ramo' => 'required|string|max:400',
            'template' => 'nullable|string',
        ]);

        $nomeEmpresa = $cliente->nome_fantasia ?: ($cliente->razao_social ?: 'Nossa Empresa');
        $ramo = trim($validated['ramo']);

        $system = <<<TXT
Você é um copywriter especialista em Landing Pages de alta conversão no Brasil.
Gere textos persuasivos em português do Brasil para a empresa "{$nomeEmpresa}" que atua com: "{$ramo}".
Responda ESTRITAMENTE com um objeto JSON válido (sem blocos markdown ```json) contendo exatamente as chaves:
{
  "hero_badge": "Selo curto de até 4 palavras",
  "hero_titulo": "Headline forte e persuasiva de até 90 caracteres",
  "hero_subtitulo": "Subtítulo claro explicando a proposta de valor em até 200 caracteres",
  "hero_cta_texto": "Texto curto de ação do botão (ex: Solicitar Orçamento Agora)",
  "sobre_titulo": "Título da seção sobre a empresa",
  "sobre_texto": "Texto institucional persuasivo de 2 parágrafos curtos",
  "meta_descricao": "Descrição para SEO no Google com até 155 caracteres",
  "servicos": [
    {"titulo": "Nome do Serviço 1", "descricao": "Descrição curta do benefício", "preco": "", "destaque": "Destaque"},
    {"titulo": "Nome do Serviço 2", "descricao": "Descrição curta do benefício", "preco": "", "destaque": ""},
    {"titulo": "Nome do Serviço 3", "descricao": "Descrição curta do benefício", "preco": "", "destaque": ""}
  ],
  "diferenciais": [
    {"titulo": "Diferencial 1", "descricao": "Explicação curta"},
    {"titulo": "Diferencial 2", "descricao": "Explicação curta"},
    {"titulo": "Diferencial 3", "descricao": "Explicação curta"}
  ],
  "faq": [
    {"pergunta": "Pergunta frequente 1?", "resposta": "Resposta objetiva 1"},
    {"pergunta": "Pergunta frequente 2?", "resposta": "Resposta objetiva 2"},
    {"pergunta": "Pergunta frequente 3?", "resposta": "Resposta objetiva 3"}
  ]
}
TXT;

        try {
            $resp = $llm->complete(new PromptPayload($system, "Gere a copy JSON para a empresa {$nomeEmpresa} ({$ramo})."));
            $raw = trim($resp->content);
            $raw = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', $raw);
            $decoded = json_decode($raw, true);

            if (!is_array($decoded) || empty($decoded['hero_titulo'])) {
                return response()->json(['error' => 'Não foi possível interpretar a sugestão gerada pela IA. Tente novamente.'], 502);
            }

            return response()->json(['copy' => $decoded]);
        } catch (\Throwable $e) {
            Log::warning('[LandingPage] Falha ao gerar copy IA para cliente ' . $cliente->id . ': ' . $e->getMessage());
            return response()->json(['error' => 'O Bruce IA não conseguiu gerar os textos agora. Tente novamente em instantes.'], 502);
        }
    }
}
