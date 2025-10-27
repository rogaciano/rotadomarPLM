<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Produtos por Estilista') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="bg-blue-600 text-white p-4 rounded-t-lg">
                    <h5 class="text-lg font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                        </svg>
                        Produtos por Estilista ({{ $titulo ?? 'Últimos 12 meses' }})
                    </h5>
                </div>
                <div class="bg-white p-6 rounded-b-lg shadow">
                    <div class="mb-6">
                        <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 rounded">
                            <div class="flex">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p>Este gráfico mostra a distribuição de produtos criados por estilista no período selecionado.
                                Os 10 estilistas com mais produtos são mostrados individualmente, enquanto os demais são agrupados como "Outros".</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <form id="periodo-form" action="{{ route('dashboard.produtos-por-estilista') }}" method="GET" class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h3 class="text-lg font-medium text-gray-700 mb-3">Selecionar Período</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="periodo" class="block text-sm font-medium text-gray-700 mb-1">Período Predefinido</label>
                                    <select id="periodo" name="periodo" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="ultimos_12_meses" {{ request('periodo', 'ultimos_12_meses') == 'ultimos_12_meses' ? 'selected' : '' }}>Últimos 12 meses</option>
                                        <option value="ultimos_6_meses" {{ request('periodo') == 'ultimos_6_meses' ? 'selected' : '' }}>Últimos 6 meses</option>
                                        <option value="ultimos_3_meses" {{ request('periodo') == 'ultimos_3_meses' ? 'selected' : '' }}>Últimos 3 meses</option>
                                        <option value="ano_atual" {{ request('periodo') == 'ano_atual' ? 'selected' : '' }}>Ano atual</option>
                                        <option value="ano_anterior" {{ request('periodo') == 'ano_anterior' ? 'selected' : '' }}>Ano anterior</option>
                                        <option value="personalizado" {{ request('periodo') == 'personalizado' ? 'selected' : '' }}>Período personalizado</option>
                                    </select>
                                </div>
                                
                                <div id="data-inicio-container" class="{{ request('periodo') == 'personalizado' ? '' : 'hidden' }}">
                                    <label for="data_inicio" class="block text-sm font-medium text-gray-700 mb-1">Data Início</label>
                                    <input type="date" id="data_inicio" name="data_inicio" value="{{ request('data_inicio', Carbon\Carbon::now()->subMonths(12)->format('Y-m-d')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                                
                                <div id="data-fim-container" class="{{ request('periodo') == 'personalizado' ? '' : 'hidden' }}">
                                    <label for="data_fim" class="block text-sm font-medium text-gray-700 mb-1">Data Fim</label>
                                    <input type="date" id="data_fim" name="data_fim" value="{{ request('data_fim', Carbon\Carbon::now()->format('Y-m-d')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                            </div>
                            
                            <div class="mt-4 flex justify-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    Atualizar Gráfico
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="chart-container" style="position: relative; height:400px; width:100%">
                                <canvas id="donutChart"></canvas>
                            </div>
                        </div>
                        <div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white">
                                    <thead class="bg-gray-800 text-white">
                                        <tr>
                                            <th class="py-3 px-4 text-left">Estilista</th>
                                            <th class="py-3 px-4 text-center">Quantidade</th>
                                            <th class="py-3 px-4 text-center">Porcentagem</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @php
                                            $total = array_sum($data);
                                        @endphp
                                        
                                        @foreach($dadosGrafico as $index => $item)
                                        <tr class="hover:bg-gray-50">
                                            <td class="py-3 px-4">
                                                <div class="flex items-center">
                                                    <span class="color-indicator" style="background-color: {{ $cores[$index] }}"></span>
                                                    @if(isset($item['estilista_id']) && $item['estilista_id'])
                                                        <a href="{{ route('estilistas.show', $item['estilista_id']) }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                                                            {{ $item['nome_estilista'] }}
                                                        </a>
                                                    @else
                                                        {{ $item['nome_estilista'] }}
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="py-3 px-4 text-center">{{ $item['total'] }}</td>
                                            <td class="py-3 px-4 text-center">{{ number_format(($item['total'] / array_sum($data)) * 100, 1) }}%</td>
                                        </tr>
                                        @endforeach
                                        
                                    </tbody>
                                    <tfoot class="bg-gray-100">
                                        <tr>
                                            <th class="py-3 px-4 text-left font-bold">Total</th>
                                            <th class="py-3 px-4 text-center font-bold">{{ array_sum($data) }}</th>
                                            <th class="py-3 px-4 text-center font-bold">100%</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-200 my-6 pt-6"></div>
                    
                    <div class="flex justify-between items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Voltar ao Dashboard
                        </a>
                        <button onclick="exportarPDF()" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Exportar como PDF
                        </button>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    // Register the DataLabels plugin with Chart.js
    Chart.register(ChartDataLabels);
    
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('donutChart').getContext('2d');
        
        // Controle de exibição dos campos de data personalizada
        const periodoSelect = document.getElementById('periodo');
        const dataInicioContainer = document.getElementById('data-inicio-container');
        const dataFimContainer = document.getElementById('data-fim-container');
        
        periodoSelect.addEventListener('change', function() {
            if (this.value === 'personalizado') {
                dataInicioContainer.classList.remove('hidden');
                dataFimContainer.classList.remove('hidden');
            } else {
                dataInicioContainer.classList.add('hidden');
                dataFimContainer.classList.add('hidden');
            }
        });
        
        // Dados do gráfico
        const data = {
            labels: @json($labels),
            datasets: [{
                data: @json($data),
                backgroundColor: @json($cores),
                borderColor: @json($cores),
                borderWidth: 1,
                hoverOffset: 15
            }]
        };
        
        // Configuração do gráfico
        const config = {
            type: 'doughnut',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            boxWidth: 12
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    },
                    datalabels: {
                        color: '#fff',
                        font: {
                            weight: 'bold',
                            size: 12
                        },
                        formatter: (value, ctx) => {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return percentage > 5 ? `${percentage}%` : '';
                        }
                    }
                }
            }
        };
        
        // Criar o gráfico
        const myChart = new Chart(ctx, config);
    });
    
    // Função para exportar o gráfico como PDF
    function exportarPDF() {
        // Elemento a ser exportado (agora usando a div com classe p-6 que contém todo o conteúdo)
        const element = document.querySelector('.bg-white.overflow-hidden.shadow-sm.sm\\:rounded-lg');
        
        // Configurações do PDF
        const periodoInicio = '{{ $periodoInicio }}';
        const periodoFim = '{{ $periodoFim }}';
        const titulo = '{{ $titulo ?? "Ultimos-12-meses" }}'.replace(/ /g, '-').normalize('NFD').replace(/[\u0300-\u036f]/g, ''); // Remove acentos e espaços
        
        const options = {
            margin: 10,
            filename: `produtos-por-estilista-${titulo}-${periodoInicio.replace(/\//g, '-')}-a-${periodoFim.replace(/\//g, '-')}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        
        // Notificar o usuário
        alert('Gerando PDF, por favor aguarde...');
        
        // Gerar o PDF
        html2pdf().set(options).from(element).save().then(() => {
            console.log('PDF gerado com sucesso!');
        });
    }
</script>


<style>
    .chart-container {
        margin: 20px auto;
    }
    
    .color-indicator {
        display: inline-block;
        width: 15px;
        height: 15px;
        margin-right: 8px;
        border-radius: 3px;
    }
    
    @media print {
        .btn, .alert {
            display: none !important;
        }
    }
</style>
</x-app-layout>
