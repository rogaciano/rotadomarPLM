<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Novo Produto') }}
            </h2>
            <a href="{{ route('produtos.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-300 focus:outline-none focus:border-gray-300 focus:ring focus:ring-gray-200 disabled:opacity-25 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Voltar
            </a>
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

                    <form action="{{ route('produtos.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Referência -->
                            <div>
                                <label for="referencia" class="block text-sm font-medium text-gray-700 mb-1">Referência</label>
                                <input type="text" name="referencia" id="referencia" value="{{ old('referencia') }}" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            </div>

                            <!-- Descrição -->
                            <div>
                                <label for="descricao" class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                                <input type="text" name="descricao" id="descricao" value="{{ old('descricao') }}" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            </div>

                            <!-- Data de Cadastro -->
                            <div>
                                <label for="data_cadastro" class="block text-sm font-medium text-gray-700 mb-1">Data de Cadastro</label>
                                <input type="date" name="data_cadastro" id="data_cadastro" value="{{ old('data_cadastro', date('Y-m-d')) }}" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            </div>

                            <!-- Data Prevista para Produção -->
                            <div>
                                <label for="data_prevista_producao" class="block text-sm font-medium text-gray-700 mb-1">Data Prevista para Produção</label>
                                <input type="date" name="data_prevista_producao" id="data_prevista_producao" value="{{ old('data_prevista_producao') }}" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            </div>

                            <!-- Marca -->
                            <div>
                                <label for="marca_id" class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
                                <select name="marca_id" id="marca_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-gray-700" required>
                                    <option value="">Selecione uma marca</option>
                                    @foreach($marcas as $marca)
                                        <option value="{{ $marca->id }}" {{ old('marca_id') == $marca->id ? 'selected' : '' }} class="text-gray-700">
                                            {{ $marca->nome_marca }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Quantidade -->
                            <div>
                                <label for="quantidade" class="block text-sm font-medium text-gray-700 mb-1">Quantidade</label>
                                <input type="number" name="quantidade" id="quantidade" value="{{ old('quantidade', 0) }}" min="0" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            </div>

                            <!-- Estilista -->
                            <div>
                                <label for="estilista_id" class="block text-sm font-medium text-gray-700 mb-1">Estilista</label>
                                <select name="estilista_id" id="estilista_id" class="estilista-select block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-gray-700" required>
                                    <option value="">Selecione um estilista</option>
                                    @foreach($estilistas as $estilista)
                                        <option value="{{ $estilista->id }}" {{ old('estilista_id') == $estilista->id ? 'selected' : '' }} class="text-gray-700">
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
                                        <option value="{{ $grupo->id }}" {{ old('grupo_id') == $grupo->id ? 'selected' : '' }} class="text-gray-700">
                                            {{ $grupo->descricao }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Preço Atacado -->
                            <div>
                                <label for="preco_atacado" class="block text-sm font-medium text-gray-700 mb-1">Preço Atacado (R\$)</label>
                                <input type="number" name="preco_atacado" id="preco_atacado" value="{{ old('preco_atacado', '0.00') }}" min="0" step="0.01" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            </div>

                            <!-- Preço Varejo -->
                            <div>
                                <label for="preco_varejo" class="block text-sm font-medium text-gray-700 mb-1">Preço Varejo (R\$)</label>
                                <input type="number" name="preco_varejo" id="preco_varejo" value="{{ old('preco_varejo', '0.00') }}" min="0" step="0.01" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status_id" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status_id" id="status_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-gray-700" required>
                                    <option value="">Selecione um status</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->id }}" {{ old('status_id') == $status->id ? 'selected' : '' }} class="text-gray-700">
                                            {{ $status->descricao }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <!-- Seção de Tecidos separada em uma única coluna -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tecidos</label>
                            <div class="border border-gray-300 rounded-md p-4">
                                <div id="tecidos-container">
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
                        <div class="mb-4">
                            <label for="cores" class="block text-sm font-medium text-gray-700">Variações de Cores</label>
                            <p class="text-blue-500 text-sm mb-2">Todas as cores disponíveis para os tecidos selecionados serão exibidas automaticamente.</p>
                            <div id="cores-container" class="mt-2 p-4 border border-gray-200 rounded-md">
                                <p class="text-gray-500 text-sm">Selecione tecidos para ver as cores disponíveis</p>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">As cores disponíveis serão carregadas automaticamente com base nos tecidos selecionados</p>
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Salvar Produto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Aguardar que o DOM e jQuery estejam totalmente carregados
        $(function() {
            const container = document.getElementById('tecidos-container');
            const addButton = document.getElementById('add-tecido');
            let tecidoCount = 0;

            // Função para inicializar Select2 de forma consistente
            function initializeSelect2(selector) {
                $(selector).select2({
                    placeholder: "Selecione um tecido",
                    allowClear: true,
                    width: '100%',
                    language: {
                        noResults: function() {
                            return "Nenhum tecido encontrado";
                        }
                    },
                    // Configuração para busca mais flexível
                    minimumInputLength: 0,
                    escapeMarkup: function(markup) {
                        return markup;
                    },
                    // Forçar fechamento do dropdown após seleção
                    closeOnSelect: true
                }).on('select2:select', function (e) {
                    // Debug: log quando um item é selecionado
                    console.log('Tecido selecionado:', e.params.data);
                    
                });
            }

            // Inicializar selects existentes
            initializeSelect2('.grupo-select', 'Selecione um grupo');
            initializeSelect2('.estilista-select', 'Selecione um estilista');
            initializeSelect2('.tecido-select', 'Selecione um tecido');

            const tecidosContainer = $('#tecidos-container');
            let tecidoIndex = tecidosContainer.find('.tecido-item').length;

            function updateRemoveButtons() {
                const items = tecidosContainer.find('.tecido-item');
                items.length > 1 ? items.find('.remove-tecido').show() : items.find('.remove-tecido').hide();
            }

            $('#add-tecido').on('click', function() {
                const html = `
                    <div class="tecido-item mb-3 first:mt-0 mt-3 pt-3 first:pt-0 border-t border-gray-200">
                        <div class="flex items-center gap-4">
                            <div class="flex-grow">
                                <select name="tecidos[${tecidoIndex}][tecido_id]" class="tecido-select block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">Selecione um tecido</option>
                                    @foreach($tecidos as $tecido)
                                        <option value="{{ $tecido->id }}">{{ $tecido->descricao }} @if($tecido->referencia) ({{ $tecido->referencia }}) @endif</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-1/4">
                                <input type="number" name="tecidos[${tecidoIndex}][consumo]" placeholder="Consumo" step="0.001" min="0" class="block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            <button type="button" class="remove-tecido text-red-500 hover:text-red-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                            </button>
                        </div>
                    </div>
                `;
                tecidosContainer.append(html);
                initializeSelect2(`select[name="tecidos[${tecidoIndex}][tecido_id]"]`, 'Selecione um tecido');
                tecidoIndex++;
                updateRemoveButtons();
                getAvailableColors(); // Atualiza cores ao adicionar tecido
            });

            tecidosContainer.on('click', '.remove-tecido', function() {
                $(this).closest('.tecido-item').remove();
                updateRemoveButtons();
                getAvailableColors(); // Atualiza cores ao remover tecido
            });

            const coresContainer = $('#cores-container');

            function getSelectedTecidoIds() {
                console.log('Buscando tecidos selecionados...');
                const tecidos = [];
                $('select[name^="tecidos["][name$="[tecido_id]"]').each(function() {
                    const val = $(this).val();
                    if (val && val !== '') {
                        tecidos.push(val);
                        console.log('Tecido encontrado:', val);
                    }
                });
                console.log('Total de tecidos encontrados:', tecidos.length);
                return tecidos;
            }

            function getAvailableColors() {
                if (isLoadingCores) {
                    console.log('Busca de cores já em andamento. Aguardando...');
                    return; // Impede execuções simultâneas
                }
                isLoadingCores = true; // Ativa a guarda

                const selectedTecidoIds = getSelectedTecidoIds();
                if (selectedTecidoIds.length === 0) {
                    coresContainer.html('<p class="text-gray-500 text-sm">Selecione tecidos para ver as cores disponíveis</p>');
                    return;
                }

                // Mostrar mensagem de carregamento
                coresContainer.html('<div class="text-center py-4 text-blue-500 italic">Carregando cores...</div>');

                $.ajax({
                    url: '{{ route("produtos.get-available-colors") }}',
                    method: 'POST',
                    data: { tecido_ids: selectedTecidoIds, _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        console.log('Resposta da API:', response);
                        console.log('Estrutura completa da resposta:', JSON.stringify(response, null, 2));
                        
                        // Limpar o container uma única vez
                        coresContainer.empty();

                        const cores = response.cores || [];
                        console.log('Total de cores recebidas:', cores.length);
                        console.log('Primeira cor (se existir):', cores[0]);

                        if (cores.length > 0) {
                            // Criar cabeçalho da tabela
                            const tableHeader = `
                                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-300">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Cor</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome / Código</th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Quantidade</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                            `;
                            
                            let tableRows = '';
                            cores.forEach((cor, index) => {
                                console.log('Cor encontrada:', cor);
                                // Tentar diferentes estruturas de dados
                                const corNome = cor.cor || cor.nome || '';
                                const codigoCor = cor.codigo_cor || cor.codigo || '';
                                console.log(`Processando: nome="${corNome}", codigo="${codigoCor}"`);
                                tableRows += generateCorItemHtml(corNome, codigoCor, index);
                            });
                            
                            const tableFooter = `
                                        </tbody>
                                    </table>
                                </div>
                            `;
                            
                            coresContainer.html(tableHeader + tableRows + tableFooter);
                        } else {
                            coresContainer.html('<p class="text-gray-500 text-sm">Nenhuma cor disponível para os tecidos selecionados.</p>');
                        }
                        isLoadingCores = false; // Libera a guarda
                    },
                    error: (xhr, status, error) => {
                        console.error('Erro na chamada AJAX:', error);
                        console.error('Status:', status);
                        console.error('Resposta:', xhr.responseText);
                        coresContainer.html('<p class="text-red-500 text-sm">Erro ao carregar as cores.</p>');
                        isLoadingCores = false; // Libera a guarda em caso de erro
                    }
                });
            }

            function generateCorItemHtml(corNome, codigoCor, index) {
                return `
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-center">
                            <div class="w-12 h-12 rounded-lg border-2 border-gray-300 mx-auto shadow-sm" 
                                 style="background-color: ${codigoCor || '#FFFFFF'}" 
                                 title="${codigoCor || 'N/A'}"></div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">${corNome || ''}</div>
                            <div class="text-sm text-gray-500">${codigoCor || ''}</div>
                            <input type="hidden" name="cores[${index}][cor]" value="${corNome || ''}">
                            <input type="hidden" name="cores[${index}][codigo_cor]" value="${codigoCor || ''}">
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" 
                                   name="cores[${index}][quantidade]" 
                                   placeholder="0" 
                                   min="0" 
                                   class="w-24 text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        </td>
                    </tr>
                `;
            }

            tecidosContainer.on('change', '.tecido-select', getAvailableColors);

            // Inicialização com delay para garantir que o Select2 esteja completamente carregado
            updateRemoveButtons();
            
            // O carregamento de cores agora acontecerá apenas quando o usuário
            // interagir com o seletor de tecidos.
        });
    </script>
    @endpush
    </x-app-layout>
