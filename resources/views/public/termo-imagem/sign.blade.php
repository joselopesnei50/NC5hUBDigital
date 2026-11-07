<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>Termo de Uso de Imagem — {{ $termo->titulo }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; -webkit-font-smoothing: antialiased; }
        #sig-canvas { touch-action: none; -ms-touch-action: none; user-select: none; -webkit-user-select: none; }
    </style>
</head>
<body class="bg-[#F4F5F7] min-h-screen text-[#0A1128]" x-data="{
    pad: null,
    isEmpty: true,
    clientError: '',
    initPad() {
        this.$nextTick(() => {
            const canvas = document.getElementById('sig-canvas');
            if (!canvas) return;
            const resize = () => {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                const data = this.pad ? this.pad.toData() : null;
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext('2d').scale(ratio, ratio);
                if (this.pad) {
                    this.pad.clear();
                    if (data && data.length) {
                        this.pad.fromData(data);
                    }
                }
            };
            this.pad = new SignaturePad(canvas, {
                backgroundColor: 'rgb(255,255,255)',
                penColor: 'rgb(10,17,40)'
            });
            resize();
            window.addEventListener('resize', resize);
            this.pad.addEventListener('endStroke', () => {
                this.isEmpty = this.pad.isEmpty();
                this.clientError = '';
            });
        });
    },
    clearPad() {
        if (this.pad) {
            this.pad.clear();
            this.isEmpty = true;
        }
    },
    beforeSubmit(e) {
        if (this.pad && !this.pad.isEmpty()) {
            document.getElementById('signature-image').value = this.pad.toDataURL('image/png');
        }
        if (!document.getElementById('signature-image').value) {
            e.preventDefault();
            this.clientError = 'Por favor, desenhe sua assinatura com o dedo ou mouse no quadro acima antes de confirmar.';
        }
    }
}" x-init="initPad()">

<header class="bg-[#0A1128] text-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between gap-3">
        <div>
            <p class="text-[10px] font-bold tracking-widest text-[#FF7A1A] uppercase">NC5 Hub Digital</p>
            <h1 class="text-base sm:text-lg font-bold leading-tight">Termo de Autorização de Uso de Imagem e Voz</h1>
        </div>
        <a href="{{ route('public.termo-imagem.pdf', $termo->signature_token) }}"
           class="text-xs font-bold bg-white/10 hover:bg-white/20 text-white px-3 py-2 rounded-lg transition-colors whitespace-nowrap">
            Baixar PDF
        </a>
    </div>
</header>

