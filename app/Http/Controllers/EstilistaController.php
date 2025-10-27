<?php

namespace App\Http\Controllers;

use App\Models\Estilista;
use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EstilistaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Estilista::with('marca');

        // Filtros
        if ($request->filled('nome_estilista')) {
            $query->where('nome_estilista', 'like', '%' . $request->nome_estilista . '%');
        }
        
        if ($request->filled('marca')) {
            $query->whereHas('marca', function($q) use ($request) {
                $q->where('nome_marca', 'like', '%' . $request->marca . '%');
            });
        }

        if ($request->filled('created_at')) {
            $query->whereDate('created_at', $request->created_at);
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo);
        }

        // Ordenação
        $orderBy = $request->input('order_by', 'nome_estilista');
        $orderDirection = $request->input('order_direction', 'asc');
        $query->orderBy($orderBy, $orderDirection);

        $estilistas = $query->paginate(10);

        return view('estilistas.index', compact('estilistas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $marcas = Marca::orderBy('nome_marca')->get();
        return view('estilistas.create', compact('marcas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Log para depuração
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            \Log::info('Arquivo recebido (store):', [
                'nome' => $file->getClientOriginalName(),
                'mime' => $file->getMimeType(),
                'extensao' => $file->getClientOriginalExtension(),
                'tamanho' => $file->getSize(),
                'tamanho_real' => $file->getSize() / 1024 . ' KB',
                'caminho_temporario' => $file->getPathname(),
                'e_imagem' => (function() use ($file) {
                    try {
                        return is_array(getimagesize($file->getPathname()));
                    } catch (\Exception $e) {
                        return 'Erro: ' . $e->getMessage();
                    }
                })(),
            ]);
        }

        $validator = Validator::make($request->all(), [
            'nome_estilista' => 'required|string|max:255',
            'marca_id' => 'required|exists:marcas,id',
            'suporte_marca' => 'nullable|string|max:255',
            'foto' => 'nullable|file|max:2048', // Removida validação de imagem para teste
            'ativo' => 'boolean',
        ], [
            'foto.file' => 'Ocorreu um erro ao processar o arquivo.',
            'foto.max' => 'O arquivo não pode ser maior que 2MB',
        ]);

        if ($validator->fails()) {
            return redirect()->route('estilistas.create')
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'nome_estilista' => $request->nome_estilista,
            'marca_id' => $request->marca_id,
            'suporte_marca' => $request->suporte_marca,
            'ativo' => $request->has('ativo'),
        ];

        // Processar upload da foto, se fornecida
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            
            // Verificar se o arquivo é uma imagem
            if (!@getimagesize($file->getPathname())) {
                return redirect()->back()
                    ->withErrors(['foto' => 'O arquivo não parece ser uma imagem válida.'])
                    ->withInput();
            }
            
            // Verificar extensão manualmente
            $extensao = strtolower($file->getClientOriginalExtension());
            if (!in_array($extensao, ['jpeg', 'jpg', 'png', 'gif'])) {
                return redirect()->back()
                    ->withErrors(['foto' => 'Apenas arquivos JPEG, JPG, PNG e GIF são permitidos.'])
                    ->withInput();
            }
            
            try {
                $path = $file->store('estilistas', 'public');
                $data['foto'] = $path;
                \Log::info('Arquivo salvo com sucesso:', ['caminho' => $path]);
            } catch (\Exception $e) {
                \Log::error('Erro ao salvar arquivo:', ['erro' => $e->getMessage()]);
                return redirect()->back()
                    ->withErrors(['foto' => 'Erro ao salvar o arquivo. Tente novamente.'])
                    ->withInput();
            }
        }

        $estilista = Estilista::create($data);

        return redirect()->route('estilistas.show', $estilista->id)
            ->with('success', 'Estilista criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $estilista = Estilista::withTrashed()
            ->with(['marca', 'produtos.marca', 'produtos.status', 'produtos.grupoProduto'])
            ->findOrFail($id);
            
        return view('estilistas.show', [
            'estilista' => $estilista,
            'totalProdutos' => $estilista->totalProdutos(),
            'produtosPorMarca' => $estilista->produtosPorMarca(),
            'produtosPorStatus' => $estilista->produtosPorStatus(),
            'produtosPorGrupo' => $estilista->produtosPorGrupo(),
            'produtosPorLocalizacao' => $estilista->produtosPorLocalizacao(),
            'tempoMedioAtivacao' => $estilista->tempoMedioAtivacao(),
            'produtosPorMes' => $estilista->produtosPorMes(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $estilista = Estilista::withTrashed()->findOrFail($id);
        $marcas = Marca::orderBy('nome_marca')->get();
        return view('estilistas.edit', compact('estilista', 'marcas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Log para depuração
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            \Log::info('Arquivo recebido (update):', [
                'nome' => $file->getClientOriginalName(),
                'mime' => $file->getMimeType(),
                'extensao' => $file->getClientOriginalExtension(),
                'tamanho' => $file->getSize(),
                'tamanho_real' => $file->getSize() / 1024 . ' KB',
                'caminho_temporario' => $file->getPathname(),
                'e_imagem' => (function() use ($file) {
                    try {
                        return is_array(getimagesize($file->getPathname()));
                    } catch (\Exception $e) {
                        return 'Erro: ' . $e->getMessage();
                    }
                })(),
            ]);
        }

        $validator = Validator::make($request->all(), [
            'nome_estilista' => 'required|string|max:255',
            'marca_id' => 'required|exists:marcas,id',
            'suporte_marca' => 'nullable|string|max:255',
            'foto' => 'nullable|file|max:2048', // Validação mais simples
            'remover_foto' => 'nullable|boolean',
            'ativo' => 'boolean',
        ], [
            'foto.file' => 'Ocorreu um erro ao processar o arquivo.',
            'foto.max' => 'O arquivo não pode ser maior que 2MB',
        ]);

        if ($validator->fails()) {
            return redirect()->route('estilistas.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        $estilista = Estilista::withTrashed()->findOrFail($id);
        $data = [
            'nome_estilista' => $request->nome_estilista,
            'marca_id' => $request->marca_id,
            'suporte_marca' => $request->suporte_marca,
            'ativo' => $request->has('ativo'),
        ];

        // Verificar se deve remover a foto existente
        if ($request->has('remover_foto') && $request->remover_foto) {
            // Excluir a foto antiga se existir
            if ($estilista->foto && \Storage::disk('public')->exists($estilista->foto)) {
                \Storage::disk('public')->delete($estilista->foto);
            }
            $data['foto'] = null;
        }
        // Se uma nova foto foi enviada
        elseif ($request->hasFile('foto')) {
            $file = $request->file('foto');
            
            // Verificar se o arquivo é uma imagem
            if (!@getimagesize($file->getPathname())) {
                return redirect()->back()
                    ->withErrors(['foto' => 'O arquivo não parece ser uma imagem válida.'])
                    ->withInput();
            }
            
            // Verificar extensão manualmente
            $extensao = strtolower($file->getClientOriginalExtension());
            if (!in_array($extensao, ['jpeg', 'jpg', 'png', 'gif'])) {
                return redirect()->back()
                    ->withErrors(['foto' => 'Apenas arquivos JPEG, JPG, PNG e GIF são permitidos.'])
                    ->withInput();
            }
            
            try {
                // Excluir a foto antiga se existir
                if ($estilista->foto && \Storage::disk('public')->exists($estilista->foto)) {
                    \Storage::disk('public')->delete($estilista->foto);
                }
                
                // Fazer upload da nova foto
                $path = $file->store('estilistas', 'public');
                $data['foto'] = $path;
                \Log::info('Arquivo atualizado com sucesso:', ['caminho' => $path]);
            } catch (\Exception $e) {
                \Log::error('Erro ao atualizar arquivo:', ['erro' => $e->getMessage()]);
                return redirect()->back()
                    ->withErrors(['foto' => 'Erro ao salvar o arquivo. Tente novamente.'])
                    ->withInput();
            }
        }

        $estilista->update($data);

        return redirect()->route('estilistas.show', $estilista->id)
            ->with('success', 'Estilista atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $estilista = Estilista::withTrashed()->findOrFail($id);
        $estilista->delete(); // Soft delete

        return redirect()->route('estilistas.index')
            ->with('success', 'Estilista removido com sucesso.');
    }
}
