<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProdutoAlocacaoMensal;
use App\Models\ProdutoLocalizacao;

class DiagnosticarAlocacoes extends Command
{
    protected $signature = 'produtos:diagnosticar-alocacoes {produto_id?}';
    protected $description = 'Diagnosticar alocações mensais de um produto';

    public function handle()
    {
        $produtoId = $this->argument('produto_id') ?? $this->ask('ID do Produto');

        $this->info("🔍 Diagnóstico de Alocações - Produto ID: {$produtoId}");
        $this->newLine();

        // Buscar localizações do produto
        $localizacoes = ProdutoLocalizacao::where('produto_id', $produtoId)
            ->with('localizacao')
            ->get();

        $this->info("📍 Localizações do Produto ({$localizacoes->count()}):");
        foreach ($localizacoes as $loc) {
            $data = $loc->data_prevista_faccao ? \Carbon\Carbon::parse($loc->data_prevista_faccao)->format('m/Y') : 'N/A';
            $this->line("  - {$loc->localizacao->nome_localizacao}: {$loc->quantidade} unidades | Data: {$data} | OP: " . ($loc->ordem_producao ?? '-'));
        }

        $this->newLine();

        // Buscar alocações mensais
        $alocacoes = ProdutoAlocacaoMensal::where('produto_id', $produtoId)
            ->with('localizacao')
            ->orderBy('ano')
            ->orderBy('mes')
            ->get();

        $this->info("📊 Alocações Mensais ({$alocacoes->count()}):");
        foreach ($alocacoes as $aloc) {
            $this->line("  - {$aloc->localizacao->nome_localizacao}: {$aloc->quantidade} unidades | {$aloc->mes}/{$aloc->ano} | Tipo: {$aloc->tipo} | OP: " . ($aloc->ordem_producao ?? '-'));
        }

        $this->newLine();

        // Verificar inconsistências
        $this->info("⚠️  Verificando Inconsistências:");
        
        $totalLocalizacoes = $localizacoes->sum('quantidade');
        $totalAlocacoes = $alocacoes->sum('quantidade');
        
        if ($totalLocalizacoes != $totalAlocacoes) {
            $this->error("  ❌ Total de localizações ({$totalLocalizacoes}) != Total de alocações ({$totalAlocacoes})");
        } else {
            $this->info("  ✅ Totais conferem: {$totalLocalizacoes} unidades");
        }

        // Verificar duplicatas
        $duplicatas = $alocacoes->groupBy(function($item) {
            return $item->localizacao_id . '-' . $item->mes . '-' . $item->ano . '-' . ($item->ordem_producao ?? 'sem-op');
        })->filter(function($group) {
            return $group->count() > 1;
        });

        if ($duplicatas->count() > 0) {
            $this->error("  ❌ Encontradas {$duplicatas->count()} alocações duplicadas!");
            foreach ($duplicatas as $key => $group) {
                $this->line("    - Chave: {$key} ({$group->count()} registros)");
            }
        } else {
            $this->info("  ✅ Sem duplicatas");
        }

        return 0;
    }
}
