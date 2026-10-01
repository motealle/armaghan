# Armaghan Trading B2B Catalog — Project Handoff

## 1. Project identity

- Repository: `motealle/armaghan`
- Current prototype/development scope: `/t`
- Root website (`/public_html`) is a protected landing page and must remain untouched by prototype work.
- Test 26 customer-facing UX is frozen pending customer feedback; do not mutate `/t/26`.
- The active phase is now Backend MVP productionization. Any later UI change, including customer feedback, must start in Test 27 or higher.

## 2. Deployment and hosting contract

- FTP secrets already configured in GitHub Actions:
  - `FTP_SERVER`
  - `FTP_USERNAME`
  - `FTP_PASSWORD`
- FTP smoke test has succeeded.
- Root landing deployment has succeeded.
- `/public_html/index.html` and `/public_html/logo.png` are the approved root landing assets.
- No remote delete/sync-delete is allowed.
- Prototype deployments must be scoped to `/public_html/t` only.
- Root `/public_html` must not be overwritten by prototype changes.
- All prototype links and asset references must be relative so `/t` can later be moved to the root without path rewrites.
- GitHub Actions is the CI/CD mechanism.

## 3. Business

Armaghan Trading is a B2B clothing production/trading catalog for wholesale/export-oriented customers, primarily serving Iran, Iraq, Kurdistan Iraq and Arab markets.

Main categories:
1. نوزادی / Baby
2. بچگانه / Kids
3. زنانه / Women

Phase one is not a conventional ecommerce checkout. There is no online payment, final checkout or fixed public price. Negotiation, price announcement and final deal happen through WhatsApp.

The website must:
- present products clearly;
- help customers select products and/or custom services;
- collect structured request details;
- generate a clean WhatsApp message with product codes, options, quantities and request type;
- feel fast, beautiful, simple and app-like, especially on mobile.

## 4. Prototype architecture

All prototype work lives under `/t`.

Required archive structure:
- `/t/index.htm` — launcher
- `/t/01/index.htm` — Test 01
- `/t/01/assets/app.css`
- `/t/01/assets/app.js`
- future tests: `/t/02/`, `/t/03/`, etc.

Never overwrite an earlier test. Each numbered test is an archive snapshot. New tests are added to the top of the launcher.

Shared files may live under `/t/shared/`, but each test should remain self-contained where practical.

## 5. Language and localization

The product is Persian-first and RTL-first.

Core labels include:
- خانه
- محصولات
- دسته‌بندی‌ها
- نوزادی
- بچگانه
- زنانه
- استعلام قیمت
- ارسال به واتساپ
- مشخصات ثابت
- قابل سفارشی‌سازی
- افزودن به لیست استعلام
- تولید سفارشی
- برند اختصاصی
- بسته‌بندی اختصاصی

Do not build full multilingual logic now, but design strings and structure so later translation is straightforward.

## 6. Visual direction

### Dark theme
The dark visual reference is the current Midjourney web experience: image/product-first presentation, deep dark surfaces, restrained chrome, strong visual hierarchy, compact controls, rounded surfaces and an editorial/creative feel. Midjourney's current web experience centers visual feeds, search/filter controls and fullscreen/detail interactions. citehttps://docs.midjourney.com/hc/en-us/articles/33329460426765-Website-Overview

### Light theme
The light visual reference is Digikala: Persian ecommerce information density, strong search/navigation, practical product cards, familiar RTL commerce patterns, clear CTAs and functional hierarchy. This is a taste reference, not a request to copy Digikala branding or proprietary UI.

### Armaghan brand layer
Use Armaghan's own identity above both references:
- primary navy: `#151DAB`
- landing/logo blue: `#0613BF`
- white: `#FFFFFF`
- gold: `#FFD80D`
- mint: `#C8E3DB`
- action green: `#21946A`
- light background: `#F7F8FC`
- dark background: `#090B18` or refined equivalent
- text: `#111827`
- muted: `#6B7280`
- danger: `#DC2626`

The result must be B2B/export-professional, not childish, even for baby/kids products.

## 7. Mobile UX

