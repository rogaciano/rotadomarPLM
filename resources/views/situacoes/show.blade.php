<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalhes da Situação') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('situacoes.edit', $situacao->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    Editar
                </a>
                <a href="{{ route('situacoes.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Voltar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Informações da Situação</h3>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Descrição</p>
                                <p class="mt-1 text-lg text-gray-900">{{ $situacao->descricao }}</p>
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-500">Status</p>
                                <p class="mt-1">
                                    <span class="px-2 py-1 rounded-full text-xs {{ $situacao->ativo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $situacao->ativo ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </p>
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-500">Prazo</p>
                                <p class="mt-1 text-lg text-gray-900">
                                    @if($situacao->prazo)
                                        {{ $situacao->prazo }} {{ $situacao->prazo == 1 ? 'dia' : 'dias' }}
                                        <span class="text-xs text-gray-500">(dias úteis)</span>
                                    @else
                                        <span class="text-gray-400">Não definido</span>
                                    @endif
                                </p>
                            </div>

                            <div class="md:col-span-2">
                                <p class="text-sm font-medium text-gray-500">Observações</p>
                                <p class="mt-1 text-gray-900 whitespace-pre-line">{{ $situacao->observacoes ?? 'Nenhuma observação cadastrada.' }}</p>
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-500">Data de Criação</p>
                                <p class="mt-1 text-gray-900">{{ $situacao->created_at->format('d/m/Y H:i:s') }}</p>
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-500">Última Atualização</p>
                                <p class="mt-1 text-gray-900">{{ $situacao->updated_at->format('d/m/Y H:i:s') }}</p>
                            </div>

                            @if($situacao->trashed())
                            <div>
                                <p class="text-sm font-medium text-red-600">Excluído em</p>
                                <p class="mt-1 text-red-900">{{ $situacao->deleted_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
