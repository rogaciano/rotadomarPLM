<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserFilter;

class TestUserFilters extends Command
{
    protected $signature = 'test:filters {user_id}';
    protected $description = 'Testa a funcionalidade de salvar filtros de usuário';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("Usuário com ID {$userId} não encontrado");
            return 1;
        }
        
        $this->info("Testando filtros para o usuário: {$user->name}");
        
        try {
            // Tenta salvar um filtro de teste
            $testFilters = [
                'test_filter' => 'test_value',
                'timestamp' => now()->toDateTimeString()
            ];
            
            $result = $user->saveFilters('test_page', $testFilters);
            
            $this->info("Filtro salvo com sucesso: " . json_encode($result->toArray()));
            
            // Tenta recuperar o filtro
            $savedFilters = $user->getFilters('test_page');
            
            $this->info("Filtros recuperados: " . json_encode($savedFilters));
            
            // Verifica se os filtros foram salvos corretamente
            if ($savedFilters['test_filter'] === 'test_value') {
                $this->info("✅ Teste bem-sucedido! Os filtros foram salvos e recuperados corretamente.");
            } else {
                $this->error("❌ Teste falhou! Os filtros recuperados não correspondem aos filtros salvos.");
            }
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Erro ao testar filtros: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}
