<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <h2 class="font-display font-extrabold text-2xl sm:text-3xl text-[#0A1128] tracking-tight">
                        Motor de Crescimento do Bruce
                    </h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                        {{ $currentNicheInfo['icon'] }} {{ $currentNicheInfo['label'] }}
                    </span>
                </div>
                <p class="text-[#64748B] text-xs sm:text-sm">
                    Atração local e estratégias calibradas para o nicho da sua empresa.
                </p>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                @if(!$googleConnected)
                    <a href="{{ route('customer.google-business.index') }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-blue-600" viewBox="0 0 24 24" fill="currentColor"><path d="M12.24 10.285V14.4h6.806c-.275 1.765-2.056 5.174-6.806 5.174-4.095 0-7.439-3.389-7.439-7.574s3.345-7.574 7.439-7.574c2.33 0 3.891.989 4.785 1.849l3.254-3.138C18.189 1.186 15.479 0 12.24 0c-6.635 0-12 5.365-12 12s5.365 12 12 12c6.926 0 11.52-4.869 11.52-11.726 0-.788-.085-1.39-.189-1.989H12.24z"/></svg>
                        Conectar Google
                    </a>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-medium text-emerald-800 bg-emerald-50 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Google Conectado
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div x-data="{ tab: 'posts', modalPost: null, copyToast: false }" class="space-y-6">

        {{-- Toast Copiado --}}
        <div x-show="copyToast" x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="fixed bottom-6 right-6 z-50 bg-[#0A1128] text-white px-4 py-3 rounded-2xl shadow-xl flex items-center gap-2.5 text-xs font-bold">
            <span class="w-5 h-5 rounded-full bg-emerald-500 flex items-center justify-center text-xs">✓</span>
            Texto copiado para a área de transferência!
        </div>

        {{-- Abas Limpas e Elegantes (Padrão Executivo NC5) --}}
        <div class="border-b border-gray-200">
            <nav class="flex gap-6 -mb-px overflow-x-auto no-scrollbar">
                <button type="button"
                        @click="tab = 'posts'"
                        :class="tab === 'posts' ? 'text-[#0A1128] border-orange-500 font-bold' : 'text-[#64748B] border-transparent hover:text-[#0A1128] font-medium'"
                        class="pb-3 text-sm border-b-2 transition whitespace-nowrap flex items-center gap-2 cursor-pointer">
                    <span>📍 Posts para o Google Meu Negócio</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-extrabold bg-orange-100 text-orange-800">IA</span>
                </button>

                <button type="button"
                        @click="tab = 'radar'"
                        :class="tab === 'radar' ? 'text-[#0A1128] border-orange-500 font-bold' : 'text-[#64748B] border-transparent hover:text-[#0A1128] font-medium'"
                        class="pb-3 text-sm border-b-2 transition whitespace-nowrap flex items-center gap-2 cursor-pointer">
                    <span>⚡ Radar de Vendas &amp; Dicas</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">{{ count($growthTips) }}</span>
                </button>

                <button type="button"
                        @click="tab = 'perfil'"
                        :class="tab === 'perfil' ? 'text-[#0A1128] border-orange-500 font-bold' : 'text-[#64748B] border-transparent hover:text-[#0A1128] font-medium'"
                        class="pb-3 text-sm border-b-2 transition whitespace-nowrap flex items-center gap-2 cursor-pointer">
                    <span>🎯 Nicho &amp; DNA da Empresa</span>
                </button>
            </nav>
        </div>

        {{-- =================================================================
             ABA 1: POSTS PARA O GOOGLE MEU NEGÓCIO (Limpo e Direto)
             ================================================================= --}}
        <div x-show="tab === 'posts'" class="space-y-6">

            {{-- Barra de Filtros por Tema (Clean & Minimalista) --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-0.5">
                    <span class="text-xs font-semibold text-gray-500 mr-1 shrink-0">Temas:</span>
                    @foreach($currentNicheInfo['suggested_topics'] as $topic)
                        <a href="{{ route('customer.conhecimento.index', ['tema' => $topic]) }}"
                           class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 hover:bg-orange-50 hover:text-orange-700 text-gray-700 transition shrink-0">
                            {{ $topic }}
                        </a>
                    @endforeach
                </div>

                <a href="{{ route('customer.conhecimento.index') }}"
                   class="inline-flex items-center justify-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold bg-[#0A1128] text-white hover:bg-orange-600 transition shrink-0 shadow-2xs">
                    <span>↻</span>
                    <span>Gerar Novas Ideias</span>
                </a>
            </div>

            {{-- Grid de 3 Cards de Posts --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($suggestedPosts as $idx => $post)
                    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm hover:border-gray-300 transition flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-orange-50 text-orange-700 border border-orange-200">
                                    {{ $post['cta_label'] ?? 'Ação' }}
                                </span>
                                <span class="text-[11px] text-gray-400 font-medium">Post #{{ $idx + 1 }}</span>
                            </div>

                            <h4 class="font-display font-bold text-base text-[#0A1128] mb-2 leading-snug">
                                {{ $post['titulo_tema'] ?? 'Destaque' }}
                            </h4>

                            {{-- Texto Formatado --}}
                            <div class="bg-gray-50 rounded-xl p-3.5 border border-gray-100 text-[13px] text-gray-800 leading-relaxed whitespace-pre-wrap font-sans mb-3.5">
                                {{ $post['texto_post'] ?? '' }}
                            </div>

                            {{-- Dica de Foto --}}
                            <p class="text-xs text-gray-500 flex items-start gap-1.5 mb-4">
                                <span class="text-sm leading-none shrink-0">📸</span>
                                <span><strong class="text-gray-700 font-semibold">Foto ideal:</strong> {{ $post['sugestao_foto'] ?? 'Foto real do espaço ou produto.' }}</span>
                            </p>
                        </div>

                        {{-- Ações --}}
                        <div class="pt-3 border-t border-gray-100 flex items-center gap-2">
                            <button type="button"
                                    @click="navigator.clipboard.writeText(@js($post['texto_post'] ?? '')); copyToast = true; setTimeout(() => copyToast = false, 3000)"
                                    class="p-2 rounded-xl border border-gray-200 hover:bg-gray-50 text-gray-600 transition cursor-pointer"
                                    title="Copiar texto">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                            </button>

                            <button type="button"
                                    @click="modalPost = @js($post)"
                                    class="flex-1 py-2 px-3 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-2xs cursor-pointer">
                                <span>Publicar no Google</span>
                                <span>&rarr;</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- =================================================================
             ABA 2: RADAR DE DICAS & OPORTUNIDADES
             ================================================================= --}}
        <div x-show="tab === 'radar'" class="space-y-6" style="display: none;">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($growthTips as $tip)
                    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm hover:border-gray-300 transition flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold uppercase tracking-wider {{ $tip['tipo'] === 'alerta' ? 'bg-rose-50 text-rose-700 border border-rose-200' : ($tip['tipo'] === 'sucesso' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200') }}">
                                    {{ $tip['badge'] }}
                                </span>
                                <span class="text-xs text-gray-400 font-medium">{{ $tip['categoria'] }}</span>
                            </div>

                            <h4 class="font-display font-bold text-base text-[#0A1128] mb-2 leading-snug">
                                {{ $tip['titulo'] }}
                            </h4>

                            <p class="text-gray-600 text-xs sm:text-sm leading-relaxed mb-4">
                                {{ $tip['descricao'] }}
                            </p>
                        </div>

                        <div class="pt-3 border-t border-gray-100">
                            @if(str_starts_with($tip['acao_url'], '#'))
                                <button type="button"
                                        @click="tab = 'posts'"
                                        class="w-full py-2 px-3 rounded-xl bg-gray-900 hover:bg-orange-600 text-white text-xs font-bold transition text-center shadow-2xs cursor-pointer">
                                    {{ $tip['acao_label'] }} &rarr;
                                </button>
                            @else
                                <a href="{{ $tip['acao_url'] }}"
                                   class="w-full block py-2 px-3 rounded-xl bg-gray-900 hover:bg-orange-600 text-white text-xs font-bold transition text-center shadow-2xs">
                                    {{ $tip['acao_label'] }} &rarr;
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- =================================================================
             ABA 3: PERFIL DO NICHO & DNA DA EMPRESA
             ================================================================= --}}
        <div x-show="tab === 'perfil'" class="space-y-6" style="display: none;">
            <div class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-8 shadow-sm">
                <div class="max-w-3xl">
                    <h3 class="font-display font-extrabold text-xl text-[#0A1128]">
                        Configuração do Segmento &amp; Tom da Marca
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">
                        Essas informações calibram as postagens no Google e os atendimentos via IA para o seu tipo de cliente.
                    </p>

                    <form method="POST" action="{{ route('customer.conhecimento.profile') }}" class="mt-6 space-y-5">
                        @csrf

                        {{-- Seletor de Nicho --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                                Segmento Principal *
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                                @foreach($niches as $key => $n)
                                    <label class="border rounded-xl p-3 flex items-start gap-2.5 cursor-pointer transition {{ $currentNiche === $key ? 'border-orange-500 bg-orange-50/40 ring-1 ring-orange-500' : 'border-gray-200 hover:border-gray-300' }}">
                                        <input type="radio" name="nicho_atuacao" value="{{ $key }}"
                                               {{ $currentNiche === $key ? 'checked' : '' }}
                                               class="mt-0.5 text-orange-600 focus:ring-orange-500">
                                        <div>
                                            <span class="text-sm font-bold text-gray-900">{{ $n['icon'] }} {{ $n['label'] }}</span>
                                            <p class="text-[11px] text-gray-500 mt-0.5 leading-snug">{{ $n['description'] }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                                    Tom de Voz Desejado
                                </label>
                                <input type="text" name="tom_voz"
                                       value="{{ old('tom_voz', $cliente->tom_voz ?: $currentNicheInfo['default_tone']) }}"
                                       class="w-full rounded-xl border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                                    Público-Alvo Principal
                                </label>
                                <input type="text" name="publico_alvo"
                                       value="{{ old('publico_alvo', $cliente->publico_alvo) }}"
                                       class="w-full rounded-xl border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                                Principais Diferenciais da Sua Empresa
                            </label>
                            <textarea name="diferenciais_competitivos" rows="3"
                                      placeholder="Ex: Ingredientes artesanais, entrega rápida, 10 anos no mercado, estacionamento gratuito..."
                                      class="w-full rounded-xl border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">{{ old('diferenciais_competitivos', $cliente->diferenciais_competitivos) }}</textarea>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit"
                                    class="px-5 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs transition shadow-2xs cursor-pointer">
                                Salvar Perfil
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Detalhes Adicionais (Antigo conhecimento) --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-8 shadow-sm">
                <div class="flex items-center justify-between gap-4 mb-4 flex-wrap">
                    <div>
                        <h4 class="font-display font-bold text-base text-[#0A1128]">
                            Políticas &amp; Informações Complementares
                        </h4>
                        <p class="text-xs text-gray-500">Tabelas de preços, regras de troca ou dúvidas frequentes.</p>
                    </div>
                    <a href="{{ route('customer.conhecimento.create') }}"
                       class="px-3 py-1.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-bold transition">
                        + Nova Informação
                    </a>
                </div>

                @if($dnaItens->isEmpty())
                    <p class="text-xs text-gray-400 py-4 text-center">Nenhuma informação adicional cadastrada no momento.</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($dnaItens as $item)
                            <div class="border border-gray-100 rounded-xl p-3.5 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between gap-2 mb-1">
                                        <h5 class="font-bold text-xs text-[#0A1128]">{{ $item->titulo }}</h5>
                                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded {{ $item->ativo ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                            {{ $item->ativo ? 'Ativo' : 'Pausado' }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 line-clamp-2">{{ $item->conteudo }}</p>
                                </div>
                                <div class="mt-2.5 pt-2 border-t border-gray-100 flex items-center justify-end gap-3 text-[11px]">
                                    <a href="{{ route('customer.conhecimento.edit', $item) }}" class="text-orange-600 font-bold hover:underline">Editar</a>
                                    <form action="{{ route('customer.conhecimento.destroy', $item) }}" method="POST" onsubmit="return confirm('Excluir?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 font-bold hover:underline cursor-pointer">Excluir</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- =================================================================
             MODAL DE CONFIRMAÇÃO & PUBLICAÇÃO NO GOOGLE
             ================================================================= --}}
        <div x-show="modalPost" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.away="modalPost = null"
                 class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-200 relative animate-in fade-in zoom-in-95">

                <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                    <h3 class="font-display font-bold text-base text-[#0A1128]">Publicar no Google Meu Negócio</h3>
                    <button type="button" @click="modalPost = null" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>

                @if(!$googleConnected)
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-900 mb-3">
                        Sua ficha ainda não está conectada.
                        <a href="{{ route('customer.google-business.index') }}" class="font-bold underline ml-1">Conectar agora &rarr;</a>
                    </div>
                @endif

                <form method="POST" action="{{ route('customer.conhecimento.publish-google') }}" enctype="multipart/form-data" class="space-y-3.5">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Texto da Publicação
                        </label>
                        <textarea name="summary" rows="4" required maxlength="1500"
                                  x-model="modalPost ? modalPost.texto_post : ''"
                                  class="w-full rounded-xl border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 leading-relaxed"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Botão de Ação
                            </label>
                            <select name="cta_type" x-model="modalPost ? modalPost.cta_recomendado : 'CALL'"
                                    class="w-full rounded-xl border-gray-300 text-xs">
                                <option value="CALL">Ligar Agora</option>
                                <option value="BOOK">Agendar Horário</option>
                                <option value="ORDER">Fazer Pedido</option>
                                <option value="LEARN_MORE">Saiba Mais</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Link (Opcional)
                            </label>
                            <input type="url" name="cta_url"
                                   placeholder="https://suaempresa.com.br"
                                   class="w-full rounded-xl border-gray-300 text-xs">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Foto (Opcional)
                        </label>
                        <input type="file" name="image" accept="image/png,image/jpeg,image/jpg"
                               class="w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 hover:file:bg-gray-200 cursor-pointer">
                    </div>

                    <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                        <button type="button" @click="modalPost = null"
                                class="px-4 py-2 rounded-xl border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-50 cursor-pointer">
                            Cancelar
                        </button>
                        <button type="submit"
                                {{ $googleConnected ? '' : 'disabled' }}
                                class="px-4 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 disabled:opacity-50 text-white text-xs font-bold transition shadow-2xs cursor-pointer">
                            Publicar Agora
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
