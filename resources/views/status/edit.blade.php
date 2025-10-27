<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Editar Status') }}
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

                    <form action="{{ route('status.update', $status) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="descricao" class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                            <input type="text" name="descricao" id="descricao" value="{{ old('descricao', $status->descricao) }}" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                        </div>

                        <div class="mb-4">
                            <label for="observacoes" class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                            <textarea name="observacoes" id="observacoes" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('observacoes', $status->observacoes) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="ativo" name="ativo" type="checkbox" value="1" {{ old('ativo', $status->ativo) ? 'checked' : '' }} class="focus:ring-purple-500 h-4 w-4 text-purple-600 border-gray-300 rounded">
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
                                    <input id="calc_necessidade" name="calc_necessidade" type="checkbox" value="1" {{ old('calc_necessidade', $status->calc_necessidade) ? 'checked' : '' }} class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="calc_necessidade" class="font-medium text-gray-700">Calcular Necessidade</label>
                                    <p class="text-gray-500">Define se produtos com este status devem ter a necessidade de tecido calculada</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Atualizar
                            </button>


                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
