# Armaghan — Current Status

Last reconciled: 2026-10-01 18:45 +03:30  
Canonical main head at reconciliation start: `79ee20ad14414298be2ce6f129e6c48922017200`

This file is the **current operational source of truth** for the next run. Historical decision records remain useful, but when an older document conflicts with this file, use this file plus the latest `docs/HANDOFF.md` and `docs/BACKLOG.md`.

## Executive status

Core MVP delivery is approximately **75–80% complete**. The remaining work is mostly admin CRUD/public API wiring, customer share/login flows, production backup/release hardening, and final end-to-end acceptance.

Current UI lane:
- Test 26: frozen/immutable.
- Test 27: active mutable review lane and visible in the test launcher.
- Test 27 is live under `/t/27`.
- Customer visual-correction batch is live in Test 27: logo-only footer branding, six-way product labels/unified unavailable state, hidden-by-default subcategory codes, white Products background, Mint cards, strong green heading role, Brand Blue/Gold Why styling. FTP Deploy #277 delivered the customer correction set; final heading-green polish FTP Deploy #282 PASS. No remote deletion occurred and root deployment remained skipped.

Current backend lane:
- Laravel 13.34.0 is live under `/backend`.
- Production SQLite is active and is the primary database.
- Production Style Profile persistence/version/restore semantics are verified against the live SQLite database.
- One real active production administrator exists.
- Filament login page is live.
- Final browser-authenticated Test 27 shared-persistence acceptance is still open.

## Production backend — verified complete

The current production backend is no longer a planning-only or local-only implementation.

Verified:
- PHP 8.3.33 on LiteSpeed.
- PDO SQLite: PASS.
- SQLite3 extension: PASS.
- SQLite 3.53.4.
- Private SQLite file create/read/write: PASS.
- SQLite foreign keys: PASS.
- SQLite `VACUUM INTO`: PASS.
- Private application/data sibling outside `public_html`: PASS.
- HTTPS execution: PASS.
- Laravel 13.34.0 deployed under `/backend`.
- Host-only `.env` and APP_KEY remain outside Git and logs.
- Production SQLite database created outside `public_html`.
- Production migrations: PASS.
- Initial consistent SQLite snapshot: PASS.
- Core schema checks: users, products, customers, Style Profile tables.
- Public backend smoke:
  - `/backend/` → 200
  - `/backend/up` → 200
  - `/backend/api/style-profile/staging` → 200
  - `/backend/admin/login` → 200
- Public Laravel permissions normalized to LiteSpeed-safe directories `0755` / files `0644`; private data permissions were not widened.

## First production administrator — verified complete

A real production active-admin account exists.

- Account identity: `admin@armaghan.local`.
- No fixed/default password is committed or documented.
- Plaintext credential is not stored in the repository.
- Provisioning/recovery flow is fail-closed and tested.
- Active-admin policy and password hashing verification passed.
- Filament login route smoke passed.

Do not create another bootstrap admin unless the existing account is intentionally rotated/removed through an explicit administration decision.

## Test 27 visual editor — live state

Current Test 27 editor capabilities:
- Admin-only visual editor launcher.
- Mobile non-modal resizable bottom sheet.
- Direct touch selection.
- Explicit chooser for nearby/nested targets.
- Structured element browser.
- Recoverable hidden targets.
- Text editing for registered locale-aware targets.
- Approved-token text/background/border controls.
- Hide/show and per-target reset.
- Contrast guard for explicit text/background token pairs.
- Browser-local autosave.
- Sanitized JSON export/import for manual portability only.
- Laravel Style Profile adapter.
- Sync status: local / checking / saving / synced / conflict / error.
- 900ms debounced authenticated server draft save.
- `expected_checksum` optimistic-concurrency protection.
- Explicit HTTP 409 conflict resolution.
- Staging Publish.
- Recent immutable version history.
- Restore-as-new-version behavior.

### Current Test 27 customer color state

Canonical approved palette:
- Brand Green: `#21946A`
- Brand Blue / logo-background blue: `#0714C2`
- Brand Mint: `#C8E3DB`
- Brand White: `#FFFFFF`
- Brand Gold: `#FFB514`

The old canonical blue `#151EDA` is retired for Test 27.

Current semantic defaults:
- Header/Footer/Hero brand chrome → Brand Blue `#0714C2`.
- Home light-mode page background → Brand White.
- Primary Home panel background → Brand Mint.
- `home.page` background is editable but the root itself cannot be hidden.
- `home.content` is separately hideable/selectable.

Test 27 color/saveability implementation is live; FTP Deploy Run #268 passed with no remote deletes and no root deployment.

## Style Profile production persistence — verified server-side

Production Style Profile persistence has passed a live transactional acceptance test against the real production SQLite database.

Production acceptance Run `36878931948`: **PASS**.

Verified inside one outer transaction:
- draft save;
- first staging Publish;
- second changed staging Publish;
- Restore from the first version as a new immutable version;
- staging publication pointer update;
- ActivityLog writes.

Observed during the transaction:
- 3 new immutable versions;
- 3 new activity records.

