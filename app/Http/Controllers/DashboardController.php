<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Movimentacao;
use App\Models\Tecido;
use App\Models\Estilista;
use App\Models\Marca;
use App\Models\Tipo;
use App\Models\Status;
use App\Models\Situacao;
use App\Models\GrupoProduto;
use App\Models\Localizacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    /**
     * Exibe o dashboard com estatísticas e dados relevantes.
     */
    public function index()
    {
        // Estatísticas gerais
        $totalProdutos = Produto::count();
        $totalMovimentacoes = Movimentacao::count();
        $movimentacoesHoje = Movimentacao::whereDate('created_at', Carbon::today())->count();
        $totalGrupoProdutos = GrupoProduto::count();

        // Estatísticas de produtos por ano
        $anoAtual = Carbon::now()->year;
        $dataAtual = Carbon::now();

        // Produtos cadastrados no ano atual
        $produtosAnoAtual = Produto::whereYear('created_at', $anoAtual)->count();

        // Produtos cadastrados no ano passado
        $produtosAnoPassado = Produto::whereYear('created_at', $anoAtual - 1)->count();

        // Produtos cadastrados no ano atual até a data atual
        $produtosAnoAtualAteHoje = Produto::whereYear('created_at', $anoAtual)
            ->whereDate('created_at', '<=', $dataAtual)
            ->count();

        // Produtos cadastrados no ano passado até a mesma data
        $dataAnoPassado = Carbon::now()->subYear();
        $produtosAnoPassadoAteHoje = Produto::whereYear('created_at', $anoAtual - 1)
            ->whereDate('created_at', '<=', $dataAnoPassado)
            ->count();

        // Calcular variação percentual
        $variacaoPercentual = $produtosAnoPassadoAteHoje > 0
            ? round((($produtosAnoAtualAteHoje - $produtosAnoPassadoAteHoje) / $produtosAnoPassadoAteHoje) * 100, 1)
            : 100;

        // Estatísticas dos últimos 5 anos
        $estatisticasUltimos5Anos = [];
        for ($i = 0; $i < 5; $i++) {
            $ano = $anoAtual - $i;
            $estatisticasUltimos5Anos[$ano] = Produto::whereYear('created_at', $ano)->count();
        }

        // Comparação do ano corrente com anos anteriores até a data atual
        $comparacaoAnos = [];
        $mesAtual = Carbon::now()->month;
        $diaAtual = Carbon::now()->day;

        for ($i = 0; $i < 3; $i++) {
            $ano = $anoAtual - $i;
            $dataLimite = Carbon::create($ano, $mesAtual, $diaAtual);
            $comparacaoAnos[$ano] = Produto::whereYear('created_at', $ano)
                ->whereDate('created_at', '<=', $dataLimite)
                ->count();
        }

        // Projeção para o ano atual
        $diasDecorridos = Carbon::now()->dayOfYear;
        $diasNoAno = Carbon::now()->isLeapYear() ? 366 : 365;
        $projecaoProdutosAnoAtual = $diasNoAno > 0
            ? round(($produtosAnoAtualAteHoje / $diasDecorridos) * $diasNoAno)
            : 0;

        // Produtos recentes
        $produtosRecentes = Produto::with(['marca', 'status'])
                                ->latest()
                                ->take(5)
                                ->get();

        // Movimentações recentes
        $movimentacoesRecentes = Movimentacao::with(['produto', 'tipo', 'situacao'])
                                    ->latest()
                                    ->take(5)
                                    ->get();

        // Produtos do setor do usuário autenticado
        $produtosDoSetor = collect();
        $localizacaoUsuario = auth()->user()->localizacao_id;

        if ($localizacaoUsuario) {
            // Busca produtos que estão na localização do usuário (setor) através da última movimentação
            $produtosDoSetor = Produto::whereHas('movimentacoes', function($query) use ($localizacaoUsuario) {
                $query->where('localizacao_id', $localizacaoUsuario)
                      ->whereIn('id', function($subquery) {
                          $subquery->selectRaw('MAX(id)')
                                   ->from('movimentacoes')
                                   ->groupBy('produto_id');
                      });
            })
            ->with(['marca', 'status', 'estilista', 'grupoProduto'])
            ->latest()
            ->take(10)
            ->get();
        }


        // Estatísticas por tipo de movimentação
        $estatisticasPorTipo = Movimentacao::select('tipo_id', DB::raw('count(*) as total'))
                                ->groupBy('tipo_id')
                                ->with('tipo')
                                ->get();

        // Estatísticas de produtos ativos por estilista
        $statusAtivo = Status::where('descricao', 'Ativo')->first();
        $statusAtivoId = $statusAtivo ? $statusAtivo->id : null;

        $produtosAtivosPorEstilista = [];
        if ($statusAtivoId) {
            // Obter todos os estilistas com produtos ativos
            $todosEstilistas = Produto::select('estilistas.nome_estilista', DB::raw('count(*) as total'))
                                ->join('estilistas', 'produtos.estilista_id', '=', 'estilistas.id')
                                ->where('produtos.status_id', $statusAtivoId)
                                ->groupBy('estilistas.nome_estilista')
                                ->orderByDesc('total')
                                ->get();

            // Separar os top 10 estilistas
            $top10Estilistas = $todosEstilistas->take(10);

            // Calcular o total de produtos dos estilistas restantes
            $outrosTotal = $todosEstilistas->skip(10)->sum('total');

            // Criar a coleção final com os top 10 + outros
            $produtosAtivosPorEstilista = $top10Estilistas;

            // Adicionar a categoria "Outros" se houver estilistas além dos top 10
            if ($outrosTotal > 0) {
                $produtosAtivosPorEstilista->push([
                    'nome_estilista' => 'Outros',
                    'total' => $outrosTotal
                ]);
            }
        }

        // Estatísticas de produtos ativos por estilista ao longo do tempo (últimos 12 meses)
        $produtosAtivosPorMes = [];
        $mesesLabels = [];

        if ($statusAtivoId) {
            for ($i = 11; $i >= 0; $i--) {
                $data = Carbon::now()->subMonths($i);
                $mes = $data->format('M Y');
                $mesesLabels[] = $mes;

                $produtosAtivosNoMes = Produto::where('status_id', $statusAtivoId)
                                        ->whereYear('created_at', $data->year)
                                        ->whereMonth('created_at', $data->month)
                                        ->count();

                $produtosAtivosPorMes[] = $produtosAtivosNoMes;
            }
        }

        // Dados de Capacidade das Localizações (3 meses: atual + 2 futuros)
        $mesAtual = Carbon::now()->month;
        $anoAtual = Carbon::now()->year;
        
        $capacidadeLocalizacoes = [];
        $mesesCapacidade = [];
        
        // Gerar os 3 meses (atual + 2 futuros)
        for ($i = 0; $i < 3; $i++) {
            $data = Carbon::now()->copy()->addMonths($i);
            $mesesCapacidade[] = [
                'mes' => $data->month,
                'ano' => $data->year,
                'label' => ucfirst($data->locale('pt_BR')->translatedFormat('F/Y'))
            ];
        }
        
        // Buscar localizações que possuem capacidade mensal cadastrada nos próximos 3 meses
        $localizacoesComCapacidade = \App\Models\LocalizacaoCapacidadeMensal::where(function($query) use ($mesesCapacidade) {
                foreach ($mesesCapacidade as $mesData) {
                    $query->orWhere(function($q) use ($mesData) {
                        $q->where('mes', $mesData['mes'])
                          ->where('ano', $mesData['ano']);
                    });
                }
            })
            ->where('capacidade', '>', 0)
            ->distinct()
            ->pluck('localizacao_id')
            ->unique()
            ->toArray();
        
        // Se não encontrou localizações com capacidade mensal, retornar vazio
        if (empty($localizacoesComCapacidade)) {
            $localizacoes = collect();
        } else {
            $localizacoes = \App\Models\Localizacao::where('ativo', true)
                ->whereIn('id', $localizacoesComCapacidade)
                ->orderBy('nome_localizacao')
                ->get();
        }
        
        foreach ($localizacoes as $localizacao) {
            $dadosPorMes = [];
            
            foreach ($mesesCapacidade as $mesData) {
                // Buscar capacidade mensal
                $capacidadeMensal = \App\Models\LocalizacaoCapacidadeMensal::where('localizacao_id', $localizacao->id)
                    ->where('mes', $mesData['mes'])
                    ->where('ano', $mesData['ano'])
                    ->first();
                
                // Usar apenas capacidade mensal cadastrada
                $capacidade = $capacidadeMensal ? $capacidadeMensal->capacidade : 0;
                $previsto = $capacidadeMensal ? $capacidadeMensal->getProdutosPrevistos() : 0;
                
                $dadosPorMes[] = [
                    'capacidade' => (int) $capacidade,
                    'previsto' => (int) $previsto,
                    'saldo' => (int) ($capacidade - $previsto),
                    'percentual' => $capacidade > 0 ? round(($previsto / $capacidade) * 100, 1) : 0
                ];
            }
            
            $capacidadeLocalizacoes[] = [
                'nome' => $localizacao->nome_localizacao,
                'nome_reduzido' => $localizacao->nome_reduzido,
                'dados' => $dadosPorMes
            ];
        }

        return view('dashboard', compact(
            'totalProdutos',
            'totalMovimentacoes',
            'movimentacoesHoje',
            'totalGrupoProdutos',
            'produtosAnoAtual',
            'produtosAnoPassado',
            'produtosAnoAtualAteHoje',
            'produtosAnoPassadoAteHoje',
            'variacaoPercentual',
            'estatisticasUltimos5Anos',
            'comparacaoAnos',
            'projecaoProdutosAnoAtual',
            'produtosRecentes',
            'movimentacoesRecentes',
            'estatisticasPorTipo',
            'produtosAtivosPorEstilista',
            'produtosAtivosPorMes',
            'mesesLabels',
            'produtosDoSetor',
            'capacidadeLocalizacoes',
            'mesesCapacidade'
        ));
    }

    /**
     * Busca dados de produtos por ano para atualização dinâmica dos gráficos.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDadosPorAno(Request $request)
    {
        $ano = $request->input('ano', Carbon::now()->year);

        // Estatísticas de produtos ativos por estilista para o ano selecionado
        $statusAtivo = Status::where('descricao', 'Ativo')->first();
        $statusAtivoId = $statusAtivo ? $statusAtivo->id : null;

        $produtosAtivosPorEstilista = [];
        if ($statusAtivoId) {
            // Obter todos os estilistas com produtos ativos do ano selecionado
            $todosEstilistas = Produto::select('estilistas.nome_estilista', DB::raw('count(*) as total'))
                                ->join('estilistas', 'produtos.estilista_id', '=', 'estilistas.id')
                                ->where('produtos.status_id', $statusAtivoId)
                                ->whereYear('produtos.created_at', $ano)
                                ->groupBy('estilistas.nome_estilista')
                                ->orderByDesc('total')
                                ->get();

            // Separar os top 10 estilistas
            $top10Estilistas = $todosEstilistas->take(10);

            // Calcular o total de produtos dos estilistas restantes
            $outrosTotal = $todosEstilistas->skip(10)->sum('total');

            // Criar a coleção final com os top 10 + outros
            $produtosAtivosPorEstilista = $top10Estilistas;

            // Adicionar a categoria "Outros" se houver estilistas além dos top 10
            if ($outrosTotal > 0) {
                $produtosAtivosPorEstilista->push([
                    'nome_estilista' => 'Outros',
                    'total' => $outrosTotal
                ]);
            }
        }

        // Estatísticas de produtos por mês para o ano selecionado
        $produtosPorMes = [];
        $mesesLabels = [];

        for ($i = 1; $i <= 12; $i++) {
            $data = Carbon::createFromDate($ano, $i, 1);
            $mes = $data->format('M');
            $mesesLabels[] = $mes;

            $produtosNoMes = Produto::whereYear('created_at', $ano)
                            ->whereMonth('created_at', $i)
                            ->count();

            $produtosPorMes[] = $produtosNoMes;
        }

        // Produtos ativos por mês para o ano selecionado
        $produtosAtivosPorMes = [];

        if ($statusAtivoId) {
            for ($i = 1; $i <= 12; $i++) {
                $produtosAtivosNoMes = Produto::where('status_id', $statusAtivoId)
                                        ->whereYear('created_at', $ano)
                                        ->whereMonth('created_at', $i)
                                        ->count();

                $produtosAtivosPorMes[] = $produtosAtivosNoMes;
            }
        }

        return response()->json([
            'produtosAtivosPorEstilista' => [
                'labels' => $produtosAtivosPorEstilista->pluck('nome_estilista')->toArray(),
                'data' => $produtosAtivosPorEstilista->pluck('total')->toArray(),
            ],
            'produtosPorMes' => [
                'labels' => $mesesLabels,
                'data' => $produtosPorMes,
            ],
            'produtosAtivosPorMes' => [
                'labels' => $mesesLabels,
                'data' => $produtosAtivosPorMes,
            ],
            'ano' => $ano
        ]);
    }

    /**
     * Exibe um gráfico de Estilistas dos produtos criados por estilista no período selecionado.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function produtosPorEstilista(Request $request)
    {
        // Determinar o período com base na seleção do usuário
        $periodo = $request->input('periodo', 'ultimos_12_meses');
        $dataAtual = Carbon::now();
        $titulo = '';

        // Definir datas de início e fim com base no período selecionado
        switch ($periodo) {
            case 'ultimos_3_meses':
                $dataInicio = $dataAtual->copy()->subMonths(3);
                $dataFim = $dataAtual->copy();
                $titulo = 'Últimos 3 meses';
                break;

            case 'ultimos_6_meses':
                $dataInicio = $dataAtual->copy()->subMonths(6);
                $dataFim = $dataAtual->copy();
                $titulo = 'Últimos 6 meses';
                break;

            case 'ano_atual':
                $dataInicio = Carbon::createFromDate($dataAtual->year, 1, 1)->startOfDay();
                $dataFim = $dataAtual->copy();
                $titulo = 'Ano atual (' . $dataAtual->year . ')';
                break;

            case 'ano_anterior':
                $dataInicio = Carbon::createFromDate($dataAtual->year - 1, 1, 1)->startOfDay();
                $dataFim = Carbon::createFromDate($dataAtual->year - 1, 12, 31)->endOfDay();
                $titulo = 'Ano anterior (' . ($dataAtual->year - 1) . ')';
                break;

            case 'personalizado':
                $dataInicio = $request->filled('data_inicio')
                    ? Carbon::createFromFormat('Y-m-d', $request->input('data_inicio'))->startOfDay()
                    : $dataAtual->copy()->subMonths(12);

                $dataFim = $request->filled('data_fim')
                    ? Carbon::createFromFormat('Y-m-d', $request->input('data_fim'))->endOfDay()
                    : $dataAtual->copy();

                $titulo = 'Período personalizado';
                break;

            case 'ultimos_12_meses':
            default:
                $dataInicio = $dataAtual->copy()->subMonths(12);
                $dataFim = $dataAtual->copy();
                $titulo = 'Últimos 12 meses';
                break;
        }

        // Buscar produtos do período selecionado agrupados por estilista
        $produtosPorEstilista = Produto::select('estilistas.id as estilista_id', 'estilistas.nome_estilista', DB::raw('count(*) as total'))
            ->join('estilistas', 'produtos.estilista_id', '=', 'estilistas.id')
            ->where('produtos.created_at', '>=', $dataInicio)
            ->where('produtos.created_at', '<=', $dataFim)
            ->groupBy('estilistas.id', 'estilistas.nome_estilista')
            ->orderByDesc('total')
            ->get();

        // Separar os top 10 estilistas
        $topEstilistas = $produtosPorEstilista->take(10);

        // Calcular o total de produtos dos estilistas restantes
        $outrosTotal = $produtosPorEstilista->skip(10)->sum('total');

        // Criar a coleção final com os top 10 + outros
        $dadosGrafico = $topEstilistas->toArray();

        // Adicionar a categoria "Outros" se houver estilistas além dos top 10
        if ($outrosTotal > 0) {
            $dadosGrafico[] = [
                'estilista_id' => null, // Não há ID para "Outros"
                'nome_estilista' => 'Outros',
                'total' => $outrosTotal
            ];
        }

        // Preparar dados para o gráfico
        $labels = array_column($dadosGrafico, 'nome_estilista');
        $data = array_column($dadosGrafico, 'total');
        
        // Gerar cores aleatórias para o gráfico
        $cores = [];
        foreach ($labels as $index => $label) {
            // Gerar cores HSL com boa separação visual
            $hue = ($index * 137) % 360; // Fórmula para distribuir bem as cores
            $cores[] = "hsl($hue, 70%, 60%)";
        }
        
        // Período do relatório
        $periodoInicio = $dataInicio->format('d/m/Y');
        $periodoFim = $dataFim->format('d/m/Y');
        
        // Passar os dados completos dos estilistas para a view
        return view('dashboard.produtos-por-estilista', compact('labels', 'data', 'cores', 'periodoInicio', 'periodoFim', 'titulo', 'dadosGrafico'));
    }

    /**
     * Exibe um resumo da média de atraso em dias por localização para movimentações não concluídas.
     *
     * @return \Illuminate\View\View
     */
    public function mediaDiasAtraso()
    {
        // Função para calcular dias úteis entre duas datas (excluindo sábados e domingos)
        $calcularDiasUteis = function($dataInicio, $dataFim) {
            if (!$dataInicio) return 0;

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
        };

        // Buscar todas as movimentações não concluídas
        $movimentacoes = Movimentacao::with(['localizacao'])
            ->where('concluido', false)
            ->get();

        // Agrupar por localização e calcular média de dias
        $localizacoes = $movimentacoes->groupBy('localizacao_id');

        $mediaDiasPorLocalizacao = new Collection();

        foreach ($localizacoes as $localizacaoId => $grupo) {
            $localizacao = $grupo->first()->localizacao;

            if (!$localizacao) continue;

            $totalDias = 0;
            $totalMovimentacoes = 0;
            $totalAtrasados = 0;

            foreach ($grupo as $movimentacao) {
                // Calcular dias úteis entre data de entrada e hoje (ou data de saída se existir)
                $dataFim = $movimentacao->data_saida ?? now();
                $diasUteis = $calcularDiasUteis($movimentacao->data_entrada, $dataFim);

                $totalDias += $diasUteis;
                $totalMovimentacoes++;

                // Verificar se está atrasado em relação ao prazo do setor
                if ($localizacao->prazo && $diasUteis > $localizacao->prazo) {
                    $totalAtrasados++;
                }
            }

            // Calcular média de dias
            $mediaDias = $totalMovimentacoes > 0 ? round($totalDias / $totalMovimentacoes, 1) : 0;

            // Calcular percentual de atrasados
            $percentualAtrasados = $totalMovimentacoes > 0 ? round(($totalAtrasados / $totalMovimentacoes) * 100) : 0;

            // Determinar status baseado no prazo do setor
            $status = 'normal';
            if ($localizacao->prazo) {
                if ($mediaDias > $localizacao->prazo * 1.5) {
                    $status = 'critico';
                } elseif ($mediaDias > $localizacao->prazo) {
                    $status = 'atencao';
                }
            }

            $mediaDiasPorLocalizacao->push([
                'localizacao' => $localizacao->nome_localizacao,
                'localizacao_id' => $localizacao->id,
                'media_dias' => $mediaDias,
                'total_movimentacoes' => $totalMovimentacoes,
                'total_atrasados' => $totalAtrasados,
                'percentual_atrasados' => $percentualAtrasados,
                'prazo_setor' => $localizacao->prazo,
                'status' => $status,
                'ativo' => $localizacao->ativo
            ]);
        }

        // Ordenar por status (crítico primeiro) e depois por média de dias (decrescente)
        $mediaDiasPorLocalizacao = $mediaDiasPorLocalizacao->sortBy([
            ['status', 'desc'],
            ['media_dias', 'desc']
        ])->values();

        // Separar localizações ativas e inativas
        $localizacoesAtivas = $mediaDiasPorLocalizacao->where('ativo', true)->values();
        $localizacoesInativas = $mediaDiasPorLocalizacao->where('ativo', false)->values();

        // Preparar dados para o gráfico
        $labels = $localizacoesAtivas->pluck('localizacao')->toArray();
        $data = $localizacoesAtivas->pluck('media_dias')->toArray();

        // Gerar cores baseadas no status
        $cores = $localizacoesAtivas->map(function ($item) {
            switch ($item['status']) {
                case 'critico':
                    return 'rgb(239, 68, 68)'; // Vermelho
                case 'atencao':
                    return 'rgb(245, 158, 11)'; // Amarelo
                default:
                    return 'rgb(34, 197, 94)'; // Verde
            }
        })->toArray();

        return view('consultas.media-dias-atraso', compact('localizacoesAtivas', 'localizacoesInativas', 'labels', 'data', 'cores'));
    }
}
