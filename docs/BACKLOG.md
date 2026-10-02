# Armaghan — Product Backlog

## Preserved custom administration — customer vertical slice, 2026-10-02

Owner explicitly requests the previously designed administration interface connected to Laravel rather than replacement with stock panel styling. Baseline checkpoint: `4b19f5da25734a47facd68396ed6538bf79fc207`. Shared lease: `codex-20261002-custom-admin`.

| Rank | Path | Score | Reason |
|---:|---|---:|---|
| 1 | Reuse custom UI, connect one complete domain flow at a time | 10 | Preserves design and permits focused persistence/security acceptance |
| 2 | Connect all administration at once | 7 | Larger failure scope |
| 3 | Restyle the stock panel | 6 | Rebuilds approved interaction details |
| 4 | Independent admin data stores | 3 | Divergent data |
| 5 | Browser-local administration | 1 | No shared persistence or real authorization |

Selected option 1. This run connects `/\#/admin` and `/t/29/\#/admin` to real admin session and Customer records using the existing AdminDashboard tab shell, table, form, profile-head and AdaptivePanel styles. Customers are the first migrated domain; other custom tabs remain visible but disabled until wired. Existing Filament product/media/customer operations remain available through explicit links. No stock replacement, new database, dependency or migration.

- Active administrator is checked by existing Laravel `active.admin` middleware on every API request. No review role/storage state grants access. Session and customer responses are no-store/private; inactive/customer/guest principals cannot read or mutate. Administrator logout rotates session state while retaining independent customer authentication.
- Paginated/searchable customer list; create CRM record; edit company/contact/country/internal notes/priority/access flags; deactivate/reactivate without deleting records. Same Customer Eloquent model and database as Filament, with explicit fields. Public account registration remains owner-operated; admin form never changes account email/password/user ownership.
- Revision fingerprint prevents stale edits even within one timestamp second, inside a database transaction. Conflicts require reload; no silent overwrite. Audit stores actor/id/field names only, not note/contact contents. Failures remain visible and no fake row/demo-order data is used in the live custom screen.
- Customer password/timeline/payment/photo/impersonation and bulk deletion prototypes are not exposed as live operations. Customer counts come from the server page, not fixtures. Disabled customer access retains records.
- Password administrator login opens the custom route; verified Google owner setup page links there after private password setup. Ordinary Filament login remains available. Test29/root selector preserved; 01–28 frozen.
- Backend CI #62 (37051870668) PASS: 70 tests / 538 assertions; Backend Code Deploy #18 (37051870771) PASS, health/admin/catalog/resource smoke and temporary cleanup PASS. Frontend 53 tests / 14 files, type-check/build and frozen guards PASS. Initial FTP #348 (37051870707) Test29/root PASS. Final UI label release FTP #351 (37052472009): QA, smoke, Test29 publish/checksums and root promotion all PASS. Final JS index-LZ6WcOKf.js / CSS index-B0UfuJw4.css, lazy AdminView-C-JWJxiT.js; root browser reload confirms final JS and custom admin route. Live guest admin session/list GET=401 and no-CSRF customer-create/admin-logout POST=419. No production customer records or credentials inspected, created, edited or deleted by the agent. Actual owner authenticated browser writes/reload, mobile/tablet visual acceptance and valid owner Google provisioning remain OPEN.

Next bounded slices: (1) preserve AdminProductsPanel/ProductEditorPanel and connect canonical Product CRUD and existing Spatie media validation/ordering; (2) real admin access to existing style profile/editor publishing and device settings; (3) remaining dashboard/lead data only when actual services exist. Do not duplicate services or claim a fully migrated custom admin.

Implementation references: official Laravel 13 request-forgery protection and database transaction documentation. Existing session/CSRF mechanism remains unchanged.

## Owner administrator access and real password accounts — 2026-10-02

- Owner explicitly authorized administrator access for motealle@gmail.com and armaghantrading.company@gmail.com, real email/password sign-in, public customer signup, removal of the direct-link login tab, transparent gold producible labels, and a footer logo 130% larger at bottom right.
- Five ranked methods: verified Google owner provisioning + real password flows (10), separate admin password panel (8), Google-only (7), public magic-link flow (5), browser demo authentication (1). Selected the first; no browser-local role or typed email alone grants administrator rights.
- The server owner-email allowlist is configuration-backed. Only stateful Socialite with a valid subject and verified Google email can create/elevate those administrators. Disabled accounts remain disabled; promoting an existing customer disables its customer access and replaces its old password. Existing administrator passwords survive repeat Google login.
- Owner Google login establishes real Filament web authentication and opens /backend/account/security with a 10-minute, own-user, one-use password setup proof. The user chooses the password privately in the browser; no password is seeded, displayed, transmitted in chat or saved in Git. Existing password changes require current password or fresh Google proof. Profile management is enabled in Filament.
- Same-origin CSRF-protected POST login/register routes hash passwords, enforce at least 12 characters with letters/numbers and confirmation, rate-limit login per account and IP, and issue real customer sessions. Public signup always creates customer role, cannot claim owner emails, cannot set verified/admin/internal fields, and rejects duplicate emails. No email verification or password-reset mail delivery is claimed.
- LoginSheet uses only real server login/signup on root and Test29. Direct-link tab removed; existing safe manager-issued Magic Link consume remains functional for compatibility. Clear administrator password-panel shortcut remains visible. Real customer account includes a password-settings link.
- Producible badge keeps its gold text, centered small weight 400 typography, with transparent background and no navy border/shadow. Footer brand moved after columns in DOM and aligned physical bottom right; 3rem to 6.9rem is +130% (2.3×), with contained image and no adjacent text. Editor target IDs remain stable.
- Tests 01–28 frozen; Test29 remains active and selected at root. Local type-check/build + 51 frontend tests PASS; Backend CI #61 (37046579401): 63 tests / 473 assertions PASS. Code deploy #17 (37046579406): deployed and health/admin/catalog smoke PASS. Initial FTP #345 (37046579358) stopped before UI deployment on an obsolete active-source assertion requiring browser-local signup and a public Magic Link tab; owner-requested semantics were corrected in Test19/Test24 source contracts, without changing frozen numbered folders. FTP #346 (37046866862) QA/type-check/51 frontend tests PASS; Test29 and root publication/checksums PASS; live root reload loads index-DhRFzVDI.js and confirms both real login/signup forms, no public direct-link tab, and the real admin login destination. Actual owner Google sign-in/password setup and live authenticated logout remain OPEN until exercised; no production credentials or customer records inspected.

## Real customer profile isolation, logout and Why cards — 2026-10-02

- Owner reported a retry after browser Back, generic Google failure, a Baghdad Buyer profile and no visible logout. Root customer UI requires real backend authentication, but the customer snapshot adapter incorrectly merged a real numeric ID into a same-ID demo fixture. Null company/country fields inherited the demo name, address, flag and fake order metadata. This is a client fixture collision, not evidence of another real customer's records being disclosed.
- Replace all fields of a colliding record with server-authoritative identity or empty/default state; never borrow fixture details. Authenticated self-session/Magic Link responses now expose only own user name/email as read-only additions; profile PATCH still cannot alter user name/email or internal fields.
- Real customer dashboard omits browser-only photo/address editing and fabricated order timeline/payment actions; those are not implemented production order workflows. Name/company and WhatsApp retain existing guarded backend editing; email is read-only.
- Explicit logout is visible in Tracking header, uses the real server logout, disables while pending and reports failure without claiming logout.
- Invalid Socialite state now returns only a safe `auth_reason=expired` marker. Localized guidance starts a fresh login from the site rather than replaying browser Back. Authentication state validation is preserved; no stateless bypass.
- User explicitly requested visual repair of Why Armaghan. Test29 uses spaced mint cards, green 3px side accents, rounded corners and smaller numbered markers. Historical selectors/older numbered snapshots remain untouched; approved semantic colors and editor targets remain available.
- Root selector remains 29; revision bumped solely to repromote the updated active Test29. Test29 and root deployed and verified. Tests 01–28 remain frozen.
- Local type-check/build and 51 frontend tests across 13 files PASS. Backend CI #60 (37032219502), code deploy #16 (37032219436), and FTP #343 (37032219602), including Test29 and root promotion, PASS. Backend: 52 tests / 376 assertions. Fresh live status enabled:true, guest session 401, and invalid-state callback 302 to root with auth_reason=expired. Actual customer logout/repeat Google login acceptance remains OPEN.

| Rank | Profile method | Score | Reason |
|---:|---|---:|---|
| 1 | Authoritative full snapshot + own readonly identity | 9.5 | Removes actual ID-collision cause without account bypass |
| 2 | Clear browser data manually | 5 | Temporary, owner-dependent |
| 3 | Renumber demo customers | 4 | Future collisions remain possible |
| 4 | Hide every profile name | 3 | Conceals legitimate identity |
| 5 | Bypass OAuth state | 0 | Rejected security weakening |

Selected: option 1. Visual choice: semantic mint cards with green side dividers over one continuous blue-bordered block; preserve current four texts and editor registry.

Deployment closeout: implementation commit `8136892dc9c689c11f5d84a622236811a4f2b0d2`. Published active assets `index-BN1V-7yO.js` / `index-DMxFJMmU.css` passed HTTP verification; root version 29 HTML and selected assets verified, launcher still links Test29 first. Browser checked Persian Why cards on /t/29 (four rounded cards, 13.6px gap, no list border, 1px card border and 3px side accent); desktop overflow absent. Root browser reload confirmed the same new JS and localized expired-login guidance with a fresh sign-in button. Guest Tracking shows no demo customer profile. This is limited desktop visual acceptance, not full mobile/multilingual QA or real-account authenticated logout acceptance. Product/customer admin resources and product-image upload are implemented; production admin upload acceptance and complete customer order/payment workflows remain open.



## Google activation and frontend return repair — 2026-10-02

- Owner created the Google web client and installed private credentials in both `armaghan-data/.env` and `armaghan-backend/.env`; neither file nor secret is in Git. Live status now returns enabled:true.
- Read-only redirect check: HTTP 302 to accounts.google.com; expected client ID/callback, openid/profile/email and state present.
- Owner reported the Laravel welcome page after Google login. A fresh cancellation probe reproduced HTTP 302 to https://armaghantrading.com/backend/#/tracking?auth_error=google. Laravel prefixes frontend-relative redirects with the production /backend root.
- Repair uses the fixed production origin plus the existing strict root/numbered-test path allowlist for success, cancellation and invalid-state returns. No authentication bypass or email-only linking.
- Two regression cases force a /backend URL root and cover successful root login and cancellation to root/numbered paths, including hostile path fallback. Existing assertions now require the fixed production origin.
- Deployment/tests and live root cancellation probe PASS; see closeout evidence. Real Google signup/repeat login still OPEN; the screenshot is not proof of an authenticated customer session.
- Tests 01–28, Test29 assets, root selector, host credentials and database schema remain unchanged.

| Rank | Method | Score | Reason |
|---:|---|---:|---|
| 1 | Fixed frontend origin + existing strict path allowlist | 9.5 | Repairs actual subdirectory behavior without extra configuration |
| 2 | Configurable frontend origin | 8 | Flexible but adds a host setting |
| 3 | Change all backend base URL settings | 5 | Affects unrelated backend links |
| 4 | Redirect backend welcome route | 4 | Masks the wrong callback destination |
| 5 | Browser-side forwarding | 2 | Depends on loading another page |

Selected: option 1.


## Root Test29 release — current

- [x] Owner-authorized guarded root selector; preserve /t and frozen 01–28.
- [x] Root 29 and /t/29 deployment + exact HTML/initial asset verification; eight rollback/version tests.
- [x] Hide four Home eyebrows by default; editor shows them again with one action.
- [x] Concise producible badge: smaller, weight 400, centered; verified at root.
- [x] Keep root navigation/skip links at root; initial Home imagery loads.
- [x] Direct real Filament product/photo/customer navigation; retain backend authentication boundaries.
- [x] Official Google Socialite dependency, stable identity table, signup/sign-in, no admin/email-only linking; 49 backend tests + guarded additive deployment PASS.
- [x] Root only accepts real customer sessions; 49 frontend tests PASS.
- [x] Owner configures Google Cloud client + exact callback and private host credentials. Live status is enabled:true (2026-10-02).
- [ ] Actual Google signup/repeat login, authenticated product upload/customer edit; automation does not prove these.
- [ ] Verify/update customer Magic Link/FavoriteShare canonical root fragment paths against private environment; source historical defaults still /t/27.
- [ ] Complete deferred mobile/tablet and broad multilingual/theme visual acceptance.

