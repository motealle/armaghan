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
