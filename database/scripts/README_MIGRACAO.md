# Scripts de Migração - Produto Localização

## 📋 Arquivos Disponíveis

1. **migrar_produto_localizacao.sql** - Script principal de migração
2. **verificar_migracao_produto_localizacao.sql** - Script de verificação

## 🚀 Como Executar

### Opção 1: Via MySQL Client

```bash
# 1. Conectar ao banco
mysql -u usuario -p nome_banco

# 2. Executar o script de migração
source /caminho/completo/database/scripts/migrar_produto_localizacao.sql

# 3. Executar o script de verificação
source /caminho/completo/database/scripts/verificar_migracao_produto_localizacao.sql
```

### Opção 2: Via Linha de Comando

```bash
# 1. Migração
mysql -u usuario -p nome_banco < database/scripts/migrar_produto_localizacao.sql

# 2. Verificação
mysql -u usuario -p nome_banco < database/scripts/verificar_migracao_produto_localizacao.sql
```

### Opção 3: Via phpMyAdmin / MySQL Workbench

1. Abra o arquivo `migrar_produto_localizacao.sql`
2. Copie e cole no console SQL
3. Execute (pode executar passo a passo ou tudo de uma vez)
4. Repita com `verificar_migracao_produto_localizacao.sql`

### Opção 4: Via Laravel Tinker

```bash
php artisan tinker

# No tinker:
DB::unprepared(file_get_contents('database/scripts/migrar_produto_localizacao.sql'));
```

## 📝 O Que o Script Faz

### Script de Migração (`migrar_produto_localizacao.sql`)

**PASSO 1:** Verifica quantos registros serão migrados
```sql
SELECT COUNT(*) FROM produtos WHERE localizacao_id IS NOT NULL...
```

**PASSO 2:** Verifica registros existentes em produto_localizacao

**PASSO 3:** Insere os dados (comando principal)
```sql
INSERT INTO produto_localizacao (produto_id, localizacao_id, quantidade, data_prevista_faccao, ...)
SELECT ...
FROM produtos
WHERE localizacao_id IS NOT NULL
  AND deleted_at IS NULL
  AND NOT EXISTS (evita duplicatas)
```

**PASSO 4-6:** Queries de verificação e validação

### Script de Verificação (`verificar_migracao_produto_localizacao.sql`)

1. ✅ Resumo geral da migração
2. ✅ Produtos não migrados (se houver)
3. ✅ Amostra dos dados migrados
4. ✅ Produtos com múltiplas localizações
5. ✅ Verificação de quantidades
6. ✅ Verificação de datas de facção
7. ✅ Estatísticas por localização
8. ✅ Produtos sem localização

## ⚠️ IMPORTANTE

### Antes de Executar

1. **SEMPRE faça backup do banco de dados!**
```bash
mysqldump -u usuario -p nome_banco > backup_antes_migracao_$(date +%Y%m%d_%H%M%S).sql
```

2. **Execute em ambiente de teste primeiro**

3. **Revise os resultados do PASSO 1** antes de continuar

### Segurança do Script

- ✅ **Idempotente**: Pode ser executado múltiplas vezes sem criar duplicatas
- ✅ **Não destrutivo**: Não altera ou remove dados da tabela `produtos`
- ✅ **Verificação de duplicatas**: Usa `NOT EXISTS` para evitar inserções duplicadas
- ✅ **Ignora deletados**: Só migra produtos ativos (`deleted_at IS NULL`)

### Após a Migração

1. Execute o script de verificação
2. Revise os resultados
3. Se tudo estiver OK, os campos antigos podem ser removidos:
   - `localizacao_id` da tabela `produtos`
   - `data_prevista_faccao` da tabela `produtos`

## 🔍 Interpretando os Resultados

### Resultado Esperado do PASSO 1:
```
total_produtos_com_localizacao: 150
com_data_faccao: 120
```
Significa que 150 produtos têm localização, e 120 deles têm data de facção.

### Resultado Esperado do PASSO 4:
```
status: Migração concluída!
total_registros_criados: 150
```

### Se houver produtos não migrados:
Execute a query do PASSO 6 para ver quais produtos não têm localização.

## 🔄 Rollback (Se Necessário)

Se precisar desfazer a migração:

```sql
-- CUIDADO: Isso remove TODOS os registros de produto_localizacao
-- Use apenas se tiver certeza!
DELETE FROM produto_localizacao;

-- OU deletar apenas os registros migrados (se souber a data):
DELETE FROM produto_localizacao 
WHERE created_at >= 'YYYY-MM-DD HH:MM:SS';
```

## 📊 Queries Úteis Pós-Migração

### Ver todos os produtos com suas localizações:
```sql
SELECT 
    p.referencia,
    p.descricao,
    l.nome_localizacao,
    pl.quantidade,
    pl.data_prevista_faccao
FROM produto_localizacao pl
INNER JOIN produtos p ON pl.produto_id = p.id
INNER JOIN localizacoes l ON pl.localizacao_id = l.id
ORDER BY p.referencia;
```

### Contar produtos por localização:
```sql
SELECT 
    l.nome_localizacao,
    COUNT(*) as total_produtos,
    SUM(pl.quantidade) as quantidade_total
FROM produto_localizacao pl
INNER JOIN localizacoes l ON pl.localizacao_id = l.id
GROUP BY l.id, l.nome_localizacao
ORDER BY total_produtos DESC;
```

## 🆘 Solução de Problemas

### Erro: "Duplicate entry"
- O script já tem proteção contra duplicatas
- Se mesmo assim ocorrer, verifique se há dados inconsistentes

### Erro: "Unknown column"
- Verifique se a tabela `produto_localizacao` existe
- Verifique se as colunas foram criadas corretamente

### Contagem diferente entre produtos e produto_localizacao
- Execute o script de verificação
- Veja a query 2 para identificar quais produtos não foram migrados
- Pode ser intencional (produtos sem localização)

## ✅ Checklist Pós-Migração

- [ ] Backup do banco criado
- [ ] Script de migração executado
- [ ] Script de verificação executado
- [ ] Resultados revisados
- [ ] Testes na aplicação realizados
- [ ] Funcionamento confirmado no ambiente de produção
