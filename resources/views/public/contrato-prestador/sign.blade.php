<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>Contrato — {{ $contrato->titulo }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <style>body{font-family:'Plus Jakarta Sans',sans-serif;-webkit-font-smoothing:antialiased;}</style>
</head>
<body class="bg-[#F4F5F7] min-h-screen text-[#0A1128]" x-data="{ pad: null, isEmpty: true,
    initPad() {
        this.$nextTick(() => {
            const canvas = document.getElementById('sig-canvas');
            if (!canvas) return;
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext('2d').scale(ratio, ratio);
            this.pad = new SignaturePad(canvas, { backgroundColor: 'rgb(255,255,255)' });
            this.pad.addEventListener('endStroke', () => this.isEmpty = this.pad.isEmpty());
        });
    },
    clearPad() { if (this.pad) { this.pad.clear(); this.isEmpty = true; } },
    beforeSubmit() {
        if (this.pad && !this.pad.isEmpty()) {
            document.getElementById('signature-image').value = this.pad.toDataURL();
        }
    }
}" x-init="initPad()">

<header class="bg-[#0A1128] text-white">
    <div class="max-w-4xl mx-auto px-6 py-5 flex items-center justify-between">
        <div>
            <p class="text-[10px] font-bold tracking-widest text-[#FF7A1A] uppercase">NC5 Hub Digital</p>
            <h1 class="text-lg font-bold">Contrato de Prestação de Serviço</h1>
        </div>
        <a href="{{ route('public.contrato-prestador.pdf', $contrato->signature_token) }}"
           class="text-xs font-bold bg-white/10 hover:bg-white/20 text-white px-3 py-2 rounded-lg transition-colors">Baixar PDF</a>
    </div>
</header>

