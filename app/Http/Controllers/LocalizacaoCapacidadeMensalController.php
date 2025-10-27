<?php

namespace App\Http\Controllers;

use App\Models\LocalizacaoCapacidadeMensal;
use App\Models\Localizacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocalizacaoCapacidadeMensalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LocalizacaoCapacidadeMensal::with('localizacao');

        // Filtros
        if ($request->filled('localizacao_id')) {
            $query->where('localizacao_id', $request->localizacao_id);
        }

        if ($request->filled('mes')) {
            $query->where('mes', $request->mes);
        }

        if ($request->filled('ano')) {
            $query->where('ano', $request->ano);
        }

        $capacidades = $query->orderBy('ano', 'desc')
            ->orderBy('mes', 'desc')
            ->orderBy('localizacao_id')
            ->paginate(15);

        // Carregar localizações para o filtro
        $localizacoes = Localizacao::where('ativo', true)
            ->orderBy('nome_localizacao')
            ->get();

        return view('localizacao-capacidade.index', compact('capacidades', 'localizacoes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $localizacoes = Localizacao::where('ativo', true)
            ->orderBy('nome_localizacao')
            ->get();

        return view('localizacao-capacidade.create', compact('localizacoes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'localizacao_id' => 'required|exists:localizacoes,id',
            'mes' => 'required|integer|min:1|max:12',
            'ano' => 'required|integer|min:2020|max:2100',
            'capacidade' => 'required|integer|min:0',
            'observacoes' => 'nullable|string'
        ], [
            'localizacao_id.required' => 'A localização é obrigatória.',
            'localizacao_id.exists' => 'Localização inválida.',
            'mes.required' => 'O mês é obrigatório.',
            'mes.min' => 'O mês deve ser entre 1 e 12.',
            'mes.max' => 'O mês deve ser entre 1 e 12.',
            'ano.required' => 'O ano é obrigatório.',
            'ano.min' => 'Ano inválido.',
            'capacidade.required' => 'A capacidade é obrigatória.',
            'capacidade.min' => 'A capacidade deve ser maior ou igual a 0.'
        ]);

        if ($validator->fails()) {
            return redirect()->route('localizacao-capacidade.create')
                ->withErrors($validator)
                ->withInput();
        }

        // Verificar se já existe capacidade para esta localização/mês/ano
        $existente = LocalizacaoCapacidadeMensal::where('localizacao_id', $request->localizacao_id)
            ->where('mes', $request->mes)
            ->where('ano', $request->ano)
            ->first();

        if ($existente) {
            return redirect()->route('localizacao-capacidade.create')
                ->withErrors(['mes' => 'Já existe uma capacidade cadastrada para esta localização neste mês/ano.'])
                ->withInput();
        }

        $data = $request->only(['localizacao_id', 'mes', 'ano', 'capacidade', 'observacoes']);
        
        if (empty(trim($data['observacoes'] ?? ''))) {
            $data['observacoes'] = null;
        }

        LocalizacaoCapacidadeMensal::create($data);

        return redirect()->route('localizacao-capacidade.index')
            ->with('success', 'Capacidade mensal criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $capacidade = LocalizacaoCapacidadeMensal::with('localizacao')->findOrFail($id);
        
        // Buscar alocações para este período
        $alocacoes = \App\Models\ProdutoAlocacaoMensal::where('localizacao_id', $capacidade->localizacao_id)
            ->where('mes', $capacidade->mes)
            ->where('ano', $capacidade->ano)
            ->with(['produto.marca', 'produto.grupoProduto', 'produto.observacoes'])
            ->orderBy('created_at')
            ->get();

        // Transformar alocações em produtos para compatibilidade com a view
        $produtos = $alocacoes->map(function($alocacao) {
            $produto = $alocacao->produto;
            if ($produto) {
                $produto->quantidade = $alocacao->quantidade;
                // data_prevista_faccao agora está em produto_localizacao (pode ter múltiplas datas)
            }
            return $produto;
        })->filter();

        return view('localizacao-capacidade.show', compact('capacidade', 'produtos'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $capacidade = LocalizacaoCapacidadeMensal::findOrFail($id);
        
        $localizacoes = Localizacao::where('ativo', true)
            ->orderBy('nome_localizacao')
            ->get();

        return view('localizacao-capacidade.edit', compact('capacidade', 'localizacoes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $capacidade = LocalizacaoCapacidadeMensal::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'localizacao_id' => 'required|exists:localizacoes,id',
            'mes' => 'required|integer|min:1|max:12',
            'ano' => 'required|integer|min:2020|max:2100',
            'capacidade' => 'required|integer|min:0',
            'observacoes' => 'nullable|string'
        ], [
            'localizacao_id.required' => 'A localização é obrigatória.',
            'localizacao_id.exists' => 'Localização inválida.',
            'mes.required' => 'O mês é obrigatório.',
            'mes.min' => 'O mês deve ser entre 1 e 12.',
            'mes.max' => 'O mês deve ser entre 1 e 12.',
            'ano.required' => 'O ano é obrigatório.',
            'ano.min' => 'Ano inválido.',
            'capacidade.required' => 'A capacidade é obrigatória.',
            'capacidade.min' => 'A capacidade deve ser maior ou igual a 0.'
        ]);

        if ($validator->fails()) {
            return redirect()->route('localizacao-capacidade.edit', $capacidade)
                ->withErrors($validator)
                ->withInput();
        }

        // Verificar se já existe outra capacidade para esta localização/mês/ano
        $existente = LocalizacaoCapacidadeMensal::where('localizacao_id', $request->localizacao_id)
            ->where('mes', $request->mes)
            ->where('ano', $request->ano)
            ->where('id', '!=', $id)
            ->first();

        if ($existente) {
            return redirect()->route('localizacao-capacidade.edit', $capacidade)
                ->withErrors(['mes' => 'Já existe uma capacidade cadastrada para esta localização neste mês/ano.'])
                ->withInput();
        }

        $data = $request->only(['localizacao_id', 'mes', 'ano', 'capacidade', 'observacoes']);
        
        if (empty(trim($data['observacoes'] ?? ''))) {
            $data['observacoes'] = null;
        }

        $capacidade->update($data);

        return redirect()->route('localizacao-capacidade.index')
            ->with('success', 'Capacidade mensal atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $capacidade = LocalizacaoCapacidadeMensal::withTrashed()->findOrFail($id);

        if ($capacidade->trashed()) {
            // Restaurar
            $capacidade->restore();
            $message = 'Capacidade mensal restaurada com sucesso!';
        } else {
            // Excluir
            $capacidade->delete();
            $message = 'Capacidade mensal excluída com sucesso!';
        }

        return redirect()->route('localizacao-capacidade.index')
            ->with('success', $message);
    }

    /**
     * Dashboard de capacidade das localizações
     */
    public function dashboard(Request $request)
    {
        // Mês e ano padrão (atual)
        $mes = $request->filled('mes') ? $request->mes : now()->month;
        $ano = $request->filled('ano') ? $request->ano : now()->year;
        $localizacaoId = $request->filled('localizacao_id') ? $request->localizacao_id : null;

        // Buscar capacidades do período
        $query = LocalizacaoCapacidadeMensal::with('localizacao')
            ->where('mes', $mes)
            ->where('ano', $ano);
            
        // Aplicar filtro de localização se selecionado
        if ($localizacaoId) {
            $query->where('localizacao_id', $localizacaoId);
        }
        
        $capacidades = $query->get();

        // Adicionar informações de produtos previstos
        $dadosDashboard = $capacidades->map(function ($capacidade) use ($mes, $ano) {
            // Buscar alocações para esta localização no período
            $alocacoes = \App\Models\ProdutoAlocacaoMensal::where('localizacao_id', $capacidade->localizacao_id)
                ->where('mes', $mes)
                ->where('ano', $ano)
                ->with(['produto.marca', 'produto.grupoProduto', 'produto.status'])
                ->orderBy('created_at')
                ->get();

            // Transformar alocações em produtos para compatibilidade com a view
            $produtos = $alocacoes->map(function($alocacao) {
                $produto = $alocacao->produto;
                if ($produto) {
                    // Criar uma cópia do produto para evitar referências compartilhadas
                    $produtoCopia = clone $produto;
                    $produtoCopia->quantidade_alocada = $alocacao->quantidade;
                    $produtoCopia->alocacao_id = $alocacao->id;
                    $produtoCopia->tipo_alocacao = $alocacao->tipo;
                    return $produtoCopia;
                }
                return null;
            })->filter(); // Remove nulls

            return [
                'localizacao' => $capacidade->localizacao,
                'capacidade' => $capacidade->capacidade,
                'produtos_previstos' => $capacidade->getProdutosPrevistos(),
                'produtos' => $produtos,
                'saldo' => $capacidade->getSaldo(),
                'percentual' => $capacidade->getPercentualOcupacao(),
                'acima_capacidade' => $capacidade->isAcimaDaCapacidade()
            ];
        });

        // Localizações para filtro
        $localizacoes = Localizacao::where('ativo', true)
            ->orderBy('nome_localizacao')
            ->get();

        return view('localizacao-capacidade.dashboard', compact('dadosDashboard', 'mes', 'ano', 'localizacoes', 'localizacaoId'));
    }

    /**
     * Sugerir redistribuição de produtos excedentes
     */
    public function sugerirRedistribuicao(Request $request)
    {
        $localizacaoId = $request->localizacao_id;
        $mes = $request->mes;
        $ano = $request->ano;

        // Buscar capacidade atual
        $capacidadeAtual = LocalizacaoCapacidadeMensal::where('localizacao_id', $localizacaoId)
            ->where('mes', $mes)
            ->where('ano', $ano)
            ->with('localizacao')
            ->first();

        if (!$capacidadeAtual) {
            return response()->json(['error' => 'Capacidade não encontrada'], 404);
        }

        // Calcular excedente
        $excedente = $capacidadeAtual->getProdutosPrevistos() - $capacidadeAtual->capacidade;

        if ($excedente <= 0) {
            return response()->json(['message' => 'Não há excedente para redistribuir'], 200);
        }

        // Buscar alocações para redistribuir
        // Ordena por quantidade CRESCENTE para pegar menores primeiro
        $alocacoes = \App\Models\ProdutoAlocacaoMensal::where('localizacao_id', $localizacaoId)
            ->where('mes', $mes)
            ->where('ano', $ano)
            ->with(['produto.marca', 'produto.grupoProduto'])
            ->orderBy('quantidade', 'asc') // Menores quantidades primeiro
            ->orderBy('created_at', 'desc') // Mais recentes primeiro
            ->get();

        // Selecionar alocações e calcular quantidades exatas para redistribuir
        $alocacoesSelecionadas = [];
        $quantidadeAcumulada = 0;

        foreach ($alocacoes as $alocacao) {
            // Se já completou o excedente necessário, para
            if ($quantidadeAcumulada >= $excedente) {
                break;
            }

            // Calcula quanto ainda falta
            $quantidadeFaltante = $excedente - $quantidadeAcumulada;
            
            if ($alocacao->quantidade <= $quantidadeFaltante) {
                // Alocação cabe inteira - move tudo
                $alocacoesSelecionadas[] = [
                    'alocacao' => $alocacao,
                    'quantidade_mover' => $alocacao->quantidade,
                    'tipo' => 'completo'
                ];
                $quantidadeAcumulada += $alocacao->quantidade;
            } else {
                // Alocação é maior que o necessário - DIVIDIR
                $alocacoesSelecionadas[] = [
                    'alocacao' => $alocacao,
                    'quantidade_mover' => $quantidadeFaltante,
                    'tipo' => 'parcial'
                ];
                $quantidadeAcumulada += $quantidadeFaltante;
                break; // Completou o excedente exato
            }
        }

        // Buscar próximo mês com capacidade disponível
        $proximoMes = $mes + 1;
        $proximoAno = $ano;

        if ($proximoMes > 12) {
            $proximoMes = 1;
            $proximoAno++;
        }

        $capacidadeDestino = LocalizacaoCapacidadeMensal::where('localizacao_id', $localizacaoId)
            ->where('mes', $proximoMes)
            ->where('ano', $proximoAno)
            ->first();

        return response()->json([
            'excedente' => $excedente,
            'quantidade_selecionada' => $quantidadeAcumulada,
            'alocacoes' => collect($alocacoesSelecionadas)->map(function($item) {
                $alocacao = $item['alocacao'];
                $produto = $alocacao->produto;
                
                return [
                    'alocacao_id' => $alocacao->id,
                    'produto_id' => $produto->id,
                    'referencia' => $produto->referencia,
                    'descricao' => $produto->descricao,
                    'quantidade' => $alocacao->quantidade,
                    'quantidade_mover' => $item['quantidade_mover'],
                    'tipo' => $item['tipo'],
                    'marca' => $produto->marca,
                    'grupoProduto' => $produto->grupoProduto
                ];
            }),
            'mes_destino' => $proximoMes,
            'ano_destino' => $proximoAno,
            'capacidade_destino' => $capacidadeDestino,
            'localizacao' => $capacidadeAtual->localizacao
        ]);
    }

    /**
     * Aplicar redistribuição de alocações
     */
    public function aplicarRedistribuicao(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'alocacoes' => 'required|array',
            'alocacoes.*.alocacao_id' => 'required|exists:produto_alocacao_mensal,id',
            'alocacoes.*.quantidade_mover' => 'required|integer|min:1',
            'mes_destino' => 'required|integer|min:1|max:12',
            'ano_destino' => 'required|integer',
            'localizacao_id' => 'required|exists:localizacoes,id',
            'motivo' => 'nullable|string',
            'observacoes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $redistribuidos = 0;
        $divididos = 0;
        $erros = [];

        \DB::beginTransaction();

        try {
            foreach ($request->alocacoes as $alocacaoData) {
                $alocacao = \App\Models\ProdutoAlocacaoMensal::find($alocacaoData['alocacao_id']);
                
                if (!$alocacao) {
                    $erros[] = "Alocação ID {$alocacaoData['alocacao_id']} não encontrada";
                    continue;
                }

                $quantidadeMover = $alocacaoData['quantidade_mover'];
                
                if ($quantidadeMover < $alocacao->quantidade) {
                    // DIVIDIR ALOCAÇÃO
                    $quantidadeFicar = $alocacao->quantidade - $quantidadeMover;
                    
                    // Reduzir quantidade da alocação original
                    $alocacao->quantidade = $quantidadeFicar;
                    $alocacao->save();
                    
                    // Criar nova alocação no mês destino
                    \App\Models\ProdutoAlocacaoMensal::create([
                        'produto_id' => $alocacao->produto_id,
                        'localizacao_id' => $request->localizacao_id,
                        'mes' => $request->mes_destino,
                        'ano' => $request->ano_destino,
                        'quantidade' => $quantidadeMover,
                        'tipo' => 'redistribuido',
                        'usuario_id' => auth()->id(),
                        'observacoes' => "Redistribuído de {$alocacao->mes_ano_formatado}. " . ($request->observacoes ?? '')
                    ]);
                    
                    $divididos++;
                    
                } else {
                    // MOVER ALOCAÇÃO COMPLETA
                    $alocacao->mes = $request->mes_destino;
                    $alocacao->ano = $request->ano_destino;
                    $alocacao->tipo = 'redistribuido';
                    $alocacao->observacoes = "Redistribuído. " . ($request->observacoes ?? '');
                    $alocacao->save();
                }

                // Registrar histórico
                \App\Models\ProdutoRedistribuicaoHistorico::create([
                    'produto_id' => $alocacao->produto_id,
                    'localizacao_origem_id' => $alocacao->localizacao_id,
                    'data_prevista_origem' => null,
                    'mes_origem' => $alocacao->mes,
                    'ano_origem' => $alocacao->ano,
                    'localizacao_destino_id' => $request->localizacao_id,
                    'data_prevista_destino' => null,
                    'mes_destino' => $request->mes_destino,
                    'ano_destino' => $request->ano_destino,
                    'quantidade' => $quantidadeMover,
                    'motivo' => $request->motivo ?? 'excedente_capacidade',
                    'tipo_redistribuicao' => $divididos > 0 ? 'automatica_divisao' : 'automatica',
                    'usuario_id' => auth()->id(),
                    'observacoes' => $request->observacoes
                ]);

                $redistribuidos++;
            }

            \DB::commit();

            $mensagem = "$redistribuidos alocação(ões) redistribuída(s)";
            if ($divididos > 0) {
                $mensagem .= " ($divididos dividida(s))";
            }
            $mensagem .= " com sucesso!";

            return response()->json([
                'success' => true,
                'message' => $mensagem,
                'redistribuidos' => $redistribuidos,
                'divididos' => $divididos,
                'erros' => $erros
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao redistribuir: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gerar capacidades mensais baseadas no padrão das localizações
     */
    public function gerarCapacidadesMes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mes' => 'required|integer|min:1|max:12',
            'ano' => 'required|integer|min:2020|max:2100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $mes = $request->mes;
            $ano = $request->ano;

            // Buscar localizações ativas com capacidade > 0
            $localizacoes = Localizacao::where('ativo', true)
                ->where('capacidade', '>', 0)
                ->get();

            if ($localizacoes->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma localização ativa com capacidade encontrada.'
                ]);
            }

            $criados = 0;
            $jaExistentes = 0;
            $erros = [];

            foreach ($localizacoes as $localizacao) {
                // Verificar se já existe registro para este mês/ano
                $existe = LocalizacaoCapacidadeMensal::where('localizacao_id', $localizacao->id)
                    ->where('mes', $mes)
                    ->where('ano', $ano)
                    ->exists();

                if ($existe) {
                    $jaExistentes++;
                    continue;
                }

                try {
                    LocalizacaoCapacidadeMensal::create([
                        'localizacao_id' => $localizacao->id,
                        'mes' => $mes,
                        'ano' => $ano,
                        'capacidade' => $localizacao->capacidade
                    ]);
                    $criados++;
                } catch (\Exception $e) {
                    $erros[] = "Erro ao criar capacidade para {$localizacao->nome_localizacao}: " . $e->getMessage();
                }
            }

            $mesesNomes = ['', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
            $mensagem = "Capacidades geradas para {$mesesNomes[$mes]}/{$ano}: ";
            $mensagem .= "{$criados} criada(s)";
            
            if ($jaExistentes > 0) {
                $mensagem .= ", {$jaExistentes} já existente(s)";
            }
            
            if (!empty($erros)) {
                $mensagem .= ". Alguns erros ocorreram.";
            }

            return response()->json([
                'success' => true,
                'message' => $mensagem,
                'criados' => $criados,
                'ja_existentes' => $jaExistentes,
                'erros' => $erros
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar capacidades: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reverter redistribuição de alocação
     */
    public function reverterRedistribuicao(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'historico_id' => 'required|exists:produto_redistribuicao_historico,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        \DB::beginTransaction();

        try {
            $historico = \App\Models\ProdutoRedistribuicaoHistorico::with(['produto'])->findOrFail($request->historico_id);

            // Buscar a alocação redistribuída (no destino)
            $alocacaoDestino = \App\Models\ProdutoAlocacaoMensal::where('produto_id', $historico->produto_id)
                ->where('localizacao_id', $historico->localizacao_destino_id)
                ->where('mes', $historico->mes_destino)
                ->where('ano', $historico->ano_destino)
                ->where('tipo', 'redistribuido')
                ->first();

            if (!$alocacaoDestino) {
                throw new \Exception('Alocação redistribuída não encontrada. Pode já ter sido revertida ou removida.');
            }

            // Verificar se a alocação origem ainda existe (se foi dividida)
            $alocacaoOrigem = \App\Models\ProdutoAlocacaoMensal::where('produto_id', $historico->produto_id)
                ->where('localizacao_id', $historico->localizacao_origem_id)
                ->where('mes', $historico->mes_origem)
                ->where('ano', $historico->ano_origem)
                ->first();

            if ($alocacaoOrigem) {
                // Se a alocação origem existe, foi uma divisão - somar a quantidade de volta
                $alocacaoOrigem->quantidade += $historico->quantidade;
                $alocacaoOrigem->save();

                // Remover a alocação do destino
                $alocacaoDestino->delete();
            } else {
                // Se não existe, foi uma movimentação completa - mover de volta
                $alocacaoDestino->mes = $historico->mes_origem;
                $alocacaoDestino->ano = $historico->ano_origem;
                $alocacaoDestino->localizacao_id = $historico->localizacao_origem_id;
                $alocacaoDestino->tipo = 'manual';
                $alocacaoDestino->observacoes = "Redistribuição revertida. " . ($alocacaoDestino->observacoes ?? '');
                $alocacaoDestino->save();
            }

            // Marcar histórico como revertido (vamos adicionar esse campo)
            // Por enquanto, vamos deletar o histórico ou adicionar uma observação
            $historico->observacoes = "[REVERTIDO] " . ($historico->observacoes ?? '');
            $historico->save();

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Redistribuição revertida com sucesso!',
                'produto' => $historico->produto->referencia ?? 'N/A'
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao reverter redistribuição: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar histórico de redistribuições para um período
     */
    public function historicoRedistribuicoes(Request $request)
    {
        $mes = $request->filled('mes') ? $request->mes : now()->month;
        $ano = $request->filled('ano') ? $request->ano : now()->year;

        $historico = \App\Models\ProdutoRedistribuicaoHistorico::with([
            'produto',
            'localizacaoOrigem',
            'localizacaoDestino',
            'usuario'
        ])
        ->porPeriodo($mes, $ano)
        ->where(function($query) {
            $query->whereNull('observacoes')
                  ->orWhere('observacoes', 'NOT LIKE', '[REVERTIDO]%');
        })
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json([
            'success' => true,
            'historico' => $historico
        ]);
    }

    /**
     * Gerar relatório PDF do dashboard de capacidades
     */
    public function gerarRelatorioPDF(Request $request)
    {
        $mes = $request->filled('mes') ? $request->mes : now()->month;
        $ano = $request->filled('ano') ? $request->ano : now()->year;
        $localizacaoId = $request->filled('localizacao_id') ? $request->localizacao_id : null;

        // Buscar capacidades do período
        $query = LocalizacaoCapacidadeMensal::with('localizacao')
            ->where('mes', $mes)
            ->where('ano', $ano);
            
        // Aplicar filtro de localização se selecionado
        if ($localizacaoId) {
            $query->where('localizacao_id', $localizacaoId);
        }
        
        $capacidades = $query->get();

        // Adicionar informações de produtos previstos
        $dadosDashboard = $capacidades->map(function ($capacidade) use ($mes, $ano) {
            // Buscar alocações para esta localização no período
            $alocacoes = \App\Models\ProdutoAlocacaoMensal::where('localizacao_id', $capacidade->localizacao_id)
                ->where('mes', $mes)
                ->where('ano', $ano)
                ->with(['produto.marca', 'produto.grupoProduto', 'produto.status'])
                ->orderBy('created_at')
                ->get();

            // Transformar alocações em produtos
            $produtos = $alocacoes->map(function($alocacao) {
                $produto = $alocacao->produto;
                if ($produto) {
                    // Criar uma cópia do produto para evitar referências compartilhadas
                    $produtoCopia = clone $produto;
                    $produtoCopia->quantidade_alocada = $alocacao->quantidade;
                    $produtoCopia->alocacao_id = $alocacao->id;
                    $produtoCopia->tipo_alocacao = $alocacao->tipo;
                    return $produtoCopia;
                }
                return null;
            })->filter();

            return [
                'localizacao' => $capacidade->localizacao,
                'capacidade' => $capacidade->capacidade,
                'produtos_previstos' => $capacidade->getProdutosPrevistos(),
                'produtos' => $produtos,
                'saldo' => $capacidade->getSaldo(),
                'percentual' => $capacidade->getPercentualOcupacao(),
                'acima_capacidade' => $capacidade->isAcimaDaCapacidade()
            ];
        });

        // Nome do mês
        $meses = [
            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
            5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
            9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
        ];
        $mesNome = $meses[$mes] ?? '';

        $pdf = \PDF::loadView('localizacao-capacidade.relatorio-pdf', compact('dadosDashboard', 'mes', 'ano', 'mesNome'));
        
        return $pdf->stream("Relatorio_Capacidade_{$mesNome}_{$ano}.pdf");
    }

    /**
     * Gerar PDF da listagem de capacidades mensais
     */
    public function gerarPDFListagem(Request $request)
    {
        $query = LocalizacaoCapacidadeMensal::with('localizacao');

        // Aplicar filtros
        if ($request->filled('localizacao_id')) {
            $query->where('localizacao_id', $request->localizacao_id);
        }

        if ($request->filled('mes')) {
            $query->where('mes', $request->mes);
        }

        if ($request->filled('ano')) {
            $query->where('ano', $request->ano);
        }

        $capacidades = $query->orderBy('ano', 'desc')
            ->orderBy('mes', 'desc')
            ->orderBy('localizacao_id')
            ->get();

        // Carregar localizações para exibir nomes nos filtros
        $localizacoes = Localizacao::where('ativo', true)
            ->orderBy('nome_localizacao')
            ->get();

        // Preparar dados dos filtros para exibição no PDF
        $filtros = [
            'localizacao' => $request->filled('localizacao_id') 
                ? $localizacoes->firstWhere('id', $request->localizacao_id)->nome_localizacao ?? 'N/A'
                : 'Todas',
            'mes' => $request->filled('mes') 
                ? ['', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'][$request->mes]
                : 'Todos',
            'ano' => $request->filled('ano') ? $request->ano : 'Todos'
        ];

        $pdf = \PDF::loadView('localizacao-capacidade.listagem-pdf', compact('capacidades', 'filtros'));
        
        return $pdf->stream("Listagem_Capacidades_Mensais_" . now()->format('d_m_Y_H_i') . ".pdf");
    }
}
