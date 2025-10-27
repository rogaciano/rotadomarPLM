<?php

namespace App\Traits;

trait MovimentacaoFilters
{
    /**
     * Aplica o filtro por status de dias (Todos, Atrasados, Em Dia)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $statusDias
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyStatusDiasFilter($query, $statusDias)
    {
        if (!$statusDias) {
            return $query;
        }
        
        if ($statusDias === 'atrasados') {
            // Subconsulta para obter movimentações atrasadas
            return $query->whereHas('localizacao', function($q) {
                // Localizações com prazo definido
                $q->whereNotNull('prazo');
            })
            ->where(function($q) {
                $q->whereNull('data_saida') // Ainda não concluídas
                  ->whereRaw('DATEDIFF(NOW(), data_entrada) > (SELECT prazo FROM localizacoes WHERE localizacoes.id = movimentacoes.localizacao_id)');
            });
        } 
        elseif ($statusDias === 'em_dia') {
            // Subconsulta para obter movimentações em dia
            return $query->where(function($q) {
                $q->whereNotNull('data_saida') // Já concluídas
                  ->orWhere(function($sq) {
                      $sq->whereNull('data_saida') // Não concluídas mas dentro do prazo
                         ->whereHas('localizacao', function($lq) {
                             $lq->whereNotNull('prazo');
                         })
                         ->whereRaw('DATEDIFF(NOW(), data_entrada) <= (SELECT prazo FROM localizacoes WHERE localizacoes.id = movimentacoes.localizacao_id)');
                  })
                  ->orWhereHas('localizacao', function($lq) {
                      $lq->whereNull('prazo'); // Localizações sem prazo definido
                  });
            });
        }
        
        return $query;
    }
}
