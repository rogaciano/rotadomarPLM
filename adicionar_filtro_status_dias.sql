-- Este script adiciona uma view que pode ser usada para filtrar movimentações por status de dias

-- Criar view para identificar movimentações atrasadas
CREATE OR REPLACE VIEW movimentacoes_status_dias AS
SELECT 
    m.id,
    m.produto_id,
    m.localizacao_id,
    m.situacao_id,
    m.data_entrada,
    m.data_saida,
    l.prazo,
    CASE
        WHEN m.data_saida IS NOT NULL THEN 'concluido'
        WHEN l.prazo IS NULL THEN 'em_dia'
        WHEN DATEDIFF(NOW(), m.data_entrada) > l.prazo THEN 'atrasado'
        ELSE 'em_dia'
    END AS status_dias,
    DATEDIFF(NOW(), m.data_entrada) AS dias_corridos,
    CASE 
        WHEN m.data_saida IS NOT NULL THEN DATEDIFF(m.data_saida, m.data_entrada)
        ELSE DATEDIFF(NOW(), m.data_entrada)
    END AS dias_totais
FROM 
    movimentacoes m
LEFT JOIN 
    localizacoes l ON m.localizacao_id = l.id
WHERE 
    m.deleted_at IS NULL;

-- Exemplo de uso:
-- Para obter movimentações atrasadas:
-- SELECT * FROM movimentacoes_status_dias WHERE status_dias = 'atrasado';
-- 
-- Para obter movimentações em dia:
-- SELECT * FROM movimentacoes_status_dias WHERE status_dias = 'em_dia';
--
-- Para obter movimentações concluídas:
-- SELECT * FROM movimentacoes_status_dias WHERE status_dias = 'concluido';
