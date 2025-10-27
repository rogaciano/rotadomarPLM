<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Minhas Movimentações') }}
            </h2>
            <a href="{{ route('movimentacoes.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Voltar para Todas as Movimentações
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(!$localizacao)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                Você não possui uma localização atribuída. Entre em contato com o administrador para configurar sua localização.
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Resumo das Movimentações -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-medium text-gray-500">Sua Localização</h3>
                                    <p class="text-lg font-semibold text-gray-900">{{ $localizacao->nome_localizacao }}</p>
                                    @if($localizacao->prazo)
                                        <p class="text-xs text-gray-500">Prazo: {{ $localizacao->prazo }} dias</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-medium text-gray-500">Movimentações Pendentes</h3>
                                    <p class="text-2xl font-bold text-gray-900">{{ $totalPendentes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-medium text-gray-500">Movimentações Atrasadas</h3>
                                    <p class="text-2xl font-bold text-red-600">{{ $totalAtrasadas }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabela de Movimentações -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        @if($movimentacoes->isEmpty())
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma movimentação pendente</h3>
                                <p class="mt-1 text-sm text-gray-500">Parabéns! Você está em dia com todas as suas movimentações.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 table-auto">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 110px;">Status</th>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produto</th>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 120px;">Tipo</th>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 120px;">Situação</th>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 110px;">Data Entrada</th>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 100px;">Dias</th>
                                            <th scope="col" class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 100px;">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($movimentacoes as $movimentacao)
                                            <tr class="{{ $movimentacao->esta_atrasado ? 'bg-red-50' : '' }}">
                                                <td class="px-3 py-4 whitespace-nowrap">
                                                    @if($movimentacao->esta_atrasado)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                            </svg>
                                                            Atrasado
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                            </svg>
                                                            Em Dia
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $movimentacao->produto->referencia ?? '-' }}</div>
                                                    <div class="text-sm text-gray-500">{{ $movimentacao->produto ? Str::limit($movimentacao->produto->descricao, 50) : '-' }}</div>
                                                </td>
                                                <td class="px-3 py-4 text-sm text-gray-900" style="max-width: 120px;">
                                                    {{ $movimentacao->tipo->descricao ?? '-' }}
                                                </td>
                                                <td class="px-3 py-4 text-sm text-gray-900" style="max-width: 120px; word-wrap: break-word;">
                                                    {{ $movimentacao->situacao->descricao ?? '-' }}
                                                </td>
                                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $movimentacao->data_entrada->format('d/m/Y') }}
                                                </td>
                                                <td class="px-3 py-4 whitespace-nowrap">
                                                    <div class="text-sm">
                                                        <span class="font-bold {{ $movimentacao->esta_atrasado ? 'text-red-600' : 'text-gray-900' }}">
                                                            {{ number_format($movimentacao->dias_decorridos, 0, ',', '.') }} dias
                                                        </span>
                                                        @if($localizacao->prazo)
                                                            <div class="text-xs text-gray-500">
                                                                @if($movimentacao->esta_atrasado)
                                                                    Atraso: {{ number_format(abs($movimentacao->dias_restantes), 0, ',', '.') }} dias
                                                                @else
                                                                    Restam: {{ number_format($movimentacao->dias_restantes, 0, ',', '.') }} dias
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-3 py-4 whitespace-nowrap text-right text-sm font-medium" style="width: 100px;">
                                                    <div class="flex justify-end gap-2">
                                                        <a href="{{ route('movimentacoes.show', $movimentacao) }}?back_url={{ urlencode(route('movimentacoes.minhas')) }}" class="text-indigo-600 hover:text-indigo-900" title="Ver Detalhes">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                            </svg>
                                                        </a>
                                                        @if(auth()->user()->canUpdate('movimentacoes'))
                                                        <a href="{{ route('movimentacoes.edit', $movimentacao) }}?back_url={{ urlencode(route('movimentacoes.minhas')) }}" class="text-blue-600 hover:text-blue-900" title="Editar">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                        </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
