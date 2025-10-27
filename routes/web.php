<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TecidoController;
use App\Http\Controllers\EstilistaController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\GrupoProdutoController;
use App\Http\Controllers\LocalizacaoController;
use App\Http\Controllers\LocalizacaoCapacidadeMensalController;
use App\Http\Controllers\TipoController;
use App\Http\Controllers\SituacaoController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\ProdutoAnexoController;
use App\Http\Controllers\ProdutoLocalizacaoController;
use App\Http\Controllers\ProdutoObservacaoController;
use App\Http\Controllers\ProdutoCombinacaoController;
use App\Http\Controllers\MovimentacaoController;
use App\Http\Controllers\MovimentacaoFilterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActivityLogController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified', \App\Http\Middleware\CheckUserAccessSchedule::class])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/dados-por-ano', [DashboardController::class, 'getDadosPorAno'])->name('dashboard.dados-por-ano');
    Route::get('/dashboard/produtos-por-estilista', [DashboardController::class, 'produtosPorEstilista'])->name('dashboard.produtos-por-estilista');

    // Rota para servir arquivos de rede
    Route::get('/arquivo/rede', [\App\Http\Controllers\ArquivoController::class, 'servirArquivoRede'])->name('arquivo.rede');

    // Rotas para gerenciamento de usuários (apenas para administradores)
    Route::middleware(\App\Http\Middleware\AdminMiddleware::class)->group(function () {
        Route::resource('users', \App\Http\Controllers\UserController::class);

        // Rotas para gerenciamento de grupos de usuários
        Route::get('user-groups', [\App\Http\Controllers\UserGroupController::class, 'index'])->name('user-groups.index');
        Route::get('user-groups/{user}/edit', [\App\Http\Controllers\UserGroupController::class, 'edit'])->name('user-groups.edit');
        Route::put('user-groups/{user}', [\App\Http\Controllers\UserGroupController::class, 'update'])->name('user-groups.update');
        Route::get('user-groups/{user}/permissions', [\App\Http\Controllers\UserGroupController::class, 'showPermissions'])->name('user-groups.permissions');

        // Rotas para gerenciamento de permissões
        Route::resource('permissions', \App\Http\Controllers\PermissionController::class);

        // Rotas para permissões específicas por usuário (granulares)
        Route::get('user-permissions/{user}/edit', [\App\Http\Controllers\UserPermissionController::class, 'edit'])->name('user-permissions.edit');
        Route::put('user-permissions/{user}', [\App\Http\Controllers\UserPermissionController::class, 'update'])->name('user-permissions.update');

        // Rotas para horários de acesso do usuário
        Route::get('user-access-schedules/{user}/edit', [\App\Http\Controllers\UserAccessScheduleController::class, 'edit'])->name('user-access-schedules.edit');
        Route::put('user-access-schedules/{user}', [\App\Http\Controllers\UserAccessScheduleController::class, 'update'])->name('user-access-schedules.update');

        // ALIAS legados (compatibilidade com views antigas que usam underscore nos nomes das rotas)
        Route::get('user_permissions/{user}/edit', [\App\Http\Controllers\UserPermissionController::class, 'edit'])->name('user_permissions.edit');
        Route::put('user_permissions/{user}', [\App\Http\Controllers\UserPermissionController::class, 'update'])->name('user_permissions.update');
        Route::get('user_permissions', [\App\Http\Controllers\UserController::class, 'index'])->name('user_permissions.index');

        // Rotas para visualização de logs
        Route::get('logs', [\App\Http\Controllers\LogController::class, 'index'])->name('logs.index');
        Route::get('logs/{filename}', [\App\Http\Controllers\LogController::class, 'show'])->name('logs.show');
        Route::get('logs/{filename}/download', [\App\Http\Controllers\LogController::class, 'download'])->name('logs.download');
        Route::delete('logs/{filename}', [\App\Http\Controllers\LogController::class, 'destroy'])->name('logs.destroy');

        // Rotas para o log de atividades
        Route::get('activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
        Route::get('activity-log/{id}', [ActivityLogController::class, 'show'])->name('activity-log.show');

        // Alias plurais para compatibilidade com views que usam activity-logs.*
        Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('activity-logs/{id}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
    });

    // Routes para Tecidos
    Route::resource('tecidos', TecidoController::class);
    Route::get('tecidos/{tecido}/atualizar-estoque', [TecidoController::class, 'atualizarEstoque'])->name('tecidos.atualizar-estoque');
    Route::get('tecidos-atualizar-todos-estoques', [TecidoController::class, 'atualizarTodosEstoques'])->name('tecidos.atualizar-todos-estoques');
    Route::get('tecidos/{tecido}/estoque-por-cor', [TecidoController::class, 'estoquePorCor'])->name('tecidos.estoque-por-cor');
    Route::get('tecidos/{tecidoId}/cores/{corId}/produtos', [TecidoController::class, 'produtosPorCor'])->name('tecidos.produtos-por-cor');
    Route::post('tecidos/{tecido}/salvar-quantidades', [TecidoController::class, 'salvarQuantidades'])->name('tecidos.salvar-quantidades');
    Route::get('tecidos-importar-estoque', [TecidoController::class, 'importarEstoqueForm'])->name('tecidos.importar-estoque-form');
    Route::post('tecidos-importar-estoque', [TecidoController::class, 'importarEstoque'])->name('tecidos.importar-estoque');
    Route::get('debug-estoque/{referencia}', [TecidoController::class, 'debugEstoque'])->name('tecidos.debug-estoque');

    // Routes para Estilistas
    Route::resource('estilistas', EstilistaController::class);

    // Routes para Marcas
    Route::resource('marcas', MarcaController::class);

    // Routes para Grupo de Produtos
    Route::resource('grupo_produtos', GrupoProdutoController::class);

    // Routes para Localização
    Route::get('localizacoes/pdf/gerar', [LocalizacaoController::class, 'gerarPdf'])->name('localizacoes.pdf');
    Route::put('localizacoes/{id}/restore', [LocalizacaoController::class, 'restore'])->name('localizacoes.restore');
    Route::resource('localizacoes', LocalizacaoController::class);

    // Routes para Capacidade Mensal das Localizações
    Route::get('localizacao-capacidade/dashboard', [LocalizacaoCapacidadeMensalController::class, 'dashboard'])->name('localizacao-capacidade.dashboard');
    Route::get('localizacao-capacidade/relatorio-pdf', [LocalizacaoCapacidadeMensalController::class, 'gerarRelatorioPDF'])->name('localizacao-capacidade.relatorio-pdf');
    Route::get('localizacao-capacidade/listagem-pdf', [LocalizacaoCapacidadeMensalController::class, 'gerarPDFListagem'])->name('localizacao-capacidade.listagem-pdf');
    Route::get('localizacao-capacidade/historico-redistribuicoes', [LocalizacaoCapacidadeMensalController::class, 'historicoRedistribuicoes'])->name('localizacao-capacidade.historico-redistribuicoes');
    Route::post('localizacao-capacidade/sugerir-redistribuicao', [LocalizacaoCapacidadeMensalController::class, 'sugerirRedistribuicao'])->name('localizacao-capacidade.sugerir-redistribuicao');
    Route::post('localizacao-capacidade/aplicar-redistribuicao', [LocalizacaoCapacidadeMensalController::class, 'aplicarRedistribuicao'])->name('localizacao-capacidade.aplicar-redistribuicao');
    Route::post('localizacao-capacidade/reverter-redistribuicao', [LocalizacaoCapacidadeMensalController::class, 'reverterRedistribuicao'])->name('localizacao-capacidade.reverter-redistribuicao');
    Route::post('localizacao-capacidade/gerar-mes', [LocalizacaoCapacidadeMensalController::class, 'gerarCapacidadesMes'])->name('localizacao-capacidade.gerar-mes');
    Route::resource('localizacao-capacidade', LocalizacaoCapacidadeMensalController::class);

    // Routes para Tipos
    Route::resource('tipos', TipoController::class);
    Route::post('tipos/{tipo}/restore', [TipoController::class, 'restore'])->name('tipos.restore');
    Route::delete('tipos/{tipo}/force-delete', [TipoController::class, 'forceDelete'])->name('tipos.force-delete');

    // Routes para Situações
    Route::resource('situacoes', SituacaoController::class);
    Route::post('situacoes/{situacao}/restore', [SituacaoController::class, 'restore'])->name('situacoes.restore');
    Route::delete('situacoes/{situacao}/force-delete', [SituacaoController::class, 'forceDelete'])->name('situacoes.force-delete');

    // Routes para Status
    Route::resource('status', StatusController::class);

    // Routes para Produtos
    Route::resource('produtos', ProdutoController::class);
    Route::get('produtos/{id}/pdf', [ProdutoController::class, 'generatePdf'])->name('produtos.pdf');
    Route::post('produtos/get-available-colors', [ProdutoController::class, 'getAvailableColors'])->name('produtos.get-available-colors');
    Route::get('produtos-inconsistencias', [ProdutoController::class, 'inconsistencias'])->name('produtos.inconsistencias');
    Route::get('produtos-lista-pdf', [ProdutoController::class, 'generateListPdf'])->name('produtos.lista.pdf');
    Route::post('produtos/{id}/reprogramar', [ProdutoController::class, 'reprogramar'])->name('produtos.reprogramar');

    // Routes para Anexos de Produtos
    Route::post('produtos/{produto}/anexos', [ProdutoAnexoController::class, 'store'])->name('produtos.anexos.store');
    Route::delete('produtos/anexos/{anexo}', [ProdutoAnexoController::class, 'destroy'])->name('produtos.anexos.destroy');
    
    // Routes para Localizações de Produtos
    Route::post('produtos/{produto}/localizacoes', [ProdutoLocalizacaoController::class, 'store'])->name('produtos.localizacoes.store');
    Route::put('produtos/{produto}/localizacoes/{produtoLocalizacao}', [ProdutoLocalizacaoController::class, 'update'])->name('produtos.localizacoes.update');
    Route::delete('produtos/{produto}/localizacoes/{produtoLocalizacao}', [ProdutoLocalizacaoController::class, 'destroy'])->name('produtos.localizacoes.destroy');
    
    // Routes para Observações de Produtos
    Route::post('produtos/observacoes', [ProdutoObservacaoController::class, 'store'])->name('produtos.observacoes.store');
    Route::delete('produtos/observacoes/{observacao}', [ProdutoObservacaoController::class, 'destroy'])->name('produtos.observacoes.destroy');
    
    // Routes para Combinações de Produtos
    Route::post('produtos/{produto}/combinacoes', [ProdutoCombinacaoController::class, 'store'])->name('produtos.combinacoes.store');
    Route::put('produtos/combinacoes/{combinacao}', [ProdutoCombinacaoController::class, 'update'])->name('produtos.combinacoes.update');
    Route::delete('produtos/combinacoes/{combinacao}', [ProdutoCombinacaoController::class, 'destroy'])->name('produtos.combinacoes.destroy');
    Route::get('produtos/{produto}/combinacoes', [ProdutoCombinacaoController::class, 'getCombinacoes'])->name('produtos.combinacoes.get');
    
    // Routes para Componentes de Combinações
    Route::post('combinacoes/{combinacao}/componentes', [ProdutoCombinacaoController::class, 'addComponente'])->name('combinacoes.componentes.add');
    Route::put('combinacoes/componentes/{componente}', [ProdutoCombinacaoController::class, 'updateComponente'])->name('combinacoes.componentes.update');
    Route::delete('combinacoes/componentes/{componente}', [ProdutoCombinacaoController::class, 'removeComponente'])->name('combinacoes.componentes.remove');
    Route::get('tecidos/{tecido}/cores', [ProdutoCombinacaoController::class, 'getTecidoCores'])->name('tecidos.cores.get');

    // Routes para Movimentações
    Route::get('movimentacoes/minhas', [MovimentacaoController::class, 'minhasMovimentacoes'])->name('movimentacoes.minhas');
    Route::resource('movimentacoes', MovimentacaoController::class)->parameters([
        'movimentacoes' => 'movimentacao'
    ]);
    Route::delete('movimentacoes/{movimentacao}/remover-anexo', [MovimentacaoController::class, 'removerAnexo'])->name('movimentacoes.remover-anexo');
    Route::get('movimentacoes/{movimentacao}/pdf', [MovimentacaoController::class, 'generatePdf'])->name('movimentacoes.pdf');
    Route::get('movimentacoes-lista-pdf', [MovimentacaoController::class, 'generateListPdf'])->name('movimentacoes.lista.pdf');
    
    // Filtro de Movimentações por Status de Dias
    Route::get('movimentacoes/filtro/status-dias', [MovimentacaoFilterController::class, 'filtrarPorStatusDias'])->name('movimentacoes.filtro.status-dias');

    // Routes para Consultas
    Route::get('consultas/produtos-ativos-por-localizacao', [\App\Http\Controllers\ConsultaController::class, 'produtosAtivosPorLocalizacao'])->name('consultas.produtos-ativos-por-localizacao');
    Route::get('consultas/media-dias-atraso', [DashboardController::class, 'mediaDiasAtraso'])->name('consultas.media-dias-atraso');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
