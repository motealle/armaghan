# Armaghan Project Rules

## Product image upload invariant
- Product photos must support selecting several images up to the remaining six-image capacity, preview before upload, and sequential retry-safe submission.
- Browser optimization is performance-only; server authorization, MIME/content validation, decode/rewrite, generated filenames, rate limits and upload limits stay authoritative.
- Canonical delivery variants are optimized WebP thumb (~320px), card (~800px), detail (~1600px), with no forced crop/upscale and safe fallback for older media.
- Do not treat larger timeouts/file limits or an unavailable long-running queue worker as the primary upload fix.

## Product gallery management invariant
- Admin gallery tiles exist only for persisted media rows or explicit pending local files; never render fixed empty photo slots.
- Admin gallery media must display the real file. A failed media fetch is shown as an explicit unavailable state with safe selection/deletion, never disguised as a generic garment placeholder.
- Public product media is served through the guarded Laravel catalog-media route; do not depend on a host-level public storage symlink for customer-visible product photos.
- Reorder and deletion are server-authoritative, active-admin-only and revision-checked. Deleting media must verify product ownership and refresh the media relation before returning the snapshot.
- On the public Products page, edit affordances are rendered only from the real server admin session. Reuse BackendProductEditor; do not create a browser-local parallel admin editor/store.
- A seed-only product edited by a real manager should materialize into the canonical backend row using its existing code/taxonomy rather than creating a parallel fake product.

## Deployment and immutable tests
1. `/t` is the validation workspace; root `/public_html` is protected.
2. Never recursive-delete, mirror-delete or root-wide-sync over FTP.
3. Prototype deployment may write only to `/public_html/t`.
4. Released tests are immutable snapshots.
5. Tests 01–28 are frozen. Test 28 is preserved at snapshot/test28-final and must never be modified; the active mutable UI lane is Test 29.
6. Do not modify an older numbered test to improve a newer one.
7. `/t/index.htm` is mutable and newest test must be first.
8. Current Vue test is generated from `/platform/frontend`; do not hand-edit compiled test files on the host.

## UX
9. Persian-first/RTL-first; English LTR; Arabic/Sorani RTL.
10. Mobile-first with desktop responsive extension.
11. Guest browsing and WhatsApp inquiry do not require login.
12. Bottom navigation contains top-level destinations only.
13. WhatsApp handoff always shows a message preview first.
14. Six request paths are domain values, not duplicated UI strings.
15. Fixed vs negotiable specs differ by icon, label and surface — never color alone.
16. Adaptive product details: bottom sheet mobile, side drawer desktop.
17. Product/category media preserves the full garment silhouette when cropping would hide meaningful edges; use a polished contained fallback instead of destructive crop.
18. Shimmer must not cause layout shift.
19. Touch targets, focus-visible, safe-area and reduced-motion behavior are required.

## Visual/media policy
20. Palette anchors: `#21946A`, `#151EDA`, `#C8E3DB`, `#FFFEFF`, `#FFB514`.
21. UI icons use Lucide Vue imports; no runtime global SVG sizing rules.
22. Final women photography must be fully modest Islamic hijab, no visible hair and no revealing/body-emphasizing styling.
23. The canonical generated set under `platform/frontend/public/images/final` is the default media source for the current Vue prototype and every later numbered prototype; temporary web stock is fallback-only and must not be presented as actual Armaghan products, factory, staff, customers or certificates.
24. Automated stock sourcing must use reuse-compatible licensing and record source, creator, license and checksum.
25. No runtime hotlinking for catalog photography; vendor an optimized local derivative.
26. Hidden Design Lab opens after 3-second top-bar long press and may switch design system/palette without changing business state.

## Vue 3 engineering
27. Frontend: Vue 3 + TypeScript + Vite + Vue Router + Pinia + Tailwind.
28. Use SFCs and `<script setup>`; no giant HTML-template strings.
29. Feature-first organization; stores only for cross-screen/domain state.
30. Components never construct WhatsApp URLs ad hoc; use the message-builder service.
31. Components do not read/write auth storage directly; use the session store.
32. Logout must be idempotent and available in every authenticated modal/sheet state.
33. SmartImage owns loading/error/fallback behavior for content photography.
34. Static tests use hash routing; production Laravel/Inertia may use server routes.
35. Type-check + unit tests + production build are deployment gates.

