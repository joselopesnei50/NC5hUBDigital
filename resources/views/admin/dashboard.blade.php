<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:justify-between md:items-end gap-6 mb-2">
            <div>
                <h2 class="font-display font-extrabold text-3xl md:text-4xl text-[#0A1128] leading-tight tracking-tight">
                    Boa {{ now()->hour < 12 ? 'manhã' : (now()->hour < 18 ? 'tarde' : 'noite') }}, {{ Str::before(Auth::user()->name, ' ') }}.
                </h2>
                <p class="text-[#64748B] text-sm mt-2 font-medium">Visão consolidada da operação · {{ now()->translatedFormat('l, d \d\e F \d\e Y') }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.lembretes.create') }}" class="bg-white hover:bg-gray-50 border border-gray-200 text-[#0A1128] px-5 py-2.5 rounded-xl text-sm font-bold transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#FF7A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    Avisos Rápidos
                </a>
                <a href="{{ route('admin.clientes.create') }}" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    Novo Cliente
                </a>
            </div>
        </div>
    </x-slot>

    <!-- KPIs Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-5 mb-10">
        <!-- Faturamento (Hero Card) -->
        <div class="lg:col-span-2 bg-gradient-to-br from-[#0A1128] to-[#141D38] rounded-2xl p-7 text-white relative overflow-hidden border border-white/10">
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-white/[0.06] rounded-xl flex items-center justify-center border border-white/10">
                        <svg class="w-5 h-5 text-[#FF7A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-white/70">Faturamento Mensal</span>
                </div>
                <h3 class="font-display text-4xl font-extrabold text-white tracking-tight mb-2">R$ {{ number_format($faturamentoMes, 2, ',', '.') }}</h3>
                <div class="flex items-center gap-2 mt-4">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white/[0.06] text-white text-[11px] font-semibold border border-white/10">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                        {{ $faturasPendentes }} pendentes
                    </span>
                    <a href="{{ route('admin.faturas.index') }}" class="text-[11px] font-bold text-white/50 hover:text-white uppercase tracking-wider transition-colors ml-2">Gerenciar &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Clientes Ativos -->
        <div class="bg-white rounded-2xl p-7 border border-gray-100 hover:border-gray-200 transition-colors relative overflow-hidden">
            <span class="absolute inset-y-0 left-0 w-1 bg-emerald-500"></span>
            <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 mb-5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <p class="text-[11px] font-bold uppercase tracking-widest text-[#64748B] mb-1">Clientes Ativos</p>
            <h3 class="font-display text-3xl font-extrabold text-[#0A1128]">{{ $clientesAtivos }}</h3>
        </div>

        <!-- Contratos -->
        <div class="bg-white rounded-2xl p-7 border border-gray-100 hover:border-gray-200 transition-colors relative overflow-hidden">
            <span class="absolute inset-y-0 left-0 w-1 bg-blue-500"></span>
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 mb-5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <p class="text-[11px] font-bold uppercase tracking-widest text-[#64748B] mb-1">Contratos Vigentes</p>
            <h3 class="font-display text-3xl font-extrabold text-[#0A1128]">{{ $contratosVigentes }}</h3>
        </div>

        <!-- Leads (IA) -->
        <div class="bg-white rounded-2xl p-7 border border-gray-100 hover:border-gray-200 transition-colors relative overflow-hidden">
            <span class="absolute inset-y-0 left-0 w-1 bg-[#FF7A1A]"></span>
            <div class="w-10 h-10 bg-[#FF7A1A]/10 rounded-xl flex items-center justify-center text-[#FF7A1A] mb-5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <p class="text-[11px] font-bold uppercase tracking-widest text-[#64748B] mb-1">Leads gerados (mês)</p>
            <h3 class="font-display text-3xl font-extrabold text-[#0A1128]">{{ $leadsNoMes ?? 0 }}</h3>
        </div>

        <!-- Tickets / Suporte -->
        <div class="bg-white rounded-2xl p-7 border border-gray-100 hover:border-gray-200 transition-colors relative overflow-hidden">
            <span class="absolute inset-y-0 left-0 w-1 bg-rose-500"></span>
            <div class="flex justify-between items-start mb-5">
                <div class="w-10 h-10 bg-rose-50 rounded-xl flex items-center justify-center text-rose-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                @if($ticketsAbertos > 0)
                    <span class="flex h-3 w-3 relative">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500"></span>
                    </span>
                @endif
            </div>
            <p class="text-[11px] font-bold uppercase tracking-widest text-[#64748B] mb-1">Tickets Abertos</p>
            <h3 class="font-display text-3xl font-extrabold text-[#0A1128]">{{ $ticketsAbertos }}</h3>
        </div>
    </div>

    <!-- TABS: Organização de Leads e Clientes -->
    <div x-data="{ tab: 'leads' }" class="bg-white rounded-[24px] shadow-sm border border-gray-100 overflow-hidden">
        
        <!-- Header da Caixa de Listagem -->
        <div class="border-b border-gray-100 px-8 py-5 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gray-50/50">
            <div class="flex gap-6 border-b border-transparent">
                <button @click="tab = 'leads'" :class="tab === 'leads' ? 'text-[#0A1128] border-[#FF7A1A]' : 'text-[#8A8F9C] border-transparent hover:text-[#0A1128]'" class="pb-4 font-bold text-sm border-b-2 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Leads Recentes (IA)
                    <span class="bg-gray-200 text-gray-600 py-0.5 px-2 rounded-full text-[10px] ml-1">{{ count($ultimosLeads) }}</span>
                </button>
                <button @click="tab = 'clientes'" :class="tab === 'clientes' ? 'text-[#0A1128] border-[#FF7A1A]' : 'text-[#8A8F9C] border-transparent hover:text-[#0A1128]'" class="pb-4 font-bold text-sm border-b-2 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    Novos Clientes
                </button>
            </div>
            
            <div>
                <a x-show="tab === 'leads'" href="{{ route('admin.leads.index') }}" class="text-[11px] font-bold text-[#8A8F9C] hover:text-[#FF7A1A] uppercase tracking-wider transition-colors inline-flex items-center gap-1">Ver Gestão de Leads &rarr;</a>
                <a x-show="tab === 'clientes'" href="{{ route('admin.clientes.index') }}" class="text-[11px] font-bold text-[#8A8F9C] hover:text-[#FF7A1A] uppercase tracking-wider transition-colors inline-flex items-center gap-1" style="display: none;">Ver Todos os Clientes &rarr;</a>
            </div>
        </div>

        <!-- Conteúdo: LEADS -->
        <div x-show="tab === 'leads'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="overflow-x-auto">
            @if($ultimosLeads->isEmpty())
                <div class="p-16 text-center">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <h4 class="text-lg font-bold text-[#0A1128] mb-1">Nenhum Lead Capturado</h4>
                    <p class="text-[#8A8F9C] text-sm max-w-md mx-auto">Os leads gerados através da sua página pública de Análise Gratuita aparecerão organizados aqui.</p>
                </div>
            @else
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white text-[10px] uppercase tracking-widest text-[#8A8F9C] border-b border-gray-100">
                            <th class="py-4 px-8 font-bold">Contato</th>
                            <th class="py-4 px-8 font-bold">Tipo de IA</th>
                            <th class="py-4 px-8 font-bold text-center">Tamanho (Seguidores)</th>
                            <th class="py-4 px-8 font-bold text-right">Data de Entrada</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($ultimosLeads as $lead)
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="py-4 px-8">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-[#0A1128]/5 flex items-center justify-center text-[#0A1128] font-bold text-sm">
                                            {{ strtoupper(substr($lead->nome, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-sm text-[#0A1128]">{{ $lead->nome }}</p>
                                            <p class="text-xs text-[#8A8F9C]">{{ $lead->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-8">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-gray-100 text-[#0A1128] text-xs font-semibold">
                                        {{ str_replace('_', ' ', $lead->tipo_analise) }}
                                    </span>
                                </td>
                                <td class="py-4 px-8 text-center">
                                    @if($lead->tipo_analise === 'redes_sociais' && !is_null($lead->ig_followers))
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-[#FF7A1A]/10 text-[#FF7A1A] text-xs font-bold border border-[#FF7A1A]/20">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path></svg>
                                            {{ number_format($lead->ig_followers, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-gray-300 text-sm">—</span>
                                    @endif
                                </td>
                                <td class="py-4 px-8 text-right">
                                    <p class="text-sm font-medium text-[#0A1128]">{{ $lead->created_at->format('d M') }}</p>
                                    <p class="text-xs text-[#8A8F9C]">{{ $lead->created_at->diffForHumans() }}</p>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Conteúdo: CLIENTES -->
        <div x-show="tab === 'clientes'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;" class="overflow-x-auto">
            @if($ultimosClientes->isEmpty())
                <div class="p-16 text-center">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <h4 class="text-lg font-bold text-[#0A1128] mb-1">Sem Clientes Ainda</h4>
                    <p class="text-[#8A8F9C] text-sm max-w-md mx-auto mb-6">Cadastre seu primeiro cliente para começar a gerar contratos e faturas.</p>
                    <a href="{{ route('admin.clientes.create') }}" class="bg-[#0A1128] text-white px-5 py-2 rounded-xl text-sm font-bold shadow-lg">Criar Cliente</a>
                </div>
            @else
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white text-[10px] uppercase tracking-widest text-[#8A8F9C] border-b border-gray-100">
                            <th class="py-4 px-8 font-bold">Empresa (Razão Social)</th>
                            <th class="py-4 px-8 font-bold">Documento</th>
                            <th class="py-4 px-8 font-bold text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($ultimosClientes as $c)
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="py-4 px-8">
                                    <div class="flex items-center gap-4 min-w-0">
                                        <div class="w-12 h-12 rounded-[14px] bg-[#0A1128] text-white flex items-center justify-center font-display font-extrabold text-lg flex-shrink-0 shadow-md">
                                            {{ substr($c->razao_social, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-[15px] text-[#0A1128] truncate">{{ $c->razao_social }}</p>
                                            <p class="text-xs text-[#8A8F9C] truncate">{{ $c->user->email ?? 'Sem usuário vinculado' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-8">
                                    <span class="text-sm text-[#0A1128] font-mono">{{ $c->cpf_cnpj }}</span>
                                    <span class="block text-[10px] text-[#8A8F9C] uppercase tracking-wider">{{ $c->tipo_pessoa }}</span>
                                </td>
                                <td class="py-4 px-8 text-center">
                                    <a href="{{ route('admin.clientes.show', $c->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-500 hover:bg-[#FF7A1A] hover:text-white transition-colors" title="Abrir Perfil">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</x-admin-layout>
