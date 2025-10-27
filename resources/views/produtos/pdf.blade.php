<!DOCTYPE html>
<html>
<head>
    <title>Produto - {{ $produto->referencia }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .subtitle {
            font-size: 14px;
            margin-bottom: 5px;
        }
        .columns {
            display: flex;
            flex-wrap: wrap;
        }
        .column {
            width: 50%;
            box-sizing: border-box;
            padding-right: 15px;
        }
        .info-section {
            margin-bottom: 20px;
            /* page-break-inside: avoid; <-- REMOVIDO */
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ccc;
        }
        .info-row {
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
            display: inline-block;
        }
        .info-value {
            display: inline-block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 6px;
            text-align: left;
            font-size: 11px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .page-break {
            page-break-before: always;
        }
        .logo {
            max-height: 50px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <!-- Resto do código permanece igual -->
    <div class="container">
        <div class="header">
            <div class="title">Rota do Mar - DETALHES DO PRODUTO</div>
            <div class="subtitle">{{ $produto->referencia }} - {{ $produto->descricao }}</div>
            <div>Data do relatório: {{ now()->format('d/m/Y H:i') }}</div>
        </div>

        <div class="info-section">
            <div class="section-title">Informações do Produto</div>
            <div style="display: flex; flex-wrap: wrap;">
                <div style="width: 33%; padding-right: 10px;">
                    <div class="info-row">
                        <span class="info-label">Referência:</span>
                        <span class="info-value">{{ $produto->referencia }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Descrição:</span>
                        <span class="info-value">{{ $produto->descricao }}</span>
                    </div>
                    @if($produto->marca)
                    <div class="info-row">
                        <span class="info-label">Marca:</span>
                        <span class="info-value">{{ $produto->marca->nome_marca }}</span>
                    </div>
                    @endif
                    @if($produto->grupoProduto)
                    <div class="info-row">
                        <span class="info-label">Grupo:</span>
                        <span class="info-value">{{ $produto->grupoProduto->nome_grupo }}</span>
                    </div>
                    @endif
                </div>
                <div style="width: 33%; padding-right: 10px;">
                    @if($produto->estilista)
                    <div class="info-row">
                        <span class="info-label">Estilista:</span>
                        <span class="info-value">{{ $produto->estilista->nome }}</span>
                    </div>
                    @endif
                    @if($produto->status)
                    <div class="info-row">
                        <span class="info-label">Status:</span>
                        <span class="info-value">{{ $produto->status->descricao }}</span>
                    </div>
                    @endif
                    @if($produto->cor)
                    <div class="info-row">
                        <span class="info-label">Cor:</span>
                        <span class="info-value">{{ $produto->cor }}</span>
                    </div>
                    @endif
                    @if($produto->tamanho)
                    <div class="info-row">
                        <span class="info-label">Tamanho:</span>
                        <span class="info-value">{{ $produto->tamanho }}</span>
                    </div>
                    @endif
                </div>
                <div style="width: 33%; padding-right: 10px;">
                    <div class="info-row">
                        <span class="info-label">Quantidade:</span>
                        <span class="info-value">{{ $produto->quantidade }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Criado:</span>
                        <span class="info-value">{{ $produto->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Atualizado:</span>
                        <span class="info-value">{{ $produto->updated_at->format('d/m/Y') }}</span>
                    </div>
                    @if($produto->observacao)
                    <div class="info-row">
                        <span class="info-label">Observação:</span>
                        <span class="info-value">{{ Str::limit($produto->observacao, 50, '...') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if($produto->tecidos && count($produto->tecidos) > 0)
        <div class="info-section">
            <div class="section-title">Tecidos Associados</div>
            <table>
                <thead>
                    <tr>
                        <th>Referência</th>
                        <th>Descrição</th>
                        <th>Composição</th>
                        <th>Quantidade</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($produto->tecidos as $tecido)
                    <tr>
                        <td>{{ $tecido->referencia }}</td>
                        <td>{{ $tecido->descricao }}</td>
                        <td>{{ $tecido->composicao ?: '-' }}</td>
                        <td>{{ $tecido->pivot->quantidade ?: '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <div class="info-section">
            <div class="section-title">Histórico de Movimentações</div>
            @if(count($produto->movimentacoes) > 0)
            <table style="width: 100%">
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="15%">Data Entrada</th>
                        <th width="15%">Data Conclusão</th>
                        <th width="18%">Localização</th>
                        <th width="12%">Tipo</th>
                        <th width="15%">Situação</th>
                        <th width="20%">Observação</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $movimentacoesOrdenadas = collect();
                        if ($produto->movimentacoes && $produto->movimentacoes->count() > 0) {
                            $movimentacoesOrdenadas = $produto->movimentacoes->filter(function($mov) {
                                return $mov && $mov->data_entrada;
                            })->sortByDesc(function($mov) {
                                return $mov->data_entrada;
                            });
                        }
                    @endphp
                    @foreach($movimentacoesOrdenadas as $movimentacao)
                    <tr>
                        <td>{{ $movimentacao->id ?? 'N/A' }}</td>
                        <td>{{ $movimentacao->data_entrada ? $movimentacao->data_entrada->format('d/m/Y H:i') : 'N/A' }}</td>
                        <td>{{ $movimentacao->data_saida ? $movimentacao->data_saida->format('d/m/Y H:i') : '-' }}</td>
                        <td>{{ $movimentacao->localizacao ? $movimentacao->localizacao->nome_localizacao : 'N/A' }}</td>
                        <td>{{ $movimentacao->tipo ? $movimentacao->tipo->descricao : 'N/A' }}</td>
                        <td>{{ $movimentacao->situacao ? $movimentacao->situacao->descricao : 'N/A' }}</td>
                        <td>{{ $movimentacao->observacao ? Str::limit($movimentacao->observacao, 40, '...') : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p>Nenhuma movimentação registrada para este produto.</p>
            @endif
        </div>

        <div class="footer">
            <p>Rota do Mar - Documento gerado em {{ now()->format('d/m/Y às H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
