<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.campanhas.index') }}" class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-[#0A1128] hover:border-[#0A1128] transition-colors">
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
        <form action="{{ route('admin.campanhas.store') }}" method="POST" class="bg-white border border-gray-100 rounded-[24px] shadow-sm p-8">
            @csrf

            <div class="mb-10 border-b border-gray-100 pb-10">
                <h3 class="text-lg font-bold text-[#0A1128] mb-1">Configurações Básicas</h3>
                <p class="text-sm text-[#8A8F9C] mb-6">Defina o nome de identificação e o assunto que o cliente verá.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-[#0A1128] mb-2 uppercase tracking-wider">Nome da Campanha (Interno)</label>
                        <input type="text" name="nome" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]" placeholder="Ex: Black Friday 2026">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#0A1128] mb-2 uppercase tracking-wider">Assunto do E-mail</label>
                        <input type="text" name="assunto" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]" placeholder="Ex: Uma oportunidade exclusiva para você...">
                    </div>
                </div>
            </div>

            <div class="mb-10 border-b border-gray-100 pb-10">
                <h3 class="text-lg font-bold text-[#0A1128] mb-1">Audiência (Para quem enviar?)</h3>
                <p class="text-sm text-[#8A8F9C] mb-6">Escolha o público alvo que receberá este e-mail.</p>

                <div class="grid grid-cols-1 gap-4">
                    <label class="relative flex cursor-pointer rounded-xl border border-gray-200 bg-white p-5 focus:outline-none hover:bg-gray-50 transition-colors">
                        <input type="radio" name="audience" value="leads_ia" class="mt-0.5 h-4 w-4 shrink-0 cursor-pointer text-[#FF7A1A] border-gray-300 focus:ring-[#FF7A1A]" checked>
                        <div class="ml-4 flex flex-col">
                            <span class="block text-sm font-bold text-[#0A1128]">Leads da Inteligência Artificial</span>
                            <span class="block text-xs text-[#8A8F9C] mt-1">Sincroniza automaticamente todos os {{ $totalLeads }} leads que fizeram o diagnóstico gratuito pelo site.</span>
                        </div>
                    </label>

                    <label class="relative flex cursor-not-allowed rounded-xl border border-gray-200 bg-gray-50/50 p-5 opacity-60">
                        <input type="radio" name="audience" value="csv" class="mt-0.5 h-4 w-4 shrink-0 text-gray-300 border-gray-300" disabled>
                        <div class="ml-4 flex flex-col">
                            <span class="block text-sm font-bold text-gray-500">Planilha (CSV) <span class="ml-2 text-[10px] bg-gray-200 px-2 py-0.5 rounded-md text-gray-600">Em Breve</span></span>
                            <span class="block text-xs text-gray-400 mt-1">Faça upload de uma planilha com e-mails e nomes para disparar.</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="mb-10">
                <h3 class="text-lg font-bold text-[#0A1128] mb-1">Design do E-mail (Template Brevo)</h3>
                <p class="text-sm text-[#8A8F9C] mb-6">Selecione um template profissional criado na sua conta do Brevo.</p>

                @if(empty($templates))
                    <div class="p-6 bg-orange-50 border border-orange-200 rounded-xl text-center">
                        <p class="text-sm font-bold text-orange-800">Nenhum template encontrado no Brevo.</p>
                        <p class="text-xs text-orange-700 mt-1">Crie um template no painel do Brevo, marque-o como Ativo, e atualize esta página.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-h-[400px] overflow-y-auto pr-2">
                        @foreach($templates as $tpl)
                            <label class="relative flex cursor-pointer rounded-xl border border-gray-200 bg-white p-4 focus:outline-none hover:border-[#FF7A1A] transition-colors group has-[:checked]:border-[#FF7A1A] has-[:checked]:bg-[#FF7A1A]/5">
                                <input type="radio" name="template_id" value="{{ $tpl['id'] }}" class="peer sr-only" required>
                                
                                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0 mr-4 overflow-hidden border border-gray-200">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <span class="block text-sm font-bold text-[#0A1128] truncate">{{ $tpl['name'] }}</span>
                                    <span class="block text-xs text-[#8A8F9C] mt-0.5">ID: #{{ $tpl['id'] }}</span>
                                </div>
                                <div class="absolute top-4 right-4 text-[#FF7A1A] opacity-0 peer-checked:opacity-100 transition-opacity">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif
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
