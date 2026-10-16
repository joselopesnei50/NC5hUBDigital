<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-end justify-between gap-4 flex-wrap">
            <div>
                <p class="text-xs font-bold text-bruce uppercase tracking-widest mb-2">Atendimento</p>
                <h2 class="font-display font-bold text-3xl text-ink leading-tight">Dashboard do Bot IA</h2>
                <p class="text-slate text-sm mt-1">Como seu bot tá performando — resposta, custo, clientes atendidos e pedidos gerados.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.hub.whatsapp.dashboard', ['range' => '7d']) }}"
                   class="px-4 py-2 rounded-xl text-sm font-bold transition-colors {{ $range === 7 ? 'bg-bruce text-white' : 'bg-white border border-black/10 text-slate hover:bg-slate-50' }}">
                    7 dias
                </a>
                <a href="{{ route('admin.hub.whatsapp.dashboard', ['range' => '30d']) }}"
                   class="px-4 py-2 rounded-xl text-sm font-bold transition-colors {{ $range === 30 ? 'bg-bruce text-white' : 'bg-white border border-black/10 text-slate hover:bg-slate-50' }}">
                    30 dias
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- KPIs --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-black/5 rounded-2xl p-5 shadow-sm">
                <p class="text-xs text-slate uppercase tracking-wider font-bold">Mensagens recebidas</p>
                <p class="font-display font-bold text-3xl text-ink mt-2">{{ number_format($inboundCount, 0, ',', '.') }}</p>
                <p class="text-xs text-slate mt-1">últimos {{ $range }} dias</p>
            </div>
            <div class="bg-white border border-black/5 rounded-2xl p-5 shadow-sm">
                <p class="text-xs text-slate uppercase tracking-wider font-bold">Respondidas pelo bot</p>
                <p class="font-display font-bold text-3xl text-emerald-700 mt-2">{{ number_format($successfulReplies, 0, ',', '.') }}</p>
                <p class="text-xs text-slate mt-1">
                    @if($avgResponseMs > 0)
                        tempo médio: {{ number_format($avgResponseMs / 1000, 1, ',', '.') }}s
                    @else
                        tempo médio: —
                    @endif
                </p>
            </div>
            <div class="bg-white border border-black/5 rounded-2xl p-5 shadow-sm">
                <p class="text-xs text-slate uppercase tracking-wider font-bold">Taxa de resposta</p>
                <p class="font-display font-bold text-3xl text-ink mt-2">{{ number_format($responseRate, 1, ',', '.') }}%</p>
                <p class="text-xs text-slate mt-1">respondidas / recebidas</p>
            </div>
            <div class="bg-white border border-black/5 rounded-2xl p-5 shadow-sm">
                <p class="text-xs text-slate uppercase tracking-wider font-bold">Escalações pra humano</p>
                <p class="font-display font-bold text-3xl text-amber-700 mt-2">{{ number_format($escalations, 0, ',', '.') }}</p>
                <p class="text-xs text-slate mt-1">bot pausado automaticamente</p>
            </div>
        </div>

        {{-- Gráfico --}}
        <div class="bg-white border border-black/5 rounded-3xl p-6 sm:p-8 shadow-sm">
            <h3 class="font-display font-bold text-lg text-ink mb-1">Mensagens por dia</h3>
            <p class="text-slate text-sm mb-4">Volume de entrada versus respostas do bot ao longo dos últimos {{ $range }} dias.</p>
            <div style="height: 320px;">
                <canvas id="chart-messages"></canvas>
            </div>
        </div>

        {{-- Clientes + pedidos --}}
        <div class="bg-white border border-black/5 rounded-3xl p-6 sm:p-8 shadow-sm">
            <h3 class="font-display font-bold text-lg text-ink mb-1">Clientes atendidos</h3>
            <p class="text-slate text-sm mb-5">Quem o bot conversou e quantos pedidos foram gerados no período.</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <p class="text-xs text-slate uppercase tracking-wider font-bold">Conversas únicas</p>
                    <p class="font-display font-bold text-3xl text-ink mt-1">{{ number_format($uniqueCustomers, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate uppercase tracking-wider font-bold">Retornantes</p>
                    <p class="font-display font-bold text-3xl text-slate mt-1">{{ number_format($returningCustomers, 0, ',', '.') }}</p>
                    <p class="text-xs text-slate mt-1">já falaram antes do período</p>
                </div>
                <div>
                    <p class="text-xs text-emerald-700 uppercase tracking-wider font-bold">Pedidos gerados</p>
                    <p class="font-display font-bold text-3xl text-emerald-700 mt-1">{{ number_format($pedidosGerados, 0, ',', '.') }}</p>
                    <p class="text-xs text-slate mt-1">clientes que conversaram no período</p>
                </div>
            </div>
            @if($failures > 0)
                <div class="mt-6 pt-4 border-t border-black/5">
                    <p class="text-xs text-rose-700 font-bold">⚠ {{ $failures }} falha(s) de resposta no período</p>
                    <p class="text-xs text-slate mt-1">O bot não conseguiu gerar/enviar resposta. Se persistir, verifique a conexão.</p>
                </div>
            @endif
        </div>

        {{-- Agenda --}}
        <div class="bg-white border border-black/5 rounded-3xl p-6 sm:p-8 shadow-sm">
            <h3 class="font-display font-bold text-lg text-ink mb-1">Agenda do período</h3>
            <p class="text-slate text-sm mb-5">Agendamentos criados, cancelamentos e ocorrências de conflito nos últimos {{ $range }} dias.</p>
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-6">
                <div>
                    <p class="text-xs text-slate uppercase tracking-wider font-bold">Agendamentos criados</p>
                    <p class="font-display font-bold text-3xl text-ink mt-1">{{ number_format($agenda['created_total'], 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-xs text-emerald-700 uppercase tracking-wider font-bold">Pelo bot</p>
                    <p class="font-display font-bold text-3xl text-emerald-700 mt-1">{{ number_format($agenda['created_by_bot'], 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate uppercase tracking-wider font-bold">Cancelamentos</p>
                    <p class="font-display font-bold text-3xl text-slate mt-1">{{ number_format($agenda['cancelled'], 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate uppercase tracking-wider font-bold">Conflitos de horário</p>
                    <p class="font-display font-bold text-3xl text-amber-700 mt-1">{{ number_format($agenda['conflito_count'], 0, ',', '.') }}</p>
                    <p class="text-xs text-slate mt-1">bot teve que oferecer outro horário</p>
                </div>
                <div>
                    <p class="text-xs text-slate uppercase tracking-wider font-bold">Fora do expediente</p>
                    <p class="font-display font-bold text-3xl text-amber-700 mt-1">{{ number_format($agenda['fora_expediente_count'], 0, ',', '.') }}</p>
                    <p class="text-xs text-slate mt-1">cliente pediu em horário fechado</p>
                </div>
            </div>
        </div>

        {{-- Insights da agenda --}}
        <div class="bg-white border border-black/5 rounded-3xl p-6 sm:p-8 shadow-sm">
            <h3 class="font-display font-bold text-lg text-ink mb-1">Insights da agenda</h3>
            <p class="text-slate text-sm mb-5">Métricas acionáveis pra entender como o bot está convertendo e quando os clientes mais marcam.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <p class="text-xs text-slate uppercase tracking-wider font-bold">Taxa de conversão</p>
                    <p class="font-display font-bold text-3xl text-emerald-700 mt-1">{{ number_format($agenda['conversion_rate'], 1, ',', '.') }}%</p>
                    <p class="text-xs text-slate mt-1">{{ $agenda['chats_com_appointment'] }} de {{ $agenda['chats_unicos'] }} conversas viraram agendamento</p>
                </div>
                <div>
                    <p class="text-xs text-slate uppercase tracking-wider font-bold">Taxa de no-show</p>
                    <p class="font-display font-bold text-3xl {{ $agenda['no_show_rate'] > 20 ? 'text-rose-700' : 'text-ink' }} mt-1">{{ number_format($agenda['no_show_rate'], 1, ',', '.') }}%</p>
                    <p class="text-xs text-slate mt-1">{{ $agenda['past_no_show'] }} de {{ $agenda['past_total'] }} passados ficaram sem conclusão</p>
                </div>
                <div>
                    <p class="text-xs text-slate uppercase tracking-wider font-bold">Horário mais marcado</p>
                    <p class="font-display font-bold text-3xl text-ink mt-1">
                        @if($agenda['best_hour'] !== null)
                            {{ str_pad((string) $agenda['best_hour'], 2, '0', STR_PAD_LEFT) }}h
                        @else
                            —
                        @endif
                    </p>
                    <p class="text-xs text-slate mt-1">quando os clientes mais agendam</p>
                </div>
                <div>
                    <p class="text-xs text-slate uppercase tracking-wider font-bold">Dia mais procurado</p>
                    <p class="font-display font-bold text-3xl text-ink mt-1 capitalize">{{ $agenda['best_dow'] ?: '—' }}</p>
                    <p class="text-xs text-slate mt-1">dia da semana campeão</p>
                </div>
            </div>
            @if($agenda['no_show_rate'] > 20)
                <div class="mt-5 pt-4 border-t border-black/5">
                    <p class="text-xs text-rose-800">
                        <strong>⚠ Taxa de no-show alta.</strong> Considere ativar o segundo lembrete (24h antes) em <a href="{{ route('admin.hub.whatsapp.bot.index') }}" class="underline font-bold">Bot IA</a> pra reduzir faltas.
                    </p>
                </div>
            @endif
        </div>

        @if($totalInteractions === 0)
            <div class="bg-amber-50 border border-amber-200 text-amber-900 px-5 py-4 rounded-2xl text-sm">
                <strong>Sem interações no período.</strong> Verifique se o bot está <a href="{{ route('admin.hub.whatsapp.bot.index') }}" class="underline font-bold">ativado</a> e se o WhatsApp está <a href="{{ route('admin.hub.whatsapp.conexao') }}" class="underline font-bold">conectado</a>.
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function () {
            const ctx = document.getElementById('chart-messages');
            if (!ctx) return;
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($dailyLabels),
                    datasets: [
                        {
                            label: 'Recebidas',
                            data: @json($dailyInbound),
                            borderColor: '#64748b',
                            backgroundColor: 'rgba(100, 116, 139, 0.1)',
                            tension: 0.3,
                            fill: true,
                        },
                        {
                            label: 'Respondidas pelo bot',
                            data: @json($dailyReplies),
                            borderColor: '#059669',
                            backgroundColor: 'rgba(5, 150, 105, 0.1)',
                            tension: 0.3,
                            fill: true,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } }
                    }
                }
            });
        })();
    </script>
</x-admin-layout>
