# Armaghan — Filament Admin Core

Status: implementation batch for practical catalog/customer administration and secret-driven first-admin provisioning.

## Filament CRUD strategy — ranked options

| Rank | Method | Score | Why |
|---:|---|---:|---|
| 1 | **Use Filament 5 resource generators against the migrated schema, then customize generated Schema/Table classes** | **10.0** | Version-correct v5 structure, less boilerplate risk, modular resource-specific files, easy future extension |
| 2 | Hand-write all Resource/Page/Schema/Table files | 7.5 | Full control but higher API-version and copy/paste regression risk |
| 3 | One generic admin CRUD page driven by metadata | 5.0 | Less code but weak domain UX and harder validation |
| 4 | Rebuild the existing Vue admin against JSON APIs now | 4.5 | Preserves prototype visuals but costs more before core delivery |
| 5 | Direct database editing | 0.0 | Unsafe and unsuitable for a nontechnical customer |

**Selected:** option 1.

## First-admin provisioning — ranked options

| Rank | Method | Score | Why |
|---:|---|---:|---|
| 1 | **Idempotent Artisan command reading runtime environment secrets; never seed or print the password** | **10.0** | Re-runnable, auditable, no credentials in Git, reusable by automated deployment |
| 2 | Short-lived protected web bootstrap endpoint | 7.0 | Works on shell-restricted hosting but adds temporary web attack surface |
| 3 | Environment email allowlist with separately-created user | 6.5 | Simple gate but still needs another account-creation path |
| 4 | Manual cPanel/SQL user creation | 3.0 | Requires user intervention and is error-prone |
| 5 | Hard-coded admin seed/password | 0.0 | Credential disclosure and unsafe production defaults |

**Selected:** option 1. A later deployment hook may invoke the same account-creation logic without requiring manual SQL/cPanel work.

## Resource scope

- Category.
- Subcategory.
- Product.
- Customer.

Product media is intentionally deferred to the Spatie Media Library batch. Magic links and favorites shares remain domain foundations and get their operational UI when their flows are implemented.

## UX / safety requirements

- Persian-first labels in the admin-facing fields where practical.
- Relationship fields use searchable Selects rather than raw foreign-key IDs.
- Product availability is selected from the domain enum.
- Active state and sort order are directly manageable.
- Customer notes/priority/contact fields are editable.
- Destructive actions require Filament's normal confirmation/action patterns.
- No admin password or production credential is committed.
- Test 26/27 remain untouched.
