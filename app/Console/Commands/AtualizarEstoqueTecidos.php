<?php

namespace App\Console\Commands;

use App\Models\Tecido;
use App\Models\TecidoCorEstoque;
use App\Services\EstoqueService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AtualizarEstoqueTecidos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tecidos:atualizar-estoque {--chunk=10 : Número de tecidos a processar por lote} {--memory-limit=128 : Limite de memória em MB}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza o estoque de todos os tecidos que possuem referência';

    /**
     * The EstoqueService instance.
     *
     * @var EstoqueService
     */
    protected $estoqueService;

    /**
     * Create a new command instance.
     *
     * @param EstoqueService $estoqueService
     * @return void
     */
    public function __construct(EstoqueService $estoqueService)
    {
        parent::__construct();
        $this->estoqueService = $estoqueService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Configurar limite de memória
        $memoryLimit = $this->option('memory-limit');
        ini_set('memory_limit', $memoryLimit . 'M');
        
        $this->info('Iniciando atualização de estoque de tecidos...');
        $this->info('Memória inicial: ' . $this->formatBytes(memory_get_usage(true)));
        Log::info('Comando de atualização de estoque iniciado');

        // Contar tecidos para a barra de progresso
        $totalTecidos = Tecido::whereNotNull('referencia')->count();

        if ($totalTecidos == 0) {
            $this->warn('Não há tecidos com referência para atualizar.');
            Log::info('Nenhum tecido com referência encontrado para atualização');
            return 0;
        }

        $this->info('Total de tecidos a processar: ' . $totalTecidos);
        $this->info('Consultando API de estoque...');
        $this->info('Memória antes de consultar API: ' . $this->formatBytes(memory_get_usage(true)));
        
        // Usar try-catch para capturar erros de memória
        try {
            $todosEstoques = $this->estoqueService->consultarTodosEstoques();
            
            $this->info('Memória após consultar API: ' . $this->formatBytes(memory_get_usage(true)));

            if (empty($todosEstoques)) {
                $this->error('Não foi possível obter informações de estoque da API.');
                Log::error('Falha ao obter dados de estoque da API');
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('Erro ao consultar API de estoque: ' . $e->getMessage());
            Log::error('Erro ao consultar API de estoque', ['exception' => $e->getMessage()]);
            return 1;
        }

        $atualizados = 0;
        $dataConsulta = now();
        $chunkSize = $this->option('chunk');
        
        $this->output->progressStart($totalTecidos);

        // Processar tecidos em lotes para economizar memória
        Tecido::whereNotNull('referencia')->chunkById($chunkSize, function ($tecidos) use (&$atualizados, $todosEstoques, $dataConsulta) {
            foreach ($tecidos as $tecido) {
                if (empty($tecido->referencia)) {
                    $this->output->progressAdvance();
                    continue;
                }

                // Filtrar os dados de estoque para este tecido
                $referencia = $tecido->referencia;
                if (isset($todosEstoques[$referencia])) {
                    $dadosEstoque = $todosEstoques[$referencia];
                    
                    try {
                        DB::beginTransaction();
                        
                        // Atualizar o tecido com os dados de estoque
                        $tecido->update([
                            'quantidade_estoque' => $dadosEstoque['quantidade'],
                            'ultima_consulta_estoque' => $dataConsulta
                        ]);
                        
                        // Remover registros antigos de estoque por cor para este tecido
                        TecidoCorEstoque::where('tecido_id', $tecido->id)->delete();
                        
                        // Inserir novos registros de estoque por cor
                        if (isset($dadosEstoque['detalhes']) && is_array($dadosEstoque['detalhes'])) {
                            foreach ($dadosEstoque['detalhes'] as $cor => $tamanhos) {
                                // Calcular quantidade total para esta cor
                                $quantidadeCor = 0;
                                foreach ($tamanhos as $tamanho => $quantidade) {
                                    $quantidadeCor += $quantidade;
                                }
                                
                                // Criar registro de estoque por cor
                                TecidoCorEstoque::create([
                                    'tecido_id' => $tecido->id,
                                    'cor' => $cor,
                                    'codigo_cor' => null, // Não temos esta informação no retorno da API
                                    'quantidade' => $quantidadeCor,
                                    'data_atualizacao' => $dataConsulta,
                                    'observacoes' => 'Tamanhos disponíveis: ' . implode(', ', array_keys($tamanhos))
                                ]);
                            }
                        }
                        
                        DB::commit();
                        $atualizados++;
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Log::error('Erro ao atualizar tecido #' . $tecido->id . ': ' . $e->getMessage());
                        $this->error('Erro ao atualizar tecido #' . $tecido->id . ': ' . $e->getMessage());
                    }
                }
                
                $this->output->progressAdvance();
                
                // Liberar memória
                unset($dadosEstoque);
                gc_collect_cycles();
            }
        });

        $this->output->progressFinish();
        
        $this->info("Estoque atualizado para {$atualizados} de {$totalTecidos} tecidos.");
        $this->info('Memória final: ' . $this->formatBytes(memory_get_usage(true)));
        Log::info("Estoque atualizado para {$atualizados} de {$totalTecidos} tecidos.");

        return 0;
    }
    
    /**
     * Formata bytes para uma unidade legível
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    private function formatBytes($bytes, $precision = 2) 
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
