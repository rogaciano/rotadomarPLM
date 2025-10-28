<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 Corrigindo data do produto 288037...\n\n";

$atualizado = DB::table('produto_localizacao')
    ->where('produto_id', 5468)
    ->where('localizacao_id', 20516)
    ->where('ordem_producao', '-')
    ->where('data_prevista_faccao', '2025-11-30')
    ->update(['data_prevista_faccao' => '2025-12-31']);

echo "✅ {$atualizado} registro(s) atualizado(s)!\n";
echo "\n🧪 Testando...\n";

$registros = DB::table('produto_localizacao')
    ->where('produto_id', 5468)
    ->where('localizacao_id', 20516)
    ->whereNull('deleted_at')
    ->get(['quantidade', 'data_prevista_faccao', 'ordem_producao']);

foreach ($registros as $reg) {
    echo "  - Qtd: {$reg->quantidade} | Data: {$reg->data_prevista_faccao} | OP: {$reg->ordem_producao}\n";
}

echo "\n✅ Pronto! Atualize o dashboard no navegador.\n";
