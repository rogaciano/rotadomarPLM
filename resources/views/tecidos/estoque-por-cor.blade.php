<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Estoque por Cor') }} - {{ $tecido->descricao }}
            </h2>
            <div>
                @if(auth()->user() && auth()->user()->canUpdate('tecidos'))
                <a href="{{ route('tecidos.atualizar-estoque', $tecido->id) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Atualizar Estoque
                </a>
                @endif
                <button onclick="window.history.go(-1); return false;" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Voltar
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Informações do Tecido</h3>
                            <div class="text-sm text-gray-500">
                                Última atualização: {{ $tecido->ultima_consulta_estoque ? $tecido->ultima_consulta_estoque->format('d/m/Y H:i') : 'Não disponível' }}
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm font-medium text-gray-500">Descrição</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $tecido->descricao }}</p>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm font-medium text-gray-500">Referência</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $tecido->referencia ?: 'Não informada' }}</p>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm font-medium text-gray-500">Estoque Total</p>
                                <p class="mt-1 text-sm text-gray-900 font-semibold">{{ number_format($tecido->quantidade_estoque, 2, ',', '.') }} metros</p>
                            </div>
                        </div>

                        <!-- Resumo de Necessidades -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">Cálculo da Necessidade</h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <p>A <strong>Necessidade</strong> é calculada automaticamente com base nos produtos que usam este tecido:</p>
                                        <p class="mt-1"><strong>Necessidade = Quantidade da cor no produto × Consumo do tecido</strong></p>
                                        <p class="mt-1">O <strong>Saldo</strong> mostra se há estoque suficiente (verde) ou déficit (vermelho).</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Estoque por Cor</h3>
                        
                        @php $canUpdate = auth()->user() && auth()->user()->canUpdate('tecidos'); @endphp
                        <form action="{{ route('tecidos.salvar-quantidades', $tecido->id) }}" method="POST">
                            @csrf
                            <div class="overflow-x-auto bg-white rounded-lg shadow">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                         <tr>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Selecionar</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cor</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Estoque Atual</th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Necessidade</th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo</th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Quantidade Pretendida</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Observações</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Atualização</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($tecido->estoquesCores as $estoqueCor)
                                        <tr>
                                            <td class="px-3 py-4 whitespace-nowrap">
                                                <input type="checkbox" name="cores[{{ $estoqueCor->id }}][selecionada]" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ $canUpdate ? '' : 'disabled' }}>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="h-4 w-4 rounded-full mr-2" style="background-color: {{ $estoqueCor->codigo_cor ?: '#ccc' }}"></div>
                                                    <a href="{{ route('tecidos.produtos-por-cor', ['tecidoId' => $tecido->id, 'corId' => $estoqueCor->id]) }}" class="text-sm font-medium text-blue-600 underline hover:text-blue-800">
                                                        {{ $estoqueCor->cor }}
                                                    </a>
                                                    <input type="hidden" name="cores[{{ $estoqueCor->id }}][cor]" value="{{ $estoqueCor->cor }}">
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $estoqueCor->codigo_cor ?: '-' }}
                                                <input type="hidden" name="cores[{{ $estoqueCor->id }}][codigo_cor]" value="{{ $estoqueCor->codigo_cor }}">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">
                                                {{ number_format($estoqueCor->quantidade, 2, ',', '.') }}
                                                <input type="hidden" name="cores[{{ $estoqueCor->id }}][quantidade_atual]" value="{{ $estoqueCor->quantidade }}">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">
                                                {{ number_format($estoqueCor->necessidade, 2, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">
                                                @php
                                                    $saldo = $estoqueCor->saldo;
                                                    $corSaldo = $saldo >= 0 ? 'text-green-600' : 'text-red-600';
                                                @endphp
                                                <span class="{{ $corSaldo }}">
                                                    {{ number_format($saldo, 2, ',', '.') }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <input type="number" name="cores[{{ $estoqueCor->id }}][quantidade_pretendida]" step="0.01" min="0" class="mt-1 block w-24 mx-auto rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="0.00" {{ $canUpdate ? '' : 'disabled' }}>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                {{ $estoqueCor->observacoes ?: '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $estoqueCor->data_atualizacao ? $estoqueCor->data_atualizacao->format('d/m/Y') : '-' }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                Total
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                                                {{ number_format($tecido->total_estoque_por_cores, 2, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                                                @php
                                                    $totalNecessidade = $tecido->estoquesCores->sum('necessidade');
                                                @endphp
                                                {{ number_format($totalNecessidade, 2, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                                                @php
                                                    $totalSaldo = $tecido->total_estoque_por_cores - $totalNecessidade;
                                                    $corTotalSaldo = $totalSaldo >= 0 ? 'text-green-600' : 'text-red-600';
                                                @endphp
                                                <span class="{{ $corTotalSaldo }} font-medium">
                                                    {{ number_format($totalSaldo, 2, ',', '.') }}
                                                </span>
                                            </td>
                                            <td colspan="3"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            
                            @if($canUpdate)
                            <div class="mt-6 flex justify-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Salvar Quantidades Selecionadas
                                </button>
                            </div>
                            @endif
                        </form>
                    </div>

                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Visualização Gráfica</h3>
                        <div class="bg-white p-4 rounded-lg shadow" style="height: 300px;">
                            <!-- Aqui você pode adicionar um gráfico de barras ou pizza para visualizar a distribuição de estoque por cor -->
                            <!-- Exemplo de placeholder para um gráfico -->
                            <div class="flex items-center justify-center h-full">
                                <p class="text-gray-500">Gráfico de distribuição de estoque por cor (implementação futura)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get all dropdown buttons
        const dropdownButtons = document.querySelectorAll('.dropdown button');
        
        // Add click event to each button
        dropdownButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                
                // Close all other dropdowns first
                document.querySelectorAll('.dropdown-content').forEach(content => {
                    if (content !== this.nextElementSibling) {
                        content.classList.add('hidden');
                    }
                });
                
                // Toggle current dropdown
                const dropdownContent = this.nextElementSibling;
                dropdownContent.classList.toggle('hidden');
            });
        });
        
        // Close all dropdowns when clicking outside
        document.addEventListener('click', function() {
            document.querySelectorAll('.dropdown-content').forEach(content => {
                content.classList.add('hidden');
            });
        });
    });
</script>
</x-app-layout>
