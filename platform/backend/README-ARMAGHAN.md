# Armaghan Backend

Laravel 13 + Filament 5 backend for Armaghan.

## Environment policy

- Local/dev/test: SQLite by default.
- Production: MySQL/MariaDB via environment-only credentials.
- Never commit `.env`, production credentials, or `APP_KEY`.
- The production host serves only the Laravel public surface; application/private files stay outside `public_html`.
- Composer dependencies are built in CI for production deployment because host web-PHP shell functions are disabled.

## Current bootstrap scope

This bootstrap installs Laravel and the Filament panel only. Domain migrations/models/resources, admin-user provisioning, media, public API endpoints, magic links, favorites sharing, and semantic theme settings are separate bounded batches.
