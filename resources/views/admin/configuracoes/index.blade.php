<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">
                Configurações Globais
            </h2>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-medium">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8 max-w-4xl">
        
        <form action="{{ route('admin.configuracoes.store') }}" method="POST">
            @csrf

            <!-- Seção Brevo -->
            <div class="mb-8 border-b border-gray-100 pb-8">
                <h3 class="text-lg font-bold text-[#0A1128] flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-[#E63888]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"></path></svg>
                    Integração Brevo (E-mails Transacionais)
                </h3>
                <p class="text-sm text-[#8A8F9C] mb-6">
                    A chave SMTP gerada no painel do Brevo será utilizada para disparar todas as notificações e e-mails do sistema via API segura do servidor.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-[#0A1128] mb-2">Login SMTP (Brevo Email)</label>
                        <input type="text" name="brevo_smtp_login" value="{{ $configuracoes['brevo_smtp_login'] ?? '' }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]" placeholder="seuemail@nc5.com.br">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-[#0A1128] mb-2">Chave SMTP (Brevo API)</label>
                        <input type="password" name="brevo_api_key" value="{{ $configuracoes['brevo_api_key'] ?? '' }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128] font-mono text-sm" placeholder="xsmtpsib-xxxxxxxxxxxxxxxxxxxxxxx">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-[#0A1128] mb-2">E-mail do Remetente (From)</label>
                        <input type="email" name="mail_from_address" value="{{ $configuracoes['mail_from_address'] ?? 'contato@nc5.com.br' }}" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]" placeholder="contato@seudominio.com.br">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-[#0A1128] mb-2">Nome do Remetente</label>
                        <input type="text" name="mail_from_name" value="{{ $configuracoes['mail_from_name'] ?? 'NC5 Hub' }}" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]" placeholder="Ex: NC5 Hub">
                    </div>
                </div>
            </div>

            <!-- Motor BruceIA -->
            <div class="mb-8 border-b border-gray-100 pb-8">
                <h3 class="text-lg font-bold text-[#0A1128] flex items-center gap-3 mb-4">
                    <img src="{{ asset('images/bruce/bruceia-icone-fundo-claro.svg') }}" alt="BruceIA" class="w-6 h-6">
                    Motor BruceIA
                </h3>
                <p class="text-sm text-[#8A8F9C] mb-6">
                    Chave usada pelo BruceIA para gerar as análises gratuitas na página pública de captação de leads. Somente admins têm acesso a este campo.
                </p>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-[#0A1128] mb-2">Chave do motor de IA</label>
                        <input type="password" name="deepseek_api_key" value="{{ $configuracoes['deepseek_api_key'] ?? '' }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128] font-mono text-sm" placeholder="sk-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                        <p class="mt-1 text-xs text-gray-500">Interno: o BruceIA opera hoje sobre a API DeepSeek. Cole aqui a secret key gerada no painel de desenvolvedores do provedor.</p>
                    </div>
                </div>
            </div>

            <!-- Gateway AbacatePay -->
            <div class="mb-8 border-b border-gray-100 pb-8">
                <h3 class="text-lg font-bold text-[#0A1128] flex items-center gap-3 mb-4">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Gateway AbacatePay
                </h3>
                <p class="text-sm text-[#8A8F9C] mb-6">
                    Configure sua chave de API para habilitar a geração automática de links de checkout para as faturas.
                </p>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-[#0A1128] mb-2">Chave da API (Bearer Token)</label>
                        <input type="password" name="abacatepay_api_key" value="{{ $configuracoes['abacatepay_api_key'] ?? '' }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128] font-mono text-sm" placeholder="sua-api-key-aqui">
                    </div>
                </div>
            </div>

            <!-- Botões -->
            <div class="flex items-center justify-end gap-4">
                <button type="submit" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-8 py-3 rounded-xl text-sm font-bold transition-colors shadow-lg">
                    Salvar Configurações
                </button>
            </div>
        </form>

    </div>
</x-admin-layout>
