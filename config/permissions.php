<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Permissions Bypass
    |--------------------------------------------------------------------------
    |
    | Quando true, qualquer verificação de Gate ou Policy será automaticamente
    | aprovada. Mantenha como true durante o desenvolvimento inicial e defina
    | como false quando quiser aplicar as restrições reais de permissão.
    | É controlado pela variável de ambiente PERMISSIONS_BYPASS para facilitar
    | a alteração sem tocar no código.
    */

    'bypass' => env('PERMISSIONS_BYPASS', true),
];
