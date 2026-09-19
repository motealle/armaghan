# Armaghan — Product Backlog

Priority: **P0 current**, P1 next, P2 later.  
Delivery rule: released UX tests are immutable snapshots. Current development target: **Test 11**.

## Current milestone — Test 11 / Product-like vertical slice

### P0.1 — Freeze previous tests + CI quality gate
- [x] Freeze `/t/01` through `/t/10` as immutable snapshots.
- [x] Add immutable-test policy file.
- [x] Add automated regression check that fails CI if a frozen test folder is modified.
- [x] Add Test 11 contract tests before FTP deployment.
- [x] Add SQLite schema test before deployment.
- [ ] After Test 11 approval, freeze `/t/11` too.

### P0.2 — Visual foundation
- [x] Adopt approved color-bar anchors:
  - green `#21946A`
  - royal blue `#151EDA`
  - mint `#C8E3DB`
  - near-white `#FFFEFF`
  - gold `#FFB514`
- [x] Define 5 selectable design systems.
- [x] Define 5 selectable color sets.
- [x] Add hidden long-press design lab (3 seconds on top bar).
- [x] Replace ad-hoc icons with a consistent Lucide icon system.
- [x] Add scoped icon sizing to prevent giant SVG/icon regressions.
- [x] Add skeleton/shimmer loading states.
- [x] Add image fallbacks that stay attractive when photography is missing.
- [x] Create `docs/pics-Prompt.md` with the first production image pack prompts.
- [ ] Receive generated photography from owner.
- [ ] Resize, crop, compress and convert supplied images to WebP/AVIF.
- [ ] Insert final photography and verify art direction consistency.

### P0.3 — Test 11 customer-facing shell
- [x] Create new immutable-candidate folder `/t/11`; do not alter Tests 01–10.
- [x] Use Tailwind in Test 11.
- [x] App-like responsive shell, Persian-first RTL.
- [x] Hero slideshow with 3 visual slots and robust fallbacks.
- [x] Image-led cards for Baby, Kids and Women categories.
- [x] Product-card action row: WhatsApp, Favorites, Details.
- [x] Adaptive product detail sheet/drawer.
- [x] Fixed vs negotiable specifications use icon + label + surface treatment.
- [x] Bottom navigation: Home / Products / Production / Favorites / Tracking.
- [ ] Finish all six product subcategory datasets from the approved source spreadsheet.
- [ ] Finalize four-language copy review: FA / AR / EN / KU-Sorani.
- [ ] Accessibility pass: focus, labels, reduced motion, contrast.

### P0.4 — Demo authentication and roles
- [x] Prototype login UI.
- [x] Demo admin credentials: `1 / 1`.
- [x] Demo customer credentials: `2 / 2`.
- [x] Logout action.
- [x] Admin/customer/guest UI states.
- [x] Prototype magic-link recognition for Test 11.
- [ ] Production auth: Laravel session auth.
- [ ] Google login through Socialite.
- [ ] Production username/password reset flow.
- [ ] Production magic links: hashed token, expiration, revoke/regenerate, scope and audit.

### P0.5 — Admin vertical slice
- [x] Admin dashboard shell.
- [x] Customer list with country, WhatsApp, email, active-order state and quick actions.
- [x] Customer detail view with order timeline, favorites and history placeholders.
- [x] Demo impersonation/customer view.
- [x] Product add/remove in browser demo state.
- [x] Client-side image upload + resize/compression preview for prototype.
- [ ] Filament resources for Customers, Products, Orders, Timelines, Media and Magic Links.
- [ ] Server-side media conversion WebP/AVIF and multiple responsive sizes.
- [ ] Real audit trail for admin impersonation and customer mutations.

### P0.6 — Customer account vertical slice
- [x] Customer dashboard shell.
- [x] Favorites view.
- [x] Active-order timeline.
- [x] Reorder / prepayment UI placeholders.
- [ ] Production customer mutations and notifications.
- [ ] Real shareable favorites/order links.

### P0.7 — Database foundation
- [x] Default development database: **SQLite**.
- [x] Add database schema draft for users, customers, products, product images/specs, favorites, orders, timeline, magic links and audit/activity.
- [x] Add repeatable SQLite bootstrap script.
- [x] Add automated schema smoke test using in-memory SQLite.
- [x] Add environment example that requires no MySQL credentials for local/dev use.
- [ ] Convert schema draft into Laravel migrations during Laravel bootstrap.
- [ ] Seed demo admin/customer/product data.
- [ ] Add database factories and Pest feature tests.

## P1 — Production Laravel application

### Architecture
- [ ] Bootstrap Laravel application in `/platform`.
- [ ] Customer frontend: Inertia + Vue 3 + Tailwind.
- [ ] Admin: Filament.
- [ ] Authentication: Laravel auth + Socialite + secure magic links.
- [ ] Authorization: roles/permissions and impersonation audit.
- [ ] Media: server-side validation, resize, optimization, WebP/AVIF.
- [ ] Queue/cache: Redis when hosting permits.
- [ ] Activity timeline/audit log.
- [ ] PWA production build and update strategy.

### Data
- [ ] Normalize Excel catalog into database seed/import format.
- [ ] Category → subcategory → product → spec-definition model.
- [ ] Locked/negotiable values stored explicitly.
- [ ] Product availability and six request paths stored as business rules, not UI-only assumptions.

## P1 — MySQL transition
SQLite is the default until the application is mature enough to deploy the Laravel backend.

When moving to MySQL:
- [ ] Provision database and credentials.
- [ ] Set `DB_CONNECTION=mysql`.
- [ ] Set host, port, database, username, password through server secrets/env only.
- [ ] Run migrations on MySQL staging.
- [ ] Run feature/regression tests.
- [ ] Export/import required data from SQLite.
- [ ] Verify charset/collation is `utf8mb4`.
- [ ] Cut over only after staging verification.

No MySQL credentials are required in the repository today.

## P1 — Image integration
- [ ] Hero 01 brand.
- [ ] Hero 02 production.
- [ ] Hero 03 export.
- [ ] Baby clothing.
- [ ] Baby blanket.
- [ ] Girls.
- [ ] Boys.
- [ ] Women tunic — Islamic hijab, no visible hair, modest styling.
- [ ] Women casual/sportswear — Islamic hijab, no visible hair, modest styling.
- [ ] Capabilities.
- [ ] Documents/credentials.
- [ ] Sales communication.
- [ ] Admin cover.
- [ ] Placeholder illustration family.

## Definition of Done — Test 11
Test 11 can be frozen when:
1. Tests 01–10 remain byte-for-byte untouched by the Test 11 change set.
2. Automated QA passes before FTP deployment.
3. No giant UI icon/SVG regression exists.
4. Mobile and desktop layouts are usable.
5. Hero, category and product surfaces have final images or polished fallbacks.
6. Demo admin/customer login and logout work.
7. Product, customer, favorites and timeline demo flows work without a backend.
8. SQLite schema/bootstrap tests pass.
9. Newest-test launcher ordering is correct.
10. FTP deployment touches only `/public_html/t` and performs no remote deletion.
