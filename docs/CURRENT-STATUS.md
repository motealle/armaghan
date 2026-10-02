# Armaghan — Current Status

Last reconciled: 2026-10-02
Canonical main head at reconciliation start: `72a45224e0bd6fd6547f30f2fa9983e6f1424a0b`

This file is the **current operational source of truth** for the next run. Historical decision records remain useful, but when an older document conflicts with this file, use this file plus the latest `docs/HANDOFF.md` and `docs/BACKLOG.md`.

## Executive status

Core MVP delivery is approximately **98–99% complete**. Core admin/catalog/media/customer-auth/share flows, guarded deployment, encrypted off-host SQLite backup and artifact round-trip restore verification are live. Test 28 is the active mutable review lane. The only remaining core run is final end-to-end QA + delivery handoff.

Current UI lane:
- Tests 01–27: frozen/immutable. Test 27 is the final pre-hardening review snapshot and remains live under `/t/27`.
- Test 28: active mutable review lane, live under `/t/28`, and first in the mutable test launcher.
- Test 28 uses isolated `armaghan:test28:*` browser-state namespaces; active Vite/FTP lanes reject builds/deployments targeting frozen Test 27.
- Test 28 carries forward the validated Test 27 customer UI and all current Backend integrations, while adding the production-hardening release boundary.
- FTP Deploy #320: PASS — 112 Test 28 files uploaded, no remote files deleted, launcher updated, `deploy-t` PASS and `deploy-root` skipped.
- Independent live verification: `/t/28` loads, `/t/27` still loads unchanged, and the launcher lists Test 28 before Test 27.

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
- Persisted FavoriteShare/WhatsApp is live. New share links are server-backed, carry only a high-entropy bearer token in the browser fragment, store only SHA-256 token hashes in SQLite, preserve product order, validate active taxonomy/products and resolve through a fixed POST endpoint.
- Guest shares expire after 7 days; authenticated customer shares expire after 30 days and may be revoked by that customer. Public share creation/resolution is CSRF-protected + rate-limited; customer revocation requires the real Backend customer session.
- Test 27 no longer generates product-code-in-URL links. Old `?shared=v1:` links remain read-only compatible, while all new sharing uses persisted Backend links. The WhatsApp action sends the persisted share URL through the existing seller WhatsApp path.
- Favorite changes now attribute real customers using `currentCustomerId`; the old hard-coded Customer #1 bug is removed.
- Backend Code Deploy #11 PASS with FavoriteShare resolve route GET = 405; independent Production probe confirmed both FavoriteShare issue and resolve endpoints exist as POST-only. FTP Deploy #316 PASS delivered Test 27; root skipped.
- Read-only live browser acceptance with an intentionally invalid 64-character share token reached the persisted shared-favorites route and ended in the correct invalid/unavailable state without crash or legacy product-code sharing.
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
  - `/backend/api/customer/session` → 401 for guest
  - GET `/backend/api/customer/magic-link/consume` → 405 (POST-only route exists)
  - GET `/backend/api/favorite-shares/resolve` → 405 (POST-only route exists)
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

## Test 28 visual editor — live state

Current Test 28 editor capabilities:
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

### Current Test 28 customer color state

Canonical approved palette:
- Brand Green: `#21946A`
- Brand Blue / logo-background blue: `#0714C2`
- Brand Mint: `#C8E3DB`
- Brand White: `#FFFFFF`
- Brand Gold: `#FFB514`

The old canonical blue `#151EDA` is retired for Test 28.

Current semantic defaults:
- Header/Footer/Hero brand chrome → Brand Blue `#0714C2`.
- Home light-mode page background → Brand White.
- Primary Home panel background → Brand Mint.
- `home.page` background is editable but the root itself cannot be hidden.
- `home.content` is separately hideable/selectable.

The current Test 28 source inherits the validated color/saveability implementation; Test 27 remains its frozen predecessor.

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
2. open Test 28 as the authenticated administrator;
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

