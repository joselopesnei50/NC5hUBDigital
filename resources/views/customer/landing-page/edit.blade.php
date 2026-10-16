<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-xs font-bold text-bruce uppercase tracking-widest mb-1">Presença Digital</p>
                <h2 class="font-display font-bold text-3xl text-ink leading-tight">Meu Site / Landing Page</h2>
                <p class="text-slate text-sm mt-1">Escolha entre os 4 modelos profissionais, personalize seus conteúdos e receba leads direto no seu CRM.</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('public.landing-page.show', ['slug' => $landingPage->slug]) }}"
                   target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-white border border-black/10 hover:border-ink text-ink text-xs font-bold transition-colors shadow-sm">
                    <svg class="w-4 h-4 text-bruce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Visualizar Página
                </a>
                <button type="button"
                        x-data="{ copied: false }"
                        x-on:click="navigator.clipboard.writeText('{{ route('public.landing-page.show', ['slug' => $landingPage->slug]) }}'); copied = true; setTimeout(() => copied = false, 2000)"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-ink hover:bg-bruce text-white text-xs font-bold transition-colors shadow-sm">
                    <span x-text="copied ? '✓ Link Copiado!' : 'Copiar Link da Página'">Copiar Link da Página</span>
                </button>
            </div>
        </div>
    </x-slot>

    <style>[x-cloak]{display:none!important}</style>

    @php
        $servicosInit = old('servicos', $landingPage->servicos_json ?: []);
        $diferenciaisInit = old('diferenciais', $landingPage->diferenciais_json ?: []);
        $depoimentosInit = old('depoimentos', $landingPage->depoimentos_json ?: []);
        $faqInit = old('faq', $landingPage->faq_json ?: []);
    @endphp

    <div class="space-y-6"
         x-data="{
            tab: 'modelo',
            selectedTemplate: {{ json_encode(old('template', $landingPage->template)) }},
            corPrimaria: {{ json_encode(old('cor_primaria', $landingPage->cor_primaria ?: '#6366f1')) }},
            corSecundaria: {{ json_encode(old('cor_secundaria', $landingPage->cor_secundaria ?: '#0f172a')) }},
            servicos: {{ json_encode(array_values($servicosInit)) }},
            diferenciais: {{ json_encode(array_values($diferenciaisInit)) }},
            depoimentos: {{ json_encode(array_values($depoimentosInit)) }},
            faq: {{ json_encode(array_values($faqInit)) }},
            aiRamo: '',
            aiLoading: false,
            aiError: '',
            aiSuccess: ''
         }">

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-bold shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-sm">
                <p class="font-bold mb-1">Verifique os campos abaixo:</p>
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- KPIs da Landing Page --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-5 rounded-2xl bg-white border border-black/5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-slate">Status da Página</p>
                <div class="mt-2 flex items-center gap-2">
                    @if($landingPage->is_published)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-extrabold uppercase">
                            ● Publicada Online
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-extrabold uppercase">
                            ● Rascunho (Privado)
                        </span>
                    @endif
                </div>
                <p class="text-[11px] text-slate mt-2 truncate">/lp/{{ $landingPage->slug }}</p>
            </div>

            <div class="p-5 rounded-2xl bg-white border border-black/5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-slate">Visitas na Página</p>
                <p class="font-display text-3xl font-bold text-ink mt-1">{{ number_format($landingPage->views_count, 0, ',', '.') }}</p>
                <p class="text-[11px] text-slate mt-1">Acessos públicos acumulados</p>
            </div>

            <div class="p-5 rounded-2xl bg-white border border-black/5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-slate">Cliques no WhatsApp</p>
                <p class="font-display text-3xl font-bold text-emerald-600 mt-1">{{ number_format($landingPage->whatsapp_clicks_count, 0, ',', '.') }}</p>
                <p class="text-[11px] text-slate mt-1">Inícios de conversa direta</p>
            </div>

            <div class="p-5 rounded-2xl bg-white border border-black/5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-slate">Leads Capturados</p>
                <p class="font-display text-3xl font-bold text-bruce mt-1">{{ number_format($landingPage->leads_count, 0, ',', '.') }}</p>
                <p class="text-[11px] text-slate mt-1">Salvos automaticamente no seu CRM</p>
            </div>
        </div>

        {{-- Barra de Assistente Bruce IA + Importar Catálogo --}}
        <div class="p-5 rounded-3xl bg-white border border-black/5 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full bg-bruce/10 text-bruce text-[10px] font-extrabold uppercase tracking-wider">Bruce IA Copywriter</span>
                    <h3 class="font-display font-bold text-ink text-sm">Quer que o Bruce escreva os textos da sua página?</h3>
                </div>
                <p class="text-xs text-slate mb-3">Descreva seu ramo de atuação e diferenciais em uma frase e o Bruce preenche os títulos, sobre nós, serviços e FAQ para você revisar.</p>
                <div class="flex flex-col sm:flex-row gap-2">
                    <input type="text" x-model="aiRamo"
                           placeholder="Ex: Clínica odontológica especializada em implantes e estética em Campinas..."
                           class="flex-1 rounded-xl border-gray-300 focus:border-bruce focus:ring-bruce text-xs">
                    <button type="button"
                            x-bind:disabled="aiLoading || !aiRamo.trim()"
                            x-on:click="
                                aiLoading = true; aiError = ''; aiSuccess = '';
                                fetch('{{ route('customer.landing-page.ai-generate') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                    },
                                    body: JSON.stringify({ ramo: aiRamo, template: selectedTemplate })
                                })
                                .then(r => r.json().then(d => ({ok: r.ok, d})))
                                .then(({ok, d}) => {
                                    if (!ok || d.error) throw new Error(d.error || 'Falha ao gerar copy');
                                    const c = d.copy;
                                    if (c.hero_badge) document.querySelector('[name=hero_badge]').value = c.hero_badge;
                                    if (c.hero_titulo) document.querySelector('[name=hero_titulo]').value = c.hero_titulo;
                                    if (c.hero_subtitulo) document.querySelector('[name=hero_subtitulo]').value = c.hero_subtitulo;
                                    if (c.hero_cta_texto) document.querySelector('[name=hero_cta_texto]').value = c.hero_cta_texto;
                                    if (c.sobre_titulo) document.querySelector('[name=sobre_titulo]').value = c.sobre_titulo;
                                    if (c.sobre_texto) document.querySelector('[name=sobre_texto]').value = c.sobre_texto;
                                    if (c.meta_descricao) document.querySelector('[name=meta_descricao]').value = c.meta_descricao;
                                    if (Array.isArray(c.servicos) && c.servicos.length) servicos = c.servicos;
                                    if (Array.isArray(c.diferenciais) && c.diferenciais.length) diferenciais = c.diferenciais;
                                    if (Array.isArray(c.faq) && c.faq.length) faq = c.faq;
                                    aiSuccess = 'Textos preenchidos com sucesso! Revise nas abas abaixo e clique em Salvar Alterações.';
                                })
                                .catch(e => { aiError = e.message; })
                                .finally(() => { aiLoading = false; });
                            "
                            class="px-4 py-2 rounded-xl bg-bruce hover:bg-ink text-white text-xs font-bold transition-colors disabled:opacity-50 whitespace-nowrap">
                        <span x-text="aiLoading ? 'Gerando textos...' : '✨ Preencher com Bruce IA'"></span>
                    </button>
                </div>
                <p x-show="aiError" x-cloak x-text="aiError" class="text-xs text-rose-600 font-semibold mt-2"></p>
                <p x-show="aiSuccess" x-cloak x-text="aiSuccess" class="text-xs text-emerald-600 font-semibold mt-2"></p>
            </div>

            @if($catalogoCount > 0)
                <div class="border-t lg:border-t-0 lg:border-l border-black/10 pt-4 lg:pt-0 lg:pl-6 flex-shrink-0">
                    <p class="text-xs font-bold text-ink mb-1">Catálogo de Produtos/Serviços</p>
                    <p class="text-[11px] text-slate mb-2">Você tem {{ $catalogoCount }} item(ns) no catálogo.</p>
                    <form action="{{ route('customer.landing-page.import-catalog') }}" method="POST"
                          onsubmit="return confirm('Importar os itens do seu catálogo para a lista de serviços da Landing Page?');">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-xl bg-mist hover:bg-ink hover:text-white text-ink text-xs font-bold border border-black/10 transition-colors">
                            Importar do Catálogo ({{ $catalogoCount }})
                        </button>
                    </form>
                </div>
            @endif
        </div>

        {{-- Formulário Principal da Landing Page --}}
        <form action="{{ route('customer.landing-page.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Navegação por Abas --}}
            <div class="flex items-center gap-2 overflow-x-auto pb-1">
                <button type="button" x-on:click="tab = 'modelo'"
                        :class="tab === 'modelo' ? 'bg-ink text-white' : 'bg-white text-slate hover:text-ink border border-black/5'"
                        class="px-4 py-2.5 rounded-full text-xs font-bold transition-colors whitespace-nowrap">
                    1. Modelo & Identidade
                </button>
                <button type="button" x-on:click="tab = 'hero'"
                        :class="tab === 'hero' ? 'bg-ink text-white' : 'bg-white text-slate hover:text-ink border border-black/5'"
                        class="px-4 py-2.5 rounded-full text-xs font-bold transition-colors whitespace-nowrap">
                    2. Topo (Hero) & Sobre Nós
                </button>
                <button type="button" x-on:click="tab = 'servicos'"
                        :class="tab === 'servicos' ? 'bg-ink text-white' : 'bg-white text-slate hover:text-ink border border-black/5'"
                        class="px-4 py-2.5 rounded-full text-xs font-bold transition-colors whitespace-nowrap">
                    3. Serviços / Vitrine & Diferenciais
                </button>
                <button type="button" x-on:click="tab = 'prova'"
                        :class="tab === 'prova' ? 'bg-ink text-white' : 'bg-white text-slate hover:text-ink border border-black/5'"
                        class="px-4 py-2.5 rounded-full text-xs font-bold transition-colors whitespace-nowrap">
                    4. Depoimentos & FAQ
                </button>
                <button type="button" x-on:click="tab = 'contato'"
                        :class="tab === 'contato' ? 'bg-ink text-white' : 'bg-white text-slate hover:text-ink border border-black/5'"
                        class="px-4 py-2.5 rounded-full text-xs font-bold transition-colors whitespace-nowrap">
                    5. Contatos, WhatsApp & SEO
                </button>
            </div>

            {{-- ABA 1: Escolha entre os 4 Modelos & Identidade Visual --}}
            <div x-show="tab === 'modelo'" class="bg-white rounded-3xl p-6 sm:p-8 border border-black/5 shadow-sm space-y-8">
                <div>
                    <h3 class="font-display text-xl font-bold text-ink mb-1">Escolha um dos 4 Modelos</h3>
                    <p class="text-xs text-slate mb-5">Você pode alternar de modelo a qualquer momento sem perder as informações preenchidas.</p>

                    <input type="hidden" name="template" x-model="selectedTemplate">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($templates as $key => $tpl)
                            <div x-on:click="selectedTemplate = '{{ $key }}'"
                                 :class="selectedTemplate === '{{ $key }}' ? 'ring-2 ring-bruce border-bruce bg-bruce/5' : 'border-black/10 hover:border-black/25 bg-white'"
                                 class="cursor-pointer rounded-2xl border p-5 transition-all flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between gap-2 mb-2">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-mist text-ink">
                                            {{ $tpl['badge'] }}
                                        </span>
                                        <span x-show="selectedTemplate === '{{ $key }}'" class="text-xs font-extrabold text-bruce">✓ Modelo Selecionado</span>
                                    </div>
                                    <h4 class="font-display font-bold text-base text-ink">{{ $tpl['nome'] }}</h4>
                                    <p class="text-xs font-semibold text-slate mt-0.5">{{ $tpl['subtitulo'] }}</p>
                                    <p class="text-xs text-slate/80 mt-2 leading-relaxed">{{ $tpl['descricao'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <hr class="border-black/5">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Nome da Empresa / Título *</label>
                        <input type="text" name="titulo_pagina" value="{{ old('titulo_pagina', $landingPage->titulo_pagina) }}" required
                               class="w-full rounded-xl border-gray-300 focus:border-bruce focus:ring-bruce text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Endereço da Página (Slug) *</label>
                        <div class="flex items-center rounded-xl border border-gray-300 overflow-hidden bg-mist">
                            <span class="px-3 text-xs text-slate font-mono select-none">/lp/</span>
                            <input type="text" name="slug" value="{{ old('slug', $landingPage->slug) }}" required
                                   class="w-full border-0 focus:ring-0 text-sm bg-white py-2 px-3">
                        </div>
                    </div>

                    <div class="flex items-center pt-5">
                        <label class="inline-flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_published" value="1"
                                   @checked(old('is_published', $landingPage->is_published))
                                   class="rounded border-gray-300 text-bruce focus:ring-bruce w-5 h-5">
                            <div>
                                <span class="block text-sm font-bold text-ink">Publicar Landing Page Online</span>
                                <span class="block text-[11px] text-slate">Desmarque para manter em modo rascunho privado</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Cor Principal (Botões e Destaques)</label>
                        <div class="flex items-center gap-3">
                            <input type="color" x-model="corPrimaria" class="h-10 w-14 rounded-lg border border-gray-300 cursor-pointer">
                            <input type="text" name="cor_primaria" x-model="corPrimaria" class="flex-1 rounded-xl border-gray-300 text-sm font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Cor Secundária (Fundos Escuros)</label>
                        <div class="flex items-center gap-3">
                            <input type="color" x-model="corSecundaria" class="h-10 w-14 rounded-lg border border-gray-300 cursor-pointer">
                            <input type="text" name="cor_secundaria" x-model="corSecundaria" class="flex-1 rounded-xl border-gray-300 text-sm font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Logotipo (Opcional)</label>
                        <input type="file" name="logo" accept="image/*" class="w-full text-xs text-slate">
                        @if($landingPage->logo_path)
                            <div class="mt-2 flex items-center gap-3">
                                <img src="{{ asset('storage/' . $landingPage->logo_path) }}" alt="Logo" class="h-8 w-auto rounded border">
                                <label class="text-[11px] text-rose-600 font-bold flex items-center gap-1 cursor-pointer">
                                    <input type="checkbox" name="remover_logo" value="1" class="rounded border-gray-300 text-rose-600">
                                    Remover logo
                                </label>
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Imagem de Capa / Hero (Opcional)</label>
                        <input type="file" name="hero_image" accept="image/*" class="w-full text-xs text-slate">
                        @if($landingPage->hero_image_path)
                            <div class="mt-2 flex items-center gap-3">
                                <img src="{{ asset('storage/' . $landingPage->hero_image_path) }}" alt="Hero" class="h-8 w-12 object-cover rounded border">
                                <label class="text-[11px] text-rose-600 font-bold flex items-center gap-1 cursor-pointer">
                                    <input type="checkbox" name="remover_hero_image" value="1" class="rounded border-gray-300 text-rose-600">
                                    Remover capa
                                </label>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ABA 2: Topo (Hero) & Sobre Nós --}}
            <div x-show="tab === 'hero'" x-cloak class="bg-white rounded-3xl p-6 sm:p-8 border border-black/5 shadow-sm space-y-6">
                <h3 class="font-display text-xl font-bold text-ink">Topo da Página (Hero) & Apresentação</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Selo Superior (Badge)</label>
                        <input type="text" name="hero_badge" value="{{ old('hero_badge', $landingPage->hero_badge) }}"
                               placeholder="Ex: Atendimento em toda a região"
                               class="w-full rounded-xl border-gray-300 focus:border-bruce focus:ring-bruce text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Texto do Botão Principal (CTA) *</label>
                        <input type="text" name="hero_cta_texto" value="{{ old('hero_cta_texto', $landingPage->hero_cta_texto) }}" required
                               placeholder="Ex: Solicitar Orçamento no WhatsApp"
                               class="w-full rounded-xl border-gray-300 focus:border-bruce focus:ring-bruce text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Título Principal de Impacto (Headline) *</label>
                    <input type="text" name="hero_titulo" value="{{ old('hero_titulo', $landingPage->hero_titulo) }}" required
                           class="w-full rounded-xl border-gray-300 focus:border-bruce focus:ring-bruce text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Subtítulo Explicativo</label>
                    <textarea name="hero_subtitulo" rows="3"
                              class="w-full rounded-xl border-gray-300 focus:border-bruce focus:ring-bruce text-sm">{{ old('hero_subtitulo', $landingPage->hero_subtitulo) }}</textarea>
                </div>

                <hr class="border-black/5">

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Título da Seção "Sobre Nós"</label>
                    <input type="text" name="sobre_titulo" value="{{ old('sobre_titulo', $landingPage->sobre_titulo) }}"
                           class="w-full rounded-xl border-gray-300 focus:border-bruce focus:ring-bruce text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Texto "Sobre Nós" / História e Compromisso</label>
                    <textarea name="sobre_texto" rows="5"
                              class="w-full rounded-xl border-gray-300 focus:border-bruce focus:ring-bruce text-sm">{{ old('sobre_texto', $landingPage->sobre_texto) }}</textarea>
                </div>
            </div>

            {{-- ABA 3: Serviços / Vitrine & Diferenciais --}}
            <div x-show="tab === 'servicos'" x-cloak class="bg-white rounded-3xl p-6 sm:p-8 border border-black/5 shadow-sm space-y-8">
                <div>
                    <div class="flex items-center justify-between gap-4 mb-4">
                        <div>
                            <h3 class="font-display text-xl font-bold text-ink">Serviços ou Produtos da Vitrine</h3>
                            <p class="text-xs text-slate">Estes itens aparecem nos cards de soluções ou no catálogo com botão direto para o WhatsApp.</p>
                        </div>
                        <button type="button"
                                x-on:click="servicos.push({ titulo: '', descricao: '', preco: '', destaque: '' })"
                                class="px-4 py-2 rounded-full bg-ink hover:bg-bruce text-white text-xs font-bold transition-colors">
                            + Adicionar Item
                        </button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(serv, idx) in servicos" :key="idx">
                            <div class="p-4 rounded-2xl bg-mist border border-black/5 grid grid-cols-1 md:grid-cols-12 gap-3 items-start">
                                <div class="md:col-span-4">
                                    <label class="block text-[10px] font-bold uppercase text-slate mb-1">Nome do Serviço / Produto</label>
                                    <input type="text" :name="`servicos[${idx}][titulo]`" x-model="serv.titulo"
                                           placeholder="Ex: Consultoria Tributária"
                                           class="w-full rounded-xl border-gray-300 text-xs">
                                </div>
                                <div class="md:col-span-4">
                                    <label class="block text-[10px] font-bold uppercase text-slate mb-1">Descrição Curta</label>
                                    <input type="text" :name="`servicos[${idx}][descricao]`" x-model="serv.descricao"
                                           placeholder="Benefício ou detalhes do item"
                                           class="w-full rounded-xl border-gray-300 text-xs">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-bold uppercase text-slate mb-1">Preço (Opcional)</label>
                                    <input type="text" :name="`servicos[${idx}][preco]`" x-model="serv.preco"
                                           placeholder="Ex: R$ 150,00"
                                           class="w-full rounded-xl border-gray-300 text-xs">
                                </div>
                                <div class="md:col-span-2 flex items-end gap-2">
                                    <div class="flex-1">
                                        <label class="block text-[10px] font-bold uppercase text-slate mb-1">Selo</label>
                                        <input type="text" :name="`servicos[${idx}][destaque]`" x-model="serv.destaque"
                                               placeholder="Destaque"
                                               class="w-full rounded-xl border-gray-300 text-xs">
                                    </div>
                                    <button type="button" x-on:click="servicos.splice(idx, 1)"
                                            class="p-2 text-rose-600 hover:bg-rose-50 rounded-xl text-xs font-bold">
                                        ✕
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <hr class="border-black/5">

                <div>
                    <div class="flex items-center justify-between gap-4 mb-4">
                        <div>
                            <h3 class="font-display text-lg font-bold text-ink">Diferenciais Competitivos</h3>
                            <p class="text-xs text-slate">Destaques rápidos exibidos no topo da página.</p>
                        </div>
                        <button type="button"
                                x-on:click="diferenciais.push({ titulo: '', descricao: '' })"
                                class="px-4 py-2 rounded-full bg-mist hover:bg-ink hover:text-white text-ink text-xs font-bold transition-colors">
                            + Adicionar Diferencial
                        </button>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(dif, idx) in diferenciais" :key="idx">
                            <div class="p-4 rounded-2xl bg-mist border border-black/5 grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
                                <div class="md:col-span-5">
                                    <input type="text" :name="`diferenciais[${idx}][titulo]`" x-model="dif.titulo"
                                           placeholder="Título do diferencial (ex: Atendimento 24h)"
                                           class="w-full rounded-xl border-gray-300 text-xs">
                                </div>
                                <div class="md:col-span-6">
                                    <input type="text" :name="`diferenciais[${idx}][descricao]`" x-model="dif.descricao"
                                           placeholder="Explicação curta"
                                           class="w-full rounded-xl border-gray-300 text-xs">
                                </div>
                                <div class="md:col-span-1 text-right">
                                    <button type="button" x-on:click="diferenciais.splice(idx, 1)"
                                            class="p-2 text-rose-600 hover:bg-rose-50 rounded-xl text-xs font-bold">
                                        ✕
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- ABA 4: Depoimentos & FAQ --}}
            <div x-show="tab === 'prova'" x-cloak class="bg-white rounded-3xl p-6 sm:p-8 border border-black/5 shadow-sm space-y-8">
                <div>
                    <div class="flex items-center justify-between gap-4 mb-4">
                        <div>
                            <h3 class="font-display text-xl font-bold text-ink">Depoimentos de Clientes</h3>
                            <p class="text-xs text-slate">Avaliações positivas ajudam a aumentar a taxa de conversão da página.</p>
                        </div>
                        <button type="button"
                                x-on:click="depoimentos.push({ nome: '', cargo: 'Cliente', texto: '', estrelas: 5 })"
                                class="px-4 py-2 rounded-full bg-ink hover:bg-bruce text-white text-xs font-bold transition-colors">
                            + Adicionar Depoimento
                        </button>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(dep, idx) in depoimentos" :key="idx">
                            <div class="p-4 rounded-2xl bg-mist border border-black/5 grid grid-cols-1 md:grid-cols-12 gap-3 items-start">
                                <div class="md:col-span-3">
                                    <label class="block text-[10px] font-bold uppercase text-slate mb-1">Nome do Cliente</label>
                                    <input type="text" :name="`depoimentos[${idx}][nome]`" x-model="dep.nome"
                                           class="w-full rounded-xl border-gray-300 text-xs">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-bold uppercase text-slate mb-1">Cidade / Cargo</label>
                                    <input type="text" :name="`depoimentos[${idx}][cargo]`" x-model="dep.cargo"
                                           class="w-full rounded-xl border-gray-300 text-xs">
                                </div>
                                <div class="md:col-span-5">
                                    <label class="block text-[10px] font-bold uppercase text-slate mb-1">Comentário</label>
                                    <input type="text" :name="`depoimentos[${idx}][texto]`" x-model="dep.texto"
                                           class="w-full rounded-xl border-gray-300 text-xs">
                                </div>
                                <div class="md:col-span-2 flex items-end gap-2">
                                    <div class="flex-1">
                                        <label class="block text-[10px] font-bold uppercase text-slate mb-1">Estrelas</label>
                                        <select :name="`depoimentos[${idx}][estrelas]`" x-model="dep.estrelas"
                                                class="w-full rounded-xl border-gray-300 text-xs">
                                            <option value="5">5 ★</option>
                                            <option value="4">4 ★</option>
                                        </select>
                                    </div>
                                    <button type="button" x-on:click="depoimentos.splice(idx, 1)"
                                            class="p-2 text-rose-600 hover:bg-rose-50 rounded-xl text-xs font-bold">
                                        ✕
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <hr class="border-black/5">

                <div>
                    <div class="flex items-center justify-between gap-4 mb-4">
                        <div>
                            <h3 class="font-display text-lg font-bold text-ink">Perguntas Frequentes (FAQ)</h3>
                            <p class="text-xs text-slate">Responda às principais dúvidas antes mesmo do cliente entrar em contato.</p>
                        </div>
                        <button type="button"
                                x-on:click="faq.push({ pergunta: '', resposta: '' })"
                                class="px-4 py-2 rounded-full bg-mist hover:bg-ink hover:text-white text-ink text-xs font-bold transition-colors">
                            + Adicionar Pergunta
                        </button>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(item, idx) in faq" :key="idx">
                            <div class="p-4 rounded-2xl bg-mist border border-black/5 grid grid-cols-1 md:grid-cols-12 gap-3 items-start">
                                <div class="md:col-span-5">
                                    <input type="text" :name="`faq[${idx}][pergunta]`" x-model="item.pergunta"
                                           placeholder="Pergunta frequente..."
                                           class="w-full rounded-xl border-gray-300 text-xs">
                                </div>
                                <div class="md:col-span-6">
                                    <input type="text" :name="`faq[${idx}][resposta]`" x-model="item.resposta"
                                           placeholder="Resposta clara e objetiva..."
                                           class="w-full rounded-xl border-gray-300 text-xs">
                                </div>
                                <div class="md:col-span-1 text-right">
                                    <button type="button" x-on:click="faq.splice(idx, 1)"
                                            class="p-2 text-rose-600 hover:bg-rose-50 rounded-xl text-xs font-bold">
                                        ✕
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- ABA 5: Contatos, WhatsApp & SEO --}}
            <div x-show="tab === 'contato'" x-cloak class="bg-white rounded-3xl p-6 sm:p-8 border border-black/5 shadow-sm space-y-6">
                <h3 class="font-display text-xl font-bold text-ink">Canais de Atendimento, Redes Sociais & SEO</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">WhatsApp para Botões (Com DDD)</label>
                        <input type="text" name="whatsapp_numero" value="{{ old('whatsapp_numero', $landingPage->whatsapp_numero) }}"
                               placeholder="Ex: (11) 99999-9999"
                               class="w-full rounded-xl border-gray-300 focus:border-bruce focus:ring-bruce text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Telefone Exibido na Página</label>
                        <input type="text" name="telefone" value="{{ old('telefone', $landingPage->telefone) }}"
                               placeholder="Ex: (11) 3333-4444"
                               class="w-full rounded-xl border-gray-300 focus:border-bruce focus:ring-bruce text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">E-mail de Contato</label>
                        <input type="email" name="email_contato" value="{{ old('email_contato', $landingPage->email_contato) }}"
                               placeholder="contato@suaempresa.com.br"
                               class="w-full rounded-xl border-gray-300 focus:border-bruce focus:ring-bruce text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Endereço Físico</label>
                        <input type="text" name="endereco_texto" value="{{ old('endereco_texto', $landingPage->endereco_texto) }}"
                               placeholder="Rua Exemplo, 123 - Centro, Cidade/UF"
                               class="w-full rounded-xl border-gray-300 focus:border-bruce focus:ring-bruce text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Horário de Funcionamento</label>
                        <input type="text" name="horario_atendimento" value="{{ old('horario_atendimento', $landingPage->horario_atendimento) }}"
                               placeholder="Segunda a Sexta, das 08h às 18h"
                               class="w-full rounded-xl border-gray-300 focus:border-bruce focus:ring-bruce text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Link do Instagram (Opcional)</label>
                        <input type="url" name="instagram_url" value="{{ old('instagram_url', $landingPage->instagram_url) }}"
                               placeholder="https://instagram.com/suaempresa"
                               class="w-full rounded-xl border-gray-300 focus:border-bruce focus:ring-bruce text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Link do Facebook (Opcional)</label>
                        <input type="url" name="facebook_url" value="{{ old('facebook_url', $landingPage->facebook_url) }}"
                               placeholder="https://facebook.com/suaempresa"
                               class="w-full rounded-xl border-gray-300 focus:border-bruce focus:ring-bruce text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Descrição para Google & WhatsApp (Meta Description)</label>
                    <textarea name="meta_descricao" rows="2" maxlength="500"
                              placeholder="Resumo que aparece no Google e ao compartilhar o link no WhatsApp..."
                              class="w-full rounded-xl border-gray-300 focus:border-bruce focus:ring-bruce text-sm">{{ old('meta_descricao', $landingPage->meta_descricao) }}</textarea>
                </div>
            </div>

            {{-- Barra Fixa de Salvar --}}
            <div class="flex items-center justify-between gap-4 bg-white rounded-3xl p-5 border border-black/5 shadow-sm">
                <div class="text-xs text-slate">
                    Link público: <a href="{{ route('public.landing-page.show', ['slug' => $landingPage->slug]) }}" target="_blank" class="font-bold text-bruce hover:underline">{{ route('public.landing-page.show', ['slug' => $landingPage->slug]) }}</a>
                </div>
                <button type="submit" class="px-8 py-3.5 rounded-full bg-ink hover:bg-bruce text-white font-bold text-sm shadow-md transition-colors">
                    Salvar Alterações da Landing Page
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
