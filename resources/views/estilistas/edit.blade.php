<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Editar Estilista') }}
            </h2>
            <a href="{{ route('estilistas.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
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
                    <!-- Erros de validação -->
                    @if ($errors->any())
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p class="font-bold">Ocorreram erros de validação:</p>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Formulário de Edição -->
                    <form action="{{ route('estilistas.update', $estilista->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="nome_estilista" class="block text-sm font-medium text-gray-700">Nome do Estilista</label>
                            <input type="text" name="nome_estilista" id="nome_estilista" value="{{ old('nome_estilista', $estilista->nome_estilista) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                        <div class="mb-4">
                            <label for="marca_id" class="block text-sm font-medium text-gray-700">Marca</label>
                            <select name="marca_id" id="marca_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Selecione uma marca</option>
                                @foreach($marcas as $marca)
                                    <option value="{{ $marca->id }}" {{ old('marca_id', $estilista->marca_id) == $marca->id ? 'selected' : '' }}>{{ $marca->nome_marca }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="suporte_marca" class="block text-sm font-medium text-gray-700">Suporte Marca</label>
                            <select name="suporte_marca" id="suporte_marca" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Selecione uma marca para suporte</option>
                                @foreach($marcas as $marca)
                                    <option value="{{ $marca->nome_marca }}" {{ old('suporte_marca', $estilista->suporte_marca) == $marca->nome_marca ? 'selected' : '' }}>{{ $marca->nome_marca }}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="mb-4">
                            <div class="flex items-center">
                                <input type="checkbox" name="ativo" id="ativo" value="1" {{ old('ativo', $estilista->ativo) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50">
                                <label for="ativo" class="ml-2 block text-sm text-gray-900">Ativo</label>
                            </div>
                        </div>
                        
                        <!-- Campo de Upload de Foto -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Foto do Estilista</label>
                            
                            <!-- Exibir foto atual se existir -->
                            @if($estilista->foto)
                                <div class="mb-3">
                                    <p class="text-sm font-medium text-gray-700 mb-1">Foto Atual:</p>
                                    <img src="{{ asset('storage/' . $estilista->foto) }}" alt="Foto do estilista" class="h-40 w-40 object-cover rounded-md border border-gray-200">
                                    <div class="mt-2 flex items-center">
                                        <input type="checkbox" name="remover_foto" id="remover_foto" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50">
                                        <label for="remover_foto" class="ml-2 block text-sm text-red-600">Remover foto atual</label>
                                    </div>
                                </div>
                                <p class="text-sm font-medium text-gray-700 mb-1">Alterar Foto:</p>
                            @endif
                            
                            <div class="mt-1 flex items-center">
                                <input type="file" name="foto" id="foto" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" accept="image/*">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB</p>
                            
                            <!-- Preview da Imagem -->
                            <div id="image-preview" class="mt-2 {{ !$estilista->foto ? 'hidden' : '' }}">
                                <p class="text-sm font-medium text-gray-700 mb-1">Nova Prévia:</p>
                                <img id="preview" class="h-40 w-40 object-cover rounded-md border border-gray-200">
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Atualizar
                            </button>
                        </div>
                    </form>

                    <!-- Script para visualização da imagem selecionada -->
                    @push('scripts')
                    <script>
                        document.getElementById('foto').addEventListener('change', function(e) {
                            const file = e.target.files[0];
                            const preview = document.getElementById('preview');
                            const previewContainer = document.getElementById('image-preview');
                            
                            if (file) {
                                const reader = new FileReader();
                                
                                reader.onload = function(e) {
                                    preview.src = e.target.result;
                                    previewContainer.classList.remove('hidden');
                                }
                                
                                reader.readAsDataURL(file);
                            } else if (!{{ $estilista->foto ? 'true' : 'false' }}) {
                                preview.src = '';
                                previewContainer.classList.add('hidden');
                            }
                        });
                        
                        // Mostrar/ocultar preview ao marcar/desmarcar remover_foto
                        document.getElementById('remover_foto')?.addEventListener('change', function(e) {
                            const previewContainer = document.getElementById('image-preview');
                            if (e.target.checked) {
                                previewContainer.classList.add('hidden');
                            } else if (document.getElementById('foto').files.length > 0) {
                                previewContainer.classList.remove('hidden');
                            }
                        });
                    </script>
                    @endpush
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
