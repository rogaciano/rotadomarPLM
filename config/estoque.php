<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configurações da API de Estoque de Tecidos
    |--------------------------------------------------------------------------
    |
    | Estas configurações são usadas para conectar à API externa de estoque
    | de tecidos. Os valores são obtidos do arquivo .env
    |
    */

    'api_url' => env('ESTOQUE_API_URL', 'https://dapic.webpic.com.br/api/home/estoques'),
    'empresa' => env('ESTOQUE_API_EMPRESA', 'canalpernambuco'),
    'token' => env('ESTOQUE_API_TOKEN', '9EUVDSZKT8zh5uqirzgdPN3WKwWGGd'),
    'armazenador' => env('ESTOQUE_API_ARMAZENADOR', 'Armazenador - Matéria Prima - Tecido'),
];
