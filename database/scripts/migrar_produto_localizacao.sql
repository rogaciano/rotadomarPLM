-- ============================================================================
-- Script de Migração: Produtos -> Produto_Localizacao
-- Data: 2025-01-16
-- Objetivo: Migrar dados de localizacao_id e data_prevista_faccao 
--           da tabela produtos para produto_localizacao
-- ============================================================================

-- PASSO 1: Verificar quantos registros serão migrados
SELECT 
    COUNT(*) as total_produtos_com_localizacao,
    COUNT(CASE WHEN data_prevista_faccao IS NOT NULL THEN 1 END) as com_data_faccao
FROM produtos
WHERE localizacao_id IS NOT NULL
  AND deleted_at IS NULL;

-- PASSO 2: Verificar se já existem registros na produto_localizacao
SELECT 
    COUNT(*) as registros_existentes
FROM produto_localizacao;

-- PASSO 3: Inserir os dados na tabela produto_localizacao
-- (Ignora duplicatas caso o script seja executado mais de uma vez)
INSERT INTO produto_localizacao (produto_id, localizacao_id, quantidade, data_prevista_faccao, created_at, updated_at)
SELECT 
    p.id as produto_id,
    p.localizacao_id,
    COALESCE(p.quantidade, 0) as quantidade,
    p.data_prevista_faccao,
    NOW() as created_at,
    NOW() as updated_at
FROM produtos p
WHERE p.localizacao_id IS NOT NULL
  AND p.deleted_at IS NULL
  AND NOT EXISTS (
      -- Evita duplicatas
      SELECT 1 
      FROM produto_localizacao pl 
      WHERE pl.produto_id = p.id 
        AND pl.localizacao_id = p.localizacao_id
  );

-- PASSO 4: Verificar o resultado da migração
SELECT 
    'Migração concluída!' as status,
    COUNT(*) as total_registros_criados
FROM produto_localizacao;

-- PASSO 5: Validar os dados migrados
SELECT 
    p.id,
    p.referencia,
    p.descricao,
    pl.localizacao_id,
    l.nome_localizacao,
    pl.quantidade,
    pl.data_prevista_faccao
FROM produtos p
INNER JOIN produto_localizacao pl ON p.id = pl.produto_id
INNER JOIN localizacoes l ON pl.localizacao_id = l.id
ORDER BY p.referencia
LIMIT 10;

-- PASSO 6 (OPCIONAL): Verificar produtos que NÃO foram migrados
-- (produtos sem localização definida)
SELECT 
    id,
    referencia,
    descricao,
    'Sem localização definida' as motivo
FROM produtos
WHERE localizacao_id IS NULL
  AND deleted_at IS NULL
ORDER BY referencia
LIMIT 10;

-- ============================================================================
-- NOTAS IMPORTANTES:
-- ============================================================================
-- 1. Este script pode ser executado múltiplas vezes sem criar duplicatas
-- 2. Só migra produtos com localizacao_id definido
-- 3. Ignora produtos deletados (deleted_at IS NOT NULL)
-- 4. A quantidade vem do campo quantidade da tabela produtos
-- 5. Após a migração, você ainda terá os campos antigos em produtos
--    (eles serão removidos em uma migration separada)
-- ============================================================================
