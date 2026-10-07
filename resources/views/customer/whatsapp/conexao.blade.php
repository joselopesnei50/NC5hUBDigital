<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold text-bruce uppercase tracking-widest mb-2">Atendimento</p>
            <h2 class="font-display font-bold text-3xl text-ink leading-tight">WhatsApp — Conexão</h2>
            <p class="text-slate text-sm mt-1">Conecte seu número do WhatsApp pra começar a receber e responder mensagens de clientes direto no painel.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3 rounded-xl text-sm">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-3 rounded-xl text-sm">{{ session('error') }}</div>
        @endif

        @if(!$evoConfigured)
            <div class="bg-amber-50 border border-amber-200 text-amber-900 px-5 py-4 rounded-xl text-sm">
                <strong>Configuração pendente.</strong> O administrador ainda não configurou a Evolution API. Entre em contato com o suporte.
            </div>
        @endif

        <div class="bg-white border border-black/5 rounded-3xl p-6 sm:p-8 shadow-sm">
            @if(!$instance)
                <div class="text-center py-8">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-emerald-100 flex items-center justify-center">
                        <svg class="w-8 h-8 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                    </div>
                    <h3 class="font-display font-bold text-xl text-ink mb-2">WhatsApp não conectado</h3>
                    <p class="text-slate text-sm mb-6 max-w-md mx-auto">Clique no botão abaixo pra gerar um QR code. Você vai escaneá-lo pelo WhatsApp do celular (Dispositivos conectados).</p>
                    <form method="POST" action="{{ route('customer.whatsapp.connect') }}" class="inline-block">
                        @csrf
                        <button type="submit" @if(!$evoConfigured) disabled @endif
                                class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            Conectar WhatsApp
                        </button>
                    </form>
                </div>
            @else
                <div class="flex flex-col md:flex-row gap-8 items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-4">
                            <span id="status-badge" class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold
                                @if($instance->status === 'open') bg-emerald-100 text-emerald-800
                                @elseif($instance->status === 'connecting') bg-amber-100 text-amber-800
                                @else bg-slate-100 text-slate-700 @endif">
                                <span class="w-2 h-2 rounded-full
                                    @if($instance->status === 'open') bg-emerald-500
                                    @elseif($instance->status === 'connecting') bg-amber-500 animate-pulse
                                    @else bg-slate-400 @endif"></span>
                                <span id="status-text">
                                    @if($instance->status === 'open') Conectado
                                    @elseif($instance->status === 'connecting') Aguardando leitura do QR
                                    @else Desconectado @endif
                                </span>
                            </span>
                        </div>

                        <dl class="text-sm space-y-2 mb-6">
                            <div class="flex gap-2">
                                <dt class="text-slate w-32">Instância:</dt>
                                <dd class="text-ink font-mono text-xs">{{ $instance->instance_name }}</dd>
                            </div>
                            @if($instance->phone_number)
                                <div class="flex gap-2">
                                    <dt class="text-slate w-32">Número:</dt>
                                    <dd class="text-ink font-bold">{{ $instance->phone_number }}</dd>
                                </div>
                            @endif
                            @if($instance->connected_at)
                                <div class="flex gap-2">
                                    <dt class="text-slate w-32">Conectado em:</dt>
                                    <dd class="text-ink">{{ $instance->connected_at->format('d/m/Y H:i') }}</dd>
                                </div>
                            @endif
                        </dl>

                        <form method="POST" action="{{ route('customer.whatsapp.disconnect') }}"
                              onsubmit="return confirm('Desconectar o WhatsApp? Você precisará escanear o QR de novo pra reconectar.');">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-sm font-bold transition-colors">
                                Desconectar
                            </button>
                        </form>
                    </div>

                    @if($instance->status !== 'open')
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
            @endif
        </div>
    </div>

    @if($instance && $instance->status !== 'open')
        <script>
            (function() {
                const qrImg = document.getElementById('qr-image');
                const qrLoad = document.getElementById('qr-loading');
                const statusBadge = document.getElementById('status-badge');
                const statusText = document.getElementById('status-text');

                async function pollQr() {
                    try {
                        const res = await fetch('{{ route('customer.whatsapp.qr') }}');
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
                        const res = await fetch('{{ route('customer.whatsapp.status') }}');
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
</x-app-layout>