## Database/backend
36. Local/development/test defaults to SQLite with no MySQL credentials. Production uses MySQL/MariaDB because the verified host lacks PDO SQLite and exposes PDO MySQL.
37. Production target is Laravel 13.
38. Admin target is Filament 5.
39. Google OAuth uses Laravel Socialite.
40. Production direct links use high-entropy values stored hashed with revoke/regenerate, scope, expiry and audit.
41. Admin impersonation must be explicit, reversible and auditable.

## Host-centric asset management
42. Runtime binary media is host-centric and stored on Laravel local/public/private filesystem disks, not database BLOBs.
43. Public media starts under `storage/app/public/media` and is served through `public/storage`.
44. Private customer/order documents remain under private host storage and are served only through authorized/temporary access.
45. Primary Laravel media package: `spatie/laravel-medialibrary`.
46. Primary Filament integration: official `filament/spatie-laravel-media-library-plugin`.
47. Do not introduce S3/CDN as the primary runtime store unless explicitly approved later; off-host storage is backup/DR for now.
48. Uploads require MIME/decode validation, size/pixel limits and metadata stripping.
49. Generated derivatives include deterministic card/gallery/hero sizes; never upscale small originals.
50. Production customer/private originals never belong in Git.
51. Portrait placeholder derivatives are reproducible build artifacts generated from immutable landscape originals; never overwrite the originals.

## QA
52. Frozen-test guard runs before deploy.
53. SQLite schema+seed smoke test runs before deploy.
54. Current source contract, web-stock provenance policy, Vue unit tests, TypeScript and Vite build run before deploy.
55. Verify mobile/desktop, RTL/LTR, sheet close, logout, impersonation, favorites, wizard and WhatsApp.
56. No remote deletion to match Git.


## Repository memory and reversible experience policy
57. The repository is the operational project memory. Before planning or implementing a new numbered test, read `docs/PROJECT-RULES.md`, `docs/HANDOFF.md`, `docs/BACKLOG.md`, the current test audit, and relevant asset/media docs; chat memory is secondary.
58. Every new numbered test starts from a rollback checkpoint at the last delivered source commit. Never rely on destructive edits as the only way to change a customer-facing choice.
59. When a customer-requested UI choice conflicts with a reasonable owner-preferred alternative, default to the customer choice and preserve the alternative through a typed setting or mode when doing so is maintainable and does not create impossible UX states.
60. Device-specific Admin settings use viewport profiles, not user-agent detection. Test 26 profiles are: mobile <48rem, tablet 48–<64rem, desktop >=64rem, aligned with the existing responsive breakpoints.
61. Only high-value behavioral/layout differences belong in ordinary Admin settings. Do not expose arbitrary micro-spacing, minor radii or free-form color values. The Test 27 Visual Style Editor is the controlled exception for registered page targets: it may assign only approved palette tokens to supported text/background/border roles.
62. Test 26 unresolved marketing-image slots may use clearly labeled `placehold.co` runtime fallbacks in the numbered prototype only. Every such slot must exist in `docs/TEST26-IMAGE-REQUIREMENTS.md`; final approved assets are local files and catalog photography remains non-hotlinked.
63. Test 26 sections and alternate modes must remain modular: single hero must not delete the carousel, desktop expanded navigation must not delete the accessible mobile drawer, hidden Home product grid must remain recoverable, and category numbers should be hidden by policy rather than removed from domain data.