# Armaghan — Product Backlog

## Test29 nonvisual release — current

- [x] Read actual state; preserve completed backend work.
- [x] Acquire shared lease and create snapshot/test28-final.
- [x] Freeze 01–28; promote build/deploy/launcher/runtime markers to Test29.
- [x] Isolate browser writes under armaghan:test29:*, including manual locale.
- [x] Localize Why Armaghan defaults for fa/ar/ku; retain en and override priority.
- [x] Add automated locale isolation/reload/default tests; carry dark contrast check.
- [x] FTP #328 / run 37003816665: 44 Vue tests, build, scoped deploy and published index/JS/CSS + first launcher link verified PASS; no visual acceptance inferred.
- [ ] Visual/device and authenticated-admin acceptance deferred by owner instruction; never infer PASS from automation.


## Test28 live QA follow-up — 2026-10-02

- [x] Localize the Persian Home eyebrow in Test29 source; frozen Test28 retains its historical text. Broad visual Home language acceptance remains open.

- [x] Confirm completed core capabilities from canonical repository state; no reimplementation.
- [x] Live desktop guest checks: 18 products, search 1 result, category filter 6 results, detail open/close, four locale directions, guest favorites reload, invalid-share state.
- [x] Measure dark-title defect: 4.279:1 on card surface; select approved mint semantic default; numeric check fails before and passes after repair.
- [x] Confirm this run's guarded build/deploy and fresh live dark-title color; see CURRENT-STATUS/HANDOFF section 45 evidence.
- [ ] Complete mobile/tablet acceptance and broader language/theme matrix; existing broad final QA tasks remain open.
- [ ] Authenticated editor save/reload/publish/restore, real product upload and customer Magic Link remain untested in this browser (login form, no session).


## Completed — bounded operational-memory reconciliation — 2026-10-02

- [x] Reconcile current main, latest handoff, backlog, Test28 hardening audit and shared lock.
- [x] Correct CURRENT-STATUS next-run instructions: Tests 01–27 frozen; Test28 active; backup/restore hardening already complete.
- [x] Correct HANDOFF opening phase pointer and record ranked method + exact remaining acceptance evidence (section 44).
- [x] Keep all final live QA tasks open; no inferred browser acceptance and no UI/backend/deployment changes.


## P0 — Final end-to-end QA + delivery handoff — next

- [ ] Validate Test29 mobile/tablet/desktop layout and interaction when visual testing is resumed.
- [ ] Validate Persian/Arabic/Sorani RTL and English LTR.
- [ ] Validate light/dark contrast and customer navigation.
- [ ] Validate catalog/product media fallback, customer session/Magic Link, FavoriteShare/WhatsApp and admin permission boundaries.
- [ ] Validate deployment lanes and backup/restore evidence in final delivery notes.
- [ ] Perform opportunistic authenticated-admin browser acceptance only if a valid real Filament session is available; never weaken auth.
- [ ] Produce final delivery/handoff status and close nonessential prototype/demo paths or clearly label them.

## P0 — Test 28 + off-host SQLite backup hardening — 2026-10-02

- [x] Select backup architecture: production `VACUUM INTO` snapshot → AES-256-GCM encryption before transfer → encrypted GitHub Actions artifact → isolated artifact-download restore drill.
- [x] Refuse plaintext SQLite artifacts because the repository is public.
- [x] Prefer dedicated `ARMAGHAN_BACKUP_PASSPHRASE`; until it exists, derive a domain-separated key from the existing GitHub-held FTP secret without logging/persisting the raw secret.
- [x] Add daily scheduled backup at 01:23 UTC with 14-day artifact retention and no remote plaintext export.
- [x] Add restore drill from the uploaded/downloaded artifact: ciphertext hash, plaintext hash, `PRAGMA integrity_check`, `foreign_key_check`, table inventory/counts and write-lock rollback.
- [x] Freeze Test 27 and promote source/runtime namespace to Test 28.
- [x] Set active build/deploy lane to `/t/28`; make both Vite and FTP deployment reject frozen Test 27.
- [x] Add Test28 visual-editor/customer-auth/FavoriteShare source contracts and a Test27 freeze/promotion contract.
- [x] Backend CI #53/#54 pre-merge and #55 post-merge PASS.
- [x] First real SQLite Off-host Backup #1 PASS; encrypted artifact `armaghan-sqlite-backup-36974268704`, artifact ID `11212796186`, size 337,663 bytes, retention through 2026-10-16.
- [x] Artifact round-trip restore drill PASS: ciphertext/plaintext checksums, SQLite integrity, foreign keys, table inventory/counts and write-transaction rollback verified; plaintext restore not retained.
- [x] FTP #318 stopped before deployment on stale historical Test26 assertion; corrected contract only.
- [x] FTP #319 stopped before deployment on two stale Test27 session-test fixtures; corrected fixtures only.
- [x] FTP #320 PASS: all frozen/Test28/backup contracts + TypeScript + 41 unit tests + build + FTP smoke; 112 Test28 files uploaded; root skipped.
- [x] Independently verify Test28 live, Test27 preserved, launcher Test28-first.
- [x] Reconcile `CURRENT-STATUS`, `HANDOFF`, `BACKLOG`, rules and release shared lock.
- [ ] P1 operational improvement: add dedicated `ARMAGHAN_BACKUP_PASSPHRASE`; retain recovery ability for fallback-encrypted artifacts until their 14-day expiry.

## P0 — Persisted FavoriteShare + WhatsApp — 2026-10-02

- [x] Select architecture: Backend FavoriteShare record + SHA-256 token hash + ordered product pivot + fragment-held share token + fixed POST resolve + WhatsApp handoff.
- [x] Add bounded issue/resolve/revoke service with active-product/taxonomy validation, ordered products, guest/customer origin, expiry and audit logging.
- [x] Use shorter guest TTL (7 days) than authenticated-customer TTL (30 days); guest shares intentionally rely on expiry while customer-owned shares have authenticated revoke.
- [x] Add CSRF-protected/rate-limited fixed POST issue + resolve endpoints and authenticated customer revoke endpoint.
- [x] Keep raw share token out of SQLite/logs/query strings/server paths; only the browser fragment carries it.
- [x] Replace Test27 product-code-in-URL generation with Backend persisted shares and `/favorites/share/:token`; retain old `?shared=v1:` links read-only for compatibility only.
- [x] Add one-tap WhatsApp handoff using the server-backed share URL and existing seller WhatsApp path.
- [x] Fix Favorites customer attribution to use the real `currentCustomerId`, never hard-coded Customer #1.
- [x] Add Backend lifecycle tests + frontend unit/source contracts for ordering, expiry, revoke, ownership, malformed tokens and shared-page resolution.
- [x] PR #9 Backend CI #51 PASS; squash merge `76f80179292f743cb54443e540602bba47c4d8bb`; Backend CI #52 PASS.
- [x] Backend Code Deploy #11 PASS with pre-swap snapshot/no drift/all existing smokes + FavoriteShare resolve GET 405; independent Production probe reconfirmed issue/resolve GET 405.
- [x] FTP Deploy #316 PASS: Test27 FavoriteShare contract + TypeScript/Vue/build/smoke/deploy PASS; root/Test26 untouched.
- [x] Live read-only invalid-token browser acceptance PASS: persisted share route rendered invalid/unavailable state without crash or legacy code-sharing.
- [x] Reconcile CURRENT-STATUS/HANDOFF/BACKLOG/rules and release shared lock.

## P0 — Customer session + Magic Link — 2026-10-02

- [x] Select architecture: same-origin Laravel session + database-backed high-entropy hashed one-time Magic Link; no customer password dependency and no new auth package.
- [x] Add bounded issue/revoke/consume service with expiry, scope, one-time atomic consume and audit logging.
- [x] Add customer-session middleware + `GET/PATCH /api/customer/session` + scoped logout.
- [x] Add active-admin issue/revoke API and native Filament Customer actions for 24h/72h/7d links.
- [x] Rate-limit public consume/admin issue endpoints; store only SHA-256 token hashes and never persist plaintext tokens.
- [x] Wire Test 27 to hydrate real Backend customer sessions while retaining demo/local fallback only when no server session exists.
- [x] Persist the Backend-supported customer profile subset from the customer dashboard; internal notes/user ownership stay server-internal.
- [x] Remove browser-issued Magic Link UI and explain that secure links are issued from the Backend.
- [x] Add feature/unit/source-contract coverage for issue, expiry, revoke, replay prevention, scoped session behavior, authorization and Test 27 hydration.
- [x] Backend Code Deploy #8 PASS for the initial live core.
- [x] Backend Code Deploy #9 detected production token-in-path 404 and automatically rolled back; rollback/health/cleanup PASS.
- [x] Repair the production route with browser-fragment token + fixed CSRF-protected POST consume endpoint; PR #8 merge `10d040c7...`.
- [x] Backend CI #49/#50 PASS; Backend Code Deploy #10 PASS with guest session 401 and fixed consume endpoint GET 405.
- [x] FTP Deploy #314 PASS; Test 27 auth/session regression contract + TypeScript/Vue/build/smoke/deploy PASS; root skipped.
- [ ] Opportunistic browser acceptance when a valid real admin session is available: issue one real customer Magic Link in Filament, open it in a fresh browser, verify session/profile update/replay rejection/logout.
- [x] Reconcile `CURRENT-STATUS`, `HANDOFF`, `BACKLOG` and release the shared lock.

Priority: **P0 current**, P1 next, P2 later.  
Current implementation targets: **Backend MVP productionization** + **Test 27 visual-editor/UI lane**. Test 26 is frozen.
Detailed ranked UX decisions: `docs/TEST26-UX-AUDIT.md`.

## P0 — Production product media + additive backend deploy — 2026-10-02

- [x] Select official Spatie Media Library + official Filament integration; lock dependencies through Composer, not hand-written package versions.
- [x] Add guarded additive Backend deployment lane: existing migrations byte-identical, new migration create-only, pre-migration SQLite snapshot, restore-on-migration-failure, code/public rollback and HTTP smoke.
- [x] Route Composer/migration drift away from the code-only updater; verify ordinary code-only lane remains green.
- [x] Add Product `product-gallery` ownership on public disk with paths under `media/products/<media-id>/`.
- [x] Limit uploads to JPEG/PNG/WebP, 8 MiB, 5000×5000, maximum 6 ordered files.
- [x] Re-decode and re-encode stored originals to strip metadata; normalize JPEG orientation.
- [x] Generate synchronous `card`/`thumb` conversions without destructive crop or upscaling.
- [x] Add official Filament multi-upload/reorder UI and product-list thumbnail.
- [x] Eager-load media in Public Catalog API and expose ordered `media` URLs.
- [x] Make Test 27 prefer the server gallery when present and retain local fallback only when server media is empty/API unavailable.
- [x] Backend CI #41 pre-merge and #42 post-merge PASS.
- [x] Backend Code Deploy #7 correctly skipped deployment on dependency/migration drift.
- [x] Backend Additive Deploy #3 PASS: snapshot, dependency change, 1 additive migration, public storage link, all HTTP smokes and cleanup PASS.
- [x] FTP Deploy #307 stopped before deployment on an external-URL unit-test fixture; no customer UI mutation occurred.
- [x] Same-origin fixture fix `b3722d3f...`; FTP Deploy #308 PASS with full Test27 build/smoke/deploy; root skipped.
- [x] Live API independently verified: all 18 Products return a `media` field; current arrays are empty until real admin uploads occur, so Test 27 fallback remains intact.
- [ ] Opportunistic acceptance when a real admin session is available: upload/reorder one real product image in Filament and verify it appears in Test 27 after reload.
- [ ] Next P0: minimum customer API/session + Magic Link core.

