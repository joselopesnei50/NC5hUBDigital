@php
    $indexRoute      = $routePrefix . 'index';
    $connectRoute    = $routePrefix . 'connect';
    $disconnectRoute = $routePrefix . 'accounts.disconnect';
    $destroyAccRoute = $routePrefix . 'accounts.destroy';
    $storePostRoute  = $routePrefix . 'posts.store';
    $retryPostRoute  = $routePrefix . 'posts.retry';
    $destroyPostRoute= $routePrefix . 'posts.destroy';
    $captionAiRoute  = $routePrefix . 'posts.generate-caption';
@endphp

<div class="space-y-8" x-data="socialMediaHub()">

    @if(!$metaConfigured)
        <div class="bg-amber-50 border border-amber-200 text-amber-900 px-5 py-4 rounded-2xl text-sm">
            <strong>Configuração do Meta App pendente:</strong> O administrador precisa preencher o <code>Meta App ID</code> e o <code>Meta App Secret</code> (do app <strong>NC5HUBDIGITAL-EMP</strong>) em <strong>Configurações Globais</strong> para habilitar o login com Facebook e Instagram.
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl text-sm">
            <p class="font-bold mb-1">Verifique os campos do post:</p>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- =====================================================================
         SEÇÃO 1: CONTAS CONECTADAS (FACEBOOK PAGES + INSTAGRAM BUSINESS)
         ===================================================================== --}}
    <div class="bg-white border border-black/5 rounded-3xl p-6 sm:p-8 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <span class="text-[11px] font-extrabold uppercase tracking-widest text-bruce">Integração Oficial Meta Graph API v22</span>
                <h3 class="font-display font-bold text-2xl text-ink mt-0.5">Contas Conectadas (Instagram &amp; Facebook)</h3>
                <p class="text-xs text-slate mt-1">Conecte sua Página do Facebook vinculada ao seu perfil profissional do Instagram para agendar e publicar direto do painel.</p>
            </div>

            <a href="{{ route($connectRoute) }}"
               class="inline-flex items-center justify-center gap-2.5 px-5 py-3 bg-[#1877F2] hover:bg-blue-700 text-white rounded-xl font-bold text-xs shadow-sm transition-colors self-start">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                {{ $accounts->isEmpty() ? 'Conectar Facebook & Instagram' : 'Conectar / Atualizar Contas' }}
            </a>
        </div>

        @if($accounts->isEmpty())
            <div class="bg-slate-50 border border-dashed border-slate-300 rounded-2xl p-8 text-center">
                <p class="font-bold text-ink text-sm">Nenhuma página conectada ainda</p>
                <p class="text-xs text-slate mt-1 max-w-md mx-auto">Clique em <strong>Conectar Facebook &amp; Instagram</strong> acima, autorize as páginas que você administra e o sistema detectará automaticamente o seu Instagram Business vinculado.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($accounts as $acc)
                    <div class="border {{ $acc->is_active ? 'border-slate-200 bg-white' : 'border-slate-200/60 bg-slate-50 opacity-75' }} rounded-2xl p-4 flex flex-col justify-between gap-4">
                        <div class="flex items-start gap-3.5">
                            @if($acc->page_picture)
                                <img src="{{ $acc->page_picture }}" alt="{{ $acc->page_name }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200 flex-shrink-0">
                            @else
                                <div class="w-12 h-12 rounded-xl bg-blue-100 text-[#1877F2] font-bold flex items-center justify-center flex-shrink-0">
                                    {{ mb_substr($acc->page_name, 0, 1) }}
                                </div>
                            @endif
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <h4 class="font-bold text-sm text-ink truncate">{{ $acc->page_name }}</h4>
                                    @if($acc->is_active && !$acc->isTokenExpired())
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-800">Ativa</span>
                                    @elseif($acc->isTokenExpired())
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-rose-100 text-rose-800">Expirada</span>
                                    @else
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-200 text-slate-700">Inativa</span>
                                    @endif
                                </div>

                                <div class="mt-1.5 space-y-1 text-xs">
                                    <div class="text-slate flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-[#1877F2]"></span>
                                        <span>Facebook Page ID: <code class="text-[11px]">{{ $acc->page_id }}</code></span>
                                    </div>
                                    @if($acc->instagram_business_id)
                                        <div class="text-pink-700 font-semibold flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-pink-600"></span>
                                            <span>Instagram: {{ $acc->instagram_username ? '@' . $acc->instagram_username : 'Vinculado' }}</span>
                                        </div>
                                    @else
                                        <div class="text-amber-700 text-[11px]">Sem Instagram Business vinculado à Página</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate">
                            <span>
                                @if($acc->token_expires_at)
                                    Token até {{ $acc->token_expires_at->format('d/m/Y') }}
                                @endif
                            </span>
                            <div class="flex items-center gap-2">
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

    {{-- =====================================================================
         SEÇÃO 2: CRIADOR DE POSTS + ASSISTENTE DE CONTEÚDO BRUCEIA
         ===================================================================== --}}
    <div class="bg-white border border-black/5 rounded-3xl p-6 sm:p-8 shadow-sm">
        <div class="mb-6">
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-bruce">Publicador Multi-Formato + BruceIA</span>
            <h3 class="font-display font-bold text-2xl text-ink mt-0.5">Criar ou Agendar Publicação</h3>
            <p class="text-xs text-slate mt-1">Publique no Feed, Reels, Carrossel (até 10 fotos/vídeos) ou Stories 24h, com legenda criada pela BruceIA usando a memória da sua empresa.</p>
        </div>

        <form method="POST" action="{{ route($storePostRoute) }}" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            @csrf

            <div class="lg:col-span-7 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-1.5">Conta Social</label>
                        <select name="social_account_id" class="w-full rounded-xl border-slate-300 text-sm focus:border-ink focus:ring-ink">
                            @forelse($activeAccounts as $acc)
                                <option value="{{ $acc->id }}">
                                    {{ $acc->page_name }} {{ $acc->instagram_username ? '(@' . $acc->instagram_username . ')' : '' }}
                                </option>
                            @empty
                                <option value="">Salvar apenas como Rascunho</option>
                            @endforelse
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-1.5">Onde Publicar *</label>
                        <select name="platform" x-model="platform" class="w-full rounded-xl border-slate-300 text-sm focus:border-ink focus:ring-ink">
                            <option value="both">Instagram + Facebook</option>
                            <option value="instagram">Somente Instagram</option>
                            <option value="facebook">Somente Facebook</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-1.5">Formato *</label>
                        <select name="format" x-model="format" class="w-full rounded-xl border-slate-300 text-sm focus:border-ink focus:ring-ink">
                            <option value="feed">Feed / Carrossel / Reels</option>
                            <option value="story">Story 24h (Instagram)</option>
                        </select>
                    </div>
                </div>

                {{-- Assistente de Legenda com a BruceIA --}}
                <div class="bg-slate-50 border border-slate-200/90 rounded-2xl p-4 space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs font-bold text-ink flex items-center gap-2">
                            <img src="{{ asset('images/bruce/bruceia-icone-fundo-claro.svg') }}" alt="BruceIA" class="w-4 h-4">
                            Assistente de Copywriting BruceIA
                        </span>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-bruce bg-bruce/10 px-2 py-0.5 rounded-md">Usa o DNA do Negócio</span>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <input type="text" x-model="aiTopic"
                               placeholder="Sobre o que é o post? Ex: Oferta da semana, bastidores, dica prática, depoimento..."
                               class="flex-1 rounded-xl border-slate-300 text-xs focus:border-ink focus:ring-ink">
                        <button type="button" @click="generateCaptionWithAi()" :disabled="aiLoading"
                                class="px-4 py-2.5 bg-ink hover:bg-bruce text-white rounded-xl text-xs font-bold transition-colors whitespace-nowrap disabled:opacity-50">
                            <span x-show="!aiLoading">✨ Gerar Legenda com IA</span>
                            <span x-show="aiLoading">Criando legenda...</span>
                        </button>
                    </div>
                    <p x-show="aiError" x-text="aiError" class="text-xs text-rose-600 font-semibold"></p>
                </div>

                {{-- Campo de Legenda --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-ink uppercase tracking-wider">Legenda da Publicação *</label>
                        <span class="text-[11px] text-slate" x-text="(caption ? caption.length : 0) + ' / 2200 caracteres'"></span>
                    </div>
                    <textarea name="caption" x-model="caption" rows="6" maxlength="2200" required
                              placeholder="Escreva a legenda do post ou peça para a BruceIA gerar acima..."
                              class="w-full rounded-2xl border-slate-300 text-sm focus:border-ink focus:ring-ink leading-relaxed">{{ old('caption') }}</textarea>
                </div>

                {{-- Upload de Mídia (Foto, Vídeo/Reels ou Carrossel até 10 itens) --}}
                <div>
                    <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-1.5">
                        Fotos ou Vídeos <span class="text-slate font-normal normal-case">(Selecione 1 arquivo para Post/Reels ou de 2 a 10 arquivos para Carrossel)</span>
                    </label>
                    <input type="file" name="media_files[]" multiple accept="image/jpeg,image/png,image/webp,video/mp4"
                           class="w-full text-xs text-slate file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-ink hover:file:bg-slate-200 border border-slate-200 rounded-2xl p-2">
                </div>
            </div>

            {{-- Coluna Direita: Agendamento / Disparo Imediato --}}
            <div class="lg:col-span-5 bg-slate-50 border border-slate-200/80 rounded-3xl p-6 space-y-5">
                <h4 class="font-display font-bold text-lg text-ink">Quando publicar?</h4>

                <div class="space-y-3">
                    <label class="flex items-start gap-3 p-3.5 rounded-2xl border cursor-pointer transition-all"
                           :class="publishNow ? 'bg-white border-bruce ring-1 ring-bruce' : 'bg-white/60 border-slate-200'">
                        <input type="radio" name="publish_now" value="1" x-model="publishNowRadio" class="mt-0.5 text-bruce focus:ring-bruce">
                        <div>
                            <span class="block text-sm font-bold text-ink">Publicar Agora Imediatamente</span>
                            <span class="block text-xs text-slate mt-0.5">Dispara na mesma hora para o Facebook e/ou Instagram selecionados.</span>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-3.5 rounded-2xl border cursor-pointer transition-all"
                           :class="!publishNow ? 'bg-white border-bruce ring-1 ring-bruce' : 'bg-white/60 border-slate-200'">
                        <input type="radio" name="publish_now" value="0" x-model="publishNowRadio" class="mt-0.5 text-bruce focus:ring-bruce">
                        <div class="flex-1">
                            <span class="block text-sm font-bold text-ink">Agendar Data e Horário</span>
                            <span class="block text-xs text-slate mt-0.5">O robô publica sozinho no horário programado.</span>
                            <div x-show="!publishNow" class="mt-3">
                                <input type="datetime-local" name="scheduled_at"
                                       value="{{ old('scheduled_at', now()->addHours(2)->format('Y-m-d\TH:i')) }}"
                                       class="w-full rounded-xl border-slate-300 text-xs focus:border-ink focus:ring-ink">
                            </div>
                        </div>
                    </label>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 px-6 bg-bruce hover:bg-bruceDark text-white rounded-2xl font-bold text-sm shadow-md transition-all">
                        <span x-show="publishNow">🚀 Publicar Agora nas Redes Sociais</span>
                        <span x-show="!publishNow">📅 Confirmar Agendamento do Post</span>
                    </button>
                </div>

                <div class="text-[11px] text-slate space-y-1 pt-2 border-t border-slate-200/80">
                    <p>• <strong>Carrossel Instagram:</strong> envie de 2 a 10 fotos/vídeos.</p>
                    <p>• <strong>Reels Instagram:</strong> envie 1 vídeo MP4 vertical (9:16).</p>
                    <p>• <strong>Stories 24h:</strong> selecione o formato "Story 24h (Instagram)".</p>
                </div>
            </div>
        </form>
    </div>

    {{-- =====================================================================
         SEÇÃO 3: CALENDÁRIO E FILA DE PUBLICAÇÕES
         ===================================================================== --}}
    <div class="bg-white border border-black/5 rounded-3xl p-6 sm:p-8 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h3 class="font-display font-bold text-xl text-ink">Calendário &amp; Histórico de Publicações</h3>
                <p class="text-xs text-slate mt-0.5">Acompanhe seus posts agendados, publicados e rascunhos.</p>
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
                    @php $activeTab = $filter === $key; @endphp
                    <a href="{{ route($indexRoute, $key ? ['status' => $key] : []) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition-colors {{ $activeTab ? 'bg-ink text-white' : 'bg-slate-100 text-slate hover:text-ink' }}">
                        {{ $tab['label'] }} ({{ $tab['count'] }})
                    </a>
                @endforeach
            </div>
        </div>

        @if($posts->isEmpty())
            <div class="text-center py-10 text-slate text-sm">
                Nenhuma publicação encontrada neste filtro.
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
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate">
                                        {{ $post->platform === 'both' ? 'IG + FB' : strtoupper($post->platform) }} · {{ strtoupper($post->format ?? 'FEED') }}
                                    </span>
                                </div>

                                <p class="text-xs text-ink line-clamp-4 whitespace-pre-line leading-relaxed">{{ $post->caption }}</p>

                                @if($post->status === 'failed' && $post->error_message)
                                    <div class="mt-2.5 p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-[11px] text-rose-700">
                                        <strong>Motivo:</strong> {{ $post->error_message }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="px-4 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate">
                            <span>{{ $post->scheduled_at ? $post->scheduled_at->format('d/m/Y H:i') : '—' }}</span>

                            <div class="flex items-center gap-2.5">
                                @if($post->status !== 'published' && $post->account)
                                    <form method="POST" action="{{ route($retryPostRoute, $post) }}">
                                        @csrf
                                        <button type="submit" class="text-[#1877F2] hover:underline font-bold">
                                            {{ $post->status === 'failed' ? 'Tentar de novo' : 'Publicar agora' }}
                                        </button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route($destroyPostRoute, $post) }}" onsubmit="return confirm('Excluir este post do histórico?');">
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
</div>

<script>
    function socialMediaHub() {
        return {
            platform: 'both',
            format: 'feed',
            publishNowRadio: '0',
            caption: @json(old('caption', '')),
            aiTopic: '',
            aiLoading: false,
            aiError: '',
            get publishNow() {
                return this.publishNowRadio === '1';
            },
            async generateCaptionWithAi() {
                if (!this.aiTopic.trim()) {
                    this.aiError = 'Digite sobre qual tema ou oferta você quer que a BruceIA escreva.';
                    return;
                }
                this.aiError = '';
                this.aiLoading = true;
                try {
                    const res = await fetch('{{ route($captionAiRoute) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            topic: this.aiTopic,
                            platform: this.platform,
                            format: this.format,
                        }),
                    });
                    const data = await res.json();
                    if (!res.ok || data.error) {
                        this.aiError = data.error || 'Falha ao gerar legenda.';
                    } else if (data.caption) {
                        this.caption = data.caption;
                    }
                } catch (e) {
                    this.aiError = 'Erro de conexão ao consultar a BruceIA.';
                } finally {
                    this.aiLoading = false;
                }
            }
        };
    }
</script>
