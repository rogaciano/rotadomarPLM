<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\Response;

class ArquivoController extends Controller
{
    /**
     * Serve um arquivo de rede de forma segura
     *
     * @param Request $request
     * @return Response
     */
    public function servirArquivoRede(Request $request)
    {
        $path = urldecode($request->path);
        
        // Verificar se o caminho existe
        if (!file_exists($path)) {
            abort(404, 'Arquivo não encontrado');
        }

        // Obter o tipo MIME do arquivo
        $mime = mime_content_type($path);
        
        // Se for uma imagem, exibir diretamente
        if (strpos($mime, 'image/') === 0) {
            return response()->file($path);
        }
        
        // Para outros tipos de arquivo, forçar download
        return response()->download($path);
    }
}