## P0 — Public catalog API + production bootstrap — 2026-10-02

- [x] Add read-only Public Catalog API Resources/Controller for active categories and products.
- [x] Add bounded product filtering/search/pagination and API throttling.
- [x] Return managed-code metadata so an intentionally inactive Backend row suppresses stale local fallback data.
- [x] Add Test 27 Hybrid Sync: Backend-managed product/taxonomy data overlays local state; Backend failure keeps the existing customer page usable.
- [x] Preserve local product media/specs during staged sync; Backend domain data owns names/status/active state.
- [x] Backend CI #35 PASS; FTP Deploy #295 PASS with TypeScript/unit/Test 27 production build and `/t/27` deployment; root skipped.
- [x] Backend Code Deploy #4 PASS; public catalog category/product endpoints both HTTP 200.
- [x] Add guarded `armaghan:bootstrap-catalog` command with dry-run default, exact empty-table precondition, transaction and 3/6/18 count contract.
- [x] Backend CI #37 + Backend Code Deploy #5 PASS.
- [x] Catalog Bootstrap Production #1 PASS: before 0/0/0 → consistent SQLite snapshot → after 3/6/18; representative category/subcategory/product codes verified; temporary helper cleanup PASS.
- [x] Independently re-read live API: 3 managed categories, 6 managed subcategories, 18 managed products.
- [x] Independently render live Test 27 Products page after bootstrap: 18 products displayed with customer-facing subcategory/unavailable presentation intact.
- [x] Remove one-shot production bootstrap workflow after successful initialization; reusable helper remains fail-closed because production catalog is no longer empty.
- [x] Production product-media ownership/upload capability completed and wired to Test 27; customer API/session is now the next P0.

## P0 — Test 27 customer visual corrections — 2026-10-01

- [x] Create rollback checkpoint `rollback/test27-pre-customer-ui-corrections`.
- [x] Make Footer brand area logo-only; remove customer-facing brand name/description beside the logo.
- [x] Centralize customer product labels: available → localized six-way subcategory; non-available/made-to-order → unified unavailable/producible label.
- [x] Keep canonical `Product.name` data intact for backend/admin ownership.
- [x] Restyle product codes as high-contrast Brand Blue/White capsules with restrained Brand Gold border.
- [x] Add persisted `showSubcategoryCodes` Feature Flag; defaults OFF on mobile/tablet/desktop; preserve schema-v4 choices during migration.
- [x] Default Products page to Brand White and product cards to Brand Mint in light mode; preserve dark-mode surfaces.
- [x] Register `products.page` and existing `product.card` as structured visual-editor surfaces.
- [x] Apply strong brand-derived green heading role on light content surfaces without reducing contrast on dark/image/brand-chrome surfaces.
- [x] Make Why Armaghan dividers 3px Brand Blue and number circles Brand Blue with Brand Gold numbers.
- [x] Add unit/source-contract coverage for presentation helper, schema migration, feature flag, editor targets and semantic CSS roles.
- [x] Confirm main CI/build/FTP deploy of Test 27 and record the workflow run — FTP Deploy #277 PASS; 112 files uploaded to `/public_html/t/27` + launcher; no remote delete; root deploy skipped.

### Test 27 customer visual corrections delivery record

- [x] Main implementation commit: `972d66bcd6e0e3ab72946d2fd14ffe8a6d329052`.
- [x] FTP Deploy #275 stopped before build/deploy because the historical Test 23 contract still asserted the old current-source title expression; no remote mutation occurred in that failed run.
- [x] Historical Test 23 contract was future-proofed in `fb7ee2a1c4200f8080c68e37e10edb238cda9833`; FTP Deploy #276 QA path PASS.
- [x] Validated release marker: `65b9f1158019aece79438fbc680af4070e0c0665`.
- [x] **FTP Deploy #277: SUCCESS** — immutable guard and Test 11–27 contracts PASS; TypeScript, Vue unit tests, generated portrait media and Test 27 production build PASS; FTP smoke PASS; `deploy-t` PASS; `deploy-root` skipped.
- [x] Deployment uploaded **112 files** to active Test 27 plus mutable launcher and explicitly deleted **0 remote files**.
- [x] Test 26 and earlier frozen snapshots remained untouched.
- [x] Final heading-green polish commit: `13b3dbb5abd32265f752139b9cf670127a8ce5c9`; **FTP Deploy #282 PASS** with the full Test 27 QA/build/smoke/deploy lane. Strong light-surface headings now resolve to a visibly dark-green semantic role rather than the earlier blue-leaning mix.

Image requirements: `docs/TEST26-IMAGE-REQUIREMENTS.md`.
Customer clarification script: `docs/TEST26-CUSTOMER-QUESTIONS.md`.
Rollback checkpoint: `rollback/test25-pre-test26` → `328f6c48ea9c4f1346702282ddb6d21ae1685a16`.


## P0 — Frozen Test 26 / CI release-lane repair

Detailed ranked analysis: `docs/CI-FREEZE-REPAIR.md`.

- [x] Add Test 26 to the immutable-test registry.
- [x] Convert the historical Test 22 contract from current-source assumptions to historical release invariants.
- [x] Future-proof historical Test 20/21/23/24/25 contracts so they no longer enumerate the current build target or stop at Test 26 namespaces.
- [x] Convert the Test 26 source contract into a frozen-handoff contract that no longer blocks Test 27+ source evolution.
- [x] Remove active CI build/deploy paths that can regenerate `/t/26` from main.
- [x] Set Test 27 as the next explicit CI UI release lane while keeping the launcher unchanged until a real Test 27 UI change is approved.
- [x] Make local Vite builds write to `.build/frontend` unless an unfrozen numbered target is explicitly supplied.
- [x] Exclude Vite-config-only maintenance from automatic numbered-test deployment; explicit workflow dispatch remains available.
- [x] Propagate terminology rules 75–77 into root `AGENTS.md`.
- [x] Confirm the repair commit's GitHub Actions run is green before starting Backend MVP mutations.

### CI/freeze repair delivery record

- [x] Final repair head: `8065ed94aee9f603217e4131ca0460b55aac414d`.
- [x] GitHub Actions **FTP Deploy Run #244: SUCCESS**.
- [x] All QA contracts passed, including future-proofed Test 20–25 historical contracts and the frozen Test 26 handoff contract.
- [x] FTP smoke test passed.
- [x] `deploy-t` and `deploy-root` were both skipped; Test 26 remained untouched and Test 27 was not published.
- [x] Runs #241–#243 exposed brittle historical assertions during the repair; all failed before deployment and were used only as feedback to harden the contracts.

## P0 — Backend hosting preflight

- [x] Verify PHP >= 8.3 and Laravel-required PHP extensions — PHP 8.3.33; all required extensions PASS.
- [x] Verify safe web/private layout — `public_html` is the active document root and PHP can read/write a private sibling outside it.
- [x] Verify HTTPS and deployment capabilities — HTTPS PASS; CI-built Composer/vendor path selected; web shell functions are disabled. Original 2026-09-30 probe had PDO SQLite unavailable and PDO MySQL available.
- [x] Owner reports PDO SQLite was enabled on 2026-10-01; architecture switched back to SQLite primary.
- [ ] Re-run production SQLite probe immediately before first production migration: confirm `pdo_sqlite`, private DB path write, foreign keys and backup path write.
- [ ] **Production SQLite Reprobe #1: INCONCLUSIVE** — FTP/private sibling/cleanup PASS, but the temporary PHP probe could not be reached through a canonical web URL. Do not treat this as SQLite FAIL or PASS. Retry later using the confirmed canonical application URL or deployed backend health route.

## P0 — Laravel 13 / Filament 5 bootstrap

- [x] Generate Laravel through Composer on an isolated bootstrap branch rather than hand-writing framework files.
- [x] Install Laravel Framework **13.34.0** under `platform/backend`.
- [x] Install Filament **5.9.0** Panel Builder and generate `AdminPanelProvider`.
- [x] Preserve SQLite as local/dev/test default and add a production MySQL/MariaDB environment template with no credentials.
- [x] Verify Laravel `/up` health route.
- [x] Run bootstrap Laravel tests: 2 passed / 2 assertions.
- [x] Run locked Composer security audit: no known vulnerability advisories.
- [x] Add permanent Backend CI for Composer validation, migrations, tests, version checks, audit and secret hygiene.
- [x] Replace generated nested agent instructions with Armaghan project rules; do not auto-install Laravel Boost.
- [x] Confirm no Test 26/Test 27/launcher mutation during bootstrap.
- [x] Build the Armaghan core domain migrations/models from the approved schema draft, adapted portably for SQLite dev/test + MySQL/MariaDB production.
- [x] Add production-safe Filament access gate: only active users with admin role can enter the admin panel; no repository credential is seeded.
- [x] Add secret-free-in-repo first-administrator provisioning: fail-closed Artisan command + encrypted one-shot production provisioner; no seeded/default password.



### Domain foundation delivery record

- [x] Customer, Category, Subcategory, Product, SpecDefinition, ProductSpecValue, FavoriteShare, MagicLink and ActivityLog foundations added.
- [x] Favorites-share tokens and magic-link tokens are hash-only database values.
- [x] Product media table intentionally deferred to Spatie Media Library to avoid duplicate media ownership.
- [x] Orders/timeline remain deferred unless delivery requires them.
- [x] DatabaseSeeder no longer creates a default fixed user.
- [x] Backend CI Run #3: **PASS** — 4 tests / 23 assertions; Composer audit clean.
- [x] No production MySQL migration and no Test 26/Test 27 change occurred.
- [x] Filament Product/Customer/Category/Subcategory Resources are implemented, CI-verified and live in production. First-admin provisioning remains complete.

- [x] First bounded Filament CRUD batch: Category + Subcategory Resources — commit `4281034656ead0c0538b4f1dc4e607ea5f161438`; Backend CI #31 PASS.
  - create/edit/list/search/sort/filter implemented;
  - parent Category relation uses Filament relationship select;
  - destructive delete actions intentionally omitted because Category → Subcategory cascades;
  - active-admin access tests and non-admin denial tests PASS.
- [x] Taxonomy Resources activated in production through guarded Backend Code Deploy #2; health/login/categories/subcategories HTTP smoke all 200.
- [x] Product + Customer Resources implemented in commit `de450a15c339cb8a3460c7c3f7f45d01a7074158`; Backend CI #33 PASS.
- [x] Product + Customer Resources activated through Backend Code Deploy #3; health/login/categories/subcategories/products/customers HTTP smoke all 200.
- [x] Guarded code-only backend updater production-proven: Composer/migration drift refusal, SQLite pre-swap snapshot, staged code swap, HTTP smoke, automatic rollback and temp cleanup.
- [x] Backend Code Deploy #1 exposed a public-directory permission regression and automatically rolled back successfully; fix `f6c997ae9496a0cc48ff8be6a1e190278d02e115` normalized public root to 0755 before successful Runs #2/#3.
- [ ] Add a migration-aware update variant only when a future release actually changes migrations or dependencies; the current code-only lane intentionally fails closed on such drift.




## P0 — SQLite-first persistence

Canonical architecture: `docs/SQLITE-FIRST-PERSISTENCE.md`.

- [x] Select SQLite as the primary Laravel database for local/dev/test/production after host SQLite enablement.
- [x] Remove the superseded custom JSON runtime-store layer before any production data used it.
- [x] Keep JSON as ordinary import/export/fixture interchange only.
- [x] Add `armaghan:backup-sqlite` using SQLite `VACUUM INTO` for consistent private snapshots.
- [x] Backend CI Run #17: PASS after SQLite-primary pivot and backup command tests.
- [x] FTP Deploy Run #258: PASS; only `/public_html/t/index.htm` uploaded; no remote files deleted; root deployment skipped.
- [x] Add backup command tests.
- [x] Make production example use an absolute private SQLite path and private backup path.
- [x] Re-probe production `pdo_sqlite` and private-path write access before first live migration — PDO SQLite, SQLite 3.53.4, private file R/W, foreign keys and `VACUUM INTO` all PASS.
- [x] Configure the real production SQLite file path in the host-only shared `.env`; the path remains private and is not stored in Git.
- [ ] Configure backup retention + at least one off-host rotated copy. Production code deploys and catalog bootstrap already create consistent on-host SQLite snapshots.
- [ ] Add optional MySQL logical mirror/export only after the SQLite production path is stable; never dual-write in live requests.
- [ ] Add mirror verification (row counts/checksums + restore drill) when MySQL mirror is implemented.

