<?php

namespace App\Http\Controllers;

use App\Models\GrupoProduto;
use App\Models\Localizacao;
use App\Models\Marca;
use App\Models\Movimentacao;
use App\Models\Situacao;
use App\Models\Status;
use App\Models\Tecido;
use App\Models\Tipo;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class MovimentacaoFilterController extends Controller
{
    /**
     * Aplica o filtro por status de dias (Todos, Atrasados, Em Dia)
     *
     * @param Builder $query
     * @param string|null $statusDias
     * @return Builder
     */
    public static function applyStatusDiasFilter(Builder $query, $statusDias)
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
    
    /**
     * Filtra as movimentações por status de dias
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function filtrarPorStatusDias(Request $request)
    {
        if (!auth()->user() || !auth()->user()->canRead('movimentacoes')) {
            abort(403, 'Acesso negado.');
        }
        
        // Verificar se é uma requisição de limpeza de filtros
        if ($request->has('limpar_filtros')) {
            auth()->user()->clearFilters('movimentacoes');
            return redirect()->route('movimentacoes.filtro.status-dias');
        }
        
        // Iniciar a query com os relacionamentos
        $query = Movimentacao::with(['produto', 'produto.marca', 'tipo', 'situacao', 'localizacao']);
        
        // Aplicar os filtros padrão
        if ($request->filled('referencia')) {
            $query->whereHas('produto', function($q) use ($request) {
                $q->where('referencia', 'like', '%' . $request->referencia . '%');
            });
        }

        if ($request->filled('produto')) {
            $query->whereHas('produto', function($q) use ($request) {
                $q->where('descricao', 'like', '%' . $request->produto . '%');
            });
        }

        if ($request->filled('produto_id')) {
            $query->where('produto_id', $request->produto_id);
        }

        if ($request->filled('marca_id')) {
            $query->whereHas('produto', function($q) use ($request) {
                $q->where('marca_id', $request->marca_id);
            });
        }
        
        // Filtro por Grupo de Produto
        if ($request->filled('grupo_produto_id')) {
            $grupoProdutoIds = is_array($request->grupo_produto_id) ? $request->grupo_produto_id : [$request->grupo_produto_id];
            $query->whereHas('produto', function($q) use ($grupoProdutoIds) {
                $q->whereIn('grupo_id', $grupoProdutoIds);
            });
        }

        if ($request->filled('status_id')) {
            $query->whereHas('produto', function($q) use ($request) {
                $q->where('status_id', $request->status_id);
            });
        }
        
        if ($request->filled('tecido_id')) {
            $tecidoIds = is_array($request->tecido_id) ? $request->tecido_id : [$request->tecido_id];
            $query->whereHas('produto', function($q) use ($tecidoIds) {
                $q->whereHas('tecidos', function($tq) use ($tecidoIds) {
                    $tq->whereIn('tecidos.id', $tecidoIds);
                });
            });
        }

        if ($request->filled('tipo_id')) {
            $query->where('tipo_id', $request->tipo_id);
        }

        if ($request->filled('situacao_id')) {
            $situacaoIds = is_array($request->situacao_id) ? $request->situacao_id : [$request->situacao_id];
            $query->whereIn('situacao_id', $situacaoIds);
        }

        if ($request->filled('localizacao_id')) {
            $localizacaoIds = is_array($request->localizacao_id) ? $request->localizacao_id : [$request->localizacao_id];
            $query->whereIn('localizacao_id', $localizacaoIds);
        }

        // Filtros de data
        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $query->whereBetween('data_entrada', [$request->data_inicio, $request->data_fim]);
        } elseif ($request->filled('data_inicio')) {
            $query->where('data_entrada', '>=', $request->data_inicio);
        } elseif ($request->filled('data_fim')) {
            $query->where('data_entrada', '<=', $request->data_fim);
        }

        if ($request->filled('comprometido')) {
            $query->where('comprometido', $request->comprometido);
        }
        
        if ($request->filled('concluido')) {
            $query->where('concluido', $request->concluido);
        }
        
        // Aplicar o filtro por status de dias
        if ($request->filled('status_dias')) {
            $query = self::applyStatusDiasFilter($query, $request->status_dias);
        }
        
        // Ordenação
        if ($request->filled('sort') && $request->filled('direction')) {
            $sortField = $request->sort;
            $direction = $request->direction;

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
        
        // Paginar os resultados
        $movimentacoes = $query->paginate(15)->withQueryString();
        
        // Carregar dados para os selects
        $situacoes = Situacao::orderBy('descricao')->get();
        $tipos = Tipo::orderBy('descricao')->get();
        $localizacoes = Localizacao::orderBy('nome_localizacao')->get();
        $status = Status::orderBy('descricao')->get();
        $marcas = Marca::orderBy('nome_marca')->get();
        $tecidos = Tecido::orderBy('descricao')->get();
        $grupoProdutos = GrupoProduto::orderBy('descricao')->get();
        
        // Lista de campos de filtro válidos
        $validFilters = [
            'referencia', 'produto', 'produto_id', 'marca_id', 'status_id', 
            'tecido_id', 'tipo_id', 'situacao_id', 'localizacao_id', 'data_inicio', 'data_fim',
            'comprometido', 'concluido', 'sort', 'direction', 'status_dias', 'grupo_produto_id'
        ];
        
        // Se tem parâmetros de filtro na URL, salvar como filtros do usuário
        if ($request->anyFilled($validFilters)) {
            $filterParams = $request->only($validFilters);
            auth()->user()->saveFilters('movimentacoes', $filterParams);
        } 
        // Se não tem parâmetros na URL mas tem filtros salvos, redirecionar com os filtros salvos
        else if (!$request->hasAny($validFilters) && !$request->ajax()) {
            $savedFilters = auth()->user()->getFilters('movimentacoes');
            
            if (!empty($savedFilters)) {
                return redirect()->route('movimentacoes.filtro.status-dias', $savedFilters);
            }
        }
        
        $filters = $request->all();
        
        return view('movimentacoes.index', compact(
            'movimentacoes', 'situacoes', 'tipos', 'localizacoes', 
            'status', 'marcas', 'tecidos', 'grupoProdutos', 'filters'
        ));
    }
}