## Shared concurrency lock for all chats, agents and automations
64. The shared Armaghan write lock is mandatory for every chat, AI agent, automation, script or human-assisted tool that intends to mutate this repository, create a numbered test, update the launcher, deploy prototype content, or otherwise change project state. Read-only inspection may happen without the lock; the first mutating action may not.
65. Canonical lock location: branch `coordination/armaghan-lock`, file `.armaghan-work-lock.json`. This coordination branch is operational state only and must never be merged into `main`.
66. Before any write, fetch the lock file and its current blob SHA. If its state is `active` and `expires_at` is still in the future, stop the write run immediately and report that another Armaghan worker holds the lock.
67. To acquire an available or expired lock, replace the lock file on `coordination/armaghan-lock` using the exact blob SHA just fetched. Set at least: `state=active`, `holder`, `run_id`, `acquired_at`, `expires_at`, `scope`, and `base_ref`. The default lease is 90 minutes. If the SHA-guarded update conflicts or fails, treat that as losing the race: perform no project write and re-read the lock.
68. A worker still active near the lease limit must renew the lease before 60 minutes have elapsed, again using a fresh fetch plus exact blob-SHA update. Never overwrite the coordination file without the current SHA.
69. Release the lock in a finally-style cleanup step by setting `state=released`, `released_at`, and preserving the holder/run metadata. Only the holder with the matching `run_id` may release its lock. Never release or rewrite another active holder's lease.
70. An expired lease may be reclaimed only through the same SHA-guarded compare-and-update flow. If there is ambiguous evidence of very recent work by another Armaghan run, chat, workflow or branch, prefer safety: do not reclaim; skip and report the possible collision.
71. Lock acquisition must happen before editing source/docs/backlog, creating a new `/t/NN`, changing `/t/index.htm`, committing implementation changes, or triggering a mutation/deployment that belongs to the run. Planning and repository reading may precede acquisition, but must be revalidated after acquiring if state could have changed.
72. A CI/test/deploy workflow explicitly triggered as part of the current lock holder's change set is considered part of that holder's lease. Any independent automation that can mutate repository/project state must acquire the same lock first.
73. Every Armaghan-focused chat/agent must read root `AGENTS.md` and this file before mutation. Tool-specific instruction files may summarize this protocol but must point back here as the canonical rule.
74. If the lock mechanism itself is unavailable or cannot be checked reliably, fail closed: do not mutate Armaghan. Report the blocker instead.



## Communication terminology rule
75. In every user-facing Armaghan surface—including chat replies, reports, explanations, handoffs, automation summaries and review notes—the first use of each specialist/technical term must be followed immediately by a short plain-Persian explanation in parentheses. Example: `Feature Flag (کلید تنظیمی برای روشن/خاموش‌کردن یک رفتار بدون حذف کد)`.
76. Acronyms and implementation jargon follow the same rule on first use in a response. Prefer the shortest explanation that teaches the term without interrupting the answer. Ordinary product labels and already-explained terms in the same response do not need repeated definitions.
77. When contrasting implementation strategies, distinguish `Feature Flag` / configuration-driven behavior from `hard-coded` behavior precisely: hard-coding is a fixed value/decision embedded directly in code; it is a common contrast to configurable behavior, but it is not the only or formal logical opposite of a feature flag.


## Backend MVP phase
78. Test 26 is frozen at snapshot branch `snapshot/test26-final`. Backend productionization may read/reuse its source patterns but must not mutate or redeploy `/t/26`.
79. The active delivery phase is Backend MVP: Laravel 13 + SQLite as the primary local/dev/test/production database; Filament 5 remains the admin baseline. JSON is plain import/export only. MySQL/MariaDB is an optional later logical mirror/export target, not a live dual-write dependency.
80. Minimum production scope is: persistent product CRUD, customer CRUD, product media, public catalog reads, anonymous favorites-share links, one-tap customer magic-link login, WhatsApp handoff, site/theme settings, backup and deployment health.
81. Google OAuth, full order/timeline workflow, advanced analytics, queues and nonessential integrations are post-MVP unless they become required for delivery. Production SQLite verification and private-path backup readiness are P0 before the first production migration.
82. Brand colors are semantic design tokens, not arbitrary per-element CSS. Admin may assign the approved palette colors to major semantic roles (for example header, primary action, secondary action, highlight, active state and soft surface) with contrast validation and preview.
83. Token-role reassignment is configuration-driven theming, not a Feature Flag. Feature Flags switch capabilities/behaviors on or off; theme configuration maps values to presentation roles.

