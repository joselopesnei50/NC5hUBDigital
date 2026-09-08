<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard de Vendas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Cards de Métricas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Faturamento -->
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="mb-2 text-sm font-medium text-gray-600">Faturamento Mensal</p>
                            <p class="text-2xl font-bold text-gray-800">R$ {{ number_format($faturamentoMes, 2, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Pedidos Aprovados -->
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="mb-2 text-sm font-medium text-gray-600">Pedidos Aprovados</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $pedidosAprovados }} <span class="text-xs font-normal text-gray-500">neste mês</span></p>
                        </div>
                    </div>
                </div>

                <!-- Novos Clientes -->
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-500 mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <p class="mb-2 text-sm font-medium text-gray-600">Novos Clientes</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $novosClientes }} <span class="text-xs font-normal text-gray-500">neste mês</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Gráfico -->
                <div class="bg-white rounded-lg shadow-sm p-6 lg:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Crescimento de Faturamento (6 meses)</h3>
                    <div class="relative h-72 w-full">
                        <canvas id="vendasChart"></canvas>
                    </div>
                </div>

                <!-- Últimas Vendas -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Últimas Vendas</h3>
                    <div class="space-y-4">
                        @forelse($ultimasVendas as $venda)
                            <div class="flex items-center justify-between border-b pb-3 last:border-0 last:pb-0">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">{{ $venda->clienteFinal->nome_empresa }}</p>
                                    <p class="text-xs text-gray-500">{{ $venda->titulo }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-green-600">R$ {{ number_format($venda->valor, 2, ',', '.') }}</p>
                                    <p class="text-xs text-gray-500">{{ $venda->created_at->format('d/m') }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-4">Nenhuma venda aprovada recente.</p>
                        @endforelse
                    </div>
                    
                    <div class="mt-6 text-center">
                        <a href="{{ route('customer.pedidos.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-semibold">Ver todos os pedidos &rarr;</a>
                    </div>
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
                    backgroundColor: 'rgba(59, 130, 246, 0.2)', // blue-500
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: 'rgba(59, 130, 246, 1)',
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
                        ticks: {
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR');
                            }
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>
