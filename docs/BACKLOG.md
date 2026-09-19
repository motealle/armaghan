# Armaghan — Product Backlog

Priority: **P0 current**, P1 next, P2 later.  
Current implementation target: **Test 13 — Host-Centric Media + Licensed Web Stock**.

## P0 — Test 13

### Snapshot safety + CI
- [x] Freeze Tests 01–12.
- [x] Keep launcher mutable and newest test first.
- [x] Build each Vue iteration to a new numbered `/t/NN` folder.
- [x] Keep Vue type-check, unit tests, production build, SQLite and frozen-test guard before FTP.
- [x] Add licensed-stock provenance policy to QA.
- [x] Keep FTP incremental and non-destructive.

### Host-centric asset management
- [x] Reframe runtime storage as host-centric.
- [x] Rank open-source Laravel media options.
- [x] Select `spatie/laravel-medialibrary`.
- [x] Select official Filament Spatie Media Library integration for backend phase.
- [x] Define public/private host disk structure.
- [x] Define upload/conversion/metadata/archive rules.
- [x] Add automated Wikimedia Commons vendor pipeline.
- [x] Reject NC/ND/unknown media and runtime hotlinks.
- [x] Store creator/source/license/checksum provenance.
- [x] Re-encode to local WebP and strip metadata.
- [x] Add in-product credits screen.
- [ ] Install Media Library in Laravel backend when backend scaffold lands.
- [ ] Validate actual hosting PHP extensions (GD/EXIF) before media conversion deployment.
- [ ] Add scheduled off-host backup + tested restore procedure.

### Catalog/data
- [x] Expand frontend mock catalog from 6 to 18 products.
- [x] Ensure every demo product has a local stock-image slot.
- [x] Expand demo customers to 6.
- [x] Add SQLite demo seed with 18 products, media rows, customers, favorites, orders and timeline events.
- [x] Make SQLite bootstrap load schema + demo seed.
- [ ] Revalidate all six subcategory specs against source workbook before production import.
- [ ] Complete FA/AR/EN/KU-Sorani copy.

### UX
- [x] Hero uses local vendored image slots with graceful fallback.
- [x] Category cards use local vendored image slots.
- [x] Product cards use local vendored image slots.
- [x] Clearly label web-stock imagery as temporary/non-Armaghan.
- [x] Preserve full six-path WhatsApp/order wizard behavior.
- [x] Keep robust no-image fallback.
- [ ] Visual QA after stock vendor workflow commits binaries.
- [ ] Accessibility regression pass.
- [ ] Add Playwright browser smoke flows.

## P1 — Laravel backend
- [ ] Bootstrap Laravel 13 under `/platform/backend`.
- [ ] Keep SQLite as default.
- [ ] Convert schema/seed into Laravel migrations, seeders and factories.
- [ ] Install pinned/tested Spatie Media Library v11 patch line.
- [ ] Filament 5 admin resources and official Spatie plugin.
- [ ] Laravel auth + Socialite.
- [ ] Secure magic links with hash/revoke/expiry/scope/audit.
- [ ] Auditable impersonation.
- [ ] Host-local public/private media disks.
- [ ] Media conversions and responsive variants.
- [ ] Pest feature tests.

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
SQLite remains default until Laravel behavior is stable. Then provision MySQL through environment/secrets, migrate staging, import data, run full tests, verify `utf8mb4`, and cut over. No MySQL credentials are required now.

## Definition of Done — Test 13
1. Tests 01–12 remain untouched.
2. All automated QA gates pass.
3. Licensed web-stock pipeline either vendors every configured required slot or fails closed.
4. All runtime catalog photography is local/hosted, not hotlinked.
5. Attribution/provenance is visible and machine-recorded.
6. 18 demo products and richer SQLite data are present.
7. Missing photography still renders gracefully.
8. Six request paths, logout, impersonation and wizard remain regression-free.
9. Test 13 is newest in launcher.
10. FTP writes only Test 13 build/launcher and performs no remote deletion.