## Frozen Test 26 / open Test 27+ release lane
84. Main-branch frontend development remains open for customer UI changes, but frozen Test 26 must never be rebuilt or redeployed from main. Any new customer-facing UI snapshot starts at Test 27 or higher.
85. Local Vite builds must default to the non-numbered `.build/frontend` preview directory. A numbered UI output requires explicit `ARMAGHAN_UI_TARGET`; main must reject any numbered target <=26.
86. GitHub Actions uses `ACTIVE_UI_TEST=27` as the next release lane until Test 27 is explicitly delivered. Advancing the lane requires freezing the delivered test, adding it to `docs/IMMUTABLE-TESTS.txt`, and updating release contracts in the same atomic change.
87. Backend MVP work and UI evolution are parallel lanes: urgent customer UI requests may proceed in the next unfrozen numbered test without waiting for backend completion, provided the shared lock, regression contracts and deployment scope rules are respected.


## Verified production-host constraints
88. Hosting preflight on 2026-09-30 originally found no PDO SQLite. A fresh authoritative probe on 2026-10-01 after the hosting change verified PDO SQLite, SQLite 3.53.4, private file read/write, foreign keys and `VACUUM INTO`; production SQLite is now PASS and active.
89. Host web-PHP disables `proc_open`, `exec` and `shell_exec`. Do not make production deployment depend on server-side Composer or shell execution; build dependencies in CI and upload a prepared release.
90. Host PHP has `open_basedir` enabled but the verified private sibling layout is accessible. Keep application/private files outside `public_html` and expose only the Laravel public surface.


## Backend implementation baseline
91. Canonical backend application root is `platform/backend`. Bootstrap resolved Laravel Framework 13.34.0 and Filament 5.9.0; `composer.lock` is the reproducible dependency source of truth and must be committed with dependency changes.
92. Backend changes on `main` must pass `.github/workflows/backend-ci.yml`: Composer validation/install from lock, isolated SQLite migration, framework/admin major checks, tests, locked dependency audit and secret-hygiene checks.
93. Generated Laravel agent/bootstrap files do not override Armaghan rules. Do not auto-install Laravel Boost, starter kits or other packages unless the project backlog/architecture explicitly selects them.
94. Never commit backend `.env`, `APP_KEY`, database credentials, production customer data or `vendor/`. Production credentials remain environment/server secrets.
95. A Filament package being installed does not mean production admin access is complete. Before production exposure, the User model must enforce an explicit panel-access policy and administrator provisioning must use environment/runtime secrets rather than hard-coded repository credentials.

## Test 27 visual editor rules
96. The approved base palette is exactly: Brand Green `#21946A`, Brand Blue / logo-background blue `#0714C2`, Brand Mint `#C8E3DB`, Brand White `#FFFFFF`, Brand Gold `#FFB514`. `#0714C2` was selected from the actual repository logo image by pixel analysis on 2026-10-01 and replaces the earlier `#151EDA` canonical blue. These values are canonical visual-editor tokens unless the owner explicitly changes the palette later.
97. The Visual Style Editor is an admin-only plugin layer. Turning it off must remove its inspector, selection listeners and edit outlines without removing the saved style/content profile.
98. On mobile, the primary inspector is a non-modal bottom sheet that coexists with the page. It defaults near half-height, has a drag handle, respects safe areas and keeps the page above scrollable/selectable. A small floating control may be used only as a launcher/shortcut, not as the primary mobile inspector.
99. Editable page targets require stable `data-style-id` identifiers. Touch selection must not guess when nested/nearby targets are ambiguous: collect candidates from the rendered touch neighborhood and ask the administrator which target was intended.
100. The editor must not accept arbitrary CSS, HTML, JavaScript or unrestricted color input. Style overrides are structured and may reference only validated approved tokens. Generated CSS is derived output, never trusted free-form input.
101. Text overrides are content data, not CSS. They are stored separately per locale and are allowed only on elements explicitly marked text-editable. Canonical domain data such as product/customer records remains owned by its domain editor rather than being silently overridden as visual text.
102. Hidden elements must remain recoverable from the editor even when they can no longer be tapped on the page. Resetting a target removes its editor overrides and returns control to the normal component/theme value.
103. Every new numbered UI test must use its own browser-state namespace. A later test served on the same origin must never write keys belonging to a frozen earlier test. Test 27 uses `armaghan:test27:*`; Test 26 keys remain untouched.
104. Test 27 is the active mutable UI review lane. The owner explicitly approved adding it to the test launcher on 2026-10-01. Test 26 remains frozen; Test 27 may continue evolving until it is explicitly frozen/delivered.

