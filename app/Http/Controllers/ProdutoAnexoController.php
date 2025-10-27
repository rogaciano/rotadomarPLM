<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\ProdutoAnexo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProdutoAnexoController extends Controller
{
    /**
     * Armazena um novo anexo para o produto.
     */
    public function store(Request $request, $produtoId)
    {
        // Autorização: apenas usuários com permissão de atualização em produtos
        $user = auth()->user();
        if (!$user || !$user->canUpdate('produtos')) {
            abort(403, 'Ação não autorizada.');
        }

        $produto = Produto::findOrFail($produtoId);

        // Log para depuração antes da validação
        if ($request->hasFile('arquivo')) {
            $arquivo = $request->file('arquivo');
            \Log::info('Arquivo recebido antes da validação', [
                'nome_original' => $arquivo->getClientOriginalName(),
                'tamanho' => $arquivo->getSize(),
                'tipo' => $arquivo->getMimeType(),
                'extensao' => $arquivo->getClientOriginalExtension()
            ]);
        }
        
        // Validação inicial
        $validator = Validator::make($request->all(), [
            'descricao' => 'required|string|max:255',
            'arquivo' => 'required|file',
        ], [
            'descricao.required' => 'A descrição do anexo é obrigatória.',
            'arquivo.required' => 'O arquivo é obrigatório.',
        ]);
        
        // Validação de tamanho com limites diferentes para PDF e imagens
        if ($request->hasFile('arquivo')) {
            $arquivo = $request->file('arquivo');
            $extensao = strtolower($arquivo->getClientOriginalExtension());
            $mimeType = $arquivo->getMimeType();
            $tamanhoEmMB = $arquivo->getSize() / 1024 / 1024; // Tamanho em MB
            
            \Log::info('Verificando tamanho do arquivo', [
                'tamanho_mb' => $tamanhoEmMB,
                'extensao' => $extensao,
                'mime_type' => $mimeType
            ]);
            
            // Limite de 1MB para PDFs
            if (($extensao === 'pdf' || strpos($mimeType, 'pdf') !== false) && $tamanhoEmMB > 1) {
                return redirect()->back()
                    ->withErrors(['arquivo' => 'Arquivos PDF não podem ser maiores que 1MB. Seu arquivo tem aproximadamente ' . round($tamanhoEmMB, 2) . 'MB.'])
                    ->withInput();
            }
            
            // Limite de 10MB para outros arquivos
            if ($tamanhoEmMB > 10) {
                return redirect()->back()
                    ->withErrors(['arquivo' => 'O arquivo não pode ser maior que 10MB.'])
                    ->withInput();
            }
        }
        
        // Validação manual do tipo de arquivo
        if ($request->hasFile('arquivo')) {
            $arquivo = $request->file('arquivo');
            $extensao = strtolower($arquivo->getClientOriginalExtension());
            $mimeType = $arquivo->getMimeType();
            
            \Log::info('Verificando tipo de arquivo', [
                'extensao' => $extensao,
                'mime_type' => $mimeType
            ]);
            
            $tiposPermitidos = ['pdf', 'png', 'jpg', 'jpeg'];
            $mimePermitidos = [
                'application/pdf', 'application/x-pdf', 'application/acrobat', 'applications/vnd.pdf',
                'image/png',
                'image/jpeg', 'image/jpg', 'image/pjpeg'
            ];
            
            if (!in_array($extensao, $tiposPermitidos) && !in_array($mimeType, $mimePermitidos)) {
                return redirect()->back()
                    ->withErrors(['arquivo' => 'O arquivo deve ser do tipo: pdf, png, jpg ou jpeg.'])
                    ->withInput();
            }
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($request->hasFile('arquivo')) {
            try {
                $arquivo = $request->file('arquivo');
                
                // Log para depuração
                \Log::info('Iniciando upload de arquivo', [
                    'nome_original' => $arquivo->getClientOriginalName(),
                    'tamanho' => $arquivo->getSize(),
                    'tipo' => $arquivo->getMimeType(),
                    'extensao' => $arquivo->getClientOriginalExtension()
                ]);
                
                $nomeArquivo = time() . '_' . preg_replace('/[^A-Za-z0-9\-\.]/', '_', $arquivo->getClientOriginalName());
                
                // Determinar o tipo de arquivo para armazenar na base de dados
                $tipoArquivo = $arquivo->getClientOriginalExtension();
                
                // Criar diretório se não existir
                $diretorio = 'anexos/produtos/' . $produtoId;
                $directory = storage_path('app/public/' . $diretorio);
                
                // Verificar e criar diretório com tratamento de erros
                if (!file_exists($directory)) {
                    if (!mkdir($directory, 0755, true)) {
                        \Log::error('Falha ao criar diretório', ['diretorio' => $directory]);
                        return redirect()->back()->with('error', 'Não foi possível criar o diretório para armazenar o arquivo.');
                    }
                }
                
                // Verificar permissões do diretório
                if (!is_writable($directory)) {
                    \Log::error('Diretório sem permissão de escrita', ['diretorio' => $directory]);
                    return redirect()->back()->with('error', 'Diretório sem permissão de escrita.');
                }
                
                // Tratamento especial para PDFs
                $extensao = strtolower($arquivo->getClientOriginalExtension());
                $mimeType = $arquivo->getMimeType();
                
                \Log::info('Preparando para armazenar arquivo', [
                    'extensao' => $extensao,
                    'mime_type' => $mimeType,
                    'diretorio' => $diretorio,
                    'nome_arquivo' => $nomeArquivo
                ]);
                
                // Armazenar arquivo
                if ($extensao === 'pdf' || strpos($mimeType, 'pdf') !== false) {
                    \Log::info('Detectado arquivo PDF, usando método alternativo de armazenamento');
                    
                    // Método alternativo para PDFs
                    $caminhoCompleto = storage_path('app/public/' . $diretorio . '/' . $nomeArquivo);
                    $conteudoArquivo = file_get_contents($arquivo->getRealPath());
                    
                    if (file_put_contents($caminhoCompleto, $conteudoArquivo) === false) {
                        \Log::error('Falha ao salvar arquivo PDF usando método alternativo');
                        return redirect()->back()->with('error', 'Falha ao salvar o arquivo PDF.');
                    }
                    
                    $caminhoArquivo = $diretorio . '/' . $nomeArquivo;
                    \Log::info('PDF salvo com sucesso usando método alternativo', ['caminho' => $caminhoArquivo]);
                } else {
                    // Método padrão para outros tipos de arquivo
                    $caminhoArquivo = $arquivo->storeAs($diretorio, $nomeArquivo, 'public');
                }
                
                // Verificar se o arquivo foi armazenado com sucesso
                if (!$caminhoArquivo) {
                    \Log::error('Falha ao armazenar o arquivo');
                    return redirect()->back()->with('error', 'Falha ao armazenar o arquivo.');
                }
                
                \Log::info('Arquivo armazenado com sucesso', ['caminho' => $caminhoArquivo]);
                
                // Criar o registro do anexo
                $anexo = ProdutoAnexo::create([
                    'produto_id' => $produtoId,
                    'descricao' => $request->descricao,
                    'arquivo_path' => $caminhoArquivo,
                    'tipo_arquivo' => $tipoArquivo
                ]);
                
                \Log::info('Registro de anexo criado com sucesso', ['anexo_id' => $anexo->id]);
            } catch (\Exception $e) {
                \Log::error('Erro ao processar upload de arquivo', [
                    'mensagem' => $e->getMessage(),
                    'arquivo' => $e->getFile(),
                    'linha' => $e->getLine()
                ]);
                
                return redirect()->back()->with('error', 'Ocorreu um erro ao processar o arquivo: ' . $e->getMessage());
            }

            return redirect()->route('produtos.show', $produtoId)
                ->with('success', 'Anexo adicionado com sucesso!');
        }

        return redirect()->back()
            ->with('error', 'Ocorreu um erro ao fazer upload do arquivo.');
    }

    /**
     * Remove o anexo especificado.
     */
    public function destroy($id)
    {
        // Autorização: apenas usuários com permissão de atualização em produtos
        $user = auth()->user();
        if (!$user || !$user->canUpdate('produtos')) {
            abort(403, 'Ação não autorizada.');
        }

        $anexo = ProdutoAnexo::findOrFail($id);
        $produtoId = $anexo->produto_id;

        // Excluir o arquivo físico
        if ($anexo->arquivo_path) {
            Storage::disk('public')->delete($anexo->arquivo_path);
        }

        // Excluir o registro do banco de dados
        $anexo->delete();

        // Redirecionar de volta para a página anterior (edit ou show)
        return redirect()->back()
            ->with('success', 'Anexo excluído com sucesso!');
    }
}
