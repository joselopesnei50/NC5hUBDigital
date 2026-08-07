<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.materiais.index') }}" class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-slateText hover:text-ink hover:border-slate-300 flex items-center justify-center transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <h2 class="font-display font-extrabold text-2xl text-ink leading-tight tracking-tight">{{ $material->titulo }}</h2>
                    <p class="text-xs text-slateText font-medium mt-0.5">ID #{{ $material->id }} · Cadastrado em {{ $material->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
            <a href="{{ route('admin.materiais.edit', $material->id) }}" class="bg-ink hover:bg-bruce text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-colors">
                Editar Material
            </a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- ================== Coluna principal ================== --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Status + resumo do material --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-8">
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
                                    <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span> Ajustes Solicitados
                                </span>
                            @elseif($material->status_aprovacao === 'reprovado')
                                <span class="px-4 py-1.5 text-sm font-extrabold rounded-full bg-rose-100 text-rose-800 border border-rose-200 inline-flex items-center gap-2 uppercase">
                                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Reprovado pelo Cliente
                                </span>
                            @else
                                <span class="px-4 py-1.5 text-sm font-extrabold rounded-full bg-amber-100 text-amber-800 border border-amber-200 inline-flex items-center gap-2 uppercase">
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span> Aguardando Cliente
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($material->data_resposta)
                        <div class="text-right">
                            <span class="text-xs font-bold text-slateText uppercase tracking-wider block">Última Resposta</span>
                            <span class="text-sm font-bold text-ink mt-1 block">{{ $material->data_resposta->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                </div>

                @if($material->descricao)
                    <div class="mb-6">
                        <h4 class="text-xs font-extrabold text-slateText uppercase tracking-wider mb-2">Instruções enviadas ao cliente</h4>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-sm font-medium text-slate-700 whitespace-pre-line">
                            {{ $material->descricao }}
                        </div>
                    </div>
                @endif

                <div>
                    <h4 class="text-xs font-extrabold text-slateText uppercase tracking-wider mb-3">Links & arquivos disponibilizados</h4>
                    <div class="flex flex-wrap gap-3">
                        @if($material->arquivo_path)
                            <a href="{{ $material->arquivo_path }}" target="_blank" class="bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-5 py-3 rounded-xl text-xs font-bold flex items-center gap-2 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                Abrir Link Externo
                            </a>
                        @endif

                        @if($material->anexo_admin_path)
                            <a href="{{ asset('storage/' . $material->anexo_admin_path) }}" target="_blank" class="bg-ink text-white px-5 py-3 rounded-xl text-xs font-bold flex items-center gap-2 hover:bg-bruce transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Baixar Anexo NC5
                            </a>
                        @endif

                        @if(!$material->arquivo_path && !$material->anexo_admin_path)
                            <span class="text-sm text-slate-400 italic">Nenhum link ou anexo cadastrado.</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Timeline / conversa --}}
            @php
                // Monta timeline: comentário inicial do cliente (se houver) + replies em ordem
                $timeline = collect();
                if ($material->comentario_cliente || $material->anexo_cliente_path) {
                    $timeline->push((object)[
                        'autor_type' => 'cliente',
                        'autor_nome' => $material->cliente->user->name ?? $material->cliente->razao_social ?? 'Cliente',
                        'mensagem' => $material->comentario_cliente,
                        'anexo_path' => $material->anexo_cliente_path,
                        'created_at' => $material->data_resposta ?? $material->updated_at,
                        'is_initial' => true,
                    ]);
                }
                foreach ($material->replies as $r) {
                    $timeline->push((object)[
                        'autor_type' => $r->autor_type,
                        'autor_nome' => $r->user->name ?? ($r->autor_type === 'admin' ? 'Equipe NC5' : 'Cliente'),
                        'mensagem' => $r->mensagem,
                        'anexo_path' => $r->anexo_path,
                        'created_at' => $r->created_at,
                        'is_initial' => false,
                    ]);
                }
                $timeline = $timeline->sortBy('created_at')->values();
            @endphp

            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-[#FF7A1A]/10 text-[#FF7A1A] flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-display font-bold text-lg text-ink">Conversa</h3>
                            <p class="text-xs text-slateText">{{ $timeline->count() }} mensagem(ns)</p>
                        </div>
                    </div>
                </div>

                @if($timeline->isEmpty())
                    <div class="p-10 text-center">
                        <div class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 8h10M7 12h6m4 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-4l-4 4z"/></svg>
                        </div>
                        <h4 class="text-sm font-bold text-ink mb-1">Nenhuma mensagem ainda</h4>
                        <p class="text-xs text-slateText max-w-xs mx-auto">Quando o cliente avaliar o material, as observações dele aparecem aqui e você poderá responder abaixo.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach($timeline as $msg)
                            @php
                                $isAdmin = $msg->autor_type === 'admin';
                                $bg = $isAdmin ? 'bg-[#FF7A1A]/5' : 'bg-slate-50';
                                $avatarBg = $isAdmin ? 'bg-[#FF7A1A] text-white' : 'bg-ink text-white';
                                $badgeBg = $isAdmin ? 'bg-[#FF7A1A]/10 text-[#FF7A1A]' : 'bg-ink/5 text-ink';
                                $badgeLabel = $isAdmin ? 'Equipe NC5' : 'Cliente';
                            @endphp
                            <div class="px-6 py-5 {{ $bg }}">
                                <div class="flex gap-4">
                                    <div class="w-10 h-10 rounded-xl {{ $avatarBg }} flex items-center justify-center font-bold text-sm flex-shrink-0">
                                        {{ strtoupper(substr($msg->autor_nome, 0, 1)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                                            <span class="text-sm font-bold text-ink">{{ $msg->autor_nome }}</span>
                                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md {{ $badgeBg }}">{{ $badgeLabel }}</span>
                                            @if($msg->is_initial ?? false)
                                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md bg-amber-100 text-amber-800">Feedback inicial</span>
                                            @endif
                                            <span class="text-xs text-slateText">· {{ \Carbon\Carbon::parse($msg->created_at)->format('d/m/Y H:i') }}</span>
                                        </div>
                                        @if($msg->mensagem)
                                            <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $msg->mensagem }}</p>
                                        @endif
                                        @if($msg->anexo_path)
                                            <a href="{{ asset('storage/' . $msg->anexo_path) }}" target="_blank" class="mt-3 inline-flex items-center gap-2 bg-white hover:bg-slate-50 border border-slate-200 text-ink px-3 py-2 rounded-lg text-xs font-bold transition-colors">
                                                <svg class="w-4 h-4 text-slateText" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                Baixar anexo
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Form: nova resposta do admin --}}
                <div class="px-6 py-6 border-t border-slate-100 bg-slate-50/60">
                    <form action="{{ route('admin.materiais.replies.store', $material->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-[11px] font-extrabold uppercase tracking-widest text-slateText mb-2">Sua resposta para o cliente</label>
                            <textarea name="mensagem" required rows="4"
                                      class="w-full bg-white border border-slate-200 rounded-xl focus:ring-0 focus:border-[#FF7A1A] text-sm text-ink px-4 py-3 transition-colors placeholder-slate-400"
                                      placeholder="Explique os ajustes feitos, tire dúvidas ou peça mais informações…"></textarea>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:justify-between">
                            <div class="flex items-center gap-3">
                                <label class="cursor-pointer inline-flex items-center gap-2 bg-white border border-slate-200 hover:border-slate-300 text-ink px-4 py-2.5 rounded-xl text-xs font-bold transition-colors">
                                    <svg class="w-4 h-4 text-slateText" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                    <span x-data="{ f: null }" x-text="f ? f : 'Anexar arquivo'"></span>
                                    <input type="file" name="anexo" class="hidden" x-on:change="f = $event.target.files[0]?.name || null">
                                </label>
                                <span class="text-[11px] text-slateText">Máx. 25 MB</span>
                            </div>

                            <div class="flex items-center gap-4">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="hidden" name="reabrir" value="0">
                                    <input type="checkbox" name="reabrir" value="1" class="rounded border-slate-300 text-[#FF7A1A] focus:ring-[#FF7A1A]">
                                    <span class="text-xs font-semibold text-slate-700">Reabrir para nova aprovação</span>
                                </label>

                                <button type="submit" class="bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-colors flex items-center gap-2">
                                    Enviar ao cliente
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        {{-- ================== Sidebar ================== --}}
        <div class="space-y-6">
            {{-- Próxima ação contextual --}}
            @if(in_array($material->status_aprovacao, ['ajustes_solicitados', 'reprovado']))
                <div class="bg-gradient-to-br from-[#0A1128] to-[#141D38] text-white border border-white/10 rounded-2xl p-6">
                    <span class="text-[10px] font-extrabold text-[#FF7A1A] uppercase tracking-widest">Próxima ação</span>
                    <h3 class="font-display font-bold text-lg text-white mt-1 mb-2">Aguardando sua resposta</h3>
                    <p class="text-sm text-white/70 leading-relaxed">O cliente {{ $material->status_aprovacao === 'reprovado' ? 'reprovou' : 'pediu ajustes' }} neste material. Responda na conversa abaixo e reabra para nova aprovação quando o material atualizado estiver pronto.</p>
                </div>
            @endif

            <div class="bg-white border border-slate-200 rounded-2xl p-6">
                <h3 class="font-display font-bold text-base text-ink mb-4 pb-3 border-b border-slate-100">Cliente</h3>

                <div class="space-y-4 text-sm">
                    <div>
                        <span class="text-xs font-bold text-slateText uppercase tracking-wider block">Razão Social / Nome</span>
                        <span class="font-bold text-ink block mt-0.5">{{ $material->cliente->razao_social ?? '—' }}</span>
                    </div>

                    <div>
                        <span class="text-xs font-bold text-slateText uppercase tracking-wider block">E-mail Cadastrado</span>
                        <span class="font-semibold text-slate-700 block mt-0.5 break-all">{{ $material->cliente->user->email ?? '—' }}</span>
                    </div>

                    <div>
                        <span class="text-xs font-bold text-slateText uppercase tracking-wider block">CNPJ / CPF</span>
                        <span class="font-semibold text-slate-700 block mt-0.5">{{ $material->cliente->cpf_cnpj ?? $material->cliente->cnpj_cpf ?? '—' }}</span>
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
