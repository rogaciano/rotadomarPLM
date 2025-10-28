-- Este script cria uma view para facilitar a filtragem de movimentações por status de dias

-- Criar view para identificar movimentações atrasadas e em dia
CREATE OR REPLACE VIEW vw_movimentacoes_status_dias AS
SELECT 
    m.id,
    m.produto_id,
    m.localizacao_id,
    m.situacao_id,
    m.tipo_id,
    m.data_entrada,
    m.data_saida,
    m.data_devolucao,
    m.concluido,
    m.comprometido,
    m.observacao,
    m.created_at,
    m.updated_at,
    m.deleted_at,
    m.anexo,
    l.prazo,
    CASE
        WHEN m.data_saida IS NOT NULL THEN 'concluido'
        WHEN l.prazo IS NULL THEN 'em_dia'
        WHEN DATEDIFF(NOW(), m.data_entrada) > l.prazo THEN 'atrasados'
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

-- Criar stored procedure para filtrar movimentações por status de dias
DELIMITER //

CREATE PROCEDURE sp_filtrar_movimentacoes_por_status_dias(
    IN p_status_dias VARCHAR(50)
)
BEGIN
    IF p_status_dias = 'atrasados' THEN
        SELECT * FROM vw_movimentacoes_status_dias WHERE status_dias = 'atrasados';
    ELSEIF p_status_dias = 'em_dia' THEN
        SELECT * FROM vw_movimentacoes_status_dias WHERE status_dias = 'em_dia';
    ELSE
        SELECT * FROM vw_movimentacoes_status_dias;
    END IF;
END //

DELIMITER ;

-- Exemplo de uso:
-- CALL sp_filtrar_movimentacoes_por_status_dias('atrasados');
-- CALL sp_filtrar_movimentacoes_por_status_dias('em_dia');
-- CALL sp_filtrar_movimentacoes_por_status_dias(NULL); -- Retorna todas