<main class="max-w-3xl mx-auto px-4 sm:px-6 py-6 sm:py-8">

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-medium text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 font-medium text-sm">
            {{ session('error') }}
        </div>
    @endif
    @if(isset($errors) && $errors->any())
        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800">
            <p class="font-bold text-sm mb-1">Corrija os campos abaixo:</p>
            <ul class="list-disc pl-5 text-sm">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 sm:p-8 mb-6">
        <div class="mb-5 pb-5 border-b border-gray-100">
            <p class="text-[10px] font-bold text-[#8A8F9C] uppercase tracking-widest mb-1">
                Termo nº {{ str_pad($termo->id, 5, '0', STR_PAD_LEFT) }}
            </p>
            <h2 class="text-xl sm:text-2xl font-extrabold text-[#0A1128] leading-tight">{{ $termo->titulo }}</h2>
            <p class="text-xs sm:text-sm text-[#8A8F9C] mt-2">
                Cedente: <strong class="text-[#0A1128]">{{ $termo->cedente_nome }}</strong> ({{ $termo->cedente_cpf }})
                @if($termo->cliente)
                    · Produção para <strong class="text-[#0A1128]">{{ $termo->cliente->razao_social }}</strong>
                @endif
            </p>
            @if($termo->cedente_menor)
                <div class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-900 text-xs font-semibold">
                    <span>Menor de idade — Assinatura pelo responsável legal: <strong>{{ $termo->responsavel_nome }}</strong> ({{ $termo->responsavel_parentesco }})</span>
                </div>
            @endif
        </div>

        @if($termo->projeto_referencia || $termo->data_captacao || $termo->local_captacao)
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5 pb-5 border-b border-gray-100 text-xs">
                @if($termo->projeto_referencia)
                    <div>
                        <span class="font-bold text-[#8A8F9C] uppercase block">Projeto / Gravação</span>
                        <span class="font-bold text-[#0A1128] text-sm">{{ $termo->projeto_referencia }}</span>
                    </div>
                @endif
                @if($termo->data_captacao)
                    <div>
                        <span class="font-bold text-[#8A8F9C] uppercase block">Data da Captação</span>
                        <span class="font-bold text-[#0A1128] text-sm">{{ $termo->data_captacao->format('d/m/Y') }}</span>
                    </div>
                @endif
                @if($termo->local_captacao)
                    <div>
                        <span class="font-bold text-[#8A8F9C] uppercase block">Local</span>
                        <span class="font-bold text-[#0A1128] text-sm">{{ $termo->local_captacao }}</span>
                    </div>
                @endif
            </div>
        @endif

        <div class="mb-6 pb-6 border-b border-gray-100">
            <h3 class="text-xs font-bold text-[#0A1128] uppercase tracking-widest mb-3">Termos da Autorização</h3>
            <div class="p-4 sm:p-6 rounded-xl bg-[#F4F5F7] border border-gray-200 text-sm text-[#0A1128] whitespace-pre-line leading-relaxed">{{ $termo->conteudo }}</div>
        </div>

        @if($termo->status === 'assinado')
            @php $sig = $termo->assinatura_array; @endphp
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 sm:p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-lg shrink-0">✓</div>
                    <div>
                        <p class="font-bold text-emerald-900">Termo assinado eletronicamente</p>
                        <p class="text-xs text-emerald-700">{{ $termo->assinado_em?->format('d/m/Y H:i:s') }}</p>
                    </div>
                </div>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <div><dt class="text-xs text-emerald-700 font-bold uppercase">Assinado por</dt><dd class="text-emerald-900 font-semibold">{{ $sig['nome'] ?? '—' }}</dd></div>
                    <div><dt class="text-xs text-emerald-700 font-bold uppercase">CPF</dt><dd class="text-emerald-900 font-semibold">{{ $sig['documento'] ?? '—' }}</dd></div>
                    <div><dt class="text-xs text-emerald-700 font-bold uppercase">IP</dt><dd class="text-emerald-900">{{ $sig['ip'] ?? '—' }}</dd></div>
                    <div><dt class="text-xs text-emerald-700 font-bold uppercase">Data/hora</dt><dd class="text-emerald-900">{{ $sig['timestamp'] ?? '—' }}</dd></div>
                </dl>
                @if(!empty($sig['signature_image']))
                    <div class="mt-4">
                        <p class="text-xs text-emerald-700 font-bold uppercase mb-1">Assinatura manuscrita</p>
                        <img src="{{ $sig['signature_image'] }}" alt="Assinatura" class="bg-white border border-emerald-200 rounded-lg max-w-xs p-2">
                    </div>
                @endif
                <a href="{{ route('public.termo-imagem.pdf', $termo->signature_token) }}"
                   class="inline-block mt-5 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-3 rounded-xl text-sm font-bold transition-colors">
                    Baixar PDF assinado
                </a>
            </div>
        @elseif($termo->status === 'cancelado')
            <div class="bg-rose-50 border border-rose-200 rounded-2xl p-6 text-center">
                <p class="font-bold text-rose-900 text-base">Este termo de autorização foi cancelado</p>
                <p class="text-xs text-rose-700 mt-1">Não é mais possível registrar assinatura para este documento. Caso necessário, solicite um novo link à equipe da NC5 Hub Digital.</p>
            </div>
        @else
            <form method="POST" action="{{ route('public.termo-imagem.sign', $termo->signature_token) }}" @submit="beforeSubmit($event)" class="space-y-5">
                @csrf
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                    <p class="text-sm font-bold text-amber-900 mb-1">
                        @if($termo->cedente_menor)
                            ⚠ Atenção: assinatura do Responsável Legal
                        @else
                            ⚠ Confirme seus dados e assine com o dedo na tela
                        @endif
                    </p>
                    <p class="text-xs text-amber-800">
                        Seu IP ({{ request()->ip() }}), data/hora e assinatura manuscrita serão gravados junto ao hash de integridade do documento, com validade jurídica (MP 2.200-2/2001 e Lei 14.063/2020).
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-[#0A1128] mb-1.5">
                            {{ $termo->cedente_menor ? 'Nome completo do responsável legal *' : 'Nome completo de quem assina *' }}
                        </label>
                        <input type="text" name="nome_assinante"
                               value="{{ old('nome_assinante', $termo->nome_assinante_esperado) }}" required
                               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128] text-base">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#0A1128] mb-1.5">
                            {{ $termo->cedente_menor ? 'CPF do responsável legal *' : 'CPF de quem assina *' }}
                        </label>
                        <input type="text" name="documento"
                               value="{{ old('documento', $termo->cpf_assinante_esperado) }}" required
                               inputmode="numeric"
                               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128] text-base">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-bold text-[#0A1128]">Sua assinatura manuscrita (obrigatória) *</label>
                        <button type="button" @click="clearPad()" class="text-xs text-rose-600 hover:text-rose-800 font-bold px-2 py-1 rounded">
                            Limpar assinatura
                        </button>
                    </div>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl bg-white overflow-hidden" style="height:200px;">
                        <canvas id="sig-canvas" style="width:100%;height:200px;touch-action:none;display:block;"></canvas>
                    </div>
                    <p class="text-xs text-[#8A8F9C] mt-1.5">Assine com o dedo na tela do celular ou com o mouse no quadro acima.</p>
                    <p x-show="clientError" x-text="clientError" class="text-xs font-bold text-rose-600 mt-1" style="display:none;"></p>
                    <input type="hidden" name="signature_image" id="signature-image">
                </div>

                <label class="flex items-start gap-3 p-4 rounded-xl bg-[#F4F5F7] border border-gray-200 cursor-pointer">
                    <input type="checkbox" name="aceite" value="1" required class="mt-1 w-5 h-5 rounded border-gray-300 text-[#FF7A1A] focus:ring-[#FF7A1A] shrink-0">
                    <span class="text-sm text-[#0A1128] leading-snug">
                        Declaro que li e concordo integralmente com os termos desta Autorização de Uso de Imagem e Voz,
                        reconhecendo esta assinatura eletrônica como livre, válida e legítima.
                    </span>
                </label>

                <button type="submit" class="w-full bg-[#0A1128] hover:bg-[#FF7A1A] active:bg-[#E5651A] text-white font-bold py-4 rounded-xl text-base transition-colors shadow-lg">
                    Confirmar e Assinar Termo
                </button>
            </form>
        @endif
    </div>

    <p class="text-center text-xs text-[#8A8F9C]">
        NC5 Hub Digital · nc5hubdigital.com.br · Protegido pela LGPD (Lei nº 13.709/2018)
    </p>
</main>

</body>
</html>
