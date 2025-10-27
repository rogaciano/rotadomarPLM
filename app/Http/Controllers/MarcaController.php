<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class MarcaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Marca::query();

        // Filtros
        if ($request->filled('marca')) {
            $query->where('nome_marca', 'like', '%' . $request->marca . '%');
        }

        // Filtro de data_cadastro removido

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo);
        }

        $marcas = $query->orderBy('nome_marca')->paginate(10);
        
        return view('marcas.index', compact('marcas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('marcas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome_marca' => 'required|string|max:255|unique:marcas,nome_marca',
            'ativo' => 'boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cor_fundo' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'cor_fonte' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        if ($validator->fails()) {
            return redirect()->route('marcas.create')
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        // Upload da imagem de logo, se fornecida
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = time() . '_' . preg_replace('/[^A-Za-z0-9\-\.]/', '_', $logo->getClientOriginalName());
            
            // Criar diretório se não existir
            $directory = storage_path('app/public/logos');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            
            // Método alternativo: mover o arquivo diretamente
            $logo->move($directory, $logoName);
            
            // Garantir que o caminho seja salvo corretamente no banco de dados
            $logoPath = 'logos/' . $logoName;
            $data['logo_path'] = $logoPath;
            
            // Debug - registrar no log para verificar
            \Illuminate\Support\Facades\Log::info('Upload de logo: ' . $logoName);
            \Illuminate\Support\Facades\Log::info('Caminho salvo: ' . $logoPath);
        }
        
        // Debug - mostrar todos os dados que serão salvos
        \Illuminate\Support\Facades\Log::info('Dados a serem salvos (create): ' . json_encode($data));
        
        // Criar a marca diretamente
        $marca = new Marca();
        $marca->nome_marca = $data['nome_marca'];
        $marca->ativo = isset($data['ativo']) ? $data['ativo'] : false;
        $marca->cor_fundo = $data['cor_fundo'] ?? null;
        $marca->cor_fonte = $data['cor_fonte'] ?? null;
        
        // Atribuir o caminho da logo explicitamente
        if (isset($data['logo_path'])) {
            $marca->logo_path = $data['logo_path'];
            \Illuminate\Support\Facades\Log::info('Atribuindo logo_path explicitamente (create): ' . $data['logo_path']);
        }
        
        // Salvar a nova marca
        $marca->save();
        
        // Debug - verificar se o caminho foi salvo corretamente
        \Illuminate\Support\Facades\Log::info('Marca criada com ID: ' . $marca->id);
        \Illuminate\Support\Facades\Log::info('Caminho salvo no banco (create): ' . $marca->logo_path);

        return redirect()->route('marcas.index')
            ->with('success', 'Marca criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Marca $marca)
    {
        // Load the brand with its products and related data
        $marca->load(['produtos.estilista', 'produtos.grupoProduto', 'produtos.status']);
        
        // Get statistical data
        $estatisticas = [
            'totalProdutos' => $marca->total_produtos,
            'produtosPorEstilista' => $marca->getProdutosPorEstilista(),
            'produtosPorLocalizacao' => $marca->getProdutosPorLocalizacao(),
            'produtosPorGrupo' => $marca->getProdutosPorGrupo(),
            'produtosPorStatus' => $marca->getProdutosPorStatus()
        ];
        
        return view('marcas.show', [
            'marca' => $marca,
            'estatisticas' => $estatisticas
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Marca $marca)
    {
        return view('marcas.edit', compact('marca'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Marca $marca)
    {
        $validator = Validator::make($request->all(), [
            'nome_marca' => 'required|string|max:255|unique:marcas,nome_marca,' . $marca->id,
            'ativo' => 'boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cor_fundo' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'cor_fonte' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        if ($validator->fails()) {
            return redirect()->route('marcas.edit', $marca)
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        // Upload da nova imagem de logo, se fornecida
        if ($request->hasFile('logo')) {
            // Remover logo anterior, se existir
            if ($marca->logo_path) {
                $oldLogoPath = storage_path('app/public/' . $marca->logo_path);
                if (file_exists($oldLogoPath)) {
                    unlink($oldLogoPath);
                }
            }
            
            $logo = $request->file('logo');
            $logoName = time() . '_' . preg_replace('/[^A-Za-z0-9\-\.]/', '_', $logo->getClientOriginalName());
            
            // Criar diretório se não existir
            $directory = storage_path('app/public/logos');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            
            // Método alternativo: mover o arquivo diretamente
            $logo->move($directory, $logoName);
            
            // Garantir que o caminho seja salvo corretamente no banco de dados
            $logoPath = 'logos/' . $logoName;
            
            // Debug - registrar no log para verificar
            \Illuminate\Support\Facades\Log::info('Upload de logo (update): ' . $logoName);
            \Illuminate\Support\Facades\Log::info('Caminho salvo (update): ' . $logoPath);
            
            // Adicionar o caminho ao array de dados separadamente
            $data['logo_path'] = $logoPath;
        }
        
        // Debug - mostrar todos os dados que serão salvos
        \Illuminate\Support\Facades\Log::info('Dados a serem salvos (update): ' . json_encode($data));
        
        // Atualizar os dados da marca diretamente
        $marca->nome_marca = $data['nome_marca'];
        $marca->ativo = isset($data['ativo']) ? $data['ativo'] : false;
        $marca->cor_fundo = $data['cor_fundo'] ?? null;
        $marca->cor_fonte = $data['cor_fonte'] ?? null;
        
        // Atribuir o caminho da logo explicitamente
        if (isset($data['logo_path'])) {
            $marca->logo_path = $data['logo_path'];
            \Illuminate\Support\Facades\Log::info('Atribuindo logo_path explicitamente: ' . $data['logo_path']);
        }
        
        // Salvar as alterações
        $marca->save();
        
        // Debug - mostrar o ID da marca atualizada
        \Illuminate\Support\Facades\Log::info('Marca atualizada com ID: ' . $marca->id);
        
        // Verificar se o caminho foi salvo corretamente no banco
        $marcaAtualizada = Marca::find($marca->id);
        \Illuminate\Support\Facades\Log::info('Caminho salvo no banco: ' . $marcaAtualizada->logo_path);
        
        // Verificar se o arquivo existe no sistema de arquivos
        $caminhoCompleto = storage_path('app/public/' . $marcaAtualizada->logo_path);
        $arquivoExiste = file_exists($caminhoCompleto);
        \Illuminate\Support\Facades\Log::info('Arquivo existe no sistema: ' . ($arquivoExiste ? 'SIM' : 'NÃO'));
        \Illuminate\Support\Facades\Log::info('Caminho completo: ' . $caminhoCompleto);

        return redirect()->route('marcas.index')
            ->with('success', 'Marca atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Marca $marca)
    {
        $marca->delete();

        return redirect()->route('marcas.index')
            ->with('success', 'Marca excluída com sucesso!');
    }
}
