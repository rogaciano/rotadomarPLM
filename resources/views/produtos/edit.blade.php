<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Editar Produto') }}
            </h2>
            <div class="flex space-x-2">
                <!-- Botão Salvar no topo -->
                <button type="submit" form="produto-form" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Salvar
                </button>
                
                <!-- Botão Voltar -->
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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <!-- Erros de validação -->
                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
                            <p class="font-bold">Ocorreram erros. Por favor, verifique:</p>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="produto-form" action="{{ route('produtos.update', $produto->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Referência -->
                            <div>
                                <label for="referencia" class="block text-sm font-medium text-gray-700 mb-1">Referência</label>
                                <input type="text" name="referencia" id="referencia" value="{{ old('referencia', $produto->referencia) }}" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            </div>

                            <!-- Descrição -->
                            <div>
                                <label for="descricao" class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                                <input type="text" name="descricao" id="descricao" value="{{ old('descricao', $produto->descricao) }}" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            </div>

                            <!-- Data de Cadastro -->
                            <div>
                                <label for="data_cadastro" class="block text-sm font-medium text-gray-700 mb-1">Data de Cadastro</label>
                                <input type="date" name="data_cadastro" id="data_cadastro" value="{{ old('data_cadastro', $produto->data_cadastro ? $produto->data_cadastro->format('Y-m-d') : date('Y-m-d')) }}" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            </div>

                            <!-- Data Prevista para Produção -->
                            <div>
                                <label for="data_prevista_producao" class="block text-sm font-medium text-gray-700 mb-1">Data Prevista para Produção</label>
                                <input type="date" name="data_prevista_producao" id="data_prevista_producao" value="{{ old('data_prevista_producao', $produto->data_prevista_producao ? $produto->data_prevista_producao->format('Y-m-d') : '') }}" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            </div>

                            <!-- Marca -->
                            <div>
                                <label for="marca_id" class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
                                <select name="marca_id" id="marca_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-gray-700" required>
                                    <option value="">Selecione uma marca</option>
                                    @foreach($marcas as $marca)
                                        <option value="{{ $marca->id }}" {{ old('marca_id', $produto->marca_id) == $marca->id ? 'selected' : '' }} class="text-gray-700">
                                            {{ $marca->nome_marca }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Quantidade -->
                            <div>
                                <label for="quantidade" class="block text-sm font-medium text-gray-700 mb-1">Quantidade</label>
                                <input type="number" name="quantidade" id="quantidade" value="{{ old('quantidade', $produto->quantidade) }}" min="0" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" >
                            </div>

                            <!-- Estilista -->
                            <div>
                                <label for="estilista_id" class="block text-sm font-medium text-gray-700 mb-1">Estilista</label>
                                <select name="estilista_id" id="estilista_id" class="estilista-select block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-gray-700" required>
                                    <option value="">Selecione um estilista</option>
                                    @foreach($estilistas as $estilista)
                                        <option value="{{ $estilista->id }}" {{ old('estilista_id', $produto->estilista_id) == $estilista->id ? 'selected' : '' }} class="text-gray-700">
                                            {{ $estilista->nome_estilista }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Grupo -->
                            <div>
                                <label for="grupo_id" class="block text-sm font-medium text-gray-700 mb-1">Grupo</label>
                                <select name="grupo_id" id="grupo_id" class="grupo-select block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-gray-700" required>
                                    <option value="">Selecione um grupo</option>
                                    @foreach($grupos as $grupo)
                                        <option value="{{ $grupo->id }}" {{ old('grupo_id', $produto->grupo_id) == $grupo->id ? 'selected' : '' }} class="text-gray-700">
                                            {{ $grupo->descricao }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Preço Atacado -->
                            <div>
                                <label for="preco_atacado" class="block text-sm font-medium text-gray-700 mb-1">Preço Atacado (R$)</label>
                                <input type="number" name="preco_atacado" id="preco_atacado" value="{{ old('preco_atacado', $produto->preco_atacado) }}" min="0" step="0.01" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            </div>

                            <!-- Preço Varejo -->
                            <div>
                                <label for="preco_varejo" class="block text-sm font-medium text-gray-700 mb-1">Preço Varejo (R$)</label>
                                <input type="number" name="preco_varejo" id="preco_varejo" value="{{ old('preco_varejo', $produto->preco_varejo) }}" min="0" step="0.01" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status_id" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status_id" id="status_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-gray-700 bg-white" required>
                                    <option value="">Selecione um status</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->id }}" {{ old('status_id', $produto->status_id) == $status->id ? 'selected' : '' }} class="text-gray-700">
                                            {{ $status->descricao }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Número de Reprogramação (só aparece se for reprogramação) -->
                            @if($produto->isReprogramacao())
                            <div>
                                <label for="numero_reprogramacao" class="block text-sm font-medium text-gray-700 mb-1">
                                    Número de Reprogramação
                                    <span class="text-xs text-gray-500">(para sistemas antigos)</span>
                                </label>
                                <input type="number" name="numero_reprogramacao" id="numero_reprogramacao" value="{{ old('numero_reprogramacao', $produto->numero_reprogramacao) }}" min="1" max="99" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <p class="text-xs text-gray-500 mt-1">
                                    Campo para ajuste manual quando reprogramações foram iniciadas em sistemas antigos
                                </p>
                            </div>
                            @endif

                            <!-- Anexos Flexíveis -->
                            <div class="col-span-1 md:col-span-2">
                                <div class="flex justify-between items-center mb-3">
                                    <h3 class="text-md font-medium text-gray-700">Anexos</h3>
                                    <button type="button" onclick="document.getElementById('modal-anexo').classList.remove('hidden')" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Adicionar Anexo
                                    </button>
                                </div>

                                @if($produto->anexos && $produto->anexos->count() > 0)
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
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
                                                    <button type="button" onclick="deleteAttachment({{ $anexo->id }})" class="text-red-600 hover:text-red-800 text-sm">
                                                        Excluir
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4 text-gray-500 italic mb-4">
                                        Nenhum anexo adicionado. Salve o produto e adicione anexos na tela de visualização.
                                    </div>
                                @endif

                                <p class="text-sm text-gray-500">Você pode adicionar múltiplos anexos com descrições clicando no botão "Adicionar Anexo".</p>
                            </div>
                        </div>

                        <!-- Tecidos -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tecidos</label>
                            <div class="border border-gray-300 rounded-md p-4">
                                <div id="tecidos-container">
                                    @forelse($produto->tecidos as $index => $produtoTecido)
                                        <div class="tecido-item mb-3 first:mt-0 mt-3 pt-3 first:pt-0 border-t first:border-t-0 border-gray-200">
                                            <div class="flex items-center gap-4">
                                                <div class="flex-grow">
                                                    <select name="tecidos[{{ $index }}][tecido_id]" class="tecido-select select2 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-gray-700">
                                                        <option value="">Selecione um tecido</option>
                                                        @foreach($tecidos as $tecido)
                                                            <option value="{{ $tecido->id }}" {{ $produtoTecido->id == $tecido->id ? 'selected' : '' }} class="text-gray-700">
                                                                {{ $tecido->descricao }} @if($tecido->referencia) ({{ $tecido->referencia }}) @endif
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="w-1/4">
                                                    <input type="number" name="tecidos[{{ $index }}][consumo]" value="{{ $produtoTecido->pivot->consumo }}" placeholder="Consumo" step="0.001" min="0" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                </div>
                                                <button type="button" class="remove-tecido text-red-500 hover:text-red-700">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="tecido-item mb-3 first:mt-0 mt-3 pt-3 first:pt-0 border-t first:border-t-0 border-gray-200">
                                            <div class="flex items-center gap-4">
                                                <div class="flex-grow">
                                                    <select name="tecidos[0][tecido_id]" class="tecido-select select2 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-gray-700">
                                                        <option value="">Selecione um tecido</option>
                                                        @foreach($tecidos as $tecido)
                                                            <option value="{{ $tecido->id }}" class="text-gray-700">
                                                                {{ $tecido->descricao }} @if($tecido->referencia) ({{ $tecido->referencia }}) @endif
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="w-1/4">
                                                    <input type="number" name="tecidos[0][consumo]" placeholder="Consumo" step="0.001" min="0" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                </div>
                                                <button type="button" class="remove-tecido text-red-500 hover:text-red-700" style="display: none;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                                <button type="button" id="add-tecido" class="mt-3 inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                    Adicionar Tecido
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Adicione um ou mais tecidos utilizados neste produto</p>
                        </div>

                        <!-- Seção de Variações de Cores -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Variações de Cores</label>
                            <div class="border border-gray-300 rounded-md p-4">
                                <div id="cores-container">
                                    @forelse($produto->cores as $index => $produtoCor)
                                        <div class="cor-item mb-3 first:mt-0 mt-3 pt-3 first:pt-0 border-t first:border-t-0 border-gray-200">
                                            <div class="flex items-center gap-4">
                                                <div class="flex-grow">
                                                    <input type="text" name="cores[{{ $index }}][cor]" value="{{ $produtoCor->cor }}" placeholder="Nome da cor" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" readonly>
                                                </div>
                                                <div class="w-1/4">
                                                    <input type="text" name="cores[{{ $index }}][codigo_cor]" value="{{ $produtoCor->codigo_cor }}" placeholder="Código" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" readonly>
                                                </div>
                                                <div class="w-1/4">
                                                    <input type="number" name="cores[{{ $index }}][quantidade]" value="{{ $produtoCor->quantidade }}" placeholder="Quantidade" min="1" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                </div>
                                                <button type="button" class="remove-cor text-red-500 hover:text-red-700">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    @empty
                                        <!-- As cores serão adicionadas dinamicamente via JavaScript -->
                                    @endforelse
                                </div>

                            </div>
                            <p class="mt-1 text-xs text-gray-500">As cores disponíveis serão carregadas automaticamente com base nos tecidos selecionados</p>
                        </div>

                        <!-- Contador de cores selecionadas -->
                        <div class="mt-4 text-sm text-gray-700">
                            <span id="cores-count">0</span> cores selecionadas
                        </div>

                        <!-- Seção de Combinações de Cores -->
                        <div class="mt-6">
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-sm font-medium text-gray-700">Combinações de Cores</label>
                                <button type="button" id="add-combinacao" onclick="showCombinacaoModal()" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                    Nova Combinação
                                </button>
                            </div>
                            <p class="mt-1 mb-2 text-xs text-gray-500">Crie combinações de cores para este produto, especificando os tecidos e cores utilizados em cada combinação.</p>
                            <div class="border border-gray-300 rounded-md p-4">
                                <div id="combinacoes-container">
                                    <!-- As combinações serão carregadas via JavaScript -->
                                    <div class="text-center py-4 text-gray-500 italic">
                                        Clique em "Nova Combinação" para adicionar uma combinação de cores.
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Botão de salvar -->
                        <div class="flex justify-end mt-6">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para adicionar/editar combinação -->
    <div id="modal-combinacao" class="fixed z-50 inset-0 overflow-y-auto hidden" style="display: none;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('modal-combinacao').classList.add('hidden')"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-combinacao-title">Nova Combinação</h3>
                            <div class="mt-4 space-y-4">
                                <input type="hidden" id="combinacao-id" value="">
                                <div>
                                    <label for="combinacao-descricao" class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                                    <input type="text" id="combinacao-descricao" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                </div>
                                <div>
                                    <label for="combinacao-quantidade" class="block text-sm font-medium text-gray-700 mb-1">Quantidade Pretendida</label>
                                    <input type="number" id="combinacao-quantidade" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" min="1" value="1" required>
                                </div>
                                <div>
                                    <label for="combinacao-observacoes" class="block text-sm font-medium text-gray-700 mb-1">Observações (opcional)</label>
                                    <textarea id="combinacao-observacoes" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="salvar-combinacao" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">Salvar</button>
                    <button type="button" onclick="document.getElementById('modal-combinacao').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para adicionar/editar componente -->
    <div id="modal-componente" class="fixed z-50 inset-0 overflow-y-auto hidden" style="display: none;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('modal-componente').classList.add('hidden')"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-componente-title">Adicionar Componente</h3>
                            <div class="mt-4 space-y-4">
                                <input type="hidden" id="componente-id" value="">
                                <input type="hidden" id="componente-combinacao-id" value="">
                                <div>
                                    <label for="componente-tecido" class="block text-sm font-medium text-gray-700 mb-1">Tecido</label>
                                    <select id="componente-tecido" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                        <option value="">Selecione um tecido</option>
                                        <!-- Mostrar apenas os tecidos já selecionados para o produto -->
                                        @foreach($produto->tecidos as $tecido)
                                            <option value="{{ $tecido->id }}">{{ $tecido->descricao }} @if($tecido->referencia) ({{ $tecido->referencia }}) @endif</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="componente-cor" class="block text-sm font-medium text-gray-700 mb-1">Cor</label>
                                    <select id="componente-cor" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required disabled>
                                        <option value="">Selecione um tecido primeiro</option>
                                    </select>
                                </div>
                                
                                <!-- Informações de estoque -->
                                <div id="info-estoque" class="hidden bg-gray-50 p-3 rounded-md border border-gray-200">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Informações de Estoque</h4>
                                    <table class="w-full text-sm">
                                        <tr>
                                            <td class="pr-1 py-0.5 whitespace-nowrap text-right"><span class="text-gray-600">Estoque:</span></td>
                                            <td class="pl-1 py-0.5 whitespace-nowrap"><span id="info-estoque-valor" class="font-medium text-gray-900">0 m</span></td>
                                            <td class="pr-1 py-0.5 whitespace-nowrap text-right"><span class="text-gray-600">Necessidade:</span></td>
                                            <td class="pl-1 py-0.5 whitespace-nowrap"><span id="info-necessidade-valor" class="font-medium text-gray-900">0 m</span></td>
                                        </tr>
                                        <tr>
                                            <td class="pr-1 py-0.5 whitespace-nowrap text-right"><span class="text-gray-600">Saldo:</span></td>
                                            <td class="pl-1 py-0.5 whitespace-nowrap"><span id="info-saldo-valor" class="font-medium text-gray-900">0 m</span></td>
                                            <td class="pr-1 py-0.5 whitespace-nowrap text-right"><span class="text-gray-600">Produção possível:</span></td>
                                            <td class="pl-1 py-0.5 whitespace-nowrap"><span id="info-producao-valor" class="font-medium text-gray-900">0 unidades</span></td>
                                        </tr>
                                    </table>
                                </div>
                                
                                <div>
                                    <label for="componente-consumo" class="block text-sm font-medium text-gray-700 mb-1">Consumo (metros)</label>
                                    <input type="number" id="componente-consumo" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" min="0.001" step="0.001" value="1" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="salvar-componente" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">Salvar</button>
                    <button type="button" onclick="document.getElementById('modal-componente').classList.add('hidden'); document.getElementById('modal-componente').style.display = 'none';" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal para adicionar anexo -->
    <div id="modal-anexo" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('modal-anexo').classList.add('hidden')"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('produtos.anexos.store', $produto->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Adicionar Anexo</h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label for="descricao" class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                                        <input type="text" name="descricao" id="descricao" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    </div>
                                    <div>
                                        <label for="arquivo" class="block text-sm font-medium text-gray-700 mb-1">Arquivo</label>
                                        <input type="file" name="arquivo" id="arquivo" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
                                        <p class="mt-1 text-sm text-gray-500">Formatos aceitos: PNG, JPG, JPEG (máx. 10MB) e PDF (máx. 1MB)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">Salvar</button>
                        <button type="button" onclick="document.getElementById('modal-anexo').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
<script>
    // Função global para mostrar o modal de combinação
    function showCombinacaoModal() {
        console.log('showCombinacaoModal called');
        var modal = document.getElementById('modal-combinacao');
        modal.classList.remove('hidden');
        modal.style.display = 'block';
        
        // Adicionar evento para fechar o modal ao clicar fora dele
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
                modal.classList.add('hidden');
            }
        }
    }
    // Function to delete attachment
    function deleteAttachment(anexoId) {
        if (!confirm('Tem certeza que deseja excluir este anexo?')) {
            return;
        }
        
        // Criar um formulário temporário para enviar a requisição DELETE
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ url("produtos/anexos") }}/' + anexoId;
        form.style.display = 'none';
        
        // Adicionar campo CSRF
        const csrfField = document.createElement('input');
        csrfField.type = 'hidden';
        csrfField.name = '_token';
        csrfField.value = '{{ csrf_token() }}';
        form.appendChild(csrfField);
        
        // Adicionar campo METHOD spoofing para DELETE
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        // Adicionar o formulário ao body e submeter
        document.body.appendChild(form);
        form.submit();
    }

    $(document).ready(function() {
        // Variáveis para combinações
        const combinacoesContainer = $('#combinacoes-container');
        let combinacoes = [];
        
        // Atualizar o contador de cores
        function updateCoresCount(count) {
            $('#cores-count').text(count);
        }
        
        // Funções para gerenciar combinações
        function carregarCombinacoes() {
            $.ajax({
                url: '{{ url("produtos") }}/' + {{ $produto->id }} + '/combinacoes',
                method: 'GET',
                success: function(response) {
                    if (response.success && response.combinacoes && response.combinacoes.length > 0) {
                        combinacoes = response.combinacoes;
                        renderizarCombinacoes();
                    } else {
                        combinacoesContainer.html('<div class="text-center py-4 text-gray-500 italic">Clique em "Nova Combinação" para adicionar uma combinação de cores.</div>');
                    }
                },
                error: function(xhr, status, error) {
                    combinacoesContainer.html('<div class="text-center py-4 text-red-500 italic">Erro ao carregar combinações: ' + error + '</div>');
                }
            });
        }
        
        // Carregar combinações ao iniciar
        carregarCombinacoes();
        
        // Renderizar combinações
        function renderizarCombinacoes() {
            if (!combinacoes || combinacoes.length === 0) {
                combinacoesContainer.html('<div class="text-center py-4 text-gray-500 italic">Clique em "Nova Combinação" para adicionar uma combinação de cores.</div>');
                return;
            }
            
            let html = '';
            
            combinacoes.forEach(function(combinacao) {
                html += `
                    <div class="combinacao-item mb-6 pb-6 border-b border-gray-200 last:border-b-0 last:mb-0 last:pb-0" data-id="${combinacao.id}">
                        <div class="flex justify-between items-center mb-3">
                            <div class="flex-grow">
                                <h4 class="text-md font-medium text-gray-800">${combinacao.descricao}</h4>
                                <div class="text-sm text-gray-600">Quantidade pretendida: ${combinacao.quantidade_pretendida}</div>
                                ${combinacao.observacoes ? `<div class="text-sm text-gray-500 italic">${combinacao.observacoes}</div>` : ''}
                            </div>
                            <div class="flex space-x-2">
                                <button type="button" class="editar-combinacao text-blue-600 hover:text-blue-800" data-id="${combinacao.id}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button type="button" class="excluir-combinacao text-red-600 hover:text-red-800" data-id="${combinacao.id}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <div class="componentes-container" data-combinacao-id="${combinacao.id}">
                            ${renderizarComponentes(combinacao.componentes)}
                        </div>
                        
                        <div class="mt-3">
                            <button type="button" class="adicionar-componente inline-flex items-center px-2 py-1 border border-transparent text-xs leading-4 font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" data-combinacao-id="${combinacao.id}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Adicionar Componente
                            </button>
                        </div>
                    </div>
                `;
            });
            
            combinacoesContainer.html(html);
        }
        
        // Renderizar componentes de uma combinação
        function renderizarComponentes(componentes) {
            if (!componentes || componentes.length === 0) {
                return '<div class="text-center py-3 text-gray-500 italic">Nenhum componente adicionado.</div>';
            }
            
            let html = '<div class="grid grid-cols-1 gap-3">';
            
            componentes.forEach(function(componente) {
                html += `
                    <div class="componente-item bg-gray-50 p-3 rounded-md border border-gray-200" data-id="${componente.id}">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center space-x-3">
                                <div class="w-6 h-6 rounded border border-gray-300" style="background-color: ${componente.codigo_cor || '#FFFFFF'}"></div>
                                <div>
                                    <div class="font-medium text-gray-900">${componente.tecido ? componente.tecido.descricao : 'Tecido não encontrado'}</div>
                                    <div class="text-sm text-gray-600">${componente.cor} ${componente.codigo_cor ? `(${componente.codigo_cor})` : ''}</div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="text-sm text-gray-700">Consumo: <span class="font-medium">${componente.consumo} m</span></div>
                                <button type="button" class="editar-componente text-blue-600 hover:text-blue-800 ml-2" data-id="${componente.id}" data-combinacao-id="${componente.produto_combinacao_id}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button type="button" class="excluir-componente text-red-600 hover:text-red-800" data-id="${componente.id}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
            return html;
        }
        
        // Funções para gerenciar modais
        function mostrarModalCombinacao(combinacaoId = null) {
            // Limpar campos
            $('#combinacao-id').val('');
            $('#combinacao-descricao').val('');
            $('#combinacao-quantidade').val('1');
            $('#combinacao-observacoes').val('');
            
            // Se for edição, carregar dados da combinação
            if (combinacaoId) {
                const combinacao = combinacoes.find(c => c.id == combinacaoId);
                if (combinacao) {
                    $('#modal-combinacao-title').text('Editar Combinação');
                    $('#combinacao-id').val(combinacao.id);
                    $('#combinacao-descricao').val(combinacao.descricao);
                    $('#combinacao-quantidade').val(combinacao.quantidade_pretendida);
                    $('#combinacao-observacoes').val(combinacao.observacoes);
                }
            } else {
                $('#modal-combinacao-title').text('Nova Combinação');
            }
            
            // Exibir modal
            $('#modal-combinacao').removeClass('hidden');
            document.getElementById('modal-combinacao').style.display = 'block';
        }
        
        function mostrarModalComponente(combinacaoId, componenteId = null) {
            // Limpar campos
            $('#componente-id').val('');
            $('#componente-combinacao-id').val(combinacaoId);
            $('#componente-tecido').val('').trigger('change');
            $('#componente-cor').val('').prop('disabled', true);
            $('#componente-consumo').val('1');
            
            // Se for edição, carregar dados do componente
            if (componenteId) {
                const combinacao = combinacoes.find(c => c.id == combinacaoId);
                if (combinacao) {
                    const componente = combinacao.componentes.find(comp => comp.id == componenteId);
                    if (componente) {
                        $('#modal-componente-title').text('Editar Componente');
                        $('#componente-id').val(componente.id);
                        $('#componente-tecido').val(componente.tecido_id).trigger('change');
                        
                        // Carregar cores do tecido e selecionar a cor atual
                        carregarCoresTecido(componente.tecido_id, componente.cor, componente.codigo_cor);
                        
                        $('#componente-consumo').val(componente.consumo);
                    }
                }
            } else {
                $('#modal-componente-title').text('Adicionar Componente');
            }
            
            // Exibir modal
            $('#modal-componente').removeClass('hidden');
            document.getElementById('modal-componente').style.display = 'block';
        }
        
        // Função para carregar cores de um tecido
        function carregarCoresTecido(tecidoId, corSelecionada = null, codigoCor = null) {
            if (!tecidoId) {
                $('#componente-cor').html('<option value="">Selecione um tecido primeiro</option>');
                $('#componente-cor').prop('disabled', true);
                $('#info-estoque').addClass('hidden');
                return;
            }
            
            $.ajax({
                url: '{{ url("tecidos") }}/' + tecidoId + '/cores',
                method: 'GET',
                success: function(response) {
                    if (response.success && response.cores && response.cores.length > 0) {
                        let options = '<option value="">Selecione uma cor</option>';
                        
                        response.cores.forEach(function(cor) {
                            const selected = corSelecionada && cor.cor === corSelecionada ? ' selected' : '';
                            options += `<option value="${cor.cor}" data-codigo="${cor.codigo_cor || ''}" data-estoque="${cor.estoque || 0}" data-necessidade="${cor.necessidade || 0}" data-saldo="${cor.saldo || 0}" data-producao="${cor.producao_possivel || 0}"${selected}>${cor.cor} ${cor.codigo_cor ? `(${cor.codigo_cor})` : ''}</option>`;
                        });
                        
                        $('#componente-cor').html(options);
                        $('#componente-cor').prop('disabled', false);
                        
                        // Se temos uma cor selecionada mas não foi encontrada nas opções, adicionar manualmente
                        if (corSelecionada && !$('#componente-cor').val()) {
                            const newOption = `<option value="${corSelecionada}" data-codigo="${codigoCor || ''}" data-estoque="0" data-necessidade="0" data-saldo="0" data-producao="0" selected>${corSelecionada} ${codigoCor ? `(${codigoCor})` : ''}</option>`;
                            $('#componente-cor').append(newOption);
                        }          
                        // Se já temos uma cor selecionada, mostrar informações de estoque
                        if (corSelecionada) {
                            atualizarInfoEstoque();
                        }
                    } else {
                        $('#componente-cor').html('<option value="">Nenhuma cor disponível</option>');
                        $('#componente-cor').prop('disabled', true);
                        $('#info-estoque').addClass('hidden');
                    }
                },
                error: function(xhr, status, error) {
                    $('#componente-cor').html('<option value="">Erro ao carregar cores</option>');
                    $('#componente-cor').prop('disabled', true);
                    $('#info-estoque').addClass('hidden');
                }
            });
        }
        
        // Função para atualizar as informações de estoque
        function atualizarInfoEstoque() {
            const corOption = $('#componente-cor option:selected');
            
            if (corOption.val()) {
                const estoque = parseFloat(corOption.data('estoque')) || 0;
                const necessidade = parseFloat(corOption.data('necessidade')) || 0;
                const saldo = parseFloat(corOption.data('saldo')) || 0;
                const producao = parseFloat(corOption.data('producao')) || 0;
                
                $('#info-estoque-valor').text(estoque.toLocaleString('pt-BR') + ' m');
                $('#info-necessidade-valor').text(necessidade.toLocaleString('pt-BR') + ' m');
                
                // Aplicar cores para saldo
                const saldoElement = $('#info-saldo-valor');
                saldoElement.text(saldo.toLocaleString('pt-BR') + ' m');
                if (saldo < 0) {
                    saldoElement.removeClass('text-green-600').addClass('text-red-600');
                } else {
                    saldoElement.removeClass('text-red-600').addClass('text-green-600');
                }
                
                // Aplicar cores para produção possível
                const producaoElement = $('#info-producao-valor');
                producaoElement.text(producao.toLocaleString('pt-BR') + ' unidades');
                if (producao <= 0) {
                    producaoElement.removeClass('text-green-600').addClass('text-red-600');
                } else {
                    producaoElement.removeClass('text-red-600').addClass('text-green-600');
                }
                
                $('#info-estoque').removeClass('hidden');
            } else {
                $('#info-estoque').addClass('hidden');
            }
        }
        
        // Event handlers
        $(document).on('click', '#add-combinacao', function() {
            mostrarModalCombinacao();
        });
        
        $(document).on('click', '.editar-combinacao', function() {
            const combinacaoId = $(this).data('id');
            mostrarModalCombinacao(combinacaoId);
        });
        
        $(document).on('click', '.excluir-combinacao', function() {
            if (!confirm('Tem certeza que deseja excluir esta combinação?')) {
                return;
            }
            
            const combinacaoId = $(this).data('id');
            
            $.ajax({
                url: '{{ url("produtos/combinacoes") }}/' + combinacaoId,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        carregarCombinacoes();
                    } else {
                        alert('Erro ao excluir combinação: ' + (response.message || 'Erro desconhecido'));
                    }
                },
                error: function(xhr, status, error) {
                    alert('Erro ao excluir combinação: ' + error);
                }
            });
        });
        
        $(document).on('click', '#salvar-combinacao', function() {
            const combinacaoId = $('#combinacao-id').val();
            const descricao = $('#combinacao-descricao').val();
            const quantidade = $('#combinacao-quantidade').val();
            const observacoes = $('#combinacao-observacoes').val();
            
            if (!descricao || !quantidade) {
                alert('Por favor, preencha todos os campos obrigatórios.');
                return;
            }
            
            const data = {
                descricao: descricao,
                quantidade_pretendida: quantidade,
                observacoes: observacoes,
                _token: '{{ csrf_token() }}'
            };
            
            let url, method;
            
            if (combinacaoId) {
                // Edição
                url = '{{ url("produtos/combinacoes") }}/' + combinacaoId;
                method = 'PUT';
            } else {
                // Nova combinação
                url = '{{ url("produtos") }}/' + {{ $produto->id }} + '/combinacoes';
                method = 'POST';
            }
            
            $.ajax({
                url: url,
                method: method,
                data: data,
                success: function(response) {
                    if (response.success) {
                        $('#modal-combinacao').addClass('hidden');
                        document.getElementById('modal-combinacao').style.display = 'none';
                        carregarCombinacoes();
                    } else {
                        alert('Erro ao salvar combinação: ' + (response.message || 'Erro desconhecido'));
                    }
                },
                error: function(xhr, status, error) {
                    alert('Erro ao salvar combinação: ' + error);
                }
            });
        });
        
        $(document).on('click', '.adicionar-componente', function() {
            const combinacaoId = $(this).data('combinacao-id');
            mostrarModalComponente(combinacaoId);
        });
        
        $(document).on('click', '.editar-componente', function() {
            const componenteId = $(this).data('id');
            const combinacaoId = $(this).data('combinacao-id');
            mostrarModalComponente(combinacaoId, componenteId);
        });
        
        $(document).on('click', '.excluir-componente', function() {
            if (!confirm('Tem certeza que deseja excluir este componente?')) {
                return;
            }
            
            const componenteId = $(this).data('id');
            
            $.ajax({
                url: '{{ url("combinacoes/componentes") }}/' + componenteId,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        carregarCombinacoes();
                    } else {
                        alert('Erro ao excluir componente: ' + (response.message || 'Erro desconhecido'));
                    }
                },
                error: function(xhr, status, error) {
                    alert('Erro ao excluir componente: ' + error);
                }
            });
        });
        
        $(document).on('change', '#componente-tecido', function() {
            const tecidoId = $(this).val();
            carregarCoresTecido(tecidoId);
        });
        
        $(document).on('change', '#componente-cor', function() {
            atualizarInfoEstoque();
        });
        
        $(document).on('click', '#salvar-componente', function() {
            const componenteId = $('#componente-id').val();
            const combinacaoId = $('#componente-combinacao-id').val();
            const tecidoId = $('#componente-tecido').val();
            const corOption = $('#componente-cor option:selected');
            const cor = corOption.val();
            const codigoCor = corOption.data('codigo');
            const consumo = $('#componente-consumo').val();
            
            if (!tecidoId || !cor || !consumo) {
                alert('Por favor, preencha todos os campos obrigatórios.');
                return;
            }
            
            const data = {
                tecido_id: tecidoId,
                cor: cor,
                codigo_cor: codigoCor,
                consumo: consumo,
                _token: '{{ csrf_token() }}'
            };
            
            let url, method;
            
            if (componenteId) {
                // Edição
                url = '{{ url("combinacoes/componentes") }}/' + componenteId;
                method = 'PUT';
            } else {
                // Novo componente
                url = '{{ url("combinacoes") }}/' + combinacaoId + '/componentes';
                method = 'POST';
            }
            
            $.ajax({
                url: url,
                method: method,
                data: data,
                success: function(response) {
                    if (response.success) {
                        $('#modal-componente').addClass('hidden');
                        document.getElementById('modal-componente').style.display = 'none';
                        carregarCombinacoes();
                    } else {
                        alert('Erro ao salvar componente: ' + (response.message || 'Erro desconhecido'));
                    }
                },
                error: function(xhr, status, error) {
                    alert('Erro ao salvar componente: ' + error);
                }
            });
        });
        
        // Debug: Capturar evento de submit do formulário
        $('form').on('submit', function(e) {
            
            // Verificar campos obrigatórios
            let camposVazios = [];
            
            // Verificar campos required
            $(this).find('[required]').each(function() {
                const campo = $(this);
                const valor = campo.val();
                const nome = campo.attr('name') || campo.attr('id');
                
                if (!valor || valor.trim() === '') {
                    camposVazios.push(nome);
                }
            });
            
            // Verificar se há pelo menos um tecido selecionado
            const tecidosSelecionados = $('.tecido-select').filter(function() {
                return $(this).val() && $(this).val() !== '';
            }).length;
            
            if (tecidosSelecionados === 0) {
                camposVazios.push('tecidos');
            }
            
            if (camposVazios.length > 0) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos obrigatórios: ' + camposVazios.join(', '));
                return false;
            }

            // Não prevenir o envio se tudo estiver OK
        });

        const tecidosContainer = $('#tecidos-container');
        const coresContainer = $('#cores-container');
        const addTecidoBtn = $('#add-tecido');


        function initializeSelect2(selector, placeholder) {
            $(selector).select2({
                placeholder: placeholder,
                allowClear: true,
                width: '100%',
                language: { noResults: () => "Nenhum resultado encontrado" }
            }).on('select2:open', () => {
                $('.select2-dropdown').addClass('rounded-md shadow-lg');
                $('.select2-search__field').addClass('border-gray-300 rounded-md');
            });
        }

        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                const context = this;
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(context, args), wait);
            };
        }

        function updateColorVariations() {
            const selectedTecidos = [];
            $('.tecido-select').each(function() {
                const val = $(this).val();
                if (val && val !== '') {
                    selectedTecidos.push(val);
                }
            });


            if (selectedTecidos.length === 0) {
                coresContainer.html('<div class="text-center py-4 text-gray-500 italic">Selecione pelo menos um tecido para ver as cores disponíveis.</div>');
                return;
            }

            // Log antes da requisição


            // Armazenar a requisição AJAX atual para poder cancelá-la se necessário
            if (window.currentAjaxRequest) {
                window.currentAjaxRequest.abort();
            }
            
            try {
                // Fazer requisição AJAX
                window.currentAjaxRequest = $.ajax({
                    url: '{{ route("produtos.get-available-colors") }}',
                    method: 'POST',
                    data: {
                        tecido_ids: selectedTecidos,
                        produto_id: {{ $produto->id }},
                        _token: '{{ csrf_token() }}'
                    },
                    beforeSend: function() {
                        coresContainer.html('<div class="text-center py-4 text-gray-500 italic">Carregando cores disponíveis...</div>');
                    },
                    success: function(response) {
                        if (response && response.cores) {
                            renderCores(response.cores);
                        } else {
                            coresContainer.html('<div class="text-center py-4 text-gray-500 italic">Nenhuma cor disponível para os tecidos selecionados.</div>');
                        }
                    },
                    error: function(xhr, status, error) {
                        // Não mostrar erro se a requisição foi abortada intencionalmente
                        if (status !== 'abort') {
                            coresContainer.html('<div class="text-center py-4 text-red-500 italic">Erro ao carregar cores: ' + error + '</div>');
                        }
                    },
                    complete: function() {
                        window.currentAjaxRequest = null;
                    }
                });
            } catch (e) {
                console.error('Erro ao iniciar requisição AJAX:', e);
                coresContainer.html('<div class="text-center py-4 text-red-500 italic">Erro ao iniciar requisição: ' + e.message + '</div>');
            }
        }

        function renderCores(cores) {
            
            if (!cores || cores.length === 0) {
                coresContainer.html('<div class="text-center py-4 text-gray-500 italic">Nenhuma cor disponível para os tecidos selecionados.</div>');
                updateCoresCount(0);
                return;
            }

            let htmlExistentes = '';
            let htmlDisponiveis = '';
            let indexExistentes = 0;
            let indexDisponiveis = 0;

            cores.forEach(function(cor) {
                const backgroundColor = cor.codigo_cor && cor.codigo_cor !== '' ? cor.codigo_cor : '#FFFFFF';
                const borderColor = cor.tipo === 'existente' ? 'border-green-300 bg-green-50' : 'border-blue-300 bg-blue-50';
                const labelColor = cor.tipo === 'existente' ? 'text-green-700' : 'text-blue-700';
                const typeLabel = cor.tipo === 'existente' ? 'Cadastrada' : 'Disponível';
                
                // Formatar valores numéricos
                const estoque = parseFloat(cor.estoque || 0).toFixed(2).replace('.', ',');
                const necessidade = parseFloat(cor.necessidade || 0).toFixed(2).replace('.', ',');
                const producaoPossivel = parseInt(cor.producao_possivel || 0);
                
                // Determinar a cor do saldo (estoque - necessidade)
                const saldo = parseFloat(cor.estoque || 0) - parseFloat(cor.necessidade || 0);
                const saldoFormatado = saldo.toFixed(2).replace('.', ',');
                const saldoClass = saldo >= 0 ? 'text-green-600' : 'text-red-600';
                
                const corHtml = `
                    <div class="mb-3 p-3 border ${borderColor} rounded-md">
                        <div class="flex flex-col space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded border border-gray-300" style="background-color: ${backgroundColor}" title="${cor.codigo_cor || 'N/A'}"></div>
                                    <div>
                                        <div class="font-medium text-gray-900">${cor.cor || ''}</div>
                                        <div class="text-sm text-gray-500">${cor.codigo_cor || ''}</div>
                                        <div class="text-xs ${labelColor} font-medium">${typeLabel}</div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <label class="text-sm text-gray-700">Quantidade:</label>
                                    <input type="number" 
                                           name="cores[${cor.tipo === 'existente' ? indexExistentes : cores.filter(c => c.tipo === 'existente').length + indexDisponiveis}][quantidade]" 
                                           value="${cor.quantidade || 0}" 
                                           placeholder="0" 
                                           min="0" 
                                           class="w-20 text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <input type="hidden" name="cores[${cor.tipo === 'existente' ? indexExistentes : cores.filter(c => c.tipo === 'existente').length + indexDisponiveis}][cor]" value="${cor.cor}">
                                    <input type="hidden" name="cores[${cor.tipo === 'existente' ? indexExistentes : cores.filter(c => c.tipo === 'existente').length + indexDisponiveis}][codigo_cor]" value="${cor.codigo_cor}">
                                </div>
                            </div>
                            <div class="border-t border-gray-200 pt-3">
                                <table class="w-full text-sm">
                                    <tr>
                                        <td class="pr-2"><span class="font-medium text-gray-700">Estoque:</span></td>
                                        <td class="pr-4">${estoque} m</td>
                                        <td class="pr-2"><span class="font-medium text-gray-700">Necessidade:</span></td>
                                        <td class="pr-4">${necessidade} m</td>
                                        <td class="pr-2"><span class="font-medium text-gray-700">Saldo:</span></td>
                                        <td class="pr-4"><span class="${saldoClass}">${saldoFormatado} m</span></td>
                                        <td class="pr-2"><span class="font-medium text-gray-700">Produção possível:</span></td>
                                        <td><span class="font-semibold ${producaoPossivel > 0 ? 'text-green-600' : 'text-gray-600'}">${producaoPossivel} unidades</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                `;

                if (cor.tipo === 'existente') {
                    htmlExistentes += corHtml;
                    indexExistentes++;
                } else {
                    htmlDisponiveis += corHtml;
                    indexDisponiveis++;
                }
            });

            let finalHtml = '';
            
            if (htmlExistentes) {
                finalHtml += `
                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-green-700 mb-2">Cores já cadastradas no produto:</h4>
                        ${htmlExistentes}
                    </div>
                `;
            }
            
            if (htmlDisponiveis) {
                finalHtml += `
                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-blue-700 mb-2">Cores disponíveis nos tecidos:</h4>
                        ${htmlDisponiveis}
                    </div>
                `;
            }
            
            coresContainer.html(finalHtml);
            
            // Atualizar o contador de cores selecionadas
            updateCoresCount(cores.length);
        }
        
        // Função para atualizar o contador de cores
        function updateCoresCount(count) {
            $('#cores-count').text(count);
        }

        function updateRemoveTecidoButtons() {
            const items = tecidosContainer.find('.tecido-item');
            if (items.length > 1) {
                items.find('.remove-tecido').show();
            } else {
                items.find('.remove-tecido').hide();
            }
        }

        function reindexTecidos() {
            tecidosContainer.find('.tecido-item').each(function(index) {
                $(this).find('[name^="tecidos"]').each(function() {
                    const oldName = $(this).attr('name');
                    const newName = oldName.replace(/tecidos\[\d+\]/, `tecidos[${index}]`);
                    $(this).attr('name', newName);
                });
            });
        }

        // Adicionar tecido - usando event delegation para garantir que funcione
        $(document).on('click', '#add-tecido', function(e) {
            e.preventDefault();
            
            const newIndex = tecidosContainer.find('.tecido-item').length;
            
            const template = `
                <div class="tecido-item mb-3 first:mt-0 mt-3 pt-3 first:pt-0 border-t border-gray-200">
                    <div class="flex items-center gap-4">
                        <div class="flex-grow">
                            <select name="tecidos[${newIndex}][tecido_id]" class="tecido-select-new block w-full">
                                <option></option>
                                @foreach($tecidos as $tecido)
                                    <option value="{{ $tecido->id }}">{{ $tecido->descricao }} @if($tecido->referencia) ({{ $tecido->referencia }}) @endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-1/4"><input type="number" name="tecidos[${newIndex}][consumo]" placeholder="Consumo" step="0.001" min="0" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></div>
                        <button type="button" class="remove-tecido text-red-500 hover:text-red-700"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg></button>
                    </div>
                </div>`;
            
            tecidosContainer.append(template);
            const newSelect = tecidosContainer.find('.tecido-select-new');
            initializeSelect2(newSelect, 'Selecione um tecido');
            newSelect.removeClass('tecido-select-new').addClass('tecido-select');
            updateRemoveTecidoButtons();
            updateColorVariations();
        });

        // Remover tecido - usando event delegation
        $(document).on('click', '.remove-tecido', function(e) {
            e.preventDefault();
            $(this).closest('.tecido-item').remove();
            reindexTecidos();
            updateRemoveTecidoButtons();
            updateColorVariations();
        });

        // Initial setup
        initializeSelect2('.grupo-select', 'Selecione um grupo');
        initializeSelect2('.estilista-select', 'Selecione um estilista');
        
        // Inicializar Select2 para tecidos e verificar se foram inicializados corretamente
        const tecidoSelects = $('.tecido-select');
        initializeSelect2('.tecido-select', 'Selecione um tecido');
        
        // Verificar valores iniciais dos selects de tecido
        tecidoSelects.each(function(index) {
        });
        
        // Atualizar cores quando um tecido é alterado
        const debouncedUpdateColors = debounce(updateColorVariations, 400);
        tecidosContainer.on('change', '.tecido-select', function() {
            debouncedUpdateColors();
        });
        
        // Inicialização
        
        // Atualizar botões de remover tecido
        updateRemoveTecidoButtons();
        
        // Carregar cores diretamente com delay maior para garantir que Select2 esteja inicializado
        setTimeout(function() {
            // updateColorVariations(); // COMENTADO para evitar dupla chamada
            
            // Verificar novamente os tecidos selecionados
            const tecidosSelecionados = [];
            $('.tecido-select').each(function() {
                const val = $(this).val();
                if (val && val !== '') {
                    tecidosSelecionados.push(val);
                }
            });
            
            if (tecidosSelecionados.length > 0) {
                updateColorVariations(); // Chamar apenas se houver tecidos selecionados
            } else {
                coresContainer.html('<div class="text-center py-4 text-gray-500 italic">Selecione pelo menos um tecido para ver as cores disponíveis.</div>');
            }
        }, 1500);
        
        // Exibir mensagem ao usuário
        const infoMsg = $('<div class="mt-2 text-xs text-blue-600">Todas as cores disponíveis para os tecidos selecionados serão exibidas automaticamente.</div>');
        coresContainer.parent().append(infoMsg);
        
    });
</script>
@endpush
</x-app-layout>
