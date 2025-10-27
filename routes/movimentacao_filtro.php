<?php

use App\Http\Controllers\MovimentacaoFiltroController;
use Illuminate\Support\Facades\Route;

// Rotas para o filtro de movimentações
Route::middleware(['auth'])->group(function () {
    Route::get('/movimentacoes/filtro', [MovimentacaoFiltroController::class, 'index'])->name('movimentacoes.filtro');
    Route::get('/movimentacoes/filtro/pdf', [MovimentacaoFiltroController::class, 'generateListPdf'])->name('movimentacoes.filtro.pdf');
});
