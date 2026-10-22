@php
    $indexRoute       = $routePrefix . 'index';
    $connectRoute     = $routePrefix . 'connect';
    $disconnectRoute  = $routePrefix . 'accounts.disconnect';
    $destroyAccRoute  = $routePrefix . 'accounts.destroy';
    $storePostRoute   = $routePrefix . 'posts.store';
    $retryPostRoute   = $routePrefix . 'posts.retry';
    $destroyPostRoute = $routePrefix . 'posts.destroy';
    $captionAiRoute   = $routePrefix . 'posts.generate-caption';

    $defaultTab = 'studio';
    if (request()->has('status') || request()->query('tab') === 'posts') {
        $defaultTab = 'posts';
    } elseif ($accounts->isEmpty() || request()->query('tab') === 'accounts') {
        $defaultTab = 'accounts';
    }

    $accountsPayload = $activeAccounts->map(fn ($a) => [
        'id'         => (string) $a->id,
        'page_name'  => $a->page_name,
        'picture'    => $a->page_picture,
        'ig_user'    => $a->instagram_username,
        'has_ig'     => (bool) $a->instagram_business_id,
    ])->values();
@endphp

<div class="space-y-6" x-data="socialMediaStudio()">

    {{-- Avisos de Configuração ou Validação --}}
    @if(!$metaConfigured)
        <div class="bg-amber-50 border border-amber-200 text-amber-900 px-5 py-4 rounded-2xl text-sm flex items-center justify-between gap-4">
            <div>
                <strong>Configuração do Meta App pendente:</strong> Preencha o <code>Meta App ID</code> e o <code>Meta App Secret</code> em <strong>Configurações Globais</strong> para liberar o login com Facebook e Instagram.
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl text-sm">
            <p class="font-bold mb-1">Verifique os detalhes da publicação:</p>
            <ul class="list-disc pl-5 space-y-0.5 text-xs">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- =====================================================================
         BARRA SUPERIOR COMPACTA: NAVEGAÇÃO EM ABAS + STATUS DAS CONTAS
         ===================================================================== --}}
    <div class="bg-white border border-slate-200/80 rounded-2xl p-3 sm:p-4 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">

        {{-- Abas Principais --}}
        <div class="flex flex-wrap items-center gap-1.5 bg-slate-100/90 p-1.5 rounded-xl">
            <button type="button" @click="activeTab = 'studio'"
                    :class="activeTab === 'studio' ? 'bg-[#0A1128] text-white shadow-sm' : 'text-slate-600 hover:text-[#0A1128]'"
                    class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                <svg class="w-4 h-4 text-[#FF7A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Novo Post
            </button>

            <button type="button" @click="activeTab = 'posts'"
                    :class="activeTab === 'posts' ? 'bg-[#0A1128] text-white shadow-sm' : 'text-slate-600 hover:text-[#0A1128]'"
                    class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Fila &amp; Histórico
                <span class="px-1.5 py-0.5 text-[10px] rounded-md"
                      :class="activeTab === 'posts' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'">
                    {{ $counts['all'] ?? 0 }}
                </span>
            </button>

            <button type="button" @click="activeTab = 'accounts'"
                    :class="activeTab === 'accounts' ? 'bg-[#0A1128] text-white shadow-sm' : 'text-slate-600 hover:text-[#0A1128]'"
                    class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                Contas Conectadas
                <span class="px-1.5 py-0.5 text-[10px] rounded-md"
                      :class="activeTab === 'accounts' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800'">
                    {{ $activeAccounts->count() }}
                </span>
            </button>
        </div>

        {{-- Resumo Rápido das Contas + Botão Conectar --}}
        <div class="flex items-center justify-between sm:justify-end gap-3">
            @if($activeAccounts->isNotEmpty())
                <div class="flex items-center gap-2 overflow-x-auto py-0.5">
                    @foreach($activeAccounts->take(2) as $acc)
                        <button type="button" @click="activeTab = 'accounts'"
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200/80 text-xs transition-colors">
                            @if($acc->page_picture)
                                <img src="{{ $acc->page_picture }}" alt="" class="w-5 h-5 rounded-full object-cover">
                            @else
                                <span class="w-5 h-5 rounded-full bg-[#1877F2] text-white text-[10px] font-bold flex items-center justify-center">{{ mb_substr($acc->page_name, 0, 1) }}</span>
                            @endif
                            <span class="font-bold text-[#0A1128] truncate max-w-[130px]">{{ $acc->page_name }}</span>
                            @if($acc->instagram_username)
                                <span class="text-[11px] font-semibold text-pink-600">{{ '@' . $acc->instagram_username }}</span>
                            @endif
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        </button>
                    @endforeach
                    @if($activeAccounts->count() > 2)
                        <button type="button" @click="activeTab = 'accounts'" class="text-xs font-bold text-slate-500 hover:text-[#0A1128] px-2">
                            +{{ $activeAccounts->count() - 2 }}
                        </button>
                    @endif
                </div>
            @endif

            <a href="{{ route($connectRoute) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#1877F2] hover:bg-blue-700 text-white rounded-xl font-bold text-xs shadow-sm transition-colors whitespace-nowrap">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                {{ $accounts->isEmpty() ? 'Conectar Facebook & Instagram' : '+ Conectar Conta' }}
            </a>
        </div>
    </div>

    {{-- =====================================================================
         ABA 1: ESTÚDIO DE CRIAÇÃO + PREVIEW EM TEMPO REAL
         ===================================================================== --}}
    <div x-show="activeTab === 'studio'" x-cloak>
        <form method="POST" action="{{ route($storePostRoute) }}" enctype="multipart/form-data" id="socialPostForm"
              class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            @csrf

            {{-- COLUNA ESQUERDA: PASSO A PASSO DE CRIAÇÃO (7 COLUNAS) --}}
            <div class="lg:col-span-7 bg-white border border-slate-200/80 rounded-3xl p-6 sm:p-7 shadow-sm space-y-6">

                {{-- Passo 1: Conta e Destino --}}
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-display font-bold text-lg text-[#0A1128]">1. Destino e Formato</h3>
                        @if($activeAccounts->isEmpty())
                            <button type="button" @click="activeTab = 'accounts'" class="text-xs font-bold text-[#1877F2] hover:underline">
                                Conectar conta primeiro →
                            </button>
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Qual conta vai publicar?</label>
                        <select name="social_account_id" x-model="selectedAccountId" @change="onAccountChange()"
                                class="w-full rounded-xl border-slate-300 text-sm font-semibold text-[#0A1128] focus:border-[#0A1128] focus:ring-[#0A1128]">
                            @forelse($activeAccounts as $acc)
                                <option value="{{ $acc->id }}">
                                    {{ $acc->page_name }} {{ $acc->instagram_username ? '· @' . $acc->instagram_username . ' (Instagram + Facebook)' : '· (Somente Facebook)' }}
                                </option>
                            @empty
                                <option value="">— Nenhuma conta ativa (Salvar como Rascunho) —</option>
                            @endforelse
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                        {{-- Seletor Visual de Rede --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">Publicar em</label>
                            <input type="hidden" name="platform" :value="platform">
                            <div class="grid grid-cols-3 gap-1.5 bg-slate-100 p-1 rounded-xl">
                                <button type="button" @click="setPlatform('both')"
                                        :disabled="format === 'story' || !currentAccountHasIg()"
                                        :class="platform === 'both' ? 'bg-white text-[#0A1128] shadow-sm font-bold' : 'text-slate-600 hover:text-[#0A1128] disabled:opacity-40'"
                                        class="py-2 px-2 rounded-lg text-xs transition-all text-center">
                                    Ambos
                                </button>
                                <button type="button" @click="setPlatform('instagram')"
                                        :disabled="!currentAccountHasIg()"
                                        :class="platform === 'instagram' ? 'bg-white text-pink-600 shadow-sm font-bold' : 'text-slate-600 hover:text-[#0A1128] disabled:opacity-40'"
                                        class="py-2 px-2 rounded-lg text-xs transition-all text-center">
                                    Instagram
                                </button>
                                <button type="button" @click="setPlatform('facebook')"
                                        :disabled="format === 'story'"
                                        :class="platform === 'facebook' ? 'bg-white text-[#1877F2] shadow-sm font-bold' : 'text-slate-600 hover:text-[#0A1128] disabled:opacity-40'"
                                        class="py-2 px-2 rounded-lg text-xs transition-all text-center">
                                    Facebook
                                </button>
                            </div>
                        </div>

                        {{-- Seletor Visual de Formato --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">Formato do Post</label>
                            <input type="hidden" name="format" :value="format">
                            <div class="grid grid-cols-2 gap-1.5 bg-slate-100 p-1 rounded-xl">
                                <button type="button" @click="setFormat('feed')"
                                        :class="format === 'feed' ? 'bg-white text-[#0A1128] shadow-sm font-bold' : 'text-slate-600 hover:text-[#0A1128]'"
                                        class="py-2 px-3 rounded-lg text-xs transition-all text-center">
                                    Feed / Reels / Carrossel
                                </button>
                                <button type="button" @click="setFormat('story')"
                                        :disabled="!currentAccountHasIg()"
                                        :class="format === 'story' ? 'bg-white text-pink-600 shadow-sm font-bold' : 'text-slate-600 hover:text-[#0A1128] disabled:opacity-40'"
                                        class="py-2 px-3 rounded-lg text-xs transition-all text-center">
                                    Story 24h (IG)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="border-slate-100">

                {{-- Passo 2: Mídia com Miniaturas e Drag-and-Drop --}}
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-display font-bold text-lg text-[#0A1128]">2. Fotos ou Vídeos</h3>
                            <p class="text-xs text-slate-500">1 foto/vídeo para post único ou Reels · 2 a 10 mídias para Carrossel no Instagram.</p>
                        </div>
                        <span x-show="mediaFiles.length >= 2"
                              class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-pink-50 text-pink-700 border border-pink-200">
                            Carrossel (<span x-text="mediaFiles.length"></span> itens)
                        </span>
                    </div>

                    <label for="studioMediaInput"
                           class="flex flex-col items-center justify-center border-2 border-dashed border-slate-300 hover:border-[#FF7A1A] bg-slate-50/70 hover:bg-orange-50/20 rounded-2xl p-5 cursor-pointer transition-all text-center">
                        <svg class="w-7 h-7 text-slate-400 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="text-xs font-bold text-[#0A1128]">Clique para adicionar fotos ou vídeos (MP4)</span>
                        <span class="text-[11px] text-slate-500 mt-0.5">JPG, PNG, WEBP ou MP4 até 50MB · Até 10 arquivos</span>
                        <input type="file" id="studioMediaInput" name="media_files[]" multiple
                               accept="image/jpeg,image/png,image/webp,video/mp4"
                               class="hidden" @change="handleMediaSelect($event)">
                    </label>

                    {{-- Grade de Miniaturas Selecionadas --}}
                    <div x-show="mediaFiles.length > 0" class="grid grid-cols-3 sm:grid-cols-5 gap-2.5 pt-1">
                        <template x-for="(item, idx) in mediaFiles" :key="idx">
                            <div class="relative group aspect-square rounded-xl overflow-hidden border border-slate-200 bg-slate-900">
                                <template x-if="item.isVideo">
                                    <video :src="item.url" class="w-full h-full object-cover" muted></video>
                                </template>
                                <template x-if="!item.isVideo">
                                    <img :src="item.url" class="w-full h-full object-cover" alt="">
                                </template>
                                <span class="absolute bottom-1.5 left-1.5 bg-black/75 text-white text-[10px] font-bold px-1.5 py-0.5 rounded" x-text="idx + 1"></span>
                                <button type="button" @click="removeMedia(idx)"
                                        class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-rose-600 text-white text-xs font-bold flex items-center justify-center shadow hover:bg-rose-700"
                                        title="Remover mídia">
                                    &times;
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <hr class="border-slate-100">

                {{-- Passo 3: Legenda + Assistente BruceIA --}}
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="font-display font-bold text-lg text-[#0A1128]">3. Legenda da Publicação</h3>
                        <button type="button" @click="showAiBox = !showAiBox"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-orange-50 text-[#FF7A1A] hover:bg-orange-100 border border-orange-200 transition-colors">
                            <span>✨ Escrever com BruceIA</span>
                        </button>
                    </div>

                    {{-- Painel Expansível BruceIA --}}
                    <div x-show="showAiBox" x-transition class="bg-slate-50 border border-slate-200 rounded-2xl p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-[#0A1128]">Sobre o que você quer falar neste post?</span>
                            <span class="text-[10px] font-bold text-slate-500">Conectado ao DNA da empresa</span>
                        </div>

                        {{-- Sugestões rápidas em 1 clique --}}
                        <div class="flex flex-wrap gap-1.5">
                            @foreach(['Oferta especial da semana', 'Bastidores e rotina', 'Dica prática para clientes', 'Diferencial do nosso atendimento'] as $sugestao)
                                <button type="button" @click="aiTopic = '{{ $sugestao }}'"
                                        class="text-[11px] font-semibold px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 hover:border-[#FF7A1A] hover:text-[#0A1128] transition-colors">
                                    + {{ $sugestao }}
                                </button>
                            @endforeach
                        </div>

                        <div class="flex flex-col sm:flex-row gap-2">
                            <input type="text" x-model="aiTopic"
                                   @keydown.enter.prevent="generateCaptionWithAi()"
                                   placeholder="Descreva o tema, produto ou novidade..."
                                   class="flex-1 rounded-xl border-slate-300 text-xs focus:border-[#0A1128] focus:ring-[#0A1128]">
                            <button type="button" @click="generateCaptionWithAi()" :disabled="aiLoading"
                                    class="px-4 py-2.5 bg-[#0A1128] hover:bg-[#FF7A1A] text-white rounded-xl text-xs font-bold transition-colors whitespace-nowrap disabled:opacity-50">
                                <span x-show="!aiLoading">Gerar Legenda</span>
                                <span x-show="aiLoading">Criando...</span>
                            </button>
                        </div>
                        <p x-show="aiError" x-text="aiError" class="text-xs text-rose-600 font-semibold"></p>
                    </div>

                    <div>
                        <textarea name="caption" x-model="caption" rows="5" maxlength="2200" required
                                  placeholder="Digite a legenda do post (com emojis e hashtags) ou clique em 'Escrever com BruceIA'..."
                                  class="w-full rounded-2xl border-slate-300 text-sm focus:border-[#0A1128] focus:ring-[#0A1128] leading-relaxed">{{ old('caption') }}</textarea>
                        <div class="flex justify-end mt-1">
                            <span class="text-[11px] text-slate-400 font-medium" x-text="(caption ? caption.length : 0) + ' / 2200'"></span>
                        </div>
                    </div>
                </div>

                <hr class="border-slate-100">

                {{-- Passo 4: Quando Publicar & Botão Final --}}
                <div class="space-y-4">
                    <h3 class="font-display font-bold text-lg text-[#0A1128]">4. Quando publicar?</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 p-3.5 rounded-2xl border cursor-pointer transition-all"
                               :class="publishNow ? 'bg-orange-50/40 border-[#FF7A1A] ring-1 ring-[#FF7A1A]' : 'bg-white border-slate-200 hover:bg-slate-50'">
                            <input type="radio" name="publish_now" value="1" x-model="publishNowRadio" class="text-[#FF7A1A] focus:ring-[#FF7A1A]">
                            <div>
                                <span class="block text-xs font-bold text-[#0A1128]">🚀 Publicar Agora</span>
                                <span class="block text-[11px] text-slate-500">Envia imediatamente ao salvar</span>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3.5 rounded-2xl border cursor-pointer transition-all"
                               :class="!publishNow ? 'bg-orange-50/40 border-[#FF7A1A] ring-1 ring-[#FF7A1A]' : 'bg-white border-slate-200 hover:bg-slate-50'">
                            <input type="radio" name="publish_now" value="0" x-model="publishNowRadio" class="text-[#FF7A1A] focus:ring-[#FF7A1A]">
                            <div>
                                <span class="block text-xs font-bold text-[#0A1128]">📅 Agendar Horário</span>
                                <span class="block text-[11px] text-slate-500">Publicação automática no horário</span>
                            </div>
                        </label>
                    </div>

                    <div x-show="!publishNow" x-transition class="pt-1">
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Data e Hora do Agendamento</label>
                        <input type="datetime-local" name="scheduled_at"
                               value="{{ old('scheduled_at', now()->addHours(2)->format('Y-m-d\TH:i')) }}"
                               class="w-full sm:w-72 rounded-xl border-slate-300 text-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                                class="w-full py-4 px-6 bg-[#0A1128] hover:bg-[#FF7A1A] text-white rounded-2xl font-bold text-sm shadow-md transition-all flex items-center justify-center gap-2">
                            <span x-show="publishNow">🚀 Publicar Agora nas Redes Sociais</span>
                            <span x-show="!publishNow">📅 Confirmar Agendamento</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- COLUNA DIREITA: PREVIEW AO VIVO ESTILO INSTAGRAM / FACEBOOK (5 COLUNAS) --}}
            <div class="lg:col-span-5 lg:sticky lg:top-6 space-y-4">
                <div class="bg-white border border-slate-200/80 rounded-3xl p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[11px] font-extrabold uppercase tracking-widest text-slate-400">Preview em Tempo Real</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700"
                              x-text="format === 'story' ? 'Story 24h (Instagram)' : (platform === 'both' ? 'Instagram + Facebook' : (platform === 'instagram' ? 'Instagram Feed' : 'Facebook Feed'))">
                        </span>
                    </div>

                    {{-- Card Mockup de Rede Social --}}
                    <div class="border border-slate-200 rounded-2xl overflow-hidden bg-white shadow-sm max-w-sm mx-auto">
                        {{-- Topo do Perfil --}}
                        <div class="p-3.5 flex items-center justify-between border-b border-slate-100">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <template x-if="currentAccount()?.picture">
                                    <img :src="currentAccount().picture" class="w-9 h-9 rounded-full object-cover border border-slate-200" alt="">
                                </template>
                                <template x-if="!currentAccount()?.picture">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-amber-500 via-pink-600 to-purple-600 text-white font-bold text-xs flex items-center justify-center">
                                        NC5
                                    </div>
                                </template>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-[#0A1128] truncate"
                                       x-text="currentAccount()?.ig_user ? ('@' + currentAccount().ig_user) : (currentAccount()?.page_name || 'Sua Empresa')"></p>
                                    <p class="text-[10px] text-slate-400" x-text="publishNow ? 'Publicação imediata' : 'Agendado'"></p>
                                </div>
                            </div>
                            <span class="text-slate-400 font-bold text-sm">•••</span>
                        </div>

                        {{-- Área de Mídia do Preview --}}
                        <div class="relative bg-slate-900 flex items-center justify-center overflow-hidden"
                             :class="format === 'story' ? 'aspect-[9/16] max-h-[380px]' : 'aspect-square'">
                            <template x-if="mediaFiles.length === 0">
                                <div class="text-center p-6 text-slate-400">
                                    <svg class="w-10 h-10 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <p class="text-xs font-medium text-slate-300">A foto ou vídeo escolhido aparecerá aqui</p>
                                </div>
                            </template>

                            <template x-if="mediaFiles.length > 0">
                                <div class="w-full h-full relative">
                                    <template x-if="mediaFiles[previewIndex]?.isVideo">
                                        <video :src="mediaFiles[previewIndex].url" class="w-full h-full object-cover" controls muted></video>
                                    </template>
                                    <template x-if="!mediaFiles[previewIndex]?.isVideo">
                                        <img :src="mediaFiles[previewIndex].url" class="w-full h-full object-cover" alt="">
                                    </template>

                                    {{-- Setas do Carrossel no Preview --}}
                                    <template x-if="mediaFiles.length > 1">
                                        <div>
                                            <button type="button" @click="previewIndex = (previewIndex > 0) ? previewIndex - 1 : mediaFiles.length - 1"
                                                    class="absolute left-2 top-1/2 -translate-y-1/2 w-7 h-7 rounded-full bg-black/60 text-white text-xs flex items-center justify-center hover:bg-black">
                                                ‹
                                            </button>
                                            <button type="button" @click="previewIndex = (previewIndex < mediaFiles.length - 1) ? previewIndex + 1 : 0"
                                                    class="absolute right-2 top-1/2 -translate-y-1/2 w-7 h-7 rounded-full bg-black/60 text-white text-xs flex items-center justify-center hover:bg-black">
                                                ›
                                            </button>
                                            <span class="absolute top-2.5 right-2.5 bg-black/70 text-white text-[10px] font-bold px-2 py-0.5 rounded-full"
                                                  x-text="(previewIndex + 1) + '/' + mediaFiles.length"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>

                        {{-- Barra de Ícones + Legenda (Oculta em Story) --}}
                        <div x-show="format !== 'story'" class="p-3.5 space-y-2">
                            <div class="flex items-center justify-between text-slate-700">
                                <div class="flex items-center gap-3.5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                </div>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                            </div>

                            <div class="text-xs text-[#0A1128] leading-relaxed max-h-40 overflow-y-auto whitespace-pre-line">
                                <span class="font-bold mr-1" x-text="currentAccount()?.ig_user ? ('@' + currentAccount().ig_user) : (currentAccount()?.page_name || 'sua.empresa')"></span>
                                <span x-text="caption || 'Sua legenda aparecerá aqui em tempo real conforme você digita ou gera com a BruceIA...'"
                                      :class="!caption ? 'text-slate-400 italic' : ''"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- =====================================================================
         ABA 2: FILA & HISTÓRICO DE PUBLICAÇÕES
         ===================================================================== --}}
    <div x-show="activeTab === 'posts'" x-cloak class="bg-white border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h3 class="font-display font-bold text-xl text-[#0A1128]">Fila &amp; Histórico de Publicações</h3>
                <p class="text-xs text-slate-500 mt-0.5">Gerencie postagens agendadas, publicadas ou com falha.</p>
            </div>

            <div class="flex flex-wrap items-center gap-1.5">
                @php
                    $tabs = [
                        null        => ['label' => 'Todos',      'count' => $counts['all'] ?? 0],
                        'scheduled' => ['label' => 'Agendados',  'count' => $counts['scheduled'] ?? 0],
                        'published' => ['label' => 'Publicados', 'count' => $counts['published'] ?? 0],
                        'failed'    => ['label' => 'Com Falha',  'count' => $counts['failed'] ?? 0],
                        'draft'     => ['label' => 'Rascunhos',  'count' => $counts['draft'] ?? 0],
                    ];
                @endphp
                @foreach($tabs as $key => $tab)
                    @php $activeFilter = $filter === $key; @endphp
                    <a href="{{ route($indexRoute, $key ? ['status' => $key, 'tab' => 'posts'] : ['tab' => 'posts']) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition-colors {{ $activeFilter ? 'bg-[#0A1128] text-white' : 'bg-slate-100 text-slate-600 hover:text-[#0A1128]' }}">
                        {{ $tab['label'] }} ({{ $tab['count'] }})
                    </a>
                @endforeach
            </div>
        </div>

        @if($posts->isEmpty())
            <div class="text-center py-14 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                <p class="font-bold text-[#0A1128] text-sm">Nenhuma publicação encontrada neste filtro</p>
                <p class="text-xs text-slate-500 mt-1">Crie seu primeiro post ou agendamento na aba <strong>Novo Post</strong>.</p>
                <button type="button" @click="activeTab = 'studio'"
                        class="mt-4 px-5 py-2.5 bg-[#0A1128] hover:bg-[#FF7A1A] text-white rounded-xl text-xs font-bold transition-colors">
                    + Criar Novo Post
                </button>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($posts as $post)
                    @php
                        $statusBadge = match ($post->status) {
                            'published' => ['bg-emerald-100 text-emerald-800', 'Publicado'],
                            'scheduled' => ['bg-blue-100 text-blue-800', 'Agendado'],
                            'failed'    => ['bg-rose-100 text-rose-800', 'Falhou'],
                            default     => ['bg-amber-100 text-amber-800', 'Rascunho'],
                        };
                        $mediaItems = $post->mediaList();
                    @endphp
                    <div class="border border-slate-200 rounded-2xl overflow-hidden flex flex-col justify-between bg-white shadow-sm">
                        <div>
                            @if(!empty($mediaItems))
                                <div class="relative h-44 bg-slate-900 overflow-hidden">
                                    @if(($mediaItems[0]['type'] ?? 'image') === 'video')
                                        <video src="{{ $mediaItems[0]['url'] }}" class="w-full h-full object-cover" muted></video>
                                    @else
                                        <img src="{{ $mediaItems[0]['url'] }}" alt="Mídia do post" class="w-full h-full object-cover">
                                    @endif
                                    @if(count($mediaItems) > 1)
                                        <span class="absolute top-2.5 right-2.5 bg-black/75 text-white text-[10px] font-bold px-2.5 py-1 rounded-full">
                                            Carrossel ({{ count($mediaItems) }} mídias)
                                        </span>
                                    @endif
                                </div>
                            @endif

                            <div class="p-4">
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full {{ $statusBadge[0] }}">
                                        {{ $statusBadge[1] }}
                                    </span>
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">
                                        {{ $post->platform === 'both' ? 'IG + FB' : strtoupper($post->platform) }} · {{ strtoupper($post->format ?? 'FEED') }}
                                    </span>
                                </div>

                                <p class="text-xs text-[#0A1128] line-clamp-4 whitespace-pre-line leading-relaxed">{{ $post->caption }}</p>

                                @if($post->status === 'failed' && $post->error_message)
                                    <div class="mt-2.5 p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-[11px] text-rose-700">
                                        <strong>Motivo:</strong> {{ $post->error_message }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="px-4 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                            <span>{{ $post->scheduled_at ? $post->scheduled_at->format('d/m/Y H:i') : '—' }}</span>

                            <div class="flex items-center gap-3">
                                @if($post->status !== 'published' && $post->account)
                                    <form method="POST" action="{{ route($retryPostRoute, $post) }}">
                                        @csrf
                                        <button type="submit" class="text-[#1877F2] hover:underline font-bold">
                                            {{ $post->status === 'failed' ? 'Tentar de novo' : 'Publicar agora' }}
                                        </button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route($destroyPostRoute, $post) }}" onsubmit="return confirm('Excluir esta publicação?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:underline font-semibold">Excluir</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $posts->links() }}
            </div>
        @endif
    </div>

    {{-- =====================================================================
         ABA 3: CONTAS CONECTADAS (FACEBOOK PAGES & INSTAGRAM BUSINESS)
         ===================================================================== --}}
    <div x-show="activeTab === 'accounts'" x-cloak class="bg-white border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h3 class="font-display font-bold text-xl text-[#0A1128]">Contas Conectadas (Instagram &amp; Facebook)</h3>
                <p class="text-xs text-slate-500 mt-0.5">Vincule suas Páginas do Facebook e perfis profissionais do Instagram Business via API Oficial da Meta.</p>
            </div>

            <a href="{{ route($connectRoute) }}"
               class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-[#1877F2] hover:bg-blue-700 text-white rounded-xl font-bold text-xs shadow-sm transition-colors self-start">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                Conectar / Atualizar Contas Meta
            </a>
        </div>

        @if($accounts->isEmpty())
            <div class="bg-slate-50 border border-dashed border-slate-300 rounded-2xl p-10 text-center max-w-xl mx-auto my-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-[#1877F2] flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </div>
                <p class="font-bold text-[#0A1128] text-base">Nenhuma página conectada ainda</p>
                <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                    Clique no botão azul acima para autorizar suas Páginas do Facebook. O sistema localizará automaticamente a conta profissional do <strong>Instagram Business</strong> vinculada a cada página.
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($accounts as $acc)
                    <div class="border {{ $acc->is_active ? 'border-slate-200 bg-white' : 'border-slate-200/60 bg-slate-50 opacity-75' }} rounded-2xl p-5 flex flex-col justify-between gap-4 shadow-sm">
                        <div class="flex items-start gap-3.5">
                            @if($acc->page_picture)
                                <img src="{{ $acc->page_picture }}" alt="{{ $acc->page_name }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200 shrink-0">
                            @else
                                <div class="w-12 h-12 rounded-xl bg-blue-100 text-[#1877F2] font-bold flex items-center justify-center shrink-0">
                                    {{ mb_substr($acc->page_name, 0, 1) }}
                                </div>
                            @endif

                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <h4 class="font-bold text-sm text-[#0A1128] truncate">{{ $acc->page_name }}</h4>
                                    @if($acc->is_active && !$acc->isTokenExpired())
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-800">Ativa</span>
                                    @elseif($acc->isTokenExpired())
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-rose-100 text-rose-800">Expirada</span>
                                    @else
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-200 text-slate-700">Pausada</span>
                                    @endif
                                </div>

                                <div class="mt-2 space-y-1 text-xs">
                                    <div class="text-slate-500 flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-[#1877F2]"></span>
                                        <span>Facebook ID: <code class="text-[11px]">{{ $acc->page_id }}</code></span>
                                    </div>
                                    @if($acc->instagram_business_id)
                                        <div class="text-pink-600 font-bold flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-pink-600"></span>
                                            <span>Instagram: {{ $acc->instagram_username ? '@' . $acc->instagram_username : 'Vinculado' }}</span>
                                        </div>
                                    @else
                                        <div class="text-amber-700 text-[11px]">Sem Instagram Business vinculado</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                            <button type="button" @click="selectedAccountId = '{{ $acc->id }}'; onAccountChange(); activeTab = 'studio'"
                                    class="font-bold text-[#0A1128] hover:text-[#FF7A1A] transition-colors">
                                + Criar Post nesta conta
                            </button>

                            <div class="flex items-center gap-2.5">
                                @if($acc->is_active)
                                    <form method="POST" action="{{ route($disconnectRoute, $acc) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-amber-700 hover:underline font-semibold">Pausar</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route($destroyAccRoute, $acc) }}" onsubmit="return confirm('Remover esta conta social?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:underline font-semibold">Remover</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<script>
    function socialMediaStudio() {
        const accountsData = @json($accountsPayload);
        const firstAcc = accountsData.length > 0 ? accountsData[0] : null;

        return {
            activeTab: @json($defaultTab),
            accounts: accountsData,
            selectedAccountId: firstAcc ? firstAcc.id : '',
            platform: (firstAcc && firstAcc.has_ig) ? 'both' : 'facebook',
            format: 'feed',
            caption: @json(old('caption', '')),
            showAiBox: false,
            aiTopic: '',
            aiLoading: false,
            aiError: '',
            publishNowRadio: '{{ old('publish_now', '1') }}',
            mediaFiles: [],
            previewIndex: 0,

            get publishNow() {
                return this.publishNowRadio === '1';
            },

            currentAccount() {
                return this.accounts.find(a => String(a.id) === String(this.selectedAccountId)) || null;
            },

            currentAccountHasIg() {
                const acc = this.currentAccount();
                return acc ? !!acc.has_ig : true;
            },

            onAccountChange() {
                if (!this.currentAccountHasIg()) {
                    this.format = 'feed';
                    this.platform = 'facebook';
                }
            },

            setPlatform(p) {
                this.platform = p;
            },

            setFormat(f) {
                this.format = f;
                if (f === 'story') {
                    this.platform = 'instagram';
                }
            },

            handleMediaSelect(event) {
                const files = Array.from(event.target.files || []);
                for (const file of files) {
                    if (this.mediaFiles.length >= 10) break;
                    this.mediaFiles.push({
                        file: file,
                        url: URL.createObjectURL(file),
                        isVideo: file.type.startsWith('video/')
                    });
                }
                this.rebuildFileInput();
            },

            removeMedia(idx) {
                this.mediaFiles.splice(idx, 1);
                if (this.previewIndex >= this.mediaFiles.length) {
                    this.previewIndex = Math.max(0, this.mediaFiles.length - 1);
                }
                this.rebuildFileInput();
            },

            rebuildFileInput() {
                const input = document.getElementById('studioMediaInput');
                if (!input || typeof DataTransfer === 'undefined') return;
                const dt = new DataTransfer();
                this.mediaFiles.forEach(item => dt.items.add(item.file));
                input.files = dt.files;
            },

            async generateCaptionWithAi() {
                if (!this.aiTopic || this.aiTopic.trim().length < 3) {
                    this.aiError = 'Digite ou selecione um tema para a BruceIA criar a legenda.';
                    return;
                }
                this.aiError = '';
                this.aiLoading = true;

                try {
                    const response = await fetch('{{ route($captionAiRoute) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            topic: this.aiTopic,
                            platform: this.platform,
                            format: this.format
                        })
                    });

                    const data = await response.json();
                    if (!response.ok || data.error) {
                        this.aiError = data.error || 'Não foi possível gerar a legenda.';
                    } else if (data.caption) {
                        this.caption = data.caption;
                        this.showAiBox = false;
                    }
                } catch (e) {
                    this.aiError = 'Falha de comunicação ao acionar a BruceIA.';
                } finally {
                    this.aiLoading = false;
                }
            }
        };
    }
</script>
