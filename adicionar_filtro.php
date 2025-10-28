<?php

// Arquivo MovimentacaoController.php
$controllerPath = __DIR__ . '/app/Http/Controllers/MovimentacaoController.php';
$controllerContent = file_get_contents($controllerPath);

// Adicionar o filtro por status de dias após o filtro de concluido no método index
$pattern = '/\/\/ Adicionar filtro para o campo concluido\s+if \(\$request->filled\(\'concluido\'\)\) \{\s+\$query->where\(\'concluido\', \$request->concluido\);\s+\}\s+\s+\/\/ Ordenação/';
$replacement = "// Adicionar filtro para o campo concluido\n        if (\$request->filled('concluido')) {\n            \$query->where('concluido', \$request->concluido);\n        }\n        \n        // Filtro por status de dias (Atrasados, Em Dia)\n        if (\$request->filled('status_dias')) {\n            \$query = MovimentacaoFilterController::applyStatusDiasFilter(\$query, \$request->status_dias);\n        }\n\n        // Ordenação";

$controllerContent = preg_replace($pattern, $replacement, $controllerContent);

// Adicionar o filtro por status de dias após o filtro de concluido no método generateListPdf
$pattern = '/\/\/ Adicionar filtro para o campo concluido\s+if \(\$request->filled\(\'concluido\'\)\) \{\s+\$query->where\(\'concluido\', \$request->concluido\);\s+\}\s+\s+\/\/ Ordenação/';
$replacement = "// Adicionar filtro para o campo concluido\n        if (\$request->filled('concluido')) {\n            \$query->where('concluido', \$request->concluido);\n        }\n        \n        // Filtro por status de dias (Atrasados, Em Dia)\n        if (\$request->filled('status_dias')) {\n            \$query = MovimentacaoFilterController::applyStatusDiasFilter(\$query, \$request->status_dias);\n        }\n\n        // Ordenação";

$controllerContent = preg_replace($pattern, $replacement, $controllerContent);

// Salvar as alterações
file_put_contents($controllerPath, $controllerContent);

echo "Filtro por status de dias adicionado com sucesso!\n";
