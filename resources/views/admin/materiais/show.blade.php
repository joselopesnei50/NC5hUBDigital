<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.materiais.index') }}" class="w-10 h-10 rounded-2xl bg-white border border-slate-200 text-slateText hover:text-ink hover:border-slate-300 flex items-center justify-center transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <h2 class="font-display font-extrabold text-2xl text-ink leading-tight tracking-tight">{{ $material->titulo }}</h2>
                    <p class="text-xs text-slateText font-medium mt-0.5">ID #{{ $material->id }} · Cadastrado em {{ $material->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
            <a href="{{ route('admin.materiais.edit', $material->id) }}" class="bg-ink hover:bg-magenta text-white px-6 py-2.5 rounded-2xl text-sm font-bold transition-all shadow-md">
                Editar Material
            </a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Details (2 Cols) -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Card Status & Resumo -->
            <div class="bg-white border border-slate-200/80 rounded-3xl p-8 shadow-premium">
                <div class="flex items-center justify-between mb-6 pb-6 border-b border-slate-100">
                    <div>
                        <span class="text-xs font-extrabold text-slateText uppercase tracking-wider block">Status da Aprovação</span>
                        <div class="mt-2">
                            @if($material->status_aprovacao === 'aprovado')
                                <span class="px-4 py-1.5 text-sm font-extrabold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200 inline-flex items-center gap-2 uppercase">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Aprovado pelo Cliente
                                </span>
                            @elseif($material->status_aprovacao === 'ajustes_solicitados')
                                <span class="px-4 py-1.5 text-sm font-extrabold rounded-full bg-purple-100 text-purple-800 border border-purple-200 inline-flex items-center gap-2 uppercase">
                                    <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span> Ajustes Solicitados pelo Cliente
                                </span>
                            @elseif($material->status_aprovacao === 'reprovado')
                                <span class="px-4 py-1.5 text-sm font-extrabold rounded-full bg-rose-100 text-rose-800 border border-rose-200 inline-flex items-center gap-2 uppercase">
                                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Reprovado pelo Cliente
                                </span>
                            @else
                                <span class="px-4 py-1.5 text-sm font-extrabold rounded-full bg-amber-100 text-amber-800 border border-amber-200 inline-flex items-center gap-2 uppercase">
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span> Aguardando Avaliação do Cliente
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($material->data_resposta)
                        <div class="text-right">
                            <span class="text-xs font-bold text-slateText uppercase tracking-wider block">Data da Resposta</span>
                            <span class="text-sm font-bold text-ink mt-1 block">{{ $material->data_resposta->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                </div>

                <!-- Conteúdo enviado pela NC5 -->
                <div class="space-y-6">
                    @if($material->descricao)
                        <div>
                            <h4 class="text-xs font-extrabold text-slateText uppercase tracking-wider mb-2">Instruções / Contexto Enviado ao Cliente</h4>
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/60 text-sm font-medium text-slate-700 whitespace-pre-line">
                                {{ $material->descricao }}
                            </div>
                        </div>
                    @endif

                    <!-- Links e Anexos NC5 -->
                    <div>
                        <h4 class="text-xs font-extrabold text-slateText uppercase tracking-wider mb-3">Links & Arquivos Disponibilizados</h4>
                        <div class="flex flex-wrap gap-3">
                            @if($material->arquivo_path)
                                <a href="{{ $material->arquivo_path }}" target="_blank" class="bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-5 py-3 rounded-2xl text-xs font-bold shadow-md flex items-center gap-2 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    Abrir Link Externo (Canva/Figma/Drive)
                                </a>
                            @endif

                            @if($material->anexo_admin_path)
                                <a href="{{ asset('storage/' . $material->anexo_admin_path) }}" target="_blank" class="bg-ink text-white px-5 py-3 rounded-2xl text-xs font-bold shadow-md flex items-center gap-2 hover:bg-magenta transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    Baixar Anexo NC5
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Feedback / Observações do Cliente -->
            <div class="bg-white border border-slate-200/80 rounded-3xl p-8 shadow-premium">
                <h3 class="font-display font-bold text-lg text-ink mb-4 flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-magenta"></span>
                    Observações e Feedback do Cliente
                </h3>

                @if($material->comentario_cliente)
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 text-sm text-slate-800 font-medium whitespace-pre-line leading-relaxed mb-4">
                        "{{ $material->comentario_cliente }}"
                    </div>
                @else
                    <div class="p-6 text-center text-slate-400 bg-slate-50/50 border border-dashed border-slate-200 rounded-2xl">
                        O cliente ainda não enviou observações sobre este material.
                    </div>
                @endif

                @if($material->anexo_cliente_path)
                    <div class="mt-4 pt-4 border-t border-slate-100">
                        <span class="text-xs font-bold text-slateText uppercase tracking-wider block mb-2">Arquivo / Anexo enviado pelo Cliente</span>
                        <a href="{{ asset('storage/' . $material->anexo_cliente_path) }}" target="_blank" class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-ink px-4 py-2.5 rounded-xl text-xs font-bold transition-colors">
                            📎 Baixar Anexo de Referência do Cliente
                        </a>
                    </div>
                @endif
            </div>

        </div>

        <!-- Sidebar Details (1 Col) -->
        <div class="space-y-6">
            <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-premium">
                <h3 class="font-display font-bold text-base text-ink mb-4 pb-3 border-b border-slate-100">Informações do Cliente</h3>

                <div class="space-y-4 text-sm">
                    <div>
                        <span class="text-xs font-bold text-slateText uppercase tracking-wider block">Razão Social / Nome</span>
                        <span class="font-bold text-ink block mt-0.5">{{ $material->cliente->razao_social ?? '—' }}</span>
                    </div>

                    <div>
                        <span class="text-xs font-bold text-slateText uppercase tracking-wider block">E-mail Cadastrado</span>
                        <span class="font-semibold text-slate-700 block mt-0.5">{{ $material->cliente->user->email ?? '—' }}</span>
                    </div>

                    <div>
                        <span class="text-xs font-bold text-slateText uppercase tracking-wider block">CNPJ / CPF</span>
                        <span class="font-semibold text-slate-700 block mt-0.5">{{ $material->cliente->cnpj_cpf ?? '—' }}</span>
                    </div>

                    <div>
                        <span class="text-xs font-bold text-slateText uppercase tracking-wider block">Categoria do Material</span>
                        <span class="font-semibold text-ink block mt-0.5">{{ $material->tipo ?? 'Não especificado' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