## Delivery roadmap — remaining ~6 core runs

Current estimate excludes open-ended new customer redesign requests. Filament CRUD/media may split into two bounded runs, making the practical range about 6–7 runs.

1. [x] **Urgent Test 27 editor access** — persistent admin quick-launch button, structured target browser, direct visibility toggles, live Test 27 deployment.
2. [x] **Visual Style Profile frontend + production service foundation** — local-first adapter, checksum conflict handling, staging Publish/history/Restore UI, production Laravel/SQLite deployment and live service-level persistence acceptance all PASS.
3. [x] **Production SQLite/Laravel + first admin** — PDO SQLite/SQLite 3.53.4 PASS, private DB/backup active, migrations/snapshot PASS, `/backend` healthy, one real active production admin provisioned securely.
4. [ ] **Browser-authenticated editor acceptance** — real Filament session + CSRF, Test 27 server autosave, reload/cross-device draft, staging Publish and Restore through the actual UI. This is the only remaining Style Profile persistence acceptance.
5. [~] **Filament CRUD + catalog/customer API/media** — Product/Category/Subcategory/Customer Resources and public catalog reads are complete/live; production product-media ownership/upload and customer API/session wiring remain.
6. [x] **Favorites/WhatsApp + Customer Magic Link** — Customer session/Magic Link and persisted FavoriteShare/WhatsApp handoff are live with hash-only tokens, expiry/revoke, ordered products and audited/rate-limited endpoints.
7. [ ] **Production hardening + final QA/handoff** — rotated off-host SQLite backup + restore drill, repeatable backend update workflow with pre-migration snapshot/rollback guard, responsive/RTL/LTR/light/dark/permissions QA, final customer handoff.

### Production backend activation delivery record

- [x] Fresh production hosting probe: PASS.
- [x] Build locked Laravel 13.34.0 + Filament 5 release in CI with production Composer dependencies.
- [x] Generate and preserve APP_KEY only on the host; no APP_KEY or production `.env` entered GitHub or logs.
- [x] Create private production SQLite database and private backup directory outside `public_html`.
- [x] Run production migrations successfully.
- [x] Create first consistent SQLite snapshot.
- [x] Verify core schema: users/products/customers/style profile tables.
- [x] Expose only Laravel `public` surface under `/backend`.
- [x] Repair public permissions to LiteSpeed-safe `0755/0644`; private state remains private.
- [x] HTTP smoke PASS: backend root, health, public Style Profile API and Filament login page.
- [x] Provision the first real active admin through a one-time secure bootstrap flow; no seeded/default password and no plaintext credential stored in Git/repo logs.
- [x] Point Test 27 Style Profile API base to `/backend`; production SQLite Style Profile save/publish/restore semantics verified transactionally on the live database with full rollback.
- [ ] Complete the final browser-authenticated acceptance cycle: real Filament session + CSRF, edit Test 27, reload/cross-device, staging Publish and Restore.
- [ ] Configure rotated off-host SQLite backup copy + restore drill.
- [x] Add safe repeatable **code-only** backend release/update workflow with SQLite pre-swap snapshot, fail-closed migration/dependency drift detection, HTTP smoke and automatic code rollback. Migration-aware releases remain intentionally separate.

## P0 — Test 27 color/saveability customer request

- [x] Derive the canonical blue from the actual repository logo rather than guessing. Pixel probe result: image-dominant logo background `#0714C2`.
- [x] Replace canonical Brand Blue `#151EDA` with logo-background blue `#0714C2` in Test 27 semantic tokens/default balanced palette.
- [x] Add semantic Home page background role; light-mode default = Brand White.
- [x] Make the whole Home page background a registered editor target (`home.page`) with background-color control.
- [x] Add `home.content` separately so Home content can be controlled without hiding the page root.
- [x] Protect `home.page` from Hide to prevent an unrecoverable/blank editing state.
- [x] Add semantic panel-background role; default primary Home panels = Brand Mint.
- [x] Apply Mint panel default to About copy, Why list and Capability cards while retaining token-based per-target overrides.
- [x] Expand nested editable targets for Why items and Capability card titles/texts.
- [x] Recover hidden dynamic/unregistered targets through the structured target browser.
- [x] Restrict Inspector controls per target; e.g. page root exposes only background color.
- [x] Default Test 27 Style Profile API base to same-origin `/backend`.
- [x] Add CSRF token bootstrap/retry for authenticated backend Style Profile writes.
- [x] Add sanitized Style Profile JSON export/import as a simple portable backup/transfer mechanism; do not revive JSON runtime persistence.
- [x] Branch CI: latest Test 27 Editor Color Saveability runs PASS.
- [x] Backend CI Run #19: **PASS** after adding the CSRF bootstrap endpoint and Style Profile API test coverage.
- [x] Historical Test 24/25 color contracts made token-value agnostic so a customer-approved Brand Blue value change does not regress frozen semantic contracts.
- [x] **FTP Deploy Run #268: PASS** — full QA/build/smoke passed; active Test 27 rebuilt and uploaded to `/public_html/t/27`; 112 files uploaded; no remote files deleted; root deploy skipped.
- [x] Provision the first real Laravel administrator without a seeded/default password; account `admin@armaghan.local` is active in production.
- [ ] Log into the real backend admin session and verify Test 27 draft autosave through the browser; **server-side production persistence itself is already acceptance-tested PASS**.
- [ ] Verify cross-device reload from shared draft, staging Publish and Restore end-to-end.
- [ ] After successful shared persistence QA, decide whether JSON export/import remains visible by default or moves under an advanced/backup disclosure.


### First-admin provisioning delivery record

- [x] Backend CI: secure provisioning command/tests, PHP helper syntax, dependency audit and secret hygiene PASS.
- [x] First bootstrap attempt created the intended admin but failed before credential handoff because the temporary RSA public key was malformed; no plaintext credential was logged.
- [x] Inspector run confirmed the only active admin exactly matched the bootstrap identity and timestamp: `admin@armaghan.local`, `Armaghan Administrator`, created at 2026-10-01T13:35:31Z.
- [x] Guarded recovery rotated only that exact account using email + name + creation-time proof.
- [x] Recovery generated the password on-host, encrypted it before mutation, stored only the Laravel hash, and returned only RSA ciphertext through CI.
- [x] Active-admin policy and password hash verification PASS; `/backend/admin/login` HTTP smoke PASS.
- [x] One-shot provisioning workflow removed after success.
- [x] Final main validation: **Backend CI #30 PASS**.
- [x] **FTP Deploy #270 PASS**; this backend/security batch did not modify or redeploy Test 26/27 UI.
- [ ] Acceptance remaining: authenticate in the real backend session from the browser and verify Test 27 shared Style Profile save/reload/publish/restore end-to-end.

### Production Style Profile acceptance delivery record

- [x] Add reusable token-protected FTP/HTTP acceptance helper: `platform/scripts/verify_style_profile_ftp.py`.
- [x] Helper never creates an authenticated browser session and contains no auth bypass.
- [x] Run the acceptance sequence against the **live production SQLite database** inside one outer transaction.
- [x] Draft save PASS.
- [x] Consecutive staging Publish sequence PASS.
- [x] Restore creates a newer immutable version PASS.
- [x] Staging publication pointer follows the restored version PASS.
- [x] Inside the transaction: **3 immutable versions + 3 ActivityLog records** created as expected.
- [x] Outer rollback PASS; logical database state after the test exactly matched the pre-test state.
- [x] Temporary public helper cleanup PASS.
- [x] Production Style Profile Acceptance Run `36878931948`: PASS.
- [x] Main post-merge validation: **FTP Deploy #272 PASS**; QA/smoke passed and both UI/root deploy jobs were skipped.
- [ ] Only remaining editor persistence acceptance: real browser Filament login/session + CSRF + cross-device UI cycle.


## P0 — Test 27 visual style editor foundation

Detailed architecture and ranked decisions: `docs/TEST27-VISUAL-EDITOR.md`.

- [x] Create rollback checkpoint `rollback/test26-pre-test27-visual-editor` before the first Test 27 UI change.
- [x] Select a non-modal resizable Bottom Sheet as the primary mobile inspector; reject a fully draggable floating inspector as the main mobile UI.
- [x] Add approved five-color token registry. Current canonical Test 27 palette: `#21946A`, logo-background Brand Blue `#0714C2`, `#C8E3DB`, `#FFFFFF`, `#FFB514`; the earlier `#151EDA` blue is retired.
- [x] Route shared navy/blue brand chrome for Header, Footer and Hero text bar through `--role-brand-chrome`.
- [x] Add admin-only Visual Editor launcher under Appearance; editor is off by default.
- [x] Keep the editor UI/listeners removable while persisted style/content overrides continue through a separate runtime layer.
- [x] Add touch-neighborhood selection with `document.elementsFromPoint()` and an explicit candidate chooser for nested/nearby elements.
- [x] Add text editing for explicitly registered text targets, stored per locale.
- [x] Add approved-token text/background/border controls, hide/show and per-element reset.
- [x] Keep hidden targets recoverable from a dedicated hidden-elements list.
- [x] Persist a structured style profile plus generated CSS; do not accept arbitrary CSS/HTML/JS input.
- [x] Isolate Test 27 browser state from Test 26 by moving mutable prototype keys to `armaghan:test27:*`.
- [x] Add permanent `tests/test_test27_visual_editor_contract.py`.
- [x] Staged branch validation PASS: source contract, TypeScript, 25 unit tests and Test 27 Vite build.
- [x] Expand stable editable-target coverage across Home/About/Why/Capabilities/product banners plus major card/panel surfaces; keep domain-owned product/customer values outside free-form visual text.
- [x] Add WCAG-normal-text contrast guard at 4.5:1 for explicit token text/background pairs, with blocked unsafe assignments and visible feedback.
- [x] Persist/version Style Profiles through Laravel with mutable draft, immutable versions, staging/production publication pointers, admin-only writes, server-side safe CSS compilation, activity log and checksum conflict protection.
- [x] Connect the Test 27 visual-editor store to the Laravel Style Profile API through a local-first adapter: conservative public staging baseline, 900ms debounced authenticated draft autosave, expected-checksum 409 conflict handling, staging Publish, history/Restore and local fallback.
- [x] Visual editor structured target controls foundation: grouped browser for header/hero/about/why/capabilities/product banners/product cards/footer, direct ON/OFF visibility, text/background/border/token editing through the existing inspector, and hidden targets recoverable.
- [x] Add sanitized profile JSON Export/Import plus backend immutable version-history/Restore UX; keep JSON as interchange/backup only, not runtime persistence.

### Style Profile frontend adapter delivery record

- [x] Add same-origin API client with cookie/session credentials and optional XSRF header support; no auth bypass.
- [x] Public staging profile is adopted only when the local browser has no edits or still matches its previous server baseline; local work is never silently overwritten.
- [x] Admin sync checks the real Laravel admin endpoint; 401/403 or API absence falls back to browser-local persistence without disabling the editor.
- [x] Draft changes debounce for 900ms and use `expected_checksum`; HTTP 409 becomes an explicit local-vs-server choice.
- [x] Add staging Publish and version-history Restore controls; restore remains create-new-version semantics from the backend.
- [x] Add sync status UI: checking / local / saving / synced / conflict / error.
- [x] Branch validation PASS: Test 27 contract, TypeScript, **30/30** Vue unit tests, numbered Test 27 build.
- [x] **FTP Deploy Run #262: PASS** — active Test 27 rebuilt and deployed to `/public_html/t/27`; 112 files uploaded; no remote files deleted; root deploy skipped.
- [ ] Complete the only remaining Style Profile acceptance: real browser Filament login/session + CSRF, edit/save in Test 27, reload, verify cross-device draft, Publish staging and Restore. Production server-side save/version/restore semantics are already acceptance-tested PASS.

