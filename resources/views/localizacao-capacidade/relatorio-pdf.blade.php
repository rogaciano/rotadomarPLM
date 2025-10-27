<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Capacidade - {{ $mesNome }}/{{ $ano }}</title>
    <style>
        @page {
            margin: 15mm;
            size: A4 landscape;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #333;
            margin: 0;
            padding: 10px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #3B82F6;
            padding-bottom: 10px;
        }
        
        .header h1 {
            font-size: 20px;
            color: #1F2937;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 12px;
            color: #6B7280;
        }
        
        .localizacao-section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .localizacao-header {
            background-color: #F3F4F6;
            padding: 10px;
            border-left: 4px solid #3B82F6;
            margin-bottom: 10px;
        }
        
        .localizacao-title {
            font-size: 14px;
            font-weight: bold;
            color: #1F2937;
            margin-bottom: 5px;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        
        .stats-row {
            display: table-row;
        }
        
        .stat-cell {
            display: table-cell;
            padding: 8px;
            border: 1px solid #E5E7EB;
            width: 25%;
        }
        
        .stat-label {
            font-size: 9px;
            color: #6B7280;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        
        .stat-value {
            font-size: 13px;
            font-weight: bold;
        }
        
        .stat-value.green {
            color: #059669;
        }
        
        .stat-value.red {
            color: #DC2626;
        }
        
        .stat-value.yellow {
            color: #D97706;
        }
        
        .produtos-section {
            margin-top: 15px;
        }
        
        .produtos-title {
            font-size: 11px;
            font-weight: bold;
            color: #1F2937;
            margin-bottom: 8px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        th {
            background-color: #F9FAFB;
            padding: 6px 4px;
            text-align: left;
            font-size: 8px;
            font-weight: bold;
            color: #6B7280;
            text-transform: uppercase;
            border: 1px solid #E5E7EB;
        }
        
        td {
            padding: 5px 4px;
            font-size: 9px;
            border: 1px solid #E5E7EB;
        }
        
        tr:nth-child(even) {
            background-color: #F9FAFB;
        }
        
        .text-center {
            text-align: center;
        }
        
        .font-semibold {
            font-weight: 600;
        }
        
        .observacao {
            font-size: 8px;
            color: #4B5563;
            line-height: 1.3;
            margin-bottom: 2px;
        }
        
        .qtd-alocada {
            display: inline-block;
            background-color: #DBEAFE;
            color: #1E40AF;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: 600;
            margin-top: 4px;
        }
        
        .alocacao-item {
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid #E5E7EB;
        }
        
        .alocacao-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .obs-table {
            width: 100%;
            border: none;
            margin: 0;
            padding: 0;
            border-collapse: collapse;
        }
        
        .obs-table tr {
            border: none;
            border-bottom: 1px solid #E5E7EB;
        }
        
        .obs-table tr:last-child {
            border-bottom: none;
        }
        
        .obs-table td {
            border: none;
            padding: 4px 4px;
            vertical-align: top;
        }
        
        .obs-info {
            width: 70%;
            padding-right: 8px;
        }
        
        .obs-qtd {
            width: 30%;
            text-align: right;
            padding-left: 8px;
        }
        
        .no-produtos {
            text-align: center;
            padding: 20px;
            color: #9CA3AF;
            font-style: italic;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #E5E7EB;
            text-align: center;
            font-size: 8px;
            color: #9CA3AF;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Relatório de Capacidade por Localização</h1>
        <p>Período: {{ $mesNome }} de {{ $ano }}</p>
        <p>Gerado em: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    @forelse($dadosDashboard as $dado)
        <div class="localizacao-section">
            <div class="localizacao-header">
                <div class="localizacao-title">{{ $dado['localizacao']->nome_localizacao }}</div>
            </div>

            <div class="stats-grid">
                <div class="stats-row">
                    <div class="stat-cell">
                        <div class="stat-label">Capacidade</div>
                        <div class="stat-value">{{ number_format($dado['capacidade'], 0, ',', '.') }}</div>
                    </div>
                    <div class="stat-cell">
                        <div class="stat-label">Previstos</div>
                        <div class="stat-value {{ $dado['acima_capacidade'] ? 'red' : 'green' }}">
                            {{ number_format($dado['produtos_previstos'], 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="stat-cell">
                        <div class="stat-label">Saldo</div>
                        <div class="stat-value {{ $dado['saldo'] < 0 ? 'red' : 'green' }}">
                            {{ number_format($dado['saldo'], 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="stat-cell">
                        <div class="stat-label">Ocupação</div>
                        <div class="stat-value {{ $dado['percentual'] > 100 ? 'red' : ($dado['percentual'] > 80 ? 'yellow' : 'green') }}">
                            {{ number_format($dado['percentual'], 1, ',', '.') }}%
                        </div>
                    </div>
                </div>
            </div>

            @if($dado['produtos']->count() > 0)
                <div class="produtos-section">
                    @php
                        // Agrupar produtos por referência + descrição + marca + grupo + qtd total + data + status
                        $produtosAgrupados = $dado['produtos']->groupBy(function($produto) {
                            $primeiraData = $produto->localizacoes()
                                ->whereNotNull('data_prevista_faccao')
                                ->orderBy('data_prevista_faccao', 'asc')
                                ->first();
                            
                            $dataFormatada = 'N/A';
                            if ($primeiraData && $primeiraData->pivot->data_prevista_faccao) {
                                $dataFormatada = is_string($primeiraData->pivot->data_prevista_faccao) 
                                    ? \Carbon\Carbon::parse($primeiraData->pivot->data_prevista_faccao)->format('Y-m-d')
                                    : $primeiraData->pivot->data_prevista_faccao->format('Y-m-d');
                            }
                            
                            return $produto->id . '|' . 
                                   $produto->referencia . '|' . 
                                   $produto->descricao . '|' . 
                                   ($produto->marca ? $produto->marca->id : 'sem_marca') . '|' . 
                                   ($produto->grupoProduto ? $produto->grupoProduto->id : 'sem_grupo') . '|' . 
                                   $produto->quantidade . '|' . 
                                   $dataFormatada . '|' . 
                                   ($produto->status ? $produto->status->id : 'sem_status');
                        });
                    @endphp
                    <div class="produtos-title">Produtos Previstos ({{ $produtosAgrupados->count() }} produtos, {{ $dado['produtos']->count() }} alocações)</div>
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 8%;">Ref</th>
                                <th style="width: 20%;">Descrição</th>
                                <th style="width: 12%;">Marca</th>
                                <th style="width: 12%;">Grupo</th>
                                <th style="width: 36%;">Observações</th>
                                <th style="width: 8%;" class="text-center">Qtd Total</th>
                                <th style="width: 4%;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($produtosAgrupados as $chave => $produtosGrupo)
                                @php
                                    $produtoPrincipal = $produtosGrupo->first();
                                @endphp
                                <tr>
                                    <td class="font-semibold">{{ $produtoPrincipal->referencia }}</td>
                                    <td>{{ $produtoPrincipal->descricao }}</td>
                                    <td>
                                        @if($produtoPrincipal->marca)
                                            @if($produtoPrincipal->marca->cor_fundo && $produtoPrincipal->marca->cor_fonte)
                                                <span style="background-color: {{ $produtoPrincipal->marca->cor_fundo }}; color: {{ $produtoPrincipal->marca->cor_fonte }}; padding: 2px 8px; border-radius: 12px; font-size: 8px; font-weight: 600; white-space: nowrap; display: inline-block;">
                                                    {{ $produtoPrincipal->marca->nome_marca }}
                                                </span>
                                            @else
                                                {{ $produtoPrincipal->marca->nome_marca }}
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $produtoPrincipal->grupoProduto->descricao ?? 'N/A' }}</td>
                                    <td>
                                        @php
                                            // Carregar observações do produto (apenas uma vez)
                                            $obs = \App\Models\ProdutoObservacao::where('produto_id', $produtoPrincipal->id)->get();

                                            // Carregar todas as observações das localizações de todas as alocações
                                            $todasObsLocalizacoes = collect();
                                            foreach($produtosGrupo as $produto) {
                                                $obsLoc = $produto->localizacoes()
                                                    ->where(function($q) {
                                                        $q->whereNotNull('ordem_producao')
                                                          ->orWhereNotNull('produto_localizacao.observacao');
                                                    })
                                                    ->get();
                                                $todasObsLocalizacoes = $todasObsLocalizacoes->merge($obsLoc);
                                            }
                                            
                                            // Remover duplicatas baseado em ordem_producao + observacao
                                            $todasObsLocalizacoes = $todasObsLocalizacoes->unique(function($loc) {
                                                return $loc->pivot->ordem_producao . '|' . $loc->pivot->observacao;
                                            });

                                            $temObservacoes = $obs->count() > 0 || $todasObsLocalizacoes->count() > 0;
                                        @endphp
                                        
                                        {{-- Observações do Produto (apenas uma vez) --}}
                                        @if($obs->count() > 0)
                                            @foreach($obs as $observacao)
                                                @php
                                                    // Processar observações (suporta HTML do Quill e tags customizadas)
                                                    $obsTexto = $observacao->observacao;
                                                    
                                                    // Se não contém tags HTML do Quill, processar tags customizadas
                                                    if (strpos($obsTexto, '<p>') === false && strpos($obsTexto, '<span') === false) {
                                                        $obsTexto = preg_replace('/<red>(.*?)<\/red>/i', '<span style="color: #DC2626; font-weight: 600;">$1</span>', $obsTexto);
                                                        $obsTexto = preg_replace('/<blue>(.*?)<\/blue>/i', '<span style="color: #2563EB; font-weight: 600;">$1</span>', $obsTexto);
                                                        $obsTexto = preg_replace('/<green>(.*?)<\/green>/i', '<span style="color: #16A34A; font-weight: 600;">$1</span>', $obsTexto);
                                                        $obsTexto = preg_replace('/<yellow>(.*?)<\/yellow>/i', '<span style="color: #CA8A04; font-weight: 600;">$1</span>', $obsTexto);
                                                        $obsTexto = preg_replace('/<orange>(.*?)<\/orange>/i', '<span style="color: #EA580C; font-weight: 600;">$1</span>', $obsTexto);
                                                        $obsTexto = preg_replace('/<purple>(.*?)<\/purple>/i', '<span style="color: #9333EA; font-weight: 600;">$1</span>', $obsTexto);
                                                        $obsTexto = preg_replace('/<pink>(.*?)<\/pink>/i', '<span style="color: #DB2777; font-weight: 600;">$1</span>', $obsTexto);
                                                    }
                                                    
                                                    $obsTexto = Str::limit($obsTexto, 100);
                                                @endphp
                                                <div class="observacao">
                                                    {!! $obsTexto !!}
                                                </div>
                                            @endforeach
                                        @endif

                                        {{-- Observações das Localizações (Ordem de Produção) - sem duplicatas --}}
                                        @if($todasObsLocalizacoes->count() > 0)
                                            <table class="obs-table">
                                                @foreach($todasObsLocalizacoes as $loc)
                                                    @php
                                                        // Buscar a quantidade alocada para esta ordem de produção
                                                        $qtdAlocada = 0;
                                                        foreach($produtosGrupo as $produto) {
                                                            $localizacaoAtual = $produto->localizacoes()
                                                                ->where('ordem_producao', $loc->pivot->ordem_producao)
                                                                ->first();
                                                            if ($localizacaoAtual) {
                                                                $qtdAlocada = $localizacaoAtual->pivot->quantidade ?? 0;
                                                                break;
                                                            }
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td class="obs-info">
                                                            <div class="observacao" style="margin: 0;">
                                                                @if($loc->pivot->ordem_producao)
                                                                    <strong style="color: #1E40AF;">OP: {{ $loc->pivot->ordem_producao }}</strong>
                                                                @endif
                                                                @if($loc->pivot->ordem_producao && $loc->pivot->observacao)
                                                                    <span> - </span>
                                                                @endif
                                                                @if($loc->pivot->observacao)
                                                                    @php
                                                                        // Processar tags de cor nas observações
                                                                        $obsTexto = $loc->pivot->observacao;
                                                                        $obsTexto = preg_replace('/<red>(.*?)<\/red>/i', '<span style="color: #DC2626; font-weight: 600;">$1</span>', $obsTexto);
                                                                        $obsTexto = preg_replace('/<blue>(.*?)<\/blue>/i', '<span style="color: #2563EB; font-weight: 600;">$1</span>', $obsTexto);
                                                                        $obsTexto = preg_replace('/<green>(.*?)<\/green>/i', '<span style="color: #16A34A; font-weight: 600;">$1</span>', $obsTexto);
                                                                        $obsTexto = preg_replace('/<yellow>(.*?)<\/yellow>/i', '<span style="color: #CA8A04; font-weight: 600;">$1</span>', $obsTexto);
                                                                        $obsTexto = preg_replace('/<orange>(.*?)<\/orange>/i', '<span style="color: #EA580C; font-weight: 600;">$1</span>', $obsTexto);
                                                                        $obsTexto = preg_replace('/<purple>(.*?)<\/purple>/i', '<span style="color: #9333EA; font-weight: 600;">$1</span>', $obsTexto);
                                                                        $obsTexto = preg_replace('/<pink>(.*?)<\/pink>/i', '<span style="color: #DB2777; font-weight: 600;">$1</span>', $obsTexto);
                                                                        $obsTexto = Str::limit($obsTexto, 80);
                                                                    @endphp
                                                                    {!! $obsTexto !!}
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td class="obs-qtd">
                                                            <span class="qtd-alocada">{{ number_format($qtdAlocada, 0, ',', '.') }}</span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        @endif
                                        
                                        @if(!$temObservacoes)
                                            <div class="observacao" style="color: #9CA3AF; font-style: italic;">-</div>
                                        @endif
                                    </td>
                                    <td class="text-center font-semibold">
                                        {{ number_format($produtoPrincipal->quantidade ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td>{{ $produtoPrincipal->status->descricao ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="no-produtos">Nenhum produto previsto para este período</div>
            @endif
        </div>
    @empty
        <div class="no-produtos" style="margin-top: 50px;">Nenhuma capacidade cadastrada para este período</div>
    @endforelse

    <div class="footer">
        <p>Sistema de Gestão de Capacidade - Rota do Amar</p>
    </div>
</body>
</html>