## Style Profile persistence rules
105. Style Profile persistence uses a mutable draft plus immutable versions and explicit publication pointers. Never implement restore by editing/deleting an old version row.
106. Allowed Style Profile publication channels are `staging` and `production`. A staging publish must never implicitly update production.
107. Style Profile write endpoints require an authenticated active administrator. Public clients may read only an explicitly published channel.
108. Never trust or persist arbitrary client CSS/HTML/JavaScript for the visual editor. Persist validated structured overrides/texts and generate CSS server-side from the approved token registry.
109. Style target identifiers are stable contract keys, not arbitrary selectors. Backend accepts only the restricted id character set and the whitelisted style/text fields documented in `docs/STYLE-PROFILE-BACKEND.md`.
110. Draft autosave must use checksum-based optimistic concurrency once connected to Laravel. On HTTP 409, surface the conflict and reload/reconcile; never silently overwrite newer server state.
111. Test 27 frontend integration must retain a local fallback during staged rollout so backend unavailability cannot break the customer-facing page. Server publication becomes the shared source of truth only after successful authenticated sync/deploy validation.

## SQLite-first persistence rules
112. SQLite is the primary Laravel database for local, development, test and the current production plan. Keep the production database file at an absolute private path outside `public_html`.
113. Before the first production migration, re-probe the host and confirm `pdo_sqlite`, foreign-key support and read/write access to both the private database path and private backup directory.
114. Primary database backups must remain SQLite-to-SQLite consistent snapshots. Prefer SQLite Online Backup API or `VACUUM INTO`; do not rely on a naive raw copy of a live database file.
115. MySQL/MariaDB may be added later as a logical mirror/export/disaster-recovery target. Do not make normal application requests dual-write to SQLite and MySQL.
116. A MySQL mirror is not the sole backup. Keep restorable SQLite snapshots and at least one rotated off-host copy before production handoff.
117. JSON is a normal portable interchange format for import/export, fixtures and optional bundles only. Do not build or maintain a custom JSON database engine, locking protocol, query layer or relational emulation.
118. Application/domain code should use normal Laravel Eloquent/query services against SQLite. Do not add an unnecessary repository abstraction solely to hide the selected SQLite engine unless a real alternate runtime store becomes required.
119. If sustained concurrent writes, multi-server deployment or materially heavier reporting makes SQLite unsuitable, re-evaluate MySQL/PostgreSQL based on measured requirements rather than record count alone.
120. Supabase remains an optional future PostgreSQL/Auth/Storage/Realtime infrastructure target and must not be introduced without a concrete requirement that justifies an additional backend boundary.

## Production backend activation rules
121. Production Laravel is deployed with the application and shared state outside `public_html`; only the Laravel public surface is exposed under `/backend`.
122. Production SQLite, `.env`, APP_KEY and backup snapshots must remain outside `public_html`; no future deploy may package or overwrite the host-managed shared `.env` or database file.
123. Public Laravel directories/files must use web-readable permissions compatible with LiteSpeed (`0755` directories, `0644` files). Do not widen private application/data permissions merely to fix public serving.
124. Production backend deploys must build Composer/vendor in CI. Host PHP shell functions remain disabled and must not become a deployment dependency.
125. Before any future production migration, create or verify a consistent SQLite snapshot. A failed remote mirror must never block or corrupt the live SQLite source of truth.
126. Production backend canonical base path is `/backend`. Public API and Filament paths are therefore under `/backend/api/...` and `/backend/admin/...`.

