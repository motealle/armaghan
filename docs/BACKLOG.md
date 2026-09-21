# Armaghan — Product Backlog

Priority: **P0 current**, P1 next, P2 later.  
Current implementation target: **Test 20 — Customer 360 + Product Administration**.  
Detailed ranked UX decisions: `docs/TEST20-UX-AUDIT.md`.

## P0 — Final generated image set

Decision record: `docs/IMAGE-GENERATION-AUDIT.md`.

- [x] Inventory and triangulate all image prompt/list/manifest files in the repository.
- [x] Rank five implementation options and select the current 10-asset contract from `pics.md`.
- [x] Generate 10 canonical source images with one coherent visual direction.
- [x] Derive optimized AVIF and WebP variants in the paths defined by `pics.md`.
- [x] Record prompt provenance, dimensions, sizes and checksums in a generated-media manifest.
- [x] Wire final media into the current Vue application without deleting stock fallbacks.
- [x] Run image-policy tests, type-check, unit tests and production build.

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
