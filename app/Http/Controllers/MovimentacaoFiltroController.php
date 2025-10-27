<?php

namespace App\Http\Controllers;

use App\Helpers\MovimentacaoHelper;
use App\Models\Localizacao;
use App\Models\Marca;
use App\Models\Movimentacao;
use App\Models\Produto;
use App\Models\Situacao;
use App\Models\Status;
use App\Models\Tecido;
use App\Models\Tipo;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class MovimentacaoFiltroController extends Controller
{
    /**
     * Display a listing of the resource with filters.
     */
    public function index(Request $request)
    {
        if (!auth()->user() || !auth()->user()->canRead('movimentacoes')) {
            abort(403, 'Acesso negado.');
        }
        
        // Verificar se é uma requisição de limpeza de filtros
        if ($request->has('limpar_filtros')) {
            auth()->user()->clearFilters('movimentacoes');
            return redirect()->route('movimentacoes.filtro');
        }
        
        // Lista de campos de filtro válidos
        $validFilters = [
            'referencia', 'produto', 'produto_id', 'marca_id', 'status_id', 'tecido_id',
            'tipo_id', 'situacao_id', 'localizacao_id', 'data_inicio', 'data_fim',
            'comprometido', 'concluido', 'sort', 'direction', 'status_dias'
        ];
        
        // Se tem parâmetros de filtro na URL, salvar como filtros do usuário
        if ($request->anyFilled($validFilters)) {
            $filterParams = $request->only($validFilters);
            auth()->user()->saveFilters('movimentacoes', $filterParams);
        } 
        // Se não tem parâmetros na URL mas tem filtros salvos, redirecionar com os filtros salvos
        else if (!$request->hasAny($validFilters)) {
            $savedFilters = auth()->user()->getFilters('movimentacoes');
            
            if (!empty($savedFilters)) {
                return redirect()->route('movimentacoes.filtro', $savedFilters);
            }
        }
        
        // Usar os filtros da requisição
        $filters = $request->all();
        
        // Obter a query com os filtros aplicados
        $query = MovimentacaoHelper::getMovimentacoesComFiltro($filters);
        
        // Paginar os resultados
        $movimentacoes = $query->paginate(15)->withQueryString();
        
        // Carregar dados para os selects
        $produtos = Produto::orderBy('referencia')->get();
        
        // Carregar situações para o select
        $situacoesAtivas = Situacao::where('ativo', true)->orderBy('descricao')->get();
        $situacoesInativas = Situacao::where('ativo', false)->orderBy('descricao')->get();
        $situacoes = $situacoesAtivas->concat($situacoesInativas);
        
        // Carregar tipos para o select
        $tiposAtivos = Tipo::where('ativo', true)->orderBy('descricao')->get();
        $tiposInativos = Tipo::where('ativo', false)->orderBy('descricao')->get();
        $tipos = $tiposAtivos->concat($tiposInativos);
        
        // Carregar localizações para o select
        $localizacoesAtivas = Localizacao::where('ativo', true)->orderBy('nome_localizacao')->get();
        $localizacoesInativas = Localizacao::where('ativo', false)->orderBy('nome_localizacao')->get();
        $localizacoes = $localizacoesAtivas->concat($localizacoesInativas);
        
        $status = Status::where('ativo', true)->orderBy('descricao')->get();
        $marcas = Marca::where('ativo', true)->orderBy('nome_marca')->get();
        $tecidos = Tecido::where('ativo', true)->orderBy('descricao')->get();
        
        return view('movimentacoes.index', compact(
            'movimentacoes', 'produtos', 'situacoes', 'tipos', 'localizacoes', 
            'status', 'marcas', 'tecidos', 'filters'
        ));
    }
    
    /**
     * Gerar PDF da lista de movimentações filtradas
     */
    public function generateListPdf(Request $request)
    {
        if (!auth()->user() || !auth()->user()->canRead('movimentacoes')) {
            abort(403, 'Acesso negado.');
        }
        
        // Obter a query com os filtros aplicados
        $query = MovimentacaoHelper::getMovimentacoesComFiltro($request->all());
        
        // Obter todos os resultados
        $movimentacoes = $query->get();
        
        $pdf = PDF::loadView('movimentacoes.pdf_list', compact('movimentacoes'));
        return $pdf->stream('lista_movimentacoes.pdf');
    }
}