- [x] Add Test 27 to the mutable test launcher after explicit owner approval on 2026-10-01; keep Test 26 frozen.
- [x] Add persistent admin-only «ویرایش ظاهر» quick launcher so a logged-in admin can open the Test 27 editor from any page and be routed to Home automatically.
- [x] Add grouped element browser inside the bottom sheet so nearby/nested elements do not need precise finger selection.
- [x] Branch-only urgent validation PASS: visual-editor contract, TypeScript, unit tests and Test 27 build.
- [x] **FTP Deploy Run #260 attempt 2: PASS** — Test 27 rebuilt and deployed to `/public_html/t/27`; mutable test index updated; 112 files uploaded; no remote files deleted; root deploy skipped.
- [x] Run #260 attempt 1 stopped before build/deploy only because the GitHub runner timed out downloading Pillow; failed jobs were retried and the complete pipeline then passed.

### Style Profile backend delivery record

- [x] Architecture/validation record: `docs/STYLE-PROFILE-BACKEND.md`.
- [x] Add `style_profiles`, `style_profile_versions`, `style_profile_publications` migrations/models.
- [x] Public read endpoint for staging/production publications.
- [x] Admin-only draft/save/publish/restore endpoints using the persisted active-admin gate.
- [x] Reject arbitrary CSS and unknown payload keys; compile CSS only from approved palette tokens.
- [x] Add optimistic checksum conflict response (HTTP 409) to prevent stale-editor overwrite.
- [x] Restore historical versions by creating a new immutable version; do not rewrite history.
- [x] Fix pre-existing ActivityLog model/table mismatch without rewriting historical migration.
- [x] Backend CI Run #10: **PASS** — 12 tests / 84 assertions; Composer audit clean; secret hygiene PASS.
- [ ] Next bounded batch: Vue/Test 27 persistence adapter + staging Publish/history/Restore UX.



### Test 26 Run 8 — footer scope + mobile language + terminology rule

- [x] Rollback checkpoint: `rollback/test26-pre-footer-language-terminology` → `a00666040922d248d3e3e5d68490d26b797ef339`.
- [x] Mobile Footer is shown only on Home; tablet/desktop retain the current Footer behavior on inner pages.
- [x] Existing `showFooter` Admin setting remains the top-level visibility control.
- [x] Mobile compact header now shows the language selector in the blue top bar when `showLanguage` is enabled.
- [x] Mobile drawer suppresses the duplicate language selector; tablet/desktop behavior remains unchanged.
- [x] Permanent communication rules 75–77 added: first use of each specialist term in user-facing project reports must include a short Persian explanation in parentheses.
- [x] Feature Flag vs hard-coded terminology distinction recorded in the audit and project rules.
- [x] Ranked five-option implementation analysis recorded in `docs/TEST26-UX-AUDIT.md` Run 8.
- [x] Final Test 26 QA/build, FTP smoke and scoped deploy passed.

### Test 26 Run 8 delivery record

- [x] Rollback checkpoint: `rollback/test26-pre-footer-language-terminology` → `a00666040922d248d3e3e5d68490d26b797ef339`.
- [x] Release head: `5a7ca47291b64bd70cf1fed2b4a32f94e9753798`.
- [x] **FTP Deploy Run #234: SUCCESS** — Test 11–26 contracts, TypeScript, unit tests, Vite Test 26 build and FTP smoke passed; scoped `deploy-t` passed; `deploy-root` skipped.
- [x] Mobile Footer is Home-only; non-mobile Footer behavior remains unchanged.
- [x] Mobile top bar exposes the language selector; mobile drawer does not duplicate it.
- [x] Permanent terminology-explanation rules 75–77 are active in `docs/PROJECT-RULES.md`.
- [x] Build marker / launcher query: `test26-footer-language-r8a`.
- [x] Test 25 and earlier snapshots remain unchanged.

### Test 26 Run 7 — mobile UX polish delivery

- [x] Rollback checkpoint: `rollback/test26-pre-mobile-ux-polish` → `54e2075b67ae1c21b0bac50fe32b9cd1e5144864`.
- [x] Header Mode and hamburger controls are editable again per viewport.
- [x] Customer default for mobile is compact header with hamburger **off**; schema v4 migrates the previously forced schema-v3 state while preserving unrelated Appearance choices.
- [x] BottomNav remains mobile-only and now has both live viewport-profile visibility and explicit CSS width fallback for responsive desktop emulation.
- [x] Mobile Test 26 footer is full-bleed / bottom-flush; tablet and desktop keep the existing inset white gutters.
- [x] Product title and code remain separate rows but are both centered.
- [x] Active subcategory/filter chips use readable foreground text on the pale mint surface; no white-on-pale active text.
- [x] Ranked alternatives and rationale recorded in `docs/TEST26-UX-AUDIT.md` Run 7.
- [x] Release commit: `633e7e1399db1e6cca84256bcfdd516004088476`.
- [x] **FTP Deploy Run #223: SUCCESS** — full QA, TypeScript, unit tests, Vite Test 26 build and FTP smoke passed; `deploy-t` passed; `deploy-root` skipped.
- [x] Test 25 and earlier snapshots remain unchanged.

## P0 live regression — Test 26 still showing expanded mobile header + IMAGE REQUIRED

- [x] Screenshot diagnosis: captured width is 653px, below the 768px mobile/tablet boundary, so an expanded primary header there is persisted-state regression, not a breakpoint interpretation.
- [x] Media diagnosis: `SmartImage.vue` was advertising guessed AVIF siblings for WebP files. Test 26 selected images are WebP-only, so browsers supporting AVIF could request a nonexistent file and fall through to the remote placeholder.
- [x] Restrict AVIF source generation to asset families that actually ship AVIF siblings.
- [x] Bump Appearance schema to v3 and enforce navigation placement during migration and subsequent Admin updates: mobile compact+BottomNav; tablet/desktop expanded top navigation.
- [x] Lock only the primary navigation placement in Admin while preserving the other per-device Appearance controls.
- [x] Add local Test 26 media existence + AVIF-safety regression assertions.
- [x] Add cache-busted launcher URL and Test 26 build marker/no-cache hints.
- [x] Final QA, build, FTP smoke and scoped Test 26 deployment passed.

### Live P0 screenshot-fix delivery

- [x] Rollback checkpoint: `rollback/test26-pre-live-p0-fix` → `a82a209901b70b4bbb2dbfb9f0cfad7669f7d101`.
- [x] SmartImage AVIF root-cause fix: `8da6847156242e84e072ac207e857bdb2ec408ce` plus Test 25 compatibility follow-up `596c86bf1009741a95d1c76ec168bc5c4519104e`.
- [x] Appearance navigation invariant / schema v3: `5773e92a78b89830910b74dfbe597f136288d0b8`.
- [x] Cache-busted launcher and fresh-document marker are included in the final build.
- [x] **FTP Deploy Run #208: SUCCESS** — Test 11–26 QA, TypeScript, unit tests and Vite Test 26 build passed; FTP smoke passed; `deploy-t` passed; `deploy-root` skipped.
- [x] Test 25 and older snapshots were not modified.

## P0 regression hotfix — mobile PWA bottom navigation

- [x] Diagnose the regression: the original `BottomNav.vue` and its mobile styling were still present; Test 26 accidentally kept them visible through the tablet range and kept tablet on compact-drawer navigation.
- [x] Preserve the exact existing mobile BottomNav component/visual treatment; change only its visibility boundary from `lg:hidden` to `md:hidden`.
- [x] Make the BottomNav mobile-only: visible below 48rem/768px, absent on tablet and desktop.
- [x] Remove tablet floating BottomNav geometry and bottom spacing from 48rem upward.
- [x] Change Test 26 customer-default tablet navigation to the expanded blue top header with no hamburger.
- [x] Add a one-time appearance schema migration so browsers that already opened Test 26 do not remain stuck on the mistaken tablet default.
- [x] Preserve Admin per-viewport overrides for the top header; do not make the BottomNav a decorative toggle.
- [x] Add unit and source-contract coverage at the 767/768/1024 boundaries.
- [x] Final CI, FTP smoke and scoped `/t/26` deployment passed.

### Mobile PWA BottomNav hotfix delivery

- [x] Rollback checkpoint: `rollback/test26-pre-mobile-bottomnav-fix` → `b89f52b92774a376a86925a82f72553cdb3235a3`.
- [x] Implementation commit: `453805ae591586d7e2d70f6ed4d82e69dece559d`.
- [x] Contract-alignment commits: `7b6deabaa8fb77bb45ffd30b2b3e8ba83ecea13b` and `0e64894d886ef9b2bf96b84b1dcf106d8213f7b9`.
- [x] Deployment-trigger / regression-selector commit: `c49a37c0aa0e56759e4a3418c8c798232f4e64b5`.
- [x] **FTP Deploy Run #195: SUCCESS** — full QA/build passed, FTP smoke passed, `deploy-t` passed, `deploy-root` skipped.
- [x] Mobile BottomNav remains the same five-item app-like navigation below 768px; tablet/desktop use the blue top navigation.
- [x] Existing Test 26 browsers migrate the old tablet compact default to expanded top navigation; other tablet appearance choices are preserved.
- [x] Test 25 and earlier frozen snapshots were not modified.

## P0 — Test 26: reversible homepage + device-aware appearance controls

### Planning / repository memory
- [x] Read and reconcile `PROJECT-RULES.md`, `HANDOFF.md`, `BACKLOG.md`, asset architecture and current Test 25 source structure before changing implementation.
- [x] Freeze Test 25 as the last delivered snapshot.
- [x] Create rollback branch `rollback/test25-pre-test26` at `328f6c48ea9c4f1346702282ddb6d21ae1685a16`.
- [x] Record five-option ranked architecture analysis in `docs/TEST26-UX-AUDIT.md`.
- [x] Record customer-ready ambiguity questions in `docs/TEST26-CUSTOMER-QUESTIONS.md`.
- [x] Record all unresolved marketing images, dimensions, target paths, formats and `placehold.co` fallbacks in `docs/TEST26-IMAGE-REQUIREMENTS.md`.
- [x] Codify repository-memory, reversible-choice and viewport-profile rules in `docs/PROJECT-RULES.md`.

### Batch 26A — typed reversible configuration foundation
- [x] Add typed appearance policy for mobile / tablet / desktop.
- [x] Add one viewport-profile resolver aligned with 48rem / 64rem breakpoints.
- [x] Add Test 26 appearance Pinia store under `armaghan:test26:appearance`.
- [x] Add validated persistence/migration and reset-to-customer-default behavior.
- [x] Add unit tests for defaults, per-device resolution, reset and invalid persisted state.
- [x] Make no visible page redesign in this batch.

### Batch 26B — Admin Appearance controls
- [x] Add a dedicated Admin → Appearance view, separate from content/translation editing.
- [x] Add Mobile / Tablet / Desktop tabs.
- [x] Add only high-value controls: header mode/actions, hero mode, Home product grid, category numbers and main Home section visibility.
- [x] Prevent impossible navigation states in control validation.
- [x] Add “Reset this device” and “Reset all to customer defaults”.
- [x] Keep all controls keyboard/focus accessible and localized.

### Batch 26C — modular Home
- [x] Refactor `HomeView.vue` into composition-only section assembly.
- [x] Add `HeroSection.vue`; customer default is one hero image while preserving `HeroCarousel.vue` as an alternate mode.
- [x] Default-hide the Home product grid while preserving the `recommended-6` alternate mode.
- [x] Add About Armaghan using the supplied final Persian copy.
- [x] Add Why Armaghan with the supplied four value propositions.
- [x] Add three capability cards with structured detail content.
- [x] Add three horizontal product-category banners that route into the relevant Products category.
- [x] Use only documented Test 26 local paths + `placehold.co` fallbacks for unresolved images.
- [x] Keep section visibility independently configurable per viewport.

