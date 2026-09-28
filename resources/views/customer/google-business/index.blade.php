<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold text-bruce uppercase tracking-widest mb-2">Integração</p>
            <h2 class="font-display font-bold text-3xl text-ink leading-tight">Google Meu Negócio</h2>
            <p class="text-slate text-sm mt-1">Conecte sua ficha do Google para gerenciar postagens e desempenho aqui no painel.</p>
        </div>
    </x-slot>

    <style>[x-cloak]{display:none!important}</style>

    <div class="space-y-6">

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm text-sm">
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(!empty($erros['fichas']))
            <div class="p-4 rounded-2xl bg-orange-50 border border-orange-200 text-orange-800 shadow-sm">
                <p class="font-bold text-sm mb-1">Aviso do Google</p>
                <p class="text-sm">{{ $erros['fichas'] }}</p>
                <p class="text-xs mt-2 opacity-75">Verifique se as APIs do Google Meu Negócio estão ativadas no Google Cloud Console.</p>
            </div>
        @endif

        @if(!$isConnected)
            {{-- ESTADO: NÃO CONECTADO --}}
            <div class="bg-white border border-black/5 rounded-3xl p-10 shadow-sm">
                <div class="max-w-2xl mx-auto text-center">
                    <div class="w-16 h-16 mx-auto mb-5 rounded-2xl bg-bruce/10 flex items-center justify-center text-bruce">
                        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12.24 10.285V14.4h6.806c-.275 1.765-2.056 5.174-6.806 5.174-4.095 0-7.439-3.389-7.439-7.574s3.345-7.574 7.439-7.574c2.33 0 3.891.989 4.785 1.849l3.254-3.138C18.189 1.186 15.479 0 12.24 0c-6.635 0-12 5.365-12 12s5.365 12 12 12c6.926 0 11.52-4.869 11.52-11.726 0-.788-.085-1.39-.189-1.989H12.24z"/>
                        </svg>
                    </div>
                    <h3 class="font-display text-2xl font-bold text-ink mb-2">Conecte sua conta do Google</h3>
                    <p class="text-slate text-sm max-w-md mx-auto mb-8">
                        Ao integrar sua ficha do Google Meu Negócio você poderá publicar posts, acompanhar métricas e responder avaliações direto daqui.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-left mb-8">
                        <div class="p-4 rounded-2xl bg-mist">
                            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            </div>
                            <p class="font-bold text-ink text-sm">Métricas de desempenho</p>
                            <p class="text-slate text-xs mt-1">Impressões, cliques e ligações da sua ficha.</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-mist">
                            <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </div>
                            <p class="font-bold text-ink text-sm">Publicações direto daqui</p>
                            <p class="text-slate text-xs mt-1">Programe posts sem sair do painel.</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-mist">
                            <div class="w-9 h-9 rounded-xl bg-bruce/10 text-bruce flex items-center justify-center mb-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <p class="font-bold text-ink text-sm">Seguro por padrão</p>
                            <p class="text-slate text-xs mt-1">Suas credenciais ficam criptografadas.</p>
                        </div>
                    </div>

                    <a href="{{ route('customer.google-business.connect') }}"
                       class="inline-flex items-center gap-2 px-6 py-3 bg-ink hover:bg-bruce text-white rounded-full font-bold text-sm transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.24 10.285V14.4h6.806c-.275 1.765-2.056 5.174-6.806 5.174-4.095 0-7.439-3.389-7.439-7.574s3.345-7.574 7.439-7.574c2.33 0 3.891.989 4.785 1.849l3.254-3.138C18.189 1.186 15.479 0 12.24 0c-6.635 0-12 5.365-12 12s5.365 12 12 12c6.926 0 11.52-4.869 11.52-11.726 0-.788-.085-1.39-.189-1.989H12.24z"/>
                        </svg>
                        Conectar ao Google Meu Negócio
                    </a>
                </div>
            </div>
        @else
            {{-- ESTADO: CONECTADO --}}
            <div class="bg-white border border-black/5 rounded-3xl p-6 sm:p-8 shadow-sm">
                <div class="flex items-start justify-between gap-4 flex-wrap mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="font-display font-bold text-ink text-lg">Conta conectada</p>
                            <p class="text-slate text-xs">Você pode alternar entre suas fichas abaixo.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        <a href="{{ route('customer.google-business.index', ['refresh' => 1]) }}"
                           class="text-xs font-bold text-ink hover:text-white hover:bg-ink px-4 py-2 rounded-full border border-black/10 hover:border-ink transition-colors">
                            Atualizar dados
                        </a>
                        <form action="{{ route('customer.google-business.disconnect') }}" method="POST"
                              onsubmit="return confirm('Tem certeza que deseja desconectar? Você precisará autorizar novamente no Google para reconectar.');">
                            @csrf
                            <button type="submit"
                                    class="text-xs font-bold text-rose-600 hover:text-white hover:bg-rose-600 px-4 py-2 rounded-full border border-rose-200 hover:border-rose-600 transition-colors">
                                Desconectar conta
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Seletor de ficha (Location) --}}
                <div class="mb-6 p-4 bg-mist rounded-2xl border border-black/5">
                    <form action="{{ route('customer.google-business.location') }}" method="POST">
                        @csrf
                        <label for="location" class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Ficha (Location)</label>
                        <select id="location" name="v4_name" onchange="this.form.submit()"
                                class="w-full rounded-xl border-gray-300 focus:border-bruce focus:ring-bruce text-sm bg-white">
                            @if(empty($locations))
                                <option value="">Nenhuma ficha disponível na sua conta Google.</option>
                            @else
                                <option value="">Selecione uma ficha para gerenciar...</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc['_v4_name'] ?? '' }}"
                                            @selected(($cliente->google_location_id ?? null) === ($loc['_v4_name'] ?? null))>
                                        {{ $loc['title'] ?? 'Ficha sem título' }}
                                        @if(!empty($loc['storefrontAddress']['locality']))
                                            — {{ $loc['storefrontAddress']['locality'] }}
                                        @endif
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </form>
                    @if($selected)
                        <p class="text-xs text-slate mt-2">
                            Gerenciando: <span class="font-bold text-ink">{{ $selected['title'] ?? '—' }}</span>
                            @if(!empty($selected['websiteUri']))
                                · <a href="{{ $selected['websiteUri'] }}" target="_blank" rel="noopener" class="text-bruce hover:underline">site</a>
                            @endif
                        </p>
                    @endif
                </div>

                @if(!$selected)
                    <div class="p-6 rounded-2xl bg-mist text-center text-slate text-sm">
                        Escolha uma ficha acima para ver métricas, posts e avaliações.
                    </div>
                @else
                    {{-- Métricas --}}
                    <div class="mb-8">
                        <h3 class="font-display text-lg font-bold text-ink mb-4">Desempenho nos últimos 30 dias</h3>

                        @if(!empty($erros['metricas']))
                            <div class="p-4 rounded-2xl bg-orange-50 border border-orange-200 text-orange-800 text-sm">
                                {{ $erros['metricas'] }}
                            </div>
                        @elseif($metricas)
                            @php
                                $atual    = $metricas['atual']    ?? [];
                                $anterior = $metricas['anterior'] ?? [];
                                $delta    = $metricas['delta']    ?? [];

                                $cards = [
                                    'impressoes'   => ['label' => 'Visualizações',   'valor' => $atual['impressoes']   ?? 0, 'valor_ant' => $anterior['impressoes']   ?? 0, 'delta' => $delta['impressoes']   ?? 0, 'serie' => $atual['serie_impressoes'] ?? []],
                                    'cliques_site' => ['label' => 'Cliques no site', 'valor' => $atual['cliques_site'] ?? 0, 'valor_ant' => $anterior['cliques_site'] ?? 0, 'delta' => $delta['cliques_site'] ?? 0, 'serie' => $atual['serie_cliques']    ?? []],
                                    'ligacoes'     => ['label' => 'Ligações',        'valor' => $atual['ligacoes']     ?? 0, 'valor_ant' => $anterior['ligacoes']     ?? 0, 'delta' => $delta['ligacoes']     ?? 0, 'serie' => $atual['serie_ligacoes']   ?? []],
                                    'rotas'        => ['label' => 'Pedidos de rota', 'valor' => $atual['rotas']        ?? 0, 'valor_ant' => $anterior['rotas']        ?? 0, 'delta' => $delta['rotas']        ?? 0, 'serie' => $atual['serie_rotas']      ?? []],
                                ];

                                // Helper de sparkline SVG (polyline). Devolve d="M x,y L x,y..."
                                $sparkPoints = function (array $serie): string {
                                    if (empty($serie)) return '';
                                    $valores = array_values($serie);
                                    $n = count($valores);
                                    $max = max($valores) ?: 1;
                                    $w = 100; $h = 30;
                                    $pontos = [];
                                    foreach ($valores as $i => $v) {
                                        $x = $n > 1 ? round(($i / ($n - 1)) * $w, 2) : 0;
                                        $y = round($h - (($v / $max) * $h), 2);
                                        $pontos[] = $x . ',' . $y;
                                    }
                                    return implode(' ', $pontos);
                                };

                                // Dados do gráfico principal
                                $labels = array_keys($atual['serie_impressoes'] ?? []);
                                $chartLabels     = array_map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'), $labels);
                                $chartImpressoes = array_values($atual['serie_impressoes'] ?? []);
                                $chartCliques    = array_values($atual['serie_cliques']    ?? []);

                                // Mobile vs Desktop
                                $mobile  = $atual['impressoes_mobile']  ?? 0;
                                $desktop = $atual['impressoes_desktop'] ?? 0;
                                $totalDevice = $mobile + $desktop;
                                $pctMobile  = $totalDevice > 0 ? round(($mobile  / $totalDevice) * 100, 1) : 0;
                                $pctDesktop = $totalDevice > 0 ? round(($desktop / $totalDevice) * 100, 1) : 0;
                            @endphp

                            {{-- 4 cards com sparkline + comparativo --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                @foreach($cards as $c)
                                    @php
                                        $d = $c['delta'];
                                        if ($d === null) { $badgeClass = 'bg-emerald-50 text-emerald-700'; $badgeText = 'novo'; }
                                        elseif ($d > 0)  { $badgeClass = 'bg-emerald-50 text-emerald-700'; $badgeText = '+' . number_format($d, 1, ',', '.') . '%'; }
                                        elseif ($d < 0)  { $badgeClass = 'bg-rose-50 text-rose-700';       $badgeText = number_format($d, 1, ',', '.') . '%'; }
                                        else             { $badgeClass = 'bg-slate-100 text-slate-600';    $badgeText = '0%'; }
                                    @endphp
                                    <div class="p-5 rounded-2xl border border-black/5 bg-white shadow-sm">
                                        <div class="flex items-start justify-between gap-2 mb-2">
                                            <p class="text-xs font-bold uppercase tracking-wider text-slate">{{ $c['label'] }}</p>
                                            <span class="inline-flex items-center text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full {{ $badgeClass }}">
                                                {{ $badgeText }}
                                            </span>
                                        </div>
                                        <p class="font-display text-3xl font-bold text-ink">{{ number_format($c['valor'], 0, ',', '.') }}</p>
                                        <div class="mt-3">
                                            @if(!empty($c['serie']))
                                                <svg viewBox="0 0 100 30" preserveAspectRatio="none" class="w-full h-8 text-bruce">
                                                    <polyline fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" points="{{ $sparkPoints($c['serie']) }}"/>
                                                </svg>
                                            @else
                                                <div class="h-8"></div>
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-slate mt-1">
                                            vs {{ number_format($c['valor_ant'], 0, ',', '.') }} nos 30 dias anteriores
                                        </p>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Gráfico principal + Mobile/Desktop --}}
                            <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-4">
                                <div class="lg:col-span-2 p-5 rounded-2xl border border-black/5 bg-white shadow-sm">
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate mb-3">Evolução diária</p>
                                    <div class="relative" style="height: 240px;">
                                        <canvas id="gbpChart"></canvas>
                                    </div>
                                    <p class="text-[11px] text-slate mt-3">
                                        Dados do Google com 2-3 dias de atraso — o período termina em {{ \Carbon\Carbon::parse($atual['periodo']['fim'])->format('d/m/Y') }}.
                                    </p>
                                </div>

                                <div class="p-5 rounded-2xl border border-black/5 bg-white shadow-sm">
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate mb-3">Onde você aparece</p>
                                    <div class="space-y-4">
                                        <div>
                                            <div class="flex items-center justify-between text-sm mb-1">
                                                <span class="font-bold text-ink flex items-center gap-1.5">
                                                    <svg class="w-4 h-4 text-bruce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                    Mobile
                                                </span>
                                                <span class="text-slate">{{ number_format($mobile, 0, ',', '.') }} · {{ number_format($pctMobile, 1, ',', '.') }}%</span>
                                            </div>
                                            <div class="w-full h-2 bg-mist rounded-full overflow-hidden">
                                                <div class="h-full bg-bruce" style="width: {{ $pctMobile }}%"></div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="flex items-center justify-between text-sm mb-1">
                                                <span class="font-bold text-ink flex items-center gap-1.5">
                                                    <svg class="w-4 h-4 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                    Desktop
                                                </span>
                                                <span class="text-slate">{{ number_format($desktop, 0, ',', '.') }} · {{ number_format($pctDesktop, 1, ',', '.') }}%</span>
                                            </div>
                                            <div class="w-full h-2 bg-mist rounded-full overflow-hidden">
                                                <div class="h-full bg-ink" style="width: {{ $pctDesktop }}%"></div>
                                            </div>
                                        </div>

                                        <div class="pt-3 border-t border-black/5 text-xs text-slate space-y-1">
                                            <div class="flex justify-between">
                                                <span>Busca</span>
                                                <span class="font-bold text-ink">{{ number_format($atual['impressoes_busca'] ?? 0, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>Maps</span>
                                                <span class="font-bold text-ink">{{ number_format($atual['impressoes_maps'] ?? 0, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Chart.js + inicialização --}}
                            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    var ctx = document.getElementById('gbpChart');
                                    if (!ctx || typeof Chart === 'undefined') return;
                                    new Chart(ctx, {
                                        type: 'line',
                                        data: {
                                            labels: @json($chartLabels),
                                            datasets: [
                                                {
                                                    label: 'Visualizações',
                                                    data: @json($chartImpressoes),
                                                    borderColor: '#FF7A1A',
                                                    backgroundColor: 'rgba(255, 122, 26, 0.12)',
                                                    borderWidth: 2,
                                                    pointRadius: 0,
                                                    pointHoverRadius: 4,
                                                    tension: 0.35,
                                                    fill: true,
                                                },
                                                {
                                                    label: 'Cliques no site',
                                                    data: @json($chartCliques),
                                                    borderColor: '#0A1128',
                                                    backgroundColor: 'transparent',
                                                    borderWidth: 2,
                                                    pointRadius: 0,
                                                    pointHoverRadius: 4,
                                                    tension: 0.35,
                                                    fill: false,
                                                },
                                            ],
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            interaction: { mode: 'index', intersect: false },
                                            plugins: {
                                                legend: { position: 'bottom', labels: { boxWidth: 12, boxHeight: 12, font: { size: 11 } } },
                                                tooltip: { backgroundColor: '#0A1128', padding: 10, cornerRadius: 8, displayColors: true },
                                            },
                                            scales: {
                                                x: { grid: { display: false }, ticks: { font: { size: 10 }, maxRotation: 0, autoSkipPadding: 20 } },
                                                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { font: { size: 10 }, precision: 0 } },
                                            },
                                        },
                                    });
                                });
                            </script>
                        @endif
                    </div>

                    {{-- Publicações --}}
                    <div class="mb-8" x-data="{ ctaType: '', chars: 0, imagePreview: null, imageName: '' }">
                        <h3 class="font-display text-lg font-bold text-ink mb-4">Publicar na ficha</h3>

                        <form action="{{ route('customer.google-business.posts.store') }}" method="POST" enctype="multipart/form-data"
                              class="p-5 rounded-2xl border border-black/5 bg-white shadow-sm space-y-4">
                            @csrf
                            <div>
                                <label for="summary" class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Conteúdo</label>
                                <textarea id="summary" name="summary" rows="4" required maxlength="1500"
                                          x-on:input="chars = $event.target.value.length"
                                          class="w-full rounded-xl border-gray-300 focus:border-bruce focus:ring-bruce text-sm"
                                          placeholder="Novidades da sua empresa...">{{ old('summary') }}</textarea>
                                <p class="text-[11px] text-slate mt-1"><span x-text="chars">0</span> / 1500 caracteres</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="cta_type" class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Botão de ação (opcional)</label>
                                    <select id="cta_type" name="cta_type" x-model="ctaType"
                                            class="w-full rounded-xl border-gray-300 focus:border-bruce focus:ring-bruce text-sm">
                                        <option value="">Sem botão</option>
                                        <option value="LEARN_MORE">Saiba mais</option>
                                        <option value="BOOK">Reservar</option>
                                        <option value="ORDER">Fazer pedido</option>
                                        <option value="SHOP">Comprar</option>
                                        <option value="SIGN_UP">Cadastrar-se</option>
                                        <option value="CALL">Ligar</option>
                                    </select>
                                </div>
                                <div x-show="ctaType && ctaType !== 'CALL'" x-cloak>
                                    <label for="cta_url" class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Link do botão</label>
                                    <input type="url" id="cta_url" name="cta_url" value="{{ old('cta_url') }}"
                                           class="w-full rounded-xl border-gray-300 focus:border-bruce focus:ring-bruce text-sm"
                                           placeholder="https://seusite.com.br/promocao">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Imagem (opcional)</label>
                                <label class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-4 rounded-xl border border-dashed border-gray-300 hover:border-bruce cursor-pointer transition-colors">
                                    <input type="file" name="image" accept="image/jpeg,image/png" class="hidden"
                                           x-on:change="
                                               var f = $event.target.files[0];
                                               if (!f) { imagePreview = null; imageName = ''; return; }
                                               imageName = f.name;
                                               var r = new FileReader();
                                               r.onload = function (e) { imagePreview = e.target.result; };
                                               r.readAsDataURL(f);
                                           ">
                                    <div x-show="!imagePreview" class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-xl bg-mist text-slate flex items-center justify-center">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-ink">Escolher imagem</p>
                                            <p class="text-[11px] text-slate">JPG ou PNG · mínimo 250×250 · até 5 MB</p>
                                        </div>
                                    </div>
                                    <div x-show="imagePreview" x-cloak class="flex items-center gap-3 w-full">
                                        <img :src="imagePreview" alt="prévia" class="w-16 h-16 rounded-xl object-cover border border-black/10">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-ink truncate" x-text="imageName"></p>
                                            <button type="button"
                                                    class="text-[11px] font-bold text-rose-600 hover:underline mt-1"
                                                    x-on:click.prevent="
                                                        imagePreview = null;
                                                        imageName = '';
                                                        $el.closest('label').querySelector('input[type=file]').value = '';
                                                    ">
                                                Remover imagem
                                            </button>
                                        </div>
                                    </div>
                                </label>
                                <p class="text-[11px] text-slate mt-1">A imagem é enviada ao Google via URL pública do painel.</p>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="px-5 py-2 bg-ink hover:bg-bruce text-white rounded-full text-sm font-bold transition-colors">
                                    Publicar agora
                                </button>
                            </div>
                        </form>

                        @if(!empty($erros['posts']))
                            <div class="mt-4 p-4 rounded-2xl bg-orange-50 border border-orange-200 text-orange-800 text-sm">
                                {{ $erros['posts'] }}
                            </div>
                        @elseif(!empty($posts))
                            <div class="mt-6 space-y-3">
                                <p class="text-xs font-bold uppercase tracking-wider text-slate">Publicações recentes</p>
                                @foreach($posts as $post)
                                    @php
                                        $state = $post['state'] ?? 'LIVE';
                                        $badge = match($state) {
                                            'LIVE'      => ['emerald', 'Publicado'],
                                            'REJECTED'  => ['rose',    'Recusado'],
                                            'PROCESSING'=> ['amber',   'Processando'],
                                            default     => ['slate',   $state],
                                        };
                                    @endphp
                                    <div class="p-4 rounded-2xl border border-black/5 bg-white flex items-start gap-4 flex-wrap">
                                        @php $postImg = $post['media'][0]['googleUrl'] ?? $post['media'][0]['sourceUrl'] ?? null; @endphp
                                        @if($postImg)
                                            <img src="{{ $postImg }}" alt="post" class="w-16 h-16 rounded-xl object-cover border border-black/10 flex-shrink-0">
                                        @endif
                                        <div class="flex-1 min-w-[200px]">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="inline-flex items-center bg-{{ $badge[0] }}-50 text-{{ $badge[0] }}-700 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full">
                                                    {{ $badge[1] }}
                                                </span>
                                                @if(!empty($post['createTime']))
                                                    <span class="text-[11px] text-slate">
                                                        {{ \Carbon\Carbon::parse($post['createTime'])->format('d/m/Y H:i') }}
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-ink whitespace-pre-line">{{ \Illuminate\Support\Str::limit($post['summary'] ?? '', 280) }}</p>
                                            @if(!empty($post['searchUrl']))
                                                <a href="{{ $post['searchUrl'] }}" target="_blank" rel="noopener"
                                                   class="inline-flex items-center gap-1 mt-2 text-[11px] font-bold text-bruce hover:underline">
                                                    Ver no Google →
                                                </a>
                                            @endif
                                        </div>
                                        @if(!empty($post['name']))
                                            <form action="{{ route('customer.google-business.posts.destroy') }}" method="POST"
                                                  onsubmit="return confirm('Remover esta publicação do Google?');">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="post_name" value="{{ $post['name'] }}">
                                                <button type="submit"
                                                        class="text-[11px] font-bold text-rose-600 hover:text-white hover:bg-rose-600 px-3 py-1.5 rounded-full border border-rose-200 hover:border-rose-600 transition-colors">
                                                    Remover
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Avaliações --}}
                    <div>
                        <div class="flex items-baseline gap-3 mb-4">
                            <h3 class="font-display text-lg font-bold text-ink">Avaliações</h3>
                            @if(!empty($reviews['total']))
                                <p class="text-sm text-slate">
                                    <span class="font-bold text-ink">{{ number_format($reviews['media'], 1, ',', '.') }}</span>
                                    ★ · {{ number_format($reviews['total'], 0, ',', '.') }}
                                    {{ $reviews['total'] === 1 ? 'avaliação' : 'avaliações' }}
                                </p>
                            @endif
                        </div>

                        @if(!empty($erros['reviews']))
                            <div class="p-4 rounded-2xl bg-orange-50 border border-orange-200 text-orange-800 text-sm">
                                {{ $erros['reviews'] }}
                            </div>
                        @elseif(empty($reviews['reviews']))
                            <div class="p-6 rounded-2xl bg-mist text-center text-slate text-sm">
                                Ainda não há avaliações nesta ficha.
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach($reviews['reviews'] as $review)
                                    @php
                                        $estrelas = \App\Services\GoogleBusinessProfileService::estrelas($review['starRating'] ?? null);
                                        $reviewer = $review['reviewer']['displayName'] ?? 'Cliente';
                                        $reviewComment = $review['comment'] ?? '';
                                        $reply = $review['reviewReply']['comment'] ?? null;
                                    @endphp
                                    <div class="p-4 rounded-2xl border border-black/5 bg-white">
                                        <div class="flex items-center justify-between gap-3 mb-2">
                                            <div class="flex items-center gap-2">
                                                <span class="text-amber-500 text-sm">
                                                    {{ str_repeat('★', $estrelas) }}<span class="text-slate/40">{{ str_repeat('★', max(0, 5 - $estrelas)) }}</span>
                                                </span>
                                                <span class="text-sm font-bold text-ink">{{ $reviewer }}</span>
                                            </div>
                                            @if(!empty($review['updateTime']))
                                                <span class="text-[11px] text-slate">
                                                    {{ \Carbon\Carbon::parse($review['updateTime'])->format('d/m/Y') }}
                                                </span>
                                            @endif
                                        </div>
                                        @if($reviewComment)
                                            <p class="text-sm text-ink whitespace-pre-line">{{ $reviewComment }}</p>
                                        @else
                                            <p class="text-sm text-slate italic">(cliente não deixou comentário)</p>
                                        @endif

                                        @if($reply)
                                            <div class="mt-3 p-3 rounded-xl bg-mist">
                                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate mb-1">Sua resposta</p>
                                                <p class="text-sm text-ink whitespace-pre-line">{{ $reply }}</p>
                                            </div>
                                        @endif

                                        <details class="mt-3">
                                            <summary class="cursor-pointer text-[11px] font-bold uppercase tracking-wider text-bruce hover:text-ink transition-colors">
                                                {{ $reply ? 'Editar resposta' : 'Responder' }}
                                            </summary>
                                            <form action="{{ route('customer.google-business.reviews.reply') }}" method="POST" class="mt-3 space-y-2">
                                                @csrf
                                                <input type="hidden" name="review_name" value="{{ $review['name'] ?? '' }}">
                                                <textarea name="comment" rows="3" required maxlength="4000"
                                                          class="w-full rounded-xl border-gray-300 focus:border-bruce focus:ring-bruce text-sm"
                                                          placeholder="Responda com educação...">{{ $reply }}</textarea>
                                                <button type="submit" class="px-4 py-1.5 bg-ink hover:bg-bruce text-white rounded-full text-xs font-bold transition-colors">
                                                    Enviar resposta
                                                </button>
                                            </form>
                                        </details>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-app-layout>
