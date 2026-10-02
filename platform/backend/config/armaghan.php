<?php

return [
    'customer' => [
        // Test 27 is the active mutable customer UI lane. Production may override
        // these paths without changing application code when the final root UI ships.
        'portal_path' => env('ARMAGHAN_CUSTOMER_PORTAL_PATH', '/t/27/?auth=magic-login#/tracking'),
        'portal_invalid_path' => env('ARMAGHAN_CUSTOMER_PORTAL_INVALID_PATH', '/t/27/?auth=link-invalid#/tracking'),
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
