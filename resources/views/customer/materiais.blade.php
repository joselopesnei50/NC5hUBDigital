<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="font-display font-extrabold text-2xl text-ink leading-tight tracking-tight">
                    Hub de Produção & Aprovação
                </h2>
                <p class="text-slateText text-sm mt-1">
                    Área restrita de relacionamento de produção entre a <strong class="text-ink">NC5 Hub</strong> e a <strong class="text-ink">{{ Auth::user()->cliente->razao_social ?? 'sua empresa' }}</strong>.
                </p>
            </div>
            <div class="flex items-center gap-2 bg-white border border-slate-200/80 px-4 py-2 rounded-2xl shadow-sm text-xs font-bold text-slateText">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                Esteira de Produção Ativa
            </div>
        </div>
    </x-slot>

    <!-- Main Materials Hub Container -->
    <div x-data="{ 
        tab: 'todos', 
        modalOpen: false, 
        selectedMaterial: null,
        openModal(material) {
            this.selectedMaterial = material;
            this.modalOpen = true;
        }
    }" class="space-y-8">

        <!-- Banner Explicativo Premium -->
        <div class="bg-[#0A1128] text-white p-8 rounded-3xl shadow-xl relative overflow-hidden">
            <div class="absolute -right-12 -bottom-12 w-64 h-64 rounded-full bg-gradient-to-br from-magenta/20 to-bruce/20 blur-3xl pointer-events-none"></div>
            <div class="relative z-10 max-w-3xl">
                <span class="px-3 py-1 text-[10px] font-extrabold uppercase tracking-widest bg-white/10 rounded-full text-bruce inline-block mb-3 border border-white/10">
                    Aprovação em Tempo Real
                </span>
                <h3 class="font-display font-extrabold text-2xl text-white mb-2">Revisão e Validação de Criativos</h3>
                <p class="text-slate-300 text-sm leading-relaxed">
                    Abaixo você encontra todos os materiais desenvolvidos pela equipe NC5. Clique em <strong class="text-white">"Avaliar / Enviar Observações"</strong> para aprovar diretamente ou detalhar quais ajustes e refinamentos de produção deseja solicitar.
                </p>
            </div>
        </div>

        <!-- Lista de Materiais -->
        <div class="grid grid-cols-1 gap-6">
            @forelse($materiais as $material)
                @php
                    $jsonMaterial = json_encode([
                        'id' => $material->id,
                        'titulo' => $material->titulo,
                        'tipo' => $material->tipo,
                        'descricao' => $material->descricao,
                        'status_aprovacao' => $material->status_aprovacao,
                        'comentario_cliente' => $material->comentario_cliente,
                        'arquivo_path' => $material->arquivo_path,
                        'anexo_admin_path' => $material->anexo_admin_path ? asset('storage/' . $material->anexo_admin_path) : null,
                    ]);
                @endphp

                <div class="bg-white border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-premium transition-all duration-300 hover:shadow-xl relative">
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 pb-6 border-b border-slate-100">
                        <!-- Identificação -->
                        <div>
                            <div class="flex flex-wrap items-center gap-3 mb-2">
                                <span class="text-xs font-extrabold text-slateText bg-slate-100 px-3 py-1 rounded-xl">
                                    {{ $material->tipo ?? 'Material de Produção' }}
                                </span>
                                <span class="text-xs font-semibold text-slateText">
                                    Enviado em {{ $material->created_at->format('d/m/Y \à\s H:i') }}
                                </span>
                            </div>
                            <h3 class="font-display font-extrabold text-xl text-ink">{{ $material->titulo }}</h3>
                        </div>

                        <!-- Status Badge -->
                        <div>
                            @if($material->status_aprovacao === 'aprovado')
                                <span class="px-4 py-2 text-xs font-extrabold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200 inline-flex items-center gap-2 uppercase">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> ✓ Material Aprovado
                                </span>
                            @elseif($material->status_aprovacao === 'ajustes_solicitados')
                                <span class="px-4 py-2 text-xs font-extrabold rounded-full bg-purple-100 text-purple-800 border border-purple-200 inline-flex items-center gap-2 uppercase">
                                    <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span> ⚠️ Ajustes Solicitados
                                </span>
                            @elseif($material->status_aprovacao === 'reprovado')
                                <span class="px-4 py-2 text-xs font-extrabold rounded-full bg-rose-100 text-rose-800 border border-rose-200 inline-flex items-center gap-2 uppercase">
                                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> ❌ Material Reprovado
                                </span>
                            @else
                                <span class="px-4 py-2 text-xs font-extrabold rounded-full bg-amber-100 text-amber-800 border border-amber-200 inline-flex items-center gap-2 uppercase animate-pulse">
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> ⏳ Aguardando Sua Aprovação
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Instruções da Equipe NC5 -->
                    @if($material->descricao)
                        <div class="mt-6 p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                            <span class="text-[11px] font-extrabold text-slateText uppercase tracking-wider block mb-1">Orientações da Equipe NC5</span>
                            <p class="text-sm text-slate-700 font-medium whitespace-pre-line">{{ $material->descricao }}</p>
                        </div>
                    @endif

                    <!-- Observações enviadas anteriormente pelo Cliente -->
                    @if($material->comentario_cliente)
                        <div class="mt-4 p-4 rounded-2xl bg-purple-50/50 border border-purple-200/60">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[11px] font-extrabold text-purple-800 uppercase tracking-wider flex items-center gap-1.5">
                                    💬 Suas Observações Enviadas
                                </span>
                                @if($material->data_resposta)
                                    <span class="text-[10px] text-purple-600 font-bold">Enviado em {{ $material->data_resposta->format('d/m/Y H:i') }}</span>
                                @endif
                            </div>
                            <p class="text-sm text-slate-800 font-medium italic">"{{ $material->comentario_cliente }}"</p>

                            @if($material->anexo_cliente_path)
                                <div class="mt-3 pt-2 border-t border-purple-200/40">
                                    <a href="{{ asset('storage/' . $material->anexo_cliente_path) }}" target="_blank" class="text-xs font-bold text-purple-700 hover:underline inline-flex items-center gap-1">
                                        📎 Seu anexo de referência enviado
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Actions & Links Bar -->
                    <div class="mt-6 pt-6 border-t border-slate-100 flex flex-wrap items-center justify-between gap-4">
                        <div class="flex flex-wrap items-center gap-3">
                            @if($material->arquivo_path)
                                <a href="{{ $material->arquivo_path }}" target="_blank" class="bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-5 py-3 rounded-2xl text-xs font-bold shadow-md flex items-center gap-2 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    Visualizar Material (Figma / Drive / Link)
                                </a>
                            @endif

                            @if($material->anexo_admin_path)
                                <a href="{{ asset('storage/' . $material->anexo_admin_path) }}" target="_blank" class="bg-[#0A1128] text-white px-5 py-3 rounded-2xl text-xs font-bold shadow-md flex items-center gap-2 hover:bg-[#E63888] transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    Baixar Arquivo Anexo
                                </a>
                            @endif
                        </div>

                        <!-- Botão Avaliar / Adicionar Observações -->
                        <button @click="openModal({{ $jsonMaterial }})" class="bg-[#0A1128] hover:bg-[#E63888] text-white px-6 py-3 rounded-2xl text-xs font-extrabold shadow-md transition-all transform hover:-translate-y-0.5 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            {{ $material->status_aprovacao === 'pendente' ? 'Avaliar & Enviar Observações' : 'Alterar Observações / Status' }}
                        </button>
                    </div>
                </div>
            @empty
                <div class="bg-white border border-slate-200/80 rounded-3xl p-16 text-center shadow-premium">
                    <div class="w-16 h-16 rounded-3xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="font-display font-bold text-xl text-ink">Nenhum material pendente de aprovação no momento.</h3>
                    <p class="text-sm text-slateText mt-1">Assim que a equipe NC5 disponibilizar novas artes, vídeos ou layouts, eles aparecerão aqui para sua validação.</p>
                </div>
            @endforelse
        </div>

        <!-- Modal de Avaliação & Observações -->
        <div x-show="modalOpen" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink/70 backdrop-blur-md" style="display: none;">
            <div @click.away="modalOpen = false" class="bg-white border border-slate-200 rounded-3xl shadow-2xl max-w-2xl w-full p-8 overflow-y-auto max-h-[90vh] relative">
                
                <!-- Close Button -->
                <button @click="modalOpen = false" class="absolute top-6 right-6 text-slate-400 hover:text-ink text-2xl font-bold transition-colors">&times;</button>

                <div class="mb-6">
                    <span class="px-3 py-1 text-[10px] font-extrabold uppercase tracking-widest bg-bruce/10 text-bruce rounded-full inline-block mb-2">
                        Relacionamento de Produção NC5
                    </span>
                    <h3 class="font-display font-extrabold text-2xl text-ink" x-text="selectedMaterial ? selectedMaterial.titulo : 'Avaliar Material'"></h3>
                    <p class="text-slateText text-xs mt-1">Escolha sua decisão e envie observações detalhadas para a equipe de criação da NC5.</p>
                </div>

                <form x-bind:action="selectedMaterial ? '{{ url('area-cliente/materiais') }}/' + selectedMaterial.id + '/avaliar' : '#'" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Escolha da Decisão -->
                    <div>
                        <label class="block text-xs font-extrabold text-slateText uppercase tracking-wider mb-3">Qual a sua decisão para este material? *</label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all hover:border-emerald-500 border-slate-200 bg-slate-50/50 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/40">
                                <input type="radio" name="status_aprovacao" value="aprovado" required class="sr-only">
                                <span class="font-extrabold text-emerald-700 text-sm">✓ Aprovar</span>
                                <span class="text-[11px] text-slate-600 mt-1">Material pronto para ser veiculado</span>
                            </label>

                            <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all hover:border-purple-500 border-slate-200 bg-slate-50/50 has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50/40">
                                <input type="radio" name="status_aprovacao" value="ajustes_solicitados" required class="sr-only">
                                <span class="font-extrabold text-purple-700 text-sm">⚠️ Ajustes</span>
                                <span class="text-[11px] text-slate-600 mt-1">Quero alterações específicas</span>
                            </label>

                            <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all hover:border-rose-500 border-slate-200 bg-slate-50/50 has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50/40">
                                <input type="radio" name="status_aprovacao" value="reprovado" required class="sr-only">
                                <span class="font-extrabold text-rose-700 text-sm">❌ Reprovar</span>
                                <span class="text-[11px] text-slate-600 mt-1">Refazer proposta</span>
                            </label>
                        </div>
                    </div>

                    <!-- Observações do Cliente -->
                    <div>
                        <label class="block text-xs font-extrabold text-slateText uppercase tracking-wider mb-2">Suas Observações & Detalhes do Pedido</label>
                        <textarea name="comentario_cliente" rows="4" x-text="selectedMaterial ? selectedMaterial.comentario_cliente : ''" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 p-4 text-sm font-medium text-ink focus:border-bruce focus:ring-bruce" placeholder="Escreva aqui todas as suas observações (ex: Alterar a frase da lâmina 2 para 'Promoção válida até domingo', utilizar o logotipo branco no fundo escuro)..."></textarea>
                    </div>

                    <!-- Upload Anexo de Referência -->
                    <div>
                        <label class="block text-xs font-extrabold text-slateText uppercase tracking-wider mb-2">Anexar Imagem / Print / Arquivo de Referência (Opcional)</label>
                        <input type="file" name="anexo_cliente" class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-ink file:text-white hover:file:bg-bruce">
                    </div>

                    <!-- Botão Enviar -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" @click="modalOpen = false" class="text-slateText hover:text-ink font-bold text-sm transition-colors px-4 py-2">
                            Cancelar
                        </button>
                        <button type="submit" class="bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-8 py-3.5 rounded-2xl text-sm font-bold shadow-md transition-all transform hover:-translate-y-0.5">
                            Enviar Observações para a NC5
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
