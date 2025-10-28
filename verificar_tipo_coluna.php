<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Verificando tipo da coluna data_prevista_faccao\n\n";

$columns = DB::select("SHOW COLUMNS FROM produto_localizacao WHERE Field = 'data_prevista_faccao'");

if (!empty($columns)) {
    $column = $columns[0];
    echo "Campo: {$column->Field}\n";
    echo "Tipo: {$column->Type}\n";
    echo "Null: {$column->Null}\n";
    echo "Default: {$column->Default}\n";
}

echo "\n🔍 Verificando dados do produto 288037:\n\n";

$registros = DB::table('produto_localizacao')
    ->where('produto_id', 5468)
    ->whereNull('deleted_at')
    ->orderBy('data_prevista_faccao')
    ->get(['id', 'quantidade', 'data_prevista_faccao', 'ordem_producao']);

foreach ($registros as $reg) {
    echo "ID: {$reg->id} | Qtd: {$reg->quantidade} | Data: {$reg->data_prevista_faccao} | OP: {$reg->ordem_producao}\n";
    
    // Verificar mês e ano
    $data = \Carbon\Carbon::parse($reg->data_prevista_faccao);
    echo "  → Mês: {$data->month} | Ano: {$data->year} | Dia: {$data->day}\n\n";
}
