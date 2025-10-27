<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Configurar Horário de Acesso') }} - {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    <form action="{{ route('user-access-schedules.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-700 mb-2">Dias permitidos</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="flex items-center">
                                    <input type="checkbox" name="monday" id="monday" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ $accessSchedule->monday ? 'checked' : '' }}>
                                    <label for="monday" class="ml-2 block text-sm text-gray-700">Segunda-feira</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="tuesday" id="tuesday" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ $accessSchedule->tuesday ? 'checked' : '' }}>
                                    <label for="tuesday" class="ml-2 block text-sm text-gray-700">Terça-feira</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="wednesday" id="wednesday" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ $accessSchedule->wednesday ? 'checked' : '' }}>
                                    <label for="wednesday" class="ml-2 block text-sm text-gray-700">Quarta-feira</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="thursday" id="thursday" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ $accessSchedule->thursday ? 'checked' : '' }}>
                                    <label for="thursday" class="ml-2 block text-sm text-gray-700">Quinta-feira</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="friday" id="friday" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ $accessSchedule->friday ? 'checked' : '' }}>
                                    <label for="friday" class="ml-2 block text-sm text-gray-700">Sexta-feira</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="saturday" id="saturday" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ $accessSchedule->saturday ? 'checked' : '' }}>
                                    <label for="saturday" class="ml-2 block text-sm text-gray-700">Sábado</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="sunday" id="sunday" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ $accessSchedule->sunday ? 'checked' : '' }}>
                                    <label for="sunday" class="ml-2 block text-sm text-gray-700">Domingo</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-700 mb-2">Horário permitido</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Horário de início</label>
                                    <input type="time" name="start_time" id="start_time" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ $accessSchedule->exists ? substr($accessSchedule->start_time, 0, 5) : '08:00' }}" required>
                                </div>
                                <div>
                                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">Horário de término</label>
                                    <input type="time" name="end_time" id="end_time" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ $accessSchedule->exists ? substr($accessSchedule->end_time, 0, 5) : '18:00' }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ $accessSchedule->is_active ? 'checked' : '' }}>
                                <label for="is_active" class="ml-2 block text-sm text-gray-700">Ativar restrição de horário</label>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">Se desativado, o usuário poderá acessar o sistema a qualquer momento.</p>
                        </div>

                        <div class="flex items-center justify-between mt-8">
                            <a href="{{ route('users.edit', $user->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Voltar
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Salvar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