Customer session/Magic Link and FavoriteShare/WhatsApp HTTP flows are implemented and live. The customer-facing session API exposes only the bounded self-service profile subset; internal notes/user ownership are not returned or customer-writable. FavoriteShare resolution returns only ordered active product codes and share expiry metadata; it does not expose share-owner identity.

Product media ownership/upload is implemented and live. The public catalog returns a `media` array per Product and Test 28 prefers that server gallery when non-empty. Because the current production Products do not yet have uploaded server media, local media/specs remain the visible fallback. Test 28 still retains demo/local auth and local favorites storage only as review/resilience fallbacks; real customer sessions, Magic Links and newly generated FavoriteShare links are Backend-backed.

## Persistence policy

Current policy:
- SQLite is the primary Laravel database for local/dev/test/production.
- JSON is ordinary import/export/fixture interchange only.
- MySQL/MariaDB is optional later as a logical mirror/export/disaster-recovery target.
- Never dual-write normal live requests to SQLite + MySQL.
- Primary production backups use a consistent SQLite `VACUUM INTO` snapshot.
- Daily off-host backup is operational through GitHub Actions at `23 1 * * *` UTC with 14-day artifact retention.
- The plaintext SQLite snapshot is encrypted on the production host with AES-256-GCM before leaving the host; the public GitHub repository artifact contains only ciphertext plus a non-secret manifest.
- SQLite Off-host Backup #1 (`36974268704`) PASS. Artifact `armaghan-sqlite-backup-36974268704` is 337,663 bytes and expires 2026-10-16T06:35:35Z.
- The restore-drill job downloaded that uploaded artifact, decrypted it only on an isolated runner, verified ciphertext/plaintext checksums, `PRAGMA integrity_check`, `foreign_key_check`, full table inventory and critical row counts, opened/rolled back a write transaction, then discarded the restored plaintext.
- First-run encryption used the explicit documented fallback `ftp_password_kdf_fallback` because `ARMAGHAN_BACKUP_PASSPHRASE` is not yet configured. Add a dedicated backup secret as an operational P1; do not rotate away the fallback secret while retained fallback-encrypted artifacts still need recovery unless the old secret is retained securely.

## Remaining core delivery — estimated 1 bounded run + opportunistic browser acceptance

This estimate excludes open-ended new customer UI revisions. Browser-authenticated Style Profile acceptance and one real Filament product-image upload remain opportunistic parallel checks rather than blockers because their server-side foundations are already production-verified.

1. **Final end-to-end QA + handoff**
   - mobile/tablet/desktop;
   - RTL/LTR;
   - light/dark;
   - admin/customer permissions;
   - live catalog/media/auth/share paths;
   - backup/restore evidence;
   - deployment smoke and final delivery documentation.
   - mobile/tablet/desktop;
   - RTL/LTR;
   - light/dark;
   - admin/customer permissions;
   - backup/restore;
   - deployment smoke;
   - final customer changes and documentation.


## Highest-priority open items

P0:
1. Complete final end-to-end QA/handoff across responsive/RTL-LTR/light-dark/auth/share/backup/deploy paths.
2. Complete browser-authenticated Test 28 Style Profile + one real Filament product-image upload + one real customer Magic Link acceptance opportunistically when a valid real admin session is available.
3. Keep code-only, additive and off-host-backup lanes green through final handoff.

