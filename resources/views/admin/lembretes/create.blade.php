<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-[#0A1128] leading-tight">
            Central de Lembretes & Notificações
        </h2>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-medium">
            {{ session('success') }}
        </div>
    @endif

    <div class="max-w-4xl bg-white rounded-2xl shadow-sm border border-gray-100 p-8" x-data="lembretesForm()">
        <form action="{{ route('admin.lembretes.store') }}" method="POST">
            @csrf

            <!-- Seleção de Cliente -->
            <div class="mb-8">
                <label class="block text-sm font-bold text-[#0A1128] mb-2 uppercase tracking-wider">Cliente</label>
                <select name="cliente_id" x-model="selectedClient" @change="loadPendencias" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                    <option value="">-- Selecione um cliente --</option>
                    @foreach($clientes as $cliente)
                        <option value="{{ $cliente['id'] }}">{{ $cliente['nome'] }} ({{ $cliente['email'] }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Ações Pendentes Rapidas (Aparece se o cliente tiver alguma) -->
            <div x-show="pendencias.length > 0" x-transition class="mb-8 p-6 bg-orange-50 rounded-2xl border border-orange-100 hidden" :class="{'hidden': pendencias.length === 0}">
                <h3 class="text-sm font-bold text-orange-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Ações Pendentes Encontradas
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <template x-for="acao in pendencias" :key="acao.id + acao.tipo">
                        <button type="button" @click="fillForm(acao)" class="text-left p-4 bg-white rounded-xl border border-orange-200 hover:border-orange-500 hover:shadow-md transition-all group">
                            <span class="block text-xs font-bold text-orange-600 mb-1 uppercase tracking-wider" x-text="acao.tipo"></span>
                            <span class="block text-sm text-[#0A1128] font-medium" x-text="acao.descricao"></span>
                            <span class="block text-[11px] text-gray-400 mt-3 group-hover:text-orange-500 transition-colors">Clique para preencher o e-mail &rarr;</span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Campos do E-mail -->
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Assunto do E-mail</label>
                    <input type="text" name="assunto" x-model="form.assunto" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]" placeholder="Ex: Fatura Pendente - Vencimento Hoje">
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Mensagem (Corpo do E-mail)</label>
                    <textarea name="mensagem" x-model="form.mensagem" rows="6" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]" placeholder="Digite a mensagem amigável aqui..."></textarea>
                    <p class="mt-2 text-xs text-gray-500">O e-mail será enviado com o template visual e logo da agência.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-5 bg-gray-50 rounded-xl border border-gray-100">
                    <div>
                        <label class="block text-sm font-bold text-[#0A1128] mb-2">Link da Ação (Botão) <span class="text-gray-400 font-normal">- Opcional</span></label>
                        <input type="url" name="link_acao" x-model="form.link" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128] text-sm" placeholder="https://nc5.com.br/cliente/faturas">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#0A1128] mb-2">Texto do Botão <span class="text-gray-400 font-normal">- Opcional</span></label>
                        <input type="text" name="texto_botao" x-model="form.botao" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128] text-sm" placeholder="Acessar Fatura">
                    </div>
                </div>
            </div>

            <!-- Botão de Envio -->
            <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end">
                <button type="submit" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white font-bold py-3 px-8 rounded-xl transition-colors shadow-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    Disparar Lembrete
                </button>
            </div>
        </form>
    </div>

    <!-- Script para gerenciar o estado -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('lembretesForm', () => ({
                clientesData: @json($clientes),
                selectedClient: '',
                pendencias: [],
                form: {
                    assunto: '',
                    mensagem: '',
                    link: '',
                    botao: ''
                },

                loadPendencias() {
                    this.pendencias = [];
                    // Limpa form ao trocar de cliente
                    this.form = { assunto: '', mensagem: '', link: '', botao: '' };

                    if (this.selectedClient) {
                        const cliente = this.clientesData.find(c => c.id == this.selectedClient);
                        if (cliente && cliente.pendencias) {
                            this.pendencias = cliente.pendencias;
                        }
                    }
                },

                fillForm(acao) {
                    this.form.assunto = acao.assunto;
                    this.form.mensagem = acao.mensagem;
                    this.form.link = acao.link;
                    this.form.botao = acao.botao;
                    
                    // Rola a tela até o formulário
                    window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
                }
            }))
        })
    </script>
</x-admin-layout>