Mobile-first PWA-like shell:
- sticky bottom navigation;
- large touch targets;
- bottom sheets for product details and request forms;
- sticky primary CTAs;
- clear icons and labels;
- safe-area padding;
- low cognitive load.

Suggested bottom nav:
1. خانه
2. محصولات
3. استعلام
4. خدمات
5. واتساپ

On desktop, this may transform into a top nav/sidebar while retaining the same information architecture.

## 8. Product cards

Each product card should support:
- 1–3 images / visual switcher;
- product code;
- category;
- availability: موجود or ناموجود / قابل تولید;
- short summary;
- WhatsApp action;
- secondary heart/wishlist;
- details action.

Details open as a mobile bottom sheet and desktop modal/drawer.

Product details must separate:
- fixed specs: locked / non-changeable;
- variable specs: selectable/customizable.

Fixed examples:
- تعداد هر پک: ۱۲ عدد
- سایزبندی پک: ۰ تا ۱۲ ماه
- جنس پایه: نخ پنبه
- مدل پایه: استاندارد

Variable examples:
- رنگ موردنظر
- تعداد درخواستی
- نوع اجرا
- بسته‌بندی
- توضیحات تغییرات

Never communicate fixed/variable state by color alone; use icons, labels and disabled/badged states.

## 9. Six business request paths

1. خرید ساده محصول موجود — same product, quantity only.
2. خرید محصول موجود با تغییرات — product + selected changes + quantity + notes.
3. تولید از مدل ناموجود/قابل تولید — simple purchase disabled; production request enabled.
4. تولید سفارشی — product group, specs, quantity, image-upload placeholder, notes.
5. برند اختصاصی — Armaghan design + customer brand OR customer design + customer brand; label, hang tag, patch, print, embroidery, logo placeholder, notes.
6. بسته‌بندی اختصاصی — product group, packaging material, dimensions, color, print, brand/logo, quantity, notes.

## 10. Recommended product decision

Use guest-first, login-later.

A guest can browse, view details, add inquiry items, start any request path, review the request and send it to WhatsApp.

Login belongs later to proforma, official invoice, packing list, order timeline, payment status and reorder history.

Primary business concept is **لیست استعلام / سبد استعلام**, not merely Wishlist. Heart can remain a secondary shortcut.

## 11. Test 01 — Unified Request Builder

Recommended UX model: one central request builder collecting product and service requests.

Entry points:
- product WhatsApp button;
- product details;
- افزودن به استعلام;
- bottom-nav استعلام;
- خدمات: تولید سفارشی، برند اختصاصی، بسته‌بندی اختصاصی.

Lightweight flow:
1. نوع درخواست
2. context محصول/خدمت
3. quantity/options
4. review
5. WhatsApp

Request chips:
- خرید ساده
- خرید با تغییرات
- تولید از مدل ناموجود
- تولید سفارشی
- برند اختصاصی
- بسته‌بندی اختصاصی

Available product: enable خرید ساده + خرید با تغییرات.
Unavailable/producible product: disable خرید ساده; enable تولید از مدل ناموجود.
Service-originated requests do not need product context.

## 12. WhatsApp

Use a simple encoded WhatsApp URL with placeholder phone `989000000000`.

Before opening WhatsApp, show a message preview.

Message should contain:
- request type;
- product/service context;
- code/category/status where applicable;
- fixed specs;
- selected variable specs;
- quantity;
- notes;
- product link placeholder where applicable.

Do not attempt direct file attachment through the simple WhatsApp link.

## 13. Homepage prototype

Include:
1. header: logo, language visual, search, customer panel placeholder;
2. hero: B2B/export positioning + product/request CTAs;
3. category cards: نوزادی، بچگانه، زنانه;
4. trust/value: تولید عمده، کنترل کیفیت، آماده صادرات، اسناد و اعتبار تجاری;
5. product grid with available/unavailable examples;
6. services: تولید سفارشی، برند اختصاصی، بسته‌بندی اختصاصی;
7. inquiry summary/floating CTA;
8. simple footer.

## 14. Mock data

