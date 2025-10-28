<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Localizações do Produto 288037 (ID: 5468)\n\n";

$registros = DB::table('produto_localizacao')
    ->where('produto_id', 5468)
    ->whereNull('deleted_at')
    ->orderBy('data_prevista_faccao')
    ->get();

echo "Total de registros: " . $registros->count() . "\n\n";

foreach ($registros as $reg) {
    echo "ID: {$reg->id}\n";
    echo "  Localização ID: {$reg->localizacao_id}\n";
    echo "  Quantidade: {$reg->quantidade}\n";
    echo "  Data: {$reg->data_prevista_faccao}\n";
    echo "  OP: {$reg->ordem_producao}\n";
    echo "  Obs: {$reg->observacao}\n";
    echo "  ---\n";
}

echo "\n❓ Qual registro você quer DELETAR?\n";
echo "Execute: php deletar_localizacao.php {ID}\n";
