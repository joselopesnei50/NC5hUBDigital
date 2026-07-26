<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('campanhas.index') }}" class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-[#0A1128] hover:border-[#0A1128] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <p class="text-[11px] font-bold text-[#FF7A1A] uppercase tracking-[0.2em] mb-1">E-mail Marketing</p>
                <h2 class="font-display font-bold text-2xl text-[#0A1128] leading-tight">
                    Nova Campanha
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl">
        <form action="{{ route('campanhas.store') }}" method="POST" enctype="multipart/form-data" class="bg-white border border-gray-100 rounded-[24px] shadow-sm p-8" x-data="{ audience: 'leads_ia' }">
            @csrf

            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 font-medium">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-10 border-b border-gray-100 pb-10">
                <h3 class="text-lg font-bold text-[#0A1128] mb-1">Configurações Básicas</h3>
                <p class="text-sm text-[#8A8F9C] mb-6">Defina o nome de identificação e o assunto que o cliente verá.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-[#0A1128] mb-2 uppercase tracking-wider">Nome da Campanha (Interno)</label>
                        <input type="text" name="nome" required value="{{ old('nome') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]" placeholder="Ex: Oferta Exclusiva 2026">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#0A1128] mb-2 uppercase tracking-wider">Assunto do E-mail</label>
                        <input type="text" name="assunto" required value="{{ old('assunto') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]" placeholder="Ex: Uma oportunidade exclusiva para você...">
                    </div>
                </div>
            </div>

            <div class="mb-10 border-b border-gray-100 pb-10">
                <h3 class="text-lg font-bold text-[#0A1128] mb-1">Audiência (Para quem enviar?)</h3>
                <p class="text-sm text-[#8A8F9C] mb-6">Escolha o público alvo que receberá este e-mail.</p>

                <div class="grid grid-cols-1 gap-4">
                    <label class="relative flex cursor-pointer rounded-xl border border-gray-200 bg-white p-5 focus:outline-none hover:bg-gray-50 transition-colors" :class="{'border-[#FF7A1A] bg-[#FF7A1A]/5': audience === 'leads_ia'}">
                        <input type="radio" name="audience" value="leads_ia" x-model="audience" class="mt-0.5 h-4 w-4 shrink-0 cursor-pointer text-[#FF7A1A] border-gray-300 focus:ring-[#FF7A1A]">
                        <div class="ml-4 flex flex-col">
                            <span class="block text-sm font-bold text-[#0A1128]">Leads da Inteligência Artificial</span>
                            <span class="block text-xs text-[#8A8F9C] mt-1">Sincroniza automaticamente todos os {{ $totalLeads }} leads que possuem e-mail válido e fizeram o diagnóstico.</span>
                        </div>
                    </label>

                    <label class="relative flex cursor-pointer rounded-xl border border-gray-200 bg-white p-5 focus:outline-none hover:bg-gray-50 transition-colors" :class="{'border-[#FF7A1A] bg-[#FF7A1A]/5': audience === 'csv'}">
                        <input type="radio" name="audience" value="csv" x-model="audience" class="mt-0.5 h-4 w-4 shrink-0 cursor-pointer text-[#FF7A1A] border-gray-300 focus:ring-[#FF7A1A]">
                        <div class="ml-4 flex flex-col w-full">
                            <span class="block text-sm font-bold text-[#0A1128]">Subir Planilha (CSV)</span>
                            <span class="block text-xs text-[#8A8F9C] mt-1">Faça upload de uma planilha .csv contendo colunas com NOME e EMAIL.</span>
                            
                            <div class="mt-4" x-show="audience === 'csv'" x-transition>
                                <input type="file" name="csv_file" accept=".csv" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#FF7A1A]/10 file:text-[#FF7A1A] hover:file:bg-[#FF7A1A]/20 transition-colors">
                                <p class="text-[11px] text-gray-400 mt-2">Dica: A primeira linha do CSV deve ser o cabeçalho (ex: EMAIL,NOME).</p>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="mb-10">
                <h3 class="text-lg font-bold text-[#0A1128] mb-1">Design do E-mail</h3>
                <p class="text-sm text-[#8A8F9C] mb-6">Escreva o conteúdo do e-mail. Você pode usar HTML para estilizar a mensagem.</p>

                <div class="bg-[#F4F5F7] p-2 rounded-xl border border-gray-200">
                    <textarea name="html_content" rows="12" class="w-full rounded-lg border-none focus:ring-0 bg-white p-4 font-mono text-sm text-[#0A1128]" placeholder="<html>
<body>
  <h1>Olá!</h1>
  <p>Escreva sua mensagem aqui...</p>
</body>
</html>">{{ old('html_content') }}</textarea>
                </div>
                <p class="text-[11px] text-gray-400 mt-2">Este código HTML será enviado diretamente para a API do Brevo e renderizado na caixa de entrada do cliente.</p>
            </div>

            <div class="flex justify-end border-t border-gray-100 pt-8 mt-4">
                <button type="submit" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-8 py-3 rounded-xl text-sm font-bold transition-all shadow-lg flex items-center gap-2" onclick="this.innerHTML='<svg class=\'animate-spin -ml-1 mr-2 h-4 w-4 text-white\' fill=\'none\' viewBox=\'0 0 24 24\'><circle class=\'opacity-25\' cx=\'12\' cy=\'12\' r=\'10\' stroke=\'currentColor\' stroke-width=\'4\'></circle><path class=\'opacity-75\' fill=\'currentColor\' d=\'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z\'></path></svg> Disparando...'; this.classList.add('cursor-not-allowed');">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    Disparar Campanha Agora
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
