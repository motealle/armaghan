<?php

return [
    'customer' => [
        // Stable production entry; numbered snapshots remain immutable.
        'magic_fragment_path' => env('ARMAGHAN_CUSTOMER_MAGIC_FRAGMENT_PATH', '/t/27/#/magic/'),
    ],

    'favorite_share' => [
        'fragment_path' => env('ARMAGHAN_FAVORITE_SHARE_FRAGMENT_PATH', '/#/s/'),
        'guest_ttl_days' => (int) env('ARMAGHAN_FAVORITE_SHARE_GUEST_TTL_DAYS', 7),
        'customer_ttl_days' => (int) env('ARMAGHAN_FAVORITE_SHARE_CUSTOMER_TTL_DAYS', 30),
        'max_products' => (int) env('ARMAGHAN_FAVORITE_SHARE_MAX_PRODUCTS', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | SQLite backup policy
    |--------------------------------------------------------------------------
    |
    | SQLite is the primary Laravel database for the current Armaghan scale.
    | Production should set DB_DATABASE to an absolute private path outside
    | public_html. Consistent SQLite backups are written to the private
    | directory below using SQLite VACUUM INTO.
    |
    | JSON remains a portable import/export format only. It is not a custom
    | database driver and no application service should depend on JSON files
    | as the runtime source of truth.
    |
    */
    'sqlite' => [
        'backup_path' => env('ARMAGHAN_SQLITE_BACKUP_PATH')
            ?: storage_path('app/private/sqlite-backups'),
    ],
];
