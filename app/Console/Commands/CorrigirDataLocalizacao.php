<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CorrigirDataLocalizacao extends Command
{
    protected $signature = 'produtos:corrigir-data {produto_id} {localizacao_id} {data_antiga} {data_nova}';
    protected $description = 'Corrigir data_prevista_faccao de uma localização específica';

    public function handle()
    {
        $produtoId = $this->argument('produto_id');
        $localizacaoId = $this->argument('localizacao_id');
        $dataAntiga = $this->argument('data_antiga');
        $dataNova = $this->argument('data_nova');

        $this->info("🔧 Corrigindo Data de Localização");
        $this->info("Produto ID: {$produtoId}");
        $this->info("Localização ID: {$localizacaoId}");
        $this->info("Data Antiga: {$dataAntiga}");
        $this->info("Data Nova: {$dataNova}");
        $this->newLine();

        // Buscar registros
        $registros = DB::table('produto_localizacao')
            ->where('produto_id', $produtoId)
            ->where('localizacao_id', $localizacaoId)
            ->where('data_prevista_faccao', $dataAntiga)
            ->whereNull('deleted_at')
            ->get();

        if ($registros->isEmpty()) {
            $this->error("❌ Nenhum registro encontrado com esses critérios!");
            return 1;
        }

        $this->info("📍 Registros encontrados ({$registros->count()}):");
        foreach ($registros as $reg) {
            $this->line("  - ID: {$reg->id} | Qtd: {$reg->quantidade} | OP: " . ($reg->ordem_producao ?? '-'));
        }

        $this->newLine();
        if (!$this->confirm('Deseja atualizar a data desses registros?')) {
            $this->info('Operação cancelada.');
            return 0;
        }

        // Atualizar
        $atualizados = DB::table('produto_localizacao')
            ->where('produto_id', $produtoId)
            ->where('localizacao_id', $localizacaoId)
            ->where('data_prevista_faccao', $dataAntiga)
            ->whereNull('deleted_at')
            ->update(['data_prevista_faccao' => $dataNova]);

        $this->newLine();
        $this->info("✅ {$atualizados} registro(s) atualizado(s)!");

        return 0;
    }
}
