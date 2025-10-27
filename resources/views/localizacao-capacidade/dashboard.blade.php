<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard de Capacidade das Localizações') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Botões de ação -->
            <div class="flex flex-wrap gap-2 mb-4">
                <a href="{{ route('localizacao-capacidade.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nova Capacidade
                </a>

                <button onclick="openGerarCapacidadesModal()" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    Gerar Capacidades do Mês
                </button>

                <a href="{{ route('localizacao-capacidade.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    Ver Listagem
                </a>

                <a href="{{ route('localizacao-capacidade.relatorio-pdf', ['mes' => $mes, 'ano' => $ano, 'localizacao_id' => $localizacaoId ?? '']) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Gerar PDF
                </a>

                <button onclick="abrirHistoricoRedistribuicoes()" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Histórico de Redistribuições
                </button>
            </div>

            <!-- Filtros -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-6">
                    <form action="{{ route('localizacao-capacidade.dashboard') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="localizacao_id" class="block text-sm font-medium text-gray-700 mb-1">Localização</label>
                            <select name="localizacao_id" id="localizacao_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Todas as Localizações</option>
                                @foreach($localizacoes as $localizacao)
                                    <option value="{{ $localizacao->id }}" {{ ($localizacaoId ?? '') == $localizacao->id ? 'selected' : '' }}>
                                        {{ $localizacao->nome_localizacao }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="mes" class="block text-sm font-medium text-gray-700 mb-1">Mês</label>
                            <select name="mes" id="mes" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @php
                                    $meses = ['', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
                                @endphp
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ $mes == $m ? 'selected' : '' }}>
                                        {{ $meses[$m] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="ano" class="block text-sm font-medium text-gray-700 mb-1">Ano</label>
                            <select name="ano" id="ano" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @foreach(range(now()->year - 1, now()->year + 2) as $a)
                                    <option value="{{ $a }}" {{ $ano == $a ? 'selected' : '' }}>
                                        {{ $a }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Atualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Resumo Geral -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                @php
                    $totalCapacidade = $dadosDashboard->sum('capacidade');
                    $totalPrevistos = $dadosDashboard->sum('produtos_previstos');
                    $totalSaldo = $totalCapacidade - $totalPrevistos;
                    $totalPercentual = $totalCapacidade > 0 ? round(($totalPrevistos / $totalCapacidade) * 100, 1) : 0;
                @endphp

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-500">Capacidade Total</p>
                                <p class="mt-1 text-3xl font-semibold text-blue-600">{{ $totalCapacidade }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-600 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-500">Produtos Previstos</p>
                                <p class="mt-1 text-3xl font-semibold {{ $totalPrevistos > $totalCapacidade ? 'text-red-600' : 'text-green-600' }}">{{ $totalPrevistos }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 {{ $totalPrevistos > $totalCapacidade ? 'text-red-600' : 'text-green-600' }} opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-500">Saldo Disponível</p>
                                <p class="mt-1 text-3xl font-semibold {{ $totalSaldo < 0 ? 'text-red-600' : 'text-green-600' }}">{{ $totalSaldo }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 {{ $totalSaldo < 0 ? 'text-red-600' : 'text-green-600' }} opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-500">Taxa de Ocupação</p>
                                <p class="mt-1 text-3xl font-semibold {{ $totalPercentual > 100 ? 'text-red-600' : ($totalPercentual > 80 ? 'text-yellow-600' : 'text-green-600') }}">{{ $totalPercentual }}%</p>
                            </div>
                            <div class="flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 {{ $totalPercentual > 100 ? 'text-red-600' : ($totalPercentual > 80 ? 'text-yellow-600' : 'text-green-600') }} opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalhes por Localização -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Capacidade por Localização</h3>

                    @if($dadosDashboard->count() > 0)
                        <div class="space-y-4">
                            @foreach($dadosDashboard as $dado)
                                <div class="border border-gray-200 rounded-lg p-4 {{ $dado['acima_capacidade'] ? 'bg-red-50 border-red-200' : 'bg-white' }}">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-lg font-semibold text-gray-800">
                                            {{ $dado['localizacao']->nome_localizacao }}
                                        </h4>
                                        <div class="flex items-center gap-2">
                                            @if($dado['acima_capacidade'])
                                                <!-- <button
                                                    onclick="abrirModalRedistribuicao({{ $dado['localizacao']->id }}, {{ $mes}}, {{ $ano }})"
                                                    class="px-3 py-1 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-md transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                                    </svg>
                                                    Redistribuir
                                                </button> -->
                                                <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-bold rounded-full">
                                                    ACIMA DA CAPACIDADE
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-3">
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase">Capacidade</p>
                                            <p class="text-2xl font-bold text-blue-600">{{ $dado['capacidade'] }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase">Previstos</p>
                                            <p class="text-2xl font-bold {{ $dado['acima_capacidade'] ? 'text-red-600' : 'text-green-600' }}">
                                                {{ $dado['produtos_previstos'] }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase">Saldo</p>
                                            <p class="text-2xl font-bold {{ $dado['saldo'] < 0 ? 'text-red-600' : 'text-green-600' }}">
                                                {{ $dado['saldo'] }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase mb-1">Ocupação</p>
                                            <div class="flex items-center">
                                                <div class="flex-1 bg-gray-200 rounded-full h-4 mr-2">
                                                    <div class="h-4 rounded-full {{ $dado['percentual'] > 100 ? 'bg-red-600' : ($dado['percentual'] > 80 ? 'bg-yellow-600' : 'bg-green-600') }}" style="width: {{ min($dado['percentual'], 100) }}%"></div>
                                                </div>
                                                <span class="text-sm font-bold {{ $dado['percentual'] > 100 ? 'text-red-600' : 'text-gray-700' }}">
                                                    {{ $dado['percentual'] }}%
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Barra de Comparação Visual -->
                                    <div class="mt-4">
                                        <div class="flex items-center space-x-2">
                                            <div class="flex-1 h-8 bg-gray-100 rounded-lg relative overflow-hidden">
                                                <div class="absolute top-0 left-0 h-full bg-blue-200" style="width: 100%"></div>
                                                <div class="absolute top-0 left-0 h-full {{ $dado['acima_capacidade'] ? 'bg-red-500' : 'bg-green-500' }}" style="width: {{ min($dado['percentual'], 100) }}%"></div>
                                                <div class="absolute inset-0 flex items-center justify-center text-xs font-semibold text-gray-700">
                                                    {{ $dado['produtos_previstos'] }} / {{ $dado['capacidade'] }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                                            <span>0</span>
                                            <span>Capacidade: {{ $dado['capacidade'] }}</span>
                                        </div>
                                    </div>

                                    <!-- Lista de Produtos Previstos -->
                                    @if($dado['produtos']->count() > 0)
                                        <div class="mt-4 border-t pt-4" x-data="{ open: false }">
                                            <button @click="open = !open" class="w-full flex items-center justify-between text-left p-2 hover:bg-gray-50 rounded-lg transition-colors">
                                                <div class="flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                                    </svg>
                                                    <span class="font-semibold text-gray-700">Produtos Previstos ({{ $dado['produtos']->count() }})</span>
                                                </div>
                                                <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                                <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                                </svg>
                                            </button>

                                            <div x-show="open" x-transition class="mt-3" style="display: none;">
                                                <div class="overflow-x-auto">
                                                    <table class="min-w-full divide-y divide-gray-200">
                                                        <thead class="bg-gray-50">
                                                            <tr>
                                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase" style="width: 8%;">Referência</th>
                                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase" style="width: 18%;">Descrição</th>
                                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase" style="width: 12%;">Marca</th>
                                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase" style="width: 12%;">Grupo</th>
                                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase" style="width: 36%;">Observações</th>
                                                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase" title="Quantidade total do produto" style="width: 8%;">Qtd Total</th>
                                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase" style="width: 10%;">Data Prevista</th>
                                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase" style="width: 6%;">Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="bg-white divide-y divide-gray-200">
                                                            @php
                                                                // Agrupar produtos por referência + descrição + marca + grupo + qtd total + data + status
                                                                $produtosAgrupados = $dado['produtos']->groupBy(function($produto) {
                                                                    $primeiraData = $produto->localizacoes()
                                                                        ->whereNotNull('data_prevista_faccao')
                                                                        ->orderBy('data_prevista_faccao', 'asc')
                                                                        ->first();
                                                                    
                                                                    $dataFormatada = 'N/A';
                                                                    if ($primeiraData && $primeiraData->pivot->data_prevista_faccao) {
                                                                        $dataFormatada = is_string($primeiraData->pivot->data_prevista_faccao) 
                                                                            ? \Carbon\Carbon::parse($primeiraData->pivot->data_prevista_faccao)->format('Y-m-d')
                                                                            : $primeiraData->pivot->data_prevista_faccao->format('Y-m-d');
                                                                    }
                                                                    
                                                                    return $produto->id . '|' . 
                                                                           $produto->referencia . '|' . 
                                                                           $produto->descricao . '|' . 
                                                                           ($produto->marca ? $produto->marca->id : 'sem_marca') . '|' . 
                                                                           ($produto->grupoProduto ? $produto->grupoProduto->id : 'sem_grupo') . '|' . 
                                                                           $produto->quantidade . '|' . 
                                                                           $dataFormatada . '|' . 
                                                                           ($produto->status ? $produto->status->id : 'sem_status');
                                                                });
                                                            @endphp
                                                            
                                                            @foreach($produtosAgrupados as $chave => $produtosGrupo)
                                                                @php
                                                                    $produtoPrincipal = $produtosGrupo->first();
                                                                @endphp
                                                                <tr class="hover:bg-gray-50">
                                                                    <td class="px-3 py-2 text-sm font-medium text-gray-900">
                                                                        <a href="{{ route('produtos.show', $produtoPrincipal->id) }}" class="text-blue-600 hover:text-blue-900 hover:underline" target="_blank">
                                                                            {{ $produtoPrincipal->referencia }}
                                                                        </a>
                                                                    </td>
                                                                    <td class="px-3 py-2 text-sm text-gray-700">{{ $produtoPrincipal->descricao }}</td>
                                                                    <td class="px-3 py-2 text-sm">
                                                                        @if($produtoPrincipal->marca)
                                                                            @if($produtoPrincipal->marca->cor_fundo && $produtoPrincipal->marca->cor_fonte)
                                                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full" style="background-color: {{ $produtoPrincipal->marca->cor_fundo }}; color: {{ $produtoPrincipal->marca->cor_fonte }};">
                                                                                    {{ $produtoPrincipal->marca->nome_marca }}
                                                                                </span>
                                                                            @else
                                                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                                                                    {{ $produtoPrincipal->marca->nome_marca }}
                                                                                </span>
                                                                            @endif
                                                                        @else
                                                                            <span class="text-gray-400 italic">N/A</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="px-3 py-2 text-sm text-gray-600">{{ $produtoPrincipal->grupoProduto->descricao ?? 'N/A' }}</td>
                                                                    <td class="px-3 py-2 text-sm text-gray-600">
                                                                        @php
                                                                            // Carregar observações do produto (apenas uma vez)
                                                                            $obs = \App\Models\ProdutoObservacao::where('produto_id', $produtoPrincipal->id)->get();

                                                                            // Carregar todas as observações das localizações de todas as alocações
                                                                            $todasObsLocalizacoes = collect();
                                                                            foreach($produtosGrupo as $produto) {
                                                                                $obsLoc = $produto->localizacoes()
                                                                                    ->where(function($q) {
                                                                                        $q->whereNotNull('ordem_producao')
                                                                                          ->orWhereNotNull('produto_localizacao.observacao');
                                                                                    })
                                                                                    ->get();
                                                                                $todasObsLocalizacoes = $todasObsLocalizacoes->merge($obsLoc);
                                                                            }
                                                                            
                                                                            // Remover duplicatas baseado em ordem_producao + observacao
                                                                            $todasObsLocalizacoes = $todasObsLocalizacoes->unique(function($loc) {
                                                                                return $loc->pivot->ordem_producao . '|' . $loc->pivot->observacao;
                                                                            });

                                                                            $temObservacoes = $obs->count() > 0 || $todasObsLocalizacoes->count() > 0;
                                                                        @endphp
                                                                        
                                                                        {{-- Observações do Produto (apenas uma vez) --}}
                                                                        @if($obs->count() > 0)
                                                                            @foreach($obs as $observacao)
                                                                                @php
                                                                                    // Processar observações (suporta HTML do Quill e tags customizadas)
                                                                                    $obsTexto = $observacao->observacao;
                                                                                    
                                                                                    // Se não contém tags HTML do Quill, processar tags customizadas
                                                                                    if (strpos($obsTexto, '<p>') === false && strpos($obsTexto, '<span') === false) {
                                                                                        $obsTexto = preg_replace('/<red>(.*?)<\/red>/i', '<span style="color: #DC2626; font-weight: 600;">$1</span>', $obsTexto);
                                                                                        $obsTexto = preg_replace('/<blue>(.*?)<\/blue>/i', '<span style="color: #2563EB; font-weight: 600;">$1</span>', $obsTexto);
                                                                                        $obsTexto = preg_replace('/<green>(.*?)<\/green>/i', '<span style="color: #16A34A; font-weight: 600;">$1</span>', $obsTexto);
                                                                                        $obsTexto = preg_replace('/<yellow>(.*?)<\/yellow>/i', '<span style="color: #CA8A04; font-weight: 600;">$1</span>', $obsTexto);
                                                                                        $obsTexto = preg_replace('/<orange>(.*?)<\/orange>/i', '<span style="color: #EA580C; font-weight: 600;">$1</span>', $obsTexto);
                                                                                        $obsTexto = preg_replace('/<purple>(.*?)<\/purple>/i', '<span style="color: #9333EA; font-weight: 600;">$1</span>', $obsTexto);
                                                                                        $obsTexto = preg_replace('/<pink>(.*?)<\/pink>/i', '<span style="color: #DB2777; font-weight: 600;">$1</span>', $obsTexto);
                                                                                    }
                                                                                    
                                                                                    $obsTexto = Str::limit($obsTexto, 120);
                                                                                @endphp
                                                                                <div class="text-xs text-gray-700 mb-1">
                                                                                    {!! $obsTexto !!}
                                                                                </div>
                                                                            @endforeach
                                                                        @endif

                                                                        {{-- Observações das Localizações (Ordem de Produção) - sem duplicatas --}}
                                                                        @if($todasObsLocalizacoes->count() > 0)
                                                                            <table class="w-full border-collapse">
                                                                                @foreach($todasObsLocalizacoes as $loc)
                                                                                    @php
                                                                                        // Buscar a quantidade alocada para esta ordem de produção
                                                                                        $qtdAlocada = 0;
                                                                                        foreach($produtosGrupo as $produto) {
                                                                                            $localizacaoAtual = $produto->localizacoes()
                                                                                                ->where('ordem_producao', $loc->pivot->ordem_producao)
                                                                                                ->first();
                                                                                            if ($localizacaoAtual) {
                                                                                                $qtdAlocada = $localizacaoAtual->pivot->quantidade ?? 0;
                                                                                                break;
                                                                                            }
                                                                                        }
                                                                                    @endphp
                                                                                    <tr class="{{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                                                                                        <td class="py-1 pr-2 align-top" style="width: 70%;">
                                                                                            <div class="text-xs text-gray-700">
                                                                                                @if($loc->pivot->ordem_producao)
                                                                                                    <span class="font-semibold text-blue-700">OP: {{ $loc->pivot->ordem_producao }}</span>
                                                                                                @endif
                                                                                                @if($loc->pivot->ordem_producao && $loc->pivot->observacao)
                                                                                                    <span class="text-gray-500"> - </span>
                                                                                                @endif
                                                                                                @if($loc->pivot->observacao)
                                                                                                    @php
                                                                                                        // Processar tags de cor nas observações
                                                                                                        $obsTexto = $loc->pivot->observacao;
                                                                                                        $obsTexto = preg_replace('/<red>(.*?)<\/red>/i', '<span style="color: #DC2626; font-weight: 600;">$1</span>', $obsTexto);
                                                                                                        $obsTexto = preg_replace('/<blue>(.*?)<\/blue>/i', '<span style="color: #2563EB; font-weight: 600;">$1</span>', $obsTexto);
                                                                                                        $obsTexto = preg_replace('/<green>(.*?)<\/green>/i', '<span style="color: #16A34A; font-weight: 600;">$1</span>', $obsTexto);
                                                                                                        $obsTexto = preg_replace('/<yellow>(.*?)<\/yellow>/i', '<span style="color: #CA8A04; font-weight: 600;">$1</span>', $obsTexto);
                                                                                                        $obsTexto = preg_replace('/<orange>(.*?)<\/orange>/i', '<span style="color: #EA580C; font-weight: 600;">$1</span>', $obsTexto);
                                                                                                        $obsTexto = preg_replace('/<purple>(.*?)<\/purple>/i', '<span style="color: #9333EA; font-weight: 600;">$1</span>', $obsTexto);
                                                                                                        $obsTexto = preg_replace('/<pink>(.*?)<\/pink>/i', '<span style="color: #DB2777; font-weight: 600;">$1</span>', $obsTexto);
                                                                                                        $obsTexto = Str::limit($obsTexto, 80);
                                                                                                    @endphp
                                                                                                    <span class="text-gray-600">{!! $obsTexto !!}</span>
                                                                                                @endif
                                                                                            </div>
                                                                                        </td>
                                                                                        <td class="py-1 pl-2 text-right align-top" style="width: 30%;">
                                                                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-semibold bg-blue-100 text-blue-800">
                                                                                                {{ number_format($qtdAlocada, 0, ',', '.') }}
                                                                                            </span>
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforeach
                                                                            </table>
                                                                        @endif
                                                                        
                                                                        @if(!$temObservacoes)
                                                                            <div class="text-xs text-gray-400 italic">-</div>
                                                                        @endif
                                                                    </td>
                                                                    <td class="px-3 py-2 text-sm text-center font-semibold text-gray-900" title="Quantidade total do produto">
                                                                        {{ number_format($produtoPrincipal->quantidade ?? 0, 0, ',', '.') }}
                                                                    </td>
                                                                    <td class="px-3 py-2 text-sm text-gray-600">
                                                                        @php
                                                                            // Buscar primeira data prevista das localizações
                                                                            $primeiraData = $produtoPrincipal->localizacoes()
                                                                                ->whereNotNull('data_prevista_faccao')
                                                                                ->orderBy('data_prevista_faccao', 'asc')
                                                                                ->first();
                                                                        @endphp
                                                                        @if($primeiraData && $primeiraData->pivot->data_prevista_faccao)
                                                                            {{ is_string($primeiraData->pivot->data_prevista_faccao) ? \Carbon\Carbon::parse($primeiraData->pivot->data_prevista_faccao)->format('d/m/Y') : $primeiraData->pivot->data_prevista_faccao->format('d/m/Y') }}
                                                                            @if($produtoPrincipal->localizacoes()->whereNotNull('data_prevista_faccao')->count() > 1)
                                                                                <span class="text-xs text-gray-400">(+{{ $produtoPrincipal->localizacoes()->whereNotNull('data_prevista_faccao')->count() - 1 }})</span>
                                                                            @endif
                                                                        @else
                                                                            <span class="text-gray-400">N/A</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="px-3 py-2 text-sm">
                                                                        @if($produtoPrincipal->status)
                                                                            <span class="px-2 py-1 text-xs rounded-full {{ $produtoPrincipal->status->descricao == 'Ativo' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                                                {{ $produtoPrincipal->status->descricao }}
                                                                            </span>
                                                                        @else
                                                                            <span class="text-gray-400">N/A</span>
                                                                        @endif
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
                            @endforeach
                        </div>
                    @else
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-gray-600 font-medium text-lg mb-2">Nenhuma capacidade cadastrada para este período</p>
                            <p class="text-gray-500">Cadastre capacidades mensais para visualizar o dashboard.</p>
                            <p class="text-gray-500 text-sm mt-2">Use o botão "Nova Capacidade" no topo da página.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Redistribuição -->
    <div id="modalRedistribuicao" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Redistribuir Produtos Excedentes</h3>
                <button onclick="fecharModalRedistribuicao()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div id="conteudoModal" class="mt-4">
                <div class="text-center py-4">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
                    <p class="mt-2 text-gray-600">Carregando sugestões...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Gerar Capacidades -->
    <div id="modalGerarCapacidades" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Gerar Capacidades do Mês</h3>
                <button onclick="fecharModalGerarCapacidades()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="mt-4 space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm text-blue-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Esta ação criará registros de capacidade mensal para <strong>todas as localizações ativas</strong> que possuem capacidade maior que zero, usando o valor padrão cadastrado na localização.
                    </p>
                </div>

                <div>
                    <label for="mesGerar" class="block text-sm font-medium text-gray-700 mb-1">Mês</label>
                    <select id="mesGerar" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @php
                            $mesesNomes = ['', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
                            $mesAtual = date('n');
                        @endphp
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $m == $mesAtual ? 'selected' : '' }}>
                                {{ $mesesNomes[$m] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="anoGerar" class="block text-sm font-medium text-gray-700 mb-1">Ano</label>
                    <select id="anoGerar" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @php $anoAtual = date('Y'); @endphp
                        @foreach(range($anoAtual - 1, $anoAtual + 2) as $a)
                            <option value="{{ $a }}" {{ $a == $anoAtual ? 'selected' : '' }}>
                                {{ $a }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <p class="text-xs text-yellow-800">
                        <strong>Atenção:</strong> Registros já existentes para o mesmo mês/ano não serão duplicados.
                    </p>
                </div>

                <div class="flex justify-end space-x-2 pt-4 border-t">
                    <button onclick="fecharModalGerarCapacidades()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                        Cancelar
                    </button>
                    <button id="btnGerarCapacidades" onclick="gerarCapacidades()" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Gerar Capacidades
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function abrirModalRedistribuicao(localizacaoId, mes, ano) {
            document.getElementById('modalRedistribuicao').classList.remove('hidden');

            // Buscar sugestões
            fetch('{{ route('localizacao-capacidade.sugerir-redistribuicao') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    localizacao_id: localizacaoId,
                    mes: mes,
                    ano: ano
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                console.log('Dados recebidos:', data); // Debug

                if (data.message) {
                    document.getElementById('conteudoModal').innerHTML = `
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="mt-2 text-gray-600">${data.message}</p>
                        </div>
                    `;
                    return;
                }

                if (data.error) {
                    document.getElementById('conteudoModal').innerHTML = `
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <h3 class="mt-4 text-lg font-semibold text-gray-900">Erro</h3>
                            <p class="mt-2 text-gray-600">${data.error}</p>
                            <button onclick="fecharModalRedistribuicao()" class="mt-4 px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                                Fechar
                            </button>
                        </div>
                    `;
                    return;
                }

                mostrarSugestoesRedistribuicao(data);
            })
            .catch(error => {
                let mensagem = 'Erro ao carregar sugestões';
                if (error.message) {
                    mensagem = error.message;
                }

                document.getElementById('conteudoModal').innerHTML = `
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <h3 class="mt-4 text-lg font-semibold text-gray-900">Redistribuição Automática Indisponível</h3>
                        <p class="mt-2 text-gray-600">${mensagem}</p>
                        ${error.excedente ? `
                            <div class="mt-4 p-3 bg-gray-100 rounded-lg text-sm text-left">
                                <p><strong>Excedente:</strong> ${error.excedente} produtos</p>
                                <p><strong>Menor produto disponível:</strong> ${error.menor_produto} produtos</p>
                            </div>
                        ` : ''}
                        <p class="mt-4 text-sm text-gray-500">
                            Você precisará ajustar manualmente as datas dos produtos para resolver o excedente.
                        </p>
                        <button onclick="fecharModalRedistribuicao()" class="mt-4 px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                            Fechar
                        </button>
                    </div>
                `;
            });
        }

        function mostrarSugestoesRedistribuicao(data) {
            const meses = ['', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                          'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];

            // Verificar se há alocações
            if (!data.alocacoes || data.alocacoes.length === 0) {
                document.getElementById('conteudoModal').innerHTML = `
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <h3 class="mt-4 text-lg font-semibold text-gray-900">Nenhuma alocação encontrada</h3>
                        <p class="mt-2 text-gray-600">Não há produtos alocados neste período para redistribuir.</p>
                        <p class="mt-2 text-sm text-gray-500">Verifique se os produtos têm alocações mensais cadastradas.</p>
                        <button onclick="fecharModalRedistribuicao()" class="mt-4 px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                            Fechar
                        </button>
                    </div>
                `;
                return;
            }

            let html = `
                <div class="space-y-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="font-semibold text-blue-900">Resumo da Redistribuição</h4>
                        <div class="mt-2 space-y-2 text-sm">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span class="text-blue-700">Excedente:</span>
                                    <span class="font-bold text-red-600">${data.excedente} produtos</span>
                                    <span class="text-xs text-gray-500">(Acima da capacidade)</span>
                                </div>
                                <div>
                                    <span class="text-blue-700">Produtos a mover:</span>
                                    <span class="font-bold text-orange-600">${data.alocacoes?.length || 0} produto(s)</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span class="text-blue-700">Quantidade total:</span>
                                    <span class="font-bold">${data.quantidade_selecionada} produtos</span>
                                    ${data.quantidade_selecionada > data.excedente ?
                                        `<span class="text-xs text-yellow-600">(${data.quantidade_selecionada - data.excedente} a mais que o excedente)</span>`
                                        : ''}
                                </div>
                                <div>
                                    <span class="text-blue-700">Destino:</span>
                                    <span class="font-bold text-green-600">${meses[data.mes_destino]}/${data.ano_destino}</span>
                                </div>
                            </div>
                        </div>
            `;

            if (data.capacidade_destino) {
                const saldoDestino = data.capacidade_destino.capacidade - data.capacidade_destino.produtos_previstos;
                const corSaldo = saldoDestino >= data.quantidade_selecionada ? 'text-green-600' : 'text-red-600';

                html += `
                    <div class="mt-2 text-sm">
                        <span class="text-blue-700">Capacidade destino:</span>
                        <span class="font-bold ${corSaldo}">${data.capacidade_destino.capacidade} (Saldo: ${saldoDestino})</span>
                    </div>
                `;
            } else {
                html += `
                    <div class="mt-2 text-sm text-yellow-600">
                        ⚠️ Mês de destino não tem capacidade cadastrada
                    </div>
                `;
            }

            html += `</div>`;

            // Verificar se há alocações que serão divididas
            const alocacoesDivididas = data.alocacoes.filter(a => a.tipo === 'parcial');

            if (alocacoesDivididas.length > 0) {
                html += `
                    <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded text-sm">
                        <strong class="text-blue-900">ℹ️ Divisão de Alocações:</strong>
                        <p class="text-blue-800 mt-1">
                            ${alocacoesDivididas.length} alocação(ões) será(ão) dividida(s) para mover exatamente
                            a quantidade necessária (${data.excedente.toLocaleString()} produtos).
                            Uma nova alocação será criada para o mês de destino.
                        </p>
                    </div>
                `;
            }

            html += `
                    <div class="mt-4">
                        <h5 class="font-semibold text-gray-900 mb-2">Produtos que serão movidos:</h5>
                        <div class="max-h-64 overflow-y-auto border border-gray-200 rounded">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Referência</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Descrição</th>
                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Qtd</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Data Atual</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nova Data</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
            `;

            data.alocacoes.forEach(alocacao => {
                const isParcial = alocacao.tipo === 'parcial';
                const quantidadeFicar = alocacao.quantidade - alocacao.quantidade_mover;

                html += `
                    <tr class="hover:bg-gray-50 ${isParcial ? 'bg-yellow-50' : ''}">
                        <td class="px-3 py-2 text-sm font-medium">
                            ${alocacao.referencia}
                            ${isParcial ? '<span class="ml-1 text-xs bg-yellow-200 text-yellow-800 px-1 rounded">DIVIDIR</span>' : ''}
                        </td>
                        <td class="px-3 py-2 text-sm">${alocacao.descricao}</td>
                        <td class="px-3 py-2 text-sm text-center">
                            ${isParcial ? `
                                <div class="space-y-1">
                                    <div class="font-semibold text-orange-600">${alocacao.quantidade_mover.toLocaleString()} <span class="text-xs">→ mover</span></div>
                                    <div class="font-semibold text-green-600">${quantidadeFicar.toLocaleString()} <span class="text-xs">← ficar</span></div>
                                </div>
                            ` : `
                                <span class="font-semibold">${alocacao.quantidade.toLocaleString()}</span>
                            `}
                        </td>
                        <td class="px-3 py-2 text-sm">-</td>
                        <td class="px-3 py-2 text-sm text-green-600 font-medium">${meses[data.mes_destino]}</td>
                    </tr>
                `;
            });

            html += `
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-6 pt-4 border-t">
                        <button onclick="fecharModalRedistribuicao()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                            Cancelar
                        </button>
                        <button onclick="aplicarRedistribuicao(${JSON.stringify(data).replace(/"/g, '&quot;')})"
                                class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">
                            Aplicar Redistribuição
                        </button>
                    </div>
                </div>
            `;

            document.getElementById('conteudoModal').innerHTML = html;
        }

        function aplicarRedistribuicao(data) {
            if (!confirm(`Confirma a redistribuição de ${data.alocacoes.length} alocação(ões)?`)) {
                return;
            }

            document.getElementById('conteudoModal').innerHTML = `
                <div class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
                    <p class="mt-2 text-gray-600">Aplicando redistribuição...</p>
                </div>
            `;

            fetch('{{ route('localizacao-capacidade.aplicar-redistribuicao') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    alocacoes: data.alocacoes.map(a => ({
                        alocacao_id: a.alocacao_id,
                        quantidade_mover: a.quantidade_mover
                    })),
                    mes_destino: data.mes_destino,
                    ano_destino: data.ano_destino,
                    localizacao_id: data.localizacao.id,
                    motivo: 'excedente_capacidade',
                    observacoes: `Redistribuído automaticamente do painel de capacidade`
                })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    document.getElementById('conteudoModal').innerHTML = `
                        <div class="text-center py-8">
                            <svg class="mx-auto h-16 w-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-4 text-lg font-semibold text-gray-900">${result.message}</h3>
                            <button onclick="window.location.reload()" class="mt-4 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                Atualizar Página
                            </button>
                        </div>
                    `;
                } else {
                    document.getElementById('conteudoModal').innerHTML = `
                        <div class="text-center py-8 text-red-600">
                            <p>Erro: ${result.message}</p>
                            <button onclick="fecharModalRedistribuicao()" class="mt-4 px-4 py-2 bg-gray-200 rounded-md">
                                Fechar
                            </button>
                        </div>
                    `;
                }
            })
            .catch(error => {
                document.getElementById('conteudoModal').innerHTML = `
                    <div class="text-center py-8 text-red-600">
                        <p>Erro ao aplicar redistribuição</p>
                    </div>
                `;
            });
        }

        function fecharModalRedistribuicao() {
            document.getElementById('modalRedistribuicao').classList.add('hidden');
        }

        // Fechar modal ao clicar fora
        document.getElementById('modalRedistribuicao')?.addEventListener('click', function(e) {
            if (e.target === this) {
                fecharModalRedistribuicao();
            }
        });

        // Modal de Gerar Capacidades
        function openGerarCapacidadesModal() {
            document.getElementById('modalGerarCapacidades').classList.remove('hidden');
        }

        function fecharModalGerarCapacidades() {
            document.getElementById('modalGerarCapacidades').classList.add('hidden');
        }

        function gerarCapacidades() {
            const mes = document.getElementById('mesGerar').value;
            const ano = document.getElementById('anoGerar').value;

            if (!mes || !ano) {
                alert('Por favor, selecione mês e ano');
                return;
            }

            const btn = document.getElementById('btnGerarCapacidades');
            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Gerando...';

            fetch('{{ route("localizacao-capacidade.gerar-mes") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ mes, ano })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    fecharModalGerarCapacidades();
                    location.reload();
                } else {
                    alert(data.message || 'Erro ao gerar capacidades');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao gerar capacidades');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = 'Gerar Capacidades';
            });
        }

        // Fechar modal ao clicar fora
        document.getElementById('modalGerarCapacidades')?.addEventListener('click', function(e) {
            if (e.target === this) {
                fecharModalGerarCapacidades();
            }
        });

        // ===== HISTÓRICO DE REDISTRIBUIÇÕES =====
        function abrirHistoricoRedistribuicoes() {
            document.getElementById('modalHistoricoRedistribuicoes').style.display = 'flex';
            carregarHistoricoRedistribuicoes();
        }

        function fecharHistoricoRedistribuicoes() {
            document.getElementById('modalHistoricoRedistribuicoes').style.display = 'none';
        }

        function carregarHistoricoRedistribuicoes() {
            const mes = {{ $mes }};
            const ano = {{ $ano }};
            const tbody = document.getElementById('historicoRedistribuicoesBody');
            const loading = document.getElementById('historicoLoading');
            const empty = document.getElementById('historicoEmpty');

            tbody.innerHTML = '';
            loading.style.display = 'block';
            empty.style.display = 'none';

            fetch(`{{ route('localizacao-capacidade.historico-redistribuicoes') }}?mes=${mes}&ano=${ano}`)
                .then(response => response.json())
                .then(data => {
                    loading.style.display = 'none';

                    if (data.success && data.historico.length > 0) {
                        data.historico.forEach(item => {
                            const row = document.createElement('tr');
                            row.className = 'hover:bg-gray-50';

                            const mesOrigem = String(item.mes_origem).padStart(2, '0');
                            const mesDestino = String(item.mes_destino).padStart(2, '0');

                            row.innerHTML = `
                                <td class="px-4 py-3 text-sm text-gray-900">${item.produto?.referencia || 'N/A'}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">${item.produto?.descricao || 'N/A'}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    ${item.localizacao_origem?.nome_localizacao || 'N/A'}<br>
                                    <span class="text-xs text-gray-500">${mesOrigem}/${item.ano_origem}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    ${item.localizacao_destino?.nome_localizacao || 'N/A'}<br>
                                    <span class="text-xs text-gray-500">${mesDestino}/${item.ano_destino}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-center font-semibold text-gray-900">${item.quantidade}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">${item.usuario?.name || 'Sistema'}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">${new Date(item.created_at).toLocaleString('pt-BR')}</td>
                                <td class="px-4 py-3 text-center">
                                    <button onclick="reverterRedistribuicao(${item.id}, '${item.produto?.referencia}')"
                                            class="inline-flex items-center px-3 py-1 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                        </svg>
                                        Reverter
                                    </button>
                                </td>
                            `;
                            tbody.appendChild(row);
                        });
                    } else {
                        empty.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    loading.style.display = 'none';
                    empty.style.display = 'block';
                    empty.querySelector('p').textContent = 'Erro ao carregar histórico';
                });
        }

        function reverterRedistribuicao(historicoId, produtoRef) {
            if (!confirm(`Deseja reverter a redistribuição do produto ${produtoRef}?\n\nIsso irá desfazer a redistribuição e restaurar a alocação original.`)) {
                return;
            }

            fetch('{{ route("localizacao-capacidade.reverter-redistribuicao") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    historico_id: historicoId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    carregarHistoricoRedistribuicoes(); // Recarregar histórico
                    // Recarregar página após 1 segundo para atualizar o dashboard
                    setTimeout(() => location.reload(), 1000);
                } else {
                    alert(data.message || 'Erro ao reverter redistribuição');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao reverter redistribuição');
            });
        }

        // Fechar modal ao clicar fora
        document.getElementById('modalHistoricoRedistribuicoes')?.addEventListener('click', function(e) {
            if (e.target === this) {
                fecharHistoricoRedistribuicoes();
            }
        });
    </script>

    <!-- Modal Histórico de Redistribuições -->
    <div id="modalHistoricoRedistribuicoes" style="display:none;" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-6xl mx-4">
            <!-- Header -->
            <div class="bg-purple-600 px-6 py-4 rounded-t-lg">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Histórico de Redistribuições
                    </h3>
                    <button onclick="fecharHistoricoRedistribuicoes()" class="text-white hover:text-gray-200">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Body -->
            <div class="p-6 max-h-[70vh] overflow-y-auto">
                <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <strong>📋 Histórico do período:</strong> Aqui você pode visualizar todas as redistribuições realizadas neste período e revertê-las se necessário.
                    </p>
                </div>

                <!-- Loading -->
                <div id="historicoLoading" class="text-center py-8" style="display:none;">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                    <p class="mt-2 text-gray-600">Carregando histórico...</p>
                </div>

                <!-- Empty State -->
                <div id="historicoEmpty" class="text-center py-8" style="display:none;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-gray-500 font-medium">Nenhuma redistribuição encontrada para este período</p>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referência</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produto</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Origem</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destino</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Quantidade</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuário</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ação</th>
                            </tr>
                        </thead>
                        <tbody id="historicoRedistribuicoesBody" class="bg-white divide-y divide-gray-200">
                            <!-- Preenchido via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 rounded-b-lg flex justify-end">
                <button onclick="fecharHistoricoRedistribuicoes()" class="px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Fechar
                </button>
            </div>
        </div>
    </div>
    @endpush
</x-app-layout>
