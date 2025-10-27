<?php

namespace App\Http\Controllers;

use App\Models\Movimentacao;
use App\Models\Produto;
use App\Models\Localizacao;
use App\Models\Tipo;
use App\Models\Situacao;
use App\Models\Status;
use App\Models\Marca;
use App\Models\Tecido;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class MovimentacaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!auth()->user() || !auth()->user()->canRead('movimentacoes')) {
            abort(403, 'Acesso negado.');
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

        // Filtro por Status do produto
        if ($request->filled('status_id')) {
            $query->whereHas('produto', function($q) use ($request) {
                $q->where('status_id', $request->status_id);
            });
        }
        
        // Filtro por Tecido do produto
        if ($request->filled('tecido_id')) {
            $query->whereHas('produto', function($q) use ($request) {
                $q->whereHas('tecidos', function($tq) use ($request) {
                    $tq->where('tecidos.id', $request->tecido_id);
                });
            });
        }

        if ($request->filled('tipo_id')) {
            $query->where('tipo_id', $request->tipo_id);
        }

        if ($request->filled('situacao_id')) {
            $query->where('situacao_id', $request->situacao_id);
        }

        if ($request->filled('localizacao_id')) {
            $query->where('localizacao_id', $request->localizacao_id);
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
                          ->orderBy('tipos.nome_tipo', $direction)
                          ->select('movimentacoes.*');
                    break;
                case 'situacao':
                    $query->join('situacoes', 'movimentacoes.situacao_id', '=', 'situacoes.id')
                          ->orderBy('situacoes.nome_situacao', $direction)
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
        $situacoesAtivas = Situacao::where('ativo', true)->orderBy('nome_situacao')->get();
        $situacoesInativas = Situacao::where('ativo', false)->orderBy('nome_situacao')->get();
        
        // Combinar as coleções: ativas primeiro, depois inativas
        $situacoes = $situacoesAtivas->concat($situacoesInativas);
        
        // Carregar tipos para o select
        $tiposAtivos = Tipo::where('ativo', true)->orderBy('nome_tipo')->get();
        $tiposInativos = Tipo::where('ativo', false)->orderBy('nome_tipo')->get();
        
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

        return view('movimentacoes.index', compact('movimentacoes', 'produtos', 'situacoes', 'tipos', 'localizacoes', 'status', 'marcas', 'tecidos'));
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
        $situacoes = Situacao::where('ativo', true)->orderBy('nome_situacao')->get();
        $tipos = Tipo::where('ativo', true)->orderBy('nome_tipo')->get();
        $localizacoes = Localizacao::where('ativo', true)->orderBy('nome_localizacao')->get();

        // Pré-selecionar produto se fornecido via query string
        $produtoId = $request->query('produto_id');

        return view('movimentacoes.create', compact('produtos', 'situacoes', 'tipos', 'localizacoes', 'produtoId'));
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
        ]);

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

            return redirect()->route('movimentacoes.index')->with('success', 'Movimentação criada com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao criar movimentação: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Movimentacao $movimentacao)
    {
        if (!auth()->user() || !auth()->user()->canRead('movimentacoes')) {
            abort(403, 'Acesso negado.');
        }

        return view('movimentacoes.show', compact('movimentacao'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Movimentacao $movimentacao)
    {
        if (!auth()->user() || !auth()->user()->canUpdate('movimentacoes')) {
            abort(403, 'Acesso negado.');
        }

        $produtos = Produto::orderBy('referencia')->get();
        $situacoes = Situacao::orderBy('nome_situacao')->get();
        $tipos = Tipo::orderBy('nome_tipo')->get();
        $localizacoes = Localizacao::orderBy('nome_localizacao')->get();

        return view('movimentacoes.edit', compact('movimentacao', 'produtos', 'situacoes', 'tipos', 'localizacoes'));
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
        ]);

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

        return redirect()->route('movimentacoes.index')->with('success', 'Movimentação atualizada com sucesso!');
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
            return redirect()->route('movimentacoes.index')->with('success', 'Movimentação excluída com sucesso!');
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

        // Filtro por Status do produto
        if ($request->filled('status_id')) {
            $query->whereHas('produto', function($q) use ($request) {
                $q->where('status_id', $request->status_id);
            });
        }
        
        // Filtro por Tecido do produto
        if ($request->filled('tecido_id')) {
            $query->whereHas('produto', function($q) use ($request) {
                $q->whereHas('tecidos', function($tq) use ($request) {
                    $tq->where('tecidos.id', $request->tecido_id);
                });
            });
        }

        if ($request->filled('tipo_id')) {
            $query->where('tipo_id', $request->tipo_id);
        }

        if ($request->filled('situacao_id')) {
            $query->where('situacao_id', $request->situacao_id);
        }

        if ($request->filled('localizacao_id')) {
            $query->where('localizacao_id', $request->localizacao_id);
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
                          ->orderBy('tipos.nome_tipo', $direction)
                          ->select('movimentacoes.*');
                    break;
                case 'situacao':
                    $query->join('situacoes', 'movimentacoes.situacao_id', '=', 'situacoes.id')
                          ->orderBy('situacoes.nome_situacao', $direction)
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

        $pdf = PDF::loadView('movimentacoes.pdf_list', compact('movimentacoes'));
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

            return back()->with('success', 'Anexo removido com sucesso!');
        }

        return back()->with('error', 'Nenhum anexo encontrado para remover.');
    }
}
