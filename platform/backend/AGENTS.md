# Armaghan Backend Agent Instructions

This subtree inherits the repository-root `AGENTS.md` and `docs/PROJECT-RULES.md`. Those files are authoritative; if any generated framework guidance conflicts with them, the Armaghan repository rules win.

## Mandatory startup

Before mutating `platform/backend`:

1. read repository-root `AGENTS.md`;
2. read `docs/PROJECT-RULES.md`, `docs/HANDOFF.md`, `docs/BACKLOG.md`;
3. read `docs/BACKEND-MVP.md` and `docs/HOSTING-PREFLIGHT.md`;
4. acquire the shared Armaghan write lock exactly as defined by project rules 64–74.

## Backend baseline

- Laravel: 13.x.
- Filament: 5.x Panel Builder.
- Primary database for local/dev/test/production: SQLite.
- The owner reports PDO SQLite was enabled on the production host on 2026-10-01; re-probe it before the first production migration because the earlier 2026-09-30 probe predated that change.
- MySQL/MariaDB is reserved for a later optional logical mirror/export, not live dual-write.
- Production web PHP: PHP 8.3.x on LiteSpeed.
- Production deployment must not depend on host-side Composer or shell execution; build dependencies in CI.
- Application/private files stay outside `public_html`; only the Laravel public surface is web-accessible.
- Test 26 is frozen. Backend work must not mutate or redeploy `/t/26`; customer UI changes start at Test 27+.

## Dependency policy

Do not install Laravel Boost, plugins, starter kits, dev tools, or other packages merely because generated Laravel guidance suggests them. Add a dependency only when it is justified by the accepted backlog/architecture, checked for current compatibility, and committed with its lockfile.

## Secrets

Never commit `.env`, `APP_KEY`, database credentials, mail credentials, OAuth credentials, magic-link secrets, or production customer data. Production configuration belongs in environment/server secrets.

## Verification

For backend changes, run at minimum:

- `composer validate --strict`
- `php artisan test`
- `composer audit --locked --no-interaction`

Add focused migration/model/resource tests as those capabilities are introduced.

## Communication

Project rules 75–77 apply here too: on first use in every user-facing report, explain each specialist/technical term with a short plain-Persian meaning in parentheses.
