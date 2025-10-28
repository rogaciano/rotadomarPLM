<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Produto;
use App\Models\ProdutoAlocacaoMensal;
use Illuminate\Support\Facades\DB;

class ReconstruirAlocacoesProduto extends Command
{
    protected $signature = 'produtos:reconstruir-alocacoes {referencia}';
    protected $description = 'Limpar e recriar alocações de um produto específico';

    public function handle()
    {
        $referencia = $this->argument('referencia');

        $produto = Produto::where('referencia', $referencia)->first();

        if (!$produto) {
            $this->error("❌ Produto não encontrado!");
            return 1;
        }

        $this->info("🔍 Produto: {$produto->referencia} - {$produto->descricao}");
        $this->newLine();

        // 1. Mostrar alocações atuais
        $alocacoesAntigas = ProdutoAlocacaoMensal::where('produto_id', $produto->id)->get();
        $this->warn("📊 Alocações atuais ({$alocacoesAntigas->count()}):");
        foreach ($alocacoesAntigas as $aloc) {
            $this->line("  - Loc: {$aloc->localizacao_id} | {$aloc->mes}/{$aloc->ano} | Qtd: {$aloc->quantidade}");
        }
        $this->newLine();

        // 2. Confirmar exclusão
        if (!$this->confirm('Deseja DELETAR todas essas alocações e recriar?')) {
            $this->info('Operação cancelada.');
            return 0;
        }

        // 3. Deletar alocações antigas
        $deletadas = ProdutoAlocacaoMensal::where('produto_id', $produto->id)->delete();
        $this->info("🗑️  Deletadas: {$deletadas} alocações");
        $this->newLine();

        // 4. Buscar localizações do produto
        $localizacoes = $produto->localizacoes;
        $this->info("📍 Localizações encontradas: {$localizacoes->count()}");
        
        if ($localizacoes->count() == 0) {
            $this->warn("⚠️  Nenhuma localização encontrada. Nada a fazer.");
            return 0;
        }

        // 5. Recriar alocações
        $criadas = 0;
        foreach ($localizacoes as $loc) {
            if (!$loc->pivot->data_prevista_faccao || $loc->pivot->quantidade <= 0) {
                $this->warn("  ⏭️  Pulando: {$loc->nome_localizacao} (sem data ou qtd zero)");
                continue;
            }

            $dataFaccao = \Carbon\Carbon::parse($loc->pivot->data_prevista_faccao);
            
            $this->line("  ✅ Criando: {$loc->nome_localizacao}");
            $this->line("     Data: {$dataFaccao->format('d/m/Y')} → Mês/Ano: {$dataFaccao->month}/{$dataFaccao->year}");
            $this->line("     Qtd: {$loc->pivot->quantidade}");

            ProdutoAlocacaoMensal::create([
                'produto_id' => $produto->id,
                'localizacao_id' => $loc->id,
                'mes' => $dataFaccao->month,
                'ano' => $dataFaccao->year,
                'quantidade' => $loc->pivot->quantidade,
                'tipo' => 'original',
                'usuario_id' => 1,
                'produto_localizacao_id' => $loc->pivot->id,
                'ordem_producao' => $loc->pivot->ordem_producao,
                'observacoes' => $loc->pivot->observacao ?? 'Recriado via comando'
            ]);

            $criadas++;
        }

        $this->newLine();
        $this->info("✅ Processo concluído!");
        $this->info("📊 Alocações criadas: {$criadas}");

        return 0;
    }
}
