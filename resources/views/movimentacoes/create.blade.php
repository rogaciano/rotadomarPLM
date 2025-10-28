<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Nova Movimentação') }}
            </h2>
            <a href="{{ route('movimentacoes.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-300 focus:outline-none focus:border-gray-300 focus:ring focus:ring-gray-200 disabled:opacity-25 transition">
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

                    <form action="{{ route('movimentacoes.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        @if(isset($produto_id))
                            <input type="hidden" name="redirect_to_produto" value="{{ $produto_id }}">
                        @endif

                        @if(isset($produto_selecionado))
                            <div class="mb-4 bg-blue-50 border-l-4 border-blue-400 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-blue-700">
                                            <strong>Produto pré-selecionado:</strong> {{ $produto_selecionado->referencia }} - {{ $produto_selecionado->descricao }}
                                        </p>
                                        @if(!$produto_selecionado->podeMovimentar())
                                            <p class="mt-2 text-sm text-red-700 font-semibold">
                                                ⚠️ ATENÇÃO: Este produto não pode ter movimentações. Contate o administrador.
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Produto -->
                            <div>
                                <label for="produto_id" class="block text-sm font-medium text-gray-700 mb-1">Produto</label>
                                <select id="produto_id" name="produto_id" class="select2 block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Selecione um produto</option>
                                    @foreach($produtos as $produto)
                                        <option value="{{ $produto->id }}" {{ (old('produto_id') == $produto->id || (isset($produto_id) && $produto_id == $produto->id)) ? 'selected' : '' }}>
                                            {{ $produto->referencia }} - {{ $produto->descricao }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Localização -->
                            <div>
                                <label for="localizacao_id" class="block text-sm font-medium text-gray-700 mb-1">Localização</label>
                                <select id="localizacao_id" name="localizacao_id" class="select2 block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Selecione uma localização</option>
                                    @foreach($localizacoes as $localizacao)
                                        <option value="{{ $localizacao->id }}" {{ old('localizacao_id') == $localizacao->id ? 'selected' : '' }}>
                                            {{ $localizacao->nome_localizacao }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tipo -->
                            <div>
                                <label for="tipo_id" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Movimentação</label>
                                <select id="tipo_id" name="tipo_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Selecione um tipo</option>
                                    @foreach($tipos as $tipo)
                                        <option value="{{ $tipo->id }}" {{ old('tipo_id') == $tipo->id ? 'selected' : '' }}>
                                            {{ $tipo->descricao }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Situação -->
                            <div>
                                <label for="situacao_id" class="block text-sm font-medium text-gray-700 mb-1">Situação</label>
                                <select id="situacao_id" name="situacao_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Selecione uma situação</option>
                                    @foreach($situacoes as $situacao)
                                        <option value="{{ $situacao->id }}" {{ old('situacao_id') == $situacao->id ? 'selected' : '' }}>
                                            {{ $situacao->descricao }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Data Entrada -->
                            <div>
                                <label for="data_entrada" class="block text-sm font-medium text-gray-700 mb-1">Data de Entrada</label>
                                <input type="datetime-local" name="data_entrada" id="data_entrada" value="{{ old('data_entrada', now()->format('Y-m-d\TH:i')) }}" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            </div>

                            <!-- Data Conclusão -->
                            <div>
                                <label for="data_saida" class="block text-sm font-medium text-gray-700 mb-1">Data de Saída (opcional)</label>
                                <input type="datetime-local" name="data_saida" id="data_saida" value="{{ old('data_saida') }}" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            </div>
                            <div>
                                <label for="data_devolucao" class="block text-sm font-medium text-gray-700 mb-1">Data de Devolução (opcional)</label>
                                <input type="datetime-local" name="data_devolucao" id="data_devolucao" value="{{ old('data_devolucao') }}" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            </div>

                            <!-- Observação -->
                            <div class="md:col-span-2">
                                <label for="observacao" class="block text-sm font-medium text-gray-700 mb-1">Observação (opcional)</label>
                                <textarea id="observacao" name="observacao" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('observacao') }}</textarea>
                            </div>

                            <!-- Anexo -->
                            <div class="md:col-span-2">
                                <label for="anexo" class="block text-sm font-medium text-gray-700 mb-1">Anexo (opcional)</label>
                                <input type="file" id="anexo" name="anexo" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <p class="mt-1 text-sm text-gray-500">Formatos aceitos: JPG, JPEG, PNG. Tamanho máximo: 10MB.</p>
                            </div>

                            <!-- Concluido -->
                            <div class="md:col-span-2">
                                <div class="flex items-center mt-2">
                                    <input type="checkbox" id="concluido" name="concluido" value="1" {{ old('concluido') ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="concluido" class="ml-2 block text-sm font-medium text-gray-700">Movimentação concluída</label>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Marque esta opção se a movimentação foi finalizada.</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Salvar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Selecione um produto",
                allowClear: true,
                width: '100%'
            });

            // Ajustar estilo do Select2 para combinar com o Tailwind
            $('.select2-container--default .select2-selection--single').css({
                'height': '42px',
                'padding': '6px 4px',
                'border-color': '#d1d5db'
            });
        });
    </script>
    @endpush
</x-app-layout>
