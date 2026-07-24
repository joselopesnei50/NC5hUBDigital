<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-display font-extrabold text-2xl text-ink leading-tight tracking-tight">
                    Visão Geral
                </h2>
                <p class="text-slateText text-sm mt-0.5">Painel de controle e acompanhamento de serviços da {{ Auth::user()->cliente->razao_social ?? 'sua empresa' }}.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('customer.materiais') }}" class="bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-5 py-2.5 rounded-2xl text-xs font-bold transition-all shadow-md flex items-center gap-2">
                    Aprovar Materiais
                </a>
                <a href="{{ route('customer.support') }}" class="bg-[#0A1128] hover:bg-[#E63888] text-white px-5 py-2.5 rounded-2xl text-xs font-bold transition-all shadow-md">
                    Abrir Chamado
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        <!-- Dashboard Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Stat Card 1 -->
            <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-premium relative overflow-hidden">
                <p class="text-xs font-extrabold text-slateText uppercase tracking-wider mb-1">Status da Conta</p>
                <h3 class="text-3xl font-display font-extrabold text-ink mb-2">Ativa</h3>
                <div class="flex items-center text-xs text-emerald-700 font-bold">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                    Serviços operando normalmente
                </div>
            </div>

            <!-- Stat Card 2 -->
            <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-premium relative overflow-hidden">
                <p class="text-xs font-extrabold text-slateText uppercase tracking-wider mb-1">Materiais para Aprovação</p>
                <h3 class="text-3xl font-display font-extrabold text-[#FF7A1A] mb-2">
                    {{ Auth::user()->cliente ? Auth::user()->cliente->materiais()->where('status_aprovacao', 'pendente')->count() : 0 }}
                </h3>
                <div class="flex items-center text-xs text-slateText font-medium">
                    Aguardando sua validação
                </div>
            </div>

            <!-- Stat Card 3 -->
            <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-premium relative overflow-hidden">
                <p class="text-xs font-extrabold text-slateText uppercase tracking-wider mb-1">Faturas Pendentes</p>
                <h3 class="text-3xl font-display font-extrabold text-ink mb-2">
                    {{ Auth::user()->cliente ? Auth::user()->cliente->faturas()->where('status', 'pendente')->count() : 0 }}
                </h3>
                <div class="flex items-center text-xs text-slateText font-medium">
                    Situação financeira em dia
                </div>
            </div>
        </div>

        <!-- Active Services Table Section -->
        <div class="bg-white border border-slate-200/80 rounded-3xl overflow-hidden shadow-premium">
            <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-display font-extrabold text-ink">Esteira de Produção & Contratos</h3>
                    <p class="text-xs text-slateText mt-0.5">Visão consolidada dos seus contratos ativos e materiais cadastrados.</p>
                </div>
                <a href="{{ route('customer.contracts') }}" class="text-xs font-bold text-[#FF7A1A] hover:underline">Ver todos os contratos →</a>
            </div>
            <div class="p-8">
                <p class="text-sm text-slate-600 leading-relaxed font-medium">
                    Acesse o menu ao lado para visualizar <strong class="text-ink">Meus Contratos</strong>, baixar faturas ou responder aos briefings e chamados de suporte da sua conta.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