Use JavaScript mock data. At least 8 products:
- 3 baby
- 3 kids
- 2 women

Each product should have id, code, title, category, subcategory, status, images, fixedSpecs, variableSpecs, packQuantity, minOrder and summary.

Use visual/gradient placeholders rather than real product photography.

## 15. Prototype roadmap

01 — Unified Request Builder — Recommended
02 — Product Card Bottom Sheet Focus
03 — RFQ Cart / سبد استعلام
04 — Services Hub
05 — Customer Profile Model (comparison only)
06 — WhatsApp-first Quick Actions
07 — Classic Catalog

## 16. Acceptance criteria for Test 01

- launcher exists and links to Test 01;
- Test 01 exists and is polished/customer-presentable;
- mobile-first responsive;
- RTL Persian UI;
- relative paths only;
- homepage sections present;
- product cards with 1–3 image switcher;
- detail bottom sheet/modal;
- fixed specs with lock icon;
- editable variable specs;
- available/unavailable behavior;
- mobile bottom navigation;
- services entry points;
- unified request builder;
- inquiry list/summary;
- WhatsApp preview and encoded link;
- no backend/database/auth/API;
- no heavy framework/build dependency;
- no absolute internal asset paths.

## 17. Future Laravel mapping (not implemented now)

Expected future stack:
- Laravel
- Filament
- Blade / Livewire / Alpine
- Tailwind
- MySQL/MariaDB
- object storage for images/PDFs
- GitHub Actions
- PWA-like frontend

Future entities include categories, products, product_images, product_specs, inquiry_requests, inquiry_items, customers, proforma_invoices, official_invoices, packing_lists and order_timelines.

## 18. Explicit prohibitions

Do not build backend, database, real login, real APIs, payment, checkout, PrestaShop, WordPress or a framework-heavy prototype. Do not store real customer files. Do not use real product images. Do not hide the main action behind login. Do not use absolute internal paths. Do not put paths 4–6 only inside profile in the recommended version.

## 19. Customer testing questions

1. آیا مسیر انتخاب محصول و ارسال به واتساپ واضح است؟
2. آیا مشخصات ثابت و موارد قابل سفارشی‌سازی قابل فهم‌اند؟
3. آیا «لیست استعلام» بهتر از «علاقه‌مندی» است؟
4. آیا خدمات سفارشی بهتر است عمومی شروع شود یا داخل پنل؟
5. آیا Bottom Navigation حس اپلیکیشنی و راحتی می‌دهد؟
6. آیا WhatsApp preview مفید است؟
7. آیا مشتری خارجی بدون لاگین می‌تواند درخواست بفرستد؟
8. آیا مسیرها زیاد و گیج‌کننده‌اند؟
9. آیا ظاهر به اندازه کافی صادراتی، تمیز و حرفه‌ای است؟
10. کدام تست به نسخه نهایی نزدیک‌تر است؟

## 20. Current status

