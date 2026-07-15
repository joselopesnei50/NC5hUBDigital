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

            {{-- ============================================================
                 SEÇÃO E-MAIL · Brevo + identidade + reply-to
                 ============================================================ --}}
            @php
                $brevoConectado = !empty($configuracoes['brevo_api_key'] ?? null);
                $chaveMascarada = $brevoConectado
                    ? '••••••••••••••••••••' . substr($configuracoes['brevo_api_key'], -4)
                    : '';
            @endphp
            <div class="mb-10 border-b border-gray-100 pb-10">
                <div class="flex items-start justify-between gap-4 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#F4F5F7] flex items-center justify-center text-[#0A1128]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-[#0A1128] leading-tight">Envio de e-mails</h3>
                            <p class="text-xs text-[#8A8F9C] mt-0.5">Servidor SMTP · identidade · caixa de resposta</p>
                        </div>
                    </div>

                    @if($brevoConectado)
                        <span class="inline-flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 text-[11px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-full">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                            Brevo conectado
                        </span>
                    @else
                        <span class="inline-flex items-center gap-2 bg-orange-50 border border-orange-200 text-orange-700 text-[11px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-full">
                            <span class="w-1.5 h-1.5 bg-orange-500 rounded-full"></span>
                            Sem credencial
                        </span>
                    @endif
                </div>

                {{-- Subseção 1 · Servidor SMTP --}}
                <div class="bg-[#F4F5F7] rounded-2xl p-6 mb-4">
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#8A8F9C] mb-4">Servidor SMTP · Brevo</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-[#0A1128] mb-2 uppercase tracking-wider">Login SMTP</label>
                            <input type="text" name="brevo_smtp_login" value="{{ $configuracoes['brevo_smtp_login'] ?? '' }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128] bg-white" placeholder="usuario@dominio.com.br">
                            <p class="mt-1.5 text-[11px] text-[#8A8F9C]">Login gerado em <span class="font-semibold">Brevo → SMTP & API → SMTP</span>.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[#0A1128] mb-2 uppercase tracking-wider">Chave SMTP</label>
                            <input type="password" name="brevo_api_key" autocomplete="new-password" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128] bg-white font-mono text-sm" placeholder="{{ $brevoConectado ? $chaveMascarada : 'xsmtpsib-xxxxxxxxxxxxxxxxxxxx' }}">
                            <p class="mt-1.5 text-[11px] text-[#8A8F9C]">
                                @if($brevoConectado)
                                    Chave já salva. Deixe em branco para <span class="font-semibold">manter a atual</span> ou digite uma nova para substituir.
                                @else
                                    A chave nunca é exibida depois de salva.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Subseção 2 · Identidade da mensagem --}}
                <div class="bg-[#F4F5F7] rounded-2xl p-6 mb-4">
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#8A8F9C] mb-4">Identidade do remetente · quem aparece como autor</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-[#0A1128] mb-2 uppercase tracking-wider">E-mail (From)</label>
                            <input type="email" name="mail_from_address" value="{{ $configuracoes['mail_from_address'] ?? 'contato@nc5.com.br' }}" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128] bg-white" placeholder="contato@nc5.com.br">
                            <p class="mt-1.5 text-[11px] text-[#8A8F9C]">Deve ser um domínio <span class="font-semibold">autenticado no Brevo</span> (SPF/DKIM), senão o e-mail vai para spam.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[#0A1128] mb-2 uppercase tracking-wider">Nome de exibição</label>
                            <input type="text" name="mail_from_name" value="{{ $configuracoes['mail_from_name'] ?? 'NC5 Hub' }}" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128] bg-white" placeholder="Ex: NC5 Hub">
                            <p class="mt-1.5 text-[11px] text-[#8A8F9C]">Aparece antes do e-mail na caixa de entrada.</p>
                        </div>
                    </div>
                </div>

                {{-- Subseção 3 · Reply-To --}}
                <div class="bg-[#F4F5F7] rounded-2xl p-6">
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#8A8F9C]">Respostas do cliente · Reply-To</p>
                            <p class="text-xs text-[#8A8F9C] mt-1 max-w-xl">Quando o cliente clicar em "Responder" no e-mail, a mensagem vai para <span class="font-semibold text-[#0A1128]">este endereço</span> em vez do remetente técnico. Deixe em branco para receber no próprio From.</p>
                        </div>
                        <span class="hidden sm:inline-flex bg-white border border-black/5 text-[10px] font-bold text-[#8A8F9C] uppercase tracking-wider px-2 py-1 rounded-full">Recomendado</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-[#0A1128] mb-2 uppercase tracking-wider">E-mail para respostas</label>
                            <input type="email" name="mail_reply_to" value="{{ $configuracoes['mail_reply_to'] ?? '' }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128] bg-white" placeholder="atendimento@nc5.com.br">
                            <p class="mt-1.5 text-[11px] text-[#8A8F9C]">Use uma <span class="font-semibold">caixa monitorada</span> (ex.: atendimento@).</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[#0A1128] mb-2 uppercase tracking-wider">Nome exibido nas respostas <span class="text-[#8A8F9C] font-normal normal-case">(opcional)</span></label>
                            <input type="text" name="mail_reply_to_name" value="{{ $configuracoes['mail_reply_to_name'] ?? '' }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128] bg-white" placeholder="Ex: Atendimento NC5">
                        </div>
                    </div>
                </div>

                @if(!empty($configuracoes['mail_reply_to'] ?? null) || !empty($configuracoes['mail_from_address'] ?? null))
                    <div class="mt-5 p-4 bg-[#0A1128] rounded-xl text-white text-xs">
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-white/50 mb-2">Prévia do cabeçalho</p>
                        <div class="font-mono space-y-1 text-white/85">
                            <div><span class="text-white/40">From:</span> {{ $configuracoes['mail_from_name'] ?? 'NC5 Hub' }} &lt;{{ $configuracoes['mail_from_address'] ?? '—' }}&gt;</div>
                            @if(!empty($configuracoes['mail_reply_to'] ?? null))
                                <div><span class="text-white/40">Reply-To:</span> {{ $configuracoes['mail_reply_to_name'] ?? ($configuracoes['mail_from_name'] ?? '') }} &lt;{{ $configuracoes['mail_reply_to'] }}&gt;</div>
                            @endif
                        </div>
                    </div>
                @endif
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
