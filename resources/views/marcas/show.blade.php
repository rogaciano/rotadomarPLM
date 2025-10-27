<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalhes da Marca') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('marcas.edit', $marca) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Editar
                </a>
                <a href="{{ route('marcas.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Voltar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Informações Básicas e Total de Produtos -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-start">
                        <div class="w-full md:w-2/3">
                            <div class="flex items-start">
                                @if($marca->logo_path)
                                    <div class="mr-6 mb-4">
                                        <img src="{{ asset('storage/' . $marca->logo_path) }}" alt="Logo {{ $marca->nome_marca }}" class="w-32 h-32 object-contain rounded-md border border-gray-200 shadow-sm">
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <h3 class="text-2xl font-bold text-gray-900">{{ $marca->nome_marca }}</h3>
                                    
                                    <div class="mt-4 space-y-3">
                                        <div class="flex items-center">
                                            <span class="text-sm font-medium text-gray-500 w-32">Status:</span>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $marca->ativo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $marca->ativo ? 'Ativo' : 'Inativo' }}
                                            </span>
                                        </div>
                                        
                                        <div class="flex items-center">
                                            <span class="text-sm font-medium text-gray-500 w-32">Data de Cadastro:</span>
                                            <span class="text-sm text-gray-900">
                                                {{ $marca->created_at ? $marca->created_at->format('d/m/Y') : 'Não registrada' }}
                                            </span>
                                        </div>
                                        
                                        <div class="flex items-center">
                                            <span class="text-sm font-medium text-gray-500 w-32">Última Atualização:</span>
                                            <span class="text-sm text-gray-900">
                                                {{ $marca->updated_at ? $marca->updated_at->format('d/m/Y') : 'Não registrada' }}
                                            </span>
                                        </div>
                                        
                                        @if(isset($marca->suporte_marca))
                                        <div class="flex items-start">
                                            <span class="text-sm font-medium text-gray-500 w-32">Suporte:</span>
                                            <span class="text-sm text-gray-900">
                                                {{ $marca->suporte_marca ?: 'Não informado' }}
                                            </span>
                                        </div>
                                        @endif
                                        
                                        <div class="flex items-center">
                                            <span class="text-sm font-medium text-gray-500 w-32">Imagem:</span>
                                            <span class="text-sm text-gray-900">
                                                {{ $marca->logo_path ? 'Disponível' : 'Não disponível' }}
                                            </span>
                                        </div>

                                        @if($marca->cor_fundo && $marca->cor_fonte)
                                        <div class="mt-4 pt-4 border-t border-gray-200">
                                            <div class="flex items-start">
                                                <span class="text-sm font-medium text-gray-500 w-32">Cores da Marca:</span>
                                                <div class="flex-1 space-y-2">
                                                    <div class="flex items-center gap-3">
                                                        <span class="text-xs text-gray-500 w-16">Fundo:</span>
                                                        <div class="w-8 h-8 rounded border-2 border-gray-300" style="background-color: {{ $marca->cor_fundo }};"></div>
                                                        <span class="text-xs font-mono text-gray-600">{{ $marca->cor_fundo }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-3">
                                                        <span class="text-xs text-gray-500 w-16">Fonte:</span>
                                                        <div class="w-8 h-8 rounded border-2 border-gray-300" style="background-color: {{ $marca->cor_fonte }};"></div>
                                                        <span class="text-xs font-mono text-gray-600">{{ $marca->cor_fonte }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-3">
                                                        <span class="text-xs text-gray-500 w-16">Preview:</span>
                                                        <span class="px-4 py-2 inline-flex text-sm font-semibold rounded-full shadow" style="background-color: {{ $marca->cor_fundo }}; color: {{ $marca->cor_fonte }};">
                                                            {{ $marca->nome_marca }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6 md:mt-0 md:w-1/3 md:flex md:justify-end">
                            <a href="{{ route('produtos.index', ['marca' => $marca->nome_marca]) }}" class="w-full md:w-auto inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Ver {{ $estatisticas['totalProdutos'] }} produtos
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Estatísticas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Produtos por Estilista -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Por Estilista <span class="text-sm font-normal text-gray-500">({{ count($estatisticas['produtosPorEstilista']) }})</span></h3>
                        <button id="toggle-estilistas" class="flex items-center text-sm text-gray-500 hover:text-gray-700 focus:outline-none" aria-expanded="false" aria-controls="estilistas-content">
                            <span class="mr-1">Expandir/Recolher</span>
                            <svg class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </div>
                    <div id="estilistas-content" class="px-6 py-0 h-0 overflow-hidden transition-all duration-300 ease-in-out">
                        @if(count($estatisticas['produtosPorEstilista']) > 0)
                            <ul class="divide-y divide-gray-200">
                                @foreach($estatisticas['produtosPorEstilista'] as $estilista => $total)
                                    <li class="py-3 flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-900">{{ $estilista }}</span>
                                        <a href="{{ route('produtos.index', ['marca' => $marca->nome_marca, 'estilista' => $estilista]) }}" class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 hover:bg-indigo-200">
                                            {{ $total }} {{ $total == 1 ? 'produto' : 'produtos' }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-gray-500">Nenhum produto associado a um estilista.</p>
                        @endif
                    </div>
                </div>

                <!-- Produtos por Localização -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Por Localização <span class="text-sm font-normal text-gray-500">({{ count($estatisticas['produtosPorLocalizacao']) }})</span></h3>
                        <button id="toggle-localizacoes" class="flex items-center text-sm text-gray-500 hover:text-gray-700 focus:outline-none" aria-expanded="false" aria-controls="localizacoes-content">
                            <span class="mr-1">Expandir/Recolher</span>
                            <svg class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </div>
                    <div id="localizacoes-content" class="px-6 py-0 h-0 overflow-hidden transition-all duration-300 ease-in-out">
                        @if(count($estatisticas['produtosPorLocalizacao']) > 0)
                            <ul class="divide-y divide-gray-200">
                                @foreach($estatisticas['produtosPorLocalizacao'] as $localizacao => $total)
                                    <li class="py-3 flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-900">{{ $localizacao }}</span>
                                        <a href="{{ route('produtos.index', ['marca' => $marca->nome_marca, 'localizacao' => $localizacao]) }}" class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 hover:bg-green-200">
                                            {{ $total }} {{ $total == 1 ? 'produto' : 'produtos' }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-gray-500">Nenhum produto com localização definida.</p>
                        @endif
                    </div>
                </div>

                <!-- Produtos por Grupo -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Por Grupo <span class="text-sm font-normal text-gray-500">({{ count($estatisticas['produtosPorGrupo']) }})</span></h3>
                        <button id="toggle-grupos" class="flex items-center text-sm text-gray-500 hover:text-gray-700 focus:outline-none" aria-expanded="false" aria-controls="grupos-content">
                            <span class="mr-1">Expandir/Recolher</span>
                            <svg class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </div>
                    <div id="grupos-content" class="px-6 py-0 h-0 overflow-hidden transition-all duration-300 ease-in-out">
                        @if(count($estatisticas['produtosPorGrupo']) > 0)
                            <ul class="divide-y divide-gray-200">
                                @foreach($estatisticas['produtosPorGrupo'] as $grupo => $total)
                                    <li class="py-3 flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-900">{{ $grupo }}</span>
                                        <a href="{{ route('produtos.index', ['marca' => $marca->nome_marca, 'grupo' => $grupo]) }}" class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 hover:bg-purple-200">
                                            {{ $total }} {{ $total == 1 ? 'produto' : 'produtos' }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-gray-500">Nenhum produto associado a um grupo.</p>
                        @endif
                    </div>
                </div>

                <!-- Produtos por Status -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Por Status <span class="text-sm font-normal text-gray-500">({{ count($estatisticas['produtosPorStatus']) }})</span></h3>
                        <button id="toggle-status" class="flex items-center text-sm text-gray-500 hover:text-gray-700 focus:outline-none" aria-expanded="false" aria-controls="status-content">
                            <span class="mr-1">Expandir/Recolher</span>
                            <svg class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </div>
                    <div id="status-content" class="px-6 py-0 h-0 overflow-hidden transition-all duration-300 ease-in-out">
                        @if(count($estatisticas['produtosPorStatus']) > 0)
                            <ul class="divide-y divide-gray-200">
                                @foreach($estatisticas['produtosPorStatus'] as $status => $total)
                                    <li class="py-3 flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-900">{{ $status }}</span>
                                        <a href="{{ route('produtos.index', ['marca' => $marca->nome_marca, 'status' => $status]) }}" class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 hover:bg-blue-200">
                                            {{ $total }} {{ $total == 1 ? 'produto' : 'produtos' }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-gray-500">Nenhum produto com status definido.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        // Ensure script runs after DOM is fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            setupToggles();
        });

        // Fallback to window load event if DOMContentLoaded doesn't trigger properly
        window.addEventListener('load', function() {
            setupToggles();
        });

        function setupToggles() {
            // Prevent duplicate initialization
            if (window.togglesInitialized) return;
            window.togglesInitialized = true;
            
            // Setup toggle functionality for all statistics sections
            var toggles = [
                { button: 'toggle-estilistas', content: 'estilistas-content' },
                { button: 'toggle-localizacoes', content: 'localizacoes-content' },
                { button: 'toggle-grupos', content: 'grupos-content' },
                { button: 'toggle-status', content: 'status-content' }
            ];

            for (var i = 0; i < toggles.length; i++) {
                var button = document.getElementById(toggles[i].button);
                var content = document.getElementById(toggles[i].content);
                
                if (!button || !content) continue; // Skip if elements don't exist
                
                // Make sure all sections start collapsed
                content.style.height = '0';
                content.style.overflow = 'hidden';
                content.style.padding = '0';
                button.setAttribute('aria-expanded', 'false');
                
                // Use closure to preserve references
                (function(btn, cnt) {
                    btn.onclick = function() {
                        var icon = btn.querySelector('svg');
                        
                        // Toggle the content visibility
                        if (cnt.style.height === '0px' || cnt.style.height === '0') {
                            cnt.style.height = 'auto';
                            cnt.style.overflow = 'visible';
                            cnt.style.padding = '1rem 0'; // py-4 equivalent
                            if (icon) icon.style.transform = 'rotate(180deg)';
                            btn.setAttribute('aria-expanded', 'true');
                        } else {
                            cnt.style.height = '0';
                            cnt.style.overflow = 'hidden';
                            cnt.style.padding = '0';
                            if (icon) icon.style.transform = '';
                            btn.setAttribute('aria-expanded', 'false');
                        }
                        
                        // Prevent default action
                        return false;
                    };
                })(button, content);
            }
        }
    </script>
</x-app-layout>
