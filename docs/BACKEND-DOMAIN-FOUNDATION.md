# Armaghan — Backend Domain Foundation

Status: implementation batch for persistent catalog/customer/share foundations and production-safe Filament access.

## Data-model strategy — ranked options

| Rank | Method | Score | Why |
|---:|---|---:|---|
| 1 | **Native Laravel migrations + Eloquent models, preserving relational constraints while using application enums for portable domain values** | **10.0** | Reversible, testable on SQLite and MySQL/MariaDB, idiomatic Laravel, avoids SQLite/MySQL CHECK differences |
| 2 | Translate the legacy SQL draft 1:1 including DB-specific CHECK clauses | 7.5 | Preserves draft closely but creates portability friction between SQLite and MySQL/MariaDB |
| 3 | Execute the legacy `schema.sql` from a Laravel migration | 4.0 | Fast initially but weak rollback/introspection and couples production to SQLite-oriented SQL |
| 4 | Collapse catalog/customer fields into JSON documents | 2.5 | Fewer tables but weak relational integrity, filtering and Filament CRUD ergonomics |
| 5 | Keep browser-local persistence | 0.0 | Does not satisfy production delivery |

**Selected:** option 1.

## Filament access strategy — ranked options

| Rank | Method | Score | Why |
|---:|---|---:|---|
| 1 | **Implement FilamentUser and require `role=admin` plus `active=true`; provision credentials later through runtime secrets, never repository constants** | **10.0** | Explicit production gate, minimal model, no hard-coded credentials, easy to audit |
| 2 | Environment email allowlist | 8.0 | Secure enough for one administrator but less maintainable than an explicit persisted role |
| 3 | Add a full roles/permissions package immediately | 6.5 | Powerful but unnecessary complexity for the MVP single-admin baseline |
| 4 | Allow every authenticated user into Filament | 2.0 | Makes customer accounts potential admin accounts |
| 5 | Seed a fixed admin email/password in Git | 0.0 | Credential exposure and unsafe production bootstrap |

**Selected:** option 1.

## Implemented scope

- Extend users with portable role and active state.
- Explicit production Filament panel access policy on the User model.
- Customer model with optional user account, WhatsApp/company/contact metadata, notes, priority and activation controls.
- Category → Subcategory → Product relational catalog.
- Product availability as an application enum rather than a DB-specific CHECK.
- Specification definitions and per-product specification values.
- Persisted favorites-share record with hashed token plus product membership pivot.
- Magic-link records with hashed token, scope, expiry/use/revoke timestamps.
- Activity-log foundation for later audit actions.
- Model relationships and casts.
- Focused feature tests for schema/relations and admin-panel authorization.

## Deliberate deferrals

- **Product media table is not recreated from the legacy SQL draft.** Project rules select Spatie Media Library as the canonical media model; its package migration will own media storage in the media batch.
- Orders/order timeline remain post-MVP unless required for delivery.
- No production administrator credential is created in Git.
- No production MySQL/MariaDB migration is executed until credentials/server are provisioned.
- Filament Product/Customer Resources are the next admin-core batch.

## Portability / safety rules

- Foreign keys and unique indexes stay in the database.
- Domain state values use PHP backed enums and application validation so SQLite and MySQL/MariaDB behave consistently.
- Token-bearing public links store hashes only.
- Deleting a User nulls optional Customer.user_id; dependent share/spec/link records follow explicit foreign-key actions.


## Validation result

- Pull Request #1 was used as an isolated validation surface before main.
- First CI attempt correctly caught a test that inspected a database default before refreshing the model; production code/migration did not need a duplicated PHP default.
- Backend CI Run #3: **PASS**.
- Tests: **4 passed / 23 assertions**.
- Admin access test: PASS.
- Core schema/relationships test: PASS.
- Locked Composer security audit: no known vulnerability advisories.
- Secret-hygiene checks: PASS.
- No production database migration and no numbered UI mutation occurred.