### Batch 26D — Header + Products confirmed-safe changes
- [x] Desktop default: expanded header with direct language/help/account affordances.
- [x] Mobile/tablet default: preserve compact header + tested drawer until customer explicitly rejects it.
- [x] Hide adjacent brand/manufacturer text by default without deleting the capability to show it.
- [x] Hide category number labels 01/02/03 by default without removing category codes from domain data.
- [x] Add the pale/mint Products intro surface inspired by customer reference while retaining a no-image CSS fallback.
- [x] Polish colored subcategory/status surfaces so they visually separate from plain white/light surfaces.
- [ ] Verify Persian RTL, English LTR, Arabic/Sorani RTL across all three viewport profiles.

### Batch 26E — Favorites sharing prototype
- [x] Confirm whether shared list carries only products or also owner/list metadata.
- [x] Implement versioned share URL containing only compact product identifiers and no PII.
- [x] Add a read-only shared-list state that can still enter allowed inquiry/WhatsApp flows if approved.
- [x] Add payload validation and URL-length guard.
- [x] Document production migration to opaque/signed backend share tokens.


### Test 26 Run 2 customer-approved integration

- [x] Customer clarification gate closed; no additional customer questions are required for this test.
- [x] Rollback checkpoint created at `rollback/test26-foundation-pre-integration` → `152e20e5ec062f48319a45727c7448d4ee68843e`.
- [x] Connected Admin Appearance policy to live Header, Home, Products and Footer consumers.
- [x] Single Hero is the customer default while the prior carousel remains selectable.
- [x] Rebuilt Home as modular About → Why → Capabilities → Product Banners, with product grid hidden by default.
- [x] Capability details use the existing accessible AdaptivePanel rather than subpages.
- [x] Products category numbers are hidden by policy and the intro/filter surfaces use the requested soft-mint separation.
- [x] Favorites sharing uses a versioned anonymous list URL containing product codes only; no PII is serialized.
- [x] Customer-approved About image source and unresolved capability/banner asset slots are recorded in the image handoff contract.
- [x] Final Run 2 CI + scoped `/t/26` deployment passed.

### Test 26 foundation delivery record

- [x] Frontend foundation commit triggering the validated build: `1b92b0f69a3e2ab68a9e8ce21b93eb3e735f2a74`.
- [x] **FTP Deploy Run #131** completed successfully.
- [x] Test 11–26 regression/source contracts passed.
- [x] TypeScript type-check, unit tests, portrait generation and Vite production build for `/t/26` passed.
- [x] FTP smoke test passed.
- [x] `/public_html/t/26` and mutable `/public_html/t/index.htm` were deployed.
- [x] `deploy-root` was skipped; Test 25 remained frozen and untouched.
- [x] At the Run 1 checkpoint, customer-facing integration was intentionally deferred; Run 2 has now completed that integration without modifying Test 25.

### Test 26 Run 2 delivery record

- [x] Customer-approved integration rollback preserved at `rollback/test26-foundation-pre-integration` → `152e20e5ec062f48319a45727c7448d4ee68843e`.
- [x] Integration build/deploy head: `fafcb0d31e248e3c12ca4533ee85708f5ab08502`.
- [x] **FTP Deploy Run #185** completed successfully: Test 11–26 contracts, media policies, Python checks, portrait generation, TypeScript type-check, unit tests and Vite production build passed.
- [x] FTP smoke test passed and `deploy-t` uploaded the generated Test 26 build; `deploy-root` was skipped.
- [x] Mutable launcher description updated in `57d47e3919a9b87a3fb540723c832ca64fd23d4a`.
- [x] **FTP Deploy Run #186** completed successfully and published the updated `/t/index.htm`; `deploy-root` was skipped.
- [x] Test 25 and all earlier frozen snapshots remained untouched.
- [x] All eight Test 26 image slots now use selected local assets, including the automatically selected Women banner 8-2. See `docs/TEST26-SELECTED-IMAGES.md`.

### Batch 26F — final assets, QA and scoped release
- [x] Replace every required placeholder with an approved local asset or explicitly keep the row open.
- [x] Update Test 26 image-requirement statuses and provenance.
- [x] Add Test 26 source/UX contract; keep all previous regression contracts.
- [x] Isolate all Test 26 browser state under `armaghan:test26:*`.
- [x] Bump frontend version for Test 26.
- [x] Build only `/t/26`; do not modify `/t/25`.
- [x] Put Test 26 first in mutable `/t/index.htm`.
- [x] Run immutable guard, media policy, unit tests, type-check and Vite production build.
- [ ] Verify mobile/tablet/desktop, RTL/LTR, navigation accessibility, favorites, wizard and WhatsApp.
- [x] FTP deploy only `/public_html/t/26` plus mutable launcher; never root deploy and never remote-delete.
- [x] Record delivery commit and successful workflow run.


### Test 26 selected-media deployment — Run 3

- [x] Apply owner choices 1-2 through 7-3 to the corresponding Test 26 image slots.
- [x] Keep generated assets at their native 800×450 / 800×300 dimensions; do not upscale.
- [x] Preserve the previous hero image and all frozen snapshots.
- [x] Leave the unselected Women banner on its explicit placeholder.
- [x] Deploy through the Test 26 build and scoped `/public_html/t` workflow.
- [x] Delivery commit `e2d9931f9c5398de9320ef698051d0f9c56db991`; FTP Deploy Run #188 passed QA, FTP smoke and `deploy-t`; `deploy-root` was skipped.

### Test 26 Run 4 — final Women banner

- [x] Automatically select Women banner option 8-2 from the three numbered candidates.
- [x] Apply the prior modesty preference: loose full-coverage garments on headless mannequins, no human model.
- [x] Save the selected 800×300 WebP at the canonical Test 26 path without upscaling.
- [x] Archive selected source images and update the image requirements/selection record.
- [x] Complete QA-gated Test 26-only deployment. Commit `a35dfacde3140618899b3784db7275edea85dba2`; FTP Deploy Run #190 passed all QA and FTP checks, `deploy-t` passed, and `deploy-root` was skipped.

## P0 — Test 25: gray dark theme + reversible portrait placeholder pipeline

- [x] Freeze Test 24 and keep `/t/24` immutable.
- [x] Create rollback branch `rollback/test24-pre-test25` at the last Test 24 source commit.
- [x] Keep all original horizontal placeholder WebP/AVIF assets untouched.
- [x] Add a deterministic Pillow build pipeline that creates 960×1440 portrait WebP derivatives without stretching or cropping.
- [x] Extend portrait canvases from sampled top/bottom edge colors and apply only a very mild vignette.
- [x] Make portrait placeholders the default for tall product cards.
- [x] Keep landscape placeholders selectable and add Portrait / Landscape / Auto controls to Admin.
- [x] Keep an edge-extend rendering fallback when generated portrait media is unavailable.
- [x] Rebuild dark mode with neutral graphite/gray surfaces while preserving the Armaghan blue navbar and active states.
- [x] Reduce the visual prominence of category number badges 01/02/03.
- [x] Refine product media/body separation without adding copy over images.
- [x] Fix Production Request category-card wrapping, reset hierarchy and locale-aware Back arrow.
- [x] Isolate English footer direction/punctuation and tighten English trust-card alignment.
- [x] Isolate Test 25 browser state under `armaghan:test25:*` and migrate catalog data forward from Test 24.
- [x] Add a strict Test 25 contract and preserve all earlier regression contracts.
- [x] Build/deploy only `/public_html/t/25` plus the mutable launcher.
- [x] Confirm CI, generated portrait media, FTP smoke test and live deployment of `/public_html/t/25`.
- [x] Record successful delivery and mark Test 25 complete.

### Test 25 delivery record

- [x] Rollback checkpoint preserved at `rollback/test24-pre-test25` → `2f10bacfbd33219cf036f546cf91b6a9bfb76916`.
- [x] Main implementation commit: `370635cf6167fcae2212f07ef665516e7e50dd04`.
- [x] Regression-marker fix: `6e54533763033106c10cc53bfbf986faad2feda8`.
- [x] Final contract/build fix: `4a41f486b08cdceb56711fc06435c170f24a16bf`.
- [x] **FTP Deploy Run #86** completed successfully.
- [x] Immutable snapshot guard and Test 11–25 contracts passed.
- [x] CI generated **18 portrait placeholder derivatives** from untouched landscape originals.
- [x] TypeScript type-check, unit tests and Vite production build for `/t/25` passed.
- [x] FTP smoke test passed.
- [x] Portrait derivatives, `/public_html/t/25/index.html` and `/public_html/t/index.htm` were uploaded.
- [x] `deploy-root` was skipped.
- [x] Deployment log confirms: **104 files uploaded; no remote files were deleted.**

### Test 25 hotfix 2 — screenshot bugfix pass

Detailed decision record: `docs/TEST25-BUGFIX2-UX-AUDIT.md`.  
Rollback checkpoint: `rollback/test25-pre-bugfix-2` → `6dc0b9188d50cae156c19fac86f548ed3e3d16f0`.

- [x] Remove the literal `\\n` text node leaked by `SmartImage.vue` and clean the matching CSS escape.
- [x] Add touch/pen swipe navigation to Hero with horizontal-intent detection and preserved vertical page scrolling.
- [x] Replace low-contrast blue foreground accents in dark content/cards with light neutral gray while retaining branded navbar/filled states.
- [x] Make English main content inherit LTR/left alignment globally, including commerce layout, header layout, bottom navigation and mobile drawer.
- [x] Add a focused CI regression contract for these screenshot failures.
- [x] Confirm CI/build/FTP deployment after this hotfix.
- [x] Record the successful hotfix run.


Hotfix delivery:
- [x] Commit: `9ff6f571bd908a368f1e2b43207d4f57fb58d7ba`.
- [x] **FTP Deploy Run #88** completed successfully.
- [x] Test 11–25 regression contracts plus the new screenshot hotfix contract passed.
- [x] Portrait generation, TypeScript, unit tests and Vite build passed.
- [x] FTP smoke test passed.
- [x] `/public_html/t/25/index.html` and `/public_html/t/index.htm` were uploaded.
- [x] **104 files uploaded; no remote files deleted.**

## P0 — Test 24: responsive commerce polish + admin content control

- [x] Freeze Test 23 and leave `/t/23` untouched.
- [x] Show full product images with contain + same-image soft backdrop instead of destructive crop.
- [x] Rebuild dark mode around neutral near-black layered surfaces while preserving Armaghan brand tokens and primary navbar.
- [x] Split mobile hero into image + dedicated copy panel so copy never straddles the image/content seam.
- [x] Use a true split hero and wider editorial composition on laptop/desktop.
- [x] Restore Home main-category images using the same image-first card language as Products.
- [x] Fix English category/filter LTR structure and switch English UI typography to Inter with calmer weights.
- [x] Improve desktop spacing, category-card composition and home manufacturer/trust layout.
- [x] Add a dedicated Admin Home Content editor using existing per-language translation overrides.
- [x] Keep quick manual customer creation, allow email OR mobile, then open Customer 360.
- [x] Allow managed customers to sign in with either normalized mobile/WhatsApp or email.
- [x] Add an exact Laravel Socialite + Google Cloud activation guide without exposing secrets to Vue.
- [x] Isolate Test 24 browser state under `armaghan:test24:*` and migrate catalog data from Test 23.
- [x] Add a strict Test 24 source/UX contract and retain previous regression contracts.
- [x] Build/deploy only `/public_html/t/24` plus the mutable launcher.
- [x] Confirm CI, FTP smoke test and live deployment of `/public_html/t/24`.
- [x] Record successful delivery and mark Test 24 complete.

### Test 24 delivery record

- [x] Main implementation commit: `4a7158afbe594c231ef984d831a2b038416a67a0`.
- [x] Contract repair / final deploy commit: `74fa4e39d611959e5ac2eb6b82b8f8e5d2ea1a26`.
- [x] **FTP Deploy Run #82** completed successfully.
- [x] Immutable snapshot guard and Test 11–24 contracts passed.
- [x] TypeScript type-check and unit tests passed.
- [x] Vite production build for `/t/24` passed.
- [x] FTP smoke test passed.
- [x] `/public_html/t/24/index.html` and `/public_html/t/index.htm` were uploaded.
- [x] `deploy-root` was skipped.
- [x] Deployment log confirms: **85 files uploaded; no remote files were deleted.**

## P0 — Test 23: Image-first categories + low-copy product cards

