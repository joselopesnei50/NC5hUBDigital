<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-display font-extrabold text-2xl text-ink leading-tight tracking-tight">
                    Enviar Novo Material para Aprovação
                </h2>
                <p class="text-slateText text-sm mt-1">Disponibilize arquivos, links do Figma/Canva/Drive e orientações diretamente para o portal do cliente.</p>
            </div>
            <a href="{{ route('admin.materiais.index') }}" class="text-slateText hover:text-ink font-bold text-sm transition-colors">
                ← Voltar para lista
            </a>
        </div>
    </x-slot>

    <div class="bg-white border border-slate-200/80 rounded-3xl shadow-premium p-8 max-w-4xl">
        <form action="{{ route('admin.materiais.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Card Informação Inicial -->
            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200/80 text-amber-900 text-xs font-medium flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold text-base flex-shrink-0">✉</span>
                <span>Ao cadastrar este material, um e-mail com notificação e link direto de aprovação será encaminhado automaticamente para o cliente selecionado.</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Cliente -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-xs font-extrabold text-slateText uppercase tracking-wider mb-2">Cliente Destinatário *</label>
                    <select name="cliente_id" required class="w-full rounded-2xl border-slate-200 bg-slate-50/50 p-3.5 text-sm font-semibold text-ink focus:border-bruce focus:ring-bruce">
                        <option value="">Selecione o Cliente...</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->razao_social }} ({{ $cliente->user->email ?? 'Sem e-mail' }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tipo -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-xs font-extrabold text-slateText uppercase tracking-wider mb-2">Tipo / Categoria de Material</label>
                    <select name="tipo" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 p-3.5 text-sm font-semibold text-ink focus:border-bruce focus:ring-bruce">
                        <option value="Arte / Criativo Instagram">Arte / Criativo Instagram</option>
                        <option value="Carrossel Conteúdo">Carrossel Conteúdo</option>
                        <option value="Vídeo / Reel / Tiktok">Vídeo / Reel / Tiktok</option>
                        <option value="Landing Page / Web">Landing Page / Web</option>
                        <option value="Identidade Visual / Logo">Identidade Visual / Logo</option>
                        <option value="Copywriting / Texto">Copywriting / Texto</option>
                        <option value="Outro Material">Outro Material</option>
                    </select>
                </div>

                <!-- Título -->
                <div class="col-span-2">
                    <label class="block text-xs font-extrabold text-slateText uppercase tracking-wider mb-2">Título do Material / Campanha *</label>
                    <input type="text" name="titulo" required class="w-full rounded-2xl border-slate-200 bg-slate-50/50 p-3.5 text-sm font-semibold text-ink focus:border-bruce focus:ring-bruce" placeholder="Ex: Arte Feed - Lançamento Coleção Verão 2026">
                </div>

                <!-- Link Externo -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-xs font-extrabold text-slateText uppercase tracking-wider mb-2">Link Externo (Canva, Figma, Drive, Vimeo)</label>
                    <input type="url" name="arquivo_path" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 p-3.5 text-sm font-medium text-ink focus:border-bruce focus:ring-bruce" placeholder="https://www.figma.com/file/...">
                </div>

                <!-- Upload de Arquivo Direct -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-xs font-extrabold text-slateText uppercase tracking-wider mb-2">Ou Upload de Arquivo (PNG, JPG, PDF, ZIP)</label>
                    <input type="file" name="anexo_admin" class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-ink file:text-white hover:file:bg-bruce">
                </div>

                <!-- Instruções / Contexto NC5 -->
                <div class="col-span-2">
                    <label class="block text-xs font-extrabold text-slateText uppercase tracking-wider mb-2">Instruções / Notas da Equipe NC5 para o Cliente</label>
                    <textarea name="descricao" rows="4" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 p-3.5 text-sm font-medium text-ink focus:border-bruce focus:ring-bruce" placeholder="Escreva orientações para o cliente revisar este material (ex: Por favor, analise a paleta de cores da lâmina 2 e valide se a chamada principal atende ao objetivo da campanha)..."></textarea>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-4">
                <a href="{{ route('admin.materiais.index') }}" class="text-slateText hover:text-ink font-bold text-sm transition-colors">Cancelar</a>
                <button type="submit" class="bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-8 py-3.5 rounded-2xl text-sm font-bold transition-all shadow-md transform hover:-translate-y-0.5">
                    Enviar para Aprovação do Cliente
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
