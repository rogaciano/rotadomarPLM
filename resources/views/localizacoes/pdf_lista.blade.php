<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Localizações</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            margin: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }
        
        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #1a1a1a;
        }
        
        .header p {
            margin: 5px 0 0 0;
            font-size: 10px;
            color: #666;
        }
        
        .info-section {
            margin-bottom: 15px;
            font-size: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        thead {
            background-color: #f3f4f6;
        }
        
        th {
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
            border-bottom: 2px solid #d1d5db;
            border-top: 1px solid #d1d5db;
        }
        
        td {
            padding: 6px 6px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
        }
        
        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        tbody tr:hover {
            background-color: #f3f4f6;
        }
        
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
        }
        
        .status-ativo {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .status-inativo {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .row-excluida {
            background-color: #fee2e2 !important;
        }
        
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #d1d5db;
            text-align: center;
            font-size: 9px;
            color: #6b7280;
        }
        
        .total-registros {
            margin-top: 10px;
            text-align: right;
            font-weight: bold;
            font-size: 11px;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .no-data {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Listagem de Localizações</h1>
        <p>Gerado em {{ now()->format('d/m/Y \à\s H:i:s') }}</p>
    </div>
    
    <div class="info-section">
        <strong>Total de registros:</strong> {{ $localizacoes->count() }}
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 8%;">ID</th>
                <th style="width: 35%;">Nome</th>
                <th style="width: 15%;" class="text-center">Prazo (dias)</th>
                <th style="width: 15%;" class="text-center">Capacidade</th>
                <th style="width: 12%;" class="text-center">Status</th>
                <th style="width: 15%;" class="text-center">Criado em</th>
            </tr>
        </thead>
        <tbody>
            @forelse($localizacoes as $localizacao)
                <tr class="{{ $localizacao->trashed() ? 'row-excluida' : '' }}">
                    <td>{{ $localizacao->id }}</td>
                    <td>
                        {{ $localizacao->nome_localizacao }}
                        @if($localizacao->trashed())
                            <span style="color: #991b1b; font-size: 9px;">(EXCLUÍDA)</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $localizacao->prazo ?? 'N/A' }}</td>
                    <td class="text-center">{{ $localizacao->capacidade ? number_format($localizacao->capacidade, 0, ',', '.') : 'N/A' }}</td>
                    <td class="text-center">
                        <span class="status-badge {{ $localizacao->ativo ? 'status-ativo' : 'status-inativo' }}">
                            {{ $localizacao->ativo ? 'Ativo' : 'Inativo' }}
                        </span>
                    </td>
                    <td class="text-center">
                        {{ $localizacao->created_at ? $localizacao->created_at->format('d/m/Y') : 'N/A' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="no-data">
                        Nenhuma localização encontrada.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($localizacoes->count() > 0)
        <tfoot>
            <tr style="background-color: #f3f4f6; font-weight: bold; border-top: 2px solid #d1d5db;">
                <td colspan="3" style="padding: 8px 6px; text-align: right;">TOTAL:</td>
                <td style="padding: 8px 6px; text-align: center;">
                    {{ number_format($localizacoes->sum('capacidade'), 0, ',', '.') }}
                </td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
        @endif
    </table>
    
    @if($localizacoes->count() > 0)
        <div class="total-registros">
            Total: {{ $localizacoes->count() }} localização(ões) | Capacidade Total: {{ number_format($localizacoes->sum('capacidade'), 0, ',', '.') }} produtos
        </div>
    @endif
    
    <div class="footer">
        <p>Documento gerado automaticamente pelo sistema Rota do Amar</p>
    </div>
</body>
</html>
