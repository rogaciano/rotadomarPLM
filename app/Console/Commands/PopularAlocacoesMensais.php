<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Produto;
use App\Models\ProdutoAlocacaoMensal;

class PopularAlocacoesMensais extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'produtos:popular-alocacoes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Popular alocações mensais para produtos existentes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Iniciando população de alocações mensais via produto_localizacao...');
        
        // Buscar TODOS os registros de produto_localizacao com data prevista
        try {
            $produtoLocalizacoes = \App\Models\ProdutoLocalizacao::whereNotNull('data_prevista_faccao')
                ->where('quantidade', '>', 0)
                ->get();
        } catch (\Exception $e) {
            $this->error("Erro ao buscar produto_localizacao: " . $e->getMessage());
            return Command::FAILURE;
        }

        $this->info("📦 Encontrados {$produtoLocalizacoes->count()} registros para processar");
        
        $criados = 0;
        $pulados = 0;

        $bar = $this->output->createProgressBar($produtoLocalizacoes->count());
        $bar->start();

        foreach ($produtoLocalizacoes as $pl) {
            // Verificar se já existe alocação para este produto_localizacao
            $alocacaoExistente = ProdutoAlocacaoMensal::where('produto_localizacao_id', $pl->id)
                ->exists();

            if ($alocacaoExistente) {
                $pulados++;
                $bar->advance();
                continue;
            }

            try {
                // Converter data para Carbon se for string
                $dataFaccao = is_string($pl->data_prevista_faccao) 
                    ? \Carbon\Carbon::parse($pl->data_prevista_faccao)
                    : $pl->data_prevista_faccao;
                
                // Criar alocação
                ProdutoAlocacaoMensal::create([
                    'produto_id' => $pl->produto_id,
                    'produto_localizacao_id' => $pl->id,
                    'localizacao_id' => $pl->localizacao_id,
                    'mes' => $dataFaccao->month,
                    'ano' => $dataFaccao->year,
                    'quantidade' => $pl->quantidade,
                    'tipo' => 'original',
                    'ordem_producao' => $pl->ordem_producao,
                    'usuario_id' => 1, // ID do primeiro usuário admin
                    'observacoes' => $pl->observacao ?? 'Alocação inicial criada automaticamente via produto_localizacao'
                ]);

                $criados++;
            } catch (\Exception $e) {
                $this->error("Erro ao processar produto_localizacao ID {$pl->id}: " . $e->getMessage());
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Processo concluído!");
        $this->info("📊 Alocações criadas: {$criados}");
        $this->info("⏭️  Registros pulados (já tinham alocação): {$pulados}");

        return Command::SUCCESS;
    }
}
