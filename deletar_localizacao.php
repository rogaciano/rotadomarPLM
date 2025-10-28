<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

if (!isset($argv[1])) {
    echo "❌ Uso: php deletar_localizacao.php {ID}\n";
    exit(1);
}

$id = $argv[1];

echo "🗑️  Deletando localização ID: {$id}\n\n";

$registro = DB::table('produto_localizacao')->where('id', $id)->first();

if (!$registro) {
    echo "❌ Registro não encontrado!\n";
    exit(1);
}

echo "Registro encontrado:\n";
echo "  Produto ID: {$registro->produto_id}\n";
echo "  Localização ID: {$registro->localizacao_id}\n";
echo "  Quantidade: {$registro->quantidade}\n";
echo "  Data: {$registro->data_prevista_faccao}\n";
echo "  OP: {$registro->ordem_producao}\n\n";

echo "⚠️  TEM CERTEZA? Digite 'SIM' para confirmar: ";
$confirmacao = trim(fgets(STDIN));

if (strtoupper($confirmacao) !== 'SIM') {
    echo "❌ Operação cancelada.\n";
    exit(0);
}

DB::table('produto_localizacao')
    ->where('id', $id)
    ->update(['deleted_at' => now()]);

echo "\n✅ Registro deletado (soft delete)!\n";
echo "🧪 Execute: php ver_localizacoes_288037.php para verificar\n";