The outer transaction was rolled back. Post-test logical state exactly matched pre-test state. Temporary public helper cleanup passed.

### Only remaining editor persistence acceptance

The server-side semantics are no longer uncertain.

The remaining acceptance is **browser-only**:
1. authenticate through the real Filament login at `/backend/admin/login`;
2. open Test 27 as the authenticated administrator;
3. edit a registered target;
4. confirm the editor reaches server-synced state;
5. reload and confirm the draft persists;
6. verify the shared draft from another browser/device session;
7. Publish staging through the UI;
8. Restore an older version through the UI;
9. confirm CSRF/session behavior stays valid throughout.

Do not weaken authentication or add a bypass just to automate this final browser step.

## Backend domain state

Implemented models/foundation:
- User
- Category
- Subcategory
- Product
- ProductSpecValue
- SpecDefinition
- Customer
- FavoriteShare
- MagicLink
- ActivityLog
- StyleProfile
- StyleProfileVersion
- StyleProfilePublication

Implemented production-facing controllers:
- Public Style Profile read controller.
- Admin Style Profile controller.

Not yet implemented:
- Filament Product Resource.
- Filament Category Resource.
- Filament Subcategory Resource.
- Filament Customer Resource.
- Public catalog Product/Category API controllers.
- Customer public/admin API controllers.
- Full FavoriteShare HTTP flow.
- Full MagicLink HTTP/session flow.
- Production media ownership/upload flow.

The frontend still contains prototype/local browser data paths for customer/catalog/admin experiences until these backend resources/APIs are wired.

## Persistence policy

Current policy:
- SQLite is the primary Laravel database for local/dev/test/production.
- JSON is ordinary import/export/fixture interchange only.
- MySQL/MariaDB is optional later as a logical mirror/export/disaster-recovery target.
- Never dual-write normal live requests to SQLite + MySQL.
- Primary backups remain SQLite-to-SQLite consistent snapshots.
- At least one rotated off-host backup + restore drill is still required before final production handoff.

## Remaining core delivery — estimated 6 runs

This estimate excludes open-ended new customer UI revisions.

1. **Browser-authenticated editor acceptance**
   - real Filament session + CSRF;
   - Test 27 server sync/reload/cross-device;
   - staging Publish/Restore through the real UI;
   - fix any browser/session integration bug found.

2. **Filament CRUD**
   - Product/Category/Subcategory/Customer Resources;
   - safe validation and pagination/search;
   - no duplicate data ownership.

3. **Catalog/customer media + public API wiring**
   - public catalog reads;
   - Vue customer-facing data from backend;
   - controlled product image/media flow.

4. **Favorites/WhatsApp + customer Magic Link**
   - persisted favorite share links;
   - expiry/revoke;
   - safe one-tap customer session;
   - audit trail.

5. **Production hardening**
   - rotated off-host SQLite backup;
   - restore drill;
   - repeatable backend release/update workflow;
   - pre-migration snapshot + rollback guard;
   - logs/health verification.

6. **Final end-to-end QA + handoff**
   - mobile/tablet/desktop;
   - RTL/LTR;
   - light/dark;
   - admin/customer permissions;
   - backup/restore;
   - deployment smoke;
   - final customer changes and documentation.

Filament CRUD/media may require two separate bounded runs; if so, remaining core work becomes approximately 7 runs.

## Highest-priority open items

P0:
1. Complete the real browser-authenticated Test 27 Style Profile cycle.
2. Implement Filament Product/Category/Subcategory/Customer CRUD.
3. Wire catalog/customer data to backend APIs without modifying frozen Test 26.
4. Configure rotated off-host SQLite backup and perform a restore drill.
5. Build a safe repeatable backend update workflow with pre-migration snapshot.

P1:
- Favorites/WhatsApp persisted share flow.
- Customer Magic Link session flow.
- Product media ownership/upload subsystem.

P2 / optional:
- MySQL logical mirror/export with verification.
- Supabase only if a concrete managed-Postgres/Auth/Storage/Realtime need appears.
- Advanced analytics/orders/queues unless required for delivery.

## Recent authoritative runs

- Production Style Profile Acceptance `36878931948`: PASS.
- FTP Deploy #272: PASS after production acceptance merge; UI/root deploy skipped.
- FTP Deploy #273: PASS for final documentation; UI/root deploy skipped.
- Backend CI #30: PASS for first-admin provisioning tooling.
- FTP Deploy #270: PASS for first-admin provisioning merge.
- FTP Deploy #268: PASS for live Test 27 color/saveability update.

## Rules for the next run

1. Read this file first, then `docs/HANDOFF.md`, `docs/BACKLOG.md`, `docs/PROJECT-RULES.md`, and the shared lock.
2. Do not repeat production SQLite activation; it is complete.
3. Do not repeat first-admin bootstrap; one active admin already exists.
4. Do not claim Style Profile server persistence is pending; its production service semantics are verified.
5. The remaining Style Profile task is browser/session/CSRF acceptance.
6. Keep Test 26 immutable.
7. Keep Test 27 as the active mutable UI lane.
8. Never store production plaintext credentials in Git, docs, logs or artifacts.
