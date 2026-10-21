<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-[11px] font-extrabold text-[#FF7A1A] uppercase tracking-widest mb-1">Inteligência Comercial · BruceOS</p>
                <h2 class="font-display font-extrabold text-3xl text-[#0A1128] leading-tight">
                    Dashboard de Vendas
                </h2>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('customer.pedidos.kanban') }}" class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-[#0A1128] border border-gray-200 px-4 py-2.5 rounded-xl text-sm font-bold transition-all shadow-sm">
                    Pipeline Kanban
                </a>
                <a href="{{ route('customer.pedidos.create') }}" class="inline-flex items-center gap-2 bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-all shadow-sm">
                    + Novo Pedido
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Cards de Métricas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <!-- Faturamento -->
            <div class="bg-[#0A1128] rounded-3xl p-6 text-white shadow-lg relative overflow-hidden">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[11px] font-extrabold uppercase tracking-widest text-gray-400">Faturamento Mensal</span>
                    <div class="w-10 h-10 rounded-xl bg-[#FF7A1A]/15 text-[#FF7A1A] flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <p class="font-display font-extrabold text-3xl text-white">R$ {{ number_format($faturamentoMes, 2, ',', '.') }}</p>
                <p class="text-xs text-gray-400 mt-1.5">Pedidos concluídos/aprovados no mês atual</p>
            </div>

            <!-- Pedidos Aprovados -->
            <div class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[11px] font-extrabold uppercase tracking-widest text-gray-400">Pedidos Aprovados</span>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <p class="font-display font-extrabold text-3xl text-[#0A1128]">{{ $pedidosAprovados }}</p>
                <p class="text-xs text-gray-500 mt-1.5">Fechamentos realizados neste mês</p>
            </div>

            <!-- Novos Clientes -->
            <div class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[11px] font-extrabold uppercase tracking-widest text-gray-400">Novos Clientes</span>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
                <p class="font-display font-extrabold text-3xl text-[#0A1128]">{{ $novosClientes }}</p>
                <p class="text-xs text-gray-500 mt-1.5">Adicionados à Base de Clientes neste mês</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Gráfico -->
            <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm p-6 sm:p-8 lg:col-span-2">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#FF7A1A]">Evolução de Receita</span>
                        <h3 class="font-display font-extrabold text-lg text-[#0A1128]">Crescimento de Faturamento (6 meses)</h3>
                    </div>
                </div>
                <div class="relative h-72 w-full">
                    <canvas id="vendasChart"></canvas>
                </div>
            </div>

            <!-- Últimas Vendas -->
            <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm p-6 sm:p-8 flex flex-col justify-between">
                <div>
                    <div class="mb-6">
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#FF7A1A]">Histórico Recente</span>
                        <h3 class="font-display font-extrabold text-lg text-[#0A1128]">Últimas Vendas</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($ultimasVendas as $venda)
                            <div class="py-3.5 first:pt-0 last:pb-0 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-[#0A1128] truncate">{{ $venda->clienteFinal?->nome_empresa ?? 'Cliente não informado' }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $venda->titulo }}</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-sm font-extrabold text-emerald-600">R$ {{ number_format($venda->valor, 2, ',', '.') }}</p>
                                    <p class="text-[11px] text-gray-400">{{ $venda->created_at?->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center">
                                <p class="text-sm text-gray-500">Nenhuma venda aprovada recente.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100 text-center">
                    <a href="{{ route('customer.pedidos.index') }}" class="inline-flex items-center gap-1.5 text-xs font-extrabold text-[#0A1128] hover:text-[#FF7A1A] uppercase tracking-wider transition-colors">
                        Ver todos os pedidos
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('vendasChart').getContext('2d');
        const vendasChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($graficoLabels) !!},
                datasets: [{
                    label: 'Faturamento Aprovado (R$)',
                    data: {!! json_encode($graficoValores) !!},
                    backgroundColor: 'rgba(255, 122, 26, 0.12)',
                    borderColor: '#FF7A1A',
                    borderWidth: 2.5,
                    tension: 0.35,
                    fill: true,
                    pointBackgroundColor: '#0A1128',
                    pointBorderColor: '#FF7A1A',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let value = context.raw || 0;
                                return 'R$ ' + value.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.04)'
                        },
                        ticks: {
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR');
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>
