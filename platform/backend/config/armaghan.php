<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Domain persistence
    |--------------------------------------------------------------------------
    |
    | Customer/catalog/content data starts with the JSON driver. Security-
    | critical Laravel control-plane data (authentication, sessions, audit,
    | published style-profile history) remains on Laravel's database layer.
    |
    | The domain-store contract is intentionally driver-neutral so SQLite,
    | MySQL/MariaDB or another repository can replace JSON later without
    | coupling UI/business services to flat-file details.
    |
    */
    'domain_store' => [
        'driver' => env('ARMAGHAN_DOMAIN_STORE', 'json'),

        'json' => [
            'path' => env('ARMAGHAN_JSON_STORE_PATH')
                ?: storage_path('app/private/armaghan-domain'),
        ],
    ],
];
