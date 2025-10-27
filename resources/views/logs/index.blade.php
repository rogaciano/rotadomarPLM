<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Logs do Sistema') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Nome do Arquivo
                                    </th>
                                    <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Tamanho
                                    </th>
                                    <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Última Modificação
                                    </th>
                                    <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logsList as $log)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">
                                            {{ $log['name'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">
                                            {{ $log['size'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">
                                            {{ $log['modified'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200 text-sm">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('logs.show', basename($log['path'])) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    Visualizar
                                                </a>
                                                <a href="{{ route('logs.download', basename($log['path'])) }}" class="text-green-600 hover:text-green-900">
                                                    Download
                                                </a>
                                                <form action="{{ route('logs.destroy', basename($log['path'])) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este arquivo de log?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                                        Excluir
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap border-b border-gray-200 text-center">
                                            Nenhum arquivo de log encontrado.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
