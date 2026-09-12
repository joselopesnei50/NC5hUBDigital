<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold text-bruce uppercase tracking-widest mb-2">Integração</p>
            <h2 class="font-display font-bold text-3xl text-ink leading-tight">Google Meu Negócio</h2>
            <p class="text-slate text-sm mt-1">Conecte sua ficha do Google para gerenciar postagens e desempenho aqui no painel.</p>
        </div>
    </x-slot>

    <div class="space-y-6">

        @if(!empty($apiError))
            <div class="p-4 rounded-2xl bg-orange-50 border border-orange-200 text-orange-800 shadow-sm">
                <p class="font-bold text-sm mb-1">Aviso do Google</p>
                <p class="text-sm">{{ $apiError }}</p>
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

                    <form action="{{ route('customer.google-business.disconnect') }}" method="POST"
                          onsubmit="return confirm('Tem certeza que deseja desconectar? Você precisará autorizar novamente no Google para reconectar.');">
                        @csrf
                        <button type="submit"
                                class="text-xs font-bold text-rose-600 hover:text-white hover:bg-rose-600 px-4 py-2 rounded-full border border-rose-200 hover:border-rose-600 transition-colors">
                            Desconectar conta
                        </button>
                    </form>
                </div>

                {{-- Seletor de ficha (Location) --}}
                <div class="mb-6 p-4 bg-mist rounded-2xl border border-black/5">
                    <label for="location" class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Ficha (Location)</label>
                    <select id="location" name="location" disabled
                            class="w-full rounded-xl border-gray-300 focus:border-bruce focus:ring-bruce text-sm bg-white disabled:bg-white/60 disabled:cursor-not-allowed">
                        <option value="">Selecione uma ficha para gerenciar...</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc['name'] ?? '' }}">{{ $loc['title'] ?? 'Ficha sem título' }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate mt-2 flex items-center gap-1.5">
                        <span class="inline-flex items-center gap-1 bg-bruce/10 text-bruce font-bold text-[10px] uppercase tracking-wider px-2 py-0.5 rounded-full">Em breve</span>
                        Estamos finalizando a troca de ficha ativa e a sincronização de métricas.
                    </p>
                </div>

                {{-- Cards de Métricas (Em Breve) --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @php
                        $mockCards = [
                            ['label' => 'Impressões (30 dias)', 'icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z'],
                            ['label' => 'Cliques p/ Site', 'icon' => 'M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14'],
                            ['label' => 'Ligações Feitas', 'icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z'],
                        ];
                    @endphp
                    @foreach($mockCards as $card)
                        <div class="relative p-5 rounded-2xl border border-black/5 bg-white shadow-sm overflow-hidden">
                            <span class="absolute top-3 right-3 inline-flex items-center bg-bruce/10 text-bruce font-bold text-[10px] uppercase tracking-wider px-2 py-0.5 rounded-full">Em breve</span>
                            <div class="w-9 h-9 rounded-xl bg-mist text-slate flex items-center justify-center mb-4">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/></svg>
                            </div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate">{{ $card['label'] }}</p>
                            <p class="font-display text-3xl font-bold text-ink/40 mt-1">--</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