- FTP smoke test: PASS.
- Root landing deployment: PASS.
- `logo.png` is present in repository.
- `/t` remains the active product-design/prototype workspace.
- Tests 01–26 are released snapshots and must remain immutable.
- Test 26 is the frozen customer-review snapshot; snapshot branch: `snapshot/test26-final`.
- Any further UI work starts at Test 27 or higher. Run 2 customer-approved integration is delivered: per-viewport Appearance controls now drive the live Header/Home/Products/Footer; Home uses single Hero + About + Why + Capabilities overlay + product-category banners by default; Favorites lists are anonymously shareable.
- Test 26 rollback checkpoint: `rollback/test25-pre-test26` at `328f6c48ea9c4f1346702282ddb6d21ae1685a16`.
- Test 26 ranked UX/architecture decisions: `docs/TEST26-UX-AUDIT.md`.
- Test 26 image handoff contract: `docs/TEST26-IMAGE-REQUIREMENTS.md`.
- Test 26 customer clarification script: `docs/TEST26-CUSTOMER-QUESTIONS.md`.
- Test 26 foundation delivery: frontend build commit `1b92b0f69a3e2ab68a9e8ce21b93eb3e735f2a74`; **FTP Deploy Run #131 PASS**; `deploy-root` skipped; Test 25 unchanged.
- Test 26 Run 2 rollback: `rollback/test26-foundation-pre-integration` → `152e20e5ec062f48319a45727c7448d4ee68843e`.
- Test 26 customer integration build: `fafcb0d31e248e3c12ca4533ee85708f5ab08502`; **FTP Deploy Run #185 PASS**; full frontend build/type-check/unit tests/contracts passed; `deploy-root` skipped.
- Mutable `/t` launcher completion: `57d47e3919a9b87a3fb540723c832ca64fd23d4a`; **FTP Deploy Run #186 PASS**.
- Remaining Test 26 asset work is explicit rather than hidden: final capability images, final three banner originals/generated assets, and the optimized local derivative of the customer-approved About handshake image are tracked in `docs/TEST26-IMAGE-REQUIREMENTS.md`.
- Mobile PWA navigation invariant for Test 26+: the original app-like five-item BottomNav is mobile-only (<48rem/768px). From tablet upward, those primary routes live in the expanded blue top navigation. This placement is enforced at runtime and must not become an arbitrary mode that can duplicate/remove primary navigation.
- SmartImage AVIF invariant: never derive an `.avif` URL merely because a `.webp` exists. Advertise AVIF only for asset families known to ship a real AVIF sibling; Test 26 selected media are WebP-only.
- Mobile PWA BottomNav regression fix deployed from `c49a37c0aa0e56759e4a3418c8c798232f4e64b5`; **FTP Deploy Run #195 PASS**; `deploy-root` skipped; Test 25 and older snapshots unchanged.
- Live Test 26 P0 screenshot fix deployed from `596c86bf1009741a95d1c76ec168bc5c4519104e`; **FTP Deploy Run #208 PASS**. This fixes WebP→nonexistent-AVIF fallback, enforces mobile BottomNav vs tablet/desktop top-nav state, cache-busts the Test 26 launcher, skips root deployment, and leaves Test 25+ older snapshots untouched.
- Test 26 Run 7 mobile UX polish deployed from `633e7e1399db1e6cca84256bcfdd516004088476`; **FTP Deploy Run #223 PASS**. Header mode/hamburger are reversible again; mobile default hamburger is off; BottomNav is mobile-only with responsive-emulation fallback; mobile footer is edge-to-edge; product title/code are centered; pale active subcategory chips use readable text. Root deploy skipped; Test 25 and older snapshots unchanged.
- Test 26 Run 8 behavior: on mobile the Footer is Home-only; tablet/desktop keep the existing Footer scope. Mobile compact header exposes language selection directly in the blue top bar and does not duplicate it in the drawer.
- Permanent technical-term explanation rule: in user-facing Armaghan explanations, define each specialist term on first use with a short Persian explanation in parentheses; canonical rules are `docs/PROJECT-RULES.md` 75–77.
- Test 26 Run 8 deployed from `5a7ca47291b64bd70cf1fed2b4a32f94e9753798`; **FTP Deploy Run #234 PASS**. Mobile Footer is Home-only, mobile top bar exposes language selection, technical terms must be explained in Persian parentheses on first use, `deploy-root` skipped, Test 25 and older snapshots unchanged.
- Operational project memory hierarchy: `docs/PROJECT-RULES.md` → `docs/HANDOFF.md` → `docs/BACKLOG.md` → current test audit / asset docs. Chat memory is secondary.


## Test 26 current image state

Owner-selected generated images are wired into Test 26: Hero 1-2, About 2-2, Production 3-1, Export 4-3, Trade Documents 5-2, Baby banner 6-3 and Kids banner 7-3. They remain at native 800×450 / 800×300 resolution and are described as conceptual, not verified Armaghan photography. The previous hero file and Test 25 snapshot are preserved. The final Women-banner candidate was automatically selected as option 8-2, a 800×300 scene of loose fully covered outfits on headless mannequins; the slot now uses a local WebP and no placeholder.

