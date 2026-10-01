# Armaghan Backend

Laravel 13 + Filament 5 backend for Armaghan.

## Environment policy

- Local/dev/test/production: SQLite is the primary database.
- Production `DB_DATABASE` must be an absolute private path outside `public_html`.
- MySQL/MariaDB is optional later as a logical mirror/export target, not a required live dependency.
- Never commit `.env`, production credentials, or `APP_KEY`.
- The production host serves only the Laravel public surface; application/private files stay outside `public_html`.
- Composer dependencies are built in CI for production deployment because host web-PHP shell functions are disabled.
- Consistent SQLite snapshots are created with `php artisan armaghan:backup-sqlite`; do not make a naive live-file copy.

## Current bootstrap scope

This bootstrap installs Laravel and the Filament panel only. Domain migrations/models/resources, admin-user provisioning, media, public API endpoints, magic links, favorites sharing, and semantic theme settings are separate bounded batches.
