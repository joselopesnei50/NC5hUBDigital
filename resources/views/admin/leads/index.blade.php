<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:justify-between md:items-end gap-4">
            <div>
                <h2 class="font-display font-bold text-3xl text-ink leading-tight">Leads (IA)</h2>
                <p class="text-slate text-sm mt-1">Gerencie os contatos gerados pela Análise Gratuita.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.leads.export') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg shadow-emerald-600/20 inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Baixar CSV (Campanhas)
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Filtros -->
    <div class="bg-white border border-black/5 rounded-2xl p-6 shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.leads.index') }}" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 w-full">
                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-slate-400 mb-2">Buscar por Nome ou E-mail</label>
                <input type="text" name="search" value="{{ request('search') }}" class="w-full bg-[#F8FAFC] border-slate-200/80 rounded-xl focus:ring-0 focus:border-[#FF7A1A] text-sm px-4 py-2 transition-colors placeholder-slate-400" placeholder="Ex: João da Silva...">
            </div>
            <div class="flex-1 w-full">
                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-slate-400 mb-2">Filtrar por Tipo</label>
                <select name="tipo" class="w-full bg-[#F8FAFC] border-slate-200/80 rounded-xl focus:ring-0 focus:border-[#FF7A1A] text-sm px-4 py-2 transition-colors text-ink">
                    <option value="">Todos os tipos</option>
                    <option value="site" {{ request('tipo') == 'site' ? 'selected' : '' }}>Site / Landing Page</option>
                    <option value="redes_sociais" {{ request('tipo') == 'redes_sociais' ? 'selected' : '' }}>Rede Social</option>
                    <option value="marca" {{ request('tipo') == 'marca' ? 'selected' : '' }}>Marca</option>
                </select>
            </div>
            <button type="submit" class="bg-ink hover:bg-bruce text-white px-6 py-2 rounded-xl text-sm font-bold transition-colors shadow-lg shadow-ink/10 flex-shrink-0 h-10">
                Filtrar
            </button>
            @if(request()->has('search') || request()->has('tipo'))
                <a href="{{ route('admin.leads.index') }}" class="text-xs font-bold text-slate hover:text-rose-500 transition-colors uppercase tracking-wider h-10 flex items-center px-4">Limpar</a>
            @endif
        </form>
    </div>

    <!-- Tabela de Leads -->
    <div class="bg-white border border-black/5 rounded-2xl overflow-hidden shadow-sm">
        @if($leads->isEmpty())
            <div class="p-16 text-center">
                <div class="w-16 h-16 bg-mist rounded-2xl flex items-center justify-center mx-auto mb-4 text-slate/30">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                </div>
                <h3 class="font-display text-xl font-bold text-ink mb-2">Nenhum lead encontrado</h3>
                <p class="text-slate text-sm">Os contatos gerados pela Análise de IA aparecerão aqui.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-mist border-b border-black/5">
                            <th class="px-6 py-4 text-[10px] font-extrabold text-slate uppercase tracking-widest whitespace-nowrap">Lead</th>
                            <th class="px-6 py-4 text-[10px] font-extrabold text-slate uppercase tracking-widest whitespace-nowrap">Contato</th>
                            <th class="px-6 py-4 text-[10px] font-extrabold text-slate uppercase tracking-widest whitespace-nowrap">Tipo</th>
                            <th class="px-6 py-4 text-[10px] font-extrabold text-slate uppercase tracking-widest whitespace-nowrap">Pontuações / Dados</th>
                            <th class="px-6 py-4 text-[10px] font-extrabold text-slate uppercase tracking-widest whitespace-nowrap">Data</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/5">
                        @foreach($leads as $lead)
                            <tr class="hover:bg-mist/50 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="font-bold text-ink text-sm">{{ $lead->nome }}</p>
                                    @if($lead->url_site)
                                        <a href="{{ $lead->url_site }}" target="_blank" class="text-xs text-blue-500 hover:underline truncate inline-block max-w-[200px]">{{ $lead->url_site }}</a>
                                    @elseif($lead->url_social)
                                        <span class="text-xs text-slate truncate inline-block max-w-[200px]">{{ $lead->url_social }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-slate">{{ $lead->email }}</p>
                                    <p class="text-xs font-semibold text-slate/70 mt-0.5">{{ $lead->whatsapp }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 bg-ink/5 text-ink text-[10px] font-bold uppercase tracking-wider rounded-lg inline-flex items-center gap-1.5 whitespace-nowrap">
                                        {{ str_replace('_', ' ', $lead->tipo_analise) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($lead->tipo_analise === 'site')
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-1 bg-rose-50 text-rose-600 rounded-md text-[10px] font-bold" title="Performance">⚡ {{ $lead->performance_score ?? 'N/A' }}</span>
                                            <span class="px-2 py-1 bg-emerald-50 text-emerald-600 rounded-md text-[10px] font-bold" title="SEO">🔍 {{ $lead->seo_score ?? 'N/A' }}</span>
                                        </div>
                                    @elseif($lead->tipo_analise === 'redes_sociais')
                                        @if(!is_null($lead->ig_followers))
                                            <span class="px-2 py-1 bg-[#FF7A1A]/10 text-[#FF7A1A] rounded-md text-[10px] font-bold whitespace-nowrap" title="Seguidores">
                                                👥 {{ number_format($lead->ig_followers, 0, ',', '.') }} Seg
                                            </span>
                                        @else
                                            <span class="text-xs text-slate/50">Sem dados extras</span>
                                        @endif
                                    @else
                                        <span class="text-xs text-slate/50">N/A</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-ink">{{ $lead->created_at->format('d/m/Y') }}</p>
                                    <p class="text-xs text-slate mt-0.5">{{ $lead->created_at->format('H:i') }}</p>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($leads->hasPages())
                <div class="px-6 py-4 border-t border-black/5 bg-mist">
                    {{ $leads->links() }}
                </div>
            @endif
        @endif
    </div>
</x-admin-layout>