P1:
- Add a dedicated GitHub Actions secret `ARMAGHAN_BACKUP_PASSPHRASE` and let future backups use it instead of the current FTP-secret KDF fallback. Retained fallback-encrypted artifacts still require the original fallback secret until they expire.
- No other core P1 blocker remains; optional acceptance/polish only.

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
- FavoriteShare PR #9 pre-merge Backend CI #51 PASS; squash merge `76f80179292f743cb54443e540602bba47c4d8bb`.
- Backend CI #52 PASS after merge.
- Backend Code Deploy #11 PASS — snapshot created, no dependency/migration drift; existing backend smokes remain green and FavoriteShare fixed resolve route GET returns 405; cleanup PASS.
- Independent production probe: GET `/backend/api/favorite-shares` = 405 and GET `/backend/api/favorite-shares/resolve` = 405, confirming both fixed POST endpoints exist.
- FTP Deploy #316 PASS — Test27 FavoriteShare source contract, TypeScript, Vue unit tests/build, FTP smoke and `/t/27` deploy PASS; `deploy-root` skipped.
- Live read-only invalid-token acceptance PASS on `/t/27/#/favorites/share/<invalid-token>`: shared route loaded, server resolution attempted, invalid/unavailable shared-list state rendered, no crash or legacy local-code share.
- Backend CI #53/#54 pre-merge and #55 post-merge: PASS for Test28/off-host-backup hardening.
- Test28 hardening squash merge: `6593f6124bfccfd25ae5899d2690608940214905`.
- SQLite Off-host Backup #1 (`36974268704`): PASS — production `VACUUM INTO`, AES-256-GCM encryption before transfer, encrypted artifact upload, temp cleanup PASS.
- Restore drill in the same workflow: PASS — downloaded artifact digest matched; decryption, plaintext checksum, integrity check, foreign-key check, table inventory/counts and write-transaction rollback all PASS; restored plaintext not persisted.
- FTP Deploy #318: stopped before build/deploy on a stale Test26 current-active-test assertion; Test26/27/root remained untouched.
- Follow-up `39bd08346420a74bd217ee908c6782c7403da7f1`: future-proofed only the historical Test26 contract and added the Test28 release marker.
- FTP Deploy #319: stopped before deployment because two session unit-test fixtures still wrote Test27 browser keys; runtime source was already correctly namespaced Test28.
- Follow-up `3158ffbbcd1ad7fe95cf3c096d0cf576b1b8cfde`: aligned those fixtures with Test28.
- FTP Deploy #320: PASS — frozen Test26/27 contracts, Test28 visual/auth/share contracts, backup safety contract, Python syntax, TypeScript, 41 Vue unit tests, Test28 production build and FTP smoke PASS; 112 files uploaded; `deploy-t` PASS; `deploy-root` skipped.
- Independent live verification: Test28 and frozen Test27 both load successfully; launcher lists Test28 first.
- FTP Deploy #321: failed before any deployment because the historical Test26 rule-text assertion still expected the exact freeze range 01–26 after Test27 was frozen.
- Contract-only fix `72a45224e0bd6fd6547f30f2fa9983e6f1424a0b` made the Test26 freeze assertion range-aware without changing Test26/27 runtime content.
- FTP Deploy #322: PASS; full static QA/Python checks passed, frontend build intentionally skipped, `deploy-t` and `deploy-root` skipped.

## Rules for the next run

1. Read this file first, then `docs/HANDOFF.md`, `docs/BACKLOG.md`, `docs/PROJECT-RULES.md`, and the shared lock.
2. Do not repeat production SQLite activation; it is complete.
3. Do not repeat first-admin bootstrap; one active admin already exists.
4. Do not claim Style Profile server persistence is pending; its production service semantics are verified.
5. The remaining Style Profile task is browser/session/CSRF acceptance.
6. Keep Test 26 immutable.
7. Keep Test 27 as the active mutable UI lane.
8. Never store production plaintext credentials in Git, docs, logs or artifacts.
9. Do not rebuild product-media ownership, Customer/Magic Link or FavoriteShare/WhatsApp; all are live. The next P0 is off-host backup/restore hardening.
10. Do not claim the 18 current Products have server media yet: their API `media` arrays are currently empty and Test 27 intentionally falls back to local media until an admin uploads images.
11. Use the guarded additive lane for future approved create-only migration/dependency changes; ordinary Backend changes stay on the code-only lane.

