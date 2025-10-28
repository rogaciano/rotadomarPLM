<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Produto;
use App\Models\ProdutoAlocacaoMensal;
use Illuminate\Support\Facades\DB;

class VerificarProduto extends Command
{
    protected $signature = 'produtos:verificar {referencia}';
    protected $description = 'Verificar produto por referência';

    public function handle()
    {
        $referencia = $this->argument('referencia');

        $this->info("🔍 Buscando produto: {$referencia}");
        $this->newLine();

        $produto = Produto::where('referencia', $referencia)->first();

        if (!$produto) {
            $this->error("❌ Produto não encontrado!");
            return 1;
        }

        $this->info("✅ Produto encontrado: ID {$produto->id} - {$produto->descricao}");
        $this->newLine();

        // Verificar localizações via relacionamento
        $this->info("📍 Localizações (via relacionamento):");
        $localizacoes = $produto->localizacoes;
        
        if ($localizacoes->count() > 0) {
            foreach ($localizacoes as $loc) {
                $data = $loc->pivot->data_prevista_faccao ?? 'N/A';
                $this->line("  - {$loc->nome_localizacao}");
                $this->line("    Qtd: {$loc->pivot->quantidade}");
                $this->line("    Data: {$data}");
                $this->line("    OP: " . ($loc->pivot->ordem_producao ?? '-'));
                $this->line("    Obs: " . ($loc->pivot->observacao ?? '-'));
            }
        } else {
            $this->warn("  Nenhuma localização encontrada via relacionamento");
        }

        $this->newLine();

        // Verificar diretamente na tabela produto_localizacao
        $this->info("📍 Localizações (direto do banco):");
        $locsDB = DB::table('produto_localizacao')
            ->join('localizacoes', 'produto_localizacao.localizacao_id', '=', 'localizacoes.id')
            ->where('produto_localizacao.produto_id', $produto->id)
            ->whereNull('produto_localizacao.deleted_at')
            ->select('localizacoes.nome_localizacao', 'produto_localizacao.*')
            ->get();

        foreach ($locsDB as $loc) {
            $this->line("  - {$loc->nome_localizacao}");
            $this->line("    Qtd: {$loc->quantidade}");
            $this->line("    Data: {$loc->data_prevista_faccao}");
            $this->line("    OP: " . ($loc->ordem_producao ?? '-'));
        }

        $this->newLine();

        // Verificar alocações mensais
        $this->info("📊 Alocações Mensais:");
        $alocacoes = ProdutoAlocacaoMensal::where('produto_id', $produto->id)
            ->with('localizacao')
            ->orderBy('ano')
            ->orderBy('mes')
            ->get();

        if ($alocacoes->count() > 0) {
            foreach ($alocacoes as $aloc) {
                $this->line("  - {$aloc->localizacao->nome_localizacao}: {$aloc->quantidade} unidades | {$aloc->mes}/{$aloc->ano} | OP: " . ($aloc->ordem_producao ?? '-'));
            }
        } else {
            $this->warn("  Nenhuma alocação mensal encontrada");
        }

        return 0;
    }
}