- [x] Freeze Test 22 and leave `/t/22` untouched.
- [x] Add the three user-provided category images as optimized local WebP derivatives; no image generation.
- [x] Replace the three plain main-category buttons with image-first clickable cards and refined numeric badges.
- [x] Make tapping the active category return to all categories without adding another text control.
- [x] Remove duplicate main-category controls from the desktop filter rail.
- [x] Make product media dominant using a tall 2:3 media frame so typical cards are ~70% image.
- [x] Remove all visible text/badges from product images.
- [x] Make the metadata row contain only the product code.
- [x] Show the product title only when available; otherwise show only the localized unavailable label.
- [x] Replace the settings-like details icon with a Lucide Menu + down-right arrow composite.
- [x] Enforce icon-only card actions and remove the obsolete Compact/Labeled selector from the current admin overview.
- [x] Increase the WhatsApp glyph from 22px to 25.3px (15%).
- [x] Isolate Test 23 browser state under `armaghan:test23:*` while migrating catalog data forward from Test 22.
- [x] Add a Test 23 media/UI source contract and keep prior regression contracts active.
- [x] Build/deploy only `/public_html/t/23` plus the mutable launcher.
- [x] Confirm CI, FTP smoke test and live deployment of `/public_html/t/23`.
- [x] Record the successful deployment run and mark Test 23 delivered.

### Test 23 delivery record

- [x] **FTP Deploy Run #79** completed successfully.
- [x] Immutable snapshot guard and Test 11–23 contracts passed.
- [x] TypeScript type-check and unit tests passed.
- [x] Vite production build for `/t/23` passed.
- [x] FTP smoke test passed.
- [x] User-provided category thumbnails and `/public_html/t/23/index.html` were uploaded.
- [x] `/public_html/t/index.htm` was updated with Test 23 first.
- [x] `deploy-root` was skipped.
- [x] Deployment log confirms: **85 files uploaded; no remote files were deleted.**

## P0 — Test 22: Release hardening + immutable snapshot handoff

- [x] Freeze Test 21 and leave `/t/21` untouched.
- [x] Bump frontend package version to `0.22.0`.
- [x] Isolate Test 22 browser storage under `armaghan:test22:*`.
- [x] Preserve Test 21 product data as a catalog migration source.
- [x] Build only `/t/22` from the Vue source.
- [x] Add a strict Test 22 release contract while keeping Test 20/21 regression contracts.
- [x] Put Test 22 first in the mutable launcher.
- [x] Update CI artifact/deploy targeting from Test 21 to Test 22.
- [x] Confirm CI, FTP smoke test and live deployment of `/public_html/t/22`.
- [x] Record the successful deployment run and mark Test 22 delivered.

### Test 22 delivery record

- [x] **FTP Deploy Run #74** completed successfully.
- [x] Immutable snapshot guard and Test 11–22 contracts passed.
- [x] TypeScript type-check and unit tests passed.
- [x] Vite production build for `/t/22` passed.
- [x] FTP smoke test passed.
- [x] `/public_html/t/22/index.html` and `/public_html/t/index.htm` were uploaded.
- [x] `deploy-root` was skipped.
- [x] Deployment log confirms: **No remote files were deleted.**

## P0 — Test 21: Selectable product placeholders

- [x] Freeze Test 20 and leave `/t/20` untouched.
- [x] Generate three complete visual sets for all six subcategories.
- [x] Make set B (`paper-cut`) the default.
- [x] Preserve sets A and C as administrator-selectable alternatives.
- [x] Add an accessible preview selector to the admin overview.
- [x] Keep product-specific media above placeholders in the fallback chain.
- [x] Recover from broken product media with the active subcategory placeholder.
- [x] Isolate Test 21 browser state from released snapshots.
- [x] Add AVIF/WebP media validation, unit coverage and a Test 21 source contract.
- [x] Build and deploy only `/public_html/t/21` plus the mutable launcher.

## P0 — Final generated image set

Decision record: `docs/IMAGE-GENERATION-AUDIT.md`.

- [x] Inventory and triangulate all image prompt/list/manifest files in the repository.
- [x] Rank five implementation options and select the current 10-asset contract from `pics.md`.
- [x] Generate 10 canonical source images with one coherent visual direction.
- [x] Derive optimized AVIF and WebP variants in the paths defined by `pics.md`.
- [x] Record prompt provenance, dimensions, sizes and checksums in a generated-media manifest.
- [x] Wire final media into the current Vue application without deleting stock fallbacks.
- [x] Run image-policy tests, type-check, unit tests and production build.

## P0 — Subcategory product placeholders

- [x] Generate three coherent six-image sets for subcategories 11, 12, 21, 22, 31 and 32.
- [x] Select paper-cut set B as the safe default for products without media.
- [x] Preserve sets A and C as administrator-selectable alternatives.
- [x] Keep product-specific media above placeholders in the fallback priority.
- [x] Fall back to the selected subcategory image when product media fails to load.
- [x] Persist the administrator selection and expose an accessible preview selector.
- [x] Store optimized AVIF and WebP derivatives with a versioned manifest.
- [x] Add registry unit coverage and a repository media contract.

## P0 — Test 20: Customer 360 + Product Administration

Detailed ranked UX decisions: docs/TEST20-UX-AUDIT.md.

### Snapshot and delivery
- [x] Freeze Test 19 and leave /t/19 untouched.
- [x] Build only /t/20.
- [x] Put Test 20 first in /t/index.htm.
- [x] Add Test 20 QA contract and keep previous contracts regression-safe.
- [x] FTP deploy only /public_html/t/20 plus launcher; no remote deletion and no root deployment.

### Customer 360
- [x] Fix reactive Pinia Proxy clone path with toRaw + structuredClone.
- [x] Add adaptive bottom-sheet (<1024px) / centered modal (>=1024px) primitive.
- [x] Add explicit not-found state so customer management cannot render as an unexplained blank panel.
- [x] Add customer KPI summary: priority, previous orders and current order.
- [x] Add 0–5 star internal Customer priority.
- [x] Keep editable name, email, WhatsApp, country/flag, address, location and notes.
- [x] Keep profile photo upload/optimization.
- [x] Add current-order and timeline controls with a visible progress summary.
- [x] Add editable password state.
- [x] Make manager-set customer email/password usable by the Test 20 sign-in adapter.
- [x] Keep direct-access modes Permanent / Expiring, generation, expiry and revoke.
- [x] Resolve valid direct-access customer tokens into the correct customer session.

### Product-card micro-interaction
- [x] Add subtle hover scale/lift using transform only.
- [x] Restrict hover effect to fine pointers with hover support.
- [x] Keep reduced-motion protection.

### Product administration at ~500 items
- [x] Add search across code and all four localized product names.
- [x] Add category filter.
- [x] Add subcategory filter.
- [x] Add pagination with 50 rows per page by default.
- [x] Add 25 / 50 / 100 page-size controls.
- [x] Make select-all target the visible page.
- [x] Keep batch delete.
- [x] Add persistent three-dot overflow per product row.
- [x] Disable row overflow while batch mode is active.
- [x] Keep pagination controls at least 44×44 CSS px.
- [ ] In Laravel production, move filtering/search/pagination to server-side queries.

### Product Add/Edit
- [x] Add adaptive Add/Edit Product panel using the same sheet/modal rule.
- [x] Add four product-name fields: fa / ar / en / ku.
- [x] Persist localized names on the product record with registry fallback.
- [x] Infer category/subcategory/spec schema from first two product-code digits.
- [x] Show inferred category/subcategory immediately.
- [x] Auto-fill locked/negotiable specs when prefix changes.
- [x] Add Reset from code to restore the subcategory schema.
- [x] Keep specs editable after inference.

### Repository memory
- [x] Add five-option ranked analysis and self-detected issues to docs/TEST20-UX-AUDIT.md.
- [x] Mark delivery/QA items complete after successful CI and live FTP deployment.


### Test 20 delivery record
- [x] **FTP Deploy Run #68** completed successfully.
- [x] Test 11–20 regression contracts passed.
- [x] TypeScript type-check passed.
- [x] Unit tests passed.
- [x] Vite production build for /t/20 passed.
- [x] FTP smoke test passed.
- [x] /public_html/t/20/index.html and /public_html/t/index.htm were uploaded.
- [x] deploy-root was skipped.
- [x] Deployment log confirms: **No remote files were deleted.**
- [x] Test 19 remains frozen and was not rebuilt into /t/19.


## P0 — Test 19

### Snapshot + delivery
- [x] Freeze Tests 01–18.
- [x] Build a new immutable `/t/19` artifact.
- [x] Keep `/t/index.htm` mutable and newest test first.
- [x] Add Test 19 contract before deployment.
- [x] Keep root untouched and FTP non-destructive.

### Mobile/tablet header + drawer
- [x] Header contains only brand + theme + hamburger below desktop breakpoint.
- [x] Remove mobile/tablet header login.
- [x] Drawer language controls become 44px rounded-square buttons: Fa / En / ع / ک.
- [x] Drawer width becomes `clamp(260px,70vw,340px)`; tablet max 340px.
- [x] Drawer scrim stays 40% dark + blur.
- [x] Drawer login button moves above divider in fixed footer.
- [x] Show small readable `تولیدی ارمغان` / localized manufacturer label below login.
- [x] Add Help/Guide entry in drawer in all four languages.
- [x] Animate drawer enter/leave from the side in 200ms; honor reduced motion.

### Product copy / typography / visual consistency
- [x] Remove public-facing prototype/test words: SVG, آزمایشی, demo/test wording, technical media labels.
- [x] Replace ad-hoc typography with semantic scale.
- [x] Use Vazirmatn FD for fa/ar/ku and Roboto for English.
- [x] Hide horizontal scrollbar chrome in category chips while preserving scrolling.
- [x] Reserve layout space for bottom nav so content is never obscured.
- [x] Keep header z-index above all normal scrolling content.
- [x] Replace harsh dark gradients with neutral surfaces + restrained brand glow.
- [x] Remove hardcoded light surfaces from primary production/admin/customer components.
- [x] Use truthful generic garment-manufacturer copy only; no invented certifications/prices/claims.

### Sheet/modal motion
- [x] BaseSheet enters from bottom and exits to bottom over 200ms.
- [x] Backdrop fades in/out with the sheet.
- [x] Exit animation must complete before DOM removal.
- [x] Preserve focus trap, Escape, outside-tap close and reduced-motion behavior.

### Product card action modes
- [x] Keep admin-selectable Compact / Labeled modes.
- [x] Compact remains default.
- [x] Labeled mode uses localized `Order / Favorite / Specs`.
- [x] Keep WhatsApp brand treatment, neutral favorite container and centered icon geometry.

### Admin IA
- [x] Add top-level admin views: Overview / Customers / Products / Languages.
- [x] Overview can show customers + products together.
- [x] Customers and Products have dedicated focused views.
- [x] Add selectable data tables with row checkboxes, select-all and contextual batch action bar.
- [x] Add bulk delete with confirmation for customers and products.
- [x] Add customer create/delete.
- [x] Add vertical overflow menu per customer row.
- [x] Replace text-heavy impersonation action with icon + accessible label.
- [x] Add dedicated customer-management panel/detail view.

### Customer 360 editor
- [x] Editable name, email, WhatsApp, country/flag, address, location text, notes.
- [x] Editable current order state and timeline stage.
- [x] Show previous-order count and current-order summary.
- [x] Profile-photo upload preview in prototype.
- [x] Password set/reset control in prototype.
- [x] Direct-access link control with concise modes: `Permanent` / `Expiring`.
- [x] Expiring link exposes expiry/revoke controls.
- [x] Separate `Manage customer` from `Impersonate customer`.

### Wishlist lead notifications
- [x] Add admin alert metric for customers/visitors whose favorites changed.
- [x] Model anonymous visitors with generated visitor token, not IP as identity.
- [x] IP is documented as optional backend metadata only.
- [x] Logged-in lead actions expose contact CTA.
- [x] Guest lead actions expose in-app message path for their shared favorites page.
- [x] Add `Invite to create account` action for guest leads.

