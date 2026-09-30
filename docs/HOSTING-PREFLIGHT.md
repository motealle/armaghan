# Armaghan — Backend Hosting Preflight

Status: active P0 preflight. No Laravel installation is authorized until this preflight is recorded.

## Current official requirements

Laravel 13 requires PHP 8.3 or newer and the Ctype, cURL, DOM, Fileinfo, Filter, Hash, Mbstring, OpenSSL, PCRE, PDO, Session, Tokenizer and XML PHP extensions. The web server must route the application through Laravel's `public/index.php`; exposing the application root is unsafe. The Backend MVP currently selects SQLite, so PDO SQLite / SQLite3 and SQLite 3.26+ are checked as project-specific requirements.

Official references:
- https://laravel.com/framework/docs/deployment
- https://laravel.com/framework/docs
- https://laravel.com/framework/docs/database/schema

## Ranked execution options

| Rank | Method | Score | Why |
|---:|---|---:|---|
| 1 | Short-lived PHP runtime probe uploaded by CI, with private sibling read/write test and guaranteed FTP cleanup | 10.0 | Tests the actual production PHP runtime, required extensions, SQLite, HTTPS and private-app layout without installing Laravel |
| 2 | Hosting control-panel inspection plus a manual PHP probe | 8.6 | Strong visibility, but depends on interactive panel access and is harder to reproduce |
| 3 | SSH command-line preflight | 8.2 | Excellent when SSH is available, but shared hosting may not expose it |
| 4 | FTP-only capability inspection | 5.5 | Confirms layout/upload access but cannot prove the PHP runtime or extensions |
| 5 | Rely on hosting-plan documentation | 2.0 | Fast but can differ from the actual account/runtime |

**Selected:** option 1.

## Safety rules for the probe

- Never print the FTP hostname, username, password, full filesystem paths or environment secrets.
- Use a random short-lived probe filename.
- Place a temporary marker in an FTP-created sibling directory outside `public_html` and verify PHP can read/write that private sibling.
- Remove the PHP probe, marker and temporary private directory in a `finally` cleanup path.
- Do not touch `/t/26`, `/t/27`, the launcher, root landing assets or customer data.
- Host-side Composer is not a hard requirement: the preferred deployment path is to build Composer dependencies in CI and upload the built application.
- A PHP/runtime result is authoritative only if the returned probe token matches the current run.

## PASS criteria

1. PHP >= 8.3.
2. All Laravel 13 required PHP extensions are loaded.
3. PDO SQLite and SQLite3 are available.
4. SQLite >= 3.26 and an in-memory read/write operation succeeds.
5. `public_html` is the web document root used by the probe.
6. PHP can read and write a private sibling directory outside `public_html`.
7. The probe is successfully executed through HTTPS.
8. FTP cleanup confirms that all temporary files/directories were removed.

If the canonical web URL cannot be inferred from existing GitHub secrets/variables or the FTP hostname, the outcome is **INCONCLUSIVE**, not a false FAIL; in that case the only missing input is a site URL or hosting-panel route.


## Actual result — 2026-09-30

GitHub Actions Hosting Preflight Run #1 completed successfully as an execution run and cleaned up every temporary hosting object. The environment verdict for the **original SQLite-production plan** is **FAIL**, while the **Laravel host capability** is **PASS with a production database change**.

| Check | Result | Note |
|---|---|---|
| PHP runtime | PASS | PHP 8.3.33 on LiteSpeed |
| Laravel 13 required PHP extensions | PASS | All required extensions loaded |
| HTTPS execution | PASS | Probe executed over HTTPS |
| Web document root | PASS | Probe directory matched the active document root |
| Private app sibling outside `public_html` | PASS | PHP could read and write the temporary private sibling |
| FTP account layout | PASS | `public_html` is below the FTP account root; a private sibling could be created |
| Cleanup | PASS | Probe, marker and temporary private directory removed |
| PDO MySQL | PASS | `mysql` is an available PDO driver |
| SQLite3 extension | PASS | Native SQLite3 extension is loaded |
| PDO SQLite | **FAIL** | `sqlite` is not an available PDO driver |
| Laravel SQLite connection | **FAIL** | Laravel's SQLite database layer requires PDO SQLite, so the current production SQLite plan cannot run |
| Host-side shell functions | Restricted | `proc_open`, `exec` and `shell_exec` are disabled in the web PHP runtime |
| Symlink function | PASS | PHP `symlink()` is available |
| Upload / POST size | PASS | 256M / 256M |
| PHP memory limit | PASS | 512M |
| Max execution time | PASS | 300 seconds |

`open_basedir` is enabled, but the probe proved that the intended private sibling outside `public_html` is still readable and writable by PHP. This is the important practical check for the split deployment layout.

## Production database decision — ranked options

| Rank | Option | Score | Why |
|---:|---|---:|---|
| 1 | **Use SQLite for local/dev/test and MySQL/MariaDB for production from the first Laravel deployment** | **9.9** | PDO MySQL is already available; avoids fighting hosting module configuration; better fit for concurrent admin/customer writes; fully supported by Laravel 13 |
| 2 | Ask hosting/control panel to enable PDO SQLite and keep SQLite in production | 8.8 | Preserves the original plan but depends on a hosting setting we do not control and may regress after PHP profile changes |
| 3 | Move the production app to a VPS/host with PDO SQLite | 6.0 | Solves the module issue but is disproportionate for this MVP |
| 4 | Use the native SQLite3 extension through custom database code | 1.5 | Bypasses Laravel's normal PDO database layer and increases maintenance risk |
| 5 | Replace persistence with JSON/files | 0.5 | Unsafe for concurrent product/customer administration and not an acceptable production database |

**Selected:** option 1.

Laravel 13 officially supports MySQL 5.7+ and MariaDB 10.3+. Production MySQL/MariaDB server version and credentials still need to be provisioned/verified before the first production migration. This is now a deployment prerequisite, not a blocker for creating the Laravel application in the repository.

## Deployment implications

- Keep SQLite as the zero-credential developer/test database.
- Configure production through environment-only `DB_CONNECTION=mysql` and `DB_*` secrets.
- Build Composer dependencies in GitHub Actions; do not depend on Composer or shell commands in web PHP.
- Keep the Laravel application/private files outside `public_html`; publish only the public entry surface.
- Because PHP can create symlinks, the public storage link can be established by a controlled one-time deployment hook if hosting CLI access is unavailable.
- Do not install Laravel into `/t/26` or `/t/27`; numbered UI snapshots remain independent.