## Test 27 editor color/saveability rules
127. Test 27 Home light-mode page background defaults to Brand White through the semantic `--role-page-background` role. The whole Home page background is a registered editor target (`home.page`) and may change only through approved palette tokens.
128. Primary Home panel surfaces default to Brand Mint through `--role-panel-background`. Individual registered panels may override that role through structured editor token assignments.
129. `home.page` is a protected non-hideable editor root. Do not allow the administrator to hide the whole page root; use `home.content` for visibility control so the editor always remains recoverable.
130. The canonical Brand Blue is the actual logo-background blue `#0714C2`. Header, Footer, Hero brand chrome and other semantic Brand Blue usages must resolve through tokens/roles rather than duplicating a hard-coded legacy blue.
131. Test 28 may export/import the structured Style Profile as plain JSON for portable manual backup/transfer. Import must always pass through the existing sanitizer; JSON is not a runtime database and must not accept arbitrary CSS/HTML/JavaScript.
132. Test 28 Style Profile API defaults to same-origin `/backend`. Mutating requests must use Laravel session authentication plus CSRF protection. Never treat the local prototype admin credential as backend authentication.
133. A real production Laravel administrator exists. Test 28 browser-local Style Profile persistence remains the resilience fallback; final shared browser acceptance is complete only after a real authenticated backend session successfully saves, reloads, publishes and restores without weakening session/CSRF protection.

## First-production-admin provisioning rules
134. First-production-admin provisioning must fail closed when any active administrator already exists. Never seed or commit a default production password.
135. Plaintext administrator credentials must never be written to repository files, GitHub logs, artifacts or documentation. Production bootstrap credentials may be generated on-host and only returned through encrypted transport.
136. The canonical bootstrap account may use a temporary operational email such as `admin@armaghan.local` until the owner replaces it with a real mailbox; do not assume password-reset email delivery works before that change.
137. Any credential recovery after a partial bootstrap must prove the target account identity before rotation. At minimum require one active admin, exact bootstrap email/name and a creation-time bound that matches the failed bootstrap window.
138. Credential encryption/key validation must complete before creating or rotating a production administrator. A cryptographic-output failure must not leave behind a new account with an unrecoverable password.
139. After successful first-admin provisioning, remove any one-shot workflow or temporary public helper. Keep only reusable fail-closed tooling and tested backend commands.

## Current-status memory rules
140. `docs/CURRENT-STATUS.md` is the canonical summary of the repository's **current** operational state. Every repository mutation run must read it after `docs/PROJECT-RULES.md` and before planning work.
141. Historical records in HANDOFF/audits/preflight documents remain valid as history, but an older statement must not override a newer verified status in `docs/CURRENT-STATUS.md`, latest HANDOFF, latest BACKLOG or newer successful production acceptance.
142. After any run that materially changes production capability, active UI test state, deployment readiness, authentication/admin readiness or the next P0 task, update `docs/CURRENT-STATUS.md`, `docs/HANDOFF.md` and `docs/BACKLOG.md` in the same bounded documentation closeout.
143. Current verified state as of 2026-10-02: production SQLite/Laravel activation, first production admin, Filament CRUD, guarded code-only + additive deployment, public catalog, product media, Customer session/Magic Link, persisted FavoriteShare/WhatsApp, encrypted daily off-host SQLite backup and artifact round-trip restore drill are complete. Tests 01–28 are frozen; Test29 is the active mutable UI lane. Production Style Profile server semantics are acceptance-tested PASS; authenticated browser acceptance remains opportunistic.
144. Remaining core MVP work is final end-to-end QA + delivery handoff. Browser acceptance for Style Profile, one real product image and one real Magic Link remains opportunistic and must never weaken authentication.
145. Public catalog reads are Backend-authoritative for every code present in Backend. If a Backend-managed product/category/subcategory is inactive, Test 29 must suppress any stale local copy. Local fallback may remain only for unmanaged staged-migration data or temporary API unavailability.
146. Product media ownership is implemented. Test 29 may use the existing local media/spec fallback only for Products whose server gallery is still empty or while the catalog API is unavailable; do not claim fallback media is persisted Backend media.
147. `armaghan:bootstrap-catalog` is a guarded first-initialization tool only. It must default to dry-run, require an explicit apply flag, and refuse execution unless Category, Subcategory and Product tables are all empty. Never convert it into an update-or-create reseeder that can overwrite administrator changes.
148. Safe ordinary Backend code changes under `platform/backend/**` automatically enter the guarded code-only deploy lane. That lane must continue to refuse migration or Composer dependency drift; use a separately designed snapshot/migration/rollback flow for schema/dependency-changing releases.


