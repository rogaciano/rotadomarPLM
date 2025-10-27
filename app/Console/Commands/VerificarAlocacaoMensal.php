<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProdutoLocalizacao;
use App\Models\ProdutoAlocacaoMensal;
use Carbon\Carbon;

class VerificarAlocacaoMensal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alocacao:verificar {--fix : Corrigir automaticamente as inconsistências}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica e corrige inconsistências entre produto_localizacao e produto_alocacao_mensal';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verificando inconsistências entre produto_localizacao e produto_alocacao_mensal...');
        $this->newLine();

        $fix = $this->option('fix');
        
        // Relatórios
        $semAlocacao = [];
        $orfaos = [];
        
        // 1. Verificar localizações sem alocação mensal
        $this->line('📋 Verificando localizações sem alocação mensal...');
        $semAlocacao = $this->verificarLocalizacoesSemAlocacao();
        
        // 2. Verificar alocações órfãs
        $this->newLine();
        $this->line('📋 Verificando alocações órfãs...');
        $orfaos = $this->verificarAlocacoesOrfas();
        
        // 3. Exibir relatório
        $this->newLine();
        $this->exibirRelatorio($semAlocacao, $orfaos);
        
        // 4. Corrigir se solicitado
        if ($fix && (count($semAlocacao) > 0 || count($orfaos) > 0)) {
            $this->newLine();
            if ($this->confirm('Deseja corrigir as inconsistências encontradas?', true)) {
                $this->corrigirInconsistencias($semAlocacao, $orfaos);
            }
        } elseif (!$fix && (count($semAlocacao) > 0 || count($orfaos) > 0)) {
            $this->newLine();
            $this->comment('💡 Execute o comando com --fix para corrigir automaticamente:');
            $this->comment('   php artisan alocacao:verificar --fix');
        }
        
        $this->newLine();
        $this->info('✅ Verificação concluída!');
        
        return 0;
    }
    
    /**
     * Verificar localizações que deveriam ter alocação mensal mas não têm
     */
    private function verificarLocalizacoesSemAlocacao()
    {
        $localizacoes = ProdutoLocalizacao::whereNotNull('data_prevista_faccao')
            ->where('quantidade', '>', 0)
            ->get();
        
        $semAlocacao = [];
        
        foreach ($localizacoes as $loc) {
            $dataFaccao = is_string($loc->data_prevista_faccao) 
                ? Carbon::parse($loc->data_prevista_faccao)
                : $loc->data_prevista_faccao;
            
            // Verificar se existe alocação mensal (duas formas):
            // 1. Com produto_localizacao_id (registros novos)
            // 2. Ou com os mesmos dados mas sem produto_localizacao_id (registros antigos)
            $alocacao = ProdutoAlocacaoMensal::where(function($query) use ($loc, $dataFaccao) {
                    $query->where('produto_localizacao_id', $loc->id)
                        ->orWhere(function($q) use ($loc, $dataFaccao) {
                            $q->where('produto_id', $loc->produto_id)
                              ->where('localizacao_id', $loc->localizacao_id)
                              ->where('mes', $dataFaccao->month)
                              ->where('ano', $dataFaccao->year);
                        });
                })
                ->first();
            
            if (!$alocacao) {
                $semAlocacao[] = [
                    'id' => $loc->id,
                    'produto_id' => $loc->produto_id,
                    'localizacao_id' => $loc->localizacao_id,
                    'ordem_producao' => $loc->ordem_producao,
                    'quantidade' => $loc->quantidade,
                    'data_prevista_faccao' => $dataFaccao->format('d/m/Y'),
                    'mes' => $dataFaccao->month,
                    'ano' => $dataFaccao->year
                ];
            }
        }
        
        return $semAlocacao;
    }
    
    /**
     * Verificar alocações órfãs (sem produto_localizacao correspondente)
     */
    private function verificarAlocacoesOrfas()
    {
        // Alocações com produto_localizacao_id preenchido mas que não existe mais
        $alocacoes = ProdutoAlocacaoMensal::whereNotNull('produto_localizacao_id')->get();
        
        $orfaos = [];
        
        foreach ($alocacoes as $alocacao) {
            $localizacao = ProdutoLocalizacao::find($alocacao->produto_localizacao_id);
            
            if (!$localizacao) {
                $orfaos[] = [
                    'id' => $alocacao->id,
                    'produto_id' => $alocacao->produto_id,
                    'localizacao_id' => $alocacao->localizacao_id,
                    'produto_localizacao_id' => $alocacao->produto_localizacao_id,
                    'ordem_producao' => $alocacao->ordem_producao,
                    'quantidade' => $alocacao->quantidade,
                    'mes' => $alocacao->mes,
                    'ano' => $alocacao->ano
                ];
            }
        }
        
        return $orfaos;
    }
    
    /**
     * Exibir relatório de inconsistências
     */
    private function exibirRelatorio($semAlocacao, $orfaos)
    {
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('📊 RELATÓRIO DE INCONSISTÊNCIAS');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        // Localizações sem alocação
        $this->newLine();
        $this->line('🔴 Localizações sem alocação mensal: ' . count($semAlocacao));
        if (count($semAlocacao) > 0) {
            $this->table(
                ['ID', 'Produto', 'Localização', 'OP', 'Qtd', 'Data Facção', 'Mês/Ano'],
                array_map(function($item) {
                    return [
                        $item['id'],
                        $item['produto_id'],
                        $item['localizacao_id'],
                        $item['ordem_producao'],
                        $item['quantidade'],
                        $item['data_prevista_faccao'],
                        $item['mes'] . '/' . $item['ano']
                    ];
                }, $semAlocacao)
            );
        }
        
        // Alocações órfãs
        $this->newLine();
        $this->line('🔴 Alocações órfãs (produto_localizacao não existe): ' . count($orfaos));
        if (count($orfaos) > 0) {
            $this->table(
                ['ID Alocação', 'Produto', 'Localização', 'ID ProdLoc (órfão)', 'OP', 'Qtd', 'Mês/Ano'],
                array_map(function($item) {
                    return [
                        $item['id'],
                        $item['produto_id'],
                        $item['localizacao_id'],
                        $item['produto_localizacao_id'],
                        $item['ordem_producao'] ?? 'N/A',
                        $item['quantidade'],
                        $item['mes'] . '/' . $item['ano']
                    ];
                }, $orfaos)
            );
        }
        
        // Resumo
        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $total = count($semAlocacao) + count($orfaos);
        if ($total == 0) {
            $this->info('✅ Nenhuma inconsistência encontrada! Tudo está OK.');
        } else {
            $countSemAlocacao = count($semAlocacao);
            $countOrfaos = count($orfaos);
            $this->warn("⚠️  Total de inconsistências: {$total}");
            $this->warn("   - {$countSemAlocacao} localizações sem alocação mensal");
            $this->warn("   - {$countOrfaos} alocações órfãs");
        }
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
    
    /**
     * Corrigir inconsistências encontradas
     */
    private function corrigirInconsistencias($semAlocacao, $orfaos)
    {
        $this->info('🔧 Iniciando correção...');
        $this->newLine();
        
        $criados = 0;
        $removidos = 0;
        
        // 1. Criar alocações faltantes
        if (count($semAlocacao) > 0) {
            $this->line('📝 Criando alocações mensais faltantes...');
            
            foreach ($semAlocacao as $item) {
                try {
                    ProdutoAlocacaoMensal::create([
                        'produto_id' => $item['produto_id'],
                        'produto_localizacao_id' => $item['id'],
                        'localizacao_id' => $item['localizacao_id'],
                        'mes' => $item['mes'],
                        'ano' => $item['ano'],
                        'quantidade' => $item['quantidade'],
                        'tipo' => 'original',
                        'ordem_producao' => $item['ordem_producao'],
                        'observacoes' => 'Criado automaticamente pela rotina de verificação em ' . now()->format('d/m/Y H:i'),
                        'usuario_id' => 1 // Sistema
                    ]);
                    
                    $criados++;
                    $this->info("   ✓ Criada alocação para produto {$item['produto_id']}, OP {$item['ordem_producao']}");
                    
                } catch (\Exception $e) {
                    $this->error("   ✗ Erro ao criar alocação para produto {$item['produto_id']}: {$e->getMessage()}");
                }
            }
        }
        
        // 2. Remover alocações órfãs
        if (count($orfaos) > 0) {
            $this->newLine();
            $this->line('🗑️  Removendo alocações órfãs...');
            
            foreach ($orfaos as $item) {
                try {
                    $alocacao = ProdutoAlocacaoMensal::find($item['id']);
                    if ($alocacao) {
                        $alocacao->delete();
                        $removidos++;
                        $this->info("   ✓ Removida alocação órfã ID {$item['id']} (produto_localizacao_id {$item['produto_localizacao_id']} não existe)");
                    }
                } catch (\Exception $e) {
                    $this->error("   ✗ Erro ao remover alocação ID {$item['id']}: {$e->getMessage()}");
                }
            }
        }
        
        // Resumo da correção
        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('✅ CORREÇÃO CONCLUÍDA');
        $this->info("   - {$criados} alocações mensais criadas");
        $this->info("   - {$removidos} alocações órfãs removidas");
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}
