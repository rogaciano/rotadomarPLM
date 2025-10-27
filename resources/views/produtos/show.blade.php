<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalhes do Produto') }}
            </h2>
            <div>
                @if(!$produto->trashed() && auth()->user()->canUpdate('produtos'))
                    <a href="{{ route('produtos.edit', $produto->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 active:bg-yellow-600 focus:outline-none focus:border-yellow-600 focus:ring focus:ring-yellow-300 disabled:opacity-25 transition mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        Editar
                    </a>
                @endif
                @if(!$produto->trashed() && auth()->user()->canCreate('produtos') && $produto->podeSerReprogramado())
                    <button onclick="document.getElementById('modal-reprogramar').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-orange-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-600 active:bg-orange-600 focus:outline-none focus:border-orange-600 focus:ring focus:ring-orange-300 disabled:opacity-25 transition mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                        </svg>
                        Reprogramar
                    </button>
                @endif
                @if(auth()->user()->canRead('produtos'))
                    <a href="{{ route('produtos.pdf', $produto->id) }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-300 disabled:opacity-25 transition mr-2" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd" />
                        </svg>
                        PDF
                    </a>
                @endif
                <a href="{{ request('back_url') ? request('back_url') : route('produtos.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-300 focus:outline-none focus:border-gray-300 focus:ring focus:ring-gray-200 disabled:opacity-25 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Voltar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8" style="max-width: 95%;">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <!-- Informações do Produto -->
                    <div class="bg-gray-50 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informações Básicas</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div>
                                <span class="block text-sm font-medium text-gray-500">Referência</span>
                                <span class="block mt-1 text-sm text-gray-900">
                                    {{ $produto->referencia }}
                                    @if($produto->isReprogramacao())
                                        <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800" title="Este produto é uma reprogramação">
                                            📋 Reprogramação #{{ str_pad($produto->numero_reprogramacao, 2, '0', STR_PAD_LEFT) }}
                                        </span>
                                    @endif
                                    @if(!$produto->isReprogramacao() && $produto->reprogramacoes()->count() > 0)
                                        <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800" title="Este produto possui reprogramações">
                                            🔄 {{ $produto->reprogramacoes()->count() }} {{ $produto->reprogramacoes()->count() == 1 ? 'reprogramação' : 'reprogramações' }}
                                        </span>
                                    @endif
                                </span>
                            </div>

                            <div>
                                <span class="block text-sm font-medium text-gray-500">Descrição</span>
                                <span class="block mt-1 text-sm text-gray-900">{{ $produto->descricao }}</span>
                            </div>

                            <div>
                                <span class="block text-sm font-medium text-gray-500">Data de Cadastro</span>
                                <span class="block mt-1 text-sm text-gray-900">{{ $produto->data_cadastro ? $produto->data_cadastro->format('d/m/Y') : 'N/A' }}</span>
                            </div>

                            <div>
                                <span class="block text-sm font-medium text-gray-500">Data Prevista para Produção</span>
                                <span class="block mt-1 text-sm text-gray-900">{{ $produto->data_prevista_producao_mes_ano ? $produto->data_prevista_producao_mes_ano : 'N/A' }}</span>
                            </div>

                            <div>
                                <span class="block text-sm font-medium text-gray-500">Marca</span>
                                <span class="block mt-1 text-sm text-gray-900">{{ $produto->marca->nome_marca ?? 'N/A' }}</span>
                            </div>

                            <div>
                                <span class="block text-sm font-medium text-gray-500">Estilista</span>
                                <span class="block mt-1 text-sm text-gray-900">{{ $produto->estilista->nome_estilista ?? 'N/A' }}</span>
                            </div>

                            <div>
                                <span class="block text-sm font-medium text-gray-500">Grupo</span>
                                <span class="block mt-1 text-sm text-gray-900">{{ $produto->grupoProduto->descricao ?? 'N/A' }}</span>
                            </div>

                            <div>
                                <span class="block text-sm font-medium text-gray-500">Quantidade</span>
                                <span class="block mt-1 text-sm text-gray-900">{{ $produto->quantidade }}</span>
                            </div>

                            <div>
                                <span class="block text-sm font-medium text-gray-500">Status</span>
                                <span class="block mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $produto->status && $produto->status->descricao == 'Ativo' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $produto->status ? $produto->status->descricao : 'N/A' }}
                                    </span>
                                </span>
                            </div>

                            <div>
                                <span class="block text-sm font-medium text-gray-500">Localização Atual</span>
                                <span class="block mt-1">
                                    @if($produto->localizacao_atual)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $produto->localizacao_atual->nome_localizacao }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-sm italic">Não localizado</span>
                                    @endif
                                </span>
                            </div>

                            <div>
                                <span class="block text-sm font-medium text-gray-500">Preço Atacado</span>
                                <span class="block mt-1 text-sm text-gray-900">R$ {{ number_format($produto->preco_atacado, 2, ',', '.') }}</span>
                            </div>

                            <div>
                                <span class="block text-sm font-medium text-gray-500">Preço Varejo</span>
                                <span class="block mt-1 text-sm text-gray-900">R$ {{ number_format($produto->preco_varejo, 2, ',', '.') }}</span>
                            </div>

                            <div>
                                <span class="block text-sm font-medium text-gray-500">Criado em</span>
                                <span class="block mt-1 text-sm text-gray-900">{{ $produto->created_at->format('d/m/Y H:i') }}</span>
                            </div>

                            <div>
                                <span class="block text-sm font-medium text-gray-500">Última atualização</span>
                                <span class="block mt-1 text-sm text-gray-900">{{ $produto->updated_at->format('d/m/Y H:i') }}</span>
                            </div>

                            @if($produto->deleted_at)
                                <div>
                                    <span class="block text-sm font-medium text-gray-500">Excluído em</span>
                                    <span class="block mt-1 text-sm text-red-600">{{ $produto->deleted_at->format('d/m/Y H:i') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Tecidos -->
                    <div class="bg-gray-50 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Tecidos</h3>

                        @if($produto->tecidos->count() > 0)
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referência</th>
                                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consumo</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($produto->tecidos as $tecido)
                                                    <tr>
                                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                                            <a href="{{ route('tecidos.show', $tecido->id) }}" class="text-blue-600 hover:text-blue-900 hover:underline">
                                                                {{ $tecido->descricao }}
                                                            </a>
                                                        </td>
                                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">{{ $tecido->referencia }}</td>
                                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">{{ $tecido->pivot->consumo ?? 'N/A' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <span class="text-gray-400 italic">Nenhum tecido associado a este produto</span>
                                @endif
                    </div>

                    <!-- Localizações -->
                    <div class="bg-gray-50 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Localizações do Produto</h3>
                            @if(auth()->user()->canUpdate('produtos'))
                                <button type="button" onclick="document.getElementById('modal-adicionar-localizacao').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-700 focus:outline-none focus:border-purple-700 focus:ring focus:ring-purple-300 disabled:opacity-25 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                    Adicionar Localização
                                </button>
                            @endif
                        </div>

                        @php
                            $totalLocalizacoes = $produto->localizacoes->sum('pivot.quantidade');
                            $quantidadeProduto = $produto->quantidade ?? 0;
                            $divergencia = $totalLocalizacoes - $quantidadeProduto;
                        @endphp

                        @if($produto->localizacoes->count() > 0 && $divergencia != 0)
                            <div class="mb-4 rounded-md p-4 {{ $divergencia > 0 ? 'bg-yellow-50 border-l-4 border-yellow-400' : 'bg-red-50 border-l-4 border-red-400' }}">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 {{ $divergencia > 0 ? 'text-yellow-400' : 'text-red-400' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm {{ $divergencia > 0 ? 'text-yellow-800' : 'text-red-800' }}">
                                            <strong>Atenção:</strong> 
                                            @if($divergencia > 0)
                                                O total das localizações (<strong>{{ number_format($totalLocalizacoes, 0, ',', '.') }}</strong>) está 
                                                <strong>{{ number_format(abs($divergencia), 0, ',', '.') }} unidade(s) acima</strong> 
                                                da quantidade pretendida do produto (<strong>{{ number_format($quantidadeProduto, 0, ',', '.') }}</strong>).
                                            @else
                                                O total das localizações (<strong>{{ number_format($totalLocalizacoes, 0, ',', '.') }}</strong>) está 
                                                <strong>{{ number_format(abs($divergencia), 0, ',', '.') }} unidade(s) abaixo</strong> 
                                                da quantidade pretendida do produto (<strong>{{ number_format($quantidadeProduto, 0, ',', '.') }}</strong>).
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @elseif($produto->localizacoes->count() > 0 && $divergencia == 0)
                            <div class="mb-4 rounded-md bg-green-50 border-l-4 border-green-400 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-green-800">
                                            <strong>Perfeito!</strong> O total das localizações (<strong>{{ number_format($totalLocalizacoes, 0, ',', '.') }}</strong>) 
                                            está de acordo com a quantidade pretendida do produto.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($produto->localizacoes->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Localização</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ordem Produção</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mês/Ano</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantidade</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Prev. Facção</th>
                                            @if(auth()->user()->canUpdate('produtos'))
                                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($produto->localizacoes as $localizacao)
                                            <tr>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                        {{ $localizacao->nome_localizacao }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        {{ $localizacao->pivot->ordem_producao ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 font-medium">
                                                    @if($localizacao->pivot->data_prevista_faccao)
                                                        {{ is_string($localizacao->pivot->data_prevista_faccao) ? \Carbon\Carbon::parse($localizacao->pivot->data_prevista_faccao)->format('m/Y') : $localizacao->pivot->data_prevista_faccao->format('m/Y') }}
                                                    @else
                                                        <span class="text-gray-400 italic">Sem data</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 font-medium">{{ number_format($localizacao->pivot->quantidade, 0, ',', '.') }}</td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                                    @if($localizacao->pivot->data_prevista_faccao)
                                                        {{ is_string($localizacao->pivot->data_prevista_faccao) ? \Carbon\Carbon::parse($localizacao->pivot->data_prevista_faccao)->format('d/m/Y') : $localizacao->pivot->data_prevista_faccao->format('d/m/Y') }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                @if(auth()->user()->canUpdate('produtos'))
                                                    <td class="px-4 py-2 whitespace-nowrap text-sm space-x-3">
                                                        @php
                                                            $dataFaccao = '';
                                                            if($localizacao->pivot->data_prevista_faccao) {
                                                                $dataFaccao = is_string($localizacao->pivot->data_prevista_faccao) 
                                                                    ? $localizacao->pivot->data_prevista_faccao 
                                                                    : $localizacao->pivot->data_prevista_faccao->format('Y-m-d');
                                                            }
                                                        @endphp
                                                        <button type="button" 
                                                            onclick="abrirModalEditarLocalizacao({{ $localizacao->pivot->id }}, {{ $localizacao->id }}, {{ json_encode($localizacao->nome_localizacao) }}, {{ $localizacao->pivot->quantidade }}, {{ json_encode($dataFaccao) }}, {{ json_encode($localizacao->pivot->ordem_producao ?? '') }}, {{ json_encode($localizacao->pivot->observacao ?? '') }})" 
                                                            class="text-indigo-600 hover:text-indigo-800 text-sm">
                                                            Editar
                                                        </button>
                                                        <form action="{{ route('produtos.localizacoes.destroy', [$produto->id, $localizacao->pivot->id]) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja remover esta localização?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                                                Remover
                                                            </button>
                                                        </form>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr class="border-t-2 border-gray-300">
                                            <td colspan="3" class="px-4 py-2 text-sm font-medium text-gray-700">Total nas Localizações:</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm font-bold {{ $divergencia != 0 ? ($divergencia > 0 ? 'text-yellow-700' : 'text-red-700') : 'text-green-700' }}">
                                                {{ number_format($totalLocalizacoes, 0, ',', '.') }}
                                            </td>
                                            <td colspan="{{ auth()->user()->canUpdate('produtos') ? '3' : '2' }}" class="px-4 py-2"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="px-4 py-2 text-sm font-medium text-gray-700">Quantidade Pretendida:</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm font-bold text-gray-900">
                                                {{ number_format($quantidadeProduto, 0, ',', '.') }}
                                            </td>
                                            <td colspan="{{ auth()->user()->canUpdate('produtos') ? '3' : '2' }}" class="px-4 py-2"></td>
                                        </tr>
                                        @if($divergencia != 0)
                                            <tr class="bg-gray-100">
                                                <td colspan="3" class="px-4 py-2 text-sm font-bold {{ $divergencia > 0 ? 'text-yellow-800' : 'text-red-800' }}">
                                                    Diferença:
                                                </td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm font-bold {{ $divergencia > 0 ? 'text-yellow-700' : 'text-red-700' }}">
                                                    {{ $divergencia > 0 ? '+' : '' }}{{ number_format($divergencia, 0, ',', '.') }}
                                                </td>
                                                <td colspan="{{ auth()->user()->canUpdate('produtos') ? '3' : '2' }}" class="px-4 py-2 text-xs italic {{ $divergencia > 0 ? 'text-yellow-600' : 'text-red-600' }}">
                                                    {{ $divergencia > 0 ? 'Excedente' : 'Faltante' }}
                                                </td>
                                            </tr>
                                        @else
                                            <tr class="bg-green-50">
                                                <td colspan="3" class="px-4 py-2 text-sm font-bold text-green-800">
                                                    Status:
                                                </td>
                                                <td colspan="{{ auth()->user()->canUpdate('produtos') ? '4' : '3' }}" class="px-4 py-2 whitespace-nowrap text-sm font-medium text-green-700">
                                                    ✓ Confere
                                                </td>
                                            </tr>
                                        @endif
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4 text-gray-500 italic">
                                Nenhuma localização associada. Clique em "Adicionar Localização" para incluir.
                            </div>
                        @endif
                    </div>

                    <!-- Variações de Cores -->
                     <div class="bg-gray-50 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                         <h3 class="text-lg font-semibold text-gray-800 mb-4">Variações de Cores</h3>

                         @php
                            // Calcular totais para uso em toda a tabela
                            $totalCores = collect($coresEnriquecidas)->sum('quantidade');
                            $totalCombinacoes = $produto->combinacoes ? $produto->combinacoes->sum('quantidade_pretendida') : 0;
                            $totalGeral = $totalCores + $totalCombinacoes;
                            $quantidadeProduto = $produto->quantidade ?? 0;
                            $isEqual = $totalGeral == $quantidadeProduto;
                         @endphp
                         
                         @if($produto->cores->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cor</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantidade</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estoque (m)</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                <div class="flex items-center">
                                                    <span>Necessidade Geral(m)</span>
                                                    <div class="tooltip-container ml-1" style="position: relative;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 hover:text-gray-600 cursor-help tooltip-trigger" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                                        </svg>
                                                        <div class="tooltip-content fixed z-[9999] w-64 p-2 bg-black text-xs rounded-lg hidden" style="color: white !important; box-shadow: 0 2px 8px rgba(0,0,0,0.25);">
                                                            Necessidade total de todos os produtos que usam esta cor de tecido.
                                                            <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-black rotate-45"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consumo deste Produto (m)</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo (m)</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produção Possível</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($coresEnriquecidas as $cor)
                                            <tr>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                                    <div class="flex items-center">
                                                        @if($cor['codigo_cor'])
                                                            <div class="w-4 h-4 rounded-full mr-2 border border-gray-300" style="background-color: {{ $cor['codigo_cor'] }}"></div>
                                                        @endif
                                                        {{ $cor['cor'] }}
                                                    </div>
                                                </td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">{{ $cor['codigo_cor'] ?? 'N/A' }}</td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 font-medium">{{ number_format($cor['quantidade'], 0, ',', '.') }}</td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ number_format($cor['estoque'], 2, ',', '.') }}</td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ number_format($cor['necessidade'], 2, ',', '.') }}</td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ number_format($cor['consumo_deste_produto'], 2, ',', '.') }}</td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm {{ $cor['saldo'] >= 0 ? 'text-green-600 font-medium' : 'text-red-600 font-medium' }}">
                                                    {{ number_format($cor['saldo'], 2, ',', '.') }}
                                                </td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm {{ $cor['producao_possivel'] > 0 ? 'text-green-600 font-medium' : 'text-red-600 font-medium' }}">
                                                    {{ number_format($cor['producao_possivel'], 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                     </tbody>
                                     <tfoot class="bg-gray-50">
                                        <tr>
                                            <td colspan="2" class="px-4 py-2 text-sm font-medium text-gray-700">Total Variações:</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm font-bold text-gray-900">
                                                {{ number_format(collect($coresEnriquecidas)->sum('quantidade'), 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm font-bold text-gray-900">{{ number_format(collect($coresEnriquecidas)->sum('estoque'), 2, ',', '.') }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm font-bold text-gray-900">{{ number_format(collect($coresEnriquecidas)->sum('necessidade'), 2, ',', '.') }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm font-bold text-gray-900">{{ number_format(collect($coresEnriquecidas)->sum('consumo_deste_produto'), 2, ',', '.') }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm font-bold {{ collect($coresEnriquecidas)->sum('saldo') >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ number_format(collect($coresEnriquecidas)->sum('saldo'), 2, ',', '.') }}
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="px-4 py-2 text-sm font-medium text-gray-700">Total Combinações:</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm font-bold text-gray-900">
                                                {{ number_format($totalCombinacoes, 0, ',', '.') }}
                                            </td>
                                            <td colspan="5"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100">Total Geral:</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm font-bold text-gray-900 bg-gray-100">
                                                <div class="flex items-center">
                                                    <span class="mr-2">{{ number_format($totalGeral, 0, ',', '.') }}</span>
                                                    @if($isEqual)
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor" title="Total geral (cores + combinações) está igual à quantidade do produto ({{ number_format($quantidadeProduto, 0, ',', '.') }})">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                        </svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor" title="Total geral (cores + combinações) não está igual à quantidade do produto ({{ number_format($quantidadeProduto, 0, ',', '.') }})">
                                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                        </svg>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm font-bold text-gray-900">{{ number_format(collect($coresEnriquecidas)->sum('estoque'), 2, ',', '.') }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm font-bold text-gray-900">{{ number_format(collect($coresEnriquecidas)->sum('necessidade'), 2, ',', '.') }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm font-bold text-gray-900">{{ number_format(collect($coresEnriquecidas)->sum('consumo_deste_produto'), 2, ',', '.') }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm font-bold {{ collect($coresEnriquecidas)->sum('saldo') >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ number_format(collect($coresEnriquecidas)->sum('saldo'), 2, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm font-bold text-gray-900"></td>
                                        </tr>
                                        @if(!$isEqual)
                                             <tr>
                                                 <td colspan="8" class="px-4 py-2 text-xs text-gray-600 bg-yellow-50">
                                                     <div class="flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-500 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                        </svg>
                                                        <span>
                                                            Quantidade do produto: {{ number_format($quantidadeProduto, 0, ',', '.') }} | 
                                                            Variações de cores: {{ number_format($totalCores, 0, ',', '.') }} | 
                                                            Combinações: {{ number_format($totalCombinacoes, 0, ',', '.') }} | 
                                                            Total geral: {{ number_format($totalGeral, 0, ',', '.') }} | 
                                                            Diferença: {{ number_format(abs($totalGeral - $quantidadeProduto), 0, ',', '.') }}
                                                        </span>
                                                     </div>
                                                 </td>
                                             </tr>
                                         @endif
                                     </tfoot>
                                 </table>
                             </div>
                         @else
                             <span class="text-gray-400 italic">Nenhuma variação de cor definida para este produto</span>
                         @endif
                     </div>

                    <!-- Combinações de Cores -->
                    <div class="bg-gray-50 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Combinações de Cores</h3>

                        @if($produto->combinacoes && $produto->combinacoes->count() > 0)
                            <div class="space-y-6">
                                @foreach($produto->combinacoes as $combinacao)
                                    <div class="border border-gray-200 rounded-md p-4 bg-white">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <h4 class="text-md font-medium text-gray-800">{{ $combinacao->descricao }}</h4>
                                                <div class="text-sm text-gray-600">Quantidade pretendida: {{ $combinacao->quantidade_pretendida }}</div>
                                                @if($combinacao->observacoes)
                                                    <div class="text-sm text-gray-500 italic mt-1">{{ $combinacao->observacoes }}</div>
                                                @endif
                                            </div>
                                            @if(auth()->user()->canUpdate('produtos'))
                                                <a href="{{ route('produtos.edit', $produto->id) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>

                                        <div class="mt-3">
                                            <h5 class="text-sm font-medium text-gray-700 mb-2">Componentes:</h5>
                                            @if($combinacao->componentes && $combinacao->componentes->count() > 0)
                                                <div class="grid grid-cols-1 gap-2">
                                                    @foreach($combinacao->componentes as $componente)
                                                        <div class="bg-gray-50 p-3 rounded-md border border-gray-200">
                                                            <div class="flex justify-between items-center">
                                                                <div class="flex items-center space-x-3">
                                                                    <div class="w-6 h-6 rounded border border-gray-300" style="background-color: {{ $componente->codigo_cor || '#FFFFFF' }}"></div>
                                                                    <div>
                                                                        <div class="font-medium text-gray-900">{{ $componente->tecido ? $componente->tecido->descricao : 'Tecido não encontrado' }}</div>
                                                                        <div class="text-sm text-gray-600">{{ $componente->cor }} {{ $componente->codigo_cor ? "({$componente->codigo_cor})" : '' }}</div>
                                                                    </div>
                                                                </div>
                                                                <div class="text-sm text-gray-700">Consumo: <span class="font-medium">{{ $componente->consumo }} m</span></div>
                                                            </div>

                                                            @if($componente->tecido)
                                                                @php
                                                                    $estoqueCor = $componente->tecido->estoquesCores()
                                                                        ->where('cor', $componente->cor)
                                                                        ->first();

                                                                    $estoque = $estoqueCor ? ($estoqueCor->quantidade ?? 0) : 0;
                                                                    $necessidade = $estoqueCor ? ($estoqueCor->necessidade ?? 0) : 0;
                                                                    $necessidadeProduto = $combinacao->quantidade_pretendida * $componente->consumo;
                                                                    $saldo = $estoque - $necessidade;
                                                                    $producaoPossivel = ($saldo > 0 && $componente->consumo > 0) ? floor($saldo / $componente->consumo) : 0;
                                                                @endphp

                                                                <div class="mt-3 pt-3 border-t border-gray-200">
                                                                    <table class="w-full text-sm">
                                                                        <tr>
                                                                            <td class="pr-1 py-0.5 whitespace-nowrap text-right"><span class="text-gray-600">Estoque:</span></td>
                                                                            <td class="pl-1 py-0.5 whitespace-nowrap"><span class="font-medium text-gray-900">{{ number_format($estoque, 2, ',', '.') }} m</span></td>
                                                                            <td class="pr-1 py-0.5 whitespace-nowrap text-right"><span class="text-gray-600">Necessidade Total:</span></td>
                                                                            <td class="pl-1 py-0.5 whitespace-nowrap"><span class="font-medium text-gray-900">{{ number_format($necessidade, 2, ',', '.') }} m</span></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="pr-1 py-0.5 whitespace-nowrap text-right"><span class="text-gray-600">Necessidade do Produto:</span></td>
                                                                            <td class="pl-1 py-0.5 whitespace-nowrap"><span class="font-medium text-gray-900">{{ number_format($necessidadeProduto, 2, ',', '.') }} m</span></td>
                                                                            <td class="pr-1 py-0.5 whitespace-nowrap text-right"><span class="text-gray-600">Saldo:</span></td>
                                                                            <td class="pl-1 py-0.5 whitespace-nowrap"><span class="font-medium {{ $saldo < 0 ? 'text-red-600' : 'text-green-600' }}">{{ number_format($saldo, 2, ',', '.') }} m</span></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="pr-1 py-0.5 whitespace-nowrap text-right"><span class="text-gray-600">Produção possível:</span></td>
                                                                            <td class="pl-1 py-0.5 whitespace-nowrap"><span class="font-medium {{ $producaoPossivel <= 0 ? 'text-red-600' : 'text-green-600' }}">{{ $producaoPossivel }} unidades</span></td>
                                                                            <td colspan="2"></td>
                                                                        </tr>
                                                                    </table>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-center py-3 text-gray-500 italic">Nenhum componente adicionado.</div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 text-gray-500 italic">
                                Nenhuma combinação definida para este produto.
                                @if(auth()->user()->canUpdate('produtos'))
                                    <div class="mt-2">
                                        <a href="{{ route('produtos.edit', $produto->id) }}" class="text-blue-600 hover:text-blue-800 hover:underline">Editar produto para adicionar combinações</a>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Documentos e Anexos -->
                    <div class="bg-gray-50 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Documentos e Anexos</h3>
                            @if(auth()->user()->canUpdate('produtos'))
                                <button type="button" onclick="document.getElementById('modal-adicionar-anexo').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                    Adicionar Anexo
                                </button>
                            @endif
                        </div>



                        <!-- Novos Anexos -->
                        <div>
                            @if($produto->anexos && $produto->anexos->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-3">
                                    @foreach($produto->anexos as $anexo)
                                        <div class="bg-white p-3 rounded-md border border-gray-200 flex items-center justify-between">
                                            <div class="flex items-center">
                                                @php
                                                    $icone = 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z';
                                                    $corIcone = 'text-blue-500';

                                                    if (in_array($anexo->tipo_arquivo, ['jpg', 'jpeg', 'png'])) {
                                                        $icone = 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z';
                                                        $corIcone = 'text-green-500';
                                                    }
                                                @endphp

                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 {{ $corIcone }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icone }}" />
                                                </svg>
                                                <span class="text-sm text-gray-700">{{ $anexo->descricao }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <a href="{{ asset('storage/' . $anexo->arquivo_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm mr-3">
                                                    Visualizar
                                                </a>
                                                @if(auth()->user()->canUpdate('produtos'))
                                                    <form action="{{ route('produtos.anexos.destroy', $anexo->id) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir este anexo?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                                            Excluir
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4 text-gray-500 italic">
                                    Nenhum anexo adicionado. Clique em "Adicionar Anexo" para incluir documentos.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Modal para adicionar anexo -->
                    @if(auth()->user()->canUpdate('produtos'))
                    <div id="modal-adicionar-anexo" class="fixed inset-0 bg-gray-500 bg-opacity-75 z-50 hidden overflow-y-auto">
                        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                            <!-- Overlay de fundo -->
                            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                            </div>

                            <!-- Centralização vertical -->
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                            <!-- Modal propriamente dito -->
                            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <div class="flex justify-between items-center">
                                    <h3 class="text-lg font-medium text-gray-900">Adicionar Anexo</h3>
                                    <button type="button" onclick="document.getElementById('modal-adicionar-anexo').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <form action="{{ route('produtos.anexos.store', $produto->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="px-6 py-4">
                                    <div class="mb-4">
                                        <label for="descricao" class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                                        <input type="text" name="descricao" id="descricao" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                    </div>
                                    <div>
                                        <label for="arquivo" class="block text-sm font-medium text-gray-700 mb-1">Arquivo</label>
                                        <input type="file" name="arquivo" id="arquivo" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
                                        <p class="mt-1 text-sm text-gray-500">Formatos aceitos: PNG, JPG, JPEG (máx. 10MB) e PDF (máx. 1MB)</p>
                                    </div>
                                </div>
                                <div class="px-6 py-4 bg-gray-50 text-right rounded-b-lg">
                                    <button type="button" onclick="document.getElementById('modal-adicionar-anexo').classList.add('hidden')" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-2">
                                        Cancelar
                                    </button>
                                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Salvar
                                    </button>
                                </div>
                            </form>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Modal para adicionar localização -->
                    @if(auth()->user()->canUpdate('produtos'))
                    <div id="modal-adicionar-localizacao" class="fixed inset-0 bg-gray-500 bg-opacity-75 z-50 hidden overflow-y-auto">
                        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                            <!-- Overlay de fundo -->
                            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                            </div>

                            <!-- Centralização vertical -->
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                            <!-- Modal propriamente dito -->
                            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                <div class="px-6 py-4 border-b border-gray-200">
                                    <div class="flex justify-between items-center">
                                        <h3 class="text-lg font-medium text-gray-900">Adicionar Localização</h3>
                                        <button type="button" onclick="document.getElementById('modal-adicionar-localizacao').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <form action="{{ route('produtos.localizacoes.store', $produto->id) }}" method="POST">
                                    @csrf
                                    <div class="px-6 py-4">
                                        <div class="mb-4">
                                            <label for="localizacao_id" class="block text-sm font-medium text-gray-700 mb-1">Localização *</label>
                                            <select name="localizacao_id" id="localizacao_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                                                <option value="">Selecione uma localização</option>
                                                @foreach(\App\Models\Localizacao::where('ativo', true)->orderBy('nome_localizacao')->get() as $loc)
                                                    <option value="{{ $loc->id }}">{{ $loc->nome_localizacao }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-4">
                                            <label for="ordem_producao" class="block text-sm font-medium text-gray-700 mb-1">Ordem de Produção *</label>
                                            <input type="text" name="ordem_producao" id="ordem_producao" maxlength="30" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                                            <p class="mt-1 text-sm text-gray-500">Número/código da ordem de produção</p>
                                        </div>
                                        <div class="mb-4">
                                            <label for="quantidade" class="block text-sm font-medium text-gray-700 mb-1">Quantidade *</label>
                                            <input type="number" name="quantidade" id="quantidade" min="1" step="1" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                                            <p class="mt-1 text-sm text-gray-500">Informe a quantidade do produto nesta localização</p>
                                        </div>
                                        <div class="mb-4">
                                            <label for="data_prevista_faccao" class="block text-sm font-medium text-gray-700 mb-1">Data Prevista para Facção</label>
                                            <input type="date" name="data_prevista_faccao" id="data_prevista_faccao" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                            <p class="mt-1 text-sm text-gray-500">Data prevista de facção para esta localização</p>
                                        </div>
                                        <div>
                                            <label for="observacao" class="block text-sm font-medium text-gray-700 mb-1">Observação</label>
                                            <textarea name="observacao" id="observacao" rows="2" maxlength="255" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"></textarea>
                                            <p class="mt-1 text-sm text-gray-500">Observações adicionais sobre esta ordem de produção</p>
                                        </div>
                                    </div>
                                    <div class="px-6 py-4 bg-gray-50 text-right rounded-b-lg">
                                        <button type="button" onclick="document.getElementById('modal-adicionar-localizacao').classList.add('hidden')" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 mr-2">
                                            Cancelar
                                        </button>
                                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                            Salvar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Modal para editar localização -->
                    @if(auth()->user()->canUpdate('produtos'))
                    <div id="modal-editar-localizacao" class="fixed inset-0 bg-gray-500 bg-opacity-75 z-50 hidden overflow-y-auto">
                        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                            <!-- Overlay de fundo -->
                            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                            </div>

                            <!-- Centralização vertical -->
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                            <!-- Modal propriamente dito -->
                            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                <div class="px-6 py-4 border-b border-gray-200">
                                    <div class="flex justify-between items-center">
                                        <h3 class="text-lg font-medium text-gray-900">Editar Localização</h3>
                                        <button type="button" onclick="fecharModalEditarLocalizacao()" class="text-gray-400 hover:text-gray-500">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <form id="form-editar-localizacao" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="px-6 py-4">
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Localização</label>
                                            <p id="edit-localizacao-nome" class="text-sm text-gray-900 font-semibold bg-purple-50 px-3 py-2 rounded"></p>
                                            <input type="hidden" id="edit-localizacao-id" name="localizacao_id">
                                        </div>
                                        <div class="mb-4">
                                            <label for="edit-ordem-producao" class="block text-sm font-medium text-gray-700 mb-1">Ordem de Produção *</label>
                                            <input type="text" name="ordem_producao" id="edit-ordem-producao" maxlength="30" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                            <p class="mt-1 text-sm text-gray-500">Número/código da ordem de produção</p>
                                        </div>
                                        <div class="mb-4">
                                            <label for="edit-quantidade" class="block text-sm font-medium text-gray-700 mb-1">Quantidade *</label>
                                            <input type="number" name="quantidade" id="edit-quantidade" min="1" step="1" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                            <p class="mt-1 text-sm text-gray-500">Informe a quantidade do produto nesta localização</p>
                                        </div>
                                        <div class="mb-4">
                                            <label for="edit-data-prevista-faccao" class="block text-sm font-medium text-gray-700 mb-1">Data Prevista para Facção</label>
                                            <input type="date" name="data_prevista_faccao" id="edit-data-prevista-faccao" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <p class="mt-1 text-sm text-gray-500">Data prevista de facção para esta localização</p>
                                        </div>
                                        <div>
                                            <label for="edit-observacao" class="block text-sm font-medium text-gray-700 mb-1">Observação</label>
                                            <textarea name="observacao" id="edit-observacao" rows="2" maxlength="255" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                                            <p class="mt-1 text-sm text-gray-500">Observações adicionais sobre esta ordem de produção</p>
                                        </div>
                                    </div>
                                    <div class="px-6 py-4 bg-gray-50 text-right rounded-b-lg">
                                        <button type="button" onclick="fecharModalEditarLocalizacao()" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-2">
                                            Cancelar
                                        </button>
                                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Atualizar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <script>
                        function abrirModalEditarLocalizacao(produtoLocalizacaoId, localizacaoId, nomeLocalizacao, quantidade, dataFaccao, ordemProducao, observacao) {
                            try {
                                console.log('Abrindo modal para editar localização:', {
                                    produtoLocalizacaoId,
                                    localizacaoId,
                                    nomeLocalizacao,
                                    quantidade,
                                    dataFaccao,
                                    ordemProducao,
                                    observacao
                                });
                                
                                document.getElementById('edit-localizacao-id').value = localizacaoId;
                                document.getElementById('edit-localizacao-nome').textContent = nomeLocalizacao;
                                document.getElementById('edit-ordem-producao').value = ordemProducao || '';
                                document.getElementById('edit-quantidade').value = quantidade;
                                document.getElementById('edit-data-prevista-faccao').value = dataFaccao || '';
                                document.getElementById('edit-observacao').value = observacao || '';
                                
                                // Atualizar action do formulário com o ID do registro produto_localizacao
                                const form = document.getElementById('form-editar-localizacao');
                                const currentRoute = "{{ route('produtos.localizacoes.update', [$produto->id, 'PLACEHOLDER']) }}";
                                form.action = currentRoute.replace('PLACEHOLDER', produtoLocalizacaoId);
                                
                                console.log('Action do formulário:', form.action);
                                
                                document.getElementById('modal-editar-localizacao').classList.remove('hidden');
                            } catch (error) {
                                console.error('Erro ao abrir modal de edição:', error);
                                alert('Erro ao abrir formulário de edição. Por favor, recarregue a página e tente novamente.');
                            }
                        }

                        function fecharModalEditarLocalizacao() {
                            document.getElementById('modal-editar-localizacao').classList.add('hidden');
                        }
                    </script>
                    @endif

                    <!-- Modal Nova Observação -->
                    <div id="modal-nova-observacao" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-md bg-white">
                            <div class="flex justify-between items-center mb-4 pb-3 border-b">
                                <h3 class="text-xl font-semibold text-gray-900">Nova Observação</h3>
                                <button onclick="fecharModalObservacao()" class="text-gray-400 hover:text-gray-600">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            <form id="form-observacao" onsubmit="salvarObservacao(event)">
                                @csrf
                                <input type="hidden" name="produto_id" value="{{ $produto->id }}">
                                
                                <div class="mb-4">
                                    <label for="observacao" class="block text-sm font-medium text-gray-700 mb-2">Observação *</label>
                                    
                                    <!-- Editor Quill -->
                                    <div id="editor-container" style="height: 150px; background: white; border: 1px solid #d1d5db; border-radius: 0.375rem;"></div>
                                    <textarea 
                                        id="observacao" 
                                        name="observacao" 
                                        style="display: none;"
                                    ></textarea>
                                    
                                    <div class="mt-2 text-xs text-gray-500">
                                        <p>💡 Use a barra de ferramentas acima para formatar o texto com cores, negrito, etc.</p>
                                        <p class="mt-1 text-gray-400">Máximo de 1000 caracteres</p>
                                    </div>
                                </div>
                                
                                <div class="flex justify-end space-x-2">
                                    <button type="button" onclick="fecharModalObservacao()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                        Cancelar
                                    </button>
                                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                        Salvar Observação
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Carrossel de Imagens das Movimentações -->
                    @php
                        $movimentacoesComAnexo = $movimentacoes->filter(function($mov) {
                            return !empty($mov->anexo);
                        });
                    @endphp

                    @if($movimentacoesComAnexo->count() > 0)
                    <div class="bg-gray-50 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Imagens das Movimentações</h3>

                        <div class="relative">
                            <!-- Carrossel de imagens -->
                            <div class="carousel-container overflow-hidden">
                                <div class="carousel-inner flex transition-transform duration-300 ease-in-out">
                                    @foreach($movimentacoesComAnexo as $index => $movimentacao)
                                        <div class="carousel-item min-w-full flex flex-col items-center justify-center" data-index="{{ $index }}">
                                            <div class="relative w-full max-w-2xl">
                                                <img src="{{ $movimentacao->anexo_url }}" alt="Anexo da movimentação" class="w-full h-auto max-h-96 object-contain rounded-lg shadow-md">
                                                <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-60 text-white p-2 rounded-b-lg">
                                                    <p class="text-sm">{{ $movimentacao->localizacao ? $movimentacao->localizacao->nome_localizacao : 'N/A' }} -
                                                    {{ $movimentacao->tipo ? $movimentacao->tipo->descricao : 'N/A' }} -
                                                    {{ $movimentacao->data_entrada ? $movimentacao->data_entrada->format('d/m/Y') : 'N/A' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Controles do carrossel -->
                            @if($movimentacoesComAnexo->count() > 1)
                                <button class="carousel-control prev absolute top-1/2 left-2 transform -translate-y-1/2 bg-white bg-opacity-50 hover:bg-opacity-75 rounded-full p-2 focus:outline-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <button class="carousel-control next absolute top-1/2 right-2 transform -translate-y-1/2 bg-white bg-opacity-50 hover:bg-opacity-75 rounded-full p-2 focus:outline-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            @endif

                            <!-- Indicadores do carrossel -->
                            @if($movimentacoesComAnexo->count() > 1)
                                <div class="carousel-indicators flex justify-center mt-4">
                                    @foreach($movimentacoesComAnexo as $index => $movimentacao)
                                        <button class="carousel-indicator w-3 h-3 rounded-full mx-1 {{ $index === 0 ? 'bg-blue-600' : 'bg-gray-300' }}" data-index="{{ $index }}"></button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Observações -->
                    <div class="bg-gray-50 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Observações</h3>
                            @if(auth()->user()->canUpdate('produtos'))
                                <button type="button" onclick="abrirModalObservacao()" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-700 focus:outline-none focus:border-purple-700 focus:ring focus:ring-purple-300 disabled:opacity-25 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                    Nova Observação
                                </button>
                            @endif
                        </div>

                        @php
                            // Garantir que $observacoes existe
                            if (!isset($observacoes)) {
                                $observacoes = $produto->observacoes ?? collect();
                            }
                        @endphp

                        @if($observacoes && $observacoes->count() > 0)
                            <div class="space-y-3">
                                @foreach($observacoes as $obs)
                                    <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <div class="text-sm text-gray-700 prose prose-sm max-w-none">{!! $obs->observacao !!}</div>
                                                <div class="mt-2 flex items-center text-xs text-gray-500">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                                    </svg>
                                                    <span class="font-medium">{{ $obs->usuario ? $obs->usuario->name : 'Sistema' }}</span>
                                                    <span class="mx-2">•</span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                                    </svg>
                                                    <span>{{ $obs->created_at->format('d/m/Y H:i') }}</span>
                                                </div>
                                            </div>
                                            @if(auth()->user()->canUpdate('produtos'))
                                                <button onclick="removerObservacao({{ $obs->id }})" class="ml-4 text-red-600 hover:text-red-800" title="Remover observação">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-400 italic">Nenhuma observação registrada para este produto</p>
                        @endif
                    </div>

                    <!-- Movimentações -->
                    <div class="bg-gray-50 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        @php
                        // Função para calcular dias úteis entre duas datas (excluindo sábados e domingos)
                        function calcularDiasUteis($dataInicio, $dataFim) {
                            $diasUteis = 0;
                            $dataAtual = clone $dataInicio;

                            while ($dataAtual <= $dataFim) {
                                // 6 = sábado, 0 = domingo
                                $diaDaSemana = $dataAtual->dayOfWeek;
                                if ($diaDaSemana != 0 && $diaDaSemana != 6) {
                                    $diasUteis++;
                                }
                                $dataAtual->addDay();
                            }

                            return $diasUteis;
                        }
                        @endphp

                        <!-- Seção de Reprogramações -->
                        @if($produto->isReprogramacao() || $produto->reprogramacoes()->count() > 0)
                            <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
                                @if($produto->isReprogramacao())
                                    <!-- Este produto É uma reprogramação -->
                                    <div class="flex items-center mb-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                                        </svg>
                                        <h3 class="text-lg font-semibold text-gray-800">Produto Original</h3>
                                    </div>
                                    <div class="bg-orange-50 border-l-4 border-orange-400 p-4 rounded">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-orange-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="ml-3 flex-1">
                                                <p class="text-sm text-orange-700">
                                                    Este produto é a <strong>reprogramação #{{ str_pad($produto->numero_reprogramacao, 2, '0', STR_PAD_LEFT) }}</strong> de:
                                                </p>
                                                <div class="mt-2">
                                                    <a href="{{ route('produtos.show', $produto->produtoOriginal->id) }}" 
                                                       class="inline-flex items-center px-4 py-2 bg-white border border-orange-300 rounded-md font-semibold text-sm text-orange-700 hover:bg-orange-50 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                                        </svg>
                                                        {{ $produto->produtoOriginal->referencia }} - {{ $produto->produtoOriginal->descricao }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <!-- Este produto TEM reprogramações -->
                                    <div class="flex items-center mb-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                                        </svg>
                                        <h3 class="text-lg font-semibold text-gray-800">Reprogramações deste Produto</h3>
                                        <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $produto->reprogramacoes()->count() }}
                                        </span>
                                    </div>
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                        <div class="space-y-3">
                                            @foreach($produto->reprogramacoes as $reprogramacao)
                                                <div class="flex items-center justify-between bg-white p-3 rounded-lg border border-blue-200 hover:shadow-md transition">
                                                    <div class="flex items-center space-x-3">
                                                        <div class="flex-shrink-0">
                                                            <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 text-blue-600 font-bold">
                                                                #{{ str_pad($reprogramacao->numero_reprogramacao, 2, '0', STR_PAD_LEFT) }}
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <p class="text-sm font-semibold text-gray-900">{{ $reprogramacao->referencia }}</p>
                                                            <p class="text-xs text-gray-500">
                                                                Criado em {{ $reprogramacao->data_cadastro->format('d/m/Y') }}
                                                                @if($reprogramacao->status)
                                                                    • <span class="px-2 py-0.5 rounded-full text-xs {{ $reprogramacao->status->descricao == 'Ativo' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                                        {{ $reprogramacao->status->descricao }}
                                                                    </span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <a href="{{ route('produtos.show', $reprogramacao->id) }}" 
                                                           class="inline-flex items-center px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded-md transition">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                                            </svg>
                                                            Ver Detalhes
                                                        </a>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Movimentações</h3>
                            <a href="{{ route('movimentacoes.create', ['produto_id' => $produto->id]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Nova Movimentação
                            </a>
                        </div>

                        @if($movimentacoes->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Localização
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Tipo
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Situação
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Data Entrada
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Data Conclusão
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Dias
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Comprometido
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Obs
                                            </th>
                                            <th scope="col" class="px-6 py-3"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($movimentacoes as $movimentacao)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $movimentacao->localizacao ? $movimentacao->localizacao->nome_localizacao : 'N/A' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <span class="px-2 py-1 rounded-full text-xs {{ $movimentacao->tipo && $movimentacao->tipo->descricao == 'Entrada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                        {{ $movimentacao->tipo ? $movimentacao->tipo->descricao : 'N/A' }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    @if($movimentacao->situacao)
                                                        <span class="px-2 py-1 rounded-full text-xs {{ $movimentacao->situacao->cor ? 'bg-'.$movimentacao->situacao->cor.'-100 text-'.$movimentacao->situacao->cor.'-800' : 'bg-gray-100 text-gray-800' }}">
                                                            {{ $movimentacao->situacao->descricao ?? 'N/A' }}
                                                        </span>
                                                    @else
                                                        <span class="text-gray-400">N/A</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $movimentacao->data_entrada ? $movimentacao->data_entrada->format('d/m/Y') : 'N/A' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $movimentacao->data_saida ? $movimentacao->data_saida->format('d/m/Y') : 'N/A' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    @php
                                                        $diasEntre = null;
                                                        $prazoExcedido = false;
                                                        $prazoSetor = null;

                                                        // Calcular dias úteis entre DATA ENTRADA e DATA SAIDA (ou data atual se não houver saída)
                                                        if ($movimentacao->data_entrada) {
                                                            if ($movimentacao->data_saida) {
                                                                // Se tem data de saída, calcular dias úteis entre entrada e saída
                                                                $diasEntre = calcularDiasUteis($movimentacao->data_entrada, $movimentacao->data_saida);
                                                            } else {
                                                                // Se não tem data de saída, calcular dias úteis entre entrada e data atual
                                                                $diasEntre = calcularDiasUteis($movimentacao->data_entrada, now());
                                                            }

                                                            // Verificar se excede o prazo do setor
                                                            if ($movimentacao->localizacao && $movimentacao->localizacao->prazo) {
                                                                $prazoExcedido = $diasEntre > $movimentacao->localizacao->prazo;
                                                                $prazoSetor = $movimentacao->localizacao->prazo;
                                                            }
                                                        }
                                                    @endphp

                                                    @if($diasEntre !== null)
                                                        <div class="text-center">
                                                            <span class="px-2 py-1 inline-block text-xs {{ $prazoExcedido ? 'bg-red-100 text-red-800 font-bold' : 'bg-blue-100 text-blue-800' }} rounded-full">
                                                                {{ $diasEntre }} {{ $diasEntre == 1 ? 'dia' : 'dias' }}
                                                            </span>
                                                            @if($prazoExcedido && isset($prazoSetor))
                                                                <div class="text-xs mt-1 text-red-600 font-medium">
                                                                    (Prazo: {{ $prazoSetor }} {{ $prazoSetor == 1 ? 'dia' : 'dias' }})
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <div class="text-center">
                                                            <span class="text-gray-400">-</span>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <span class="px-2 py-1 rounded-full text-xs {{ $movimentacao->comprometido ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                                        {{ $movimentacao->comprometido ? 'Sim' : 'Não' }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500">
                                                    @if($movimentacao->observacao)
                                                        <div class="tooltip-container flex items-center justify-center" style="position: static;">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 hover:text-blue-700 cursor-help tooltip-trigger" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
                                                            </svg>
                                                            <div class="tooltip-content fixed z-[9999] w-64 p-2 bg-black text-xs rounded-lg hidden" style="color: white !important; box-shadow: 0 2px 8px rgba(0,0,0,0.25);">
                                                                {{ $movimentacao->observacao }}
                                                                <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-black rotate-45"></div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <a href="{{ route('movimentacoes.show', ['movimentacao' => $movimentacao->id, 'back_url' => route('produtos.show', $produto->id)]) }}" class="text-blue-600 hover:text-blue-900" title="Visualizar movimentação">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                                        </svg>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-gray-500 italic">
                                Nenhuma movimentação encontrada para este produto.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Reprogramação -->
    <div id="modal-reprogramar" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="relative p-4 border shadow-lg rounded-md bg-white" style="width: 400px; max-width: 90vw;">
            <div class="mt-1">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-semibold text-gray-900">Reprogramar Produto</h3>
                    <button onclick="document.getElementById('modal-reprogramar').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="mt-2 px-2 py-2">
                    <div class="bg-orange-50 border-l-4 border-orange-400 p-2 mb-3">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-orange-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-orange-700">
                                    <strong>Atenção!</strong> Será criado um novo produto baseado em:
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-2 space-y-1">
                        <div class="flex justify-between text-xs">
                            <span class="font-medium text-gray-700">Original:</span>
                            <span class="text-gray-900 font-semibold">{{ $produto->referencia }}</span>
                        </div>
                        @php
                            $ultimaReprogramacao = $produto->reprogramacoes()->max('numero_reprogramacao') ?? 0;
                            $proximoNumero = $ultimaReprogramacao + 1;
                            $novaReferencia = $produto->referencia . '-' . str_pad($proximoNumero, 2, '0', STR_PAD_LEFT);
                        @endphp
                        <div class="flex justify-between text-xs">
                            <span class="font-medium text-gray-700">Nova (sugerida):</span>
                            <span class="text-green-600 font-bold" id="preview-referencia">{{ $novaReferencia }}</span>
                        </div>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded p-2 mb-3">
                        <label for="numero_reprogramacao_manual" class="block text-xs font-semibold text-yellow-800 mb-1">
                            Número de Reprogramação (opcional)
                        </label>
                        <input 
                            type="number" 
                            id="numero_reprogramacao_manual" 
                            name="numero_reprogramacao_manual" 
                            min="1" 
                            max="99" 
                            placeholder="{{ $proximoNumero }}"
                            class="w-full text-xs border-yellow-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm"
                            onkeyup="atualizarPreviewReferencia('{{ $produto->referencia }}', {{ $proximoNumero }})"
                        >
                        <p class="text-xs text-yellow-700 mt-1">
                            Deixe em branco para usar o número sugerido ({{ $proximoNumero }}). Use este campo apenas para reprogramações iniciadas em sistemas antigos.
                        </p>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded p-2 mb-2">
                        <p class="text-xs text-blue-800 mb-1 font-semibold">✔️ Será copiado:</p>
                        <ul class="text-xs text-blue-700 space-y-0.5 ml-3">
                            <li>• Dados, tecidos, observações, anexos, cores</li>
                        </ul>
                    </div>

                    <div class="bg-red-50 border border-red-200 rounded p-2 mb-2">
                        <p class="text-xs text-red-800 mb-1 font-semibold">❌ NÃO será copiado:</p>
                        <ul class="text-xs text-red-700 space-y-0.5 ml-3">
                            <li>• Localizações e movimentações</li>
                        </ul>
                    </div>

                    <p class="text-xs text-gray-600 mb-2">
                        <strong>Obs:</strong> Produto reprogramado <strong class="text-red-600">não</strong> pode ser reprogramado novamente.
                    </p>
                </div>

                <div class="flex items-center justify-end gap-2 px-2 py-2 bg-gray-50 rounded-b">
                    <button onclick="document.getElementById('modal-reprogramar').classList.add('hidden')" class="px-3 py-1.5 bg-gray-200 text-gray-700 text-xs font-medium rounded hover:bg-gray-300">
                        Cancelar
                    </button>
                    <form id="form-reprogramar" action="{{ route('produtos.reprogramar', $produto->id) }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="numero_reprogramacao" id="numero_reprogramacao_hidden">
                        <button type="submit" onclick="capturarNumeroReprogramacao()" class="px-3 py-1.5 bg-orange-500 text-white text-xs font-medium rounded hover:bg-orange-600">
                            Confirmar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para o carrossel -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const carouselInner = document.querySelector('.carousel-inner');
            const carouselItems = document.querySelectorAll('.carousel-item');
            const prevButton = document.querySelector('.carousel-control.prev');
            const nextButton = document.querySelector('.carousel-control.next');
            const indicators = document.querySelectorAll('.carousel-indicator');

            if (!carouselInner || carouselItems.length === 0) return;

            let currentIndex = 0;
            const itemCount = carouselItems.length;

            // Função para atualizar o carrossel
            function updateCarousel() {
                const translateValue = -currentIndex * 100 + '%';
                carouselInner.style.transform = 'translateX(' + translateValue + ')';

                // Atualizar indicadores
                indicators.forEach((indicator, index) => {
                    if (index === currentIndex) {
                        indicator.classList.remove('bg-gray-300');
                        indicator.classList.add('bg-blue-600');
                    } else {
                        indicator.classList.remove('bg-blue-600');
                        indicator.classList.add('bg-gray-300');
                    }
                });
            }

            // Eventos para os botões de navegação
            if (prevButton) {
                prevButton.addEventListener('click', function() {
                    currentIndex = (currentIndex - 1 + itemCount) % itemCount;
                    updateCarousel();
                });
            }

            if (nextButton) {
                nextButton.addEventListener('click', function() {
                    currentIndex = (currentIndex + 1) % itemCount;
                    updateCarousel();
                });
            }

            // Eventos para os indicadores
            indicators.forEach((indicator, index) => {
                indicator.addEventListener('click', function() {
                    currentIndex = index;
                    updateCarousel();
                });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tooltip functionality
            const tooltipTriggers = document.querySelectorAll('.tooltip-trigger');

            tooltipTriggers.forEach(trigger => {
                trigger.addEventListener('mouseenter', function(e) {
                    const tooltipContent = this.closest('.tooltip-container').querySelector('.tooltip-content');

                    // Get position of the trigger
                    const triggerRect = this.getBoundingClientRect();

                    // Position the tooltip above the trigger with some offset
                    tooltipContent.style.position = 'fixed';
                    tooltipContent.style.zIndex = '9999';
                    tooltipContent.style.top = (triggerRect.top - 15) + 'px';
                    tooltipContent.style.left = (triggerRect.left + (triggerRect.width / 2)) + 'px';
                    tooltipContent.style.transform = 'translate(-50%, -100%)';

                    // Ensure tooltip is visible and above all other elements
                    tooltipContent.classList.remove('hidden');

                    // Prevent tooltip from being cut off at the top of the screen
                    const tooltipRect = tooltipContent.getBoundingClientRect();
                    if (tooltipRect.top < 10) {
                        // If too close to the top, position below the trigger instead
                        tooltipContent.style.top = (triggerRect.bottom + 15) + 'px';
                        tooltipContent.style.transform = 'translate(-50%, 0)';

                        // Move the arrow to the top
                        const arrow = tooltipContent.querySelector('div[class*="absolute"]');
                        if (arrow) {
                            arrow.style.top = '-5px';
                            arrow.style.bottom = 'auto';
                        }
                    }
                });

                trigger.addEventListener('mouseleave', function() {
                    const tooltipContent = this.closest('.tooltip-container').querySelector('.tooltip-content');
                    tooltipContent.classList.add('hidden');
                });
            });
        });
    </script>

    <script>
        // Variável global para o editor Quill
        let quillEditor = null;

        // Funções para o modal de observações
        function abrirModalObservacao() {
            document.getElementById('modal-nova-observacao').classList.remove('hidden');
            
            // Inicializar Quill apenas uma vez
            if (!quillEditor) {
                quillEditor = new Quill('#editor-container', {
                    theme: 'snow',
                    placeholder: 'Digite sua observação aqui...',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline'],
                            [{ 'color': ['#DC2626', '#2563EB', '#16A34A', '#CA8A04', '#EA580C', '#9333EA', '#DB2777', '#000000'] }],
                            [{ 'background': ['#FEE2E2', '#DBEAFE', '#D1FAE5', '#FEF3C7', '#FFEDD5', '#F3E8FF', '#FCE7F3', '#FFFFFF'] }],
                            ['clean']
                        ]
                    }
                });

                // Sincronizar conteúdo do editor com o textarea oculto
                quillEditor.on('text-change', function() {
                    const html = quillEditor.root.innerHTML;
                    document.getElementById('observacao').value = html;
                });
            } else {
                // Limpar o editor se já existe
                quillEditor.setText('');
            }
        }

        function fecharModalObservacao() {
            document.getElementById('modal-nova-observacao').classList.add('hidden');
            if (quillEditor) {
                quillEditor.setText('');
            }
            document.getElementById('form-observacao').reset();
        }

        // Fechar modal ao clicar fora ou pressionar ESC
        document.getElementById('modal-nova-observacao')?.addEventListener('click', function(e) {
            if (e.target === this) {
                fecharModalObservacao();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('modal-nova-observacao');
                if (modal && !modal.classList.contains('hidden')) {
                    fecharModalObservacao();
                }
            }
        });

        // Salvar observação
        async function salvarObservacao(event) {
            event.preventDefault();
            
            console.log('Função salvarObservacao chamada');
            
            // Sincronizar conteúdo do Quill com o textarea antes de enviar
            if (quillEditor) {
                const html = quillEditor.root.innerHTML;
                document.getElementById('observacao').value = html;
                console.log('HTML do Quill:', html);
            }
            
            const form = event.target;
            const submitButton = form.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            
            // Validar se há conteúdo
            const observacaoValue = document.getElementById('observacao').value;
            const textoLimpo = quillEditor ? quillEditor.getText().trim() : observacaoValue.trim();
            
            console.log('Valor do textarea:', observacaoValue);
            console.log('Texto limpo:', textoLimpo);
            console.log('Produto ID:', document.querySelector('input[name="produto_id"]').value);
            
            // Criar FormData manualmente para garantir que os dados estão corretos
            const formData = new FormData();
            formData.append('produto_id', document.querySelector('input[name="produto_id"]').value);
            formData.append('observacao', observacaoValue);
            formData.append('_token', '{{ csrf_token() }}');
            
            if (!textoLimpo || textoLimpo === '') {
                alert('Por favor, digite uma observação.');
                return;
            }
            
            submitButton.disabled = true;
            submitButton.textContent = 'Salvando...';

            try {
                const response = await fetch('{{ route("produtos.observacoes.store") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                console.log('Resposta do servidor:', data);

                if (response.ok && data.success) {
                    // Mostrar mensagem de sucesso
                    alert('Observação adicionada com sucesso!');
                    
                    // Recarregar a página para mostrar a nova observação
                    window.location.reload();
                } else {
                    // Mostrar erro de validação ou outro erro
                    let errorMessage = 'Erro ao salvar observação.';
                    
                    if (data.message) {
                        errorMessage = data.message;
                    } else if (data.errors) {
                        // Erros de validação do Laravel
                        const errors = Object.values(data.errors).flat();
                        errorMessage = errors.join('\n');
                    }
                    
                    alert(errorMessage);
                    submitButton.disabled = false;
                    submitButton.textContent = originalText;
                }
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro ao salvar observação. Por favor, tente novamente.');
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            }
        }

        // Remover observação
        async function removerObservacao(id) {
            if (!confirm('Tem certeza que deseja remover esta observação?')) {
                return;
            }

            try {
                const response = await fetch(`/produtos/observacoes/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    alert('Observação removida com sucesso!');
                    window.location.reload();
                } else {
                    alert('Erro ao remover observação: ' + (data.message || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro ao remover observação. Por favor, tente novamente.');
            }
        }
    </script>

    <script>
        // Função para atualizar o preview da referência ao digitar número de reprogramação
        function atualizarPreviewReferencia(referenciaBase, numeroSugerido) {
            const inputNumero = document.getElementById('numero_reprogramacao_manual');
            const previewElement = document.getElementById('preview-referencia');
            
            let numero = parseInt(inputNumero.value);
            
            // Se não digitou nada ou número inválido, usar o sugerido
            if (!numero || numero < 1 || numero > 99) {
                numero = numeroSugerido;
            }
            
            const numeroFormatado = numero.toString().padStart(2, '0');
            const novaReferencia = referenciaBase + '-' + numeroFormatado;
            
            previewElement.textContent = novaReferencia;
        }

        // Função para capturar o número antes de enviar o formulário
        function capturarNumeroReprogramacao() {
            const inputNumero = document.getElementById('numero_reprogramacao_manual');
            const hiddenInput = document.getElementById('numero_reprogramacao_hidden');
            
            // Se o usuário digitou um número, usar esse valor
            if (inputNumero.value && parseInt(inputNumero.value) > 0) {
                hiddenInput.value = inputNumero.value;
            }
            // Caso contrário, o campo oculto fica vazio e o backend usa o cálculo automático
        }
    </script>

    @push('styles')
    <!-- Quill.js CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    @endpush

    @push('scripts')
    <!-- Quill.js JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    @endpush
</x-app-layout>
