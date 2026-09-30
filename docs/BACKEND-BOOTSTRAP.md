# Armaghan — Laravel 13 / Filament 5 Bootstrap Analysis

Status: implementation batch selected; generated backend must pass CI before merge to `main`.

## Goal

Create a maintainable Laravel 13 backend under `platform/backend` with Filament 5 Panel Builder, without touching frozen Test 26 or publishing Test 27. SQLite remains the zero-credential local/dev/test database; production is configured for MySQL/MariaDB after the verified host preflight found PDO MySQL available and PDO SQLite unavailable.

## Ranked implementation options

| Rank | Method | Score | Why |
|---:|---|---:|---|
| 1 | **Generate Laravel 13 with Composer in GitHub Actions, install Filament 5 there, run framework/package checks and tests, then commit the generated backend to the temporary branch** | **10.0** | Uses the real package resolver and lockfile, avoids hand-copied framework files, is reproducible, and keeps the host untouched |
| 2 | Generate Laravel locally through Remote Desktop, test, then push | 8.4 | Good when the authorized device is online, but current remote device availability is not guaranteed and adds machine-specific state |
| 3 | Manually create Laravel skeleton files through the GitHub API | 4.5 | Avoids CI generation but is highly error-prone and can miss framework defaults or lockfile consistency |
| 4 | Install directly on production hosting first | 3.0 | Tests reality but creates rollback/security risk before repository state is proven |
| 5 | Continue with custom PHP instead of Laravel | 1.0 | Conflicts with the accepted architecture and would recreate auth/admin/validation concerns manually |

**Selected:** option 1.

## Bootstrap boundaries

- Backend root: `platform/backend`.
- Framework: Laravel 13.x.
- Admin framework: Filament 5.x Panel Builder.
- Local/dev/test DB default: SQLite.
- Production DB: MySQL/MariaDB through environment-only credentials.
- No production credentials are committed.
- No `.env` is committed.
- No `vendor/` directory is committed.
- No Test 26/27 files or launcher entries are modified.
- No FTP deployment occurs from the temporary bootstrap branch.
- Production deployment will later build `vendor/` in CI because host web-PHP has shell execution disabled.

## Common failure modes explicitly guarded

1. Hand-writing framework scaffolding instead of using Composer.
2. Resolving incompatible Laravel / Filament major versions.
3. Accidentally committing `.env` or credentials.
4. Making production depend on PDO SQLite after preflight disproved it.
5. Installing on the live host before repository tests pass.
6. Mixing the Laravel backend's Vite assets with the approved Vue customer frontend.
7. Creating an admin user with a hard-coded password in Git.
8. Modifying the frozen numbered UI snapshots during backend bootstrap.

## Required verification before merge

- `composer validate --strict`.
- Laravel reports major version 13.
- Filament reports major version 5.
- Filament Admin panel provider exists.
- `/up` health route exists.
- Laravel default test suite passes.
- Composer security audit reports no known vulnerable locked dependency.
- `.env`, `vendor/`, and generated secret keys are absent from the commit.
- Diff contains only backend bootstrap/docs/workflow changes.


## Bootstrap result

- GitHub Actions Backend Bootstrap Run #1: PASS.
- Resolved Laravel Framework: **13.34.0**.
- Resolved Filament: **5.9.0**.
- Generated backend root: `platform/backend`.
- Filament Admin panel provider generated at `app/Providers/Filament/AdminPanelProvider.php`.
- `/up` health route verified.
- Laravel tests: **2 passed / 2 assertions** at bootstrap.
- Composer locked-dependency security audit: no known vulnerability advisories.
- `.env` and `vendor/` remained ignored and were not committed.
- Test 26, Test 27 and the launcher were not modified.
- Generated nested agent instructions were replaced with Armaghan-specific rules so future tools do not auto-install Laravel Boost or ask the user for avoidable local setup.
- A permanent `Backend CI` workflow now validates every backend change on `main`.
