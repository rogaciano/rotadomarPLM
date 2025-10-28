<?php

namespace App\Http\Controllers;

use App\Helpers\MovimentacaoHelper;
use App\Http\Controllers\MovimentacaoFilterController;
use App\Models\GrupoProduto;
use App\Models\Localizacao;
use App\Models\Marca;
use App\Models\Movimentacao;
use App\Models\Produto;
use App\Models\Situacao;
use App\Models\Status;
use App\Models\Tecido;
use App\Models\Tipo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class MovimentacaoController extends Controller
{
    /**
     * Exibir movimentações pendentes da localização do usuário logado
     */
    public function minhasMovimentacoes()
    {
        if (!auth()->user() || !auth()->user()->canRead('movimentacoes')) {
            abort(403, 'Acesso negado.');
        }
        
        $user = auth()->user();
        
        // Verificar se o usuário tem uma localização atribuída
        if (!$user->localizacao_id) {
            return view('movimentacoes.minhas', [
                'movimentacoes' => collect(),
                'localizacao' => null,
                'totalPendentes' => 0,
                'totalAtrasadas' => 0,
            ]);
        }
        
        // Carregar localização do usuário
        $user->load('localizacao');
        $localizacao = $user->localizacao;
        
        // Obter todas as movimentações pendentes
        $movimentacoes = $user->getMovimentacoesPendentes();
        
        // Calcular dias e status de atraso para cada movimentação
        $movimentacoes->each(function ($movimentacao) use ($localizacao) {
            // Usar dias úteis ao invés de dias corridos
            $movimentacao->dias_decorridos = MovimentacaoHelper::calcularDiasUteis($movimentacao->data_entrada);
            
            if ($localizacao->prazo) {
                $movimentacao->esta_atrasado = $movimentacao->dias_decorridos > $localizacao->prazo;
                $movimentacao->dias_restantes = (int) ($localizacao->prazo - $movimentacao->dias_decorridos);
            } else {
                $movimentacao->esta_atrasado = false;
                $movimentacao->dias_restantes = null;
            }
        });
        
        // Ordenar por dias decorridos (mais atrasadas primeiro)
        $movimentacoes = $movimentacoes->sortByDesc('dias_decorridos');
        
        // Contar totais
        $totalPendentes = $movimentacoes->count();
        $totalAtrasadas = $movimentacoes->where('esta_atrasado', true)->count();
        
        return view('movimentacoes.minhas', compact('movimentacoes', 'localizacao', 'totalPendentes', 'totalAtrasadas'));
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!auth()->user() || !auth()->user()->canRead('movimentacoes')) {
            abort(403, 'Acesso negado.');
        }
        
        // Verificar se é uma requisição de limpeza de filtros
        if ($request->has('limpar_filtros')) {
            auth()->user()->clearFilters('movimentacoes');
            return redirect()->route('movimentacoes.filtro.status-dias');
        }
        
        // Se tem parâmetros de filtro na URL, salvar como filtros do usuário
        if ($request->anyFilled([
            'referencia', 'produto', 'produto_id', 'marca_id', 'status_id', 'tecido_id',
            'tipo_id', 'situacao_id', 'localizacao_id', 'data_inicio', 'data_fim',
            'comprometido', 'concluido', 'sort', 'direction', 'status_dias', 'grupo_produto_id'
        ])) {
            $filterParams = $request->only([
                'referencia', 'produto', 'produto_id', 'marca_id', 'status_id', 'tecido_id',
                'tipo_id', 'situacao_id', 'localizacao_id', 'data_inicio', 'data_fim',
                'comprometido', 'concluido', 'sort', 'direction', 'status_dias', 'grupo_produto_id'
            ]);
            
            auth()->user()->saveFilters('movimentacoes', $filterParams);
        } 
        // Se não tem parâmetros na URL mas tem filtros salvos, redirecionar com os filtros salvos
        else if (!$request->hasAny([
            'referencia', 'produto', 'produto_id', 'marca_id', 'status_id', 'tecido_id',
            'tipo_id', 'situacao_id', 'localizacao_id', 'data_inicio', 'data_fim',
            'comprometido', 'concluido', 'sort', 'direction', 'status_dias', 'grupo_produto_id'
        ])) {
            $savedFilters = auth()->user()->getFilters('movimentacoes');
            
            if (!empty($savedFilters)) {
                return redirect()->route('movimentacoes.filtro.status-dias', $savedFilters);
            }
        }

        $query = Movimentacao::with(['produto', 'produto.marca', 'tipo', 'situacao', 'localizacao']);

        // Filtro por referência do produto
        if ($request->filled('referencia')) {
            $query->whereHas('produto', function($q) use ($request) {
                $q->where('referencia', 'like', '%' . $request->referencia . '%');
            });
        }

        // Filtro por descrição do produto
        if ($request->filled('produto')) {
            $query->whereHas('produto', function($q) use ($request) {
                $q->where('descricao', 'like', '%' . $request->produto . '%');
            });
        }

        // Filtro por ID do produto
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

        // Filtro por Status do produto
        if ($request->filled('status_id')) {
            $query->whereHas('produto', function($q) use ($request) {
                $q->where('status_id', $request->status_id);
            });
        }
        
        // Filtro por Tecido do produto
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
        
        // Adicionar filtro para o campo concluido
        if ($request->filled('concluido')) {
            $query->where('concluido', $request->concluido);
        }
        
        // Filtro por status de dias (Atrasados, Em Dia)
        if ($request->filled('status_dias')) {
            $query = MovimentacaoFilterController::applyStatusDiasFilter($query, $request->status_dias);
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

        $movimentacoes = $query->paginate(15)->withQueryString();

        // Carregar produtos para o select
        $produtos = Produto::orderBy('referencia')->get();
        
        // Carregar situações para o select
        $situacoesAtivas = Situacao::where('ativo', true)->orderBy('descricao')->get();
        $situacoesInativas = Situacao::where('ativo', false)->orderBy('descricao')->get();
        
        // Combinar as coleções: ativas primeiro, depois inativas
        $situacoes = $situacoesAtivas->concat($situacoesInativas);
        
        // Carregar tipos para o select
        $tiposAtivos = Tipo::where('ativo', true)->orderBy('descricao')->get();
        $tiposInativos = Tipo::where('ativo', false)->orderBy('descricao')->get();
        
        // Combinar as coleções: ativas primeiro, depois inativas
        $tipos = $tiposAtivos->concat($tiposInativos);
        
        // Carregar localizações para o select
        $localizacoesAtivas = Localizacao::where('ativo', true)->orderBy('nome_localizacao')->get();
        $localizacoesInativas = Localizacao::where('ativo', false)->orderBy('nome_localizacao')->get();
        
        // Combinar as coleções: ativas primeiro, depois inativas
        $localizacoes = $localizacoesAtivas->concat($localizacoesInativas);
        
        $status = Status::where('ativo', true)->orderBy('descricao')->get();
        
        // Carregar marcas para o filtro
        $marcas = Marca::where('ativo', true)->orderBy('nome_marca')->get();
        
        // Carregar tecidos para o filtro
        $tecidos = Tecido::where('ativo', true)->orderBy('descricao')->get();
        
        // Carregar grupos de produtos para o filtro (incluindo ativo = NULL)
        $grupoProdutos = GrupoProduto::orderBy('descricao')->get();

        return view('movimentacoes.index', compact('movimentacoes', 'produtos', 'situacoes', 'tipos', 'localizacoes', 'status', 'marcas', 'tecidos', 'grupoProdutos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if (!auth()->user() || !auth()->user()->canCreate('movimentacoes')) {
            abort(403, 'Acesso negado.');
        }

        $produtos = Produto::orderBy('referencia')->get();
        $situacoes = Situacao::where('ativo', true)->orderBy('descricao')->get();
        $tipos = Tipo::where('ativo', true)->orderBy('descricao')->get();
        $localizacoes = Localizacao::where('ativo', true)->orderBy('nome_localizacao')->get();

        // Pré-selecionar produto se fornecido via query string
        $produto_id = $request->query('produto_id');
        $produto_selecionado = null;
        
        if ($produto_id) {
            $produto_selecionado = Produto::find($produto_id);
        }

        return view('movimentacoes.create', compact('produtos', 'situacoes', 'tipos', 'localizacoes', 'produto_id', 'produto_selecionado'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user() || !auth()->user()->canCreate('movimentacoes')) {
            abort(403, 'Acesso negado.');
        }

        $validated = $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'tipo_id' => 'required|exists:tipos,id',
            'situacao_id' => 'required|exists:situacoes,id',
            'localizacao_id' => 'required|exists:localizacoes,id',
            'data_entrada' => 'required|date',
            'data_saida' => 'nullable|date|after_or_equal:data_entrada',
            'data_devolucao' => 'nullable|date|after_or_equal:data_entrada',
            'observacao' => 'nullable|string',
            'anexo' => 'nullable|file|mimes:jpg,jpeg,png|max:10240', // 10MB máximo
            'concluido' => 'boolean',
        ], [
            'produto_id.required' => 'O campo Produto é obrigatório.',
            'produto_id.exists' => 'O produto selecionado não existe.',
            'tipo_id.required' => 'O campo Tipo de Movimentação é obrigatório.',
            'tipo_id.exists' => 'O tipo de movimentação selecionado não existe.',
            'situacao_id.required' => 'O campo Situação é obrigatório.',
            'situacao_id.exists' => 'A situação selecionada não existe.',
            'localizacao_id.required' => 'O campo Localização é obrigatório.',
            'localizacao_id.exists' => 'A localização selecionada não existe.',
            'data_entrada.required' => 'O campo Data de Entrada é obrigatório.',
            'data_entrada.date' => 'O campo Data de Entrada deve ser uma data válida.',
            'data_saida.date' => 'O campo Data de Saída deve ser uma data válida.',
            'data_saida.after_or_equal' => 'A Data de Saída deve ser igual ou posterior à Data de Entrada.',
            'data_devolucao.date' => 'O campo Data de Devolução deve ser uma data válida.',
            'data_devolucao.after_or_equal' => 'A Data de Devolução deve ser igual ou posterior à Data de Entrada.',
            'anexo.file' => 'O anexo deve ser um arquivo válido.',
            'anexo.mimes' => 'O anexo deve ser uma imagem (jpg, jpeg ou png).',
            'anexo.max' => 'O anexo não pode ser maior que 10MB.',
        ]);

        // Verificar se o produto pode ter movimentações
        $produto = Produto::findOrFail($validated['produto_id']);
        if (!$produto->podeMovimentar()) {
            return back()->withInput()->with('error', 'Este produto não pode ter movimentações. Contate o administrador.');
        }

        try {
            $movimentacao = new Movimentacao();
            $movimentacao->produto_id = $validated['produto_id'];
            $movimentacao->tipo_id = $validated['tipo_id'];
            $movimentacao->situacao_id = $validated['situacao_id'];
            $movimentacao->localizacao_id = $validated['localizacao_id'];
            $movimentacao->data_entrada = $validated['data_entrada'];
            $movimentacao->data_saida = $validated['data_saida'] ?? null;
            $movimentacao->data_devolucao = $validated['data_devolucao'] ?? null;
            $movimentacao->observacao = $validated['observacao'] ?? null;
            $movimentacao->comprometido = 0; // Valor padrão
            $movimentacao->concluido = $request->has('concluido'); // Checkbox marcado = true, não marcado = false

            // Upload de anexo se existir
            if ($request->hasFile('anexo')) {
                $anexo = $request->file('anexo');
                $nomeArquivo = time() . '_' . $anexo->getClientOriginalName();
                $anexo->move(public_path('uploads/movimentacoes'), $nomeArquivo);
                $movimentacao->anexo = $nomeArquivo;
            }

            $movimentacao->save();

            // Verificar se deve redirecionar de volta para o show do produto
            if ($request->has('redirect_to_produto') && $request->redirect_to_produto) {
                return redirect()->route('produtos.show', $request->redirect_to_produto)
                        ->with('success', 'Movimentação criada com sucesso!');
            }

            // Usar os filtros salvos do usuário
            $savedFilters = auth()->user()->getFilters('movimentacoes');
            
            return redirect()->route('movimentacoes.filtro.status-dias', $savedFilters)
                    ->with('success', 'Movimentação criada com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao criar movimentação: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Movimentacao $movimentacao)
    {
        if (!auth()->user() || !auth()->user()->canRead('movimentacoes')) {
            abort(403, 'Acesso negado.');
        }

        $backUrl = $request->query('back_url');
        return view('movimentacoes.show', compact('movimentacao', 'backUrl'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Movimentacao $movimentacao)
    {
        if (!auth()->user() || !auth()->user()->canUpdate('movimentacoes')) {
            abort(403, 'Acesso negado.');
        }

        $produtos = Produto::orderBy('referencia')->get();
        $situacoes = Situacao::orderBy('descricao')->get();
        $tipos = Tipo::orderBy('descricao')->get();
        $localizacoes = Localizacao::orderBy('nome_localizacao')->get();
        $backUrl = $request->query('back_url');

        return view('movimentacoes.edit', compact('movimentacao', 'produtos', 'situacoes', 'tipos', 'localizacoes', 'backUrl'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Movimentacao $movimentacao)
    {
        if (!auth()->user() || !auth()->user()->canUpdate('movimentacoes')) {
            abort(403, 'Acesso negado.');
        }

        $validated = $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'tipo_id' => 'required|exists:tipos,id',
            'situacao_id' => 'required|exists:situacoes,id',
            'localizacao_id' => 'required|exists:localizacoes,id',
            'data_entrada' => 'required|date',
            'data_saida' => 'nullable|date|after_or_equal:data_entrada',
            'data_devolucao' => 'nullable|date|after_or_equal:data_entrada',
            'observacao' => 'nullable|string',
            'anexo' => 'nullable|file|mimes:jpg,jpeg,png|max:10240', // 10MB máximo
        ], [
            'produto_id.required' => 'O campo Produto é obrigatório.',
            'produto_id.exists' => 'O produto selecionado não existe.',
            'tipo_id.required' => 'O campo Tipo de Movimentação é obrigatório.',
            'tipo_id.exists' => 'O tipo de movimentação selecionado não existe.',
            'situacao_id.required' => 'O campo Situação é obrigatório.',
            'situacao_id.exists' => 'A situação selecionada não existe.',
            'localizacao_id.required' => 'O campo Localização é obrigatório.',
            'localizacao_id.exists' => 'A localização selecionada não existe.',
            'data_entrada.required' => 'O campo Data de Entrada é obrigatório.',
            'data_entrada.date' => 'O campo Data de Entrada deve ser uma data válida.',
            'data_saida.date' => 'O campo Data de Saída deve ser uma data válida.',
            'data_saida.after_or_equal' => 'A Data de Saída deve ser igual ou posterior à Data de Entrada.',
            'data_devolucao.date' => 'O campo Data de Devolução deve ser uma data válida.',
            'data_devolucao.after_or_equal' => 'A Data de Devolução deve ser igual ou posterior à Data de Entrada.',
            'anexo.file' => 'O anexo deve ser um arquivo válido.',
            'anexo.mimes' => 'O anexo deve ser uma imagem (jpg, jpeg ou png).',
            'anexo.max' => 'O anexo não pode ser maior que 10MB.',
        ]);

        // Verificar se o produto pode ter movimentações
        $produto = Produto::findOrFail($validated['produto_id']);
        if (!$produto->podeMovimentar()) {
            return back()->withInput()->with('error', 'Este produto não pode ter movimentações. Contate o administrador.');
        }

        // Upload de anexo se existir
        if ($request->hasFile('anexo')) {
            // Remover anexo antigo se existir
            if ($movimentacao->anexo) {
                $caminhoAnexoAntigo = public_path('uploads/movimentacoes/' . $movimentacao->anexo);
                if (file_exists($caminhoAnexoAntigo)) {
                    unlink($caminhoAnexoAntigo);
                }
            }

            $anexo = $request->file('anexo');
            $nomeArquivo = time() . '_' . $anexo->getClientOriginalName();
            $anexo->move(public_path('uploads/movimentacoes'), $nomeArquivo);
            $validated['anexo'] = $nomeArquivo;
        } else {
            // Manter o anexo existente
            unset($validated['anexo']);
        }

        // Tratar checkbox concluido (quando não marcado, não vem no request)
        $validated['concluido'] = $request->has('concluido');

        $movimentacao->update($validated);

        // Verificar se existe back_url para retornar à página de origem
        if ($request->has('back_url') && $request->back_url) {
            return redirect($request->back_url)
                    ->with('success', 'Movimentação atualizada com sucesso!');
        }

        // Usar os filtros salvos do usuário
        $savedFilters = auth()->user()->getFilters('movimentacoes');
        
        return redirect()->route('movimentacoes.filtro.status-dias', $savedFilters)
                ->with('success', 'Movimentação atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movimentacao $movimentacao)
    {
        if (!auth()->user() || !auth()->user()->canDelete('movimentacoes')) {
            abort(403, 'Acesso negado.');
        }

        try {
            // Remover anexo se existir
            if ($movimentacao->anexo) {
                $caminhoAnexo = public_path('uploads/movimentacoes/' . $movimentacao->anexo);
                if (file_exists($caminhoAnexo)) {
                    unlink($caminhoAnexo);
                }
            }

            $movimentacao->delete();
            
            // Usar os filtros salvos do usuário
            $savedFilters = auth()->user()->getFilters('movimentacoes');
            
            return redirect()->route('movimentacoes.filtro.status-dias', $savedFilters)
                    ->with('success', 'Movimentação excluída com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao excluir movimentação: ' . $e->getMessage());
        }
    }

    /**
     * Gerar PDF da movimentação
     */
    public function generatePdf(Movimentacao $movimentacao)
    {
        if (!auth()->user() || !auth()->user()->canRead('movimentacoes')) {
            abort(403, 'Acesso negado.');
        }

        $pdf = PDF::loadView('movimentacoes.pdf', compact('movimentacao'));
        return $pdf->stream('movimentacao_' . $movimentacao->id . '.pdf');
    }

    /**
     * Gerar PDF da lista de movimentações filtradas
     */
    public function generateListPdf(Request $request)
    {
        if (!auth()->user() || !auth()->user()->canRead('movimentacoes')) {
            abort(403, 'Acesso negado.');
        }

        $query = Movimentacao::with(['produto', 'produto.marca', 'tipo', 'situacao', 'localizacao']);

        // Aplicar os mesmos filtros da listagem
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

        // Filtro por Status do produto
        if ($request->filled('status_id')) {
            $query->whereHas('produto', function($q) use ($request) {
                $q->where('status_id', $request->status_id);
            });
        }
        
        // Filtro por Tecido do produto
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
        
        // Adicionar filtro para o campo concluido
        if ($request->filled('concluido')) {
            $query->where('concluido', $request->concluido);
        }
        
        // Filtro por status de dias (Atrasados, Em Dia)
        if ($request->filled('status_dias')) {
            $query = MovimentacaoFilterController::applyStatusDiasFilter($query, $request->status_dias);
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

        $movimentacoes = $query->get();

        $pdf = PDF::loadView('movimentacoes.pdf_lista', compact('movimentacoes', 'request'));
        return $pdf->stream('lista_movimentacoes.pdf');
    }

    /**
     * Remover anexo da movimentação
     */
    public function removerAnexo(Movimentacao $movimentacao)
    {
        if (!auth()->user() || !auth()->user()->canUpdate('movimentacoes')) {
            abort(403, 'Acesso negado.');
        }

        if ($movimentacao->anexo) {
            $caminhoAnexo = public_path('uploads/movimentacoes/' . $movimentacao->anexo);
            if (file_exists($caminhoAnexo)) {
                unlink($caminhoAnexo);
            }

            $movimentacao->anexo = null;
            $movimentacao->save();

            // Se estamos na página de edição, voltar para lá
            if (request()->is('*/edit') || request()->is('*/edit/*')) {
                return back()->with('success', 'Anexo removido com sucesso!');
            } else {
                // Usar os filtros salvos do usuário
                $savedFilters = auth()->user()->getFilters('movimentacoes');
                
                return redirect()->route('movimentacoes.filtro.status-dias', $savedFilters)
                        ->with('success', 'Anexo removido com sucesso!');
            }
        }

        return back()->with('error', 'Nenhum anexo encontrado para remover.');
    }
}
