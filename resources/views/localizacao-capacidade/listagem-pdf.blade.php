<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Listagem de Capacidades Mensais</title>
    <style>
        @page {
            margin: 20mm;
            size: A4 landscape;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .header h1 {
            font-size: 18px;
            margin: 0;
            color: #333;
        }
        
        .header p {
            font-size: 10px;
            margin: 5px 0;
            color: #666;
        }
        
        .filtros {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 3px;
        }
        
        .filtros span {
            font-weight: bold;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        
        td {
            padding: 6px 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .badge {
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .badge-blue {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        
        .badge-green {
            background-color: #e8f5e8;
            color: #2e7d32;
        }
        
        .badge-red {
            background-color: #ffebee;
            color: #c62828;
        }
        
        .text-red {
            color: #c62828;
            font-weight: bold;
        }
        
        .text-green {
            color: #2e7d32;
            font-weight: bold;
        }
        
        .progress-bar {
            width: 60px;
            height: 8px;
            background-color: #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
            display: inline-block;
            vertical-align: middle;
            margin-right: 5px;
        }
        
        .progress-fill {
            height: 100%;
            border-radius: 4px;
        }
        
        .progress-green {
            background-color: #4caf50;
        }
        
        .progress-yellow {
            background-color: #ff9800;
        }
        
        .progress-red {
            background-color: #f44336;
        }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
        }
        
        .no-data {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Listagem de Capacidades Mensais das Localizações</h1>
        <p>Gerado em: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="filtros">
        <strong>Filtros aplicados:</strong><br>
        Localização: <span>{{ $filtros['localizacao'] }}</span> | 
        Mês: <span>{{ $filtros['mes'] }}</span> | 
        Ano: <span>{{ $filtros['ano'] }}</span>
    </div>

    @if($capacidades->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 20%;">Localização</th>
                    <th style="width: 12%;">Período</th>
                    <th style="width: 10%;" class="text-center">Capacidade</th>
                    <th style="width: 10%;" class="text-center">Previstos</th>
                    <th style="width: 10%;" class="text-center">Saldo</th>
                    <th style="width: 15%;" class="text-center">Ocupação</th>
                    <th style="width: 23%;">Observações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($capacidades as $capacidade)
                    @php
                        $previstos = $capacidade->getProdutosPrevistos();
                        $saldo = $capacidade->getSaldo();
                        $percentual = $capacidade->getPercentualOcupacao();
                    @endphp
                    <tr>
                        <td>{{ $capacidade->localizacao->nome_localizacao }}</td>
                        <td>{{ $capacidade->mes_ano_formatado }}</td>
                        <td class="text-center">
                            <span class="badge badge-blue">{{ $capacidade->capacidade }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $previstos > $capacidade->capacidade ? 'badge-red' : 'badge-green' }}">
                                {{ $previstos }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="{{ $saldo < 0 ? 'text-red' : 'text-green' }}">
                                {{ $saldo }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div style="display: flex; align-items: center; justify-content: center;">
                                <div class="progress-bar">
                                    <div class="progress-fill {{ $percentual > 100 ? 'progress-red' : ($percentual > 80 ? 'progress-yellow' : 'progress-green') }}" 
                                         style="width: {{ min($percentual, 100) }}%"></div>
                                </div>
                                <span style="font-size: 10px; font-weight: bold; {{ $percentual > 100 ? 'color: #c62828' : 'color: #333' }}">
                                    {{ $percentual }}%
                                </span>
                            </div>
                        </td>
                        <td>
                            @if($capacidade->observacoes)
                                {{ $capacidade->observacoes }}
                            @else
                                <span style="color: #999; font-style: italic;">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Resumo -->
        <div style="margin-top: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 3px;">
            <h3 style="margin: 0 0 10px 0; font-size: 14px;">Resumo Geral</h3>
            @php
                $totalCapacidade = $capacidades->sum('capacidade');
                $totalPrevistos = $capacidades->sum(function($cap) { return $cap->getProdutosPrevistos(); });
                $totalSaldo = $totalCapacidade - $totalPrevistos;
                $totalPercentual = $totalCapacidade > 0 ? round(($totalPrevistos / $totalCapacidade) * 100, 1) : 0;
            @endphp
            
            <table style="width: auto; margin-bottom: 0;">
                <tr>
                    <td style="padding: 5px 10px; border: none;"><strong>Capacidade Total:</strong></td>
                    <td style="padding: 5px 10px; border: none;"><span class="badge badge-blue">{{ $totalCapacidade }}</span></td>
                    <td style="padding: 5px 10px; border: none;"><strong>Previstos Total:</strong></td>
                    <td style="padding: 5px 10px; border: none;"><span class="badge {{ $totalPrevistos > $totalCapacidade ? 'badge-red' : 'badge-green' }}">{{ $totalPrevistos }}</span></td>
                    <td style="padding: 5px 10px; border: none;"><strong>Saldo Total:</strong></td>
                    <td style="padding: 5px 10px; border: none;"><span class="{{ $totalSaldo < 0 ? 'text-red' : 'text-green' }}">{{ $totalSaldo }}</span></td>
                    <td style="padding: 5px 10px; border: none;"><strong>Taxa Ocupação:</strong></td>
                    <td style="padding: 5px 10px; border: none;"><span style="font-weight: bold; {{ $totalPercentual > 100 ? 'color: #c62828' : ($totalPercentual > 80 ? 'color: #ff9800' : 'color: #2e7d32') }}">{{ $totalPercentual }}%</span></td>
                </tr>
            </table>
        </div>
    @else
        <div class="no-data">
            Nenhuma capacidade encontrada para os filtros selecionados.
        </div>
    @endif

    <div class="footer">
        <p>Sistema de Gestão - Capacidade Mensal das Localizações</p>
        <p>Página {{ isset($paginator) ? $paginator->getCurrentPage() : 1 }} de {{ isset($paginator) ? $paginator->getLastPage() : 1 }}</p>
    </div>
</body>
</html>
