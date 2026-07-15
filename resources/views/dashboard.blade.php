<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-white leading-tight tracking-tight">
                Visão Geral
            </h2>
            <div class="flex items-center gap-4">
                <button class="bg-[#1f2937] text-gray-300 hover:text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors border border-gray-700">
                    Baixar Relatório
                </button>
                <button class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-[0_0_15px_rgba(37,99,235,0.4)]">
                    Novo Chamado
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <!-- Dashboard Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Stat Card 1 -->
            <div class="bg-[#111827] border border-gray-800 rounded-2xl p-6 relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <p class="text-sm font-medium text-gray-400 mb-1">Status da Conta</p>
                <h3 class="text-3xl font-light text-white mb-2">Ativa</h3>
                <div class="flex items-center text-xs text-emerald-400 font-medium">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 mr-2 animate-pulse"></span>
                    Serviços operando normalmente
                </div>
            </div>

            <!-- Stat Card 2 -->
            <div class="bg-[#111827] border border-gray-800 rounded-2xl p-6 relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <p class="text-sm font-medium text-gray-400 mb-1">Leads Processados (Mês)</p>
                <h3 class="text-3xl font-light text-white mb-2">1,284</h3>
                <div class="flex items-center text-xs text-emerald-400 font-medium">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    +12% vs. mês passado
                </div>
            </div>

            <!-- Stat Card 3 -->
            <div class="bg-[#111827] border border-gray-800 rounded-2xl p-6 relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <p class="text-sm font-medium text-gray-400 mb-1">Faturas Pendentes</p>
                <h3 class="text-3xl font-light text-white mb-2">0</h3>
                <div class="flex items-center text-xs text-gray-500 font-medium">
                    Tudo em dia
                </div>
            </div>
        </div>

        <!-- Active Services Table Section -->
        <div class="bg-[#111827] border border-gray-800 rounded-2xl overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-800">
                <h3 class="text-lg font-medium text-white">Ecossistema de Serviços</h3>
                <p class="text-sm text-gray-400 mt-1">Visão técnica das integrações e automações vigentes em seu plano.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="px-6 py-4 border-b border-gray-800 bg-[#0B1120] text-xs font-medium text-gray-400 uppercase tracking-wider">Módulo</th>
                            <th class="px-6 py-4 border-b border-gray-800 bg-[#0B1120] text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 border-b border-gray-800 bg-[#0B1120] text-xs font-medium text-gray-400 uppercase tracking-wider">Última Sincronização</th>
                            <th class="px-6 py-4 border-b border-gray-800 bg-[#0B1120] text-xs font-medium text-gray-400 uppercase tracking-wider text-right">Ação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        <tr class="hover:bg-gray-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-blue-900/30 text-blue-500 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-white">Funil de Automação (ActiveCampaign)</p>
                                        <p class="text-xs text-gray-500">Recuperação de boletos e nutrição</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-[10px] uppercase font-bold tracking-wider rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Operante</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-400">
                                Há 2 minutos
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="#" class="text-sm text-blue-500 hover:text-blue-400 font-medium">Ver Métricas</a>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-purple-900/30 text-purple-500 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-white">Posicionamento de Marca (Meta Ads)</p>
                                        <p class="text-xs text-gray-500">Campanhas de tração ativa</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-[10px] uppercase font-bold tracking-wider rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Operante</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-400">
                                Hoje, 08:30
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="#" class="text-sm text-blue-500 hover:text-blue-400 font-medium">Analisar Criativos</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
