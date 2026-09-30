# Armaghan Backend MVP

Status: implementation active; hosting preflight and Laravel/Filament bootstrap complete; Test 26 frozen.
Frozen source snapshot: `snapshot/test26-final` at `c4f5f507f411138b124cc6feb3bec5ecef1fdc71`.

## Verified current state

- Laravel application: **installed** under `platform/backend`; resolved framework version at bootstrap: **13.34.0**.
- Backend Composer project: **present** with committed `composer.json` and `composer.lock`.
- Artisan / Eloquent / Laravel routes: **present**.
- Filament Panel Builder: **installed**, resolved version **5.9.0**; Admin panel provider exists at `app/Providers/Filament/AdminPanelProvider.php`.
- SQLite model draft: present in `platform/database/schema.sql`.
- Demo SQL seed: present in `platform/database/seed_demo.sql`.
- SQLite bootstrap helper: present in `platform/scripts/bootstrap_sqlite.py`.
- Product/customer/admin UX: present as Vue prototype, but persistence is browser-local.
- Favorites sharing: present as a frontend URL containing validated product codes.
- Theme palette: present as five-color sets mapped in code to `--c-primary`, `--c-secondary`, `--c-soft`, `--c-paper`, `--c-accent`.

## Backend architecture — ranked options

| Rank | Option | Score | Rationale |
|---:|---|---:|---|
| 1 | **Laravel 13 backend + Filament 5 admin + narrow same-origin JSON endpoints; preserve the approved Vue frontend and wire it incrementally in Test 27+** | **9.8** | Fastest route to real persistence/admin without rewriting the approved UI |
| 2 | Laravel 13 + Inertia Vue migration immediately | 8.4 | Cohesive long-term stack, but creates avoidable UI/routing migration work before delivery |
| 3 | Laravel 13 + Livewire customer UI + Filament | 6.5 | Fast backend screens but discards too much approved Vue work |
| 4 | External backend/BaaS | 5.0 | Could be fast, but diverges from repository rules and hosting plan |
| 5 | Custom plain PHP backend | 3.0 | Small initial footprint but worse auth, validation and maintainability |

**Selected:** Option 1.

## Product and customer administration — ranked options

| Rank | Option | Score | Rationale |
|---:|---|---:|---|
| 1 | **Filament Resources for Product, Customer, Category and minimal Site Settings** | **9.9** | CRUD tables/forms/search/filter/upload with minimal custom admin code |
| 2 | Rebuild the current Vue Admin against Laravel APIs | 7.1 | Preserves prototype visuals but costs more time and QA |
| 3 | Hybrid: Filament CRUD + current Vue admin | 6.9 | Two admin surfaces are confusing for handoff |
| 4 | Direct database editor | 2.5 | Unsafe for a nontechnical customer |
| 5 | CSV/Excel-only administration | 2.0 | Poor media/customer workflow and weak validation |

**Selected:** Option 1.

### MVP Product fields
Code, names/translations, category/subcategory, availability, active/archive, sort order, primary image/gallery and essential specification fields.

### MVP Customer fields
Name/company, country, WhatsApp, email, notes, priority/status, active flag and access-link controls.

## Simplest customer login — ranked options

| Rank | Option | Score | Rationale |
|---:|---|---:|---|
| 1 | **One-tap Magic Link sent by the administrator through WhatsApp** | **9.9** | No username/password to remember; fits the actual sales channel; administrator can regenerate/revoke |
| 2 | WhatsApp/SMS OTP | 8.0 | Familiar, but needs an external provider, API cost and delivery reliability |
| 3 | Google Socialite | 7.2 | Secure for Google users, but not universal and adds OAuth setup |
| 4 | Email + password | 5.5 | Standard but higher friction for this audience |
| 5 | No customer login | 4.0 | Simplest technically, but blocks private history/status later |

**Selected:** Option 1.

### Magic-link security contract
- URL contains a high-entropy random token.
- Database stores only its hash.
- Link has scope, expiry, last-used timestamp, revoke/regenerate and audit metadata.
- Opening a valid link creates a normal secure Laravel session.
- A trusted-device option may reduce repeated logins.
- Password and Google login remain optional later.

## Favorites list + WhatsApp — ranked options

| Rank | Option | Score | Rationale |
|---:|---|---:|---|
| 1 | **Persist a share record and expose a short random URL; WhatsApp button sends preview + that URL** | **9.9** | Short, stable, anonymous, works without login and survives larger lists |
| 2 | Keep current product-code list encoded directly in URL | 7.8 | Already works, but links grow and cannot be centrally revoked/managed |
| 3 | Require login and share account favorites | 5.4 | Too much friction for a sales lead |
| 4 | Send only product codes in WhatsApp text | 4.5 | Recipient loses a browsable list |
| 5 | Generate PDF for every share | 2.8 | Slow and unnecessary for MVP |

**Selected:** Option 1.

Suggested `favorite_shares`: token hash, optional customer id, product membership, created/expiry/revoked timestamps.

## Brand color control — ranked options

