# Armaghan — Product Backlog

Priority: **P0 current**, P1 next, P2 later.  
Current implementation target: **Test 12 — Vue 3 Product Foundation**.

## P0 — Test 12

### Snapshot safety + CI
- [x] Freeze Tests 01–11.
- [x] Keep launcher mutable and newest test first.
- [x] Protect frozen tracked test folders in CI.
- [x] Build Test 12 from source instead of hand-editing compiled output.
- [ ] Make Vue type-check, unit tests and production build mandatory before FTP deploy.
- [ ] Deploy generated Test 12 artifact only under `/public_html/t/12`.

### Vue 3 architecture
- [x] Vite + Vue 3 + TypeScript.
- [x] Vue Router.
- [x] Pinia.
- [x] Tailwind CSS Vite plugin.
- [x] Lucide Vue.
- [x] Feature-first folders for auth/catalog/orders/customers/admin/design.
- [x] Central WhatsApp message builder.
- [x] Central session/logout store.
- [x] Central production-order wizard state machine.
- [x] SmartImage with shimmer and graceful fallback.
- [x] Unit tests for auth/logout, WhatsApp and wizard transitions.
- [ ] Add Playwright after static build stabilizes.
- [ ] Accessibility regression pass.

### UX parity/improvement over Test 10
- [x] Adaptive product detail sheet/drawer.
- [x] Restore three production wizard paths.
- [x] Restore three card-based WhatsApp paths.
- [x] WhatsApp preview before handoff.
- [x] Strong WhatsApp visual treatment even without photos.
- [x] Favorites.
- [x] Category/subcategory/search/status filtering.
- [x] Hero slideshow with fallback.
- [x] Hidden 3-second Design Lab.
- [x] Five design systems and five color sets.
- [ ] Complete FA/AR/EN/KU-Sorani copy.
- [ ] Revalidate all six subcategory specs against the source workbook.

### Demo auth/customer/admin
- [x] Admin and customer demo roles.
- [x] Direct authenticated-header logout.
- [x] Logout clears impersonation.
- [x] Logout is also exposed inside open sheets.
- [x] Impersonation demo with explicit banner and stop action.
- [x] Customer timeline dashboard.
- [x] Admin customer table and product CRUD demo.
- [x] Prototype client-side image resize/WebP preview.
- [ ] Move all real auth/state to Laravel before real customer data is used.
- [ ] Google login via Socialite.
- [ ] Password reset.
- [ ] Secure direct links with revoke, expiry, scope and audit.

### Asset management
- [x] Document architecture in `docs/ASSET-MANAGEMENT.md`.
- [x] Create generated-review manifest.
- [x] Define media collections, conversions and frontend media contract.
- [ ] Install `spatie/laravel-medialibrary` during Laravel bootstrap.
- [ ] Install official Filament 5 Spatie Media Library plugin.
- [ ] Local public/private disks for development.
- [ ] S3-compatible object storage + CDN for production public media.
- [ ] Signed temporary URLs for private media.
- [ ] Server conversions: thumb/card/gallery/hero + responsive images.
- [ ] EXIF/location stripping and MIME/pixel/size validation.
- [ ] Import approved photography after review.

### Database
- [x] SQLite is default and needs no external credentials.
- [x] Schema draft covers users/customers/catalog/media/favorites/orders/timeline/direct links/activity.
- [x] Repeatable SQLite bootstrap and smoke test.
- [ ] Convert schema to Laravel 13 migrations.
- [ ] Seed demo data.
- [ ] Add Pest tests.

## P1 — Production backend
- [ ] Bootstrap Laravel 13 under `/platform/backend`.
- [ ] Keep SQLite for local/dev first.
- [ ] Integrate the Vue 3 customer UI through Inertia where appropriate.
- [ ] Filament 5 admin control plane.
- [ ] Authorization policies/roles.
- [ ] Laravel Socialite.
- [ ] Audit log for admin and impersonation.
- [ ] Queue media conversions and notifications.

## P1 — Admin resources
- [ ] Customers.
- [ ] Products + category/subcategory/spec definitions.
- [ ] Media upload/reorder/archive.
- [ ] Orders/request type.
- [ ] Order timeline.
- [ ] Favorites.
- [ ] Direct access links.
- [ ] Impersonation audit.
- [ ] Prepayment state.

## P1 — MySQL cutover
SQLite remains default until Laravel behavior is stable.

Then:
- [ ] provision MySQL;
- [ ] define connection only through deployment environment/secrets;
- [ ] migrate staging;
- [ ] import data;
- [ ] run full tests;
- [ ] verify `utf8mb4`;
- [ ] production cutover.

No MySQL credentials are required now.

## Definition of Done — Test 12
1. Tests 01–11 remain untouched.
2. Vue type-check, unit tests and Vite production build pass.
3. No giant SVG/icon regression.
4. Logout works from authenticated header, open sheet and impersonation.
5. All six request paths generate valid previews and WhatsApp links.
6. Wizard forward/back/reset is stable.
7. Missing photography never exposes broken-image UI or layout collapse.
8. Mobile/tablet/desktop are usable.
9. SQLite smoke test passes.
10. FTP writes only Test 12 build and mutable launcher under `/public_html/t`; no remote deletion.
