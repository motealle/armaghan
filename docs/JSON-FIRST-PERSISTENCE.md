# Armaghan — JSON-first persistence and remaining delivery plan

Status: architecture selected; first JSON domain-store foundation batch implemented on a dedicated branch.

## Persistence options — ranked

| Rank | Method | Score | Decision |
|---:|---|---:|---|
| 1 | **JSON-first domain store behind Laravel repository/contracts; keep security/control-plane state relational** | **9.7** | Selected. Best balance for ~700 low-churn records, easy backup/export, low hosting friction and clean later migration. |
| 2 | SQLite for all domain data | 8.7 | Excellent technically for one-server workloads, transactions and queryability, but the verified production host does not expose PDO SQLite. Keep as local/dev/test and potential future option if hosting changes. |
| 3 | MySQL/MariaDB now | 8.4 | Most conventional Laravel production path and already supported by the host; stronger for concurrent writes/relations, but heavier than needed for the current catalog/content scale. Keep for security/control-plane and future domain cutover. |
| 4 | Supabase Postgres as the primary persistence platform | 7.2 | Strong hosted Postgres/Auth/Storage/Realtime platform, but adds an external backend and overlaps with Laravel/Filament today. Valuable if multi-admin realtime/collaboration, external Postgres or managed media/auth becomes important. |
| 5 | Flat JSON directly inside controllers/models without a repository boundary | 3.0 | Fast initially but tightly couples business logic to file layout, makes concurrency/migration/testing harder and creates long-term debt. Rejected. |

## Selected boundary

JSON is the default **domain-data driver** for the near-term MVP, not a replacement for every Laravel persistence concern.

Good JSON-first candidates:
- categories/subcategories;
- product catalog and product specification content;
- customer business/contact profile data when low-write;
- site/content settings;
- portable visual-editor profile exports/imports.

Keep relational:
- Laravel administrator/user authentication;
- sessions and password-reset state;
- magic-link token lifecycle;
- immutable Style Profile publication history;
- audit/activity log;
- any future order/payment/transactional workflow.

This lets the user-visible catalog remain simple and portable while avoiding a custom file-based authentication/security system.

## Why ~700 records is a good JSON range here

Record count alone is not the main constraint. The important characteristics are:
- low concurrent write rate;
- mostly full-list/filter reads;
- small enough collection documents to parse in memory;
- one-server shared-host deployment;
- desire for simple export/backup/version portability.

A few megabytes of JSON is operationally modest. The implementation still uses:
- private storage outside the public web root;
- exclusive file lock around read-modify-write;
- atomic file replacement;
- revision number;
- checksum validation;
- stale-write conflict rejection.

## Supabase relation to Armaghan

Supabase is not a JSON database. Each project provides a full PostgreSQL database, with Auth, Storage, Realtime and API capabilities around it.

Possible future uses:
- replace hosted MySQL with managed PostgreSQL;
- Supabase Storage for product media;
- Supabase Auth for OTP/magic-link/social login;
- Realtime for true multi-admin collaborative visual editing;
- external API access with PostgreSQL Row Level Security.

Why it is not selected now:
- Laravel + Filament already provide the application/admin boundary;
- adopting Supabase Auth would duplicate or replace existing Laravel auth work;
- Realtime is not currently required;
- a second backend/control plane increases deployment and debugging surface;
- the current ~700-record domain does not justify that operational jump.

A future migration remains straightforward because Laravel already supports PostgreSQL connections. Supabase should be treated as an optional future infrastructure target, not mixed into the MVP without a concrete need.

## First JSON foundation batch

Implemented:
- `DomainDocumentStore` contract;
- `JsonDomainDocumentStore` implementation;
- JSON driver is default through `config/armaghan.php`;
- private default path: `storage/app/private/armaghan-domain`;
- production path can be overridden with `ARMAGHAN_JSON_STORE_PATH`;
- atomic `Filesystem::replace()` writes;
- exclusive `flock()` writer lock;
- document schema version;
- per-collection revision;
- checksum validation;
- expected-revision conflict rejection;
- Unicode-preserving JSON output;
- collection-name validation;
- focused tests.

No existing Product/Customer code is switched to JSON in this first batch. That migration is deliberately separate.

## Remaining project distance

Current rough delivery state:
- Customer-facing UI/Test 27 foundation: ~80%.
- Visual editor MVP: ~75%.
- Backend/domain foundation: ~55%.
- Production admin/content workflow: ~40%.
- Production deployment/data/bootstrap hardening: ~45%.
- Overall minimum viable handoff: approximately **60–65% complete**.

The percentage is approximate because customer UI feedback can add Test 28+ batches.

### Remaining safe batches

1. **JSON domain foundation** — current batch.
2. **Catalog JSON repository/import** — move Category/Subcategory/Product/spec reads+writes behind domain repositories and import current data.
3. **Customer JSON repository/import** — customer business/profile data behind JSON repository while keeping account/auth state relational.
4. **Filament catalog/customer admin** — resources consume repository-backed services; image/media remains its own controlled subsystem.
5. **Visual editor persistence adapter** — Test 27 loads/saves Laravel Style Profiles with local fallback and checksum conflict handling.
6. **Visual editor structured controls** — organized ON/OFF for panels/sections/sentences/headings plus text/background/border/token controls; inherited/section controls handled explicitly.
7. **Visual editor history/publish** — staging publish, version history, restore and profile portability.
8. **Favorites/WhatsApp share flow** — persisted share URLs and safe public read flow.
9. **Customer magic-link access** — one-tap customer login and minimal customer view.
10. **Production persistence/bootstrap** — private JSON path, backup/health, production relational control-plane connection, initial admin provisioning.
11. **End-to-end delivery QA** — mobile/tablet/desktop, RTL/LTR, dark/light, permission boundary, backup/restore and deployment.
12. **Customer feedback lane** — Test 28+ only if new UI feedback remains after Test 27 review.

Expected remaining core work: about **9–11 bounded implementation batches** excluding open-ended customer redesign requests.

## Primary risks / defects to control

1. **JSON concurrent writes** — mitigated by exclusive lock + atomic replace + revision conflicts.
2. **JSON becoming a fake relational DB** — do not implement joins/auth/order transactions in flat files; move such concerns to relational storage.
3. **Single large-file growth** — use bounded collection documents and later split only when evidence requires it.
4. **File exposure** — production JSON root must live outside `public_html`.
5. **Backup corruption** — copy only complete atomically replaced documents and retain versioned backups.
6. **Admin/auth split** — domain customer profile may be JSON, but login identity/token state remains relational and links by stable external id.
7. **Schema evolution** — every JSON document carries a schema version; migrations/import tools must be explicit.
8. **Visual editor override sprawl** — organize controls by stable target groups and semantic tokens; no arbitrary selector/CSS editor.
9. **Hidden UI becoming unrecoverable** — every visibility toggle must remain accessible from the editor's structured target list.
10. **Customer UI churn** — Test 26 remains immutable and every later UI change stays in Test 27+ lanes.

## Next batch after this foundation

Implement domain-specific Catalog repositories on top of the JSON store, import existing catalog data into a versioned JSON collection, and keep the current UI/API response contract unchanged.
