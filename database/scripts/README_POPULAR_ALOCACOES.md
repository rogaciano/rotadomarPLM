# 📦 Popular produto_alocacao_mensal a partir de produto_localizacao

## 🎯 Objetivo
Migrar dados de `produto_localizacao` para `produto_alocacao_mensal` para inicializar o sistema de alocações mensais.

## ⚠️ Pré-requisitos

### 1. Executar Migração Primeiro
```bash
php artisan migrate
```
Isso adiciona os campos `produto_localizacao_id` e `ordem_producao` na tabela `produto_alocacao_mensal`.

### 2. Verificar Estrutura
Certifique-se que a tabela `produto_alocacao_mensal` tem os campos:
- `produto_localizacao_id` (bigint, nullable)
- `ordem_producao` (varchar 30, nullable)

## 🚀 Como Executar

### Opção 1: Via MySQL CLI (Recomendado)
```bash
mysql -u seu_usuario -p nome_do_banco < database/scripts/popular_alocacoes_de_produto_localizacao.sql
```

### Opção 2: Via Linha de Comando
```bash
mysql -u seu_usuario -p nome_do_banco -e "$(cat database/scripts/popular_alocacoes_de_produto_localizacao.sql)"
```

### Opção 3: Via phpMyAdmin
1. Acesse phpMyAdmin
2. Selecione o banco de dados
3. Vá na aba "SQL"
4. Cole o conteúdo do arquivo `popular_alocacoes_de_produto_localizacao.sql`
5. Clique em "Executar"

### Opção 4: Via Artisan Tinker
```bash
php artisan tinker
```
Depois execute:
```php
DB::unprepared(file_get_contents('database/scripts/popular_alocacoes_de_produto_localizacao.sql'));
```

## 📊 O Que o Script Faz

### PASSO 1: Verificação Inicial
- Conta registros em `produto_localizacao`
- Mostra quantos têm data prevista
- Mostra quantos têm quantidade > 0
- Mostra quantos estão prontos para migrar

### PASSO 2: Limpeza (Opcional)
- **COMENTADO POR PADRÃO**
- Se descomentar, remove alocações do tipo 'original' existentes
- Use apenas se quiser começar do zero

### PASSO 3: Inserção de Dados
Cria alocações mensais para cada `produto_localizacao` que:
- ✅ Não está deletado (`deleted_at IS NULL`)
- ✅ Tem `data_prevista_faccao` preenchida
- ✅ Tem `quantidade > 0`
- ✅ Ainda não tem alocação criada (evita duplicatas)

**Campos populados:**
- `produto_id` ← de `produto_localizacao`
- `produto_localizacao_id` ← ID do registro origem
- `localizacao_id` ← de `produto_localizacao`
- `mes` ← extraído de `data_prevista_faccao`
- `ano` ← extraído de `data_prevista_faccao`
- `quantidade` ← de `produto_localizacao`
- `tipo` ← 'original' (fixo)
- `ordem_producao` ← de `produto_localizacao`
- `observacoes` ← de `observacao` + data de criação
- `usuario_id` ← 1 (admin)

### PASSOS 4-8: Relatórios
Gera relatórios detalhados sobre:
- Total de alocações criadas
- Resumo por localização
- Alocações por período (mês/ano)
- Produtos com múltiplas ordens de produção
- Registros que não foram migrados (para debug)

## 🔍 Verificações Pós-Execução

### 1. Verificar Total de Alocações
```sql
SELECT COUNT(*) FROM produto_alocacao_mensal;
```

### 2. Verificar Vínculo com produto_localizacao
```sql
SELECT COUNT(*) 
FROM produto_alocacao_mensal 
WHERE produto_localizacao_id IS NOT NULL;
```

### 3. Verificar Ordens de Produção
```sql
SELECT ordem_producao, COUNT(*) as total
FROM produto_alocacao_mensal
WHERE ordem_producao IS NOT NULL
GROUP BY ordem_producao
ORDER BY total DESC;
```

### 4. Listar Alocações Criadas Hoje
```sql
SELECT 
    p.referencia,
    l.nome_localizacao,
    pam.ordem_producao,
    pam.quantidade,
    CONCAT(pam.mes, '/', pam.ano) as periodo
FROM produto_alocacao_mensal pam
JOIN produtos p ON p.id = pam.produto_id
JOIN localizacoes l ON l.id = pam.localizacao_id
WHERE DATE(pam.created_at) = CURDATE()
ORDER BY p.referencia;
```

## ✅ Resultado Esperado

Após execução bem-sucedida:
- ✅ Todas as localizações com data prevista terão alocações
- ✅ Múltiplas ordens do mesmo produto/localização/mês serão preservadas
- ✅ Observer funcionará para novos registros
- ✅ Dashboard de capacidade mostrará dados corretos

## 🔄 Executar Novamente

O script é **idempotente** - pode ser executado múltiplas vezes:
- Usa `NOT EXISTS` para evitar duplicatas
- Só insere registros que ainda não têm alocação
- Seguro para re-executar após correções

## 🐛 Solução de Problemas

### Erro: "Unknown column 'produto_localizacao_id'"
**Solução:** Execute a migração primeiro:
```bash
php artisan migrate
```

### Erro: "Data truncated for column 'ordem_producao'"
**Solução:** Verifique se ordens de produção têm no máximo 30 caracteres.

### Nenhuma alocação foi criada
**Causas possíveis:**
1. Registros já têm alocações (rodar PASSO 8 para verificar)
2. Não há registros com `data_prevista_faccao` preenchida
3. Todos os registros têm `quantidade = 0`

**Verificar:**
```sql
SELECT COUNT(*) FROM produto_localizacao 
WHERE deleted_at IS NULL 
  AND data_prevista_faccao IS NOT NULL 
  AND quantidade > 0;
```

## 📞 Suporte

Se houver problemas:
1. Verifique os relatórios gerados pelo script (PASSOS 4-8)
2. Execute as queries de verificação acima
3. Confira os logs do Laravel: `storage/logs/laravel.log`

## 🎉 Próximos Passos

Após popular a tabela:
1. ✅ Observer já está ativo para novos registros
2. ✅ Dashboard de capacidade funcionará corretamente
3. ✅ Relatórios de alocação terão dados completos
4. ✅ Sistema totalmente operacional
