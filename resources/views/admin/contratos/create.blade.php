<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">

<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">
            Emitir Novo Contrato
        </h2>
    </x-slot>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8 max-w-5xl">
        <form action="{{ route('admin.contratos.store') }}" method="POST" id="contrato-form">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Cliente / Contratante</label>
                    <select name="cliente_id" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                        <option value="">Selecione o Cliente...</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->razao_social }} ({{ $cliente->cpf_cnpj }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Serviço (opcional)</label>
                    <select name="servico_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                        <option value="">— Contrato avulso / sem serviço vinculado —</option>
                        @foreach($servicos as $servico)
                            <option value="{{ $servico->id }}">{{ $servico->nome }} — R$ {{ number_format($servico->preco, 2, ',', '.') }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Data de Início</label>
                    <input type="date" name="data_inicio" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Data de Término (Opcional)</label>
                    <input type="date" name="data_fim" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Corpo do Contrato</label>
                    <p class="text-xs text-[#8A8F9C] mb-3">Escreva as cláusulas, termos e condições. O cliente verá este texto antes de assinar.</p>
                    <div id="quill-editor" class="rounded-xl border border-gray-300 bg-white" style="min-height: 400px;"></div>
                    <textarea name="conteudo" id="conteudo" class="hidden"></textarea>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-200 flex items-center justify-end gap-4 sticky bottom-0 bg-white py-4 -mx-8 px-8 shadow-[0_-4px_12px_rgba(0,0,0,0.06)]">
                <a href="{{ route('admin.contratos.index') }}" class="text-[#8A8F9C] hover:text-[#0A1128] font-bold text-sm transition-colors">Cancelar</a>
                <button type="submit" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg">
                    Emitir Contrato
                </button>
            </div>
        </form>
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
    document.getElementById('contrato-form').addEventListener('submit', function () {
        document.getElementById('conteudo').value = quill.root.innerHTML;
    });
</script>
