<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:justify-between md:items-end gap-4">
            <div>
                <p class="text-[11px] font-bold text-[#FF7A1A] uppercase tracking-[0.2em] mb-1">Marketing</p>
                <h2 class="font-display font-extrabold text-3xl text-[#0A1128] leading-tight">
                    E-mail Marketing
                </h2>
                <p class="text-[#8A8F9C] text-sm mt-1">Dispare campanhas em massa para seus Leads (Powered by Brevo)</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.campanhas.create') }}" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg shadow-[#0A1128]/10 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    Nova Campanha
                </a>
            </div>
        </div>
    </x-slot>

    @if(!$brevoConectado)
        <div class="mb-8 p-6 bg-orange-50 border border-orange-200 rounded-2xl flex items-start gap-4">
            <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center flex-shrink-0 text-orange-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-orange-800">Brevo Não Configurado</h3>
                <p class="text-orange-700 text-sm mt-1">Para criar campanhas de E-mail Marketing, você precisa inserir sua chave da API do Brevo nas Configurações Globais.</p>
                <a href="{{ route('admin.configuracoes.index') }}" class="inline-block mt-3 bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-xs font-bold transition-colors">Configurar Brevo Agora &rarr;</a>
            </div>
        </div>
    @endif

    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 class="font-bold text-lg text-[#0A1128]">Histórico de Campanhas</h3>
        </div>

        @if($campanhas->isEmpty())
            <div class="p-16 text-center">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <h4 class="text-lg font-bold text-[#0A1128] mb-1">Nenhuma campanha enviada</h4>
                <p class="text-[#8A8F9C] text-sm max-w-md mx-auto mb-6">Comece a engajar seus Leads disparando sua primeira campanha de e-mail marketing.</p>
                <a href="{{ route('admin.campanhas.create') }}" class="bg-[#0A1128] text-white px-5 py-2 rounded-xl text-sm font-bold">Criar Primeira Campanha</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white text-[10px] uppercase tracking-widest text-[#8A8F9C] border-b border-gray-100">
                            <th class="py-4 px-6 font-bold">Campanha</th>
                            <th class="py-4 px-6 font-bold">Status</th>
                            <th class="py-4 px-6 font-bold">Audiência</th>
                            <th class="py-4 px-6 font-bold">Enviados</th>
                            <th class="py-4 px-6 font-bold text-center">Aberturas</th>
                            <th class="py-4 px-6 font-bold text-center">Cliques</th>
                            <th class="py-4 px-6 font-bold text-right">Data</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($campanhas as $c)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-4 px-6">
                                    <p class="font-bold text-[14px] text-[#0A1128]">{{ $c->nome }}</p>
                                    <p class="text-xs text-[#8A8F9C] truncate max-w-[200px]">{{ $c->assunto }}</p>
                                </td>
                                <td class="py-4 px-6">
                                    @if($c->status === 'enviando' || str_contains(strtolower($c->status), 'progress'))
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 text-[10px] font-bold uppercase tracking-wider">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                            Enviando...
                                        </span>
                                    @elseif($c->status === 'sent' || str_contains(strtolower($c->status), 'concluid'))
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-[10px] font-bold uppercase tracking-wider">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Concluído
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-gray-100 text-gray-700 text-[10px] font-bold uppercase tracking-wider">
                                            {{ $c->status }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-xs font-semibold text-[#0A1128] bg-gray-100 px-2 py-1 rounded-md">
                                        {{ $c->audience === 'leads_ia' ? 'Leads (IA)' : 'Planilha' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <p class="text-sm font-bold text-[#0A1128]">{{ $c->metrics['sent'] ?? '-' }}</p>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    @php
                                        $sent = $c->metrics['sent'] ?? 0;
                                        $opens = $c->metrics['uniqueViews'] ?? 0;
                                        $taxaAbertura = $sent > 0 ? round(($opens / $sent) * 100, 1) : 0;
                                    @endphp
                                    <p class="text-sm font-bold text-emerald-600">{{ $opens }}</p>
                                    <p class="text-[10px] text-[#8A8F9C]">{{ $taxaAbertura }}%</p>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    @php
                                        $clicks = $c->metrics['uniqueClicks'] ?? 0;
                                        $taxaClique = $opens > 0 ? round(($clicks / $opens) * 100, 1) : 0;
                                    @endphp
                                    <p class="text-sm font-bold text-blue-600">{{ $clicks }}</p>
                                    <p class="text-[10px] text-[#8A8F9C]">{{ $taxaClique }}%</p>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <p class="text-sm font-medium text-[#0A1128]">{{ $c->created_at->format('d/m/Y') }}</p>
                                    <p class="text-xs text-[#8A8F9C]">{{ $c->created_at->format('H:i') }}</p>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-admin-layout>
