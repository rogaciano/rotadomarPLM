<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Movimentações</title>
    <style>
        @page {
            margin: 12mm;
            size: A4 landscape;
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 8px;
            font-size: 9px;
        }
        .container {
            width: 100%;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #3B82F6;
            padding-bottom: 10px;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #1F2937;
        }
        .subtitle {
            font-size: 12px;
            margin-bottom: 5px;
            color: #6B7280;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 8px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th {
            background-color: #F3F4F6;
            text-align: left;
            padding: 4px 3px;
            font-size: 8px;
            font-weight: bold;
            color: #374151;
        }
        td {
            padding: 3px;
            font-size: 7px;
            vertical-align: top;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .status-badge {
            padding: 1px 2px;
            border-radius: 2px;
            font-size: 6px;
            font-weight: bold;
        }
        .status-concluido {
            background-color: #D1FAE5;
            color: #065F46;
        }
        .status-pendente {
            background-color: #FEE2E2;
            color: #991B1B;
        }
        .filters-summary {
            margin-bottom: 15px;
            padding: 8px;
            background-color: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 4px;
        }
        .filter-item {
            display: inline-block;
            margin-right: 15px;
            margin-bottom: 5px;
        }
        .filter-label {
            font-weight: bold;
            margin-right: 5px;
            color: #374151;
        }
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 9px;
            color: #6B7280;
            border-top: 1px solid #E5E7EB;
            padding-top: 8px;
        }
        .page-number:after {
            content: counter(page);
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #6B7280;
            font-style: italic;
        }
        .total-registros {
            margin-bottom: 10px;
            padding: 6px;
            background-color: #EFF6FF;
            border: 1px solid #DBEAFE;
            border-radius: 4px;
            font-weight: bold;
            color: #1E40AF;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="title">Rota do Mar - LISTA DE MOVIMENTAÇÕES</div>
            <div class="subtitle">Data do relatório: {{ now()->format('d/m/Y H:i') }}</div>
        </div>

        <!-- Resumo dos filtros aplicados -->
        @isset($request)
        <div class="filters-summary">
            <div style="font-weight: bold; margin-bottom: 5px; color: #1F2937;">Filtros aplicados:</div>
            @php
                $hasFilters = false;
            @endphp

            @if($request->filled('referencia'))
                <div class="filter-item">
                    <span class="filter-label">Referência:</span> {{ $request->referencia }}
                </div>
                @php $hasFilters = true; @endphp
            @endif

            @if($request->filled('produto'))
                <div class="filter-item">
                    <span class="filter-label">Produto:</span> {{ $request->produto }}
                </div>
                @php $hasFilters = true; @endphp
            @endif

            @if($request->filled('tipo_id'))
                @php
                    $tipos = \App\Models\Tipo::pluck('descricao', 'id');
                    $tipoNome = $tipos[$request->tipo_id] ?? 'N/A';
                @endphp
                <div class="filter-item">
                    <span class="filter-label">Tipo:</span> {{ $tipoNome }}
                </div>
                @php $hasFilters = true; @endphp
            @endif

            @if($request->filled('grupo_produto_id'))
                @php
                    $grupoIds = is_array($request->grupo_produto_id) ? $request->grupo_produto_id : [$request->grupo_produto_id];
                    $grupos = \App\Models\GrupoProduto::whereIn('id', $grupoIds)->pluck('descricao');
                    $gruposNomes = $grupos->implode(', ');
                @endphp
                <div class="filter-item">
                    <span class="filter-label">Grupo(s):</span> {{ $gruposNomes }}
                </div>
                @php $hasFilters = true; @endphp
            @endif

            @if($request->filled('situacao_id'))
                @php
                    $situacaoIds = is_array($request->situacao_id) ? $request->situacao_id : [$request->situacao_id];
                    $situacoes = \App\Models\Situacao::whereIn('id', $situacaoIds)->pluck('descricao');
                    $situacoesNomes = $situacoes->implode(', ');
                @endphp
                <div class="filter-item">
                    <span class="filter-label">Situação(ões):</span> {{ $situacoesNomes }}
                </div>
                @php $hasFilters = true; @endphp
            @endif

            @if($request->filled('localizacao_id'))
                @php
                    $localizacaoIds = is_array($request->localizacao_id) ? $request->localizacao_id : [$request->localizacao_id];
                    $localizacoes = \App\Models\Localizacao::whereIn('id', $localizacaoIds)->pluck('nome_localizacao');
                    $localizacoesNomes = $localizacoes->implode(', ');
                @endphp
                <div class="filter-item">
                    <span class="filter-label">Localização(ões):</span> {{ $localizacoesNomes }}
                </div>
                @php $hasFilters = true; @endphp
            @endif

            @if($request->filled('marca_id'))
                @php
                    $marcas = \App\Models\Marca::pluck('nome_marca', 'id');
                    $marcaNome = $marcas[$request->marca_id] ?? 'N/A';
                @endphp
                <div class="filter-item">
                    <span class="filter-label">Marca:</span> {{ $marcaNome }}
                </div>
                @php $hasFilters = true; @endphp
            @endif

            @if($request->filled('status_id'))
                @php
                    $statuses = \App\Models\Status::pluck('descricao', 'id');
                    $statusNome = $statuses[$request->status_id] ?? 'N/A';
                @endphp
                <div class="filter-item">
                    <span class="filter-label">Status:</span> {{ $statusNome }}
                </div>
                @php $hasFilters = true; @endphp
            @endif

            @if($request->filled('comprometido'))
                <div class="filter-item">
                    <span class="filter-label">Comprometido:</span>
                    {{ $request->comprometido == 1 ? 'Sim' : 'Não' }}
                </div>
                @php $hasFilters = true; @endphp
            @endif

            @if($request->filled('concluido'))
                <div class="filter-item">
                    <span class="filter-label">Status Conclusão:</span>
                    {{ $request->concluido == '1' ? 'Concluídas' : ($request->concluido == '0' ? 'Não Concluídas' : 'Todas') }}
                </div>
                @php $hasFilters = true; @endphp
            @endif

            @if($request->filled('status_dias'))
                <div class="filter-item">
                    <span class="filter-label">Status de Dias:</span>
                    {{ $request->status_dias == 'atrasados' ? 'Atrasados' : ($request->status_dias == 'em_dia' ? 'Em Dia' : 'Todos') }}
                </div>
                @php $hasFilters = true; @endphp
            @endif

            @if($request->filled('data_inicio') || $request->filled('data_fim'))
                <div class="filter-item">
                    <span class="filter-label">Período:</span>
                    {{ $request->filled('data_inicio') ? \Carbon\Carbon::parse($request->data_inicio)->format('d/m/Y') : 'Início' }}
                    até
                    {{ $request->filled('data_fim') ? \Carbon\Carbon::parse($request->data_fim)->format('d/m/Y') : 'Hoje' }}
                </div>
                @php $hasFilters = true; @endphp
            @endif

            @if(!$hasFilters)
                <div class="filter-item">
                    <span style="color: #6B7280; font-style: italic;">Nenhum filtro aplicado</span>
                </div>
            @endif
        </div>
        @endisset

        <!-- Total de registros -->
        <div class="total-registros">
            Total de registros: {{ count($movimentacoes) }}
        </div>

        <!-- Tabela de movimentações -->
        @if(count($movimentacoes) > 0)
            @php
                // Função para calcular dias úteis (mesma do index) - declarada fora do loop
                function calcularDiasUteisPDF($dataInicio, $dataFim) {
                    if (!$dataInicio) return null;
                    if (!$dataFim) $dataFim = now();
                    
                    $diasUteis = 0;
                    $dataAtual = clone $dataInicio;
                    
                    while ($dataAtual <= $dataFim) {
                        if ($dataAtual->dayOfWeek != 0 && $dataAtual->dayOfWeek != 6) {
                            $diasUteis++;
                        }
                        $dataAtual->addDay();
                    }
                    return $diasUteis;
                }
            @endphp
            <table>
                <thead>
                    <tr>
                        <th width="3%" class="text-center">ID</th>
                        <th width="7%">Referência</th>
                        <th width="11%">Produto</th>
                        <th width="7%">Status</th>
                        <th width="5%" class="text-center">Concl.</th>
                        <th width="9%">Localização</th>
                        <th width="7%">Tipo</th>
                        <th width="7%">Situação</th>
                        <th width="7%" class="text-center">Dt. Entrada</th>
                        <th width="7%" class="text-center">Dt. Conclusão</th>
                        <th width="7%" class="text-center">Dt. Devolução</th>
                        <th width="5%" class="text-center">Comp.</th>
                        <th width="14%">Observação</th>
                        <th width="11%" class="text-center">Dias</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($movimentacoes as $movimentacao)
                        @php
                            // Calcular dias úteis como no index
                            $diasEntre = null;
                            $prazoExcedido = false;
                            $prazoSetor = null;

                            if ($movimentacao->data_entrada) {
                                if ($movimentacao->data_saida) {
                                    $diasEntre = calcularDiasUteisPDF($movimentacao->data_entrada, $movimentacao->data_saida);
                                } else {
                                    $diasEntre = calcularDiasUteisPDF($movimentacao->data_entrada, now());
                                }

                                // Verificar prazo: prioridade para situação, depois localização
                                if ($movimentacao->situacao && $movimentacao->situacao->prazo) {
                                    $prazoExcedido = $diasEntre > $movimentacao->situacao->prazo;
                                    $prazoSetor = $movimentacao->situacao->prazo;
                                } elseif ($movimentacao->localizacao && $movimentacao->localizacao->prazo) {
                                    $prazoExcedido = $diasEntre > $movimentacao->localizacao->prazo;
                                    $prazoSetor = $movimentacao->localizacao->prazo;
                                }
                            }
                        @endphp
                        <tr>
                            <td class="text-center">{{ $movimentacao->id }}</td>
                            <td>{{ $movimentacao->produto->referencia ?? 'N/A' }}</td>
                            <td>{{ Str::limit($movimentacao->produto->descricao ?? 'N/A', 25) }}</td>
                            <td>
                                @if($movimentacao->produto && $movimentacao->produto->status)
                                    <span class="status-badge {{ $movimentacao->produto->status->descricao == 'Ativo' ? 'status-concluido' : 'status-pendente' }}">
                                        {{ $movimentacao->produto->status->descricao }}
                                    </span>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="text-center">
                                @if($movimentacao->data_saida)
                                    <span class="status-badge status-concluido">Sim</span>
                                @else
                                    <span class="status-badge status-pendente">Não</span>
                                @endif
                            </td>
                            <td>{{ $movimentacao->localizacao->nome_localizacao ?? 'N/A' }}</td>
                            <td>{{ $movimentacao->tipo->descricao ?? 'N/A' }}</td>
                            <td>{{ $movimentacao->situacao->descricao ?? 'N/A' }}</td>
                            <td class="text-center">{{ $movimentacao->data_entrada ? $movimentacao->data_entrada->format('d/m/Y') : 'N/A' }}</td>
                            <td class="text-center">{{ $movimentacao->data_saida ? $movimentacao->data_saida->format('d/m/Y') : '-' }}</td>
                            <td class="text-center">{{ $movimentacao->data_devolucao ? $movimentacao->data_devolucao->format('d/m/Y') : '-' }}</td>
                            <td class="text-center">{{ $movimentacao->comprometido ? 'Sim' : 'Não' }}</td>
                            <td>{{ Str::limit($movimentacao->observacao, 25, '...') ?: '-' }}</td>
                            <td class="text-center">
                                @if($diasEntre !== null)
                                    <div>
                                        <span class="status-badge {{ $prazoExcedido ? 'status-pendente' : 'status-concluido' }}">
                                            {{ number_format($diasEntre, 0, ',', '.') }} {{ $diasEntre == 1 ? 'dia' : 'dias' }}
                                        </span>
                                        @if(isset($prazoSetor))
                                            <div style="font-size: 6px; {{ $prazoExcedido ? 'color: #991B1B;' : 'color: #065F46;' }} font-weight: bold; margin-top: 2px;">
                                                (Prazo: {{ number_format($prazoSetor, 0, ',', '.') }} {{ $prazoSetor == 1 ? 'dia' : 'dias' }})
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span style="color: #6B7280;">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                Nenhuma movimentação encontrada com os filtros aplicados.
            </div>
        @endif

        <div class="footer">
            <p>Rota do Mar - Documento gerado em {{ now()->format('d/m/Y às H:i:s') }} - Página <span class="page-number"></span></p>
        </div>
    </div>
</body>
</html>
