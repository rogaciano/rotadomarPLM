<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class EstoqueService
{
    protected $apiUrl;
    protected $empresa;
    protected $token;
    protected $armazenador;

    public function __construct()
    {
        $this->apiUrl = config('estoque.api_url');
        $this->empresa = config('estoque.empresa');
        $this->token = config('estoque.token');
        $this->armazenador = config('estoque.armazenador');
        
        // Log das configurações para diagnóstico
        Log::info('EstoqueService inicializado com as seguintes configurações:', [
            'apiUrl' => $this->apiUrl,
            'empresa' => $this->empresa,
            'token' => $this->token ? 'Presente (não exibido por segurança)' : 'Ausente',
            'armazenador' => $this->armazenador
        ]);
    }

    /**
     * Consulta o estoque de um tecido específico pela referência
     *
     * @param string $referencia
     * @return array|null
     */
    public function consultarEstoqueTecido(string $referencia)
    {
        try {
            $response = Http::get($this->apiUrl, [
                'empresa' => $this->empresa,
                'token' => $this->token,
                'armazenador' => $this->armazenador
                // Removido parâmetro referencia, vamos filtrar nos resultados
            ]);

            if ($response->successful()) {
                $data = $response->json();
                // Filtrar os resultados pela referência
                $filteredData = array_filter($data, function($item) use ($referencia) {
                    return isset($item['Referencia']) && $item['Referencia'] === $referencia;
                });
                
                return $this->processarResposta($filteredData);
            }

            Log::error('Erro ao consultar estoque de tecido', [
                'referencia' => $referencia,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Exceção ao consultar estoque de tecido', [
                'referencia' => $referencia,
                'exception' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Consulta o estoque de todos os tecidos
     *
     * @return array
     */
    public function consultarTodosEstoques()
    {
        try {
            // Usar streaming para processar a resposta em partes
            $tempFile = storage_path('app/temp_estoque_response.json');
            
            // Criar o diretório temporário se não existir
            if (!File::exists(storage_path('app'))) {
                File::makeDirectory(storage_path('app'), 0755, true);
            }
            
            // Fazer a requisição usando o cliente HTTP do Laravel
            Log::info('Iniciando download da API de estoque');
            
            // Preparar a URL com os parâmetros
            $url = $this->apiUrl . '?' . http_build_query([
                'empresa' => $this->empresa,
                'token' => $this->token,
                'armazenador' => $this->armazenador
            ]);
            
            // Fazer a requisição e salvar o conteúdo em um arquivo
            $response = @file_get_contents($url);
            if ($response !== false) {
                file_put_contents($tempFile, $response);
                $fileSize = File::size($tempFile);
                Log::info("Download concluído, tamanho do arquivo: " . round($fileSize / 1024 / 1024, 2) . " MB");
                
                // Verificar se o arquivo tem conteúdo válido
                if ($fileSize > 0) {
                    Log::info('Download concluído, iniciando processamento');
                    return $this->processarRespostaArquivo($tempFile);
                } else {
                    Log::error('Arquivo de resposta vazio');
                    return [];
                }
            }
            
            Log::error('Erro ao consultar todos os estoques', [
                'erro' => error_get_last()
            ]);
            
            return [];
        } catch (\Exception $e) {
            Log::error('Exceção ao consultar todos os estoques', [
                'exception' => $e->getMessage()
            ]);
            
            return [];
        } finally {
            // Limpar arquivo temporário se existir
            if (isset($tempFile) && file_exists($tempFile)) {
                @unlink($tempFile);
            }
        }
    }

    /**
     * Processa a resposta da API
     *
     * @param array $data
     * @return array
     */
    /**
     * Processa a resposta da API a partir de um arquivo
     * 
     * @param string $filePath
     * @return array
     */
    protected function processarRespostaArquivo($filePath)
    {
        if (!file_exists($filePath)) {
            Log::error('Arquivo de resposta não encontrado', ['path' => $filePath]);
            return [];
        }
        
        $resultado = [];
        $detalhes = [];
        $processados = 0;
        $tamanhoArquivo = filesize($filePath);
        
        Log::info('Iniciando processamento de arquivo', [
            'tamanho' => round($tamanhoArquivo / 1024 / 1024, 2) . ' MB'
        ]);
        
        // Processar o arquivo linha por linha para economizar memória
        $handle = fopen($filePath, 'r');
        
        if ($handle) {
            // Ler o primeiro caractere para verificar se é um array JSON
            $firstChar = fgetc($handle);
            rewind($handle);
            
            if ($firstChar != '[') {
                Log::error('Formato de resposta inválido, não é um array JSON');
                fclose($handle);
                return [];
            }
            
            // Ler o primeiro caractere (deve ser '[') e descartar
            fgetc($handle);
            
            // Variáveis para controle do parsing
            $buffer = '';
            $depth = 0;
            $inString = false;
            $escape = false;
            
            // Processar caractere por caractere
            while (!feof($handle)) {
                $char = fgetc($handle);
                
                // Controle de strings e caracteres de escape
                if ($char == '\\' && !$escape) {
                    $escape = true;
                } else if ($char == '"' && !$escape) {
                    $inString = !$inString;
                    $escape = false;
                } else {
                    $escape = false;
                }
                
                // Controle de profundidade de chaves
                if (!$inString) {
                    if ($char == '{') {
                        $depth++;
                    } else if ($char == '}') {
                        $depth--;
                        
                        // Se voltamos ao nível 0, temos um objeto JSON completo
                        if ($depth == 0) {
                            $buffer .= $char;
                            
                            // Processar o objeto
                            $item = json_decode($buffer, true);
                            if ($item && isset($item['Referencia']) && isset($item['Estoque'])) {
                                $this->processarItem($item, $resultado, $detalhes);
                                $processados++;
                                
                                // Log a cada 1000 itens
                                if ($processados % 1000 == 0) {
                                    Log::info("Processados {$processados} itens");
                                }
                            }
                            
                            // Limpar o buffer
                            $buffer = '';
                            
                            // Pular a vírgula ou qualquer outro caractere até o próximo objeto
                            while (!feof($handle) && ($nextChar = fgetc($handle)) && $nextChar != '{') {
                                // Não fazer nada, apenas avançar
                            }
                            
                            // Se encontramos uma chave de abertura, adicioná-la ao buffer
                            if (isset($nextChar) && $nextChar == '{') {
                                $buffer = '{';
                                $depth = 1;
                            }
                            
                            continue;
                        }
                    }
                }
                
                // Adicionar o caractere ao buffer
                $buffer .= $char;
            }
            
            fclose($handle);
        }
        
        Log::info("Processamento concluído. Total de {$processados} itens processados.");
        
        // Adiciona os detalhes ao resultado final
        foreach ($detalhes as $referencia => $cores) {
            if (isset($resultado[$referencia])) {
                $resultado[$referencia]['detalhes'] = $cores;
            }
        }
        
        // Liberar memória
        unset($detalhes);
        gc_collect_cycles();
        
        return $resultado;
    }
    
    /**
     * Processa um item individual da resposta
     * 
     * @param array $item
     * @param array &$resultado
     * @param array &$detalhes
     */
    protected function processarItem($item, &$resultado, &$detalhes)
    {
        $referencia = $item['Referencia'];
        $estoque = (float) $item['Estoque'];
        $cor = $item['Cor'] ?? 'Não especificada';
        $tamanho = $item['Tamanho'] ?? 'Não especificado';
        
        // Armazena detalhes por cor e tamanho
        if (!isset($detalhes[$referencia])) {
            $detalhes[$referencia] = [];
        }
        
        if (!isset($detalhes[$referencia][$cor])) {
            $detalhes[$referencia][$cor] = [];
        }
        
        $detalhes[$referencia][$cor][$tamanho] = $estoque;
        
        // Se a referência já existe, soma o estoque
        if (isset($resultado[$referencia])) {
            $resultado[$referencia]['quantidade'] += $estoque;
        } else {
            // Caso contrário, cria uma nova entrada
            $resultado[$referencia] = [
                'quantidade' => $estoque,
                'data_consulta' => now(),
                'detalhes' => []
            ];
        }
    }
    
    /**
     * Processa a resposta da API (método original mantido para compatibilidade)
     *
     * @param array $data
     * @return array
     */
    protected function processarResposta($data)
    {
        // Se não for um array, converte para array
        if (!is_array($data)) {
            return [];
        }

        $resultado = [];
        $detalhes = [];
        
        // Processa o formato de resposta da API
        foreach ($data as $item) {
            $this->processarItem($item, $resultado, $detalhes);
        }
        
        // Adiciona os detalhes ao resultado final
        foreach ($detalhes as $referencia => $cores) {
            if (isset($resultado[$referencia])) {
                $resultado[$referencia]['detalhes'] = $cores;
            }
        }

        return $resultado;
    }
}
