# Armaghan — Product Backlog

Priority: **P0 current**, P1 next, P2 later.  
Current implementation target: **Test 14 — Keyboard & Focus Accessibility**.

## P0 — Test 14

### Snapshot safety + CI
- [x] Freeze Tests 01–13.
- [x] Keep launcher mutable and newest test first.
- [x] Build each Vue iteration to a new numbered `/t/NN` folder.
- [x] Keep type-check, unit tests, production build, SQLite, provenance and frozen-test guards before FTP.
- [x] Keep FTP incremental and non-destructive.

### Accessibility increment
- [x] Add keyboard skip-link to main content.
- [x] Move focus to main content after client-side route navigation.
- [x] Keep visible keyboard focus treatment.
- [x] Preserve reduced-motion behavior including skip-link transition.
- [x] Add a source contract that fails if these affordances regress.
- [ ] Add Playwright browser smoke flows including keyboard navigation.
- [ ] Audit sheet/drawer focus trapping and focus return.
- [ ] Audit semantic headings, accessible names and color contrast across all design palettes.
- [ ] Complete FA/AR/EN/KU-Sorani copy and direction regression tests.

### Host-centric asset management
- [x] Select `spatie/laravel-medialibrary` and official Filament integration.
- [x] Define host-local public/private media disks and provenance policy.
- [x] Vendor reuse-compatible web stock locally with license/checksum manifest.
- [x] Reject runtime hotlinks, NC/ND/unknown media and strip metadata.
- [ ] Bootstrap Laravel 13 backend and install pinned/tested Media Library v11 patch line.
- [ ] Validate hosting PHP extensions (GD/EXIF) before media conversion deployment.
- [ ] Add scheduled off-host backup + tested restore procedure.

### Catalog/data
- [x] 18 demo products with local image slots.
- [x] 6 demo customers plus seeded orders/timeline/favorites/media.
- [ ] Revalidate all six subcategory specs against source workbook before production import.

## P1 — Laravel backend
- [ ] Bootstrap Laravel 13 under `/platform/backend` with SQLite default.
- [ ] Convert schema/seed into Laravel migrations, seeders and factories.
- [ ] Install Spatie Media Library v11 and Filament 5 integration.
- [ ] Laravel auth + Socialite.
- [ ] Secure magic links with hash/revoke/expiry/scope/audit.
- [ ] Auditable impersonation.
- [ ] Host-local public/private media disks and deterministic conversions.
- [ ] Pest feature tests.

## P1 — Admin resources
- [ ] Customers.
- [ ] Products + category/subcategory/spec definitions.
- [ ] Media upload/reorder/archive.
- [ ] Orders/request type and timeline.
- [ ] Favorites and direct access links.
- [ ] Impersonation audit and prepayment state.

## P1 — MySQL cutover
SQLite remains default until Laravel behavior is stable. Then provision MySQL through environment/secrets, migrate staging, import data, run full tests, verify `utf8mb4`, and cut over. No MySQL credentials are required now.

## Definition of Done — Test 14
1. Tests 01–13 remain untouched.
2. All automated QA gates pass.
3. Test 14 is built from `/platform/frontend` into `/t/14` and listed first.
4. Keyboard users can bypass repeated navigation and route changes move focus to main content.
5. Existing logout, impersonation, six request paths, media provenance and no-image fallbacks remain regression-free.
6. FTP writes only Test 14 build/launcher and performs no remote deletion.
