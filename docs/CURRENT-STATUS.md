# Armaghan — Current Status

## Real account management and owner hierarchy — 2026-10-03

Owner asks why prototype management links remain unavailable, authorizes amirmashti1378@gmail.com as a business administrator and motealle@gmail.com as the superior primary owner. Baseline 4443bd35246aaa0b283b30730cd306d0fe2c75f0; lease codex-20261003-admin-authority. Ranked options: preserve custom domain integration (10, selected), all domains at once (7), existing stock fallback (6), rebuild custom administration (4), browser-local authority (1).

- Verified Google allowlist now contains motealle@gmail.com, armaghantrading.company@gmail.com and amirmashti1378@gmail.com. The new identity becomes an administrator on its next successful stateful, verified Google callback; code deployment alone does not claim an existing database row has been elevated. Inactive accounts remain rejected. Primary-owner authority requires active admin role, verified email and exact configured primary email; no browser/request role can grant it.
- Preserve custom AdminDashboard and table/AdaptivePanel surfaces; Users joins real Customers and Products. Server-paginated/searchable accounts, customer account creation with confirmed hashed initial password and linked canonical Customer, name/status edits and archive without hard deletion. Owner alone may create additional administrators and view/manage administrator account status. Other admins can manage customer accounts, never an admin or owner. Reserved Google identities cannot be claimed by admin-created or public signup passwords. Owner account cannot be edited/deactivated via these APIs, including by itself; ordinary own password changes remain on the existing protected account-security page.
- Existing emails and roles are immutable in this slice. Resetting another existing account password, role conversion, destructive deletion, impersonation and CRM-account linking are NOT delivered. Password recovery/reset must include existing-session revocation before implementing. Creating a CRM customer is still distinct from creating a login account.
- Add shared real-admin identity/navigation/logout on public pages so a genuine admin is not shown only a customer/demo role. Administrator session reports primary-owner state. Customer session additionally rejects a disabled/noncustomer linked User even if its CRM record is reactivated. Product/customer links require that same real web session. Guest/disabled/customer authority cannot mutate management data.
- Disabled custom content/appearance/language/overview tabs reflect incomplete real-data integration, not proof of an account permissions failure. Existing business resources remain available in Filament. Do not enable prototype-local stores as production management. Next: canonical product specifications, then preserved visual editor/style profile and device/content settings with real admin authorization and shared persistence.
- Automated local and CI/deployment acceptance PENDING. Actual owner/new-admin sign-in and authenticated live account operations remain OPEN; no production accounts/passwords/private data changed by the agent. Tests01–28 frozen; root remains selected29 with guarded revision refresh.


## Preserved custom administration — products and media, 2026-10-02

Owner authorizes the next bounded custom-admin domain. Baseline/rollback checkpoint: `b2d0b7cdf12d0359789b98d95cce995a1901f475`; lease `codex-20261002-custom-products-media`. Five ranked options: preserve existing UI with canonical Laravel/Spatie (10, selected), all domains at once (7), restyle stock admin (6), independent product/media services (3), browser-only persistence (1).

- Preserve AdminProductsPanel/ProductEditorPanel entry components, AdaptivePanel, existing table/form/surface styling; add live server mode. Products tab joins Customers in the custom real-admin route; other tabs remain unavailable pending integration. Existing Filament remains an operational fallback.
- Same Product, taxonomy and Spatie product-gallery models; authenticated active-admin, CSRF-protected, bounded paginated/search/filter API. Create/update names in four languages, code, subcategory, availability, active/archive and order. No hard deletion. Existing specification values are preserved; moving a product with existing values across taxonomy is rejected. Structured specification editing remains next work, never fake prefix defaults.
- Real multipart image upload; JPEG/PNG/WebP, 8 MiB, 5000x5000, six items; MIME/decode verification, server-generated safe filename, existing EXIF/orientation sanitization and non-upscaled card/thumb conversions. Preview and exact-owner scoped gallery ordering; files stored only in existing host-centric canonical paths. No new tables/dependencies/migrations. Physical image removal remains existing Filament fallback; no custom destructive action introduced.
- Content revision covers fields, specification values and ordered gallery, checked under transaction before changes. Failed processing cleans only this request's new media/files before rollback. Audit actor/subject/field names only. Same public adapter refreshes after successful editor close and hides backend-archived local copies.
- Local build/type-check and 56 frontend tests / 15 files PASS; 31 source/frozen/media/release contracts PASS. Backend CI #68 (37061931861) PASS: 79 tests / 662 assertions, strict Composer validation, locked audit and secret hygiene. Backend Code Deploy #24 (37061931645) PASS: production health/admin/catalog/resource smoke HTTP 200 and temporary cleanup PASS. FTP #353 (37061066948) PASS: QA, smoke, Test29 publish/checksums and guarded root promotion. Later backend-only FTP #358 (37061931696) passes QA/smoke and correctly skips UI/root. Final source runtime `d729344e42218fe45af33d818e021a19a35d7e84`; initial frontend publication `02bbb142cccb81de14f759c66d5b8d730a23ea60`; JS index-DpQkpQj3.js / CSS index-CPT5jm1O.css. Root browser reload loads this JS, guest custom-admin page shows sign-in gate and zero management tables. Fresh live health GET=200, admin products/taxonomy GET=401; no-CSRF create/image-upload/image-order POST/PUT=419. Actual owner authenticated product/media browser acceptance and deferred device/visual checks remain OPEN. No production product/customer records or credentials changed by the agent.
- Acceptance caught and corrected partial Spatie model serialization and the canonical `order_column` mapping before backend deployment. Real JPEG with comment metadata verifies stripping, safe generated name and non-upscaling; executable uploaded extensions remain rejected. Upload failures preserve prior files. General admin and image-upload throttles have distinct keys: 10 upload attempts/minute; list reads do not consume upload budget, verified by a focused test. Initial backend test gates refused deployment until final PASS; no migration/dependency or private-data changes.


