<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">{{ $termo->titulo }}</h2>
                <p class="text-sm text-[#8A8F9C] mt-1">
                    Cedente: <strong>{{ $termo->cedente_nome }}</strong>
                    @if($termo->cedente_menor)
                        <span class="ml-1 px-2 py-0.5 text-[10px] font-bold rounded-full bg-indigo-100 text-indigo-700 uppercase">Menor — Resp.: {{ $termo->responsavel_nome }}</span>
                    @endif
                    · Cliente: <strong>{{ $termo->cliente->razao_social ?? 'NC5 Hub Digital' }}</strong>
                </p>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                @if(!$termo->isAssinado() && $termo->status !== 'cancelado')
                    <a href="{{ $termo->publicSignUrl() }}" target="_blank" rel="noopener"
                       class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg">
                        Abrir para assinar agora
                    </a>

                    @if($termo->whatsapp_url)
                        <a href="{{ $termo->whatsapp_url }}" target="_blank" rel="noopener"
                           class="bg-[#25D366] hover:bg-[#1ebe57] text-white px-4 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg">
                            Enviar por WhatsApp
                        </a>
                    @endif

                    @if($termo->cedente_email)
                        <form method="POST" action="{{ route('admin.termos-imagem.enviar-email', $termo) }}"
                              onsubmit="return confirm('Enviar termo para o e-mail ({{ $termo->cedente_email }})?')">
                            @csrf
                            <button type="submit"
                                    class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-4 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg">
                                {{ $termo->enviado_em ? 'Reenviar por e-mail' : 'Enviar por e-mail' }}
                            </button>
                        </form>
                    @endif
                @endif

                <a href="{{ route('admin.termos-imagem.pdf', $termo) }}"
                   class="bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg">
                    Baixar PDF
                </a>

                @if(!$termo->isAssinado())
                    <a href="{{ route('admin.termos-imagem.edit', $termo) }}"
                       class="bg-white border border-gray-300 hover:border-[#0A1128] text-[#0A1128] px-5 py-2.5 rounded-xl text-sm font-bold transition-colors">
                        Editar
                    </a>
                @endif

                @if($termo->status !== 'cancelado')
                    <form method="POST" action="{{ route('admin.termos-imagem.cancelar', $termo) }}"
                          onsubmit="return confirm('Deseja cancelar este termo de autorização?')">
                        @csrf
                        <button type="submit" class="bg-white border border-rose-200 hover:border-rose-500 text-rose-600 px-4 py-2.5 rounded-xl text-sm font-bold transition-colors">
                            Cancelar termo
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl shadow-sm p-8 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pb-6 border-b border-gray-100">
                <div>
                    <div class="text-xs text-[#8A8F9C] font-semibold uppercase">Projeto / Gravação</div>
                    <div class="text-sm font-bold text-[#0A1128]">{{ $termo->projeto_referencia ?: '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-[#8A8F9C] font-semibold uppercase">Data da Captação</div>
                    <div class="text-sm font-bold text-[#0A1128]">{{ $termo->data_captacao?->format('d/m/Y') ?: '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-[#8A8F9C] font-semibold uppercase">Local da Captação</div>
                    <div class="text-sm font-bold text-[#0A1128]">{{ $termo->local_captacao ?: '—' }}</div>
                </div>
            </div>

            <div>
                <h3 class="text-xs font-bold text-[#8A8F9C] uppercase tracking-widest mb-3">Texto do Termo</h3>
                <div class="p-6 rounded-xl bg-[#F4F5F7] border border-gray-200 text-sm text-[#0A1128] whitespace-pre-line leading-relaxed">{{ $termo->conteudo }}</div>
            </div>

            @if($termo->isAssinado())
                @php $sig = $termo->assinatura_array; @endphp
                <div class="pt-6 border-t border-gray-100">
                    <h3 class="text-xs font-bold text-emerald-700 uppercase tracking-widest mb-3">Comprovante de Assinatura Eletrônica</h3>
                    <div class="p-6 rounded-2xl bg-emerald-50 border border-emerald-200 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div>
                                <div class="text-xs text-emerald-700 font-bold uppercase">Assinado por</div>
                                <div class="font-bold text-emerald-950">{{ $sig['nome'] ?? '—' }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-emerald-700 font-bold uppercase">CPF / Documento</div>
                                <div class="font-bold text-emerald-950">{{ $sig['documento'] ?? '—' }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-emerald-700 font-bold uppercase">Data / Hora</div>
                                <div class="text-emerald-900">{{ $termo->assinado_em?->format('d/m/Y H:i:s') ?? ($sig['timestamp'] ?? '—') }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-emerald-700 font-bold uppercase">Endereço IP</div>
                                <div class="text-emerald-900">{{ $sig['ip'] ?? '—' }}</div>
                            </div>
                        </div>

                        @if($termo->conteudo_hash)
                            <div>
                                <div class="text-xs text-emerald-700 font-bold uppercase">Hash SHA-256 do conteúdo</div>
                                <div class="text-xs font-mono text-emerald-900 break-all">{{ $termo->conteudo_hash }}</div>
                            </div>
                        @endif

                        @if(!empty($sig['signature_image']))
                            <div>
                                <div class="text-xs text-emerald-700 font-bold uppercase mb-1">Assinatura Manuscrita</div>
                                <img src="{{ $sig['signature_image'] }}" alt="Assinatura" class="bg-white border border-emerald-200 rounded-xl max-w-xs p-2">
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 space-y-4 h-fit">
            <h3 class="text-xs font-bold text-[#8A8F9C] uppercase tracking-widest">Status</h3>
            @php
                $colors = [
                    'rascunho'  => 'bg-gray-100 text-gray-700',
                    'enviado'   => 'bg-amber-100 text-amber-700',
                    'assinado'  => 'bg-emerald-100 text-emerald-700',
                    'cancelado' => 'bg-rose-100 text-rose-700',
                ];
            @endphp
            <span class="inline-block px-3 py-1.5 text-xs font-bold rounded-full uppercase {{ $colors[$termo->status] ?? 'bg-gray-100 text-gray-700' }}">
                {{ $termo->status }}
            </span>

            <div class="pt-4 border-t border-gray-100">
                <h3 class="text-xs font-bold text-[#8A8F9C] uppercase tracking-widest mb-2">Link público de assinatura</h3>
                <div class="flex items-center gap-2">
                    <input type="text" readonly value="{{ $termo->publicSignUrl() }}"
                           class="flex-1 text-xs rounded-lg border-gray-200 bg-gray-50 text-[#0A1128]" onclick="this.select()">
                    <button type="button"
                            onclick="navigator.clipboard.writeText(this.previousElementSibling.value); this.textContent='Copiado!'"
                            class="text-xs font-bold bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-3 py-2 rounded-lg transition-colors">
                        Copiar link de assinatura
                    </button>
                </div>
            </div>

            @if($termo->enviado_em)
                <div class="pt-4 border-t border-gray-100">
                    <h3 class="text-xs font-bold text-[#8A8F9C] uppercase tracking-widest mb-1">E-mail enviado em</h3>
                    <div class="text-sm font-bold text-[#0A1128]">{{ $termo->enviado_em->format('d/m/Y H:i') }}</div>
                </div>
            @endif

            <div class="pt-4 border-t border-gray-100 space-y-1">
                <h3 class="text-xs font-bold text-[#8A8F9C] uppercase tracking-widest mb-2">Dados do Cedente</h3>
                <div class="text-sm font-bold text-[#0A1128]">{{ $termo->cedente_nome }}</div>
                <div class="text-xs text-[#8A8F9C]">CPF: {{ $termo->cedente_cpf }}</div>
                @if($termo->cedente_rg)
                    <div class="text-xs text-[#8A8F9C]">RG: {{ $termo->cedente_rg }}</div>
                @endif
                @if($termo->cedente_telefone)
                    <div class="text-xs text-[#8A8F9C]">Tel: {{ $termo->cedente_telefone }}</div>
                @endif
                @if($termo->cedente_email)
                    <div class="text-xs text-[#8A8F9C]">E-mail: {{ $termo->cedente_email }}</div>
                @endif
                @if($termo->cedente_endereco)
                    <div class="text-xs text-[#8A8F9C]">Endereço: {{ $termo->cedente_endereco }}</div>
                @endif
            </div>

            @if($termo->cedente_menor)
                <div class="pt-4 border-t border-gray-100 space-y-1">
                    <h3 class="text-xs font-bold text-indigo-700 uppercase tracking-widest mb-2">Responsável Legal (Menor)</h3>
                    <div class="text-sm font-bold text-[#0A1128]">{{ $termo->responsavel_nome }}</div>
                    <div class="text-xs text-[#8A8F9C]">CPF: {{ $termo->responsavel_cpf }}</div>
                    <div class="text-xs text-[#8A8F9C]">Vínculo: {{ $termo->responsavel_parentesco }}</div>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
