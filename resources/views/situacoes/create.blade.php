<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Nova Situação') }}
            </h2>
            <a href="{{ route('situacoes.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                <!-- Formulário de Criação -->
                <form action="{{ route('situacoes.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="descricao" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Descrição') }}</label>
                        <input id="descricao" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="text" name="descricao" value="{{ old('descricao') }}" required autofocus />
                        @error('descricao')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="prazo" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Prazo (dias)') }}</label>
                        <input id="prazo" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="number" name="prazo" value="{{ old('prazo') }}" min="0" />
                        <p class="text-xs text-gray-500 mt-1">Prazo em dias úteis. Se definido, tem prioridade sobre o prazo da localização.</p>
                        @error('prazo')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="observacoes" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Observações') }}</label>
                        <textarea id="observacoes" name="observacoes" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('observacoes') }}</textarea>
                        @error('observacoes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="ativo" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" checked>
                            <span class="ml-2 text-gray-700">Ativo</span>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-end mt-4">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Salvar') }}
                        </button>
                    </div>
                </form>
                
            </div>
        </div>
    </div>
</x-app-layout>
