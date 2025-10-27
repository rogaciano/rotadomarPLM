<?php

namespace App\Http\Controllers;

use App\Models\ProdutoObservacao;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProdutoObservacaoController extends Controller
{
    /**
     * Armazena uma nova observação
     */
    public function store(Request $request)
    {
        // Log para debug
        \Log::info('ProdutoObservacao store chamado', [
            'produto_id' => $request->produto_id,
            'observacao' => $request->observacao,
            'observacao_length' => strlen($request->observacao ?? ''),
        ]);

        // Verificar permissão
        if (!Auth::user()->canUpdate('produtos')) {
            abort(403, 'Você não tem permissão para adicionar observações.');
        }

        $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'observacao' => 'required|string|max:5000',
        ], [
            'produto_id.required' => 'Produto não identificado.',
            'produto_id.exists' => 'Produto não encontrado.',
            'observacao.required' => 'A observação é obrigatória.',
            'observacao.max' => 'A observação não pode ter mais de 5000 caracteres.',
        ]);

        // Validar se há conteúdo real (não apenas HTML vazio do Quill)
        $observacao = $request->observacao;
        $textoLimpo = trim(strip_tags($observacao));
        
        if (empty($textoLimpo) || $observacao === '<p><br></p>') {
            return response()->json([
                'success' => false,
                'message' => 'A observação não pode estar vazia.'
            ], 422);
        }

        try {
            \Log::info('Tentando criar observação', [
                'produto_id' => $request->produto_id,
                'usuario_id' => Auth::id(),
                'observacao_preview' => substr($request->observacao, 0, 100),
            ]);

            $observacaoModel = ProdutoObservacao::create([
                'produto_id' => $request->produto_id,
                'observacao' => $request->observacao,
                'usuario_id' => Auth::id(),
            ]);

            \Log::info('Observação criada com sucesso', ['id' => $observacaoModel->id]);

            return response()->json([
                'success' => true,
                'message' => 'Observação adicionada com sucesso!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao criar observação', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao adicionar observação: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove uma observação
     */
    public function destroy($id)
    {
        // Verificar permissão
        if (!Auth::user()->canUpdate('produtos')) {
            abort(403, 'Você não tem permissão para remover observações.');
        }

        try {
            $observacao = ProdutoObservacao::findOrFail($id);
            $observacao->delete();

            return response()->json([
                'success' => true,
                'message' => 'Observação removida com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover observação: ' . $e->getMessage()
            ], 500);
        }
    }
}
