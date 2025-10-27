<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Produtos') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-900">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <!-- Botões de ação -->
            <div class="flex flex-wrap justify-between mb-4">

                <div>
                    @if(auth()->user()->canCreate('produtos'))
                    <a href="{{ route('produtos.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Adicionar Produto
                    </a>
                    @endif
                </div>
                @if(auth()->user()->canRead('produtos'))
                    <button id="btn-gerar-pdf" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Gerar PDF
                    </button>
                @endif
            </div>

            <!-- Filtros -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- Cabeçalho dos Filtros com Toggle -->
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800">Filtros</h3>
                        <button type="button" id="toggle-filters-btn" class="inline-flex items-center px-3 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg id="filter-icon-show" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                            <svg id="filter-icon-hide" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                            </svg>
                            <span id="filter-toggle-text">Ocultar Filtros</span>
                        </button>
                    </div>

                    <!-- Filtros Ativos (visível quando filtros estão ocultos) -->
                    <div id="active-filters-summary" class="mb-4 bg-blue-50 border border-blue-200 rounded-lg p-3 hidden">
                        <div class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-blue-900 mb-2">Filtros Ativos:</p>
                                <div id="active-filters-list" class="flex flex-wrap gap-2"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulário de Filtros -->
                    <div id="filters-container" class="mb-6 bg-gray-100 p-4 rounded-lg">
                        <form id="filter-form" action="{{ route('produtos.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="md:col-span-1">
                                <label for="referencia" class="block text-sm font-medium text-gray-700 mb-1">Referência</label>
                                <input type="text" name="referencia" id="referencia" value="{{ $filters['referencia'] ?? '' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Digite a referência do produto">
                            </div>

                            <div>
                                <label for="descricao" class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                                <input type="text" name="descricao" id="descricao" value="{{ $filters['descricao'] ?? '' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Digite a descrição do produto">
                            </div>

                            <div>
                                <label for="concluido" class="block text-sm font-medium text-gray-700 mb-1">Status de Conclusão</label>
                                <select name="concluido" id="concluido" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Todos</option>
                                    <option value="1" {{ ($filters['concluido'] ?? '') === '1' ? 'selected' : '' }}>Concluídos</option>
                                    <option value="0" {{ ($filters['concluido'] ?? '') === '0' ? 'selected' : '' }}>Não Concluídos</option>
                                </select>
                            </div>

                            <div class="md:col-span-1 flex items-end pb-2">
                                <div class="flex items-center">
                                    <input type="checkbox" name="incluir_excluidos" id="incluir_excluidos" value="1" {{ isset($filters['incluir_excluidos']) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50">
                                    <label for="incluir_excluidos" class="ml-2 block text-sm text-gray-700">Incluir excluídos</label>
                                </div>
                            </div>

                            <div>
                                <label for="marca_id" class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
                                <select name="marca_id" id="marca_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Todas</option>
                                    @foreach($marcas as $marca)
                                        <option value="{{ $marca->id }}" {{ (($filters['marca_id'] ?? '') == $marca->id || (($filters['marca'] ?? '') == $marca->nome_marca)) ? 'selected' : '' }}>
                                            {{ $marca->nome_marca }}
                                        </option>
                                    @endforeach
                                </select>
                                @if(request('marca'))
                                    <input type="hidden" name="marca" value="{{ request('marca') }}">
                                @endif
                            </div>

                            <div>
                                <label for="tecido_id" class="block text-sm font-medium text-gray-700 mb-1">Tecido</label>
                                <select name="tecido_id" id="tecido_id" class="select2 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Todos</option>
                                    @foreach($tecidos as $tecido)
                                        <option value="{{ $tecido->id }}" {{ ($filters['tecido_id'] ?? '') == $tecido->id ? 'selected' : '' }}>
                                            {{ $tecido->descricao }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="estilista_id" class="block text-sm font-medium text-gray-700 mb-1">Estilista</label>
                                <select name="estilista_id" id="estilista_id" class="select2 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Todos</option>
                                    @foreach($estilistas as $estilista)
                                        <option value="{{ $estilista->id }}" {{ (($filters['estilista_id'] ?? '') == $estilista->id || (($filters['estilista'] ?? '') == $estilista->nome_estilista)) ? 'selected' : '' }}>
                                            {{ $estilista->nome_estilista }}
                                        </option>
                                    @endforeach
                                </select>
                                @if(request('estilista'))
                                    <input type="hidden" name="estilista" value="{{ request('estilista') }}">
                                @endif
                            </div>

                            <div>
                                <label for="grupo_id" class="block text-sm font-medium text-gray-700 mb-1">Grupo</label>
                                <select name="grupo_id" id="grupo_id" class="select2 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Todos</option>
                                    @foreach($grupos as $grupo)
                                        <option value="{{ $grupo->id }}" {{ (($filters['grupo_id'] ?? '') == $grupo->id || (($filters['grupo'] ?? '') == $grupo->descricao)) ? 'selected' : '' }}>
                                            {{ $grupo->descricao }}
                                        </option>
                                    @endforeach
                                </select>
                                @if(request('grupo'))
                                    <input type="hidden" name="grupo" value="{{ request('grupo') }}">
                                @endif
                            </div>

                            <div>
                                <label for="status_id" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status_id" id="status_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Todos</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->id }}" {{ (($filters['status_id'] ?? '') == $status->id || (($filters['status'] ?? '') == $status->descricao)) ? 'selected' : '' }}>
                                            {{ $status->descricao }}
                                        </option>
                                    @endforeach
                                </select>
                                @if(request('status'))
                                    <input type="hidden" name="status" value="{{ request('status') }}">
                                @endif
                            </div>

                            <div>
                                <label for="localizacao_id" class="block text-sm font-medium text-gray-700 mb-1">Localização</label>
                                <select name="localizacao_id" id="localizacao_id" class="select2 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Todas</option>
                                    @foreach($localizacoes as $localizacao)
                                        <option value="{{ $localizacao->id }}" {{ (($filters['localizacao_id'] ?? '') == $localizacao->id || (($filters['localizacao'] ?? '') == $localizacao->nome_localizacao)) ? 'selected' : '' }}>
                                            {{ $localizacao->nome_localizacao }}
                                        </option>
                                    @endforeach
                                </select>
                                @if(request('localizacao'))
                                    <input type="hidden" name="localizacao" value="{{ request('localizacao') }}">
                                @endif
                            </div>

                            <div>
                                <label for="situacao_id" class="block text-sm font-medium text-gray-700 mb-1">Situação</label>
                                <select name="situacao_id" id="situacao_id" class="select2 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Todas</option>
                                    @foreach($situacoes as $situacao)
                                        <option value="{{ $situacao->id }}" {{ (($filters['situacao_id'] ?? '') == $situacao->id || (($filters['situacao'] ?? '') == $situacao->descricao)) ? 'selected' : '' }}>
                                            {{ $situacao->descricao }}
                                        </option>
                                    @endforeach
                                </select>
                                @if(request('situacao'))
                                    <input type="hidden" name="situacao" value="{{ request('situacao') }}">
                                @endif
                            </div>

                            <!-- Seção de Filtros por Data -->
                            <div class="md:col-span-4 border-t pt-4 mt-2">
                                <h3 class="text-sm font-semibold text-gray-700 mb-3">Filtro por Datas</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    
                                    <!-- Data de Cadastro -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Data de Cadastro</label>
                                        <div class="space-y-2">
                                            <div>
                                                <label for="data_inicio" class="block text-xs text-gray-600 mb-1">Início</label>
                                                <input type="date" name="data_inicio" id="data_inicio" value="{{ $filters['data_inicio'] ?? '' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            </div>
                                            <div>
                                                <label for="data_fim" class="block text-xs text-gray-600 mb-1">Fim</label>
                                                <input type="date" name="data_fim" id="data_fim" value="{{ $filters['data_fim'] ?? '' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Data Prevista Produção -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Data Prevista Produção</label>
                                        <div class="space-y-2">
                                            <div>
                                                <label for="data_prevista_inicio" class="block text-xs text-gray-600 mb-1">Início</label>
                                                <input type="date" name="data_prevista_inicio" id="data_prevista_inicio" value="{{ $filters['data_prevista_inicio'] ?? '' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            </div>
                                            <div>
                                                <label for="data_prevista_fim" class="block text-xs text-gray-600 mb-1">Fim</label>
                                                <input type="date" name="data_prevista_fim" id="data_prevista_fim" value="{{ $filters['data_prevista_fim'] ?? '' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="md:col-span-4 flex justify-end space-x-2">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    Filtrar
                                </button>
                                <a href="{{ route('produtos.index', ['limpar_filtros' => 1]) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Limpar Filtros
                                </a>
                            </div>
                        </form>
                    </div>

            <!-- Mensagem de sucesso -->
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

                    <!-- Tabela de Produtos -->
                    <div class="relative overflow-x-auto">
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
                                        Data Prev. Produção
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Marca
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Grupo
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-0 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-8">
                                        <span class="sr-only">Concluído</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Localização
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Situação
                                    </th>
                                    <th scope="col" class="sticky right-0 px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50 shadow-md z-10">
                                        Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($produtos as $produto)
                                    <tr class="{{ $produto->trashed() ? 'bg-red-50' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $produto->referencia }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $produto->descricao }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $produto->data_prevista_producao_mes_ano }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($produto->marca && $produto->marca->logo_path)
                                                <img src="{{ asset('storage/' . $produto->marca->logo_path) }}" alt="{{ $produto->marca->nome_marca }}" class="h-6 w-auto object-contain" title="{{ $produto->marca->nome_marca }}">
                                            @else
                                                {{ $produto->marca->nome_marca ?? 'N/A' }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $produto->grupoProduto->descricao ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $produto->status && $produto->status->descricao == 'Ativo' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $produto->status ? $produto->status->descricao : 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-0 py-4 whitespace-nowrap text-xs text-center w-8">
                                            @if($produto->concluido_atual)
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mx-auto text-green-600" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mx-auto text-red-600" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                </svg>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($produto->localizacao_atual)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    {{ $produto->localizacao_atual->nome_localizacao }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 text-xs italic">Não localizado</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($produto->situacao_atual)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                    {{ $produto->situacao_atual->descricao }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 text-xs italic">Sem situação</span>
                                            @endif
                                        </td>
                                        <td class="sticky right-0 px-6 py-4 whitespace-nowrap text-right text-sm font-medium bg-white shadow-md z-10">
                                            <div class="flex justify-end space-x-2">
                                                @if(auth()->user()->canRead('produtos'))
                                                    <a href="{{ route('produtos.show', $produto->id) }}?back_url={{ urlencode(Request::fullUrl()) }}" class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-100">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </a>
                                                @endif

                                                @if(!$produto->trashed() && auth()->user()->canUpdate('produtos'))
                                                    <a href="{{ route('produtos.edit', $produto->id) }}?back_url={{ urlencode(Request::fullUrl()) }}" class="text-indigo-600 hover:text-indigo-900 p-1 rounded hover:bg-indigo-100">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </a>
                                                @endif

                                                @if(!$produto->trashed() && auth()->user()->canDelete('produtos'))
                                                    <form action="{{ route('produtos.destroy', $produto->id) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir este produto?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-100">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1 1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif

                                                @if($produto->trashed() && auth()->user()->canDelete('produtos'))
                                                    <form action="{{ route('produtos.destroy', $produto->id) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja restaurar este produto?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-green-600 hover:text-green-900 p-1 rounded hover:bg-green-100">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            Nenhum produto encontrado.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginação -->
                    <div class="mt-4">
                        {{ $produtos->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicialização do JavaScript

            // Inicializar Select2 nos filtros
            $('.select2').select2({
                placeholder: "Selecione uma opção",
                allowClear: true,
                width: '100%'
            });

            // Ajustar estilo do Select2 para combinar com Tailwind
            $('.select2-container--default .select2-selection--single').css({
                'height': '38px',
                'padding': '5px',
                'border-color': 'rgb(209, 213, 219)'
            });

            // Gerar PDF diretamente com force_generate=1 para evitar erros de contagem
            const btnGerarPdf = document.getElementById('btn-gerar-pdf');
            if (btnGerarPdf) {
                btnGerarPdf.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Mostrar indicador de carregamento
                    btnGerarPdf.disabled = true;
                    btnGerarPdf.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Gerando PDF...';

                    // Obter os filtros do formulário
                    const formData = new FormData(document.getElementById('filter-form'));
                    const queryParams = new URLSearchParams(formData).toString();
                    
                    // Gerar o PDF diretamente com force_generate=1 para evitar o erro de contagem
                    const pdfUrl = '{{ route("produtos.lista.pdf") }}?' + queryParams + '&force_generate=1';
                    
                    // Abrir o PDF em uma nova aba
                    const pdfWindow = window.open(pdfUrl, '_blank');
                    
                    // Restaurar o botão após um curto período
                    setTimeout(function() {
                        btnGerarPdf.disabled = false;
                        btnGerarPdf.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg> Gerar PDF';
                    }, 1500);
                    
                    // Verificar se a janela do PDF foi bloqueada pelo navegador
                    if (!pdfWindow || pdfWindow.closed || typeof pdfWindow.closed === 'undefined') {
                        alert('Por favor, permita pop-ups para este site para visualizar o PDF.');
                        btnGerarPdf.disabled = false;
                        btnGerarPdf.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg> Gerar PDF';
                    }
                });
            }

            // Limpar filtros: função utilitária e bind do botão
            const form = document.getElementById('filter-form');
            const clearButton = document.getElementById('btn-clear-filters');

            function resetFiltersUI() {
                if (!form) return;
                // Limpar inputs de texto e data
                form.querySelectorAll('input[type="text"], input[type="date"]').forEach(function(el) {
                    el.value = '';
                });
                // Limpar selects (inclui Select2)
                form.querySelectorAll('select').forEach(function(sel) {
                    sel.value = '';
                });
                // Resetar Select2 explicitamente
                if (typeof $ !== 'undefined' && $('.select2').length) {
                    $('.select2').val(null).trigger('change');
                }
            }

            // Se a página foi carregada sem query string, garantir que a UI dos filtros esteja limpa
            if (!window.location.search) {
                resetFiltersUI();
            }

            // Ao clicar em Limpar, limpar a UI e submeter o formulário vazio para atualizar a listagem
            if (clearButton) {
                clearButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    resetFiltersUI();
                    // Submeter o formulário vazio para atualizar a listagem
                    form.submit();
                });
            }

            // Sistema de Toggle de Filtros com Filtros Ativos
            const toggleFiltersBtn = document.getElementById('toggle-filters-btn');
            const filtersContainer = document.getElementById('filters-container');
            const activeFiltersSummary = document.getElementById('active-filters-summary');
            const activeFiltersList = document.getElementById('active-filters-list');
            const filterToggleText = document.getElementById('filter-toggle-text');
            const filterIconShow = document.getElementById('filter-icon-show');
            const filterIconHide = document.getElementById('filter-icon-hide');

            // Mapeamento de nomes de filtros para labels amigáveis
            const filterLabels = {
                'referencia': 'Referência',
                'descricao': 'Descrição',
                'concluido': 'Status',
                'incluir_excluidos': 'Incluir Excluídos',
                'marca_id': 'Marca',
                'tecido_id': 'Tecido',
                'estilista_id': 'Estilista',
                'grupo_id': 'Grupo',
                'status_id': 'Status',
                'localizacao_id': 'Localização',
                'situacao_id': 'Situação',
                'data_inicio': 'Data Cadastro (De)',
                'data_fim': 'Data Cadastro (Até)',
                'data_prevista_inicio': 'Data Prev. Produção (De)',
                'data_prevista_fim': 'Data Prev. Produção (Até)'
            };

            // Função para obter o texto de um select pelo valor
            function getSelectText(selectId, value) {
                const select = document.getElementById(selectId);
                if (!select) return value;
                const option = select.querySelector(`option[value="${value}"]`);
                return option ? option.textContent.trim() : value;
            }

            // Função para atualizar a lista de filtros ativos
            function updateActiveFilters() {
                const filters = @json($filters ?? []);
                activeFiltersList.innerHTML = '';
                
                let hasActiveFilters = false;
                
                Object.keys(filters).forEach(key => {
                    const value = filters[key];
                    if (value && value !== '' && key !== 'page') {
                        hasActiveFilters = true;
                        let displayValue = value;
                        
                        // Formatar valor baseado no tipo de filtro
                        if (key === 'concluido') {
                            displayValue = value === '1' ? 'Concluídos' : 'Não Concluídos';
                        } else if (key === 'incluir_excluidos') {
                            displayValue = 'Sim';
                        } else if (key.endsWith('_id')) {
                            displayValue = getSelectText(key, value);
                        } else if (key.includes('data_')) {
                            // Formatar datas
                            const date = new Date(value + 'T00:00:00');
                            displayValue = date.toLocaleDateString('pt-BR');
                        }
                        
                        const badge = document.createElement('span');
                        badge.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800';
                        badge.innerHTML = `
                            <span class="font-semibold mr-1">${filterLabels[key] || key}:</span>
                            <span>${displayValue}</span>
                        `;
                        activeFiltersList.appendChild(badge);
                    }
                });
                
                return hasActiveFilters;
            }

            // Função para alternar visibilidade dos filtros
            function toggleFilters() {
                const isHidden = filtersContainer.classList.contains('hidden');
                
                if (isHidden) {
                    // Mostrar filtros
                    filtersContainer.classList.remove('hidden');
                    activeFiltersSummary.classList.add('hidden');
                    filterToggleText.textContent = 'Ocultar Filtros';
                    filterIconShow.classList.remove('hidden');
                    filterIconHide.classList.add('hidden');
                    localStorage.setItem('produtos_filters_visible', 'true');
                } else {
                    // Ocultar filtros
                    filtersContainer.classList.add('hidden');
                    const hasFilters = updateActiveFilters();
                    if (hasFilters) {
                        activeFiltersSummary.classList.remove('hidden');
                    }
                    filterToggleText.textContent = 'Mostrar Filtros';
                    filterIconShow.classList.add('hidden');
                    filterIconHide.classList.remove('hidden');
                    localStorage.setItem('produtos_filters_visible', 'false');
                }
            }

            // Event listener para o botão de toggle
            if (toggleFiltersBtn) {
                toggleFiltersBtn.addEventListener('click', toggleFilters);
            }

            // Restaurar estado dos filtros do localStorage
            const filtersVisible = localStorage.getItem('produtos_filters_visible');
            if (filtersVisible === 'false') {
                // Ocultar filtros na carga da página
                filtersContainer.classList.add('hidden');
                const hasFilters = updateActiveFilters();
                if (hasFilters) {
                    activeFiltersSummary.classList.remove('hidden');
                }
                filterToggleText.textContent = 'Mostrar Filtros';
                filterIconShow.classList.add('hidden');
                filterIconHide.classList.remove('hidden');
            }

            // Atualizar filtros ativos na carga inicial
            updateActiveFilters();

            // Ao submeter o formulário de filtros, ocultar automaticamente
            if (form) {
                form.addEventListener('submit', function(e) {
                    // Marcar que os filtros devem estar ocultos após o submit
                    localStorage.setItem('produtos_filters_visible', 'false');
                });
            }
        });
    </script>

</x-app-layout>
