-- ====================================================================
-- POPULAR produto_alocacao_mensal A PARTIR DE produto_localizacao
-- ====================================================================
-- Autor: Sistema Rota do Amar
-- Data: 2025-10-16
-- Descrição: Popula a tabela produto_alocacao_mensal com dados de produto_localizacao
-- ====================================================================

-- PASSO 1: Verificar dados disponíveis
-- ====================================================================
SELECT 
    'Total de registros em produto_localizacao' AS descricao,
    COUNT(*) AS total
FROM produto_localizacao
WHERE deleted_at IS NULL

UNION ALL

SELECT 
    'Registros com data_prevista_faccao preenchida',
    COUNT(*)
FROM produto_localizacao
WHERE deleted_at IS NULL 
  AND data_prevista_faccao IS NOT NULL
  
UNION ALL

SELECT 
    'Registros com quantidade > 0',
    COUNT(*)
FROM produto_localizacao
WHERE deleted_at IS NULL 
  AND quantidade > 0
  
UNION ALL

SELECT 
    'Registros prontos para migrar (com data E quantidade)',
    COUNT(*)
FROM produto_localizacao
WHERE deleted_at IS NULL 
  AND data_prevista_faccao IS NOT NULL
  AND quantidade > 0;

-- ====================================================================
-- PASSO 2: Limpar alocações antigas (OPCIONAL - comentar se não quiser)
-- ====================================================================
-- ATENÇÃO: Isso vai deletar TODAS as alocações existentes do tipo 'original'
-- Descomente as linhas abaixo apenas se quiser começar do zero

-- DELETE FROM produto_alocacao_mensal 
-- WHERE tipo = 'original';
-- SELECT 'Alocações antigas removidas' AS status;

-- ====================================================================
-- PASSO 3: Inserir alocações a partir de produto_localizacao
-- ====================================================================
INSERT INTO produto_alocacao_mensal (
    produto_id,
    produto_localizacao_id,
    localizacao_id,
    mes,
    ano,
    quantidade,
    tipo,
    ordem_producao,
    observacoes,
    usuario_id,
    created_at,
    updated_at
)
SELECT 
    pl.produto_id,
    pl.id AS produto_localizacao_id,
    pl.localizacao_id,
    MONTH(pl.data_prevista_faccao) AS mes,
    YEAR(pl.data_prevista_faccao) AS ano,
    pl.quantidade,
    'original' AS tipo,
    pl.ordem_producao,
    CONCAT(
        COALESCE(pl.observacao, 'Migrado de produto_localizacao'),
        ' | Criado em: ',
        DATE_FORMAT(NOW(), '%d/%m/%Y %H:%i')
    ) AS observacoes,
    1 AS usuario_id, -- ID do usuário admin
    NOW() AS created_at,
    NOW() AS updated_at
FROM produto_localizacao pl
WHERE pl.deleted_at IS NULL
  AND pl.data_prevista_faccao IS NOT NULL
  AND pl.quantidade > 0
  -- Evitar duplicatas: só insere se NÃO existir alocação para este produto_localizacao
  AND NOT EXISTS (
      SELECT 1 
      FROM produto_alocacao_mensal pam 
      WHERE pam.produto_localizacao_id = pl.id
  );

-- ====================================================================
-- PASSO 4: Verificar resultado
-- ====================================================================
SELECT 
    'Alocações criadas com sucesso!' AS status,
    COUNT(*) AS total_alocacoes
FROM produto_alocacao_mensal
WHERE DATE(created_at) = CURDATE();

-- ====================================================================
-- PASSO 5: Análise detalhada das alocações criadas
-- ====================================================================
SELECT 
    'Resumo por Localização' AS relatorio,
    l.nome_localizacao,
    COUNT(DISTINCT pam.produto_id) AS total_produtos,
    COUNT(pam.id) AS total_alocacoes,
    SUM(pam.quantidade) AS quantidade_total
FROM produto_alocacao_mensal pam
INNER JOIN localizacoes l ON l.id = pam.localizacao_id
WHERE DATE(pam.created_at) = CURDATE()
GROUP BY l.id, l.nome_localizacao
ORDER BY quantidade_total DESC;

-- ====================================================================
-- PASSO 6: Verificar alocações por mês/ano
-- ====================================================================
SELECT 
    'Alocações por Período' AS relatorio,
    CONCAT(pam.mes, '/', pam.ano) AS periodo,
    COUNT(DISTINCT pam.produto_id) AS total_produtos,
    COUNT(pam.id) AS total_alocacoes,
    SUM(pam.quantidade) AS quantidade_total
FROM produto_alocacao_mensal pam
WHERE DATE(pam.created_at) = CURDATE()
GROUP BY pam.ano, pam.mes
ORDER BY pam.ano, pam.mes;

-- ====================================================================
-- PASSO 7: Produtos com múltiplas alocações (diferentes ordens)
-- ====================================================================
SELECT 
    'Produtos com Múltiplas Ordens' AS relatorio,
    p.referencia,
    p.descricao,
    COUNT(pam.id) AS total_ordens,
    GROUP_CONCAT(DISTINCT pam.ordem_producao ORDER BY pam.ordem_producao SEPARATOR ', ') AS ordens,
    SUM(pam.quantidade) AS quantidade_total
FROM produto_alocacao_mensal pam
INNER JOIN produtos p ON p.id = pam.produto_id
WHERE DATE(pam.created_at) = CURDATE()
GROUP BY p.id, p.referencia, p.descricao
HAVING COUNT(pam.id) > 1
ORDER BY total_ordens DESC, quantidade_total DESC
LIMIT 20;

-- ====================================================================
-- PASSO 8: Verificar se há produto_localizacao sem alocação
-- ====================================================================
SELECT 
    'Registros de produto_localizacao SEM alocação (possíveis problemas)' AS relatorio,
    COUNT(*) AS total
FROM produto_localizacao pl
LEFT JOIN produto_alocacao_mensal pam ON pam.produto_localizacao_id = pl.id
WHERE pl.deleted_at IS NULL
  AND pl.data_prevista_faccao IS NOT NULL
  AND pl.quantidade > 0
  AND pam.id IS NULL;

-- Se houver registros sem alocação, listar detalhes:
SELECT 
    'Detalhes dos registros sem alocação' AS info,
    pl.id AS produto_localizacao_id,
    p.referencia,
    l.nome_localizacao,
    pl.ordem_producao,
    pl.quantidade,
    pl.data_prevista_faccao
FROM produto_localizacao pl
INNER JOIN produtos p ON p.id = pl.produto_id
INNER JOIN localizacoes l ON l.id = pl.localizacao_id
LEFT JOIN produto_alocacao_mensal pam ON pam.produto_localizacao_id = pl.id
WHERE pl.deleted_at IS NULL
  AND pl.data_prevista_faccao IS NOT NULL
  AND pl.quantidade > 0
  AND pam.id IS NULL
ORDER BY pl.data_prevista_faccao
LIMIT 10;

-- ====================================================================
-- FIM DO SCRIPT
-- ====================================================================
-- Resultado esperado:
-- - produto_alocacao_mensal populada com dados de produto_localizacao
-- - Cada registro de produto_localizacao com data e quantidade gera uma alocação
-- - Múltiplas ordens de produção permitidas (mesmo produto/localização/mês)
-- ====================================================================
