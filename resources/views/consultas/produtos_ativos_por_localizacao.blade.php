<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Produtos Ativos por Localização') }}
        </h2>
    </x-slot>
    
    <!-- Loading Overlay -->
    <div id="loading-overlay" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-xl max-w-md w-full">
            <div class="flex items-center justify-center mb-4">
                <svg class="animate-spin h-8 w-8 text-indigo-600 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-lg font-medium">Processando dados...</span>
            </div>
            <p class="text-gray-600 text-center">Esta consulta pode levar alguns instantes. Por favor, aguarde.</p>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Filtro de Status -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-2">Filtrar por Status</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($todosStatus as $status)
                                <a href="{{ route('consultas.produtos-ativos-por-localizacao', ['status_id' => $status->id]) }}" 
                                   class="px-4 py-2 rounded-md text-sm font-medium {{ $statusSelecionado && $statusSelecionado->id == $status->id ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                    {{ $status->descricao }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-2">Resumo</h3>
                        <p class="text-gray-600">Total de produtos com status <span class="font-medium">{{ $statusSelecionado ? $statusSelecionado->descricao : 'Desconhecido' }}</span>: <span class="font-bold">{{ $totalProdutos }}</span></p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Localização
                                    </th>
                                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total de Produtos
                                    </th>
                                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Percentual
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($produtosPorLocalizacao as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            <a href="{{ route('produtos.index', ['localizacao' => $item['nome_localizacao']]) }}" class="text-indigo-600 hover:text-indigo-900 hover:underline">
                                                {{ $item['nome_localizacao'] }}
                                            </a>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $item['total'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            @if($totalProdutos > 0)
                                                {{ number_format(($item['total'] / $totalProdutos) * 100, 1) }}%
                                            @else
                                                0%
                                            @endif
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2.5 mt-1">
                                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $totalProdutos > 0 ? ($item['total'] / $totalProdutos) * 100 : 0 }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Hide loading overlay when page is fully loaded
        window.addEventListener('load', function() {
            document.getElementById('loading-overlay').style.display = 'none';
        });
        
        // Show loading overlay when navigating away
        document.addEventListener('click', function(e) {
            // Check if the clicked element is a link
            if (e.target.tagName === 'A' || e.target.closest('a')) {
                document.getElementById('loading-overlay').style.display = 'flex';
            }
        });
    </script>
</x-app-layout>