## Production product-media and additive-release rules
149. Product media ownership is canonical in Spatie Media Library on the Product model, collection `product-gallery`. The official Filament Spatie Media Library plugin is the admin upload/reorder surface; do not create a parallel product-image table or browser-only canonical media store.
150. Product uploads are public catalog photography only: JPEG/PNG/WebP, maximum 8 MiB and 5000×5000 per file, maximum 6 ordered files per product. Decode every accepted file, re-encode the stored original to strip metadata, preserve orientation, avoid destructive crop, and never upscale derived card/thumb images.
151. Product media paths must remain under the public disk prefix `media/products/<media-id>/`; originals and conversions belong to separate unique media-id directories. Public delivery uses Laravel `/backend/storage`; future code swaps must preserve/recreate the public storage symlink.
152. Public catalog server media is authoritative when a Product has at least one Backend media item. Test 27 may retain the existing local media/spec fallback only while that Product has no server media or the catalog API is unavailable. Do not mix local fallback images into a Product once a server gallery exists.
153. Dependency/schema-changing Backend releases use the guarded additive lane, not the code-only lane. Existing migrations must remain byte-identical; currently allowed new migrations are create-only additive migrations. Create a consistent SQLite snapshot before migration, restore it if the migration itself fails, and keep old code available for rollback until production smoke succeeds.


## Customer session and Magic Link rules
154. Customer authentication uses Laravel same-origin session state under a separate customer-session key. Do not replace or invalidate Filament admin authentication when a customer session starts or ends; rotate the session identifier to prevent fixation.
155. Magic Link bearer tokens must be high entropy and stored only as one-way SHA-256 hashes. Never write raw Magic Link tokens to database columns, activity logs, repository files, CI logs or documentation.
156. Production Magic Link URLs keep the raw token only in the browser fragment under Test28 (`#/magic/<token>`). Do not put the token in a backend path or query string. Test28 consumes it through the fixed same-origin POST endpoint and immediately replaces the route with `/tracking`.
157. Magic Links are one-time, expiring, revocable and scoped. Issuing a new customer-portal Magic Link revokes prior unused active links for that customer. Replay, expired, revoked, inactive-customer and disabled-direct-link attempts must fail generically without identity disclosure.
158. Customer Magic Link consume and self-service mutations must remain rate-limited and CSRF-protected. Guest `GET /api/customer/session` must remain unauthenticated (HTTP 401); GET on the POST-only consume endpoint should remain HTTP 405 as a production route smoke.
159. Customer session API responses and customer-writable profile fields must remain explicitly allowlisted. Do not expose or accept internal notes, priority, user ownership, admin state, activity logs or other operational fields through customer self-service.
160. Test28 may retain demo/local auth only as a review fallback when no real Backend customer session exists. A real Backend customer session is authoritative. Do not treat demo credentials or browser-local Magic Link data as production authentication.


## FavoriteShare and WhatsApp rules
161. New FavoriteShare links are Backend records. Store only a SHA-256 hash of the high-entropy raw share token; never persist or log the raw token. New tokens must retain at least 128 bits of entropy; the current short format is 22 cryptographically random alphanumeric characters.
162. New raw FavoriteShare tokens belong only in the short browser fragment (`#/s/<token>`), never in backend paths/query strings. Historical `#/favorites/share/<token>` links remain read-only compatible. Resolve both formats through the fixed same-origin CSRF-protected POST endpoint.
163. FavoriteShare issue must preserve requested product order and fail if any requested Product, Subcategory or Category is inactive/unavailable. Public resolve must expose only ordered active product codes + expiry metadata, never share-owner identity or customer data.
164. Guest shares expire after 7 days and intentionally have no authenticated revoke surface. Customer-owned shares expire after 30 days and may be revoked only by the same real Backend customer session. These TTL defaults are configuration-backed and may be shortened, not silently made permanent.
165. Test28 must not generate product-code-in-URL FavoriteShare links. Historical `?shared=v1:` links may remain read-only compatible only. All new native/copy/WhatsApp sharing uses the persisted server-backed URL.
166. Wishlist/customer attribution must use the real `currentCustomerId` (or explicit admin impersonation id). Never hard-code Customer #1 for an authenticated customer. WhatsApp handoff must use the persisted share URL and the normalized existing seller WhatsApp path.


