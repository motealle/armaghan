# Armaghan — Current Status

Last reconciled: 2026-10-02
Canonical main head at reconciliation start: `10d040c7d1706d68a5018e1240c797011440000c`

This file is the **current operational source of truth** for the next run. Historical decision records remain useful, but when an older document conflicts with this file, use this file plus the latest `docs/HANDOFF.md` and `docs/BACKLOG.md`.

## Executive status

Core MVP delivery is approximately **91–94% complete**. Core admin CRUD, public catalog reads, product media, guarded deployment, real customer Backend session and one-time Magic Link authentication are live. The remaining core delivery is persisted FavoriteShare/WhatsApp sharing, off-host backup/restore hardening, and final end-to-end acceptance.

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
- Category/Subcategory/Product/Customer Filament Resources are implemented and live in production. Taxonomy commit `4281034656...` passed Backend CI #31; Product/Customer commit `de450a15...` passed Backend CI #33; Backend Code Deploy #3 passed with all four Resource routes returning HTTP 200.
- A guarded code-only Backend update lane is operational: it refuses Composer/migration drift, creates a pre-swap SQLite snapshot, stages private/public code, HTTP-smokes the release and rolls code back on failure. Run #1 deliberately rolled back after a public-permission smoke failure; Runs #2–#5 passed.
- Public catalog API is live at `/backend/api/catalog/categories` and `/backend/api/catalog/products`; production catalog is initialized with **3 categories / 6 subcategories / 18 products**.
- Test 27 performs non-blocking Hybrid Sync: Backend-managed names/status/active state overlay the browser catalog. Server product media now overrides the local gallery when present; local media/specs remain fallback while a server gallery is empty or the API is unavailable.
- Production product media is live through Spatie Media Library + the official Filament integration: ordered `product-gallery`, JPEG/PNG/WebP validation, 8 MiB and 5000×5000 limits, metadata-stripping re-encode, non-cropping/non-upscaling card/thumb conversions, and public delivery through `/backend/storage`.
- The production `media` table is migrated and the public-storage symlink is verified. The current 18 bootstrapped products still have zero server media rows until an authenticated administrator uploads real product images; Test 27 therefore continues to show the existing local fallback images without regression.
- A guarded additive Backend deploy lane is production-proven for create-only migrations/dependency changes. Backend Additive Deploy #3 created a consistent SQLite snapshot, applied exactly one additive media migration, verified the public storage link, passed all HTTP smokes, and cleaned temporary files.
- Customer Backend session + Magic Link core is live. Links are high-entropy, hash-only in SQLite, one-time, expiring, revocable, audited and rate-limited. Customer session state is separate from Filament admin auth and rotates the session identifier without clobbering admin authentication.
- Test 27 hydrates a real Backend customer session when present and no longer generates browser-only Magic Links. Production links keep the bearer token only in the browser hash fragment (`#/magic/<token>`), then consume it through a fixed same-origin POST endpoint; the token is not sent in the initial HTTP URL.
- Backend Code Deploy #9 deliberately rolled back when the first token-in-path production smoke exposed a real 404 routing incompatibility. Repair commit `10d040c7...` moved consumption to the fixed POST endpoint; Backend Code Deploy #10 PASS with guest customer session = 401 and GET on POST-only consume endpoint = 405.
- FTP Deploy #314 PASS delivered the repaired Test 27 auth flow; root deployment remained skipped.
- Final browser-authenticated Test 27 Style Profile acceptance is still open but is non-blocking. A read-only check of the saved browser profile redirected to `/backend/admin/login`, so delivery does not rely on that session.

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
- Core schema checks: users, products, customers, Style Profile tables, media table.
- Production image runtime requirements: GD + EXIF accepted by the guarded Media deployment preflight.
- Public product media link: `/backend/storage` active.
- Public backend smoke:
  - `/backend/` → 200
  - `/backend/up` → 200
  - `/backend/api/style-profile/staging` → 200
  - `/backend/admin/login` → 200
  - `/backend/api/catalog/categories` → 200
  - `/backend/api/catalog/products?per_page=1` → 200
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
- Public Catalog controller + API Resources for Category/Product reads.

Production catalog state:
- 3 active categories.
- 6 active subcategories.
- 18 active products initialized from the canonical MVP fixture.
- Public catalog filtering/search/pagination and managed-code metadata are live.

Not yet implemented:
- Full FavoriteShare HTTP flow + persisted WhatsApp handoff.

Customer session and Magic Link HTTP flows are implemented and live. The customer-facing session API exposes only the bounded self-service profile subset; internal notes/user ownership are not returned or customer-writable.

Product media ownership/upload is implemented and live. The public catalog now returns a `media` array per Product and Test 27 prefers that server gallery when non-empty. Because the current production Products do not yet have uploaded server media, local media/specs remain the visible fallback. Customer/account flows still contain prototype/local browser paths until their APIs and Magic Link session flow are implemented.

## Persistence policy

Current policy:
- SQLite is the primary Laravel database for local/dev/test/production.
- JSON is ordinary import/export/fixture interchange only.
- MySQL/MariaDB is optional later as a logical mirror/export/disaster-recovery target.
- Never dual-write normal live requests to SQLite + MySQL.
- Primary backups remain SQLite-to-SQLite consistent snapshots.
- At least one rotated off-host backup + restore drill is still required before final production handoff.

## Remaining core delivery — estimated 3 bounded runs + opportunistic browser acceptance

This estimate excludes open-ended new customer UI revisions. Browser-authenticated Style Profile acceptance and one real Filament product-image upload remain opportunistic parallel checks rather than blockers because their server-side foundations are already production-verified.

