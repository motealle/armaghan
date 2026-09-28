# Armaghan — Product Backlog

Priority: **P0 current**, P1 next, P2 later.  
Current implementation target: **Test 26 — Customer-approved Reversible Landing**.
Detailed ranked UX decisions: `docs/TEST26-UX-AUDIT.md`.
Image requirements: `docs/TEST26-IMAGE-REQUIREMENTS.md`.
Customer clarification script: `docs/TEST26-CUSTOMER-QUESTIONS.md`.
Rollback checkpoint: `rollback/test25-pre-test26` → `328f6c48ea9c4f1346702282ddb6d21ae1685a16`.


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
- [x] Unresolved capability/banner media and the owner-supplied About derivative remain explicitly tracked in `docs/TEST26-IMAGE-REQUIREMENTS.md`; runtime placeholders are deliberate, labeled fallbacks rather than silent missing assets.

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

## P1 — Backend productionization
- [ ] Laravel 13 + SQLite local/development backend.
- [ ] Filament 5 admin.
- [ ] Persist customers/products/orders/timelines/translations/visitor leads to SQLite.
- [ ] Laravel Socialite Google OAuth with real client credentials.
- [ ] Signed/hashed magic links with expiry, scope, revoke and audit.
- [ ] Server-side media library and queued image conversions.
- [ ] MySQL migration/staging after SQLite model stabilizes.

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
