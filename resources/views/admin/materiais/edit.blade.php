<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-display font-extrabold text-2xl text-ink leading-tight tracking-tight">
                    Editar Material #{{ $material->id }}
                </h2>
                <p class="text-slateText text-sm mt-1">Atualize informações do material ou altere manualmente seu status de aprovação.</p>
            </div>
            <a href="{{ route('admin.materiais.index') }}" class="text-slateText hover:text-ink font-bold text-sm transition-colors">
                ← Voltar para lista
            </a>
        </div>
    </x-slot>

    <div class="bg-white border border-slate-200/80 rounded-3xl shadow-premium p-8 max-w-4xl">
        <form action="{{ route('admin.materiais.update', $material->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Cliente -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-xs font-extrabold text-slateText uppercase tracking-wider mb-2">Cliente Destinatário *</label>
                    <select name="cliente_id" required class="w-full rounded-2xl border-slate-200 bg-slate-50/50 p-3.5 text-sm font-semibold text-ink focus:border-bruce focus:ring-bruce">
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" {{ $material->cliente_id == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->razao_social }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status de Aprovação -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-xs font-extrabold text-slateText uppercase tracking-wider mb-2">Status de Aprovação *</label>
                    <select name="status_aprovacao" required class="w-full rounded-2xl border-slate-200 bg-slate-50/50 p-3.5 text-sm font-bold text-ink focus:border-bruce focus:ring-bruce">
                        <option value="pendente" {{ $material->status_aprovacao == 'pendente' ? 'selected' : '' }}>⏳ Pendente / Aguardando Cliente</option>
                        <option value="aprovado" {{ $material->status_aprovacao == 'aprovado' ? 'selected' : '' }}>✓ Aprovado pelo Cliente</option>
                        <option value="ajustes_solicitados" {{ $material->status_aprovacao == 'ajustes_solicitados' ? 'selected' : '' }}>⚠️ Ajustes Solicitados</option>
                        <option value="reprovado" {{ $material->status_aprovacao == 'reprovado' ? 'selected' : '' }}>❌ Reprovado</option>
                    </select>
                </div>

                <!-- Título -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-xs font-extrabold text-slateText uppercase tracking-wider mb-2">Título do Material *</label>
                    <input type="text" name="titulo" value="{{ old('titulo', $material->titulo) }}" required class="w-full rounded-2xl border-slate-200 bg-slate-50/50 p-3.5 text-sm font-semibold text-ink focus:border-bruce focus:ring-bruce">
                </div>

                <!-- Tipo -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-xs font-extrabold text-slateText uppercase tracking-wider mb-2">Tipo / Categoria</label>
                    <input type="text" name="tipo" value="{{ old('tipo', $material->tipo) }}" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 p-3.5 text-sm font-semibold text-ink focus:border-bruce focus:ring-bruce">
                </div>

                <!-- Link Externo -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-xs font-extrabold text-slateText uppercase tracking-wider mb-2">Link do Arquivo (Figma, Canva, Drive)</label>
                    <input type="url" name="arquivo_path" value="{{ old('arquivo_path', $material->arquivo_path) }}" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 p-3.5 text-sm font-medium text-ink focus:border-bruce focus:ring-bruce">
                </div>

                <!-- Substituir Arquivo Anexo -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-xs font-extrabold text-slateText uppercase tracking-wider mb-2">Substituir Arquivo Anexo</label>
                    <input type="file" name="anexo_admin" class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-ink file:text-white hover:file:bg-bruce">
                    @if($material->anexo_admin_path)
                        <a href="{{ asset('storage/' . $material->anexo_admin_path) }}" target="_blank" class="inline-block mt-2 text-xs font-bold text-bruce hover:underline">
                            📎 Ver arquivo anexo atual
                        </a>
                    @endif
                </div>

                <!-- Instruções da NC5 -->
                <div class="col-span-2">
                    <label class="block text-xs font-extrabold text-slateText uppercase tracking-wider mb-2">Instruções da Equipe NC5</label>
                    <textarea name="descricao" rows="4" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 p-3.5 text-sm font-medium text-ink focus:border-bruce focus:ring-bruce">{{ old('descricao', $material->descricao) }}</textarea>
                </div>

                <!-- Observações do Cliente -->
                <div class="col-span-2">
                    <label class="block text-xs font-extrabold text-slateText uppercase tracking-wider mb-2">Observações Registradas do Cliente</label>
                    <textarea name="comentario_cliente" rows="3" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 p-3.5 text-sm font-medium text-ink focus:border-bruce focus:ring-bruce" placeholder="Observações enviadas pelo cliente...">{{ old('comentario_cliente', $material->comentario_cliente) }}</textarea>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                <form action="{{ route('admin.materiais.destroy', $material->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este material?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-rose-500 hover:text-rose-700 text-xs font-bold transition-colors">
                        Excluir Material
                    </button>
                </form>

                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.materiais.index') }}" class="text-slateText hover:text-ink font-bold text-sm transition-colors">Cancelar</a>
                    <button type="submit" class="bg-ink hover:bg-bruce text-white px-8 py-3.5 rounded-2xl text-sm font-bold transition-all shadow-md">
                        Salvar Alterações
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-admin-layout>
