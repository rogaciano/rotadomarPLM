<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\ProdutoLocalizacao;
use Illuminate\Http\Request;

class ProdutoLocalizacaoController extends Controller
{
    /**
     * Adicionar uma localização ao produto
     */
    public function store(Request $request, $produtoId)
    {
        if (!auth()->user()->canUpdate('produtos')) {
            abort(403);
        }

        $request->validate([
            'localizacao_id' => 'required|exists:localizacoes,id',
            'quantidade' => 'required|integer|min:1',
            'data_prevista_faccao' => 'nullable|date',
            'ordem_producao' => 'required|string|max:30',
            'observacao' => 'nullable|string|max:255'
        ]);

        $produto = Produto::findOrFail($produtoId);

        // Verificar se já existe essa ordem de produção para este produto e localização
        $existe = ProdutoLocalizacao::where('produto_id', $produtoId)
            ->where('localizacao_id', $request->localizacao_id)
            ->where('ordem_producao', $request->ordem_producao)
            ->first();

        if ($existe) {
            return redirect()->route('produtos.show', $produtoId)
                ->with('error', "Já existe um registro com a Ordem de Produção '{$request->ordem_producao}' para esta localização. Use a opção 'Editar' para alterar.");
        }

        // Criar a associação
        ProdutoLocalizacao::create([
            'produto_id' => $produtoId,
            'localizacao_id' => $request->localizacao_id,
            'quantidade' => $request->quantidade,
            'data_prevista_faccao' => $request->data_prevista_faccao,
            'ordem_producao' => $request->ordem_producao,
            'observacao' => $request->observacao
        ]);

        return redirect()->route('produtos.show', $produtoId)
            ->with('success', 'Localização adicionada com sucesso!');
    }

    /**
     * Atualizar uma localização do produto
     */
    public function update(Request $request, $produtoId, $produtoLocalizacaoId)
    {
        if (!auth()->user()->canUpdate('produtos')) {
            abort(403);
        }

        $request->validate([
            'quantidade' => 'required|integer|min:1',
            'data_prevista_faccao' => 'nullable|date',
            'ordem_producao' => 'required|string|max:30',
            'observacao' => 'nullable|string|max:255'
        ]);

        // Buscar o registro na tabela pivot pelo ID
        $produtoLocalizacao = ProdutoLocalizacao::where('id', $produtoLocalizacaoId)
            ->where('produto_id', $produtoId)
            ->firstOrFail();

        // Verificar se a nova ordem de produção cria duplicata com outro registro
        $existe = ProdutoLocalizacao::where('produto_id', $produtoId)
            ->where('localizacao_id', $produtoLocalizacao->localizacao_id)
            ->where('ordem_producao', $request->ordem_producao)
            ->where('id', '!=', $produtoLocalizacaoId) // Excluir o registro atual
            ->first();

        if ($existe) {
            return redirect()->route('produtos.show', $produtoId)
                ->with('error', "Já existe outro registro com a Ordem de Produção '{$request->ordem_producao}' para esta localização.");
        }

        // Atualizar os dados
        $produtoLocalizacao->update([
            'quantidade' => $request->quantidade,
            'data_prevista_faccao' => $request->data_prevista_faccao,
            'ordem_producao' => $request->ordem_producao,
            'observacao' => $request->observacao
        ]);

        return redirect()->route('produtos.show', $produtoId)
            ->with('success', 'Localização atualizada com sucesso!');
    }

    /**
     * Remover uma localização do produto
     */
    public function destroy($produtoId, $produtoLocalizacaoId)
    {
        if (!auth()->user()->canUpdate('produtos')) {
            abort(403);
        }

        // Buscar o registro na tabela pivot pelo ID
        $produtoLocalizacao = ProdutoLocalizacao::where('id', $produtoLocalizacaoId)
            ->where('produto_id', $produtoId)
            ->firstOrFail();

        // Guardar ordem de produção para log
        $ordemProducao = $produtoLocalizacao->ordem_producao;
        $localizacaoId = $produtoLocalizacao->localizacao_id;
        
        // Deletar o registro (isso vai disparar o Observer automaticamente)
        $produtoLocalizacao->delete();
        
        // Log para debug
        \Log::info("Localização removida do produto", [
            'produto_id' => $produtoId,
            'localizacao_id' => $localizacaoId,
            'ordem_producao' => $ordemProducao,
            'pivot_id' => $produtoLocalizacaoId,
            'user_id' => auth()->id()
        ]);

        return redirect()->route('produtos.show', $produtoId)
            ->with('success', 'Localização removida com sucesso!');
    }
}