### Authentication surfaces
- [x] Keep working prototype username/password sign-in.
- [x] Add prototype registration form and persisted prototype account.
- [x] Add magic-link prototype with generated/revocable token and Permanent/Expiring mode.
- [x] Add Google sign-in button wired to a backend endpoint contract; do not fake OAuth success.
- [x] Document Laravel Socialite credentials/backend requirement as the only blocker for live Google OAuth.
- [x] Keep login modal centered with blurred backdrop.

### i18n completeness
- [x] Move visible application copy into a single translation registry.
- [x] Add persisted per-language translation overrides.
- [x] Eliminate mixed-language UI in Home / Products / Production / Favorites / Tracking / Login / Drawer / Admin.
- [x] Keep `html lang` and semantic `dir` correct for each locale.
- [x] Keep overall shell geometry stable across languages with CSS; English content and controls are LTR/left-aligned.
- [x] Translate product category/subcategory/product/spec labels needed by current UI.

### Translation manager
- [x] Add Admin → Languages.
- [x] Language selector + section/group selector + search.
- [x] Every translatable key has its own editable field.
- [x] Single-item save/reset.
- [x] Multi-select + bulk reset.
- [x] Show base value and overridden value clearly.
- [x] Persist overrides locally in prototype; design API shape for later DB persistence.

### Repository memory
- [x] Add detailed 5-option ranked analysis for each requested item and self-detected visual/product bugs in `docs/TEST19-UX-AUDIT.md`.
- [x] Update backlog checkboxes as each implementation block lands.
- [x] Reviewed `pics.md`; image requirements did not change in Test 19, so no update was required.

## P0 — Backend MVP delivery

- [x] Freeze Test 26 and create `snapshot/test26-final`.
- [x] Promote backend work from deferred P1 to active P0 scope.
- [ ] Verify production hosting supports PHP >= 8.3 and a safe Laravel document-root layout before installation.
- [ ] Install Laravel 13 under `platform/backend` with SQLite development defaults.
- [ ] Convert the approved SQL draft into Laravel migrations/models/seeders.
- [ ] Install Filament 5 and create administrator authentication.
- [ ] Product CRUD: add/edit/archive, category/subcategory, availability, sort order and image management.
- [ ] Customer CRUD: identity/contact/notes/status plus generate/revoke one-tap access links.
- [ ] Public read endpoints for categories/products/site settings; replace browser-local catalog persistence in Test 27+ only.
- [x] Backend favorites-share records with compact high-entropy public token and WhatsApp share action.
- [x] Customer magic-link login: hashed token, expiry/revoke and secure session. Trusted-device persistence remains optional and was not required for MVP.
- [ ] Persist high-value site settings including semantic color-role mappings and safe contrast preview.
- [ ] Add production backup, health check, audit log and minimal recovery procedure.
- [ ] Deploy production backend without modifying `/t/26`; any required frontend wiring lands in Test 27+.
- [ ] End-to-end delivery QA: admin product/customer changes visible publicly, favorites link opens correctly, WhatsApp handoff works, login link works, color settings render safely.

## P1 — Backend productionization
- [x] Laravel 13 backend scaffold installed: SQLite local/dev/test + MySQL/MariaDB production contract.
- [x] Filament 5 Panel Builder installed; domain Resources and production admin provisioning remain P0.
- [ ] Persist production data to MySQL/MariaDB; keep SQLite for local/dev/test fixtures.
- [ ] Laravel Socialite Google OAuth with real client credentials.
- [ ] Signed/hashed magic links with expiry, scope, revoke and audit.
- [ ] Server-side media library and queued image conversions.

## Definition of Done — Test 19
- [x] Tests 01–18 immutable.
- [x] Test 19 first in launcher.
- [x] No public prototype/test wording.
- [x] No mixed-language major flow for fa/ar/en/ku.
- [x] Mobile drawer, bottom sheets and theme transitions visually coherent.
- [x] Admin supports focused sections, multi-select, batch delete, customer CRUD and Customer 360 edit.
- [x] Translation editor works with per-key and bulk reset.
- [x] Auth surfaces clearly distinguish functional prototype flows from backend-required Google OAuth.
- [x] Test 11–19 contracts, type-check, unit tests, build, FTP smoke and deploy all pass.
- [x] Root deploy skipped; no remote file deletion.


### Delivery record
- [x] Main implementation landed in `398902cd1e2ed1ce600386656996aa427c5e1cfb`.
- [x] Regression fixes landed through `02150c18ba16cd29b46f611371ac6350bb3936a0`.
- [x] **FTP Deploy Run #63** completed successfully.
- [x] Test 11–19 contracts passed.
- [x] TypeScript type-check passed.
- [x] Unit tests passed.
- [x] Vite production build passed.
- [x] FTP smoke test passed.
- [x] `/public_html/t/19/index.html` and `/public_html/t/index.htm` were uploaded.
- [x] `deploy-root` was skipped.
- [x] Deployment log confirms: **No remote files were deleted.**

### Verified closeout for this bounded run
- Implementation commit: `88d4172e50413dec3d8951ba093535894b659db4`.
- FTP Deploy #325, run `37001349371`: QA, type-check, 41 Vue tests, active Test28 build, FTP smoke and deploy-t PASS; deploy-root SKIPPED. Frozen snapshot guard PASS for all 27 frozen tests.
- Fresh live browser read after deployment: heading/category/product title RGB(200,227,219), card RGB(30,32,36), no horizontal overflow at 1363 CSS px; repaired default is live (12.015:1 on card).
- Fresh guest request to `/backend/admin/products` redirected to `/backend/admin/login`; no authenticated-admin acceptance is inferred.
- Additional observed copy follow-up: Persian Home still displays the English eyebrow "Why Armaghan?". Keep the broad multilingual Home acceptance open and localize that label in the next bounded fix.
- Mobile/tablet and valid customer/admin/share/WhatsApp end-to-end acceptance remain open. This run advances partial final QA; it does not declare final project delivery.


## Owner-authorized root Test29 release — 2026-10-02
Owner explicitly authorizes root index replacement with selected Test29 while preserving /t. Independent selector: config/root-release.json. Tests 01–28 remain frozen; Test29 is mutable. Requested eyebrow/producible defaults and real Filament navigation are included. Root deployment verification pending. Google requires owner Cloud credentials; no real login acceptance is claimed.

Google customer signup/sign-in implemented using official Socialite with a create-only identity table and eleven backend security cases. Root hides local demo password/signup and ignores browser-only admin roles; real customer sessions remain authoritative. Backend additive deploy and real Google credential readiness pending; never claim live Google login before actual provider verification.

### Verified deployment closeout
- Repair source commit: 8676b469d0159848f775689c55b7286e35cc932f; corrected test request commit: ad02b2e50ad5e6dd70196533a700c10a947a26b9.
- Backend CI 37029811115 PASS: 51 tests / 362 assertions, dependency audit and secret hygiene PASS.
- Backend Code Deploy 37029811169 PASS: pre-swap SQLite backup created, no dependency/migration drift, health/admin/catalog smokes 200, guest session 401, POST-only endpoints 405, temporary cleanup PASS.
- FTP QA 37029811251 PASS; root and numbered UI deploys skipped. No frontend/frozen version files changed.
- Independent live cancellation probe now returns HTTP 302 to https://armaghantrading.com/#/tracking?auth_error=google (before repair: /backend/#/tracking).
- First attempt was safely stopped before host deployment by two test-harness 404s: forced production URL root also prefixed the test request. Using explicit localhost test request URLs keeps the /backend URL generator simulation intact; all tests now PASS.
- Real Google signup/repeat login still requires owner confirmation; no actual-account login success is inferred from cancellation/automated tests.

- Independent numbered-path probe: start at /backend/auth/google/redirect?return_path=/t/29/, cancel with the same cookie jar, return HTTP 302 to https://armaghantrading.com/t/29/#/tracking?auth_error=google. Provider enabled:true and guest session 401 remain verified after deploy.

### Explicit owner direction for the NEXT run — 2026-10-02 21:48 Tehran

Preserve the polished administration interface developed in the numbered tests and connect that same structure/styling to real Laravel services. Do not replace it wholesale with default Filament screens or recreate its appearance. This work begins after the current login/admin/badge/footer run is completed.

- Inventory existing admin sections and retain their approved layouts/components.
- Add real server-authoritative admin-session/read permissions; never trust browser-local admin role.
- Migrate one vertical feature at a time (product CRUD/media, customer management, device settings/editor), keep API writes allowlisted, CSRF-protected and authorized, and expose real loading/errors.
- Remove demo data/write paths from production admin; preserve frozen snapshots and reversible review alternatives.
- Existing Filament remains the operational access/fallback while the custom interface is integrated. Reuse canonical Eloquent/media/customer/style services; avoid parallel data stores.
- Prepare a bounded five-option decision and checkpoint at the delivered commit before implementation. Acceptance must include actual authenticated writes/reload; local visual resemblance alone is insufficient.


### Current-run deployment closeout — 2026-10-02
- Delivered runtime commit: 35dabd38d557e7b40567cdfafe863ecd63be34b8. Backend CI #61: 63 tests / 473 assertions PASS; Backend Code Deploy #17 PASS. FTP #346: QA, smoke, Test29 publish and root promotion all PASS. Published HTML/JS/CSS checksum verification PASS; /t launcher puts active Test29 first, /t preserved.
- Live root refreshed and observed script /t/29/assets/index-DhRFzVDI.js (CSS index-DAjvviV3.css). Root and Test29 show sign-in/signup tabs, password confirmation/strength guidance, Google button, administrator shortcut, and no direct-link login tab. Administrator shortcut resolves to https://armaghantrading.com/backend/admin/login with real Email/Password form.
- Live computed styles: seven producible labels have transparent background, 0px border, gold rgb(255,181,20), weight 400. Footer brand is last after columns, physical right/end alignment, logo approximately 110.4px square (6.9rem, +130%); no desktop horizontal overflow.
- Read-only live probes: Google enabled true; guest customer/security 401; login without CSRF 419; administrator login page 200. No production signup, password change, owner sign-in or authenticated logout was performed by the agent. Actual owner Google provisioning/private password setup, authenticated acceptance and full mobile/tablet QA remain OPEN. Password reset/email-verification delivery remains unconfigured; do not describe the whole backend as feature-complete.
- NEXT run remains the owner's explicit preserved-design Laravel administration integration above; do not substitute default panel styling for the approved custom test interface.


### Custom administration integration follow-up details
- Product migration must map by backend IDs/codes explicitly; never merge server record IDs into browser fixtures. Preserve existing ProductEditorPanel code/category inference and four-language fields, but validate canonical taxonomy and ProductSpecValue relations server-side. Reuse SanitizeProductMedia and product-gallery collection; do not persist compressed browser data URLs as canonical images.
- The transport reuses the existing same-origin CSRF retry handling; no extra authentication/token storage layer. A stale-positive administrator identity is cleared on failed recheck; save failure preserves draft and never reports success. Security tests include actual isolated SQLite create/edit/reload/deactivation, account ownership restrictions and logout session isolation.
- GitHub concurrency keeps one running and one pending run; a newer pending run can replace an older pending run even with cancel-in-progress:false. Do not assume a canceled frontend-only commit will be deployed by a later root-only commit. Final publication therefore explicitly changes frontend and root revision together at 429698496f643137f89416b42f43d1ea601b2077; verify both final deployment jobs before closeout.


### Verified custom admin closeout — 2026-10-02
Runtime: 429698496f643137f89416b42f43d1ea601b2077 (backend source 7812c241f7cb5a3997fe3bf4767396e3c3977fb9). Backend CI 70/538 PASS, frontend 53/14 PASS; FTP #351 all jobs PASS. The custom route is https://armaghantrading.com/#/admin, retained at /t/29/#/admin. Initial live guest screen shows login-required guidance without any customer table. Final root loads the final bundle; no authenticated browser or mobile acceptance is inferred.
First owner Google login still proves ownership and opens private account security; its management link now targets the preserved custom route. Real password admin sign-in routes there too. The stock Filament panel remains available for product/media operations until their existing custom forms are connected in the next slice. Do not claim all custom administration is migrated or production writes were acceptance-tested with the actual owner's browser.
