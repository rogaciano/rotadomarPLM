<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Select2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Styles personalizados -->
        @stack('styles')
    </head>
    <body class="font-sans antialiased">
        <!-- Loading Overlay for Specific Routes -->
        <div id="global-loading-overlay" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
            <div class="bg-white p-6 rounded-lg shadow-xl max-w-md w-full">
                <div class="flex items-center justify-center mb-4">
                    <svg class="animate-spin h-8 w-8 text-indigo-600 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-lg font-medium">Processando dados...</span>
                </div>
                <p class="text-gray-600 text-center">Esta consulta pode levar alguns instantes. Por favor, aguarde.</p>
            </div>
        </div>
        
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Flash Messages -->
            <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8">
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 relative" role="alert">
                        <p>{{ session('success') }}</p>
                        <button type="button" class="absolute top-0 right-0 mt-3 mr-3 text-green-700 hover:text-green-900" onclick="this.parentElement.style.display='none'">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 relative" role="alert">
                        <p>{{ session('error') }}</p>
                        <button type="button" class="absolute top-0 right-0 mt-3 mr-3 text-red-700 hover:text-red-900" onclick="this.parentElement.style.display='none'">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                @endif
            </div>
            
            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        
        <!-- jQuery (necessário para o Select2) -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        
        <!-- Select2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        
        <script>
            // Mostrar overlay de carregamento para consultas específicas
            document.addEventListener('DOMContentLoaded', function() {
                // Encontrar o link da consulta por localização
                const consultaLocalizacaoLinks = document.querySelectorAll('a[href*="consultas.produtos-ativos-por-localizacao"]');
                
                consultaLocalizacaoLinks.forEach(function(link) {
                    link.addEventListener('click', function(e) {
                        // Mostrar o overlay de carregamento
                        document.getElementById('global-loading-overlay').style.display = 'flex';
                    });
                });
            });
        </script>
        
        <!-- Scripts personalizados -->
        @stack('scripts')
    </body>
</html>