1. **Favorites/WhatsApp persisted share flow**
   - persisted favorite-share links;
   - guest/customer ownership and expiry/revoke;
   - WhatsApp handoff against server-backed shares;
   - remove the current product-code-in-URL share path from the real Backend flow.

2. **Production hardening**
   - rotated off-host SQLite backup;
   - restore drill;
   - logs/health verification;
   - additive and code-only deploy lanes are already production-proven.

3. **Final end-to-end QA + handoff**
   - mobile/tablet/desktop;
   - RTL/LTR;
   - light/dark;
   - admin/customer permissions;
   - backup/restore;
   - deployment smoke;
   - final customer changes and documentation.


## Highest-priority open items

P0:
1. Implement persisted FavoriteShare/WhatsApp handoff without modifying frozen Test 26.
2. Configure rotated off-host SQLite backup and perform a restore drill.
3. Complete browser-authenticated Test 27 Style Profile + one real Filament product-image upload acceptance opportunistically when a valid real admin session is available.
4. Keep both Backend deployment lanes green: code-only for ordinary changes, guarded additive for approved create-only migration/dependency changes.

P1:
- Favorites/WhatsApp persisted share flow.

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
- Backend CI #31: PASS for Category/Subcategory Resources.
- Backend Code Deploy #2: PASS for first live taxonomy deployment after automatic rollback of failed Run #1.
- Backend CI #33: PASS for Product/Customer Resources.
- Backend Code Deploy #3: PASS; `/backend/up`, admin login, categories, subcategories, products and customers all HTTP 200.
- Backend CI #35: PASS for Public Catalog API + Test 27 Hybrid Sync implementation.
- FTP Deploy #295: PASS; Test 27 type-check/unit/build/deploy completed and root deployment was skipped.
- Backend CI #36: PASS; Backend Code Deploy #4 PASS with both public catalog endpoints HTTP 200.
- Backend CI #37: PASS for guarded catalog-bootstrap command/fixture.
- Backend Code Deploy #5: PASS; bootstrap command deployed with no migration/dependency drift.
- Catalog Bootstrap Production #1: PASS; pre-state 0/0/0, SQLite snapshot created, post-state 3 categories / 6 subcategories / 18 products, representative codes verified, helper cleanup PASS.
- Backend CI #39/#40: PASS for additive-deploy infrastructure and up-only additive migration validator.
- Backend CI #41: PASS on the pre-merge Product Media PR; official locked Spatie/Filament dependencies, media migration and feature tests all passed.
- Product Media merge: `4d8f660a06df7b4e4a340502d6d65255b41cc949`.
- Backend CI #42: PASS after merge.
- Backend Code Deploy #7: code-only deployment correctly skipped because Composer/migration drift was detected.
- Backend Additive Deploy #3: PASS — backup created, dependency changed=true, exactly 1 create-only migration applied, `public_storage_link=true`, health/login/products/catalog smokes all 200, temp cleanup PASS.
- FTP Deploy #307: intentionally failed before Test27 deployment because the anti-hotlink policy rejected an external-domain URL used only in a unit-test fixture; no `/t/27` mutation occurred.
- Test-fixture repair: `b3722d3f4643ee403972b7a03215339e5aa802ad`.
- FTP Deploy #308: PASS — immutable contracts, web-stock policy, TypeScript, Vue unit tests, Test27 production build and FTP smoke all passed; `deploy-t` PASS; `deploy-root` skipped.
- Customer/Magic Link PR #7 merge `e311d732...`; Backend CI #47 PASS; Backend Code Deploy #8 PASS.
- FTP Deploy #311 exposed a stale historical Test19 browser-Magic-Link assertion; future-proof repair `5b1bcffc...`; FTP #312 PASS.
- Backend Code Deploy #9 activated the follow-up release but correctly rolled back after production returned 404 for the token-in-path Magic Link smoke; rollback health + cleanup PASS.
- Magic Link routing repair PR #8 merge `10d040c7d1706d68a5018e1240c797011440000c`; Backend CI #49 pre-merge + #50 post-merge PASS.
- Backend Code Deploy #10 PASS — snapshot created, no drift, all CRUD/catalog smokes 200, guest customer session 401, fixed Magic Link consume route GET 405, cleanup PASS.
- Independent production probe reconfirmed customer session 401 and fixed Magic Link consume GET 405.
- FTP Deploy #314 PASS — Test27 customer-auth contract, TypeScript, Vue tests/build, FTP smoke and `/t/27` deploy PASS; root skipped.

## Rules for the next run

1. Read this file first, then `docs/HANDOFF.md`, `docs/BACKLOG.md`, `docs/PROJECT-RULES.md`, and the shared lock.
2. Do not repeat production SQLite activation; it is complete.
3. Do not repeat first-admin bootstrap; one active admin already exists.
4. Do not claim Style Profile server persistence is pending; its production service semantics are verified.
5. The remaining Style Profile task is browser/session/CSRF acceptance.
6. Keep Test 26 immutable.
7. Keep Test 27 as the active mutable UI lane.
8. Never store production plaintext credentials in Git, docs, logs or artifacts.
9. Do not rebuild product-media ownership or Customer/Magic Link core; both are live. The next P0 is persisted FavoriteShare/WhatsApp.
10. Do not claim the 18 current Products have server media yet: their API `media` arrays are currently empty and Test 27 intentionally falls back to local media until an admin uploads images.
11. Use the guarded additive lane for future approved create-only migration/dependency changes; ordinary Backend changes stay on the code-only lane.

