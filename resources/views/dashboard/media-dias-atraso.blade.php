<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Média de Dias por Localização') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Voltar para Dashboard
                </a>
                <button id="exportPdfBtn" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 ml-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Exportar PDF
                </button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-2">Média de Dias por Localização (Movimentações não concluídas)</h3>
                        <p class="text-sm text-gray-600">Este gráfico mostra a média de dias úteis que os produtos permanecem em cada localização para movimentações não concluídas.</p>
                    </div>
                    
                    <div class="flex flex-col lg:flex-row">
                        <div class="w-full lg:w-1/2 mb-6 lg:mb-0">
                            <canvas id="mediaChart" class="w-full" height="300"></canvas>
                        </div>
                        <div class="w-full lg:w-1/2 lg:pl-6">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200" id="tabelaMediaDias">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Localização</th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Média (dias)</th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Prazo</th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Movimentações</th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">% Atrasados</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($localizacoesAtivas as $item)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $item['localizacao'] }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                                    <span class="px-2 py-1 rounded-full text-xs 
                                                        @if($item['status'] === 'critico') bg-red-100 text-red-800 
                                                        @elseif($item['status'] === 'atencao') bg-yellow-100 text-yellow-800 
                                                        @else bg-green-100 text-green-800 @endif">
                                                        {{ $item['media_dias'] }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                                    {{ $item['prazo_setor'] ?? 'N/A' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                                    {{ $item['total_movimentacoes'] }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                                    <span class="px-2 py-1 rounded-full text-xs 
                                                        @if($item['percentual_atrasados'] > 50) bg-red-100 text-red-800 
                                                        @elseif($item['percentual_atrasados'] > 25) bg-yellow-100 text-yellow-800 
                                                        @else bg-green-100 text-green-800 @endif">
                                                        {{ $item['percentual_atrasados'] }}%
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                                    Nenhuma movimentação não concluída encontrada.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(count($localizacoesInativas) > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-2">Localizações Inativas</h3>
                        <p class="text-sm text-gray-600">Localizações marcadas como inativas que ainda possuem movimentações não concluídas.</p>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Localização</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Média (dias)</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Prazo</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Movimentações</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">% Atrasados</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($localizacoesInativas as $item)
                                    <tr class="hover:bg-gray-50 bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-500">
                                            {{ $item['localizacao'] }} (Inativa)
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                            <span class="px-2 py-1 rounded-full text-xs 
                                                @if($item['status'] === 'critico') bg-red-100 text-red-800 
                                                @elseif($item['status'] === 'atencao') bg-yellow-100 text-yellow-800 
                                                @else bg-green-100 text-green-800 @endif">
                                                {{ $item['media_dias'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                            {{ $item['prazo_setor'] ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                            {{ $item['total_movimentacoes'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                            <span class="px-2 py-1 rounded-full text-xs 
                                                @if($item['percentual_atrasados'] > 50) bg-red-100 text-red-800 
                                                @elseif($item['percentual_atrasados'] > 25) bg-yellow-100 text-yellow-800 
                                                @else bg-green-100 text-green-800 @endif">
                                                {{ $item['percentual_atrasados'] }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Adicionar Chart.js e html2pdf -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Configuração do gráfico
            const ctx = document.getElementById('mediaChart').getContext('2d');
            
            const mediaChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($labels),
                    datasets: [{
                        label: 'Média de Dias',
                        data: @json($data),
                        backgroundColor: @json($cores),
                        borderColor: @json($cores),
                        borderWidth: 1
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
                                    return `Média: ${context.raw} dias`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Dias Úteis'
                            }
                        },
                        x: {
                            ticks: {
                                autoSkip: false,
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    }
                }
            });
            
            // Configuração do botão de exportação PDF
            document.getElementById('exportPdfBtn').addEventListener('click', function() {
                const element = document.querySelector('.max-w-7xl');
                
                const opt = {
                    margin: 10,
                    filename: 'media-dias-atraso.pdf',
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2 },
                    jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
                };
                
                // Adicionar título e data ao PDF
                const title = document.createElement('div');
                title.innerHTML = `
                    <div style="text-align: center; margin-bottom: 20px;">
                        <h1 style="font-size: 18px; font-weight: bold;">Média de Dias por Localização</h1>
                        <p style="font-size: 12px;">Gerado em: ${new Date().toLocaleDateString('pt-BR')}</p>
                    </div>
                `;
                
                // Clone o elemento para não modificar o original
                const elementClone = element.cloneNode(true);
                
                // Remover o botão de voltar e exportar do clone
                const buttons = elementClone.querySelectorAll('a[href], button');
                buttons.forEach(button => button.remove());
                
                // Adicionar título ao início do clone
                elementClone.insertBefore(title, elementClone.firstChild);
                
                // Gerar PDF
                html2pdf().from(elementClone).set(opt).save();
            });
        });
    </script>
</x-app-layout>
