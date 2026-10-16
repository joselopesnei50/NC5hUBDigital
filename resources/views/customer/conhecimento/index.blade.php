<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold uppercase tracking-wider bg-orange-100 text-orange-800 border border-orange-200">
                        Copiloto de Atração &amp; Vendas 🚀
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white text-gray-700 border border-gray-200 shadow-2xs">
                        {{ $currentNicheInfo['icon'] }} {{ $currentNicheInfo['label'] }}
                    </span>
                </div>
                <h2 class="font-display font-extrabold text-3xl text-[#0A1128] leading-tight tracking-tight">
                    Motor de Crescimento do Bruce
                </h2>
                <p class="text-[#64748B] text-sm mt-1">
                    Ideias e publicações automáticas no Google Meu Negócio, radar de vendas e estratégias personalizadas para o seu segmento.
                </p>
            </div>
            <div class="flex items-center gap-3">
                @if(!$googleConnected)
                    <a href="{{ route('customer.google-business.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-blue-50 text-blue-800 border border-blue-200 hover:bg-blue-100 transition shadow-2xs">
                        <svg class="w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="currentColor"><path d="M12.24 10.285V14.4h6.806c-.275 1.765-2.056 5.174-6.806 5.174-4.095 0-7.439-3.389-7.439-7.574s3.345-7.574 7.439-7.574c2.33 0 3.891.989 4.785 1.849l3.254-3.138C18.189 1.186 15.479 0 12.24 0c-6.635 0-12 5.365-12 12s5.365 12 12 12c6.926 0 11.52-4.869 11.52-11.726 0-.788-.085-1.39-.189-1.989H12.24z"/></svg>
                        Conectar Google Meu Negócio
                    </a>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-2xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Google Conectado &bull; Pronto p/ Publicar
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

        {{-- Barra de Navegação das Abas --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-2 shadow-2xs flex flex-wrap gap-2">
            <button type="button"
                    @click="tab = 'posts'"
                    :class="tab === 'posts' ? 'bg-[#0A1128] text-white shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer">
                <span>📍</span>
                <span>Posts para o Google Meu Negócio</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-extrabold bg-orange-500 text-white">IA</span>
            </button>

            <button type="button"
                    @click="tab = 'radar'"
                    :class="tab === 'radar' ? 'bg-[#0A1128] text-white shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer">
                <span>⚡</span>
                <span>Radar de Vendas &amp; Dicas</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-extrabold bg-emerald-500 text-white">{{ count($growthTips) }}</span>
            </button>

            <button type="button"
                    @click="tab = 'perfil'"
                    :class="tab === 'perfil' ? 'bg-[#0A1128] text-white shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer">
                <span>🎯</span>
                <span>Perfil do Nicho &amp; DNA da Empresa</span>
            </button>
        </div>

        {{-- =================================================================
             ABA 1: POSTS PARA O GOOGLE MEU NEGÓCIO (Gerador com IA)
             ================================================================= --}}
        <div x-show="tab === 'posts'" class="space-y-6">
            {{-- Header de Geração Rápida --}}
            <div class="bg-gradient-to-br from-[#0A1128] via-[#141D38] to-[#0A1128] rounded-3xl p-6 sm:p-8 text-white relative overflow-hidden shadow-lg border border-white/10">
                <div class="relative z-10 max-w-3xl">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        <span class="text-[11px] font-extrabold uppercase tracking-widest text-emerald-300">
                            Publicador Automático de Busca Local
                        </span>
                    </div>
                    <h3 class="font-display font-extrabold text-2xl sm:text-3xl text-white tracking-tight">
                        Ideias Semanais de Posts para o Google
                    </h3>
                    <p class="text-white/80 text-sm mt-2 leading-relaxed">
                        O Bruce gera posts calibrados para o nicho <strong>{{ $currentNicheInfo['label'] }}</strong> focados em cliques, chamadas telefônicas e rotas no Google Maps. Escolha uma ideia abaixo e publique com 1 clique!
                    </p>

                    {{-- Temas Sugeridos Rápido --}}
                    <div class="flex items-center gap-2 mt-5 flex-wrap">
                        <span class="text-xs text-white/60 font-semibold mr-1">Gerar por tema:</span>
                        @foreach($currentNicheInfo['suggested_topics'] as $topic)
                            <a href="{{ route('customer.conhecimento.index', ['tema' => $topic]) }}"
                               class="px-3 py-1.5 rounded-full text-xs font-semibold bg-white/10 hover:bg-orange-500 hover:text-white text-white/90 border border-white/15 transition shadow-2xs">
                                {{ $topic }}
                            </a>
                        @endforeach
                        <a href="{{ route('customer.conhecimento.index') }}"
                           class="px-3 py-1.5 rounded-full text-xs font-semibold bg-white/5 hover:bg-white/20 text-white/70 transition">
                            ↺ Novas Sugestões
                        </a>
                    </div>
                </div>
            </div>

            {{-- Grid de Posts Sugeridos --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($suggestedPosts as $idx => $post)
                    <div class="bg-white rounded-3xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition flex flex-col justify-between relative group">
                        {{-- Top Badge --}}
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-orange-50 text-orange-700 border border-orange-200">
                                    Ideia #{{ $idx + 1 }} &bull; {{ $post['cta_label'] ?? 'Ação Local' }}
                                </span>
                                <span class="text-xs text-gray-400 font-medium">Google Post</span>
                            </div>

                            <h4 class="font-display font-bold text-base text-[#0A1128] mb-3 leading-snug">
                                {{ $post['titulo_tema'] ?? 'Destaque da Semana' }}
                            </h4>

                            {{-- Preview do Texto --}}
                            <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 text-[13.5px] text-gray-800 leading-relaxed whitespace-pre-wrap relative font-sans mb-4">
                                {{ $post['texto_post'] ?? '' }}
                            </div>

                            {{-- Sugestão de Foto Real --}}
                            <div class="flex items-start gap-2 bg-amber-50/70 border border-amber-200/70 rounded-xl p-3 text-xs text-amber-900 mb-5">
                                <span class="text-base leading-none">📸</span>
                                <div>
                                    <strong class="font-bold">Sugestão de Foto:</strong>
                                    <span>{{ $post['sugestao_foto'] ?? 'Foto nítida e bem iluminada do seu espaço ou produto.' }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Ações do Post --}}
                        <div class="pt-4 border-t border-gray-100 flex items-center justify-between gap-2">
                            <button type="button"
                                    @click="navigator.clipboard.writeText(@js($post['texto_post'] ?? '')); copyToast = true; setTimeout(() => copyToast = false, 3000)"
                                    class="px-3 py-2 rounded-xl border border-gray-200 hover:border-gray-400 text-xs font-bold text-gray-700 transition flex items-center gap-1.5 cursor-pointer"
                                    title="Copiar texto para WhatsApp ou Instagram">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                <span>Copiar</span>
                            </button>

                            <button type="button"
                                    @click="modalPost = @js($post)"
                                    class="flex-1 px-4 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-sm cursor-pointer">
                                <span>🚀</span>
                                <span>Publicar no Google</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- =================================================================
             ABA 2: RADAR DE OPORTUNIDADES & DICAS DE VENDAS
             ================================================================= --}}
        <div x-show="tab === 'radar'" class="space-y-6" style="display: none;">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($growthTips as $tip)
                    <div class="bg-white rounded-3xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $tip['tipo'] === 'alerta' ? 'bg-rose-50 text-rose-700 border border-rose-200' : ($tip['tipo'] === 'sucesso' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200') }}">
                                    {{ $tip['badge'] }}
                                </span>
                                <span class="text-xs text-gray-400 font-semibold">{{ $tip['categoria'] }}</span>
                            </div>

                            <h4 class="font-display font-bold text-lg text-[#0A1128] mb-2 leading-snug">
                                {{ $tip['titulo'] }}
                            </h4>

                            <p class="text-gray-600 text-sm leading-relaxed mb-6">
                                {{ $tip['descricao'] }}
                            </p>
                        </div>

                        <div class="pt-4 border-t border-gray-100">
                            @if(str_starts_with($tip['acao_url'], '#'))
                                <button type="button"
                                        @click="tab = 'posts'"
                                        class="w-full px-4 py-2.5 rounded-xl bg-[#0A1128] hover:bg-orange-600 text-white text-xs font-bold transition text-center flex items-center justify-center gap-2 shadow-2xs cursor-pointer">
                                    {{ $tip['acao_label'] }} &rarr;
                                </button>
                            @else
                                <a href="{{ $tip['acao_url'] }}"
                                   class="w-full px-4 py-2.5 rounded-xl bg-[#0A1128] hover:bg-orange-600 text-white text-xs font-bold transition text-center flex items-center justify-center gap-2 shadow-2xs">
                                    {{ $tip['acao_label'] }} &rarr;
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- =================================================================
             ABA 3: PERFIL DO NICHO & DNA DO NEGÓCIO
             ================================================================= --}}
        <div x-show="tab === 'perfil'" class="space-y-6" style="display: none;">
            {{-- Formulário de Perfil do Negócio --}}
            <div class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 shadow-sm">
                <div class="max-w-3xl">
                    <h3 class="font-display font-extrabold text-xl text-[#0A1128]">
                        DNA Estratégico da Empresa
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Defina o nicho e os diferenciais da sua empresa. O Bruce IA usa essas informações para calibrar posts no Google, mensagens do WhatsApp e recomendações de vendas.
                    </p>

                    <form method="POST" action="{{ route('customer.conhecimento.profile') }}" class="mt-6 space-y-6">
                        @csrf

                        {{-- Seletor de Nicho --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                                Qual é o segmento principal do seu negócio? *
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($niches as $key => $n)
                                    <label class="border rounded-2xl p-4 flex items-start gap-3 cursor-pointer transition {{ $currentNiche === $key ? 'border-orange-500 bg-orange-50/50 ring-2 ring-orange-400/20' : 'border-gray-200 hover:border-gray-300' }}">
                                        <input type="radio" name="nicho_atuacao" value="{{ $key }}"
                                               {{ $currentNiche === $key ? 'checked' : '' }}
                                               class="mt-1 text-orange-600 focus:ring-orange-500">
                                        <div>
                                            <span class="text-base mr-1">{{ $n['icon'] }}</span>
                                            <span class="text-sm font-bold text-gray-900">{{ $n['label'] }}</span>
                                            <p class="text-xs text-gray-500 mt-1 leading-snug">{{ $n['description'] }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                                    Tom de Voz Desejado
                                </label>
                                <input type="text" name="tom_voz"
                                       value="{{ old('tom_voz', $cliente->tom_voz ?: $currentNicheInfo['default_tone']) }}"
                                       placeholder="Ex: Descontraído e apetitoso / Formal e consultivo"
                                       class="w-full rounded-xl border-gray-300 focus:border-orange-500 focus:ring-orange-500 text-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                                    Público-Alvo Principal
                                </label>
                                <input type="text" name="publico_alvo"
                                       value="{{ old('publico_alvo', $cliente->publico_alvo) }}"
                                       placeholder="Ex: Jovens e famílias da região sul / Investidores de médio padrão"
                                       class="w-full rounded-xl border-gray-300 focus:border-orange-500 focus:ring-orange-500 text-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                                Principais Diferenciais Competitivos
                            </label>
                            <textarea name="diferenciais_competitivos" rows="3"
                                      placeholder="Ex: Ingredientes 100% artesanais, entrega rápida em 30 min, estacionamento grátis, 15 anos de tradição..."
                                      class="w-full rounded-xl border-gray-300 focus:border-orange-500 focus:ring-orange-500 text-sm">{{ old('diferenciais_competitivos', $cliente->diferenciais_competitivos) }}</textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                    class="px-6 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs transition shadow-sm cursor-pointer">
                                Salvar Perfil Estratégico
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Informações Complementares Cadastradas --}}
            <div class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 shadow-sm">
                <div class="flex items-center justify-between gap-4 mb-6 flex-wrap">
                    <div>
                        <h4 class="font-display font-bold text-lg text-[#0A1128]">
                            Informações Detalhadas da Empresa
                        </h4>
                        <p class="text-xs text-gray-500 mt-0.5">Políticas de atendimento, tabelas de preços ou regras que a IA pode consultar.</p>
                    </div>
                    <a href="{{ route('customer.conhecimento.create') }}"
                       class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-[#0A1128] text-xs font-bold transition flex items-center gap-1.5">
                        <span>+</span>
                        <span>Adicionar Informação</span>
                    </a>
                </div>

                @if($dnaItens->isEmpty())
                    <div class="text-center py-8 text-gray-500 text-xs bg-gray-50 rounded-2xl border border-dashed border-gray-200 p-6">
                        Nenhuma informação complementar necessária no momento. O nicho e diferenciais acima já alimentam o robô!
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($dnaItens as $item)
                            <div class="border border-gray-200 rounded-2xl p-4 flex flex-col justify-between hover:border-gray-300 transition">
                                <div>
                                    <div class="flex items-center justify-between gap-2 mb-2">
                                        <h5 class="font-bold text-sm text-[#0A1128]">{{ $item->titulo }}</h5>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $item->ativo ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $item->ativo ? 'Ativo' : 'Pausado' }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-600 line-clamp-3 leading-relaxed">{{ $item->conteudo }}</p>
                                </div>
                                <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-end gap-3 text-xs">
                                    <a href="{{ route('customer.conhecimento.edit', $item) }}" class="text-orange-600 font-bold hover:underline">Editar</a>
                                    <form action="{{ route('customer.conhecimento.destroy', $item) }}" method="POST" onsubmit="return confirm('Excluir esta informação?');">
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
             MODAL DE CONFIRMAÇÃO & PUBLICAÇÃO DIRETA NO GOOGLE
             ================================================================= --}}
        <div x-show="modalPost" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.away="modalPost = null"
                 class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-gray-200 relative animate-in fade-in zoom-in-95">

                <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">📍</span>
                        <h3 class="font-display font-bold text-lg text-[#0A1128]">Publicar no Google Meu Negócio</h3>
                    </div>
                    <button type="button" @click="modalPost = null" class="text-gray-400 hover:text-gray-600 text-xl font-bold cursor-pointer">&times;</button>
                </div>

                @if(!$googleConnected)
                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 text-xs text-amber-900 mb-4">
                        Sua ficha do Google Meu Negócio ainda não está conectada.
                        <a href="{{ route('customer.google-business.index') }}" class="font-bold underline ml-1">Conectar agora &rarr;</a>
                    </div>
                @endif

                <form method="POST" action="{{ route('customer.conhecimento.publish-google') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                            Texto da Publicação (Editável)
                        </label>
                        <textarea name="summary" rows="5" required maxlength="1500"
                                  x-model="modalPost ? modalPost.texto_post : ''"
                                  class="w-full rounded-2xl border-gray-300 focus:border-orange-500 focus:ring-orange-500 text-sm leading-relaxed"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                                Botão de Ação
                            </label>
                            <select name="cta_type" x-model="modalPost ? modalPost.cta_recomendado : 'CALL'"
                                    class="w-full rounded-xl border-gray-300 text-xs font-bold">
                                <option value="CALL">Ligar Agora (CALL)</option>
                                <option value="BOOK">Agendar Horário (BOOK)</option>
                                <option value="ORDER">Fazer Pedido (ORDER)</option>
                                <option value="LEARN_MORE">Saiba Mais (LEARN_MORE)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                                Link do Botão (Opcional)
                            </label>
                            <input type="url" name="cta_url"
                                   placeholder="https://suaempresa.com.br"
                                   class="w-full rounded-xl border-gray-300 text-xs">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                            Adicionar Foto (Opcional)
                        </label>
                        <input type="file" name="image" accept="image/png,image/jpeg,image/jpg"
                               class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-[#0A1128] hover:file:bg-gray-200 cursor-pointer">
                        <p class="text-[11px] text-gray-500 mt-1" x-text="modalPost ? 'Sugestão: ' + modalPost.sugestao_foto : ''"></p>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                        <button type="button" @click="modalPost = null"
                                class="px-4 py-2.5 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-50 cursor-pointer">
                            Cancelar
                        </button>
                        <button type="submit"
                                {{ $googleConnected ? '' : 'disabled' }}
                                class="px-5 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 disabled:opacity-50 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-sm cursor-pointer">
                            <span>🚀</span>
                            <span>Confirmar e Publicar no Google</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
