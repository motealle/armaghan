# Armaghan Trading B2B Catalog — Project Handoff

## 1. Project identity

- Repository: `motealle/armaghan`
- Current prototype/development scope: `/t`
- Root website (`/public_html`) is a protected landing page and must remain untouched by prototype work.
- This phase is UX/UI taste testing and customer approval only.
- Production backend will later be Laravel; do not build Laravel/backend/database/auth/API in this phase.

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
- `/t` is now the active product-design/prototype workspace.
- Test 01 is the first implementation target.
