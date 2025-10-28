# Verificação e Correção de Alocações Mensais

## 📋 Contexto

Durante o desenvolvimento, foi identificado um bug que impedia a criação automática de registros em `produto_alocacao_mensal` quando uma nova localização era cadastrada em `produto_localizacao`.

### Problemas Identificados

1. **Array `$fillable` incompleto** no model `ProdutoAlocacaoMensal`
   - Colunas `produto_localizacao_id` e `ordem_producao` não estavam no array
   - Laravel ignorava silenciosamente essas colunas durante mass assignment

2. **Migrations pendentes**
   - Migration que adiciona as colunas não havia sido executada
   - Banco de dados estava desatualizado

### Correções Aplicadas

✅ **Model `ProdutoAlocacaoMensal.php`**: Adicionadas colunas ao `$fillable`
```php
protected $fillable = [
    'produto_id',
    'produto_localizacao_id',  // ← ADICIONADO
    'localizacao_id',
    'mes',
    'ano',
    'quantidade',
    'tipo',
    'ordem_producao',          // ← ADICIONADO
    'observacoes',
    'usuario_id'
];
```

✅ **Migrations**: Executadas migrations pendentes para adicionar colunas ao banco

---

## 🔧 Comando de Verificação

Foi criado um comando Artisan para verificar e corrigir inconsistências causadas pelo bug.

### Localização
`app/Console/Commands/VerificarAlocacaoMensal.php`

### Uso

#### 1. Verificar inconsistências (somente relatório)
```bash
php artisan alocacao:verificar
```

Este comando irá:
- ✅ Listar localizações que têm `data_prevista_faccao` mas não têm alocação mensal
- ✅ Listar alocações órfãs (referências a `produto_localizacao` que não existem mais)
- ✅ Exibir relatório detalhado com tabelas

#### 2. Verificar E corrigir automaticamente
```bash
php artisan alocacao:verificar --fix
```

Este comando irá:
- ✅ Exibir o relatório de inconsistências
- ✅ Solicitar confirmação do usuário
- ✅ Criar alocações mensais faltantes
- ✅ Remover alocações órfãs
- ✅ Exibir resumo das correções aplicadas

---

## 📊 Tipos de Inconsistências Detectadas

### 1. Localizações sem Alocação Mensal

**Critério de detecção:**
- Registro em `produto_localizacao` tem:
  - `data_prevista_faccao` preenchida (NOT NULL)
  - `quantidade` maior que 0
- **MAS** não existe registro correspondente em `produto_alocacao_mensal`

**Ação da correção:**
- Cria novo registro em `produto_alocacao_mensal` com:
  - `produto_localizacao_id`: ID do registro origem
  - `mes` e `ano`: Extraídos de `data_prevista_faccao`
  - `quantidade`, `localizacao_id`, `ordem_producao`: Copiados
  - `tipo`: 'original'
  - `observacoes`: "Criado automaticamente pela rotina de verificação em DD/MM/YYYY HH:MM"

### 2. Alocações Órfãs

**Critério de detecção:**
- Registro em `produto_alocacao_mensal` tem:
  - `produto_localizacao_id` preenchido (NOT NULL)
- **MAS** o registro correspondente em `produto_localizacao` não existe mais

**Ação da correção:**
- Remove (soft delete) o registro órfão de `produto_alocacao_mensal`

---

## 📈 Relatório de Exemplo

```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
📊 RELATÓRIO DE INCONSISTÊNCIAS
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

🔴 Localizações sem alocação mensal: 39
+----+---------+-------------+-----------------+------+-------------+---------+
| ID | Produto | Localização | OP              | Qtd  | Data Facção | Mês/Ano |
+----+---------+-------------+-----------------+------+-------------+---------+
| 66 | 6377    | 20507       | sgsdfg          | 620  | 01/10/2025  | 10/2025 |
| 67 | 8143    | 20511       | asdf            | 1232 | 01/10/2025  | 10/2025 |
+----+---------+-------------+-----------------+------+-------------+---------+

🔴 Alocações órfãs (produto_localizacao não existe): 0

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
⚠️  Total de inconsistências: 39
   - 39 localizações sem alocação mensal
   - 0 alocações órfãs
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

---

## ⚠️ Observações Importantes

1. **Backup**: É recomendável fazer backup do banco de dados antes de executar correções automáticas

2. **Ambiente**: Execute primeiro em ambiente de desenvolvimento/staging para validar

3. **Auditoria**: Todas as alocações criadas automaticamente terão:
   - `tipo`: 'original'
   - `observacoes`: Registro da data/hora da criação automática
   - `usuario_id`: 1 (sistema)

4. **Logs**: O Observer já registra logs em `storage/logs/laravel.log`:
   - Alocações criadas
   - Alocações atualizadas
   - Alocações removidas

---

## 🔄 Próximos Passos Recomendados

1. **Executar verificação** para ver quantas inconsistências existem
2. **Revisar relatório** para entender o impacto
3. **Executar correção com --fix** após validação
4. **Monitorar logs** para garantir que novas alocações estão sendo criadas corretamente
5. **Executar comando periodicamente** (mensal/trimestral) como manutenção preventiva

---

## 📝 Histórico de Mudanças

### 2025-10-21
- ✅ Corrigido array `$fillable` no model `ProdutoAlocacaoMensal`
- ✅ Executadas migrations pendentes
- ✅ Criado comando `alocacao:verificar` com opção `--fix`
- ✅ Documentação criada
