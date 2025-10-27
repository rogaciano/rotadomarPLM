<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalhes do Estilista') }}
            </h2>
            <div>
                <a href="{{ route('estilistas.edit', $estilista->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Editar
                </a>
                <a href="{{ route('estilistas.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Voltar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Coluna da Foto -->
                        <div class="md:col-span-1">
                            <div class="bg-gray-50 p-4 rounded-lg h-full flex flex-col items-center">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Foto do Estilista</h3>
                                @if($estilista->foto)
                                    <img src="{{ $estilista->foto_url }}" alt="Foto do estilista {{ $estilista->nome_estilista }}" class="h-64 w-64 object-cover rounded-md border-2 border-gray-200">
                                @else
                                    <div class="h-64 w-64 bg-gray-200 rounded-md flex items-center justify-center text-gray-500">
                                        <span>Sem foto</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Coluna das Informações -->
                        <div class="md:col-span-1">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Informações do Estilista</h3>
                            <div class="bg-gray-50 p-4 rounded-lg h-full">
                                <div class="mb-4">
                                    <p class="text-sm font-medium text-gray-500">Nome</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ $estilista->nome_estilista }}</p>
                                </div>
                                <div class="mb-4">
                                    <p class="text-sm font-medium text-gray-500">Marca</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ $estilista->marca ? $estilista->marca->nome_marca : 'Não informada' }}</p>
                                </div>
                                <div class="mb-4">
                                    <p class="text-sm font-medium text-gray-500">Suporte Marca</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ $estilista->suporte_marca ?: 'Não informado' }}</p>
                                </div>
                                <div class="mb-4">
                                    <p class="text-sm font-medium text-gray-500">Data de Cadastro</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ $estilista->data_cadastro ? $estilista->data_cadastro->format('d/m/Y') : 'Não informada' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Status</p>
                                    <p class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $estilista->ativo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $estilista->ativo ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            </div>
                        </div>

                    <!-- Seção de Estatísticas -->
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Estatísticas</h3>

                        <!-- Cards de Resumo -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                            <!-- Total de Produtos -->
                            <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-500">Total de Produtos</p>
                                        <p class="text-2xl font-semibold text-gray-900">{{ $totalProdutos }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Tempo Médio de Ativação -->
                            <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-500">Tempo Médio de Ativação</p>
                                        <p class="text-2xl font-semibold text-gray-900">{{ $tempoMedioAtivacao ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Estatísticas Detalhadas -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Produtos por Marca -->
                            <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                                <h4 class="text-sm font-medium text-gray-500 mb-3">Produtos por Marca</h4>
                                @if(count($produtosPorMarca) > 0)
                                    <ul class="space-y-2">
                                        @foreach($produtosPorMarca as $marca => $total)
                                            <li class="flex justify-between text-sm">
                                                <span class="text-gray-600">{{ $marca }}</span>
                                                <span class="font-medium text-gray-900">{{ $total }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-sm text-gray-500">Nenhum produto cadastrado</p>
                                @endif
                            </div>

                            <!-- Produtos por Status -->
                            <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                                <h4 class="text-sm font-medium text-gray-500 mb-3">Produtos por Status</h4>
                                @if(count($produtosPorStatus) > 0)
                                    <ul class="space-y-2">
                                        @foreach($produtosPorStatus as $status => $total)
                                            <li class="flex justify-between text-sm">
                                                <span class="text-gray-600">{{ $status }}</span>
                                                <span class="font-medium text-gray-900">{{ $total }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-sm text-gray-500">Nenhum status disponível</p>
                                @endif
                            </div>

                            <!-- Produtos por Grupo -->
                            <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                                <h4 class="text-sm font-medium text-gray-500 mb-3">Produtos por Grupo</h4>
                                @if(count($produtosPorGrupo) > 0)
                                    <ul class="space-y-2">
                                        @foreach($produtosPorGrupo as $grupo => $total)
                                            <li class="flex justify-between text-sm">
                                                <span class="text-gray-600">{{ $grupo }}</span>
                                                <span class="font-medium text-gray-900">{{ $total }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-sm text-gray-500">Nenhum grupo cadastrado</p>
                                @endif
                            </div>

                            <!-- Produtos por Localização -->
                            <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                                <h4 class="text-sm font-medium text-gray-500 mb-3">Produtos por Localização</h4>
                                @if(count($produtosPorLocalizacao) > 0)
                                    <ul class="space-y-2">
                                        @foreach($produtosPorLocalizacao as $localizacao => $total)
                                            <li class="flex justify-between text-sm">
                                                <span class="text-gray-600">{{ $localizacao }}</span>
                                                <span class="font-medium text-gray-900">{{ $total }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-sm text-gray-500">Nenhuma localização disponível</p>
                                @endif
                            </div>
                        </div>

                        <!-- Gráfico de Produtos por Mês -->
                        <div class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Produtos por Mês (Últimos 12 meses)</h3>
                            <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                                <canvas id="produtosPorMesChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Script para o gráfico -->
                    @push('styles')
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.css">
                    @endpush

                    @push('scripts')
                    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const ctx = document.getElementById('produtosPorMesChart').getContext('2d');
                            const produtosPorMesData = @json($produtosPorMes);
                            
                            new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: produtosPorMesData.labels,
                                    datasets: [{
                                        label: 'Produtos Cadastrados',
                                        data: produtosPorMesData.data,
                                        borderColor: 'rgb(99, 102, 241)',
                                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                                        borderWidth: 2,
                                        tension: 0.3,
                                        fill: true,
                                        pointBackgroundColor: 'white',
                                        pointBorderColor: 'rgb(99, 102, 241)',
                                        pointBorderWidth: 2,
                                        pointRadius: 4,
                                        pointHoverRadius: 6
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: true,
                                            position: 'top',
                                        },
                                        tooltip: {
                                            mode: 'index',
                                            intersect: false,
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                precision: 0
                                            }
                                        }
                                    }
                                }
                            });
                        });
                    </script>
                    @endpush

                    <!-- Botões de Ação -->
                    <div class="mt-6 flex justify-between items-center">
                        <a href="{{ route('estilistas.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Voltar para a lista
                        </a>

                        <div class="flex space-x-2">
                            <a href="{{ route('estilistas.edit', $estilista->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-800 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Editar
                            </a>

                            <form action="{{ route('estilistas.destroy', $estilista->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150" onclick="return confirm('Tem certeza que deseja excluir este estilista?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Excluir
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