Deployment record: selected Test 26 media commit `e2d9931f9c5398de9320ef698051d0f9c56db991` passed FTP Deploy Run #188. QA, FTP smoke and `deploy-t` passed; `deploy-root` was skipped. Test 25 and earlier snapshots were untouched.

Run 4 deployment record: women banner choice 8-2 is live in Test 26. Commit `a35dfacde3140618899b3784db7275edea85dba2`; FTP Deploy Run #190 passed QA, FTP smoke and scoped `deploy-t`; `deploy-root` was skipped. All eight Test 26 image slots now have local selected media; Test 25 and earlier snapshots remain unchanged.


## 21. Backend MVP transition

- Laravel is now installed under `platform/backend`; bootstrap resolved Laravel Framework **13.34.0**.
- Filament Panel Builder is installed at **5.9.0** and `app/Providers/Filament/AdminPanelProvider.php` exists.
- `composer.json` and `composer.lock` are committed; `.env` and `vendor/` remain ignored.
- Permanent Backend CI validates Composer metadata, local SQLite migrations, framework/admin major versions, tests, security audit and secret hygiene.
- Local/dev/test uses SQLite. Verified production hosting lacks PDO SQLite but has PDO MySQL, so production uses MySQL/MariaDB.
- Core domain models now exist for Customer, Category, Subcategory, Product, specification definitions/values, FavoriteShare, MagicLink and ActivityLog. Filament CRUD Resources/admin-core are still a separate unmerged backend batch; current Vue customer-facing catalog/customer data remains browser-local until API wiring.
- Current Favorites sharing is still a versioned product-code URL implemented entirely in the frontend until the persisted share flow is built.
- Current color system already has five-color palettes and semantic CSS variables, but the mapping is fixed in `stores/design.ts`; Backend MVP should persist a safe semantic role mapping instead of exposing arbitrary CSS.
- Canonical backend plan: `docs/BACKEND-MVP.md`; bootstrap analysis/result: `docs/BACKEND-BOOTSTRAP.md`.

## 22. Frozen Test 26 / open Test 27+ CI handoff

- Test 26 is registered as immutable in `docs/IMMUTABLE-TESTS.txt`.
- Main must never build or deploy `/t/26`; the next CI UI release lane is Test 27.
- Vite local builds default to `.build/frontend`; numbered output requires an explicit unfrozen `ARMAGHAN_UI_TARGET`.
- Vite-config-only maintenance does not auto-publish a numbered UI test.
- Test 22 and Test 26 CI contracts are historical/frozen release contracts rather than assertions about the current evolving frontend source.
- The mutable launcher remains on Test 26 until an actual Test 27 customer-facing change is intentionally added.
- Backend MVP and UI changes can proceed in parallel; customer UI corrections go to Test 27+.
- Communication terminology rules 75–77 apply across chat replies, reports, handoffs, automation summaries and review notes.
- Repair validation: GitHub Actions **FTP Deploy Run #244 PASS**; QA and FTP smoke passed, while both deploy jobs were skipped. Test 26 stayed untouched and no Test 27 release was published.


## 23. Hosting preflight result

- Hosting Preflight Run #1 executed a short-lived PHP probe and removed every temporary file/directory successfully.
- PHP 8.3.33 on LiteSpeed: PASS; all Laravel 13 required PHP extensions: PASS; HTTPS: PASS.
- FTP account root is the parent of `public_html`; PHP can read/write a verified private sibling outside the public web root even with `open_basedir` enabled.
- PDO drivers expose `mysql` but not `sqlite`. Native SQLite3 is loaded, but Laravel production SQLite is not viable without PDO SQLite.
- Architecture decision: SQLite remains local/dev/test; MySQL/MariaDB becomes the P0 production database.
- Production DB server version/credentials still need provisioning/verification before the first production migration.
- Web PHP disables `proc_open`, `exec`, and `shell_exec`; deployment must build Composer dependencies in CI rather than on the host.
- PHP `symlink()` is available. Limits are 256M upload, 256M POST, 512M memory, 300s execution.
- The preflight touched no numbered UI snapshot; Test 26 remains frozen and Test 27 remains unpublished.


