<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TipoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = \App\Models\Tipo::query();

        // Filtros
        if ($request->filled('descricao')) {
            $query->where('descricao', 'like', '%' . $request->descricao . '%');
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo);
        }
        
        // Incluir excluídos se solicitado
        if ($request->filled('incluir_excluidos')) {
            $query->withTrashed();
        }

        $tipos = $query->orderBy('descricao')->paginate(10);
        
        return view('tipos.index', compact('tipos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tipos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'descricao' => 'required|string|max:255',
            'observacoes' => 'nullable|string',
            'ativo' => 'boolean'
        ]);

        \App\Models\Tipo::create($validated);

        return redirect()->route('tipos.index')
            ->with('success', 'Tipo criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tipo = \App\Models\Tipo::withTrashed()->findOrFail($id);
        return view('tipos.show', compact('tipo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $tipo = \App\Models\Tipo::findOrFail($id);
        return view('tipos.edit', compact('tipo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'descricao' => 'required|string|max:255',
            'observacoes' => 'nullable|string',
            'ativo' => 'boolean'
        ]);

        $tipo = \App\Models\Tipo::findOrFail($id);
        $tipo->update($validated);

        return redirect()->route('tipos.index')
            ->with('success', 'Tipo atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tipo = \App\Models\Tipo::findOrFail($id);
        $tipo->delete();

        return redirect()->route('tipos.index')
            ->with('success', 'Tipo excluído com sucesso!');
    }
    
    /**
     * Restore the specified resource from trash.
     */
    public function restore($id)
    {
        $tipo = \App\Models\Tipo::withTrashed()->findOrFail($id);
        $tipo->restore();

        return redirect()->route('tipos.index')
            ->with('success', 'Tipo restaurado com sucesso!');
    }
    
    /**
     * Permanently delete the specified resource.
     */
    public function forceDelete($id)
    {
        $tipo = \App\Models\Tipo::withTrashed()->findOrFail($id);
        $tipo->forceDelete();

        return redirect()->route('tipos.index')
            ->with('success', 'Tipo removido permanentemente com sucesso!');
    }
}
