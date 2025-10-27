<?php

namespace App\Http\Controllers;

use App\Models\GrupoProduto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GrupoProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = GrupoProduto::query();

        // Filtros
        if ($request->filled('descricao')) {
            $query->where('descricao', 'like', '%' . $request->descricao . '%');
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo);
        }
        
        if ($request->filled('created_at')) {
            $query->whereDate('created_at', $request->created_at);
        }

        $grupo_produtos = $query->orderBy('descricao')->paginate(10);
        
        return view('grupo_produtos.index', compact('grupo_produtos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('grupo_produtos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'descricao' => 'required|string|max:255',
            'ativo' => 'required|boolean',
        ]);

        GrupoProduto::create($validated);

        return redirect()->route('grupo_produtos.index')
            ->with('success', 'Grupo de Produtos criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(GrupoProduto $grupo_produto)
    {
        return view('grupo_produtos.show', compact('grupo_produto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GrupoProduto $grupo_produto)
    {
        return view('grupo_produtos.edit', compact('grupo_produto'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GrupoProduto $grupo_produto)
    {
        $validated = $request->validate([
            'descricao' => 'required|string|max:255',
            'ativo' => 'required|boolean',
        ]);

        $grupo_produto->update($validated);

        return redirect()->route('grupo_produtos.index')
            ->with('success', 'Grupo de Produtos atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GrupoProduto $grupo_produto)
    {
        // Verificar se existem produtos associados
        if ($grupo_produto->produtos->count() > 0) {
            return redirect()->route('grupo_produtos.index')
                ->with('error', 'Não é possível excluir este grupo pois existem produtos associados a ele.');
        }

        $grupo_produto->delete();

        return redirect()->route('grupo_produtos.index')
            ->with('success', 'Grupo de Produtos excluído com sucesso!');
    }
}
