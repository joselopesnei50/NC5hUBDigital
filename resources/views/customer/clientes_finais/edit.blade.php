<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-[11px] font-extrabold text-[#FF7A1A] uppercase tracking-widest mb-1">Base de Clientes · BruceOS</p>
                <h2 class="font-display font-extrabold text-3xl text-[#0A1128] leading-tight">
                    Editar Cliente: {{ $clienteFinal->nome_empresa }}
                </h2>
            </div>
            <a href="{{ route('customer.clientes-finais.index') }}" class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-[#0A1128] border border-gray-200 px-4 py-2.5 rounded-xl text-sm font-bold transition-all shadow-sm">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Voltar à Base de Clientes
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm p-6 sm:p-8">
            <div class="mb-6 pb-5 border-b border-gray-100">
                <h3 class="font-display font-extrabold text-lg text-[#0A1128]">Atualizar Ficha Cadastral</h3>
                <p class="text-sm text-gray-500 mt-0.5">Mantenha os dados de contato e datas comemorativas sempre atualizados.</p>
            </div>

            <form action="{{ route('customer.clientes-finais.update', $clienteFinal->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Nome Empresa --}}
                    <div>
                        <label for="nome_empresa" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nome da Empresa / Cliente *</label>
                        <input type="text" name="nome_empresa" id="nome_empresa" value="{{ old('nome_empresa', $clienteFinal->nome_empresa) }}" required class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">
                        @error('nome_empresa') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Nome Responsável --}}
                    <div>
                        <label for="nome_responsavel" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nome do Responsável</label>
                        <input type="text" name="nome_responsavel" id="nome_responsavel" value="{{ old('nome_responsavel', $clienteFinal->nome_responsavel) }}" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">
                        @error('nome_responsavel') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Telefone --}}
                    <div>
                        <label for="telefone" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Telefone / WhatsApp</label>
                        <input type="text" name="telefone" id="telefone" value="{{ old('telefone', $clienteFinal->telefone) }}" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">
                        @error('telefone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- E-mail --}}
                    <div>
                        <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">E-mail</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $clienteFinal->email) }}" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">
                        @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Cidade --}}
                    <div>
                        <label for="cidade" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Cidade / UF</label>
                        <input type="text" name="cidade" id="cidade" value="{{ old('cidade', $clienteFinal->cidade) }}" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">
                        @error('cidade') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Datas de Aniversário --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="data_aniversario_empresa" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Fundação Empresa</label>
                            <input type="date" name="data_aniversario_empresa" id="data_aniversario_empresa" value="{{ old('data_aniversario_empresa', $clienteFinal->data_aniversario_empresa ? $clienteFinal->data_aniversario_empresa->format('Y-m-d') : '') }}" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">
                            @error('data_aniversario_empresa') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="data_aniversario_responsavel" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Aniv. Responsável</label>
                            <input type="date" name="data_aniversario_responsavel" id="data_aniversario_responsavel" value="{{ old('data_aniversario_responsavel', $clienteFinal->data_aniversario_responsavel ? $clienteFinal->data_aniversario_responsavel->format('Y-m-d') : '') }}" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">
                            @error('data_aniversario_responsavel') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Últimos Pedidos --}}
                    <div class="col-span-1 md:col-span-2">
                        <label for="ultimos_pedidos" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Histórico / Observações de Relacionamento</label>
                        <textarea name="ultimos_pedidos" id="ultimos_pedidos" rows="4" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">{{ old('ultimos_pedidos', $clienteFinal->ultimos_pedidos) }}</textarea>
                        @error('ultimos_pedidos') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('customer.clientes-finais.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-bold text-gray-600 hover:bg-gray-50 transition-all">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-all shadow-sm">
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>

        @if(isset($icsUrl))
            <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm p-6 sm:p-8" x-data="{ copied: false }">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div>
                        <span class="inline-block text-[10px] font-extrabold text-[#FF7A1A] uppercase tracking-widest mb-1">Sincronização iCal</span>
                        <h4 class="font-display font-extrabold text-base text-[#0A1128]">Link da Agenda deste Cliente</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Compartilhe este link com o cliente para sincronizar os compromissos no Google Calendar, Outlook ou Apple Calendar.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2.5 flex-wrap">
                    <input type="text" readonly value="{{ $icsUrl }}" x-ref="ics"
                           class="flex-1 min-w-[260px] px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-mono text-gray-600 outline-none">
                    <button type="button"
                            @click="navigator.clipboard.writeText($refs.ics.value); copied = true; setTimeout(() => copied = false, 2000)"
                            class="px-5 py-2.5 bg-[#0A1128] hover:bg-[#141D38] text-white rounded-xl text-xs font-bold transition-all">
                        <span x-show="!copied">Copiar Link</span>
                        <span x-show="copied" x-cloak class="text-emerald-400">Copiado!</span>
                    </button>
                    <form method="POST" action="{{ route('customer.calendar.cliente-final.regenerate', $clienteFinal) }}" onsubmit="return confirm('O link antigo deixará de funcionar. Confirma?')">
                        @csrf
                        <button type="submit" class="px-4 py-2.5 bg-white border border-gray-200 hover:border-red-300 hover:bg-red-50 text-red-600 rounded-xl text-xs font-bold transition-all">
                            Regenerar Token
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
