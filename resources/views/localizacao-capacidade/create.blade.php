<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Nova Capacidade Mensal') }}
            </h2>
            <a href="{{ route('localizacao-capacidade.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                <!-- Formulário de Criação -->
                <form action="{{ route('localizacao-capacidade.store') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label for="localizacao_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Localização') }} <span class="text-red-500">*</span></label>
                            <select id="localizacao_id" name="localizacao_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="">Selecione uma localização</option>
                                @foreach($localizacoes as $localizacao)
                                    <option value="{{ $localizacao->id }}" {{ old('localizacao_id') == $localizacao->id ? 'selected' : '' }}>
                                        {{ $localizacao->nome_localizacao }}
                                    </option>
                                @endforeach
                            </select>
                            @error('localizacao_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="mes" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Mês') }} <span class="text-red-500">*</span></label>
                            <select id="mes" name="mes" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="">Selecione o mês</option>
                                @php
                                    $meses = ['', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
                                @endphp
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ old('mes') == $m ? 'selected' : '' }}>
                                        {{ $meses[$m] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('mes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="ano" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Ano') }} <span class="text-red-500">*</span></label>
                            <select id="ano" name="ano" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="">Selecione o ano</option>
                                @foreach(range(now()->year - 1, now()->year + 5) as $a)
                                    <option value="{{ $a }}" {{ old('ano', now()->year) == $a ? 'selected' : '' }}>
                                        {{ $a }}
                                    </option>
                                @endforeach
                            </select>
                            @error('ano')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="capacidade" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Capacidade (quantidade de produtos)') }} <span class="text-red-500">*</span></label>
                            <input id="capacidade" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="number" name="capacidade" value="{{ old('capacidade') }}" min="0" required />
                            <p class="text-xs text-gray-500 mt-1">Quantos produtos esta localização pode processar neste mês.</p>
                            @error('capacidade')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="observacoes" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Observações') }}</label>
                            <textarea id="observacoes" name="observacoes" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('observacoes') }}</textarea>
                            @error('observacoes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-end mt-6">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Salvar') }}
                        </button>
                    </div>
                </form>
                
            </div>
        </div>
    </div>
</x-app-layout>
