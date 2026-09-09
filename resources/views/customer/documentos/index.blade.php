<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold text-[#FF7A1A] uppercase tracking-widest mb-2">Segurança</p>
            <h2 class="font-display font-bold text-3xl text-[#0A1128] leading-tight">Cofre de Documentos</h2>
            <p class="text-sm text-slate-500 mt-2">Armazene contratos, planilhas, notas fiscais e arquivos importantes com segurança.</p>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Formulário de Upload -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/50 sticky top-6">
                <h3 class="font-bold text-lg text-[#0A1128] mb-4">Enviar Novo Arquivo</h3>
                
                <form action="{{ route('customer.documentos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-bold text-[#0A1128] mb-2">Nome do Arquivo *</label>
                        <input type="text" name="nome" required placeholder="Ex: Contrato de Prestação de Serviços" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128] text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-[#0A1128] mb-2">Tipo / Categoria</label>
                        <select name="tipo" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128] text-sm">
                            <option value="contrato">Contrato</option>
                            <option value="nota_fiscal">Nota Fiscal / Recibo</option>
                            <option value="relatorio">Relatório / Planilha</option>
                            <option value="outro" selected>Outro</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-[#0A1128] mb-2">Selecione o Arquivo *</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-xl hover:bg-slate-50 hover:border-[#FF7A1A] transition cursor-pointer group relative">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-slate-400 group-hover:text-[#FF7A1A] transition" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                                <div class="flex text-sm text-slate-600 justify-center">
                                    <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-[#FF7A1A] hover:text-orange-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-[#FF7A1A]">
                                        <span>Fazer Upload</span>
                                        <input id="file-upload" name="arquivo" type="file" class="sr-only" required>
                                    </label>
                                </div>
                                <p class="text-xs text-slate-500">PDF, DOC, XLS, PNG ou JPG até 10MB</p>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full mt-2 bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-4 py-3 rounded-xl font-bold transition-all shadow-lg shadow-[#0A1128]/20 hover:shadow-[#FF7A1A]/30">
                        Enviar para o Cofre
                    </button>
                </form>
            </div>
        </div>

        <!-- Lista de Documentos -->
        <div class="lg:col-span-2 space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-3xl p-6 lg:p-8 shadow-xl shadow-slate-200/50">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($documentos as $doc)
                        <div class="border border-slate-200 rounded-2xl p-4 flex items-start gap-4 hover:border-[#FF7A1A] transition-colors group bg-white shadow-sm hover:shadow-md">
                            
                            <!-- Ícone Baseado no Tipo -->
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0
                                {{ $doc->tipo === 'contrato' ? 'bg-purple-100 text-purple-600' : 
                                  ($doc->tipo === 'nota_fiscal' ? 'bg-green-100 text-green-600' : 
                                  ($doc->tipo === 'relatorio' ? 'bg-blue-100 text-blue-600' : 'bg-slate-100 text-slate-500')) }}">
                                
                                @if($doc->tipo === 'contrato')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                @elseif($doc->tipo === 'nota_fiscal')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @else
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-[#0A1128] text-sm truncate">{{ $doc->nome }}</h4>
                                <p class="text-xs text-slate-500 mt-1 uppercase tracking-wider">{{ str_replace('_', ' ', $doc->tipo) }}</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">Enviado em {{ $doc->created_at->format('d/m/Y') }}</p>
                            </div>

                            <div class="flex flex-col gap-2">
                                <a href="{{ Storage::url($doc->arquivo_path) }}" target="_blank" class="p-2 text-slate-400 hover:text-[#FF7A1A] bg-slate-50 hover:bg-orange-50 rounded-lg transition-colors" title="Baixar/Visualizar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                </a>
                                <form action="{{ route('customer.documentos.destroy', $doc) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este documento?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-red-600 bg-slate-50 hover:bg-red-50 rounded-lg transition-colors" title="Excluir">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50">
                            <svg class="mx-auto h-12 w-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            <p class="text-slate-500 font-medium">Seu cofre está vazio.</p>
                            <p class="text-slate-400 text-sm mt-1">Faça o upload do seu primeiro arquivo ao lado.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
