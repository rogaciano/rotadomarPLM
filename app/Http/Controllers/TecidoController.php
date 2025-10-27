<?php

namespace App\Http\Controllers;

use App\Models\Tecido;
use App\Models\TecidoCorEstoque;
use App\Services\EstoqueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class TecidoController extends Controller
{
    protected $estoqueService;

    public function __construct(EstoqueService $estoqueService)
    {
        $this->estoqueService = $estoqueService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Autorização: leitura de tecidos
        $user = auth()->user();
        if (!$user || !$user->canRead('tecidos')) {
            abort(403, 'Ação não autorizada.');
        }

        $query = Tecido::query();

        // Filtros
        if ($request->filled('descricao')) {
            $query->where('descricao', 'like', '%' . $request->descricao . '%');
        }

        if ($request->filled('referencia')) {
            $query->where('referencia', 'like', '%' . $request->referencia . '%');
        }

        // Filtro por período de data de cadastro
        if ($request->filled('data_inicio')) {
            $query->whereDate('data_cadastro', '>=', $request->data_inicio);
        }
        
        if ($request->filled('data_fim')) {
            $query->whereDate('data_cadastro', '<=', $request->data_fim);
        }
        
        // Filtro por período de data de atualização do estoque
        if ($request->filled('estoque_data_inicio')) {
            $query->whereHas('estoquesCores', function($q) use ($request) {
                $q->whereDate('data_atualizacao', '>=', $request->estoque_data_inicio);
            });
        }
        
        if ($request->filled('estoque_data_fim')) {
            $query->whereHas('estoquesCores', function($q) use ($request) {
                $q->whereDate('data_atualizacao', '<=', $request->estoque_data_fim);
            });
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo);
        }

        // Ordenação
        $orderBy = $request->input('order_by', 'descricao');
        $orderDirection = $request->input('order_direction', 'asc');
        $query->orderBy($orderBy, $orderDirection);

        // Carrega os produtos relacionados para calcular a necessidade total
        $tecidos = $query->with(['produtos' => function($query) {
            $query->select('produtos.id', 'produtos.quantidade');
        }])->paginate(10);

        return view('tecidos.index', compact('tecidos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Autorização: criação de tecidos
        $user = auth()->user();
        if (!$user || !$user->canCreate('tecidos')) {
            abort(403, 'Ação não autorizada.');
        }
        return view('tecidos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Autorização: criação de tecidos
        $user = auth()->user();
        if (!$user || !$user->canCreate('tecidos')) {
            abort(403, 'Ação não autorizada.');
        }

        $validator = Validator::make($request->all(), [
            'descricao' => 'required|string|max:255',
            'ativo' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('tecidos.create')
                ->withErrors($validator)
                ->withInput();
        }

        Tecido::create([
            'descricao' => $request->descricao,
            'referencia' => $request->referencia,
            'data_cadastro' => now(),
            'ativo' => $request->has('ativo'),
        ]);

        return redirect()->route('tecidos.index')
            ->with('success', 'Tecido criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Autorização: leitura de tecidos
        $user = auth()->user();
        if (!$user || !$user->canRead('tecidos')) {
            abort(403, 'Ação não autorizada.');
        }
        $tecido = Tecido::withTrashed()->findOrFail($id);
        return view('tecidos.show', compact('tecido'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Autorização: atualização de tecidos
        $user = auth()->user();
        if (!$user || !$user->canUpdate('tecidos')) {
            abort(403, 'Ação não autorizada.');
        }
        $tecido = Tecido::withTrashed()->findOrFail($id);
        return view('tecidos.edit', compact('tecido'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Autorização: atualização de tecidos
        $user = auth()->user();
        if (!$user || !$user->canUpdate('tecidos')) {
            abort(403, 'Ação não autorizada.');
        }

        $validator = Validator::make($request->all(), [
            'descricao' => 'required|string|max:255',
            'ativo' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('tecidos.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        $tecido = Tecido::withTrashed()->findOrFail($id);
        $tecido->update([
            'descricao' => $request->descricao,
            'referencia' => $request->referencia,
            'ativo' => $request->has('ativo'),
        ]);

        return redirect()->route('tecidos.index')
            ->with('success', 'Tecido atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Autorização: exclusão de tecidos
        $user = auth()->user();
        if (!$user || !$user->canDelete('tecidos')) {
            abort(403, 'Ação não autorizada.');
        }
        $tecido = Tecido::withTrashed()->findOrFail($id);
        $tecido->delete(); // Soft delete

        return redirect()->route('tecidos.index')
            ->with('success', 'Tecido removido com sucesso.');
    }

    /**
     * Atualiza o estoque de um tecido específico
     */
    public function atualizarEstoque($id)
    {
        // Autorização: atualização de tecidos (estoque)
        $user = auth()->user();
        if (!$user || !$user->canUpdate('tecidos')) {
            abort(403, 'Ação não autorizada.');
        }
        $tecido = Tecido::findOrFail($id);

        if (empty($tecido->referencia)) {
            return redirect()->route('tecidos.show', $id)
                ->with('error', 'Este tecido não possui referência para consulta de estoque.');
        }

        $resultado = $this->estoqueService->consultarEstoqueTecido($tecido->referencia);

        if ($resultado && !empty($resultado)) {
            // O resultado é um array indexado pela referência do tecido
            // Verificar se existe a referência do tecido no resultado
            if (isset($resultado[$tecido->referencia])) {
                $dadosEstoque = $resultado[$tecido->referencia];
            } else {
                // Se não encontrou a referência, usar valores padrão
                $dadosEstoque = [
                    'quantidade' => 0,
                    'data_consulta' => now(),
                    'detalhes' => []
                ];
            }

            // Atualizar o tecido com os dados de estoque
            $tecido->update([
                'quantidade_estoque' => $dadosEstoque['quantidade'],
                'ultima_consulta_estoque' => $dadosEstoque['data_consulta']
            ]);

            // Remover registros antigos de estoque por cor para este tecido
            TecidoCorEstoque::where('tecido_id', $tecido->id)->delete();
            
            // Inserir novos registros de estoque por cor
            if (isset($dadosEstoque['detalhes']) && is_array($dadosEstoque['detalhes'])) {
                foreach ($dadosEstoque['detalhes'] as $cor => $tamanhos) {
                    // Calcular quantidade total para esta cor
                    $quantidadeCor = 0;
                    foreach ($tamanhos as $tamanho => $quantidade) {
                        $quantidadeCor += $quantidade;
                    }
                    
                    // Criar registro de estoque por cor
                    TecidoCorEstoque::create([
                        'tecido_id' => $tecido->id,
                        'cor' => $cor,
                        'codigo_cor' => null, // Não temos esta informação no retorno da API
                        'quantidade' => $quantidadeCor,
                        'data_atualizacao' => $dadosEstoque['data_consulta'],
                        'observacoes' => 'Tamanhos disponíveis: ' . implode(', ', array_keys($tamanhos))
                    ]);
                }
            }

            return redirect()->route('tecidos.show', $id)
                ->with('success', 'Estoque atualizado com sucesso. Quantidade total: ' . $dadosEstoque['quantidade']);
        }

        return redirect()->route('tecidos.show', $id)
            ->with('error', 'Não foi possível obter informações de estoque para este tecido.');
    }

    /**
     * Atualiza o estoque de todos os tecidos que possuem referência
     */
    public function atualizarTodosEstoques()
    {
        // Autorização: atualização de tecidos (estoques em massa)
        $user = auth()->user();
        if (!$user || !$user->canUpdate('tecidos')) {
            abort(403, 'Ação não autorizada.');
        }
        $tecidos = Tecido::whereNotNull('referencia')->get();

        if ($tecidos->isEmpty()) {
            return redirect()->route('tecidos.index')
                ->with('error', 'Não há tecidos com referência para atualizar.');
        }

        // Buscar todos os dados de estoque
        $response = Http::get(config('estoque.api_url'), [
            'empresa' => config('estoque.empresa'),
            'token' => config('estoque.token'),
            'armazenador' => config('estoque.armazenador')
        ]);

        if (!$response->successful()) {
            return redirect()->route('tecidos.index')
                ->with('error', 'Não foi possível obter informações de estoque.');
        }

        $todosEstoques = $response->json();
        $atualizados = 0;
        $dataConsulta = now();

        // Para cada tecido, filtrar os dados de estoque e atualizar
        foreach ($tecidos as $tecido) {
            if (empty($tecido->referencia)) {
                continue;
            }

            // Filtrar os dados de estoque para este tecido
            $estoqueDoTecido = array_filter($todosEstoques, function($item) use ($tecido) {
                return isset($item['Referencia']) && $item['Referencia'] === $tecido->referencia;
            });

            if (!empty($estoqueDoTecido)) {
                // Calcular a quantidade total
                $quantidadeTotal = 0;
                $estoquePorCor = [];
                
                // Agrupar estoque por cor
                foreach ($estoqueDoTecido as $item) {
                    if (isset($item['Estoque'])) {
                        $quantidade = (float)$item['Estoque'];
                        $quantidadeTotal += $quantidade;
                        
                        // Obter a cor do item (ou usar valor padrão se não existir)
                        $cor = isset($item['Cor']) ? $item['Cor'] : 'Não especificada';
                        $codigoCor = isset($item['CodigoCor']) ? $item['CodigoCor'] : null;
                        
                        // Agrupar por cor
                        if (!isset($estoquePorCor[$cor])) {
                            $estoquePorCor[$cor] = [
                                'quantidade' => 0,
                                'codigo_cor' => $codigoCor
                            ];
                        }
                        
                        $estoquePorCor[$cor]['quantidade'] += $quantidade;
                    }
                }
                
                // Atualizar o tecido principal
                $tecido->update([
                    'quantidade_estoque' => $quantidadeTotal,
                    'ultima_consulta_estoque' => $dataConsulta
                ]);
                
                // Remover registros antigos de estoque por cor para este tecido
                TecidoCorEstoque::where('tecido_id', $tecido->id)->delete();
                
                // Inserir novos registros de estoque por cor
                foreach ($estoquePorCor as $cor => $dados) {
                    TecidoCorEstoque::create([
                        'tecido_id' => $tecido->id,
                        'cor' => $cor,
                        'codigo_cor' => $dados['codigo_cor'],
                        'quantidade' => $dados['quantidade'],
                        'data_atualizacao' => $dataConsulta
                    ]);
                }

                $atualizados++;
            }
        }

        return redirect()->route('tecidos.index')
            ->with('success', "Estoque atualizado para {$atualizados} tecidos.");
    }

    /**
     * Método de debug para testar a consulta de estoque por referência
     */
    public function debugEstoque($referencia)
    {
        // Autorização: leitura de tecidos (debug)
        $user = auth()->user();
        if (!$user || !$user->canRead('tecidos')) {
            abort(403, 'Ação não autorizada.');
        }

        // Fazer a requisição diretamente para a API
        $response = Http::get(config('estoque.api_url'), [
            'empresa' => config('estoque.empresa'),
            'token' => config('estoque.token'),
            'armazenador' => config('estoque.armazenador')
            // Removido parâmetro referencia, vamos filtrar nos resultados
        ]);

        // Exibe a URL completa da requisição
        $url = config('estoque.api_url') . '?' . http_build_query([
            'empresa' => config('estoque.empresa'),
            'token' => config('estoque.token'),
            'armazenador' => config('estoque.armazenador')
            // Removido parâmetro referencia
        ]);

        // Exibe os dados brutos e processados
        $dadosBrutos = $response->json();
        
        // Verificar se a referência existe nos dados brutos
        $referenciaEncontrada = false;
        $itemReferencia = null;
        foreach ($dadosBrutos as $item) {
            if (isset($item['Referencia']) && $item['Referencia'] === $referencia) {
                $referenciaEncontrada = true;
                $itemReferencia = $item;
                break;
            }
        }
        
        $dadosProcessados = $this->estoqueService->consultarEstoqueTecido($referencia);

        // Retorna os dados para debug
        return response()->json([
            'url_requisicao' => $url,
            'status_code' => $response->status(),
            'dados_brutos_count' => count($dadosBrutos),
            'referencia_encontrada' => $referenciaEncontrada,
            'item_referencia' => $itemReferencia,
            'dados_processados' => $dadosProcessados,
            'referencia_buscada' => $referencia,
            'config_armazenador' => config('estoque.armazenador')
        ]);        
    }

    /**
     * Exibe o estoque de tecido por cor
     */
    public function estoquePorCor($id)
    {
        // Autorização: leitura de tecidos
        $user = auth()->user();
        if (!$user || !$user->canRead('tecidos')) {
            abort(403, 'Ação não autorizada.');
        }
        $tecido = Tecido::with('estoquesCores')->findOrFail($id);
        
        if ($tecido->estoquesCores->isEmpty()) {
            return redirect()->route('tecidos.show', $id)
                ->with('info', 'Não há informações de estoque por cor para este tecido.');
        }
        
        return view('tecidos.estoque-por-cor', compact('tecido'));
    }
    
    /**
     * Exibe os produtos que usam uma cor específica de tecido
     */
    public function produtosPorCor($tecidoId, $corId)
    {
        // Autorização: leitura de tecidos
        $user = auth()->user();
        if (!$user || !$user->canRead('tecidos')) {
            abort(403, 'Ação não autorizada.');
        }
        
        $tecido = Tecido::findOrFail($tecidoId);
        $estoqueCor = TecidoCorEstoque::findOrFail($corId);
        
        // Buscar produtos com variações de cor (apenas produtos com status.calc_necessidade = 1)
        $produtosVariacoes = [];
        $produtos = $tecido->produtosComNecessidade;
        
        foreach ($produtos as $produto) {
            $produtoCor = $produto->cores()
                ->where('cor', $estoqueCor->cor)
                ->first();
            
            if ($produtoCor && $produtoCor->quantidade > 0) {
                $necessidadeProduto = $produtoCor->quantidade * $produto->pivot->consumo;
                
                if ($necessidadeProduto > 0) {
                    $produtosVariacoes[] = [
                        'id' => $produto->id,
                        'referencia' => $produto->referencia,
                        'descricao' => $produto->descricao,
                        'necessidade' => $necessidadeProduto,
                        'tipo' => 'variação'
                    ];
                }
            }
        }
        
        // Buscar produtos com combinações de cores
        $produtosCombinacoes = [];
        
        foreach ($produtos as $produto) {
            $combinacoes = $produto->combinacoes;
            
            foreach ($combinacoes as $combinacao) {
                $componentes = $combinacao->componentes()
                    ->where('tecido_id', $tecido->id)
                    ->where('cor', $estoqueCor->cor)
                    ->get();
                
                foreach ($componentes as $componente) {
                    $necessidadeCombinacao = $combinacao->quantidade_pretendida * $componente->consumo;
                    
                    if ($necessidadeCombinacao > 0) {
                        $produtosCombinacoes[] = [
                            'id' => $produto->id,
                            'referencia' => $produto->referencia,
                            'descricao' => $produto->descricao . ' (' . $combinacao->descricao . ')',
                            'necessidade' => $necessidadeCombinacao,
                            'tipo' => 'combinação'
                        ];
                    }
                }
            }
        }
        
        // Combinar os resultados
        $produtosComNecessidade = array_merge($produtosVariacoes, $produtosCombinacoes);
        
        return view('tecidos.produtos-por-cor', [
            'tecido' => $tecido,
            'estoqueCor' => $estoqueCor,
            'produtosComNecessidade' => $produtosComNecessidade
        ]);
    }
    
    /**
     * Salva as quantidades pretendidas para as cores selecionadas
     */
    public function salvarQuantidades(Request $request, $id)
    {
        // Autorização: atualização de tecidos (quantidades)
        $user = auth()->user();
        if (!$user || !$user->canUpdate('tecidos')) {
            abort(403, 'Ação não autorizada.');
        }
        $tecido = Tecido::findOrFail($id);
        
        // Log para depuração - dados recebidos
        \Log::debug('Dados recebidos no salvarQuantidades:', $request->all());
        
        // Validar os dados recebidos
        $validator = Validator::make($request->all(), [
            'cores' => 'required|array',
            'cores.*.quantidade_pretendida' => 'nullable|numeric|min:0',
        ]);
        
        if ($validator->fails()) {
            \Log::debug('Validação falhou:', $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $coresSelecionadas = 0;
        $coresAtualizadas = 0;
        $logAtualizacoes = [];
        
        // Processar apenas as cores que foram selecionadas
        foreach ($request->cores as $estoqueCorId => $dados) {
            // Verificar se a cor foi selecionada
            if (!isset($dados['selecionada']) || $dados['selecionada'] != 1) {
                \Log::debug("Cor ID {$estoqueCorId} não selecionada, pulando.");
                continue;
            }
            
            $coresSelecionadas++;
            \Log::debug("Cor ID {$estoqueCorId} selecionada.");
            
            // Verificar se a quantidade pretendida foi informada
            if (!isset($dados['quantidade_pretendida']) || $dados['quantidade_pretendida'] == '') {
                \Log::debug("Cor ID {$estoqueCorId} sem quantidade pretendida informada, pulando.");
                continue;
            }
            
            \Log::debug("Cor ID {$estoqueCorId} com quantidade pretendida: {$dados['quantidade_pretendida']}");
            
            // Buscar o registro de estoque da cor
            $estoqueCor = TecidoCorEstoque::find($estoqueCorId);
            
            if (!$estoqueCor) {
                \Log::debug("Cor ID {$estoqueCorId} não encontrada no banco de dados.");
                continue;
            }
            
            if ($estoqueCor->tecido_id != $tecido->id) {
                \Log::debug("Cor ID {$estoqueCorId} não pertence ao tecido ID {$tecido->id}.");
                continue; // Ignorar se o registro não pertencer ao tecido
            }
            
            // Atualizar a quantidade pretendida
            $valorAntigo = $estoqueCor->quantidade_pretendida;
            $estoqueCor->quantidade_pretendida = $dados['quantidade_pretendida'];
            
            try {
                $salvou = $estoqueCor->save();
                \Log::debug("Salvamento da cor ID {$estoqueCorId}: " . ($salvou ? 'Sucesso' : 'Falha'));
                
                // Verificar se o valor foi realmente salvo
                $estoqueCor->refresh();
                \Log::debug("Valor após salvar: {$estoqueCor->quantidade_pretendida}, Valor esperado: {$dados['quantidade_pretendida']}");
                
                $logAtualizacoes[] = [
                    'id' => $estoqueCorId,
                    'cor' => $estoqueCor->cor,
                    'valor_antigo' => $valorAntigo,
                    'valor_novo' => $estoqueCor->quantidade_pretendida,
                    'valor_esperado' => $dados['quantidade_pretendida'],
                    'salvou' => $salvou
                ];
                
                $coresAtualizadas++;
            } catch (\Exception $e) {
                \Log::error("Erro ao salvar cor ID {$estoqueCorId}: " . $e->getMessage());
            }
        }
        
        \Log::debug('Resumo das atualizações:', [
            'cores_selecionadas' => $coresSelecionadas,
            'cores_atualizadas' => $coresAtualizadas,
            'detalhes' => $logAtualizacoes
        ]);
        
        
        if ($coresSelecionadas == 0) {
            return redirect()->route('tecidos.estoque-por-cor', $tecido->id)
                ->with('warning', 'Nenhuma cor foi selecionada.');
        }
        
        if ($coresAtualizadas == 0) {
            return redirect()->route('tecidos.estoque-por-cor', $tecido->id)
                ->with('warning', 'Nenhuma quantidade pretendida foi informada para as cores selecionadas.');
        }
        
        return redirect()->route('tecidos.estoque-por-cor', $tecido->id)
            ->with('success', "Quantidades pretendidas atualizadas para {$coresAtualizadas} cores.");
    }
    
    /**
     * Exibe o formulário para importação de estoque por cores
     */
    public function importarEstoqueForm()
    {
        // Autorização: atualização de tecidos (importação)
        $user = auth()->user();
        if (!$user || !$user->canUpdate('tecidos')) {
            abort(403, 'Ação não autorizada.');
        }
        return view('tecidos.importar-estoque');
    }
    
    /**
     * Processa a importação de estoque por cores a partir de um arquivo CSV
     */
    public function importarEstoque(Request $request)
    {
        // Autorização: atualização de tecidos (importação)
        $user = auth()->user();
        if (!$user || !$user->canUpdate('tecidos')) {
            abort(403, 'Ação não autorizada.');
        }
        $request->validate([
            'arquivo_csv' => 'required|file|mimes:csv,txt|max:2048',
        ]);
        
        $arquivo = $request->file('arquivo_csv');
        $caminho = $arquivo->getRealPath();
        
        $handle = fopen($caminho, 'r');
        $cabecalho = fgetcsv($handle, 1000, ',');
        
        // Verificar se o cabeçalho tem os campos necessários
        $camposNecessarios = ['referencia', 'cor', 'quantidade'];
        $camposFaltando = array_diff($camposNecessarios, $cabecalho);
        
        if (!empty($camposFaltando)) {
            return redirect()->route('tecidos.importar-estoque-form')
                ->with('error', 'O arquivo CSV não contém todos os campos necessários: ' . implode(', ', $camposFaltando));
        }
        
        $atualizados = 0;
        $erros = [];
        $dataAtualizacao = now();
        
        // Processar cada linha do CSV
        while (($dados = fgetcsv($handle, 1000, ',')) !== false) {
            $linha = array_combine($cabecalho, $dados);
            
            // Buscar o tecido pela referência
            $tecido = Tecido::where('referencia', $linha['referencia'])->first();
            
            if (!$tecido) {
                $erros[] = "Tecido com referência '{$linha['referencia']}' não encontrado.";
                continue;
            }
            
            // Verificar se a quantidade é válida
            if (!is_numeric($linha['quantidade']) || $linha['quantidade'] < 0) {
                $erros[] = "Quantidade inválida para o tecido '{$linha['referencia']}', cor '{$linha['cor']}'.";
                continue;
            }
            
            // Atualizar ou criar o registro de estoque por cor
            $estoqueCor = TecidoCorEstoque::updateOrCreate(
                [
                    'tecido_id' => $tecido->id,
                    'cor' => $linha['cor']
                ],
                [
                    'quantidade' => $linha['quantidade'],
                    'codigo_cor' => $linha['codigo_cor'] ?? null,
                    'data_atualizacao' => $dataAtualizacao,
                    'observacoes' => $linha['observacoes'] ?? null
                ]
            );
            
            $atualizados++;
        }
        
        fclose($handle);
        
        // Atualizar a quantidade total de estoque para cada tecido afetado
        $tecidosAfetados = TecidoCorEstoque::where('data_atualizacao', $dataAtualizacao)
            ->select('tecido_id')
            ->distinct()
            ->get()
            ->pluck('tecido_id');
            
        foreach ($tecidosAfetados as $tecidoId) {
            $tecido = Tecido::find($tecidoId);
            if ($tecido) {
                $tecido->update([
                    'quantidade_estoque' => $tecido->total_estoque_por_cores,
                    'ultima_consulta_estoque' => $dataAtualizacao
                ]);
            }
        }
        
        $mensagem = "Foram atualizados {$atualizados} registros de estoque por cor.";
        if (!empty($erros)) {
            $mensagem .= "\n\nErros encontrados:\n" . implode("\n", $erros);
            return redirect()->route('tecidos.importar-estoque-form')
                ->with('warning', $mensagem);
        }
        
        return redirect()->route('tecidos.index')
            ->with('success', $mensagem);
    }
}
