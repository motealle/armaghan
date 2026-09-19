# Test 11 — Execution Plan

## Goal
Turn the strongest parts of Test 10 into a product-like vertical slice while fixing its rendering/icon regressions and preparing the repository for a Laravel/Filament backend.

## Iteration 11.0 — Foundation (this increment)
- Freeze Tests 01–10 in CI.
- Add automated QA gate before deployment.
- Create image-generation prompt pack.
- Add Test 11 Tailwind shell.
- Fix icon architecture by using Lucide and explicit icon containers.
- Add shimmer/skeleton loading.
- Add 3-slide hero with image slots + polished fallbacks.
- Add 5 design systems + 5 color sets, opened by 3-second top-bar long press.
- Add demo role/login shell: admin 1/1, customer 2/2, logout.
- Add admin/customer product-like dashboard slices.
- Add browser-demo product CRUD and client-side image compression.
- Add SQLite schema/bootstrap/test foundation under `/platform`.

## Iteration 11.1 — Real photography integration
Owner generates images from `docs/pics-Prompt.md` and returns them. Then:
1. Inspect image quality and visual consistency.
2. Crop to agreed ratios.
3. Resize responsive variants.
4. Convert to WebP and, if supported by hosting/browser policy, AVIF.
5. Add deterministic filenames under `/t/11/assets/images`.
6. Replace fallbacks without layout changes.
7. Run visual QA on narrow mobile, tablet and desktop.

## Iteration 11.2 — Catalog completeness
- Normalize all six subcategories and spec definitions from the source workbook.
- Confirm every lock marker with owner/customer.
- Add realistic mock product records for each subcategory.
- Finish FA/AR/EN/KU translations.
- Validate all six WhatsApp request paths.

## Iteration 11.3 — Laravel bootstrap
- Bootstrap Laravel under `/platform`.
- Start SQLite by default.
- Turn `database/schema.sql` into migrations.
- Seed demo admin/customer records.
- Add Pest tests.
- Add Inertia/Vue customer shell.
- Add Filament admin resources.
- Implement real auth, secure magic links, impersonation audit and media pipeline.

## Iteration 11.4 — MySQL staging
Only after Laravel feature behavior is stable:
- provision MySQL;
- add server-side env secrets;
- migrate staging;
- run all tests;
- import data;
- verify and cut over.

## Test-first gates
Before each deploy:
- frozen-test mutation check;
- Test 11 static contract test;
- SQLite schema smoke test;
- then FTP scope/auth smoke test;
- then deploy only `/t`.

## Image contract
Until final images arrive, every image surface must have:
- fixed aspect ratio;
- shimmer while loading;
- graceful fallback;
- no broken-image icon;
- no content/layout shift.

## Security boundary
The Test 11 browser demo deliberately simulates auth/admin operations. It is not a security model. Production account access, magic links, password reset, impersonation and customer data move to Laravel before any real customer data is used.
