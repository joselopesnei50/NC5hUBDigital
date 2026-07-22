<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">

<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.contratos.index') }}" class="text-[#8A8F9C] hover:text-[#0A1128] transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">Editar Contrato #{{ $contrato->id }}</h2>
        </div>
    </x-slot>

    @if($contrato->status_assinatura === 'assinado')
        <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <div>
                <p class="text-sm font-bold text-amber-800">Contrato já assinado pelo cliente</p>
                <p class="text-xs text-amber-700 mt-0.5">Alterações após a assinatura devem ser comunicadas ao cliente. O registro original permanece preservado.</p>
            </div>
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8 max-w-5xl">
        <form action="{{ route('admin.contratos.update', $contrato->id) }}" method="POST" id="contrato-form">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Cliente</label>
                    <select name="cliente_id" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#E63888] focus:ring-[#E63888]">
                        @foreach($clientes as $c)
                            <option value="{{ $c->id }}" @selected($contrato->cliente_id == $c->id)>{{ $c->razao_social }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Serviço (opcional)</label>
                    <select name="servico_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#E63888] focus:ring-[#E63888]">
                        <option value="">— Contrato avulso / sem serviço vinculado —</option>
                        @foreach($servicos as $s)
                            <option value="{{ $s->id }}" @selected($contrato->servico_id == $s->id)>{{ $s->nome }} — R$ {{ number_format($s->preco, 2, ',', '.') }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Data de Início</label>
                    <input type="date" name="data_inicio" value="{{ old('data_inicio', optional($contrato->data_inicio)->format('Y-m-d')) }}" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#E63888] focus:ring-[#E63888]">
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Data Final (opcional)</label>
                    <input type="date" name="data_fim" value="{{ old('data_fim', optional($contrato->data_fim)->format('Y-m-d')) }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#E63888] focus:ring-[#E63888]">
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Status</label>
                    <select name="status" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#E63888] focus:ring-[#E63888]">
                        @foreach(['ativo','pendente','inativo','cancelado'] as $st)
                            <option value="{{ $st }}" @selected($contrato->status == $st)>{{ ucfirst($st) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Corpo do Contrato</label>
                    <p class="text-xs text-[#8A8F9C] mb-3">Este texto será exibido ao cliente no portal antes da assinatura.</p>
                    <div id="quill-editor" class="rounded-xl border border-gray-300 bg-white" style="min-height: 400px;"></div>
                    <textarea name="conteudo" id="conteudo" class="hidden"></textarea>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-200 flex items-center justify-end gap-4 sticky bottom-0 bg-white py-4 -mx-8 px-8 shadow-[0_-4px_12px_rgba(0,0,0,0.06)]">
                <a href="{{ route('admin.contratos.index') }}" class="text-[#8A8F9C] hover:text-[#0A1128] font-bold text-sm">Cancelar</a>
                <button type="submit" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg">Salvar</button>
            </div>
        </form>

        <div class="mt-8 pt-6 border-t border-gray-100">
            <form action="{{ route('admin.contratos.destroy', $contrato->id) }}" method="POST" onsubmit="return confirm('Remover contrato?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800 font-bold text-sm">Excluir contrato</button>
            </form>
        </div>
    </div>
</x-admin-layout>

<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
    var quill = new Quill('#quill-editor', {
        theme: 'snow',
        placeholder: 'Digite as cláusulas e termos do contrato aqui...',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                [{ 'indent': '-1' }, { 'indent': '+1' }],
                ['clean']
            ]
        }
    });

    @if($contrato->conteudo)
        quill.clipboard.dangerouslyPasteHTML({!! json_encode($contrato->conteudo) !!});
    @endif

    document.getElementById('contrato-form').addEventListener('submit', function () {
        document.getElementById('conteudo').value = quill.root.innerHTML;
    });
</script>
