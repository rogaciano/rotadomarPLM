<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalhes da Capacidade Mensal') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('localizacao-capacidade.edit', $capacidade->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    Editar
                </a>
                <a href="{{ route('localizacao-capacidade.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Voltar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                <!-- Informações da Capacidade -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informações da Capacidade</h3>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Localização</p>
                                <p class="mt-1 text-lg text-gray-900">{{ $capacidade->localizacao->nome_localizacao }}</p>
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-500">Período</p>
                                <p class="mt-1 text-lg text-gray-900">{{ $capacidade->mes_ano_formatado }}</p>
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-500">Capacidade Planejada</p>
                                <p class="mt-1 text-lg font-semibold text-blue-600">{{ $capacidade->capacidade }} produtos</p>
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-500">Produtos Previstos</p>
                                @php
                                    $previstos = $capacidade->getProdutosPrevistos();
                                    $isAcima = $capacidade->isAcimaDaCapacidade();
                                @endphp
                                <p class="mt-1 text-lg font-semibold {{ $isAcima ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $previstos }} produtos
                                </p>
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-500">Saldo</p>
                                @php
                                    $saldo = $capacidade->getSaldo();
                                @endphp
                                <p class="mt-1 text-lg font-semibold {{ $saldo < 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $saldo }} produtos
                                </p>
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-500">Ocupação</p>
                                @php
                                    $percentual = $capacidade->getPercentualOcupacao();
                                @endphp
                                <div class="flex items-center mt-1">
                                    <div class="w-full bg-gray-200 rounded-full h-4 mr-2">
                                        <div class="h-4 rounded-full {{ $percentual > 100 ? 'bg-red-600' : ($percentual > 80 ? 'bg-yellow-600' : 'bg-green-600') }}" style="width: {{ min($percentual, 100) }}%"></div>
                                    </div>
                                    <span class="text-lg font-semibold {{ $percentual > 100 ? 'text-red-600' : 'text-gray-700' }}">
                                        {{ $percentual }}%
                                    </span>
                                </div>
                            </div>

                            @if($capacidade->observacoes)
                            <div class="md:col-span-2">
                                <p class="text-sm font-medium text-gray-500">Observações</p>
                                <p class="mt-1 text-gray-900 whitespace-pre-line">{{ $capacidade->observacoes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Lista de Produtos Previstos -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Produtos Previstos para este Período</h3>

                    @if($produtos->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Referência
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Descrição
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Marca
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Grupo
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Observações
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Quantidade
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Data Prevista
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($produtos as $produto)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                <a href="{{ route('produtos.show', $produto->id) }}" class="text-blue-600 hover:text-blue-900">
                                                    {{ $produto->referencia }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $produto->descricao }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($produto->marca)
                                                    @if($produto->marca->cor_fundo && $produto->marca->cor_fonte)
                                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full" style="background-color: {{ $produto->marca->cor_fundo }}; color: {{ $produto->marca->cor_fonte }};">
                                                            {{ $produto->marca->nome_marca }}
                                                        </span>
                                                    @else
                                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                                            {{ $produto->marca->nome_marca }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="text-gray-400 italic">N/A</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $produto->grupoProduto->descricao ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600">
                                                @php
                                                    // Debug específico para produto 060021
                                                    $debugInfo = '';
                                                    if ($produto->referencia == '060021') {
                                                        $debugInfo .= "ID: {$produto->id} | ";
                                                        $debugInfo .= "Rel carregado: " . (isset($produto->observacoes) ? 'SIM' : 'NÃO') . " | ";
                                                        if (isset($produto->observacoes)) {
                                                            $debugInfo .= "Count rel: {$produto->observacoes->count()} | ";
                                                        }
                                                    }
                                                    
                                                    // Tentar carregar diretamente
                                                    $obsDirecta = \App\Models\ProdutoObservacao::where('produto_id', $produto->id)->get();
                                                    
                                                    if ($produto->referencia == '060021') {
                                                        $debugInfo .= "Count direto: {$obsDirecta->count()}";
                                                    }
                                                    
                                                    // Usar a query direta sempre
                                                    $produto->setRelation('observacoes', $obsDirecta);
                                                @endphp
                                                
                                                @if($produto->referencia == '060021')
                                                    <div class="text-xs text-red-600 mb-1">DEBUG: {{ $debugInfo }}</div>
                                                @endif
                                                
                                                @php
                                                    // Buscar observações das localizações (ordem de produção)
                                                    $obsLocalizacoes = $produto->localizacoes->filter(function($loc) {
                                                        return $loc->pivot->ordem_producao || $loc->pivot->observacao;
                                                    });
                                                    
                                                    $temObservacoes = ($produto->observacoes && $produto->observacoes->count() > 0) || $obsLocalizacoes->count() > 0;
                                                @endphp
                                                
                                                @if($temObservacoes)
                                                    <div class="max-w-xs">
                                                        {{-- Observações do Produto --}}
                                                        @if($produto->observacoes && $produto->observacoes->count() > 0)
                                                            @foreach($produto->observacoes as $obs)
                                                                <div class="mb-1 text-xs {{ !$loop->last || $obsLocalizacoes->count() > 0 ? 'border-b border-gray-200 pb-1' : '' }}">
                                                                    {{ Str::limit($obs->observacao, 80) }}
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                        
                                                        {{-- Observações das Localizações (Ordem de Produção) --}}
                                                        @foreach($obsLocalizacoes as $loc)
                                                            <div class="mb-1 text-xs {{ !$loop->last ? 'border-b border-gray-200 pb-1' : '' }}">
                                                                @if($loc->pivot->ordem_producao)
                                                                    <span class="font-semibold text-blue-700">OP: {{ $loc->pivot->ordem_producao }}</span>
                                                                @endif
                                                                @if($loc->pivot->ordem_producao && $loc->pivot->observacao)
                                                                    <span class="text-gray-500"> - </span>
                                                                @endif
                                                                @if($loc->pivot->observacao)
                                                                    <span class="text-gray-600">{{ Str::limit($loc->pivot->observacao, 60) }}</span>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-gray-400 italic text-xs">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-semibold text-gray-900">
                                                {{ number_format($produto->quantidade, 0, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @php
                                                    // Buscar primeira data prevista das localizações
                                                    $primeiraData = $produto->localizacoes->where('pivot.data_prevista_faccao', '!=', null)->sortBy('pivot.data_prevista_faccao')->first();
                                                @endphp
                                                @if($primeiraData && $primeiraData->pivot->data_prevista_faccao)
                                                    {{ is_string($primeiraData->pivot->data_prevista_faccao) ? \Carbon\Carbon::parse($primeiraData->pivot->data_prevista_faccao)->format('d/m/Y') : $primeiraData->pivot->data_prevista_faccao->format('d/m/Y') }}
                                                    @if($produto->localizacoes->where('pivot.data_prevista_faccao', '!=', null)->count() > 1)
                                                        <span class="text-xs text-gray-400">(+{{ $produto->localizacoes->where('pivot.data_prevista_faccao', '!=', null)->count() - 1 }})</span>
                                                    @endif
                                                @else
                                                    <span class="text-gray-400">N/A</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p class="text-gray-500">Nenhum produto previsto para este período.</p>
                        </div>
                    @endif
                </div>

                <!-- Metadados -->
                <div class="border-t pt-4 mt-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-500">
                        <div>
                            <span class="font-medium">Criado em:</span> {{ $capacidade->created_at->format('d/m/Y H:i:s') }}
                        </div>
                        <div>
                            <span class="font-medium">Última atualização:</span> {{ $capacidade->updated_at->format('d/m/Y H:i:s') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
