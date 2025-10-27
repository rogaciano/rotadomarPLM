<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Localização') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('localizacoes.update', $localizacao) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="current_page" value="{{ request()->query('page') }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nome da Localização -->
                            <div>
                                <label for="nome_localizacao" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Nome da Localização') }}</label>
                                <input id="nome_localizacao" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="text" name="nome_localizacao" value="{{ old('nome_localizacao', $localizacao->nome_localizacao) }}" required autofocus />
                                @error('nome_localizacao')
                                    <span class="text-red-600 text-sm mt-2">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Nome Reduzido -->
                            <div>
                                <label for="nome_reduzido" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('Nome Reduzido') }}
                                    <span class="text-xs text-gray-500">(máx. 20 caracteres, para gráficos)</span>
                                </label>
                                <input id="nome_reduzido" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="text" name="nome_reduzido" value="{{ old('nome_reduzido', $localizacao->nome_reduzido) }}" maxlength="20" />
                                @error('nome_reduzido')
                                    <span class="text-red-600 text-sm mt-2">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Prazo -->
                            <div>
                                <label for="prazo" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Prazo (em dias)') }}</label>
                                <input id="prazo" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="number" name="prazo" value="{{ old('prazo', $localizacao->prazo) }}" min="0" />
                                @error('prazo')
                                    <span class="text-red-600 text-sm mt-2">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Capacidade -->
                            <div>
                                <label for="capacidade" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Capacidade') }}</label>
                                <input id="capacidade" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="number" name="capacidade" value="{{ old('capacidade', $localizacao->capacidade) }}" min="0" />
                                @error('capacidade')
                                    <span class="text-red-600 text-sm mt-2">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="flex items-center mt-8">
                                <input id="ativo" name="ativo" type="checkbox" value="1" {{ old('ativo', $localizacao->ativo) ? 'checked' : '' }} class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                                <label for="ativo" class="ml-2 block text-sm text-gray-900">Ativo</label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('localizacoes.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
                                Cancelar
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Atualizar') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
