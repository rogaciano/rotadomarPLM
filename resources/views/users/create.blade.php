<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Criar Novo Usuário') }}
            </h2>
            <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-300 focus:outline-none focus:border-gray-300 focus:ring focus:ring-gray-200 disabled:opacity-25 transition">
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
                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Nome -->
                            <div>
                                <x-label for="name" value="{{ __('Nome') }}" />
                                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Email -->
                            <div>
                                <x-label for="email" value="{{ __('Email') }}" />
                                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <!-- Localização -->
                            <div>
                                <x-label for="localizacao_id" value="{{ __('Localização') }}" />
                                <select id="localizacao_id" name="localizacao_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Selecione uma localização</option>
                                    @foreach($localizacoes as $localizacao)
                                        <option value="{{ $localizacao->id }}" {{ old('localizacao_id') == $localizacao->id ? 'selected' : '' }}>
                                            {{ $localizacao->nome_localizacao }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('localizacao_id')" class="mt-2" />
                            </div>

                            <!-- Tipo de Usuário -->
                            <div>
                                <x-label for="is_admin" value="{{ __('Tipo de Usuário') }}" />
                                <div class="mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="hidden" name="is_admin" value="0">
                                        <input type="checkbox" name="is_admin" id="is_admin" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ old('is_admin') ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-600">Administrador</span>
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">O campo is_admin deve ser verdadeiro ou falso.</p>
                                <x-input-error :messages="$errors->get('is_admin')" class="mt-2" />
                            </div>

                            <!-- Senha -->
                            <div>
                                <x-label for="password" value="{{ __('Senha') }}" />
                                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <!-- Confirmar Senha -->
                            <div>
                                <x-label for="password_confirmation" value="{{ __('Confirmar Senha') }}" />
                                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-button class="ml-4">
                                {{ __('Criar Usuário') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
