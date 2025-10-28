# Configuração para Corrigir Permissões de Logs Automaticamente

Este documento explica como configurar a correção automática de permissões para os arquivos de log do Laravel.

## Opção 1: Laravel Scheduler (Recomendado)

Esta opção já está configurada no código. O Laravel Scheduler executará o comando `logs:fix-permissions` diariamente à 00:01.

Para que o scheduler funcione, você precisa ter um cron job que execute o comando do Laravel Scheduler a cada minuto:

```bash
* * * * * cd /caminho/para/seu/projeto && php artisan schedule:run >> /dev/null 2>&1
```

## Opção 2: Cron Job Direto

Se preferir não usar o Laravel Scheduler, você pode configurar um cron job diretamente para executar o script shell:

```bash
# Edite o crontab do usuário apropriado (normalmente www-data)
sudo crontab -u www-data -e
```

Adicione a seguinte linha para executar o script diariamente à meia-noite:

```bash
1 0 * * * /caminho/para/seu/projeto/fix-log-permissions.sh >> /caminho/para/seu/projeto/storage/logs/fix-permissions-cron.log 2>&1
```

## Opção 3: Cron Job para Executar o Comando Artisan

Alternativamente, você pode executar o comando Artisan diretamente via cron:

```bash
1 0 * * * cd /caminho/para/seu/projeto && php artisan logs:fix-permissions >> /caminho/para/seu/projeto/storage/logs/fix-permissions-cron.log 2>&1
```

## Verificação

Para verificar se as permissões estão sendo corrigidas corretamente, você pode:

1. Verificar o arquivo de log em `storage/logs/fix-permissions.log`
2. Executar o comando manualmente para teste:
   ```bash
   php artisan logs:fix-permissions
   ```
3. Verificar as permissões dos arquivos de log:
   ```bash
   ls -la storage/logs/
   ```

## Solução de Problemas

Se os problemas de permissão persistirem:

1. Verifique se o usuário que executa o cron job tem permissões suficientes
2. Verifique se o usuário do servidor web (www-data, nginx, etc.) é o proprietário dos arquivos
3. Considere ajustar o proprietário dos diretórios:
   ```bash
   sudo chown -R www-data:www-data storage/
   ```
4. Verifique se o script está sendo executado corretamente através dos logs
