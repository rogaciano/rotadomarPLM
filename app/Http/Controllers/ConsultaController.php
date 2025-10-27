<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Status;
use App\Models\Localizacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultaController extends Controller
{
    /**
     * Exibe o resumo de produtos por localização, filtrado por status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function produtosAtivosPorLocalizacao(Request $request)
    {
        // Buscar todos os status disponíveis
        $todosStatus = Status::orderBy('descricao')->get();
        
        // Determinar o status selecionado (padrão: "Ativo" ou o primeiro status disponível)
        $statusId = $request->status_id;
        
        if (!$statusId) {
            $statusAtivo = Status::where('descricao', 'Ativo')->first();
            $statusId = $statusAtivo ? $statusAtivo->id : ($todosStatus->isNotEmpty() ? $todosStatus->first()->id : null);
        }
        
        $statusSelecionado = $todosStatus->where('id', $statusId)->first();
        $produtosPorLocalizacao = [];

        if ($statusId) {
            // Buscar todos os produtos com o status selecionado
            $produtosAtivos = Produto::where('status_id', $statusId)->get();

            // Agrupar produtos por localização usando o accessor getLocalizacaoAtualAttribute
            $localizacoes = [];

            foreach ($produtosAtivos as $produto) {
                $localizacaoAtual = $produto->getLocalizacaoAtualAttribute();

                if ($localizacaoAtual) {
                    $nomeLocalizacao = $localizacaoAtual->nome_localizacao;

                    if (!isset($localizacoes[$nomeLocalizacao])) {
                        $localizacoes[$nomeLocalizacao] = 0;
                    }

                    $localizacoes[$nomeLocalizacao]++;
                } else {
                    // Produtos sem localização definida
                    if (!isset($localizacoes['Sem localização'])) {
                        $localizacoes['Sem localização'] = 0;
                    }

                    $localizacoes['Sem localização']++;
                }
            }

            // Converter para o formato esperado pela view
            $produtosPorLocalizacao = collect();

            foreach ($localizacoes as $nomeLocalizacao => $total) {
                $produtosPorLocalizacao->push([
                    'nome_localizacao' => $nomeLocalizacao,
                    'total' => $total
                ]);
            }

            // Ordenar por total (decrescente)
            $produtosPorLocalizacao = $produtosPorLocalizacao->sortByDesc('total')->values();
        }

        // Total de produtos
        $totalProdutos = $produtosPorLocalizacao->sum('total');

        return view('consultas.produtos_ativos_por_localizacao', compact(
            'produtosPorLocalizacao', 
            'totalProdutos', 
            'todosStatus', 
            'statusSelecionado'
        ));
    }
}