Next: structured canonical specifications, then existing style-profile/editor/device-settings authorization and persistence. Do not claim all custom management modules migrated.



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


Last reconciled: 2026-10-02
Canonical source checkpoint for Test29 promotion: `1fafd9638777e26796acedc60b27dfd545736794`

This file is the **current operational source of truth** for the next run. Historical decision records remain useful, but when an older document conflicts with this file, use this file plus the latest `docs/HANDOFF.md` and `docs/BACKLOG.md`.


## Root Test29 and Google preparation — verified 2026-10-02

- Selected root version: 29, independently from active numbered development. Public root: https://armaghantrading.com/#/ ; /t/29 remains live; Tests 01–28 remain frozen.
- Frontend release 5d7af888959604518cca693e8747c8dbc36fb2f1, FTP run 37009040766: QA, 49 Vue tests across 13 files, type-check/build, scoped /t upload, published artifact checksum/launcher verification and root promotion PASS.
- Root/auth verification commit 1a4de2db706f6b14bcf2e5e63fc79c9ecaa34c86, FTP run 37009477221: static QA, eight promotion/rollback tests, guest customer HTTP 401, Products/Customers login boundary and root HTML/asset verification PASS; /t deployment SKIPPED.
- Google backend 0a07d9b98bfc8e5a4d297c02b4626064167255fb: Backend CI 37008505779 and Additive Deploy 37008505784 PASS. 49 backend tests / 348 assertions; one create-only identity table; consistent production database snapshot before migration; activation and admin-login smoke PASS.
- Live readiness reports Google provider configured=False. Credentials/Cloud registration and an actual Google signup/repeat login are OPEN. Never claim real Google login acceptance from mocked tests.
- Fresh desktop DOM at root: all four home eyebrows display:none; product badges read Producible in the observed English locale, 11.2px, weight 400, centered with zero center offset. Four locale source defaults preserve the concise equivalent (Persian تولیدپذیر). Root product links resolve /#/products; Home logo/hero/about images loaded. No mobile/tablet or broad theme/language visual acceptance is inferred.
- Real product upload/customer CRUD already exist in Filament. Guest /backend/admin/products redirects to /backend/admin/login. No authenticated upload/customer edit was performed in this run because no real admin session was available.
- Root accepts real Backend customer authentication only; browser-only review/admin roles do not authenticate root. Root LoginSheet hides local demo password/signup forms and offers backend Google/Magic Link and real admin entry.
- Root Style Profile reads production; /t reads staging. The root asset base does not change native/router/skip-link navigation destinations.
- Earlier pipeline attempts stopped safely before the new upload because of workflow-string escaping or historical text-only assertions. Corrected YAML was locally parsed; current release and all publication gates PASS. Use callback replacements for JavaScript replacement text containing shell dollar/apostrophe sequences.
- Follow-up P0: owner Cloud setup/private server credentials; real authenticated product-image/customer/Google acceptance; verify canonical root fragment paths for customer Magic Link/FavoriteShare against private server configuration (source historical defaults remain /t/27); complete deferred mobile/tablet and broad visual acceptance.

## Test29 — prior nonvisual release, 2026-10-02