## Test 28 and off-host backup rules
167. Test 27 is frozen and immutable. Tests 01–28 are frozen. Test 29 is the only active mutable numbered UI lane until explicitly superseded. Current browser-local/session writes use `armaghan:test29:*`; never write earlier-version or shared legacy locale keys.
168. The active Vite and FTP lanes must reject generated UI targets 28 or lower. `/t/index.htm` remains mutable and must list Test29 above Test28; retain all prior links.
169. Production SQLite live backups must use SQLite's consistent online mechanisms (`VACUUM INTO` or an explicitly reviewed SQLite Backup API implementation). Never treat a raw filesystem copy of a live SQLite/WAL database as the canonical backup.
170. Because this GitHub repository is public, plaintext production SQLite/database exports must never be uploaded as Actions artifacts, committed to Git, printed to logs or otherwise transferred off-host unencrypted.
171. The canonical off-host workflow encrypts the verified production snapshot with authenticated encryption before it leaves the production host. The artifact may contain only ciphertext plus a non-secret verification manifest. Current cipher is AES-256-GCM; key derivation is PBKDF2-HMAC-SHA256 with per-backup salt.
172. Every scheduled off-host backup must be followed by an isolated artifact round-trip restore drill: download the uploaded artifact, verify artifact/ciphertext and plaintext checksums, SQLite integrity, foreign keys, table inventory/counts and ability to open/rollback a write transaction. Restored plaintext must remain temporary and be destroyed at job end.
173. Off-host backup cadence is daily; current artifact retention is 14 days. A dedicated `ARMAGHAN_BACKUP_PASSPHRASE` GitHub secret is preferred. When absent, the explicitly documented FTP-secret-derived KDF fallback may be used temporarily, but no raw secret may appear in artifacts/logs/manifests.
174. Before rotating/discarding a secret that is needed to derive keys for retained backup artifacts, ensure either those artifacts have expired or the old recovery secret is retained securely. Adding a dedicated backup secret is an operational P1, not a reason to weaken encryption or publish plaintext backups.


## Test29 nonvisual release rules
175. Owner deferred visual testing on 2026-10-02. Nonvisual release work may use automated language/storage/auth/share/contrast contracts, type-check and build/deploy gates. Never mark deferred visual/device/authenticated-browser acceptance complete.
176. Test28 is frozen at snapshot/test28-final. Test29 uses its own browser write namespace, including manual language selection; no write-back to frozen version keys.


## Owner-authorized independent root releases
177. On 2026-10-02 the owner explicitly authorized publishing selected Test29 at domain root. This supersedes rules 1/3 only for the guarded root index promoter; never root-wide sync or overwrite backend, routing, logo, private data, or frozen numbered folders.
178. config/root-release.json is the durable root-version selector. Numbered UI development must not implicitly change this selector. Any existing positive numbered version may be promoted without rebuilding its frozen files; verify its assets, preserve prior root HTML and restore prior entry on verification failure.
179. Root publication uses production Style Profile; /t remains staging. Root and /t keep functional hash routes and same-origin backend authentication.
180. Test29 Home eyebrow defaults are hidden and explicitly recoverable in the visual editor. Producible customer labels are concise, centered, smaller and weight 400. Do not replace canonical production product/customer management with browser-only stores.

## Custom administration and deliberate advanced access
181. Vue custom administration is the ordinary surface. Never send ordinary login, management links or failed authorization into Filament. Protect stock-panel HTTP and persistent Livewire requests.
182. Only the active verified primary owner may deliberately select a genuine integration gap, diagnosis or recovery reason to open stock administration. Permit must be audited, session-scoped, expire after 15 minutes and end on logout/revocation. Other business admins retain ordinary custom-domain API permissions.
183. Preserve the established custom tables/forms and visual editor while connecting all remaining prototype capabilities to canonical server persistence. Browser-only data or enabled links are not migration completion.
184. Desktop language picker shows the language name alone. Mobile header always offers help, globe and language abbreviation with centered themed chooser, brightness toggle and real-session sign-in/logout icon. Fresh default is light. Capabilities intro defaults hidden with reversible feature flag.
