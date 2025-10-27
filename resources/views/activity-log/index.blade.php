<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Log de Atividades') }}
        </h2>
    </x-slot>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
                <!-- Filtros -->
                <form method="GET" action="{{ route('activity-log.index') }}" class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="log_name" class="block text-sm font-medium text-gray-700">Nome do Log</label>
                            <select name="log_name" id="log_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Todos</option>
                                @foreach($logNames as $name)
                                    <option value="{{ $name }}" {{ request('log_name') == $name ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="event" class="block text-sm font-medium text-gray-700">Evento</label>
                            <select name="event" id="event" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Todos</option>
                                @foreach($events as $event)
                                    <option value="{{ $event }}" {{ request('event') == $event ? 'selected' : '' }}>
                                        {{ $event }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="subject_type" class="block text-sm font-medium text-gray-700">Tipo de Objeto</label>
                            <select name="subject_type" id="subject_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Todos</option>
                                @foreach($subjectTypes as $type)
                                    <option value="{{ $type['value'] }}" {{ request('subject_type') == $type['value'] ? 'selected' : '' }}>
                                        {{ $type['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="causer_type" class="block text-sm font-medium text-gray-700">Tipo de Usuário</label>
                            <select name="causer_type" id="causer_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Todos</option>
                                @foreach($causerTypes as $type)
                                    <option value="{{ $type['value'] }}" {{ request('causer_type') == $type['value'] ? 'selected' : '' }}>
                                        {{ $type['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700">Data Inicial</label>
                            <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />
                        </div>
                        <div>
                            <label for="date_to" class="block text-sm font-medium text-gray-700">Data Final</label>
                            <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />
                        </div>
                        <div class="flex items-end gap-2">
                            <x-button type="submit">Filtrar</x-button>
                            <a href="{{ route('activity-log.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-300 focus:outline-none focus:border-gray-300 focus:ring focus:ring-gray-200 transition">Limpar</a>
                        </div>
                    </div>
                </form>

                <!-- Tabela de Logs -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Evento</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuário</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($activities as $activity)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $activity->id }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $activity->description }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        @if($activity->subject_type)
                                            {{ class_basename($activity->subject_type) }}
                                            @if($activity->subject_id)
                                                #{{ $activity->subject_id }}
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @php($event = $activity->event)
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $event === 'created' ? 'bg-green-100 text-green-800' : ($event === 'updated' ? 'bg-blue-100 text-blue-800' : ($event === 'deleted' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                            {{ $activity->event }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        @if($activity->causer)
                                            {{ $activity->causer->name ?? class_basename($activity->causer_type) . ' #' . $activity->causer_id }}
                                        @else
                                            Sistema
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $activity->created_at->format('d/m/Y H:i:s') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <a href="{{ route('activity-log.show', $activity->id) }}" class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs font-medium text-indigo-700 bg-indigo-100 hover:bg-indigo-200">Detalhes</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">Nenhum registro encontrado</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="mt-6">
                    {{ $activities->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
