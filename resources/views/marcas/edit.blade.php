<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Editar Marca') }}
            </h2>
            <a href="{{ route('marcas.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('marcas.update', $marca) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nome da Marca -->
                            <div>
                                <x-input-label for="nome_marca" :value="__('Nome da Marca')" />
                                <x-text-input id="nome_marca" class="block mt-1 w-full" type="text" name="nome_marca" :value="old('nome_marca', $marca->nome_marca)" required autofocus />
                                <x-input-error :messages="$errors->get('nome_marca')" class="mt-2" />
                            </div>

                            <!-- Campo de Data de Cadastro removido -->

                            <!-- Logo da Marca -->
                            <div>
                                <x-input-label for="logo" :value="__('Logo da Marca')" />
                                @if($marca->logo_path)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $marca->logo_path) }}" alt="Logo da {{ $marca->nome_marca }}" class="h-24 w-auto object-contain border rounded p-1">
                                    </div>
                                @endif
                                <input id="logo" type="file" name="logo" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" accept="image/*" />
                                <p class="text-sm text-gray-500 mt-1">Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB</p>
                                <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                            </div>

                            <!-- Cor de Fundo -->
                            <div>
                                <x-input-label for="cor_fundo" :value="__('Cor de Fundo')" />
                                <div class="flex items-center gap-3 mt-1">
                                    <input id="cor_fundo" type="color" name="cor_fundo" value="{{ old('cor_fundo', $marca->cor_fundo ?? '#6366F1') }}" class="h-10 w-20 border border-gray-300 rounded cursor-pointer" />
                                    <input type="text" id="cor_fundo_text" value="{{ old('cor_fundo', $marca->cor_fundo ?? '#6366F1') }}" class="block w-28 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#6366F1" />
                                    <span class="px-3 py-2 rounded text-white font-semibold text-sm" id="preview_fundo" style="background-color: {{ old('cor_fundo', $marca->cor_fundo ?? '#6366F1') }};">Preview</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">Cor para fundo dos badges da marca</p>
                                <x-input-error :messages="$errors->get('cor_fundo')" class="mt-2" />
                            </div>

                            <!-- Cor da Fonte -->
                            <div>
                                <x-input-label for="cor_fonte" :value="__('Cor da Fonte')" />
                                <div class="flex items-center gap-3 mt-1">
                                    <input id="cor_fonte" type="color" name="cor_fonte" value="{{ old('cor_fonte', $marca->cor_fonte ?? '#FFFFFF') }}" class="h-10 w-20 border border-gray-300 rounded cursor-pointer" />
                                    <input type="text" id="cor_fonte_text" value="{{ old('cor_fonte', $marca->cor_fonte ?? '#FFFFFF') }}" class="block w-28 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#FFFFFF" />
                                    <span class="px-3 py-2 rounded font-semibold text-sm border" id="preview_fonte" style="color: {{ old('cor_fonte', $marca->cor_fonte ?? '#FFFFFF') }}; background-color: {{ old('cor_fundo', $marca->cor_fundo ?? '#6366F1') }};">Preview</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">Cor do texto sobre o fundo</p>
                                <x-input-error :messages="$errors->get('cor_fonte')" class="mt-2" />
                            </div>

                            <!-- Status -->

                            <div class="mb-4">
                            <div class="flex items-center">
                                <input type="checkbox" name="ativo" id="ativo" value="1" {{ old('ativo', $marca->ativo) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50">
                                <label for="ativo" class="ml-2 block text-sm text-gray-900">Ativo</label>
                            </div>
                        </div>


                        </div>

                        <!-- Preview da Marca -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Preview da Marca</h3>
                            <div class="bg-gray-50 rounded-lg p-6">
                                <p class="text-sm text-gray-600 mb-3">Veja como a marca ficará nos produtos:</p>
                                <div class="flex flex-wrap gap-3">
                                    <span id="marca_preview" class="inline-flex items-center px-4 py-2 rounded-md font-bold text-lg shadow-sm transition-all" style="background-color: {{ old('cor_fundo', $marca->cor_fundo ?? '#6366F1') }}; color: {{ old('cor_fonte', $marca->cor_fonte ?? '#FFFFFF') }};">
                                        <span id="marca_preview_text">{{ old('nome_marca', $marca->nome_marca) }}</span>
                                    </span>
                                    <span class="inline-flex items-center px-3 py-1 rounded-md font-semibold text-sm shadow-sm" id="marca_preview_small" style="background-color: {{ old('cor_fundo', $marca->cor_fundo ?? '#6366F1') }}; color: {{ old('cor_fonte', $marca->cor_fonte ?? '#FFFFFF') }};">
                                        <span id="marca_preview_small_text">{{ old('nome_marca', $marca->nome_marca) }}</span>
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-3">O preview é atualizado automaticamente conforme você digita</p>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Atualizar
                            </button>

                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Sincronizar color picker com campo de texto
        const corFundo = document.getElementById('cor_fundo');
        const corFundoText = document.getElementById('cor_fundo_text');
        const corFonte = document.getElementById('cor_fonte');
        const corFonteText = document.getElementById('cor_fonte_text');
        const previewFundo = document.getElementById('preview_fundo');
        const previewFonte = document.getElementById('preview_fonte');
        const nomeMarca = document.getElementById('nome_marca');
        const marcaPreview = document.getElementById('marca_preview');
        const marcaPreviewSmall = document.getElementById('marca_preview_small');
        const marcaPreviewText = document.getElementById('marca_preview_text');
        const marcaPreviewSmallText = document.getElementById('marca_preview_small_text');

        // Atualizar preview da marca
        function atualizarPreviewMarca() {
            const nome = nomeMarca.value || 'Nome da Marca';
            marcaPreviewText.textContent = nome;
            marcaPreviewSmallText.textContent = nome;
        }

        // Cor de Fundo
        corFundo.addEventListener('input', function() {
            corFundoText.value = this.value.toUpperCase();
            previewFundo.style.backgroundColor = this.value;
            previewFonte.style.backgroundColor = this.value;
            marcaPreview.style.backgroundColor = this.value;
            marcaPreviewSmall.style.backgroundColor = this.value;
        });

        corFundoText.addEventListener('input', function() {
            if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                corFundo.value = this.value;
                previewFundo.style.backgroundColor = this.value;
                previewFonte.style.backgroundColor = this.value;
                marcaPreview.style.backgroundColor = this.value;
                marcaPreviewSmall.style.backgroundColor = this.value;
            }
        });

        // Cor da Fonte
        corFonte.addEventListener('input', function() {
            corFonteText.value = this.value.toUpperCase();
            previewFonte.style.color = this.value;
            marcaPreview.style.color = this.value;
            marcaPreviewSmall.style.color = this.value;
        });

        corFonteText.addEventListener('input', function() {
            if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                corFonte.value = this.value;
                previewFonte.style.color = this.value;
                marcaPreview.style.color = this.value;
                marcaPreviewSmall.style.color = this.value;
            }
        });

        // Nome da Marca
        nomeMarca.addEventListener('input', atualizarPreviewMarca);
    </script>
    @endpush
</x-app-layout>
