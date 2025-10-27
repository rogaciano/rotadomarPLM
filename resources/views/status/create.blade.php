<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Novo Status') }}
            </h2>
            <a href="{{ route('status.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-300 focus:outline-none focus:border-gray-300 focus:ring focus:ring-gray-200 disabled:opacity-25 transition">
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

                    <form action="{{ route('status.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="descricao" class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                            <input type="text" name="descricao" id="descricao" value="{{ old('descricao') }}" class="shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                        </div>

                        <div class="mb-4">
                            <label for="observacoes" class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                            <textarea name="observacoes" id="observacoes" rows="4" class="shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('observacoes') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="ativo" name="ativo" type="checkbox" value="1" {{ old('ativo', '1') == '1' ? 'checked' : '' }} class="focus:ring-purple-500 h-4 w-4 text-purple-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="ativo" class="font-medium text-gray-700">Ativo</label>
                                    <p class="text-gray-500">O status está disponível para uso</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="calc_necessidade" name="calc_necessidade" type="checkbox" value="1" {{ old('calc_necessidade') == '1' ? 'checked' : '' }} class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="calc_necessidade" class="font-medium text-gray-700">Calcular Necessidade</label>
                                    <p class="text-gray-500">Define se produtos com este status devem ter a necessidade de tecido calculada</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Salvar Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
