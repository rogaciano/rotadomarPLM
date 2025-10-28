# Migração: Produto Localização

## Objetivo
Migrar os campos `localizacao_id` e `data_prevista_faccao` da tabela `produtos` para a tabela `produto_localizacao`, permitindo que cada produto tenha múltiplas localizações com suas respectivas datas de facção.

## Ordem de Execução

### 1. Verificar a estrutura atual
```bash
php artisan migrate:status
```

### 2. Executar as migrations na ordem correta

#### Primeira Migration: Migrar os dados
```bash
php artisan migrate --path=/database/migrations/2025_01_16_094100_migrate_produto_localizacao_data.php
```

**O que faz:**
- Copia todos os produtos que têm `localizacao_id` definido
- Cria registros na tabela `produto_localizacao` com:
  - `produto_id`: ID do produto
  - `localizacao_id`: Localização original do produto
  - `quantidade`: Quantidade total do produto
  - `data_prevista_faccao`: Data prevista de facção original
- Ignora produtos que não têm localização definida
- Evita duplicatas verificando antes de inserir

#### Segunda Migration: Remover campos antigos
```bash
php artisan migrate --path=/database/migrations/2025_01_16_094200_remove_localizacao_fields_from_produtos.php
```

**O que faz:**
- Remove a foreign key de `localizacao_id` (se existir)
- Remove as colunas `localizacao_id` e `data_prevista_faccao` da tabela `produtos`

### 3. Executar todas as migrations de uma vez
```bash
php artisan migrate
```

## Rollback (se necessário)

### Reverter remoção dos campos
```bash
php artisan migrate:rollback --step=1
```

Isso vai:
- Recriar as colunas `localizacao_id` e `data_prevista_faccao` na tabela `produtos`
- Recriar a foreign key

**ATENÇÃO:** Os dados NÃO serão restaurados automaticamente. Você precisará fazer isso manualmente se necessário.

## Verificação Pós-Migração

### Verificar dados migrados
```sql
-- Contar produtos com localização que foram migrados
SELECT COUNT(*) as total_migrados 
FROM produto_localizacao;

-- Verificar produtos que tinham localização
SELECT COUNT(*) as total_produtos_com_localizacao 
FROM produtos 
WHERE localizacao_id IS NOT NULL;

-- Os dois números acima devem ser iguais (antes de remover os campos)
```

### Verificar produtos sem localização
```sql
SELECT id, referencia, descricao 
FROM produtos 
WHERE localizacao_id IS NULL;
```

## Problemas Conhecidos e Soluções

### Se a foreign key não existir
A migration tenta remover a foreign key e captura a exceção se ela não existir. Isso é normal e não causa problemas.

### Se houver produtos duplicados
A migration verifica duplicatas antes de inserir, então não haverá problemas mesmo se executada múltiplas vezes.

### Se precisar restaurar dados
Se você reverteu a migration e precisa restaurar os dados originais:

```sql
-- Copiar de volta para produtos (execute com cuidado!)
UPDATE produtos p
INNER JOIN produto_localizacao pl ON p.id = pl.produto_id
SET 
    p.localizacao_id = pl.localizacao_id,
    p.data_prevista_faccao = pl.data_prevista_faccao
WHERE p.localizacao_id IS NULL;
```

## Testes Recomendados Após Migração

1. ✅ Acessar a tela de visualização de produtos e verificar se as localizações aparecem corretamente
2. ✅ Adicionar uma nova localização a um produto
3. ✅ Remover uma localização de um produto
4. ✅ Criar um novo produto e adicionar localizações
5. ✅ Verificar se as datas de facção estão sendo exibidas corretamente

## Backup

**IMPORTANTE:** Sempre faça um backup do banco de dados antes de executar migrations destrutivas!

```bash
# MySQL
mysqldump -u usuario -p nome_banco > backup_pre_migracao_$(date +%Y%m%d_%H%M%S).sql

# PostgreSQL
pg_dump -U usuario nome_banco > backup_pre_migracao_$(date +%Y%m%d_%H%M%S).sql
```

## Suporte

Se encontrar problemas durante a migração:
1. Verifique os logs do Laravel: `storage/logs/laravel.log`
2. Execute as queries SQL de verificação acima
3. Reverta as migrations se necessário
4. Restaure o backup do banco de dados