<main class="max-w-4xl mx-auto px-6 py-8">

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-medium">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 font-medium">
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

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sm:p-10 mb-6">
        <div class="mb-6 pb-6 border-b border-gray-100">
            <p class="text-[10px] font-bold text-[#8A8F9C] uppercase tracking-widest mb-1">Contrato nº {{ str_pad($contrato->id, 5, '0', STR_PAD_LEFT) }}</p>
            <h2 class="text-2xl font-extrabold text-[#0A1128] leading-tight">{{ $contrato->titulo }}</h2>
            <p class="text-sm text-[#8A8F9C] mt-2">
                Entre <strong>NC5 Hub Digital</strong> (CONTRATANTE) e <strong>{{ $contrato->prestador->nome }}</strong> (CONTRATADO(A)).
            </p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6 pb-6 border-b border-gray-100">
            <div>
                <p class="text-[10px] font-bold text-[#8A8F9C] uppercase tracking-wider">Valor</p>
                <p class="text-base font-bold text-[#0A1128]">R$ {{ number_format($contrato->valor, 2, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-[#8A8F9C] uppercase tracking-wider">Pagamento</p>
                <p class="text-base font-bold text-[#0A1128]">{{ $contrato->forma_pagamento_label }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-[#8A8F9C] uppercase tracking-wider">Início</p>
                <p class="text-base font-bold text-[#0A1128]">{{ $contrato->data_inicio?->format('d/m/Y') }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-[#8A8F9C] uppercase tracking-wider">Término</p>
                <p class="text-base font-bold text-[#0A1128]">{{ $contrato->data_fim?->format('d/m/Y') ?? 'Indeterminado' }}</p>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="text-xs font-bold text-[#0A1128] uppercase tracking-widest mb-2">Objeto</h3>
            <p class="text-sm text-[#0A1128] whitespace-pre-line leading-relaxed">{{ $contrato->objeto }}</p>
        </div>

        @php
            $num = 4;
            $clausulas = [];
            if ($contrato->clausula_nao_vinculo_empregaticio) {
                $clausulas[] = ['Natureza civil / ausência de vínculo empregatício', 'O(a) CONTRATADO(A) presta os serviços de forma autônoma, sem pessoalidade obrigatória, subordinação ou habitualidade típicas da relação empregatícia. Não há geração de vínculo trabalhista entre as partes, respondendo o(a) CONTRATADO(A) por seus próprios encargos tributários e previdenciários.'];
            }
            if ($contrato->clausula_confidencialidade) {
                $clausulas[] = ['Confidencialidade (NDA)', 'O(a) CONTRATADO(A) compromete-se a manter sigilo absoluto sobre todas as informações, dados, estratégias e identidade de clientes da CONTRATANTE aos quais tiver acesso em razão deste contrato, inclusive após seu término, pelo prazo de 5 anos. O descumprimento sujeita o(a) CONTRATADO(A) à multa correspondente ao valor total do contrato.'];
            }
            if ($contrato->clausula_propriedade_intelectual) {
                $clausulas[] = ['Propriedade intelectual', 'Todos os direitos patrimoniais sobre materiais, obras, códigos e entregáveis produzidos pelo(a) CONTRATADO(A) em decorrência deste contrato serão transferidos integralmente à CONTRATANTE mediante o pagamento previsto, nos termos da Lei nº 9.610/98.'];
            }
            if ($contrato->clausula_exclusividade) {
                $clausulas[] = ['Exclusividade', 'Durante a vigência, o(a) CONTRATADO(A) compromete-se a não prestar serviços similares a empresas concorrentes diretas da CONTRATANTE no mesmo nicho e território de atuação.'];
            }
        @endphp

        <div class="mb-6 pb-6 border-b border-gray-100">
            <h3 class="text-xs font-bold text-[#0A1128] uppercase tracking-widest mb-3">Cláusulas</h3>
            <ol class="space-y-4 text-sm text-[#0A1128] leading-relaxed">
                <li><strong>Cláusula 1 — Objeto.</strong> Conforme descrito acima.</li>
                <li><strong>Cláusula 2 — Prazo e Vigência.</strong>
                    O contrato terá início em {{ $contrato->data_inicio?->format('d/m/Y') }}
                    @if($contrato->data_fim) e término em {{ $contrato->data_fim->format('d/m/Y') }}.
                    @else e vigência por prazo indeterminado, podendo ser rescindido mediante aviso prévio de 30 dias.
                    @endif
                    @if($contrato->prazo_entrega) Prazo específico de entrega: {{ $contrato->prazo_entrega }}. @endif
                </li>
                <li><strong>Cláusula 3 — Remuneração.</strong>
                    R$ {{ number_format($contrato->valor, 2, ',', '.') }} ({{ $contrato->forma_pagamento_label }}){{ $contrato->periodicidade ? ', ' . $contrato->periodicidade : '' }}.
                </li>
                @foreach($clausulas as [$titulo, $texto])
                    <li><strong>Cláusula {{ $num++ }} — {{ $titulo }}.</strong> {{ $texto }}</li>
                @endforeach
                @if($contrato->clausulas_extras)
                    <li><strong>Cláusula {{ $num++ }} — Disposições adicionais.</strong>
                        <div class="mt-2 whitespace-pre-line text-[#0A1128]">{{ $contrato->clausulas_extras }}</div>
                    </li>
                @endif
                <li><strong>Cláusula {{ $num++ }} — Rescisão.</strong> O contrato poderá ser rescindido por mútuo acordo, por iniciativa de qualquer parte com 30 dias de aviso, ou por descumprimento de cláusula.</li>
                <li><strong>Cláusula {{ $num++ }} — Foro.</strong> Fica eleito o foro da comarca da sede da CONTRATANTE para dirimir dúvidas oriundas deste contrato.</li>
            </ol>
        </div>

        @if($contrato->status_assinatura === 'assinado')
            @php $sig = json_decode($contrato->assinatura_url ?? '{}', true) ?: []; @endphp
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-lg">✓</div>
                    <div>
                        <p class="font-bold text-emerald-900">Contrato assinado eletronicamente</p>
                        <p class="text-xs text-emerald-700">{{ $contrato->assinado_em?->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <div><dt class="text-xs text-emerald-700 font-bold uppercase">Nome</dt><dd class="text-emerald-900">{{ $sig['nome'] ?? '—' }}</dd></div>
                    <div><dt class="text-xs text-emerald-700 font-bold uppercase">Documento</dt><dd class="text-emerald-900">{{ $sig['documento'] ?? '—' }}</dd></div>
                    <div><dt class="text-xs text-emerald-700 font-bold uppercase">IP</dt><dd class="text-emerald-900">{{ $sig['ip'] ?? '—' }}</dd></div>
                    <div><dt class="text-xs text-emerald-700 font-bold uppercase">Data/hora</dt><dd class="text-emerald-900">{{ $sig['timestamp'] ?? '—' }}</dd></div>
                </dl>
                @if(!empty($sig['signature_image']))
                    <div class="mt-4">
                        <p class="text-xs text-emerald-700 font-bold uppercase mb-1">Assinatura manuscrita</p>
                        <img src="{{ $sig['signature_image'] }}" alt="Assinatura" class="bg-white border border-emerald-200 rounded-lg max-w-xs">
                    </div>
                @endif
                <a href="{{ route('public.contrato-prestador.pdf', $contrato->signature_token) }}"
                   class="inline-block mt-5 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-colors">
                    Baixar PDF assinado
                </a>
            </div>
        @else
            <form method="POST" action="{{ route('public.contrato-prestador.sign', $contrato->signature_token) }}" @submit="beforeSubmit()" class="space-y-6">
                @csrf
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                    <p class="text-sm font-bold text-amber-900 mb-1">⚠ Confirme seus dados antes de assinar</p>
                    <p class="text-xs text-amber-800">Seu IP ({{ request()->ip() }}) e horário serão registrados como prova do aceite eletrônico, com validade jurídica (MP 2.200-2/2001).</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-[#0A1128] mb-2">Nome completo *</label>
                        <input type="text" name="nome_assinante" value="{{ old('nome_assinante', $contrato->prestador->nome) }}" required
                               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#0A1128] mb-2">CPF ou CNPJ *</label>
                        <input type="text" name="documento" value="{{ old('documento', $contrato->prestador->cpf_cnpj) }}" required
                               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-bold text-[#0A1128]">Assinatura (opcional, mas recomendada)</label>
                        <button type="button" @click="clearPad()" class="text-xs text-[#8A8F9C] hover:text-rose-500 font-medium">Limpar</button>
                    </div>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl bg-white overflow-hidden" style="height:180px;">
                        <canvas id="sig-canvas" style="width:100%;height:180px;touch-action:none;display:block;"></canvas>
                    </div>
                    <p class="text-xs text-[#8A8F9C] mt-1">Use o dedo (celular) ou o mouse para assinar acima.</p>
                    <input type="hidden" name="signature_image" id="signature-image">
                </div>

                <label class="flex items-start gap-3 p-4 rounded-xl bg-[#F4F5F7] border border-gray-200 cursor-pointer">
                    <input type="checkbox" name="aceite" value="1" required class="mt-1 w-5 h-5 rounded border-gray-300 text-[#FF7A1A] focus:ring-[#FF7A1A]">
                    <span class="text-sm text-[#0A1128]">
                        Declaro que li e concordo integralmente com todos os termos e cláusulas deste contrato.
                        Reconheço esta assinatura eletrônica como válida, nos termos do art. 10, §2º da MP 2.200-2/2001.
                    </span>
                </label>

                <button type="submit" class="w-full bg-[#0A1128] hover:bg-[#FF7A1A] text-white font-bold py-4 rounded-xl text-base transition-colors shadow-lg">
                    Assinar contrato eletronicamente
                </button>
            </form>
        @endif
    </div>

    <p class="text-center text-xs text-[#8A8F9C]">
        NC5 Hub Digital · nc5hubdigital.com.br · Dúvidas? Fale com nossa equipe.
    </p>
</main>

</body>
</html>
