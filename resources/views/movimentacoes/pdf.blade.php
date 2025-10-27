<!DOCTYPE html>
<html>
<head>
    <title>Movimentação #{{ $movimentacao->id }}</title>
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
            page-break-inside: avoid;
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
            padding: 8px;
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
    <div class="container">
        <div class="header">
            <div class="title">Rota do Mar - DETALHES DA MOVIMENTAÇÃO #{{ $movimentacao->id ?? 'N/A' }}</div>
            <div class="subtitle">Data do relatório: {{ now()->format('d/m/Y H:i') }}</div>
        </div>

        <div class="columns">
            <div class="column">
                <div class="info-section">
                    <div class="section-title">Informações do Produto</div>
                    <div class="info-row">
                        <span class="info-label">Referência:</span>
                        <span class="info-value">{{ $movimentacao->produto ? $movimentacao->produto->referencia : 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Descrição:</span>
                        <span class="info-value">{{ $movimentacao->produto ? $movimentacao->produto->descricao : 'N/A' }}</span>
                    </div>
                    @if($movimentacao->produto && $movimentacao->produto->marca)
                    <div class="info-row">
                        <span class="info-label">Marca:</span>
                        <span class="info-value">{{ $movimentacao->produto->marca->nome_marca }}</span>
                    </div>
                    @endif
                    @if($movimentacao->produto && $movimentacao->produto->grupoProduto)
                    <div class="info-row">
                        <span class="info-label">Grupo:</span>
                        <span class="info-value">{{ $movimentacao->produto->grupoProduto->nome_grupo }}</span>
                    </div>
                    @endif
                    @if($movimentacao->produto && $movimentacao->produto->status)
                    <div class="info-row">
                        <span class="info-label">Status:</span>
                        <span class="info-value">{{ $movimentacao->produto->status->descricao }}</span>
                    </div>
                    @endif
                    <div class="info-row">
                        <span class="info-label">Quantidade:</span>
                        <span class="info-value">{{ $movimentacao->produto ? $movimentacao->produto->quantidade : 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <div class="column">
                <div class="info-section">
                    <div class="section-title">Informações da Movimentação</div>
                    <div class="info-row">
                        <span class="info-label">ID:</span>
                        <span class="info-value">{{ $movimentacao->id ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Data de Entrada:</span>
                        <span class="info-value">{{ $movimentacao->data_entrada ? $movimentacao->data_entrada->format('d/m/Y H:i') : 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Data de Saída:</span>
                        <span class="info-value">{{ $movimentacao->data_saida ? $movimentacao->data_saida->format('d/m/Y H:i') : 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Localização:</span>
                        <span class="info-value">{{ $movimentacao->localizacao ? $movimentacao->localizacao->nome_localizacao : 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tipo:</span>
                        <span class="info-value">{{ $movimentacao->tipo ? $movimentacao->tipo->descricao : 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Situação:</span>
                        <span class="info-value">{{ $movimentacao->situacao ? $movimentacao->situacao->descricao : 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Criado em:</span>
                        <span class="info-value">{{ $movimentacao->created_at ? $movimentacao->created_at->format('d/m/Y H:i') : 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Atualizado em:</span>
                        <span class="info-value">{{ $movimentacao->updated_at ? $movimentacao->updated_at->format('d/m/Y H:i') : 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        @if($movimentacao->observacao)
        <div class="info-section">
            <div class="section-title">Observações</div>
            <p style="margin-top: 10px;">{{ $movimentacao->observacao }}</p>
        </div>
        @endif

        <div class="footer">
            <p>Rota do Mar - Documento gerado em {{ now()->format('d/m/Y às H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
