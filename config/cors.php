<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Controls which cross-origin requests the browser is allowed to make
    | against this app. Only relevant for a browser-based front-end served
    | from another origin — a native mobile client isn't subject to CORS.
    |
    | Set CORS_ALLOWED_ORIGINS in .env to a comma-separated allowlist
    | (e.g. "https://app.akibashop.ga,https://admin.akibashop.ga"). It
    | defaults to "*" so local development keeps working out of the box.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_filter(
        array_map('trim', explode(',', (string) env('CORS_ALLOWED_ORIGINS', '*')))
    ),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
