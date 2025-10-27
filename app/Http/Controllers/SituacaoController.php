<?php

namespace App\Http\Controllers;

use App\Models\Situacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SituacaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = \App\Models\Situacao::query();

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

        $situacoes = $query->orderBy('descricao')->paginate(10);

        return view('situacoes.index', compact('situacoes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('situacoes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descricao' => 'required|string|max:255|unique:situacoes,descricao',
            'prazo' => 'nullable|integer|min:0',
            'observacoes' => 'nullable|string',
            'ativo' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('situacoes.create')
                ->withErrors($validator)
                ->withInput();
        }

        // Garantir que ativo seja false se não estiver presente e limpar observações se vazio
        $data = $request->only(['descricao', 'prazo', 'observacoes', 'ativo']);
        $data['ativo'] = $request->has('ativo');
        if (empty(trim($data['observacoes'] ?? ''))) {
            $data['observacoes'] = null;
        }
        // Se prazo for vazio, definir como null
        if (empty($data['prazo'])) {
            $data['prazo'] = null;
        }

        Situacao::create($data);

        return redirect()->route('situacoes.index')
            ->with('success', 'Situação criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $situacao = Situacao::withTrashed()->findOrFail($id);
        return view('situacoes.show', compact('situacao'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $situacao = Situacao::findOrFail($id);
        return view('situacoes.edit', compact('situacao'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $situacao = Situacao::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'descricao' => 'required|string|max:255|unique:situacoes,descricao,' . $id,
            'prazo' => 'nullable|integer|min:0',
            'observacoes' => 'nullable|string',
            'ativo' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('situacoes.edit', $situacao)
                ->withErrors($validator)
                ->withInput();
        }

        // Garantir que ativo seja false se não estiver presente e limpar observações se vazio
        $data = $request->only(['descricao', 'prazo', 'observacoes', 'ativo']);
        $data['ativo'] = $request->has('ativo');
        if (empty(trim($data['observacoes'] ?? ''))) {
            $data['observacoes'] = null;
        }
        // Se prazo for vazio, definir como null
        if (empty($data['prazo'])) {
            $data['prazo'] = null;
        }

        $situacao->update($data);

        return redirect()->route('situacoes.index')
            ->with('success', 'Situação atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $situacao = Situacao::withTrashed()->findOrFail($id);

        if ($situacao->trashed()) {
            // Restaurar
            $situacao->restore();
            $message = 'Situação restaurada com sucesso!';
        } else {
            // Excluir
            $situacao->delete();
            $message = 'Situação excluída com sucesso!';
        }

        return redirect()->route('situacoes.index')
            ->with('success', $message);
    }
}
