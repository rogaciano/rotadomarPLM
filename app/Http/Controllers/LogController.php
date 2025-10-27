<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class LogController extends Controller
{
    /**
     * Display a listing of the logs.
     */
    public function index()
    {
        $logPath = storage_path('logs');
        $logFiles = File::files($logPath);
        
        $logsList = [];
        foreach ($logFiles as $file) {
            $logsList[] = [
                'name' => $file->getFilename(),
                'size' => $this->formatSize($file->getSize()),
                'modified' => date('Y-m-d H:i:s', $file->getMTime()),
                'path' => $file->getPathname(),
            ];
        }
        
        // Ordenar por data de modificação (mais recente primeiro)
        usort($logsList, function($a, $b) {
            return strtotime($b['modified']) - strtotime($a['modified']);
        });
        
        return view('logs.index', compact('logsList'));
    }
    
    /**
     * Display the specified log file.
     */
    public function show(Request $request, $filename)
    {
        $logPath = storage_path('logs/' . $filename);
        
        if (!File::exists($logPath)) {
            return redirect()->route('logs.index')
                ->with('error', 'Arquivo de log não encontrado.');
        }
        
        $size = File::size($logPath);
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if ($size > $maxSize) {
            // Para arquivos grandes, mostrar apenas as últimas linhas
            $content = $this->tailFile($logPath, 1000);
            $truncated = true;
        } else {
            $content = File::get($logPath);
            $truncated = false;
        }
        
        return view('logs.show', [
            'filename' => $filename,
            'content' => $content,
            'size' => $this->formatSize($size),
            'modified' => date('Y-m-d H:i:s', File::lastModified($logPath)),
            'truncated' => $truncated
        ]);
    }
    
    /**
     * Download the specified log file.
     */
    public function download($filename)
    {
        $logPath = storage_path('logs/' . $filename);
        
        if (!File::exists($logPath)) {
            return redirect()->route('logs.index')
                ->with('error', 'Arquivo de log não encontrado.');
        }
        
        return response()->download($logPath);
    }
    
    /**
     * Delete the specified log file.
     */
    public function destroy($filename)
    {
        $logPath = storage_path('logs/' . $filename);
        
        if (!File::exists($logPath)) {
            return redirect()->route('logs.index')
                ->with('error', 'Arquivo de log não encontrado.');
        }
        
        File::delete($logPath);
        
        return redirect()->route('logs.index')
            ->with('success', 'Arquivo de log excluído com sucesso.');
    }
    
    /**
     * Format file size to human readable format.
     */
    private function formatSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    /**
     * Read the last n lines of a file.
     */
    private function tailFile($filepath, $lines = 100)
    {
        $file = new \SplFileObject($filepath, 'r');
        $file->seek(PHP_INT_MAX);
        $lastLine = $file->key();
        
        $offset = max(0, $lastLine - $lines);
        $text = "";
        
        $file->seek($offset);
        
        while (!$file->eof()) {
            $text .= $file->fgets();
        }
        
        return $text;
    }
}
