<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\ProdutoCombinacao;
use App\Models\ProdutoCombinacaoComponente;
use App\Models\Tecido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProdutoCombinacaoController extends Controller
{
    /**
     * Store a newly created combination in storage.
     */
    public function store(Request $request, $produtoId)
    {
        if (!auth()->user()->canUpdate('produtos')) { abort(403); }

        $produto = Produto::findOrFail($produtoId);

        $validator = Validator::make($request->all(), [
            'descricao' => 'required|string|max:255',
            'quantidade_pretendida' => 'required|integer|min:1',
            'observacoes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $combinacao = $produto->combinacoes()->create([
            'descricao' => $request->descricao,
            'quantidade_pretendida' => $request->quantidade_pretendida,
            'observacoes' => $request->observacoes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Combinação criada com sucesso!',
            'combinacao' => $combinacao
        ]);
    }

    /**
     * Update the specified combination in storage.
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->canUpdate('produtos')) { abort(403); }

        $combinacao = ProdutoCombinacao::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'descricao' => 'required|string|max:255',
            'quantidade_pretendida' => 'required|integer|min:1',
            'observacoes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $combinacao->update([
            'descricao' => $request->descricao,
            'quantidade_pretendida' => $request->quantidade_pretendida,
            'observacoes' => $request->observacoes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Combinação atualizada com sucesso!',
            'combinacao' => $combinacao
        ]);
    }

    /**
     * Remove the specified combination from storage.
     */
    public function destroy($id)
    {
        if (!auth()->user()->canUpdate('produtos')) { abort(403); }

        $combinacao = ProdutoCombinacao::findOrFail($id);
        $combinacao->delete();

        return response()->json([
            'success' => true,
            'message' => 'Combinação removida com sucesso!'
        ]);
    }

    /**
     * Add a component to a combination.
     */
    public function addComponente(Request $request, $combinacaoId)
    {
        if (!auth()->user()->canUpdate('produtos')) { abort(403); }

        $combinacao = ProdutoCombinacao::findOrFail($combinacaoId);

        $validator = Validator::make($request->all(), [
            'tecido_id' => 'required|exists:tecidos,id',
            'cor' => 'required|string|max:255',
            'codigo_cor' => 'nullable|string|max:50',
            'consumo' => 'required|numeric|min:0.001',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $componente = $combinacao->componentes()->create([
            'tecido_id' => $request->tecido_id,
            'cor' => $request->cor,
            'codigo_cor' => $request->codigo_cor,
            'consumo' => $request->consumo,
        ]);

        // Load the tecido relationship
        $componente->load('tecido');

        return response()->json([
            'success' => true,
            'message' => 'Componente adicionado com sucesso!',
            'componente' => $componente
        ]);
    }

    /**
     * Update a component.
     */
    public function updateComponente(Request $request, $componenteId)
    {
        if (!auth()->user()->canUpdate('produtos')) { abort(403); }

        $componente = ProdutoCombinacaoComponente::findOrFail($componenteId);

        $validator = Validator::make($request->all(), [
            'tecido_id' => 'required|exists:tecidos,id',
            'cor' => 'required|string|max:255',
            'codigo_cor' => 'nullable|string|max:50',
            'consumo' => 'required|numeric|min:0.001',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $componente->update([
            'tecido_id' => $request->tecido_id,
            'cor' => $request->cor,
            'codigo_cor' => $request->codigo_cor,
            'consumo' => $request->consumo,
        ]);

        // Load the tecido relationship
        $componente->load('tecido');

        return response()->json([
            'success' => true,
            'message' => 'Componente atualizado com sucesso!',
            'componente' => $componente
        ]);
    }

    /**
     * Remove a component.
     */
    public function removeComponente($componenteId)
    {
        if (!auth()->user()->canUpdate('produtos')) { abort(403); }

        $componente = ProdutoCombinacaoComponente::findOrFail($componenteId);
        $componente->delete();

        return response()->json([
            'success' => true,
            'message' => 'Componente removido com sucesso!'
        ]);
    }

    /**
     * Get colors for a specific fabric.
     */
    public function getTecidoCores($tecidoId)
    {
        if (!auth()->user()->canRead('produtos')) { abort(403); }

        $tecido = Tecido::findOrFail($tecidoId);
        $estoquesCores = $tecido->estoquesCores;
        
        $cores = [];
        foreach ($estoquesCores as $estoqueCor) {
            $estoque = $estoqueCor->quantidade ?? 0;
            $necessidade = $estoqueCor->necessidade ?? 0;
            $saldo = $estoque - $necessidade;
            $producaoPossivel = $saldo > 0 ? floor($saldo / ($tecido->consumo_medio ?: 0.5)) : 0;
            
            $cores[] = [
                'cor' => $estoqueCor->cor,
                'codigo_cor' => $estoqueCor->codigo_cor,
                'estoque' => $estoque,
                'necessidade' => $necessidade,
                'saldo' => $saldo,
                'producao_possivel' => $producaoPossivel
            ];
        }

        return response()->json([
            'success' => true,
            'cores' => $cores
        ]);
    }

    /**
     * Get all combinations for a product.
     */
    public function getCombinacoes($produtoId)
    {
        if (!auth()->user()->canRead('produtos')) { abort(403); }

        $produto = Produto::findOrFail($produtoId);
        $combinacoes = $produto->combinacoes()
            ->with(['componentes' => function($query) {
                $query->with('tecido:id,descricao');
            }])
            ->get();

        return response()->json([
            'success' => true,
            'combinacoes' => $combinacoes
        ]);
    }
}
