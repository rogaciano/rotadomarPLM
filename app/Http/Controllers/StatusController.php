<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = \App\Models\Status::query();

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

        $statuses = $query->orderBy('descricao')->paginate(10);

        return view('status.index', compact('statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('status.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descricao' => 'required|string|max:255|unique:status,descricao',
            'observacoes' => 'nullable|string',
            'ativo' => 'boolean',
            'calc_necessidade' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('status.create')
                ->withErrors($validator)
                ->withInput();
        }

        // Garantir que ativo e calc_necessidade sejam false se não estiverem presentes
        $data = $request->all();
        if (!isset($data['ativo'])) {
            $data['ativo'] = false;
        }
        if (!isset($data['calc_necessidade'])) {
            $data['calc_necessidade'] = false;
        }

        Status::create($data);

        return redirect()->route('status.show', $data['id'])
            ->with('success', 'Status criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $status = Status::withTrashed()->findOrFail($id);
        return view('status.show', compact('status'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $status = Status::findOrFail($id);
        return view('status.edit', compact('status'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $status = Status::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'descricao' => 'required|string|max:255|unique:status,descricao,' . $id,
            'observacoes' => 'nullable|string',
            'ativo' => 'boolean',
            'calc_necessidade' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('status.edit', $status)
                ->withErrors($validator)
                ->withInput();
        }

        // Garantir que ativo e calc_necessidade sejam false se não estiverem presentes
        $data = $request->all();
        if (!isset($data['ativo'])) {
            $data['ativo'] = false;
        }
        if (!isset($data['calc_necessidade'])) {
            $data['calc_necessidade'] = false;
        }

        $status->update($data);

        return redirect()->route('status.show', $status)
            ->with('success', 'Status atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $status = Status::withTrashed()->findOrFail($id);

        if ($status->trashed()) {
            // Restaurar
            $status->restore();
            $message = 'Status restaurado com sucesso!';
        } else {
            // Excluir
            $status->delete();
            $message = 'Status excluído com sucesso!';
        }

        return redirect()->route('status.index')
            ->with('success', $message);
    }
}
