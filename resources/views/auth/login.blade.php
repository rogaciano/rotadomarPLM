<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    
    <!-- Mensagem de erro -->
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" style="position: relative; z-index: 10;">
        @csrf

        <!-- Logos das marcas -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="p-3">
                {{-- Uma marca aleatória é fornecida pelo controller --}}

                <div class="flex justify-center items-center">
                    @if($marca)
                        <div class="text-center">
                            @if($marca->logo_path)
                                <img src="{{ asset('storage/' . $marca->logo_path) }}" alt="{{ $marca->nome_marca }}" class="h-16 w-auto object-contain mx-auto">
                            @else
                                <div class="h-16 w-24 flex items-center justify-center bg-gray-100 rounded-lg">
                                    <span class="text-sm font-semibold text-gray-700">{{ $marca->nome_marca }}</span>
                                </div>
                            @endif
                            <p class="mt-2 text-sm font-medium text-gray-600">{{ $marca->nome_marca }}</p>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-sm text-gray-500">Nenhuma marca disponível</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Login (Email ou Nome) -->
        <div>
            <x-input-label for="login" :value="__('Email ou Nome')" />
            <input id="login" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" type="text" name="login" value="{{ old('login') }}" required autofocus autocomplete="username" style="position: relative; z-index: 20;" />
            <x-input-error :messages="$errors->get('login')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Senha')" />

            <input id="password" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                   type="password"
                   name="password"
                   required autocomplete="current-password"
                   style="position: relative; z-index: 20;" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Lembrar-me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Esqueceu sua senha?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Entrar') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
