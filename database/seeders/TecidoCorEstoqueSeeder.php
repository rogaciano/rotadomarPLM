<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TecidoCorEstoque;
use App\Models\Tecido;

class TecidoCorEstoqueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar se existem tecidos
        $tecidos = Tecido::take(3)->get();
        
        if ($tecidos->count() === 0) {
            $this->command->info('Nenhum tecido encontrado. Criando tecidos de exemplo...');
            return;
        }
        
        // Cores de exemplo
        $cores = [
            ['cor' => 'Azul', 'codigo_cor' => '#0000FF'],
            ['cor' => 'Vermelho', 'codigo_cor' => '#FF0000'],
            ['cor' => 'Verde', 'codigo_cor' => '#00FF00'],
            ['cor' => 'Amarelo', 'codigo_cor' => '#FFFF00'],
            ['cor' => 'Preto', 'codigo_cor' => '#000000'],
            ['cor' => 'Branco', 'codigo_cor' => '#FFFFFF'],
        ];
        
        foreach ($tecidos as $tecido) {
            // Adicionar 3-4 cores aleatórias para cada tecido
            $coresParaTecido = collect($cores)->random(rand(3, 4));
            
            foreach ($coresParaTecido as $cor) {
                TecidoCorEstoque::updateOrCreate(
                    [
                        'tecido_id' => $tecido->id,
                        'cor' => $cor['cor'],
                    ],
                    [
                        'codigo_cor' => $cor['codigo_cor'],
                        'quantidade' => rand(10, 100),
                        'quantidade_pretendida' => rand(50, 150),
                        'data_atualizacao' => now(),
                        'observacoes' => 'Dados de exemplo para teste'
                    ]
                );
            }
        }
        
        $this->command->info('Dados de exemplo criados para TecidoCorEstoque!');
    }
}