<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.clientes.index') }}" class="text-[#8A8F9C] hover:text-[#0A1128] transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">
                    {{ $cliente->razao_social }}
                </h2>
                <p class="text-sm text-[#8A8F9C] mt-1">Dossiê do Cliente e Histórico</p>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Info Principal -->
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 bg-[#F4F5F7] rounded-full flex items-center justify-center text-xl font-bold text-[#0A1128]">
                    {{ substr($cliente->razao_social, 0, 1) }}
                </div>
                <div>
                    <h3 class="font-bold text-lg text-[#0A1128]">{{ $cliente->nome ?? $cliente->user->name }}</h3>
                    <p class="text-sm text-[#8A8F9C]">{{ $cliente->user->email }}</p>
                    <span class="inline-block mt-1 px-2.5 py-1 text-[10px] font-bold rounded-full {{ $cliente->status == 'ativo' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }} uppercase">
                        {{ $cliente->status }}
                    </span>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <p class="text-xs font-semibold text-[#8A8F9C] uppercase tracking-wide">Documento ({{ $cliente->tipo_pessoa }})</p>
                    <p class="font-medium text-[#0A1128]">{{ $cliente->cpf_cnpj }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-[#8A8F9C] uppercase tracking-wide">Telefone / WhatsApp</p>
                    <p class="font-medium text-[#0A1128]">{{ $cliente->telefone ?? 'Não informado' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-[#8A8F9C] uppercase tracking-wide">Data de Cadastro</p>
                    <p class="font-medium text-[#0A1128]">{{ $cliente->created_at->format('d/m/Y') }}</p>
                </div>
            </div>

            <div class="mt-8">
                <a href="{{ route('admin.clientes.edit', $cliente->id) }}" class="block text-center w-full bg-[#0A1128] hover:bg-[#FF7A1A] text-white py-2.5 rounded-xl text-sm font-bold transition-colors">
                    Editar Cadastro
                </a>
            </div>
        </div>

        <!-- Coluna da Direita (Dados Conectados) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Contratos e Faturas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Contratos -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    <h3 class="font-bold text-[#0A1128] flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-[#8A8F9C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Contratos Ativos ({{ $cliente->contratos->count() }})
                    </h3>
                    <div class="space-y-3">
                        @forelse($cliente->contratos as $contrato)
                            <div class="p-3 bg-[#F4F5F7] rounded-xl flex justify-between items-center">
                                <div>
                                    <p class="font-bold text-sm text-[#0A1128]">{{ $contrato->servico->nome ?? 'Serviço' }}</p>
                                    <p class="text-xs text-[#8A8F9C]">R$ {{ number_format($contrato->servico->preco ?? 0, 2, ',', '.') }}</p>
                                </div>
                                <span class="px-2 py-1 text-[10px] font-bold rounded bg-emerald-200 text-emerald-800 uppercase">{{ $contrato->status }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-[#8A8F9C]">Nenhum contrato ativo.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Faturas -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    <h3 class="font-bold text-[#0A1128] flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-[#8A8F9C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Últimas Faturas
                    </h3>
                    <div class="space-y-3">
                        @forelse($cliente->faturas->take(3) as $fatura)
                            <div class="p-3 bg-[#F4F5F7] rounded-xl flex justify-between items-center">
                                <div>
                                    <p class="font-bold text-sm text-[#0A1128]">{{ $fatura->descricao }}</p>
                                    <p class="text-xs text-[#8A8F9C]">Vence: {{ \Carbon\Carbon::parse($fatura->vencimento)->format('d/m') }}</p>
                                </div>
                                <span class="px-2 py-1 text-[10px] font-bold rounded {{ $fatura->status == 'pendente' ? 'bg-orange-200 text-orange-800' : 'bg-emerald-200 text-emerald-800' }} uppercase">{{ $fatura->status }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-[#8A8F9C]">Nenhuma fatura emitida.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Esteira de Materiais -->
            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-[#0A1128] flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#8A8F9C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Materiais na Esteira
                    </h3>
                    <a href="{{ route('admin.materiais.create') }}" class="text-xs font-bold text-[#E63888] hover:text-[#0A1128] uppercase transition-colors">Enviar Novo</a>
                </div>
                <div class="p-0">
                    <ul class="divide-y divide-gray-100">
                        @forelse($cliente->materiais as $material)
                            <li class="p-4 hover:bg-gray-50 transition-colors flex justify-between items-center">
                                <div>
                                    <p class="font-bold text-sm text-[#0A1128]">{{ $material->titulo }}</p>
                                    <p class="text-xs text-[#8A8F9C]">Enviado em: {{ $material->created_at->format('d/m/Y') }}</p>
                                </div>
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full {{ $material->status_aprovacao == 'aprovado' ? 'bg-emerald-100 text-emerald-700' : ($material->status_aprovacao == 'reprovado' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }} uppercase">
                                    {{ str_replace('_', ' ', $material->status_aprovacao) }}
                                </span>
                            </li>
                        @empty
                            <li class="p-6 text-center text-sm text-[#8A8F9C]">Nenhum material associado a este cliente no momento.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Briefings -->
            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm" x-data="{ modalBriefing: false }">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-[#0A1128] flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#8A8F9C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Briefings ({{ count($cliente->briefings ?? []) }})
                    </h3>
                    <button @click="modalBriefing = true" class="text-xs font-bold text-[#E63888] hover:text-[#0A1128] uppercase transition-colors">Solicitar Briefing</button>
                </div>
                <div class="p-0">
                    <ul class="divide-y divide-gray-100">
                        @forelse($cliente->briefings ?? [] as $briefing)
                            <li class="p-6 hover:bg-gray-50 transition-colors">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <p class="font-bold text-sm text-[#0A1128]">{{ $briefing->titulo }}</p>
                                        <p class="text-xs text-[#8A8F9C]">Enviado em: {{ $briefing->created_at->format('d/m/Y') }}</p>
                                    </div>
                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-full {{ $briefing->status == 'respondido' ? 'bg-emerald-100 text-emerald-700' : 'bg-orange-100 text-orange-700' }} uppercase">
                                        {{ $briefing->status }}
                                    </span>
                                </div>
                                
                                @if($briefing->status == 'respondido')
                                    <div class="mt-4 bg-[#F4F5F7] p-4 rounded-xl border border-gray-200">
                                        <p class="text-xs font-bold text-[#8A8F9C] uppercase tracking-wider mb-2">Resposta do Cliente:</p>
                                        <p class="text-sm text-[#0A1128] whitespace-pre-line">{{ $briefing->resposta_cliente }}</p>
                                        
                                        @if($briefing->anexo_cliente_path)
                                            <a href="{{ Storage::url($briefing->anexo_cliente_path) }}" target="_blank" class="mt-3 inline-flex items-center gap-2 text-sm font-bold text-[#E63888] hover:text-[#0A1128] transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                Baixar Anexo do Cliente
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </li>
                        @empty
                            <li class="p-6 text-center text-sm text-[#8A8F9C]">Nenhum briefing solicitado ainda.</li>
                        @endforelse
                    </ul>
                </div>

                <!-- Modal Briefing -->
                <div x-show="modalBriefing" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div x-show="modalBriefing" x-transition class="fixed inset-0 bg-[#0A1128] bg-opacity-75 transition-opacity" @click="modalBriefing = false"></div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                        <div x-show="modalBriefing" x-transition class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                            <form action="{{ route('admin.clientes.briefings.store', $cliente->id) }}" method="POST" enctype="multipart/form-data" class="p-8">
                                @csrf
                                <h3 class="text-2xl font-bold text-[#0A1128] mb-6">Solicitar Novo Briefing</h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-bold text-[#0A1128] mb-1">Título do Briefing</label>
                                        <input type="text" name="titulo" required class="w-full rounded-xl border-gray-300 focus:border-[#0A1128] focus:ring-[#0A1128]" placeholder="Ex: Briefing Lançamento Site">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-[#0A1128] mb-1">Perguntas / Instruções</label>
                                        <textarea name="descricao" rows="4" required class="w-full rounded-xl border-gray-300 focus:border-[#0A1128] focus:ring-[#0A1128]" placeholder="Liste as perguntas que o cliente deve responder..."></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-[#0A1128] mb-1">Anexar Documento ou Imagem Base (Opcional)</label>
                                        <input type="file" name="arquivo_admin" class="w-full text-sm text-[#8A8F9C] file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-[#F4F5F7] file:text-[#0A1128] hover:file:bg-gray-200">
                                    </div>
                                </div>

                                <div class="mt-8 flex justify-end gap-3">
                                    <button type="button" @click="modalBriefing = false" class="px-6 py-2.5 bg-white border border-gray-300 rounded-xl text-sm font-bold text-[#0A1128] hover:bg-gray-50">Cancelar</button>
                                    <button type="submit" class="px-6 py-2.5 bg-[#0A1128] text-white rounded-xl text-sm font-bold hover:bg-[#FF7A1A] transition-colors shadow-lg">Enviar ao Cliente</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</x-admin-layout>
