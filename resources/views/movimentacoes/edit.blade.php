<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Movimentação') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('movimentacoes.update', $movimentacao) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="current_page" value="{{ request()->query('page') }}">
                        <input type="hidden" name="back_url" value="{{ request('back_url') }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Produto -->
                            <div>
                                <x-label for="produto_id" value="{{ __('Produto') }}" />
                                <select id="produto_id" name="produto_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Selecione um produto</option>
                                    @foreach($produtos as $produto)
                                        <option value="{{ $produto->id }}" {{ old('produto_id', $movimentacao->produto_id) == $produto->id ? 'selected' : '' }}>
                                            {{ $produto->referencia }} - {{ $produto->descricao }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('produto_id')" class="mt-2" />
                            </div>

                            <!-- Localização -->
                            <div>
                                <x-label for="localizacao_id" value="{{ __('Localização') }}" />
                                <select id="localizacao_id" name="localizacao_id" class="select2 block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Selecione uma localização</option>
                                    @foreach($localizacoes as $localizacao)
                                        <option value="{{ $localizacao->id }}" {{ old('localizacao_id', $movimentacao->localizacao_id) == $localizacao->id ? 'selected' : '' }}>
                                            {{ $localizacao->nome_localizacao }}{{ !$localizacao->ativo ? ' (Inativa)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('localizacao_id')" class="mt-2" />
                            </div>

                            <!-- Tipo -->
                            <div>
                                <x-label for="tipo_id" value="{{ __('Tipo de Movimentação') }}" />
                                <select id="tipo_id" name="tipo_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Selecione um tipo</option>
                                    @foreach($tipos as $tipo)
                                        <option value="{{ $tipo->id }}" {{ old('tipo_id', $movimentacao->tipo_id) == $tipo->id ? 'selected' : '' }}>
                                            {{ $tipo->descricao }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('tipo_id')" class="mt-2" />
                            </div>

                            <!-- Situação -->
                            <div>
                                <x-label for="situacao_id" value="{{ __('Situação') }}" />
                                <select id="situacao_id" name="situacao_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Selecione uma situação</option>
                                    @foreach($situacoes as $situacao)
                                        <option value="{{ $situacao->id }}" {{ old('situacao_id', $movimentacao->situacao_id) == $situacao->id ? 'selected' : '' }}>
                                            {{ $situacao->descricao }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('situacao_id')" class="mt-2" />
                            </div>

                            <!-- Data Entrada -->
                            <div>
                                <x-label for="data_entrada" value="{{ __('Data de Entrada') }}" />
                                <x-input id="data_entrada" class="block mt-1 w-full" type="datetime-local" name="data_entrada" :value="old('data_entrada', $movimentacao->data_entrada->format('Y-m-d\TH:i'))" required />
                                <x-input-error :messages="$errors->get('data_entrada')" class="mt-2" />
                            </div>

                            <!-- Data Conclusão -->
                            <div>
                                <x-label for="data_saida" value="{{ __('Data de Saída (opcional)') }}" />
                                <x-input id="data_saida" class="block mt-1 w-full" type="datetime-local" name="data_saida" :value="old('data_saida', $movimentacao->data_saida ? $movimentacao->data_saida->format('Y-m-d\TH:i') : null)" />
                                <x-input-error :messages="$errors->get('data_saida')" class="mt-2" />
                            </div>
                            <!-- Data Devolução -->
                            <div>
                                <x-label for="data_devolucao" value="{{ __('Data de Devolução (opcional)') }}" />
                                <x-input id="data_devolucao" class="block mt-1 w-full" type="datetime-local" name="data_devolucao" :value="old('data_devolucao', $movimentacao->data_devolucao ? $movimentacao->data_devolucao->format('Y-m-d\TH:i') : null)" />
                                <x-input-error :messages="$errors->get('data_devolucao')" class="mt-2" />
                            </div>

                            <!-- Observação -->
                            <div class="md:col-span-2">
                                <x-label for="observacao" value="{{ __('Observação (opcional)') }}" />
                                <textarea id="observacao" name="observacao" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('observacao', $movimentacao->observacao) }}</textarea>
                                <x-input-error :messages="$errors->get('observacao')" class="mt-2" />
                            </div>

                            <!-- Anexo -->
                            <div class="md:col-span-2">
                                <x-label for="anexo" value="{{ __('Anexo (opcional)') }}" />
                                <input type="file" id="anexo" name="anexo" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <p class="mt-1 text-sm text-gray-500">Formatos aceitos: JPG, JPEG, PNG. Tamanho máximo: 10MB.</p>
                                @if($movimentacao->anexo)
                                <div class="mt-2">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm">Anexo atual: <span class="font-medium">{{ basename($movimentacao->anexo) }}</span></p>
                                        <button type="button" class="text-red-600 hover:text-red-800 text-sm font-medium" onclick="confirmarRemocao()">
                                            <span class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Remover anexo
                                            </span>
                                        </button>
                                    </div>
                                    <img src="{{ $movimentacao->anexo_url }}" class="mt-2 max-w-xs rounded border" alt="Anexo atual">
                                </div>
                                @endif
                                <x-input-error :messages="$errors->get('anexo')" class="mt-2" />
                            </div>

                            <!-- Concluido -->
                            <div class="md:col-span-2">
                                <div class="flex items-center mt-2">
                                    <input type="checkbox" id="concluido" name="concluido" value="1" {{ old('concluido', $movimentacao->concluido) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="concluido" class="ml-2 block text-sm font-medium text-gray-700">Movimentação concluída</label>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Marque esta opção se a movimentação foi finalizada.</p>
                                <x-input-error :messages="$errors->get('concluido')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ request('back_url') ? request('back_url') : route('movimentacoes.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
                                Cancelar
                            </a>
                            <x-button>
                                {{ __('Atualizar') }}
                            </x-button>
                        </div>
                    </form>

                    <!-- Formulário para remover anexo (fora do formulário principal) -->
                    @if($movimentacao->anexo)
                    <form id="form-remover-anexo" action="{{ route('movimentacoes.remover-anexo', $movimentacao->id) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Selecione uma localização",
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

        function confirmarRemocao() {
            if (confirm('Tem certeza que deseja remover este anexo?')) {
                document.getElementById('form-remover-anexo').submit();
            }
        }
    </script>
    @endpush
</x-app-layout>