## 24. Laravel / Filament bootstrap handoff

- Backend Bootstrap Run #1: PASS.
- Laravel Framework 13.34.0 and Filament 5.9.0 were resolved by Composer and locked.
- Bootstrap tests: 2 passed / 2 assertions; Composer audit found no known vulnerability advisories.
- Backend root is `platform/backend`; Test 26 and Test 27 were not touched.
- The one-shot bootstrap workflow was removed after use; permanent backend validation lives in `.github/workflows/backend-ci.yml`.
- Generated Laravel agent guidance was overridden by Armaghan-specific `platform/backend/AGENTS.md` and `CLAUDE.md`; no automatic Laravel Boost installation is allowed.
- Next safe batch is domain migrations/models plus production-safe Filament admin access foundation. Production MySQL/MariaDB provisioning can remain deferred until the first production migration and does not block repository development.


## 25. Backend domain foundation handoff

- Backend domain foundation validated in Pull Request #1; Backend CI Run #3 PASS.
- Test suite after this batch: 4 passed / 23 assertions; locked Composer audit clean.
- Users now have explicit `role` and `active` fields. Filament production access requires `role=admin` and `active=true`.
- No default admin/test credential is seeded by `DatabaseSeeder`.
- Core persistent models now exist for Customer, Category, Subcategory, Product, specification definitions/values, FavoriteShare, MagicLink and ActivityLog.
- Favorites share and magic-link token columns store hashes, not raw public tokens.
- Product media is intentionally deferred to Spatie Media Library per project rules; do not add a competing manual product-images table.
- Orders/order timeline remain deferred unless MVP delivery requires them.
- No production MySQL/MariaDB migration has been executed yet.
- Next P0 batch: Filament CRUD Resources for catalog/customers plus secret-driven first-admin provisioning that requires no manual user SQL/cPanel work.


## 26. Test 27 visual editor foundation handoff

- Test 26 remains immutable; rollback checkpoint before Test 27 UI work: `rollback/test26-pre-test27-visual-editor`.
- Test 27 visual editor architecture is documented in `docs/TEST27-VISUAL-EDITOR.md`.
- Approved palette is exactly: green `#21946A`, blue `#151EDA`, mint `#C8E3DB`, white `#FFFFFF`, gold `#FFB514`.
- Header, Footer and Hero brand-chrome surfaces share the semantic `--role-brand-chrome` token instead of unrelated hard-coded navies.
- Admin-only visual editor uses a non-modal, resizable mobile bottom sheet. The page remains visible/selectable above it.
- Dense touch selection samples the rendered touch neighborhood through `elementsFromPoint()`; multiple candidates are presented explicitly rather than guessed.
- Saved style profile is independent of editor visibility: editor OFF removes the editor UI/listeners but keeps the saved style applied.
- Text overrides are locale-specific and only allowed on explicitly registered text targets; arbitrary CSS/HTML/JS input is not accepted.
- Test 27 browser state is isolated under `armaghan:test27:*`; it no longer writes Test 26 mutable-storage keys.
- Editor internals are modular: shell, target chooser, inspector, selection composable, resizable-sheet composable, contrast utility, persistence store and runtime applier.
- Explicit token text/background pairs enforce a 4.5:1 contrast floor; unsafe choices are disabled.
- Editable target coverage now includes Header, Hero, About, Why, Capabilities, product banners, product cards, Footer and main Home surfaces.
- Latest branch-only validation: source contract PASS, TypeScript PASS, 25/25 Vue unit tests PASS, Vite Test 27 build PASS; no deployment was performed by that temporary validation workflow.
- Test 27 must not be promoted in the mutable launcher until owner/customer review.

- Test 27 visual-editor modularization/contrast batch landed on main at `d9a6a68353345a34b0379feca77663a240e1c856`; **FTP Deploy Run #254 PASS**. Full QA/build/smoke passed; `deploy-t` published staging `/public_html/t/27`; `deploy-root` skipped; no remote files deleted. Mutable launcher content still points to Test 26 and contains no Test 27 entry.
