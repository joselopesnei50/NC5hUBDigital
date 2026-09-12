<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold text-bruce uppercase tracking-widest mb-2">Portal do cliente</p>
            <h2 class="font-display font-bold text-3xl text-ink leading-tight">Bem-vindo(a), {{ Str::before(Auth::user()->name, ' ') }}.</h2>
            <p class="text-slate text-sm mt-1">Seu ecossistema com a NC5 Hub · {{ now()->translatedFormat('l, d \d\e F') }}</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Banner -->
        <div class="relative bg-ink rounded-3xl p-8 lg:p-10 text-white overflow-hidden shadow-xl shadow-ink/10">
            <div class="absolute -top-32 -right-24 w-80 h-80 bg-bruce/20 rounded-full blur-[100px] pointer-events-none"></div>
            <div class="relative flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div>
                    <p class="text-xs font-bold text-bruce uppercase tracking-widest mb-3">Sua conta</p>
                    <h1 class="font-display font-bold text-3xl md:text-4xl leading-tight">{{ $cliente->razao_social }}</h1>
                    <p class="text-white/60 mt-3 text-sm max-w-lg">Aqui você acompanha contratos, faturas, materiais em produção e briefings — tudo num só lugar.</p>
                </div>
                <a href="{{ route('customer.support') }}" class="bg-bruce hover:bg-white hover:text-ink text-white px-6 py-3 rounded-full font-bold text-sm transition-all shadow-lg whitespace-nowrap">
                    Precisa de ajuda?
                </a>
            </div>
        </div>

        @if($contratosPendentes > 0)
        <a href="{{ route('customer.contracts') }}" class="group block bg-bruce/5 border border-bruce/20 hover:border-bruce/40 rounded-2xl p-5 transition-colors">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 bg-bruce rounded-xl flex items-center justify-center text-white shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <p class="font-bold text-ink text-sm">Ação necessária: assinatura pendente</p>
                        <p class="text-slate text-xs">Você tem {{ $contratosPendentes }} contrato(s) aguardando sua assinatura.</p>
                    </div>
                </div>
                <span class="text-bruce font-bold text-sm group-hover:translate-x-1 transition-transform hidden sm:block">Assinar agora →</span>
            </div>
        </a>
        @endif

        <!-- KPIs -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="bg-white p-6 rounded-2xl border border-black/5 shadow-sm">
                <div class="w-11 h-11 bg-emerald-50 rounded-xl flex items-center justify-center mb-5 text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate">Status da conta</p>
                <p class="font-display text-3xl font-bold text-ink mt-1">Ativa</p>
                <p class="text-xs text-emerald-600 mt-2 font-bold uppercase tracking-wider">Operando</p>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-black/5 shadow-sm">
                <div class="w-11 h-11 {{ $faturasPendentes > 0 ? 'bg-orange-50 text-orange-600' : 'bg-emerald-50 text-emerald-600' }} rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate">Faturas pendentes</p>
                <p class="font-display text-3xl font-bold text-ink mt-1">{{ $faturasPendentes }}</p>
                <p class="text-xs mt-2 font-bold uppercase tracking-wider {{ $faturasPendentes > 0 ? 'text-orange-600' : 'text-emerald-600' }}">
                    {{ $faturasPendentes > 0 ? 'Atenção ao vencimento' : 'Tudo em dia' }}
                </p>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-black/5 shadow-sm">
                <div class="w-11 h-11 {{ $materiaisAguardando > 0 ? 'bg-bruce/10 text-bruce' : 'bg-emerald-50 text-emerald-600' }} rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate">Materiais p/ aprovar</p>
                <p class="font-display text-3xl font-bold text-ink mt-1">{{ $materiaisAguardando }}</p>
                <p class="text-xs mt-2 font-bold uppercase tracking-wider {{ $materiaisAguardando > 0 ? 'text-bruce' : 'text-emerald-600' }}">
                    {{ $materiaisAguardando > 0 ? 'Aguardando você' : 'Nada pendente' }}
                </p>
            </div>
        </div>

        <!-- Ações + dados -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <div class="lg:col-span-2 bg-white border border-black/5 rounded-2xl p-6 shadow-sm">
                <h3 class="font-display text-lg font-bold text-ink mb-5">Ações rápidas</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <a href="{{ route('customer.materiais') }}" class="group flex flex-col p-5 bg-mist hover:bg-ink hover:text-white rounded-2xl transition-colors">
                        <span class="text-slate group-hover:text-white/60 text-[10px] font-semibold uppercase tracking-wider">Materiais</span>
                        <span class="font-display text-lg font-bold mt-1">Aprovar</span>
                    </a>
                    <a href="{{ route('customer.invoices') }}" class="group flex flex-col p-5 bg-mist hover:bg-ink hover:text-white rounded-2xl transition-colors">
                        <span class="text-slate group-hover:text-white/60 text-[10px] font-semibold uppercase tracking-wider">Faturas</span>
                        <span class="font-display text-lg font-bold mt-1">Pagar</span>
                    </a>
                    <a href="{{ route('customer.briefings') }}" class="group flex flex-col p-5 bg-mist hover:bg-ink hover:text-white rounded-2xl transition-colors">
                        <span class="text-slate group-hover:text-white/60 text-[10px] font-semibold uppercase tracking-wider">Briefings</span>
                        <span class="font-display text-lg font-bold mt-1">Responder</span>
                    </a>
                    <a href="{{ route('customer.agent-drafts.index') }}" class="group flex flex-col p-5 bg-indigo-50 border border-indigo-100 hover:bg-[#0A1128] hover:border-transparent hover:text-white rounded-2xl transition-all shadow-sm">
                        <span class="text-indigo-600 group-hover:text-indigo-300 text-[10px] font-semibold uppercase tracking-wider">Mensagens IA</span>
                        <span class="font-display text-lg font-bold mt-1 text-indigo-900 group-hover:text-white">Revisar</span>
                    </a>
                </div>
            </div>

            <div class="bg-white border border-black/5 rounded-2xl p-6 shadow-sm">
                <h3 class="font-display text-lg font-bold text-ink mb-5">Dados da empresa</h3>
                <dl class="space-y-4 text-sm">
                    <div>
                        <dt class="text-xs font-bold text-slate uppercase tracking-wider">Razão social</dt>
                        <dd class="mt-1 text-ink font-semibold">{{ $cliente->razao_social }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold text-slate uppercase tracking-wider">Documento ({{ $cliente->tipo_pessoa }})</dt>
                        <dd class="mt-1 text-ink font-semibold">{{ $cliente->cpf_cnpj }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold text-slate uppercase tracking-wider">WhatsApp</dt>
                        <dd class="mt-1 text-ink font-semibold">{{ $cliente->telefone ?? '—' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Card discreto que anuncia o Bruce (substitui a caixa gigante) -->
        <div class="mt-2 flex items-center justify-between gap-4 rounded-2xl bg-[#0A0A0B] p-5 text-white shadow-sm">
            <div class="flex items-center gap-4">
                <img src="{{ asset('images/bruce/bruceia-icone-fundo-escuro.svg') }}" alt="BruceIA" class="w-12 h-12 shrink-0">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-[#FF7A1A]">Analista 24/7</p>
                    <p class="font-display text-lg font-bold leading-tight">Fale com o <span class="text-[#FF7A1A]">Bruce</span><span class="text-white">IA</span></p>
                    <p class="text-white/60 text-xs mt-1">Pergunte sobre caixa, clientes ou peça mensagens prontas.</p>
                </div>
            </div>
            <button
                type="button"
                @click="$dispatch('bruce-open')"
                class="hidden sm:inline-flex items-center gap-2 bg-[#FF7A1A] hover:bg-white hover:text-[#0A0A0B] text-white text-xs font-bold uppercase tracking-wider px-4 py-2.5 rounded-full transition-colors whitespace-nowrap"
            >
                Abrir chat →
            </button>
        </div>
    </div>

    <!-- ============================================
         Widget flutuante BruceIA (bubble + painel)
         ============================================ -->
    <div
        x-data="{
            open: false,
            init() {
                this.$watch('open', v => {
                    if (v) this.$nextTick(() => {
                        const c = document.getElementById('bruce-chat-messages');
                        if (c) c.scrollTop = c.scrollHeight;
                    });
                });
            }
        }"
        x-on:bruce-open.window="open = true"
        x-on:keydown.escape.window="open = false"
        class="fixed bottom-6 right-6 z-50 font-sans"
    >
        <!-- Botão flutuante -->
        <button
            type="button"
            @click="open = !open"
            x-show="!open"
            x-transition
            class="group relative flex items-center justify-center w-16 h-16 rounded-full bg-[#0A0A0B] shadow-2xl shadow-[#0A0A0B]/40 hover:shadow-[#FF7A1A]/40 transition-all hover:scale-105"
            aria-label="Abrir BruceIA"
        >
            <span class="absolute inset-0 rounded-full bg-[#FF7A1A]/20 animate-ping"></span>
            <img src="{{ asset('images/bruce/bruceia-icone-fundo-escuro.svg') }}" alt="BruceIA" class="relative w-11 h-11">
            <span class="absolute -top-1 -right-1 flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#FF7A1A] opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-[#FF7A1A]"></span>
            </span>
        </button>

        <!-- Painel flutuante -->
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-95"
            class="relative origin-bottom-right w-[calc(100vw-3rem)] sm:w-[380px] h-[70vh] sm:h-[560px] max-h-[calc(100vh-3rem)] bg-white rounded-3xl shadow-2xl shadow-[#0A0A0B]/25 border border-black/5 overflow-hidden flex flex-col"
            style="display: none;"
        >
            <livewire:bruce-chat />
            <button
                type="button"
                @click="open = false"
                class="absolute top-4 right-4 w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors"
                aria-label="Fechar"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
</x-app-layout>
