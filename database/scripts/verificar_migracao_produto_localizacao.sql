-- ============================================================================
-- Script de Verificação: Migração Produto_Localizacao
-- Data: 2025-01-16
-- Objetivo: Verificar se a migração foi executada corretamente
-- ============================================================================

-- 1. RESUMO GERAL DA MIGRAÇÃO
SELECT 
    'RESUMO GERAL' as tipo,
    (SELECT COUNT(*) FROM produtos WHERE localizacao_id IS NOT NULL AND deleted_at IS NULL) as produtos_com_localizacao,
    (SELECT COUNT(*) FROM produto_localizacao) as registros_em_produto_localizacao,
    CASE 
        WHEN (SELECT COUNT(*) FROM produtos WHERE localizacao_id IS NOT NULL AND deleted_at IS NULL) = 
             (SELECT COUNT(*) FROM produto_localizacao)
        THEN 'OK - Migração completa'
        ELSE 'ATENÇÃO - Verificar diferenças'
    END as status_migracao;

-- 2. VERIFICAR PRODUTOS SEM CORRESPONDÊNCIA EM PRODUTO_LOCALIZACAO
SELECT 
    'Produtos não migrados' as tipo,
    p.id,
    p.referencia,
    p.descricao,
    p.localizacao_id,
    l.nome_localizacao,
    p.data_prevista_faccao
FROM produtos p
LEFT JOIN localizacoes l ON p.localizacao_id = l.id
LEFT JOIN produto_localizacao pl ON p.id = pl.produto_id AND p.localizacao_id = pl.localizacao_id
WHERE p.localizacao_id IS NOT NULL
  AND p.deleted_at IS NULL
  AND pl.id IS NULL;

-- 3. VERIFICAR DADOS MIGRADOS CORRETAMENTE
SELECT 
    'Dados migrados (amostra)' as tipo,
    p.referencia,
    p.descricao,
    l.nome_localizacao,
    pl.quantidade,
    DATE_FORMAT(pl.data_prevista_faccao, '%d/%m/%Y') as data_prevista_faccao,
    'OK' as status
FROM produto_localizacao pl
INNER JOIN produtos p ON pl.produto_id = p.id
INNER JOIN localizacoes l ON pl.localizacao_id = l.id
ORDER BY p.referencia
LIMIT 20;

-- 4. VERIFICAR PRODUTOS COM MÚLTIPLAS LOCALIZAÇÕES
SELECT 
    'Produtos com múltiplas localizações' as tipo,
    p.referencia,
    p.descricao,
    COUNT(*) as total_localizacoes,
    GROUP_CONCAT(l.nome_localizacao SEPARATOR ', ') as localizacoes
FROM produto_localizacao pl
INNER JOIN produtos p ON pl.produto_id = p.id
INNER JOIN localizacoes l ON pl.localizacao_id = l.id
GROUP BY p.id, p.referencia, p.descricao
HAVING COUNT(*) > 1
ORDER BY total_localizacoes DESC;

-- 5. VERIFICAR QUANTIDADES MIGRADAS
SELECT 
    'Verificação de quantidades' as tipo,
    p.referencia,
    p.quantidade as quantidade_produto,
    pl.quantidade as quantidade_localizacao,
    CASE 
        WHEN p.quantidade = pl.quantidade THEN 'OK'
        ELSE 'DIFERENTE'
    END as status
FROM produto_localizacao pl
INNER JOIN produtos p ON pl.produto_id = p.id
WHERE p.quantidade != pl.quantidade
LIMIT 10;

-- 6. VERIFICAR DATAS DE FACÇÃO MIGRADAS
SELECT 
    'Produtos com data de facção' as tipo,
    COUNT(*) as total_com_data_faccao,
    COUNT(CASE WHEN pl.data_prevista_faccao IS NOT NULL THEN 1 END) as migradas_corretamente
FROM produtos p
INNER JOIN produto_localizacao pl ON p.id = pl.produto_id
WHERE p.data_prevista_faccao IS NOT NULL
  AND p.deleted_at IS NULL;

-- 7. ESTATÍSTICAS POR LOCALIZAÇÃO
SELECT 
    'Produtos por localização' as tipo,
    l.nome_localizacao,
    COUNT(*) as total_produtos,
    SUM(pl.quantidade) as quantidade_total,
    COUNT(CASE WHEN pl.data_prevista_faccao IS NOT NULL THEN 1 END) as com_data_faccao
FROM produto_localizacao pl
INNER JOIN localizacoes l ON pl.localizacao_id = l.id
GROUP BY l.id, l.nome_localizacao
ORDER BY total_produtos DESC;

-- 8. PRODUTOS SEM LOCALIZAÇÃO (NÃO MIGRADOS)
SELECT 
    'Produtos sem localização' as tipo,
    COUNT(*) as total
FROM produtos
WHERE localizacao_id IS NULL
  AND deleted_at IS NULL;

-- ============================================================================
-- INTERPRETAÇÃO DOS RESULTADOS:
-- ============================================================================
-- 1. RESUMO GERAL:
--    - Se status_migracao = 'OK', todos os produtos foram migrados
--    - Se 'ATENÇÃO', verificar query 2 para ver quais faltaram
--
-- 2. Produtos não migrados:
--    - Se retornar registros, há produtos que não foram migrados
--    - Verificar se é intencional ou se há problema
--
-- 3. Produtos com múltiplas localizações:
--    - É esperado estar vazio após a primeira migração
--    - Se houver registros, foram adicionados manualmente
--
-- 4. Verificação de quantidades:
--    - Se retornar registros, há diferenças entre produto e localização
--    - Verificar se é intencional
-- ============================================================================
