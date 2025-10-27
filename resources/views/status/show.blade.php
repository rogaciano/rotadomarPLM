<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalhes do Status') }}
            </h2>
            <div>
                <a href="{{ route('status.edit', $status) }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-700 focus:outline-none focus:border-purple-700 focus:ring focus:ring-purple-200 disabled:opacity-25 transition mr-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                    Editar
                </a>
                <a href="{{ route('status.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-300 focus:outline-none focus:border-gray-300 focus:ring focus:ring-gray-200 disabled:opacity-25 transition">
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
                    <!-- Informações do Status -->
                    <div class="bg-gray-50 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informações Básicas</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="block text-sm font-medium text-gray-500">ID</span>
                                <span class="block mt-1 text-sm text-gray-900">{{ $status->id }}</span>
                            </div>

                            <div>
                                <span class="block text-sm font-medium text-gray-500">Descrição</span>
                                <span class="block mt-1 text-sm text-gray-900">{{ $status->descricao }}</span>
                            </div>

                            <div>
                                <span class="block text-sm font-medium text-gray-500">Observações</span>
                                <span class="block mt-1 text-sm text-gray-900">{{ $status->observacoes }}</span>
                            </div>

                            <div>
                                <span class="block text-sm font-medium text-gray-500">Status</span>
                                <span class="block mt-1">
                                    @if ($status->ativo)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Ativo
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Inativo
                                        </span>
                                    @endif
                                </span>
                            </div>

                            <div>
                                <span class="block text-sm font-medium text-gray-500">Calcular Necessidade</span>
                                <span class="block mt-1">
                                    @if ($status->calc_necessidade)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Sim
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Não
                                        </span>
                                    @endif
                                </span>
                            </div>

                            <div>
                                <span class="block text-sm font-medium text-gray-500">Criado em</span>
                                <span class="block mt-1 text-sm text-gray-900">{{ $status->created_at ? $status->created_at->format('d/m/Y H:i') : 'N/A' }}</span>
                            </div>

                            <div>
                                <span class="block text-sm font-medium text-gray-500">Última atualização</span>
                                <span class="block mt-1 text-sm text-gray-900">{{ $status->updated_at ? $status->updated_at->format('d/m/Y H:i') : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</x-app-layout>
