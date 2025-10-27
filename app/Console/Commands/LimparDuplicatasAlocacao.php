<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProdutoAlocacaoMensal;
use Illuminate\Support\Facades\DB;

class LimparDuplicatasAlocacao extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alocacao:limpar-duplicatas {--dry-run : Apenas exibir duplicatas sem remover}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove duplicatas de produto_alocacao_mensal criadas por engano';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Procurando duplicatas em produto_alocacao_mensal...');
        $this->newLine();

        $dryRun = $this->option('dry-run');
        
        // Buscar grupos de registros duplicados
        // Duplicatas = mesmo produto_id, localizacao_id, mes, ano
        $duplicatas = DB::table('produto_alocacao_mensal')
            ->select(
                'produto_id',
                'localizacao_id',
                'mes',
                'ano',
                DB::raw('COUNT(*) as total'),
                DB::raw('GROUP_CONCAT(id ORDER BY id) as ids'),
                DB::raw('GROUP_CONCAT(produto_localizacao_id ORDER BY id) as pivot_ids'),
                DB::raw('GROUP_CONCAT(created_at ORDER BY id) as dates')
            )
            ->whereNull('deleted_at')
            ->groupBy('produto_id', 'localizacao_id', 'mes', 'ano')
            ->having('total', '>', 1)
            ->get();

        if ($duplicatas->isEmpty()) {
            $this->info('✅ Nenhuma duplicata encontrada!');
            return 0;
        }

        $this->warn("⚠️  Encontradas " . count($duplicatas) . " grupos de duplicatas");
        $this->newLine();

        $removidos = 0;
        $mantidos = 0;

        foreach ($duplicatas as $dup) {
            // Buscar os registros detalhados diretamente para evitar problemas com GROUP_CONCAT e NULL
            $registrosDb = ProdutoAlocacaoMensal::where('produto_id', $dup->produto_id)
                ->where('localizacao_id', $dup->localizacao_id)
                ->where('mes', $dup->mes)
                ->where('ano', $dup->ano)
                ->orderBy('id')
                ->get();

            $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->line("Produto: {$dup->produto_id} | Localização: {$dup->localizacao_id} | Mês/Ano: {$dup->mes}/{$dup->ano}");
            $this->line("Total de registros duplicados: {$dup->total}");
            
            // Exibir todos os registros duplicados
            $registros = [];
            foreach ($registrosDb as $reg) {
                $registros[] = [
                    'ID' => $reg->id,
                    'produto_localizacao_id' => $reg->produto_localizacao_id ?: 'NULL',
                    'Criado em' => $reg->created_at
                ];
            }
            $this->table(['ID', 'produto_localizacao_id', 'Criado em'], $registros);

            // Decidir qual manter e quais remover
            // Prioridade: 
            // 1. Manter o que tem produto_localizacao_id preenchido (mais recente se houver múltiplos)
            // 2. Se nenhum tem produto_localizacao_id, manter o mais antigo
            
            $indiceParaManter = null;
            
            // Procurar último registro com produto_localizacao_id preenchido (mais recente)
            for ($i = $registrosDb->count() - 1; $i >= 0; $i--) {
                if (!empty($registrosDb[$i]->produto_localizacao_id)) {
                    $indiceParaManter = $i;
                    break;
                }
            }
            
            // Se nenhum tem produto_localizacao_id, manter o mais antigo (primeiro)
            if ($indiceParaManter === null) {
                $indiceParaManter = 0;
            }

            $regParaManter = $registrosDb[$indiceParaManter];
            $this->info("   → Manter: ID {$regParaManter->id} (produto_localizacao_id: " . ($regParaManter->produto_localizacao_id ?: 'NULL') . ")");

            // Remover os outros
            foreach ($registrosDb as $i => $reg) {
                if ($i !== $indiceParaManter) {
                    if (!$dryRun) {
                        ProdutoAlocacaoMensal::where('id', $reg->id)->delete();
                        $this->comment("   ✓ Removido: ID {$reg->id}");
                        $removidos++;
                    } else {
                        $this->comment("   [DRY-RUN] Seria removido: ID {$reg->id}");
                        $removidos++;
                    }
                } else {
                    $mantidos++;
                }
            }
            
            $this->newLine();
        }

        // Resumo
        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        if ($dryRun) {
            $this->info('📊 RESUMO (DRY-RUN - Nada foi alterado)');
        } else {
            $this->info('✅ LIMPEZA CONCLUÍDA');
        }
        $this->info("   - {$mantidos} registros mantidos");
        $this->info("   - {$removidos} registros " . ($dryRun ? "seriam removidos" : "removidos"));
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        if ($dryRun) {
            $this->newLine();
            $this->comment('💡 Execute sem --dry-run para aplicar as mudanças:');
            $this->comment('   php artisan alocacao:limpar-duplicatas');
        }

        return 0;
    }
}
