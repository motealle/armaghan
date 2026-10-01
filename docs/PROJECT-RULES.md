# Armaghan Project Rules

## Deployment and immutable tests
1. `/t` is the validation workspace; root `/public_html` is protected.
2. Never recursive-delete, mirror-delete or root-wide-sync over FTP.
3. Prototype deployment may write only to `/public_html/t`.
4. Released tests are immutable snapshots.
5. Tests 01–26 are frozen. Test 26 is the final customer-review snapshot and must never be modified; any later UI change starts in Test 27 or higher.
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
88. Hosting preflight on 2026-09-30 verified PHP 8.3.33, all Laravel 13 required PHP extensions, HTTPS, a safe readable/writable private sibling outside `public_html`, and PDO MySQL. The owner reports on 2026-10-01 that PDO SQLite has since been enabled. SQLite is now the selected production primary, but a fresh runtime probe confirming `pdo_sqlite` and private-path write access is mandatory before the first production migration.
89. Host web-PHP disables `proc_open`, `exec` and `shell_exec`. Do not make production deployment depend on server-side Composer or shell execution; build dependencies in CI and upload a prepared release.
90. Host PHP has `open_basedir` enabled but the verified private sibling layout is accessible. Keep application/private files outside `public_html` and expose only the Laravel public surface.


## Backend implementation baseline
91. Canonical backend application root is `platform/backend`. Bootstrap resolved Laravel Framework 13.34.0 and Filament 5.9.0; `composer.lock` is the reproducible dependency source of truth and must be committed with dependency changes.
92. Backend changes on `main` must pass `.github/workflows/backend-ci.yml`: Composer validation/install from lock, isolated SQLite migration, framework/admin major checks, tests, locked dependency audit and secret-hygiene checks.
93. Generated Laravel agent/bootstrap files do not override Armaghan rules. Do not auto-install Laravel Boost, starter kits or other packages unless the project backlog/architecture explicitly selects them.
94. Never commit backend `.env`, `APP_KEY`, database credentials, production customer data or `vendor/`. Production credentials remain environment/server secrets.
95. A Filament package being installed does not mean production admin access is complete. Before production exposure, the User model must enforce an explicit panel-access policy and administrator provisioning must use environment/runtime secrets rather than hard-coded repository credentials.

## Test 27 visual editor rules
96. The approved base palette is exactly: Brand Green `#21946A`, Brand Blue `#151EDA`, Brand Mint `#C8E3DB`, Brand White `#FFFFFF`, Brand Gold `#FFB514`. These values are canonical visual-editor tokens unless the owner explicitly changes the palette later.
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
