# Armaghan — Product Backlog

Priority: **P0 current**, P1 next, P2 later.  
Current implementation target: **Test 19 — Production Polish + Admin CRM + Managed i18n**.  
Detailed ranked UX decisions: `docs/TEST19-UX-AUDIT.md`.

## P0 — Test 19

### Snapshot + delivery
- [ ] Freeze Tests 01–18.
- [ ] Build a new immutable `/t/19` artifact.
- [ ] Keep `/t/index.htm` mutable and newest test first.
- [ ] Add Test 19 contract before deployment.
- [ ] Keep root untouched and FTP non-destructive.

### Mobile/tablet header + drawer
- [ ] Header contains only brand + theme + hamburger below desktop breakpoint.
- [ ] Remove mobile/tablet header login.
- [ ] Drawer language controls become 44px rounded-square buttons: Fa / En / ع / ک.
- [ ] Drawer width becomes `clamp(260px,70vw,340px)`; tablet max 340px.
- [ ] Drawer scrim stays 40% dark + blur.
- [ ] Drawer login button moves above divider in fixed footer.
- [ ] Show small readable `تولیدی ارمغان` / localized manufacturer label below login.
- [ ] Add Help/Guide entry in drawer in all four languages.
- [ ] Animate drawer enter/leave from the side in 200ms; honor reduced motion.

### Product copy / typography / visual consistency
- [ ] Remove public-facing prototype/test words: SVG, آزمایشی, demo/test wording, technical media labels.
- [ ] Replace ad-hoc typography with semantic scale.
- [ ] Use Vazirmatn FD for fa/ar/ku and Roboto for English.
- [ ] Hide horizontal scrollbar chrome in category chips while preserving scrolling.
- [ ] Reserve layout space for bottom nav so content is never obscured.
- [ ] Keep header z-index above all normal scrolling content.
- [ ] Replace harsh dark gradients with neutral surfaces + restrained brand glow.
- [ ] Remove hardcoded light surfaces from primary production/admin/customer components.
- [ ] Use truthful generic garment-manufacturer copy only; no invented certifications/prices/claims.

### Sheet/modal motion
- [ ] BaseSheet enters from bottom and exits to bottom over 200ms.
- [ ] Backdrop fades in/out with the sheet.
- [ ] Exit animation must complete before DOM removal.
- [ ] Preserve focus trap, Escape, outside-tap close and reduced-motion behavior.

### Product card action modes
- [ ] Keep admin-selectable Compact / Labeled modes.
- [ ] Compact remains default.
- [ ] Labeled mode uses localized `Order / Favorite / Specs`.
- [ ] Keep WhatsApp brand treatment, neutral favorite container and centered icon geometry.

### Admin IA
- [ ] Add top-level admin views: Overview / Customers / Products / Languages.
- [ ] Overview can show customers + products together.
- [ ] Customers and Products have dedicated focused views.
- [ ] Add selectable data tables with row checkboxes, select-all and contextual batch action bar.
- [ ] Add bulk delete with confirmation for customers and products.
- [ ] Add customer create/delete.
- [ ] Add vertical overflow menu per customer row.
- [ ] Replace text-heavy impersonation action with icon + accessible label.
- [ ] Add dedicated customer-management panel/detail view.

### Customer 360 editor
- [ ] Editable name, email, WhatsApp, country/flag, address, location text, notes.
- [ ] Editable current order state and timeline stage.
- [ ] Show previous-order count and current-order summary.
- [ ] Profile-photo upload preview in prototype.
- [ ] Password set/reset control in prototype.
- [ ] Direct-access link control with concise modes: `Permanent` / `Expiring`.
- [ ] Expiring link exposes expiry/revoke controls.
- [ ] Separate `Manage customer` from `Impersonate customer`.

### Wishlist lead notifications
- [ ] Add admin alert metric for customers/visitors whose favorites changed.
- [ ] Model anonymous visitors with generated visitor token, not IP as identity.
- [ ] IP is documented as optional backend metadata only.
- [ ] Logged-in lead actions expose contact CTA.
- [ ] Guest lead actions expose in-app message path for their shared favorites page.
- [ ] Add `Invite to create account` action for guest leads.

### Authentication surfaces
- [ ] Keep working prototype username/password sign-in.
- [ ] Add prototype registration form and persisted prototype account.
- [ ] Add magic-link prototype with generated/revocable token and Permanent/Expiring mode.
- [ ] Add Google sign-in button wired to a backend endpoint contract; do not fake OAuth success.
- [ ] Document Laravel Socialite credentials/backend requirement as the only blocker for live Google OAuth.
- [ ] Keep login modal centered with blurred backdrop.

### i18n completeness
- [ ] Move visible application copy into a single translation registry.
- [ ] Add persisted per-language translation overrides.
- [ ] Eliminate mixed-language UI in Home / Products / Production / Favorites / Tracking / Login / Drawer / Admin.
- [ ] Keep `html lang` and semantic `dir` correct for each locale.
- [ ] Keep overall shell geometry stable across languages with CSS; English content and controls are LTR/left-aligned.
- [ ] Translate product category/subcategory/product/spec labels needed by current UI.

### Translation manager
- [ ] Add Admin → Languages.
- [ ] Language selector + section/group selector + search.
- [ ] Every translatable key has its own editable field.
- [ ] Single-item save/reset.
- [ ] Multi-select + bulk reset.
- [ ] Show base value and overridden value clearly.
- [ ] Persist overrides locally in prototype; design API shape for later DB persistence.

### Repository memory
- [x] Add detailed 5-option ranked analysis for each requested item and self-detected visual/product bugs in `docs/TEST19-UX-AUDIT.md`.
- [ ] Update backlog checkboxes as each implementation block lands.
- [ ] Update `pics.md` only if image requirements change.

## P1 — Backend productionization
- [ ] Laravel 13 + SQLite local/development backend.
- [ ] Filament 5 admin.
- [ ] Persist customers/products/orders/timelines/translations/visitor leads to SQLite.
- [ ] Laravel Socialite Google OAuth with real client credentials.
- [ ] Signed/hashed magic links with expiry, scope, revoke and audit.
- [ ] Server-side media library and queued image conversions.
- [ ] MySQL migration/staging after SQLite model stabilizes.

## Definition of Done — Test 19
- [ ] Tests 01–18 immutable.
- [ ] Test 19 first in launcher.
- [ ] No public prototype/test wording.
- [ ] No mixed-language major flow for fa/ar/en/ku.
- [ ] Mobile drawer, bottom sheets and theme transitions visually coherent.
- [ ] Admin supports focused sections, multi-select, batch delete, customer CRUD and Customer 360 edit.
- [ ] Translation editor works with per-key and bulk reset.
- [ ] Auth surfaces clearly distinguish functional prototype flows from backend-required Google OAuth.
- [ ] Test 11–19 contracts, type-check, unit tests, build, FTP smoke and deploy all pass.
- [ ] Root deploy skipped; no remote file deletion.
