<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Importar Estoque de Tecidos por Cor') }}
            </h2>
            <div>
                <a href="{{ route('tecidos.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Voltar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @php $canUpdate = auth()->user() && auth()->user()->canUpdate('tecidos'); @endphp
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Instruções para Importação</h3>
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        O arquivo CSV deve conter os seguintes campos:
                                    </p>
                                    <ul class="mt-2 text-sm text-yellow-700 list-disc list-inside">
                                        <li><strong>referencia</strong>: Referência do tecido (deve existir no sistema)</li>
                                        <li><strong>cor</strong>: Nome da cor do tecido</li>
                                        <li><strong>quantidade</strong>: Quantidade em estoque (número positivo)</li>
                                        <li><strong>codigo_cor</strong>: (Opcional) Código da cor</li>
                                        <li><strong>observacoes</strong>: (Opcional) Observações sobre o estoque</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h4 class="text-md font-medium text-gray-700 mb-2">Exemplo de arquivo CSV:</h4>
                            <div class="bg-gray-50 p-4 rounded-lg overflow-x-auto">
                                <pre class="text-xs text-gray-600">referencia,cor,quantidade,codigo_cor,observacoes
T001,Azul,15.5,#0000FF,Lote 123
T001,Verde,8.2,#00FF00,Lote 124
T002,Vermelho,12.0,#FF0000,Lote 125</pre>
                            </div>
                        </div>

                        <form action="{{ route('tecidos.importar-estoque') }}" method="POST" enctype="multipart/form-data" class="mt-6">
                            @csrf
                            <div class="mb-6">
                                <label for="arquivo_csv" class="block text-sm font-medium text-gray-700 mb-1">Arquivo CSV</label>
                                <input type="file" name="arquivo_csv" id="arquivo_csv" accept=".csv,.txt" required
                                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" {{ $canUpdate ? '' : 'disabled' }}>
                                @error('arquivo_csv')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            @if($canUpdate)
                            <div class="flex items-center justify-end mt-6">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                    </svg>
                                    Importar Dados
                                </button>
                            </div>
                            @else
                            <div class="mt-4 p-3 rounded bg-gray-50 text-sm text-gray-600">
                                Você não tem permissão para importar estoque de tecidos.
                            </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