Owner instruction: defer visual testing; preserve all previous versions; create a new numbered folder and prepend its link.
- Tests 01–28 are frozen. Test28 source checkpoint: `snapshot/test28-final` at `1fafd9638777e26796acedc60b27dfd545736794`.
- Test29 is the active release lane. Build/deploy target: `/t/29`; link `./29/index.html` is first in the launcher.
- Numbered build and FTP boundaries refuse targets <=28. Root was prohibited in the prior nonvisual run; the newer owner-authorized root promoter supersedes that restriction only for root index.html. Remote deletion remains prohibited.
- Test29 browser state uses `armaghan:test29:*`, including the formerly shared manual-language setting. No migration writes previous-version keys; production Backend sessions/catalog remain intentionally shared.
- Localized Why Armaghan eyebrow defaults for Persian, Arabic and Sorani; English retained. Existing locale/text overrides retain priority.
- Three new automated locale tests cover old-key preservation, new-version reload and four-language defaults. The dark-heading numeric guard is carried forward.
- Visual/device/admin-session acceptance remains OPEN and deferred by owner instruction. Automated PASS must not be presented as visual acceptance.
- Test29 release commit: `29dac32927dffd128a388a69fec5f9888ce352a9`. FTP Deploy #328, run `37003816665`: plan, QA, type-check, 44 Vue tests (12 files), build, FTP smoke, deploy-t and nonvisual HTTP verification PASS; deploy-root SKIPPED.
- Published index.html and initial JS/CSS SHA-256 match the built artifact; published launcher lists `./29/index.html` first. Tests 01–28 build/deploy boundaries PASS. All 19 prior numbered source blobs present in Git retain their starting SHA.
- First attempt #327 (`37003682710`) deployed successfully but its verification incorrectly included external font stylesheets. The verifier now checks build-owned local assets; corrected #328 passes. Theme bootstrap also uses the Test29 namespace.
- URLs: https://armaghantrading.com/t/29/ and https://armaghantrading.com/t/index.htm . Visual/device/authenticated-browser acceptance is still OPEN; no visual tests performed in this run.

| Rank | Method | Score | Reason |
|---:|---|---:|---|
| 1 | Isolated Test29 + localized defaults + automated freeze/storage contracts | 9.5 | Concrete release without visual-layout changes; protects old snapshots |
| 2 | Copy current release into a new lane only | 8.0 | Safe but fewer functional corrections |
| 3 | Documentation-only closeout | 6.0 | Does not deliver a new version |
| 4 | Add more admin capabilities | 4.0 | Core capabilities already exist |
| 5 | Layout redesign | 2.0 | Requires deferred visual testing |

Selected: option 1. Storage rationale: https://developer.mozilla.org/en-US/docs/Web/API/Web_Storage_API/Using_the_Web_Storage_API .

## Executive status

The original minimal backend is operational. Preserved custom administration is partially integrated: the customer slice is delivered, products/media/settings integration and actual-owner authenticated browser acceptance remain open. Core admin/catalog/media/customer-auth/share flows, guarded deployment, encrypted off-host SQLite backup and artifact round-trip restore verification are live. Test 29 is the active mutable review lane; FTP #328 deployment and nonvisual HTTP verification passed. Final end-to-end QA and handoff remain open; visual checks are deferred by owner instruction.

Current UI lane:
- Tests 01–28: frozen/immutable; preserve their live folders.
- Test29: active; build/deploy only /t/29 and the mutable launcher.
- Source checkpoint: snapshot/test28-final. Browser keys: armaghan:test29:*.
- Test29 inherits the delivered Test28 behavior with storage isolation and localized Why Armaghan defaults.
- Prior FTP #320 and #325 evidence belongs to Test28, not Test29. Test29 FTP #328 and nonvisual HTTP verification passed.

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

## Test29 visual editor — deployed implementation (visual acceptance open)

Current Test29 editor capabilities inherited from delivered Test28:
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
2. open Test29 as the authenticated administrator;
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
2. Complete browser-authenticated Test29 Style Profile + one real Filament product-image upload + one real customer Magic Link acceptance opportunistically when a valid real admin session is available.
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
7. Keep Tests 01–28 immutable. Test 29 is the only active mutable UI lane; use only `armaghan:test29:*` browser-state keys.
8. Never store production plaintext credentials in Git, docs, logs or artifacts.
9. Do not rebuild product-media ownership, Customer/Magic Link, FavoriteShare/WhatsApp or off-host backup/restore; all are live. The next P0 is final end-to-end QA + delivery handoff.
10. Do not claim the 18 current Products have server media yet: their last verified API `media` arrays were empty and Test 29 retains local media fallback until an admin uploads images. Recheck current catalog data before any new media-state claim.
11. Use the guarded additive lane for future approved create-only migration/dependency changes; ordinary Backend changes stay on the code-only lane.


## 2026-10-02 bounded documentation reconciliation

- Compared canonical status, latest handoff, backlog, Test28 hardening audit and released shared lease with main `9ee3a206...`.
- Corrected stale next-run instructions that still called Test27 mutable and off-host backup pending. Historical deployment evidence remains unchanged.
- GitHub Actions run `36975429480` for the inspected main head is completed/success; this is prior-run evidence, not a new browser acceptance.
- No live multi-device or authenticated browser acceptance was performed in this documentation run. Final QA remains open; do not infer completion from existing unit/build/route checks.

