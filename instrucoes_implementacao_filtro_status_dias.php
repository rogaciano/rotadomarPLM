<?php
/**
 * INSTRUÇÕES PARA IMPLEMENTAR O FILTRO POR STATUS DE DIAS NO MOVIMENTACAOCONTROLLER
 * 
 * Como não foi possível editar diretamente o arquivo MovimentacaoController.php devido a 
 * limitações técnicas, siga estas instruções para implementar o filtro manualmente.
 */

/**
 * PASSO 1: Importe o trait MovimentacaoFilters no MovimentacaoController
 * 
 * Adicione a seguinte linha após os outros imports no início do arquivo:
 */

// use App\Traits\MovimentacaoFilters;

/**
 * PASSO 2: Adicione o trait à classe MovimentacaoController
 * 
 * Modifique a declaração da classe para incluir o trait:
 */

// class MovimentacaoController extends Controller
// {
//     use MovimentacaoFilters;
//     
//     // resto do código...

/**
 * PASSO 3: Adicione o filtro por status de dias no método index
 * 
 * Localize o seguinte trecho de código no método index:
 */

// // Adicionar filtro para o campo concluido
// if ($request->filled('concluido')) {
//     $query->where('concluido', $request->concluido);
// }

/**
 * PASSO 4: Adicione o código para o filtro por status de dias logo após o trecho acima:
 */

// // Filtro por status de dias (Atrasados, Em Dia)
// if ($request->filled('status_dias')) {
//     $query = $this->applyStatusDiasFilter($query, $request->status_dias);
// }

/**
 * PASSO 5: Faça o mesmo no método generateListPdf
 * 
 * Localize o seguinte trecho de código no método generateListPdf:
 */

// // Adicionar filtro para o campo concluido
// if ($request->filled('concluido')) {
//     $query->where('concluido', $request->concluido);
// }

/**
 * PASSO 6: Adicione o código para o filtro por status de dias logo após o trecho acima:
 */

// // Filtro por status de dias (Atrasados, Em Dia)
// if ($request->filled('status_dias')) {
//     $query = $this->applyStatusDiasFilter($query, $request->status_dias);
// }

/**
 * ALTERNATIVA: Se preferir não usar o trait, você pode adicionar o código diretamente nos métodos
 * 
 * Adicione o seguinte código após o filtro de concluido em ambos os métodos (index e generateListPdf):
 */

// // Filtro por status de dias (Atrasados, Em Dia)
// if ($request->filled('status_dias')) {
//     $statusDias = $request->status_dias;
//     
//     if ($statusDias === 'atrasados') {
//         // Subconsulta para obter movimentações atrasadas
//         $query->whereHas('localizacao', function($q) {
//             // Localizações com prazo definido
//             $q->whereNotNull('prazo');
//         })
//         ->where(function($q) {
//             $q->whereNull('data_saida') // Ainda não concluídas
//               ->whereRaw('DATEDIFF(NOW(), data_entrada) > (SELECT prazo FROM localizacoes WHERE localizacoes.id = movimentacoes.localizacao_id)');
//         });
//     } 
//     elseif ($statusDias === 'em_dia') {
//         // Subconsulta para obter movimentações em dia
//         $query->where(function($q) {
//             $q->whereNotNull('data_saida') // Já concluídas
//               ->orWhere(function($sq) {
//                   $sq->whereNull('data_saida') // Não concluídas mas dentro do prazo
//                      ->whereHas('localizacao', function($lq) {
//                          $lq->whereNotNull('prazo');
//                      })
//                      ->whereRaw('DATEDIFF(NOW(), data_entrada) <= (SELECT prazo FROM localizacoes WHERE localizacoes.id = movimentacoes.localizacao_id)');
//               })
//               ->orWhereHas('localizacao', function($lq) {
//                   $lq->whereNull('prazo'); // Localizações sem prazo definido
//               });
//         });
//     }
// }

/**
 * OBSERVAÇÃO: Você também pode usar a view SQL criada no arquivo adicionar_filtro_status_dias.sql
 * como uma alternativa para implementar este filtro.
 */
