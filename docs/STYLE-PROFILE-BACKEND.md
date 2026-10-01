# Armaghan — Versioned Style Profile Backend

Status: backend persistence foundation validated; frontend adapter is the next bounded batch.

## Ranked persistence options

| Rank | Method | Score | Reason |
|---:|---|---:|---|
| 1 | **Mutable draft + immutable versions + explicit publication pointer** | **10.0** | Autosave stays cheap, published history is never rewritten, restore is safe, staging/production can point to different versions |
| 2 | Single mutable profile row + manual snapshots | 8.3 | Simpler schema, weaker audit/history guarantees |
| 3 | Per-property key/value table | 7.1 | Queryable but awkward for locale text + nested style payloads |
| 4 | JSON files in Git/server filesystem | 5.0 | Versionable but unsuitable for live admin autosave and concurrent edits |
| 5 | Store arbitrary raw CSS | 1.5 | Conflicts with project security and token rules; permits fragile/untrusted styling |

**Selected:** option 1.

## Data model

### style_profiles

One named profile owns the current mutable draft.

- slug
- name
- schema_version
- draft_styles JSON
- draft_texts JSON
- draft_css (server-generated)
- draft_checksum
- created_by / updated_by
- timestamps

The MVP uses slug `default`; the schema remains capable of supporting more named profiles later.

### style_profile_versions

Immutable publication revisions.

- style_profile_id
- monotonically increasing version number
- schema_version
- optional source_test
- styles JSON
- texts JSON
- compiled_css
- checksum
- created_by
- timestamps

Rows are never rewritten as a restore mechanism. Restore creates a new version.

### style_profile_publications

Small pointer table mapping a channel to one immutable version.

Current allowed channels:

- `staging`
- `production`

This lets Test 27 consume a reviewed staging publication without automatically changing production.

## Security boundary

- Public read: `GET /api/style-profile/{staging|production}`.
- Admin reads/writes: `/api/admin/style-profile/*`.
- Admin write access requires the same persisted `active + admin` identity used by Filament.
- Routes live in Laravel's web middleware stack so session state and Laravel request-forgery protection remain active.
- Guest writes receive 401; authenticated non-admin/inactive-admin writes receive 403.
- Arbitrary CSS/HTML/JavaScript is never accepted.
- Target ids are constrained to letters, digits, dot, underscore and dash.
- The only accepted palette token ids are `green`, `blue`, `mint`, `white`, `gold`.
- Nested style objects whitelist only `textColor`, `backgroundColor`, `borderColor`, `hidden`.
- Locale text objects whitelist only `fa`, `ar`, `en`, `ku`.
- Payload size and per-text length are bounded.
- CSS is compiled on the server from validated structured data.

## Concurrency protection

Draft updates may send `expected_checksum`.

If another editor has saved a newer draft since the caller loaded the profile, the write returns HTTP 409 rather than silently overwriting the newer work.

This is intentionally optimistic concurrency: normal single-admin editing has no lock dialog, while stale clients fail safely.

## Publish behavior

Publishing:

1. locks the profile row inside a database transaction;
2. compares the draft checksum with the currently published channel version;
3. avoids creating a duplicate version when nothing changed;
4. creates the next immutable version when content changed;
5. atomically points the requested channel to that version;
6. writes an activity-log entry.

## Restore behavior

Restoring version N:

1. re-normalizes the historical structured payload through the current compiler;
2. copies it into the mutable draft;
3. creates a new immutable version N+K;
4. points the requested channel to that new version;
5. records `style_profile.restored` in the activity log.

The historical row is never altered.

## Portability

The persistence model uses Laravel migrations and JSON columns that are exercised on SQLite in CI and intended for MySQL/MariaDB in production. Server CSS compilation is deterministic so equal structured input produces equal checksum/CSS independent of request key order.

## Validation record

Backend CI Run #10:

- 12 tests passed;
- 84 assertions passed;
- StyleProfile compiler tests passed;
- StyleProfile API/security/version/restore/conflict tests passed;
- migrations passed on isolated SQLite;
- Composer audit: no known vulnerability advisories;
- secret-hygiene checks passed.

The run also exposed a pre-existing mismatch: the ActivityLog model defaulted to plural `activity_logs` while the established migration created singular `activity_log`. The model now explicitly maps to the existing table; no historical migration was rewritten.

## Next batch

Add a small frontend persistence adapter around the existing Test 27 Pinia store:

- load `staging` publication as server baseline;
- keep local draft immediately responsive;
- debounce authenticated admin draft saves;
- send `expected_checksum` on updates;
- surface 409 conflicts instead of overwriting;
- explicit Publish to staging;
- history + Restore UI in the editor/admin;
- retain local-only fallback when the backend endpoint is unavailable during staged rollout.
