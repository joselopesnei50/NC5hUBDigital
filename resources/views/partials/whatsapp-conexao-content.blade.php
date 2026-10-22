@php
    $connectQrRoute       = $routePrefix . 'connect';
    $connectEmbeddedRoute = $routePrefix . 'cloud.embedded';
    $connectManualRoute   = $routePrefix . 'cloud.manual';
    $qrRoute              = $routePrefix . 'qr';
    $statusRoute          = $routePrefix . 'status';
    $disconnectRoute      = $routePrefix . 'disconnect';
    $inboxRoute           = $routePrefix . 'inbox';
    $botRoute             = $routePrefix . 'bot.index';
@endphp

<div class="space-y-6" x-data="{ mode: '{{ old('waba_id') ? 'cloud_manual' : 'qr' }}' }">
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3.5 rounded-2xl text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-3.5 rounded-2xl text-sm font-semibold">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-3.5 rounded-2xl text-sm">
            <p class="font-bold mb-1">Verifique os campos informados:</p>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(!$instance)
        {{-- Seletor de Modalidade de Conexão --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <button type="button"
                    @click="mode = 'qr'"
                    :class="mode === 'qr' ? 'ring-2 ring-emerald-600 bg-emerald-50/40 border-emerald-300' : 'bg-white border-black/5 hover:border-slate-300'"
                    class="text-left p-6 rounded-3xl border transition-all shadow-sm flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-display font-bold text-lg text-ink">Conexão via QR Code</span>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800">WhatsApp Web</span>
                    </div>
                    <p class="text-xs text-slate mt-1 leading-relaxed">
                        Conecta em 10 segundos lendo o QR Code com o celular da empresa (como no WhatsApp Web). Você continua usando o aparelho normalmente.
                    </p>
                </div>
            </button>

            <button type="button"
                    @click="mode = 'cloud_manual'"
                    :class="mode === 'cloud_manual' || mode === 'cloud_embedded' ? 'ring-2 ring-[#1877F2] bg-blue-50/40 border-blue-300' : 'bg-white border-black/5 hover:border-slate-300'"
                    class="text-left p-6 rounded-3xl border transition-all shadow-sm flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-100 text-[#1877F2] flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-display font-bold text-lg text-ink">API Oficial da Meta</span>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800">Cloud API v22</span>
                    </div>
                    <p class="text-xs text-slate mt-1 leading-relaxed">
                        Conexão oficial via Meta Business Manager (App <strong>NC5HUBDIGITAL-EMP</strong>). Não depende de celular ligado, alta estabilidade e suporte a Templates Oficiais.
                    </p>
                </div>
            </button>
        </div>

        {{-- PAINEL 1: QR CODE (EVOLUTION API) --}}
        <div x-show="mode === 'qr'" class="bg-white border border-black/5 rounded-3xl p-6 sm:p-8 shadow-sm">
            @if(!$evoConfigured)
                <div class="bg-amber-50 border border-amber-200 text-amber-900 px-5 py-4 rounded-xl text-sm mb-6">
                    <strong>Configuração pendente:</strong> O servidor Evolution API ainda não foi configurado em Configurações Globais. Você pode conectar agora usando a aba <strong>API Oficial da Meta</strong> ao lado.
                </div>
            @endif

            <div class="text-center py-6">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-emerald-100 flex items-center justify-center">
                    <svg class="w-8 h-8 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                </div>
                <h3 class="font-display font-bold text-xl text-ink mb-2">Conectar WhatsApp via QR Code</h3>
                <p class="text-slate text-sm mb-6 max-w-md mx-auto">
                    Clique no botão abaixo para gerar um QR Code exclusivo. Depois, abra o WhatsApp no celular em <strong>Dispositivos conectados &rarr; Conectar dispositivo</strong>.
                </p>
                <form method="POST" action="{{ route($connectQrRoute) }}" class="inline-block">
                    @csrf
                    <button type="submit" @if(!$evoConfigured) disabled @endif
                            class="px-7 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        Gerar QR Code de Conexão
                    </button>
                </form>
            </div>
        </div>

        {{-- PAINEL 2: API OFICIAL DA META (CLOUD API) --}}
        <div x-show="mode === 'cloud_manual' || mode === 'cloud_embedded'" style="display: none;" class="bg-white border border-black/5 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-5">
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-widest text-[#1877F2]">Meta WhatsApp Business Cloud API</span>
                    <h3 class="font-display font-bold text-2xl text-ink mt-0.5">Conectar pela API Oficial da Meta</h3>
                    <p class="text-xs text-slate mt-1">Conecte sua conta WABA usando o login rápido do Facebook ou colando as 3 credenciais do seu Business Manager.</p>
                </div>

                <div class="flex items-center gap-2 bg-slate-100 p-1 rounded-xl self-start">
                    <button type="button"
                            @click="mode = 'cloud_manual'"
                            :class="mode === 'cloud_manual' ? 'bg-white text-ink shadow-sm font-bold' : 'text-slate hover:text-ink'"
                            class="px-3.5 py-1.5 rounded-lg text-xs transition-all">
                        Credenciais WABA (Direto)
                    </button>
                    <button type="button"
                            @click="mode = 'cloud_embedded'"
                            :class="mode === 'cloud_embedded' ? 'bg-white text-ink shadow-sm font-bold' : 'text-slate hover:text-ink'"
                            class="px-3.5 py-1.5 rounded-lg text-xs transition-all">
                        Login Facebook (Popup)
                    </button>
                </div>
            </div>

            {{-- Sub-aba A: Conexão Manual Assistida (WABA ID + Phone Number ID + System User Token) --}}
            <div x-show="mode === 'cloud_manual'" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <div class="lg:col-span-7">
                    <form method="POST" action="{{ route($connectManualRoute) }}" autocomplete="off" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-1.5">1. WABA ID (ID da Conta do WhatsApp Business) *</label>
                            <input type="text" name="waba_id" value="{{ old('waba_id') }}" required inputmode="numeric" pattern="\d+"
                                   placeholder="Ex: 1331534925713250"
                                   class="w-full rounded-xl border-slate-300 focus:border-ink focus:ring-ink font-mono text-sm">
                            <p class="text-[11px] text-slate mt-1">Encontrado em <em>Business Manager &rarr; Contas &rarr; Contas do WhatsApp</em>.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-1.5">2. Phone Number ID (ID do Número de Telefone) *</label>
                            <input type="text" name="phone_number_id" value="{{ old('phone_number_id') }}" required inputmode="numeric" pattern="\d+"
                                   placeholder="Ex: 1168655716337509"
                                   class="w-full rounded-xl border-slate-300 focus:border-ink focus:ring-ink font-mono text-sm">
                            <p class="text-[11px] text-slate mt-1">Encontrado na aba <em>Números de telefone</em> da sua WABA ou em <em>Configuração da API</em>.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-1.5">3. System User Access Token (Token Permanente) *</label>
                            <textarea name="access_token" rows="3" required
                                      placeholder="EAAxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx..."
                                      class="w-full rounded-xl border-slate-300 focus:border-ink focus:ring-ink font-mono text-xs"></textarea>
                            <p class="text-[11px] text-slate mt-1">
                                Gere no Business Manager em <em>Usuários do Sistema</em> vinculando o app <strong>NC5HUBDIGITAL-EMP</strong> com as permissões <code class="bg-slate-100 px-1 rounded">whatsapp_business_messaging</code> e <code class="bg-slate-100 px-1 rounded">whatsapp_business_management</code>.
                            </p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-1.5">
                                4. PIN de 6 dígitos <span class="text-slate font-normal normal-case">(opcional — apenas se o número ainda não estiver registrado)</span>
                            </label>
                            <input type="text" name="pin" value="{{ old('pin') }}" maxlength="6" pattern="\d{6}" inputmode="numeric"
                                   placeholder="Ex: 142857"
                                   class="w-48 rounded-xl border-slate-300 focus:border-ink focus:ring-ink font-mono text-sm tracking-widest">
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="px-7 py-3.5 bg-[#1877F2] hover:bg-blue-700 text-white rounded-xl font-bold text-sm transition-colors shadow-sm inline-flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                Validar e Conectar WhatsApp Oficial
                            </button>
                        </div>
                    </form>
                </div>

                <div class="lg:col-span-5 bg-slate-50 border border-slate-200/80 rounded-2xl p-5 space-y-3 text-xs text-slate-700">
                    <p class="font-bold text-ink text-sm">Como obter as 3 credenciais na Meta</p>
                    <div class="space-y-2.5 leading-relaxed">
                        <p><strong>1. WABA ID:</strong> Acesse <a href="https://business.facebook.com" target="_blank" class="text-[#1877F2] font-semibold underline">business.facebook.com</a> &rarr; <em>Contas do WhatsApp</em> e copie o ID numérico da conta.</p>
                        <p><strong>2. Phone Number ID:</strong> No menu <em>WhatsApp Manager &rarr; Números de telefone</em> (ou no painel do App em <em>WhatsApp &rarr; Configuração da API</em>), copie o <strong>Identificador do número de telefone</strong>.</p>
                        <p><strong>3. Token Permanente:</strong> Em <em>Configurações do Negócio &rarr; Usuários do sistema</em>, selecione seu usuário Admin, atribua a WABA e clique em <strong>Gerar novo token</strong> selecionando o app <strong>NC5HUBDIGITAL-EMP</strong>.</p>
                        <p class="text-[11px] text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-xl p-3">
                            <strong>Segurança AES-256:</strong> O token é salvo criptografado no banco de dados e a BruceIA passa a responder imediatamente na sua linha oficial!
                        </p>
                    </div>
                </div>
            </div>

            {{-- Sub-aba B: Embedded Signup (FB.login) --}}
            <div x-show="mode === 'cloud_embedded'" style="display: none;" class="py-4">
                @if(!$embeddedConfigured)
                    <div class="bg-amber-50 border border-amber-200 text-amber-900 px-5 py-4 rounded-2xl text-sm mb-4">
                        <strong>Config ID pendente para o botão de popup:</strong> Para usar o botão de login automático do Facebook (Embedded Signup), preencha o <code>Meta App ID</code>, <code>Meta App Secret</code> e <code>Config ID</code> em <strong>Configurações Globais</strong>. Enquanto isso, você já pode conectar normalmente usando a aba <strong>Credenciais WABA (Direto)</strong>!
                    </div>
                @endif

                <div class="max-w-lg mx-auto text-center space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-1">PIN de verificação de 6 dígitos da linha</label>
                        <input id="cloud-embedded-pin" type="text" maxlength="6" pattern="\d{6}" inputmode="numeric"
                               value="{{ str_pad((string) (crc32((auth()->id() ?: 1) . '-nc5-pin') % 1000000), 6, '0', STR_PAD_LEFT) }}"
                               class="w-40 text-center rounded-xl border-slate-300 font-mono text-base tracking-widest">
                        <p class="text-[11px] text-slate mt-1">Se seu número já tem PIN de 2 etapas na Meta, digite o PIN atual acima antes de clicar.</p>
                    </div>

                    <button type="button" id="fb-embedded-signup-btn" @if(!$embeddedConfigured) disabled @endif
                            class="w-full py-4 px-6 bg-[#1877F2] hover:bg-blue-700 text-white rounded-2xl font-bold text-sm shadow-md transition-all inline-flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        Conectar com o Facebook (Embedded Signup)
                    </button>

                    <div id="cloud-embedded-status" class="text-xs"></div>
                </div>
            </div>
        </div>
    @else
        {{-- ESTADO CONECTADO (QR CODE OU API OFICIAL DA META) --}}
        <div class="bg-white border border-black/5 rounded-3xl p-6 sm:p-8 shadow-sm">
            <div class="flex flex-col md:flex-row gap-8 items-start justify-between">
                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-2.5 mb-5">
                        <span id="status-badge" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold
                            @if($instance->status === 'open') bg-emerald-100 text-emerald-800
                            @elseif($instance->status === 'connecting') bg-amber-100 text-amber-800
                            @else bg-slate-100 text-slate-700 @endif">
                            <span class="w-2 h-2 rounded-full
                                @if($instance->status === 'open') bg-emerald-500
                                @elseif($instance->status === 'connecting') bg-amber-500 animate-pulse
                                @else bg-slate-400 @endif"></span>
                            <span id="status-text">
                                @if($instance->status === 'open') Conectado e Operante
                                @elseif($instance->status === 'connecting') Aguardando leitura do QR Code
                                @else Desconectado @endif
                            </span>
                        </span>

                        @if($instance->isCloudApi())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-blue-100 text-[#1877F2]">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                API Oficial da Meta (Cloud API)
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                WhatsApp Web (QR Code)
                            </span>
                        @endif
                    </div>

                    <dl class="text-sm space-y-2.5 mb-6">
                        <div class="flex gap-2">
                            <dt class="text-slate w-40">Identificador:</dt>
                            <dd class="text-ink font-mono text-xs">{{ $instance->instance_name }}</dd>
                        </div>
                        @if($instance->phone_number)
                            <div class="flex gap-2">
                                <dt class="text-slate w-40">Linha conectada:</dt>
                                <dd class="text-ink font-bold">{{ $instance->phone_number }}</dd>
                            </div>
                        @endif
                        @if($instance->isCloudApi())
                            <div class="flex gap-2">
                                <dt class="text-slate w-40">WABA ID:</dt>
                                <dd class="text-ink font-mono text-xs">{{ $instance->waba_id }}</dd>
                            </div>
                            <div class="flex gap-2">
                                <dt class="text-slate w-40">Phone Number ID:</dt>
                                <dd class="text-ink font-mono text-xs">{{ $instance->phone_number_id }}</dd>
                            </div>
                        @endif
                        @if($instance->connected_at)
                            <div class="flex gap-2">
                                <dt class="text-slate w-40">Conectado desde:</dt>
                                <dd class="text-ink">{{ $instance->connected_at->format('d/m/Y H:i') }}</dd>
                            </div>
                        @endif
                    </dl>

                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route($inboxRoute) }}" class="px-5 py-2.5 bg-ink hover:bg-bruce text-white rounded-xl text-xs font-bold transition-colors">
                            Abrir Caixa de Entrada (Inbox)
                        </a>
                        <a href="{{ route($botRoute) }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-ink rounded-xl text-xs font-bold transition-colors">
                            Configurar / Treinar BruceIA
                        </a>
                        <form method="POST" action="{{ route($disconnectRoute) }}"
                              onsubmit="return confirm('Desconectar este número do WhatsApp?');">
                            @csrf
                            <button type="submit" class="px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold transition-colors">
                                Desconectar
                            </button>
                        </form>
                    </div>
                </div>

                @if($instance->isEvolution() && $instance->status !== 'open')
                    <div class="w-full md:w-80">
                        <div id="qr-wrapper" class="bg-mist rounded-2xl p-6 text-center">
                            <div id="qr-loading" class="py-12 text-slate text-sm">
                                <svg class="animate-spin w-8 h-8 mx-auto mb-3 text-slate" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                Gerando QR code...
                            </div>
                            <img id="qr-image" src="" alt="QR Code" class="w-full rounded-xl hidden" />
                        </div>
                        <p class="text-xs text-slate text-center mt-3">Abra o WhatsApp &rarr; Configurações &rarr; Dispositivos conectados &rarr; Conectar dispositivo</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Lista de Templates Oficiais se conectado via Meta Cloud API --}}
        @if($instance->isCloudApi())
            <div class="bg-white border border-black/5 rounded-3xl p-6 sm:p-8 shadow-sm">
                <div class="flex items-center justify-between gap-4 mb-4">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#1877F2]">Meta Business Manager</span>
                        <h3 class="font-display font-bold text-xl text-ink">Templates Oficiais da Conta ({{ count($templates) }})</h3>
                        <p class="text-xs text-slate">Modelos aprovados na sua WABA para iniciar conversas fora da janela de 24 horas.</p>
                    </div>
                </div>

                @if(empty($templates))
                    <p class="text-sm text-slate py-4">Nenhum template encontrado nesta WABA ou carregando da Meta.</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach(array_slice($templates, 0, 12) as $tpl)
                            @php
                                $tplStatus = strtoupper($tpl['status'] ?? 'UNKNOWN');
                                $badgeClass = match ($tplStatus) {
                                    'APPROVED' => 'bg-emerald-100 text-emerald-800',
                                    'PENDING'  => 'bg-amber-100 text-amber-800',
                                    default    => 'bg-rose-100 text-rose-800',
                                };
                                $bodyText = collect($tpl['components'] ?? [])->firstWhere('type', 'BODY')['text'] ?? '';
                            @endphp
                            <div class="border border-slate-200 rounded-2xl p-4 flex flex-col justify-between bg-slate-50/50">
                                <div>
                                    <div class="flex items-center justify-between gap-2 mb-2">
                                        <span class="font-mono font-bold text-xs text-ink truncate">{{ $tpl['name'] ?? 'template' }}</span>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $badgeClass }}">{{ $tplStatus }}</span>
                                    </div>
                                    <p class="text-xs text-slate-600 line-clamp-3">{{ $bodyText ?: 'Sem texto de corpo' }}</p>
                                </div>
                                <div class="mt-3 pt-2 border-t border-slate-200/70 flex items-center justify-between text-[10px] text-slate font-semibold uppercase">
                                    <span>{{ $tpl['category'] ?? 'UTILITY' }}</span>
                                    <span>{{ $tpl['language'] ?? 'pt_BR' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    @endif
</div>

@if($instance && $instance->isEvolution() && $instance->status !== 'open')
    <script>
        (function() {
            const qrImg = document.getElementById('qr-image');
            const qrLoad = document.getElementById('qr-loading');

            async function pollQr() {
                try {
                    const res = await fetch('{{ route($qrRoute) }}');
                    const data = await res.json();
                    if (data.qrcode) {
                        const src = data.qrcode.startsWith('data:') ? data.qrcode : 'data:image/png;base64,' + data.qrcode;
                        qrImg.src = src;
                        qrImg.classList.remove('hidden');
                        qrLoad.classList.add('hidden');
                    }
                } catch (e) {}
            }

            async function pollStatus() {
                try {
                    const res = await fetch('{{ route($statusRoute) }}');
                    const data = await res.json();
                    if (data.status === 'open') {
                        location.reload();
                    }
                } catch (e) {}
            }

            pollQr();
            setInterval(pollQr, 15000);
            setInterval(pollStatus, 5000);
        })();
    </script>
@endif

@if(!$instance && $embeddedConfigured)
    <script>
        (function() {
            const CLOUD_APP_ID    = @json($metaAppId);
            const CLOUD_CONFIG_ID = @json($metaConfigId);
            const CLOUD_CSRF      = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
            let __wabaData        = null;

            window.fbAsyncInit = function () {
                FB.init({
                    appId:   CLOUD_APP_ID,
                    cookie:  true,
                    xfbml:   false,
                    version: 'v22.0',
                });
            };

            (function (d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s); js.id = id;
                js.src = "https://connect.facebook.net/en_US/sdk.js";
                js.async = true; js.defer = true; js.crossOrigin = "anonymous";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));

            window.addEventListener('message', (event) => {
                if (event.origin !== 'https://www.facebook.com') return;
                try {
                    const parsed = typeof event.data === 'string' ? JSON.parse(event.data) : event.data;
                    if (parsed && parsed.type === 'WA_EMBEDDED_SIGNUP' && parsed.event === 'FINISH' && parsed.data) {
                        __wabaData = {
                            waba_id: parsed.data.waba_id,
                            phone_number_id: parsed.data.phone_number_id,
                        };
                    }
                } catch (e) {}
            });

            const btn = document.getElementById('fb-embedded-signup-btn');
            const pinInp = document.getElementById('cloud-embedded-pin');
            const statusBox = document.getElementById('cloud-embedded-status');

            if (btn) {
                btn.addEventListener('click', function () {
                    const pin = (pinInp?.value || '').trim();
                    if (!/^\d{6}$/.test(pin)) {
                        statusBox.innerHTML = '<span class="text-rose-600 font-bold">Informe um PIN numérico de 6 dígitos.</span>';
                        return;
                    }
                    if (typeof FB === 'undefined') {
                        statusBox.innerHTML = '<span class="text-amber-600 font-bold">Carregando SDK da Meta, tente novamente em 2 segundos...</span>';
                        return;
                    }
                    __wabaData = null;
                    statusBox.innerHTML = '<span class="text-blue-600 font-semibold">Aguardando autorização na janela do Facebook...</span>';

                    FB.login(function (response) {
                        if (!response || !response.authResponse || !response.authResponse.code || !__wabaData) {
                            statusBox.innerHTML = '<span class="text-rose-600 font-bold">Autorização não concluída ou cancelada.</span>';
                            return;
                        }
                        statusBox.innerHTML = '<span class="text-emerald-700 font-semibold">Ativando sua linha WhatsApp Oficial...</span>';

                        fetch('{{ route($connectEmbeddedRoute) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': CLOUD_CSRF,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                code: response.authResponse.code,
                                waba_id: __wabaData.waba_id,
                                phone_number_id: __wabaData.phone_number_id,
                                pin: pin,
                            }),
                        })
                        .then(r => r.json().then(j => ({ ok: r.ok, body: j })))
                        .then(({ ok, body }) => {
                            if (!ok) {
                                statusBox.innerHTML = '<span class="text-rose-600 font-bold">' + (body.error || 'Erro ao conectar.') + '</span>';
                                return;
                            }
                            window.location.href = body.redirect || window.location.href;
                        })
                        .catch(() => {
                            statusBox.innerHTML = '<span class="text-rose-600 font-bold">Erro de comunicação com o servidor.</span>';
                        });
                    }, {
                        config_id: CLOUD_CONFIG_ID,
                        response_type: 'code',
                        override_default_response_type: true,
                        extras: { setup: {}, sessionInfoVersion: '3' },
                    });
                });
            }
        })();
    </script>
@endif