| Rank | Option | Score | Rationale |
|---:|---|---:|---|
| 1 | **Semantic Token Mapping: keep the customer's five-color set, then let Admin assign those colors to major roles such as header, primary CTA, secondary CTA, highlight, active state and soft surface** | **9.9** | Gives the requested 'where is my yellow?' control without arbitrary CSS chaos |
| 2 | Free color picker on every element | 5.5 | Maximum freedom but easy to create unreadable/inconsistent UI |
| 3 | Current fixed five-color mapping only | 5.0 | Safe but does not satisfy role rotation |
| 4 | Several fixed themes only | 4.5 | Easy to support, but restrictive |
| 5 | Raw CSS editor | 1.0 | High regression/security/maintenance risk |

**Selected:** Option 1.

### Minimum semantic roles
- `brand_primary`
- `header_background`
- `primary_action`
- `secondary_action`
- `highlight_accent`
- `active_navigation`
- `soft_surface`
- `link_accent`

Each role selects one palette slot. Text/icon foreground must be calculated or validated for contrast. Admin gets live preview + reset-to-approved-default.

This is configuration-driven theming, not a Feature Flag. A Feature Flag switches a capability/behavior on or off; token mapping changes values used by the same feature.

## Delivery-minimum scope

### P0 — must ship
1. Hosting/PHP preflight.
2. Laravel 13 + SQLite for local/dev/test + MySQL/MariaDB for production + migrations/models/seed.
3. Filament admin login.
4. Product CRUD + image upload.
5. Customer CRUD + notes/status.
6. Public catalog/settings endpoints.
7. Short favorites-share link + WhatsApp handoff.
8. One-tap customer Magic Link.
9. Semantic color-role settings.
10. Backup/health/audit basics.
11. Test 27+ frontend wiring and end-to-end QA.

### P1 — do after delivery unless required
- Google Socialite.
- Full order/timeline workflow.
- Advanced wishlist lead analytics.
- Queued image conversion pipeline.
- Complex role/permission matrix.
- Automated WhatsApp Business API.

## Main challenges and mitigations

| Challenge | Risk | MVP mitigation |
|---|---|---|
| Shared hosting requirements | Laravel 13 needs PHP 8.3+ and safe public document root | Verified: PHP 8.3.33, required extensions, HTTPS and private sibling layout pass; build dependencies in CI |
| Current data is browser-local | Admin edits currently affect only one browser | Database becomes source of truth; one controlled seed/import path |
| Media uploads | Permissions, oversized files, stale paths | Laravel filesystem + validated upload limits; no DB BLOBs |
| Passwordless login | Token leakage/reuse | Hash tokens, expire/revoke, HTTPS, audit, secure session |
| Favorites links | Removed products or huge lists | Persist share record; gracefully skip archived products; compact token |
| Color freedom | Low contrast / broken branding | Semantic roles only, contrast guard, preview and reset |
| Frozen Test 26 | Backend wiring could alter approved prototype | Never modify `/t/26`; wire production/Test 27+ only |

## Safe implementation batches

1. **COMPLETE** — Backend bootstrap: host preflight, Laravel 13, SQLite local/dev/test, MySQL/MariaDB production configuration contract, Filament 5 panel, health route and permanent backend CI.
2. Data model: migrations/models/seed from approved SQL draft.
3. Admin core: Filament + admin user + Product/Customer resources.
4. Media + public reads: product images and catalog/settings endpoints.
5. Share flow: favorites share token + public list + WhatsApp preview.
6. Customer access: Magic Link + session + minimal customer landing/status.
7. Brand settings: semantic token-role mapping + contrast validation.
8. Frontend integration: Test 27+ reads backend while Test 26 stays frozen.
9. Delivery hardening: backup, logs, recovery test, end-to-end QA and deployment.

Each implementation run should remain bounded, reversible and committed separately.

## Verified hosting preflight — 2026-09-30

- PHP 8.3.33 / LiteSpeed: PASS.
- All Laravel 13 required PHP extensions: PASS.
- HTTPS execution: PASS.
- `public_html` document root and private sibling read/write: PASS.
- PDO MySQL: PASS.
- SQLite3 extension: present, but PDO SQLite: **absent**.
- Therefore production SQLite is rejected for this host; SQLite remains local/dev/test only.
- Production database is promoted to P0 MySQL/MariaDB. Laravel 13 supports MySQL 5.7+ and MariaDB 10.3+; actual server version and credentials must be provisioned before the first production migration.
- Web-PHP shell functions `proc_open`, `exec`, and `shell_exec` are disabled. Composer/vendor builds must happen in CI.
- PHP `symlink()` is available.
- Limits: upload 256M, POST 256M, memory 512M, execution 300s.
- Temporary probe cleanup: PASS.


## Bootstrap delivery record — 2026-09-30

- Generated by Composer in isolated GitHub Actions rather than by manually copying framework files.
- Laravel Framework: **13.34.0**.
- Filament: **5.9.0**.
- Backend root: `platform/backend`.
- Bootstrap test suite: 2/2 tests passed.
- Locked dependency security audit: no known vulnerability advisories.
- Permanent `.github/workflows/backend-ci.yml` now validates backend changes on `main`.
- Local/dev/test remains SQLite; production remains MySQL/MariaDB.
- Generated framework agent instructions were replaced with Armaghan-specific rules; Laravel Boost is not auto-installed.
- No numbered UI snapshot was changed or deployed.
- Next implementation batch: domain data model and production-safe admin access foundation.
