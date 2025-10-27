<?php

namespace App\Helpers;

use App\Models\Movimentacao;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class MovimentacaoHelper
{
    /**
     * Obtém movimentações com filtro por status de dias
     *
     * @param array $filters Filtros da requisição
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getMovimentacoesComFiltro($filters = [])
    {
        $query = Movimentacao::with(['produto', 'produto.marca', 'tipo', 'situacao', 'localizacao']);
        
        // Aplicar os filtros padrão
        if (!empty($filters['referencia'])) {
            $query->whereHas('produto', function($q) use ($filters) {
                $q->where('referencia', 'like', '%' . $filters['referencia'] . '%');
            });
        }

        if (!empty($filters['produto'])) {
            $query->whereHas('produto', function($q) use ($filters) {
                $q->where('descricao', 'like', '%' . $filters['produto'] . '%');
            });
        }

        if (!empty($filters['produto_id'])) {
            $query->where('produto_id', $filters['produto_id']);
        }

        if (!empty($filters['marca_id'])) {
            $query->whereHas('produto', function($q) use ($filters) {
                $q->where('marca_id', $filters['marca_id']);
            });
        }

        if (!empty($filters['status_id'])) {
            $query->whereHas('produto', function($q) use ($filters) {
                $q->where('status_id', $filters['status_id']);
            });
        }
        
        if (!empty($filters['tecido_id'])) {
            $query->whereHas('produto', function($q) use ($filters) {
                $q->whereHas('tecidos', function($tq) use ($filters) {
                    $tq->where('tecidos.id', $filters['tecido_id']);
                });
            });
        }

        if (!empty($filters['tipo_id'])) {
            $query->where('tipo_id', $filters['tipo_id']);
        }

        if (!empty($filters['situacao_id'])) {
            $query->where('situacao_id', $filters['situacao_id']);
        }

        if (!empty($filters['localizacao_id'])) {
            $query->where('localizacao_id', $filters['localizacao_id']);
        }

        // Filtros de data
        if (!empty($filters['data_inicio']) && !empty($filters['data_fim'])) {
            $query->whereBetween('data_entrada', [$filters['data_inicio'], $filters['data_fim']]);
        } elseif (!empty($filters['data_inicio'])) {
            $query->where('data_entrada', '>=', $filters['data_inicio']);
        } elseif (!empty($filters['data_fim'])) {
            $query->where('data_entrada', '<=', $filters['data_fim']);
        }

        if (isset($filters['comprometido']) && $filters['comprometido'] !== '') {
            $query->where('comprometido', $filters['comprometido']);
        }
        
        if (isset($filters['concluido']) && $filters['concluido'] !== '') {
            $query->where('concluido', $filters['concluido']);
        }
        
        // Filtro por status de dias (Atrasados, Em Dia)
        // NOTA: Os filtros usam DATEDIFF (dias corridos) por performance no banco de dados.
        // A exibição na view usa calcularDiasUteis() para mostrar dias úteis (excluindo fins de semana).
        if (!empty($filters['status_dias'])) {
            $statusDias = $filters['status_dias'];
            
            if ($statusDias === 'atrasados') {
                // Subconsulta para obter movimentações atrasadas
                $query->whereHas('localizacao', function($q) {
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
                $query->where(function($q) {
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
        }

        // Ordenação
        if (!empty($filters['sort']) && !empty($filters['direction'])) {
            $sortField = $filters['sort'];
            $direction = $filters['direction'];

            // Mapear os campos de ordenação para as colunas corretas no banco de dados
            switch ($sortField) {
                case 'produto':
                    $query->join('produtos', 'movimentacoes.produto_id', '=', 'produtos.id')
                          ->orderBy('produtos.descricao', $direction)
                          ->select('movimentacoes.*'); // Evitar ambiguidade de colunas
                    break;
                case 'tipo':
                    $query->join('tipos', 'movimentacoes.tipo_id', '=', 'tipos.id')
                          ->orderBy('tipos.descricao', $direction)
                          ->select('movimentacoes.*');
                    break;
                case 'situacao':
                    $query->join('situacoes', 'movimentacoes.situacao_id', '=', 'situacoes.id')
                          ->orderBy('situacoes.descricao', $direction)
                          ->select('movimentacoes.*');
                    break;
                case 'localizacao':
                    $query->join('localizacoes', 'movimentacoes.localizacao_id', '=', 'localizacoes.id')
                          ->orderBy('localizacoes.nome_localizacao', $direction)
                          ->select('movimentacoes.*');
                    break;
                default:
                    // Para campos diretos da tabela movimentacoes
                    if (in_array($sortField, ['data_entrada', 'data_saida', 'data_devolucao', 'comprometido', 'observacao', 'created_at', 'concluido'])) {
                        $query->orderBy($sortField, $direction);
                    } else {
                        // Ordenação padrão se o campo não for reconhecido
                        $query->orderBy('created_at', 'desc');
                    }
                    break;
            }
        } else {
            // Ordenação padrão
            $query->orderBy('created_at', 'desc');
        }
        
        return $query;
    }

    /**
     * Calcula dias úteis entre duas datas (excluindo sábados e domingos)
     *
     * @param \Carbon\Carbon $dataInicio Data de início
     * @param \Carbon\Carbon|null $dataFim Data fim (se null, usa data atual)
     * @return int|null Número de dias úteis
     */
    public static function calcularDiasUteis($dataInicio, $dataFim = null)
    {
        if (!$dataInicio) {
            return null;
        }

        if (!$dataFim) {
            $dataFim = now();
        }

        $diasUteis = 0;
        $dataAtual = clone $dataInicio;

        while ($dataAtual <= $dataFim) {
            // 6 = sábado, 0 = domingo
            $diaDaSemana = $dataAtual->dayOfWeek;
            if ($diaDaSemana != 0 && $diaDaSemana != 6) {
                $diasUteis++;
            }
            $dataAtual->addDay();
        }

        return $diasUteis;
    }
}
