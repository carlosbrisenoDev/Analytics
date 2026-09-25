<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Arquitectura y Configuración del Dashboard de Analítica
    |--------------------------------------------------------------------------
    |
    | Define el comportamiento mixto del sistema: integración con microservicios
    | externos y almacenamiento / generación de datos interno.
    |
    */

    'default_mode' => env('ANALYTICS_DEFAULT_MODE', 'mixed'),

    'external_api_url' => env('ANALYTICS_EXTERNAL_API_URL', 'http://127.0.0.1:3000'),

    'external_timeout' => (int) env('ANALYTICS_EXTERNAL_TIMEOUT', 2),

    'enable_remote_sync' => (bool) env('ANALYTICS_ENABLE_REMOTE_SYNC', true),

    'enable_local_storage' => (bool) env('ANALYTICS_ENABLE_LOCAL_STORAGE', true),

    'allowed_modes' => ['external', 'internal', 'mixed'],
];