## 2026-10-02 live Test28 guest acceptance and dark-title repair

Inspected source baseline: `cfb28307e3be1c56e54a210fcc211ab8f1323a31`. Shared write lease: `codex-20261002-live-qa`. Public browser viewport: 1363 × 936 CSS pixels.

| Check | Direct evidence | Result |
|---|---|---|
| Desktop Home / Products | Home rendered; 18 products rendered with local image fallback; no horizontal overflow in observed viewport | PASS for observed viewport |
| Product search / category filtering | Code 11001 returned 1 product; Baby returned 6; clearing returned 18 | PASS |
| Product details | Specs opened and closed; focus returned to invoking control | PASS |
| Locale direction on Products | en/ltr; fa/rtl; ar/rtl; ckb/rtl; no observed horizontal overflow | PASS for Products only |
| Guest favorites | Initially empty; added 11001; survived reload; removed test selection and returned to empty | PASS for browser-local guest persistence only |
| Invalid persisted-share route | Synthetic all-zero token reached generic invalid/expired state without crash | PASS; valid issue/resolve/WhatsApp not newly tested |
| Real admin session | Filament login form displayed; no authenticated session available | OPEN; no login bypass or credential change |
| Dark normal-size headings | Green #21946A on card #1E2024 measured 4.279:1 | FAIL before repair |
| Mobile/tablet, valid customer link, admin editor/upload | Not performed; supported browser API has no viewport-resize control; RC has no connected devices | OPEN |

Repair: change only the dark `--role-heading-strong` default to approved Brand Mint #C8E3DB. Light defaults, explicit editor overrides, brand tokens and frozen Tests 01–27 stay unchanged. Mint/card contrast is 12.015:1. Numeric regression coverage resolves actual CSS defaults and requires >=4.5 on dark page/card/input surfaces; the new check fails on original source and passes after repair.

| Rank | Dark title method | Score | Reason |
|---:|---|---:|---|
| 1 | Approved mint through existing semantic heading role | 9.5 | Strong measured contrast; one declaration; preserves palette and override priority |
| 2 | Approved white through heading role | 9.0 | Readable but less color differentiation |
| 3 | Mix green with white | 8.0 | Adds another combination to verify |
| 4 | Lighten dark surfaces | 6.0 | Larger visual scope |
| 5 | Increase title sizes | 4.0 | Alters layout to address color failure |

Selected automatically: option 1. Standard: https://www.w3.org/WAI/WCAG22/Understanding/contrast-minimum.html (normal text >=4.5:1). Responsive simulation is not equivalent to real hardware: https://developer.chrome.com/docs/devtools/device-mode.

Next: confirm guarded Test28 build/deploy and fresh live mint computed color; complete mobile/tablet and authenticated acceptance when available. Do not close broad final-delivery checklist from this partial desktop evidence.

### Verified closeout for this bounded run
- Implementation commit: `88d4172e50413dec3d8951ba093535894b659db4`.
- FTP Deploy #325, run `37001349371`: QA, type-check, 41 Vue tests, active Test28 build, FTP smoke and deploy-t PASS; deploy-root SKIPPED. Frozen snapshot guard PASS for all 27 frozen tests.
- Fresh live browser read after deployment: heading/category/product title RGB(200,227,219), card RGB(30,32,36), no horizontal overflow at 1363 CSS px; repaired default is live (12.015:1 on card).
- Fresh guest request to `/backend/admin/products` redirected to `/backend/admin/login`; no authenticated-admin acceptance is inferred.
- Additional observed copy follow-up: Persian Home still displays the English eyebrow "Why Armaghan?". Keep the broad multilingual Home acceptance open and localize that label in the next bounded fix.
- Mobile/tablet and valid customer/admin/share/WhatsApp end-to-end acceptance remain open. This run advances partial final QA; it does not declare final project delivery.


## Owner-authorized root Test29 release — 2026-10-02
Owner explicitly authorizes root index replacement with selected Test29 while preserving /t. Independent selector: config/root-release.json. Tests 01–28 remain frozen; Test29 is mutable. Requested eyebrow/producible defaults and real Filament navigation are included. Root deployment and guest-auth readiness verified PASS; Google credential activation remains OPEN. Google requires owner Cloud credentials; no real login acceptance is claimed.

Google customer signup/sign-in implemented using official Socialite with a create-only identity table and eleven backend security cases. Root hides local demo password/signup and ignores browser-only admin roles; real customer sessions remain authoritative. Backend additive deploy PASS; real Google credentials are still absent; never claim live Google login before actual provider verification.

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
