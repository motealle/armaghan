# Test 26 — Reversible Homepage / Navigation / Device Overrides UX Audit

Status: **planning approved; implementation not started**  
Baseline: **Test 25 delivered and frozen**  
Rollback checkpoint: `rollback/test25-pre-test26` → `328f6c48ea9c4f1346702282ddb6d21ae1685a16`

## 1. Why Test 26 exists

Customer feedback changes the homepage from a catalog-heavy landing page toward a brand / capability / export-readiness landing page. At the same time, some rejected or hidden UI from Test 25 may be useful later, so Test 26 must preserve reasonable alternatives rather than destructively deleting them.

The project owner explicitly requires:
- customer-requested behavior is the default;
- reasonable alternatives remain recoverable;
- settings that materially differ by viewport may be managed separately for mobile / tablet / desktop;
- each major section is modular and independently reversible where that is rational;
- repository documentation is the operational project memory;
- unresolved image slots use temporary `placehold.co` fallbacks and are tracked in a machine-readable markdown requirement list.

## 2. Repository / framework constraints already in force

- Tests 01–25 are immutable snapshots.
- Vue 3 + TypeScript + Vite + Vue Router + Pinia + Tailwind.
- Mobile-first, Persian-first/RTL-first, English LTR.
- `SmartImage.vue` owns loading/error/fallback behavior for content photography.
- Components should stay feature-first and cross-screen settings belong in a store, not ad-hoc component state.
- No destructive FTP sync/delete.
- Root site stays protected; prototype work stays under `/t`.

External implementation references:
- Tailwind responsive behavior is mobile-first and supports explicit breakpoint ranges: https://tailwindcss.com/docs/responsive-design
- MDN recommends viewport breakpoints based on layout needs rather than specific named devices: https://developer.mozilla.org/en-US/docs/Learn_web_development/Core/CSS_layout/Responsive_Design
- Pinia stores are appropriate for typed cross-screen state and derived getters: https://pinia.vuejs.org/core-concepts/
- Placehold supports explicit dimensions, formats and text labels: https://placehold.co/

## 3. Certainty map — what is confirmed vs ambiguous

### Confirmed enough to implement after the planning gate
- Home must not show the product grid by default.
- Home should link into Products rather than duplicate the catalog.
- Hero default is one main hero, not a three-slide carousel.
- Category number labels such as 01/02/03 should be hidden by default.
- Customer-provided About Armaghan copy should be used.
- Customer-provided Why Armaghan four-value content should be used.
- Three capability cards are required with short content + image + entry to more detail.
- Three product categories should appear as horizontal visual banners on Home.
- Footer should remain, but content must be simplified from the supplied reference.
- Desktop header should expose important actions more directly.
- Existing mobile navigation should **not** be assumed rejected until customer confirms.
- Unresolved image slots should use explicit placeholders and be tracked.

### Ambiguous; do not hard-code one irreversible interpretation
- Whether mobile/tablet hamburger should disappear.
- Exact desktop header item set and ordering.
- Whether capability details open in modal/sheet vs dedicated page.
- Whether Why Armaghan is four cards or a simple list.
- Hero CTA presence and label.
- Exact final footer columns/items.
- Exact visual treatment of the pale-green Products intro area.
- Favorites-share metadata beyond product membership.

## 4. Ranked implementation choices

### 4.1 Reversibility architecture

| Rank | Option | Score | Why / risk |
|---:|---|---:|---|
| 1 | Typed appearance policy + Pinia persistence + customer-default presets + per-device overrides | **9.9** | Centralized, testable, reversible, aligns with existing Pinia architecture and admin prototype |
| 2 | Route-level query flags for alternate layouts | 7.4 | Good for demos, poor as durable admin configuration |
| 3 | CSS classes only | 6.9 | Useful for presentation but weak for semantic choices like single vs carousel hero |
| 4 | Duplicate “customer” and “owner” component trees | 4.8 | High drift and regression risk |
| 5 | Delete old UI and rely on Git history | 3.0 | Recoverable technically, but expensive and unsuitable for day-to-day admin switching |

**Selected:** Option 1.

### 4.2 Device-specific settings model

| Rank | Option | Score | Why / risk |
|---:|---|---:|---|
| 1 | Three viewport profiles aligned with existing breakpoints: mobile <48rem, tablet 48–<64rem, desktop >=64rem | **9.7** | Fits current Tailwind `md`/`lg` usage, minimal regression risk, easy admin mental model |
| 2 | Five Tailwind breakpoint profiles | 7.6 | Precise but too many controls for nontechnical admin users |
| 3 | Device user-agent detection | 4.5 | Brittle; tablets/laptops and resized windows behave inconsistently |
| 4 | Per-component arbitrary pixel thresholds | 4.0 | Hard to reason about and maintain |
| 5 | One global layout setting | 2.7 | Does not meet the owner requirement |

**Selected:** Option 1.

The labels “mobile/tablet/desktop” are admin-friendly names for **viewport bands**, not user-agent device detection.

### 4.3 Homepage composition

| Rank | Option | Score | Why / risk |
|---:|---|---:|---|
| 1 | Section registry + independent section components + visibility flags | **9.8** | Modular, reversible, supports safe reordering and later CMS mapping |
| 2 | Keep all sections inline in `HomeView.vue` with conditionals | 6.3 | Fast initially, but HomeView will become a maintenance hotspot |
| 3 | Full schema-driven page builder now | 5.9 | Too much product complexity for current prototype |
| 4 | Separate route per home variant | 4.7 | Duplicates layout code |
| 5 | Static one-off landing page | 3.4 | Conflicts with the owner’s reversibility requirement |

**Selected:** Option 1.

### 4.4 Header strategy

| Rank | Option | Score | Why / risk |
|---:|---|---:|---|
| 1 | Preserve existing mobile drawer; make expanded/compact header policy configurable by viewport; default desktop expanded | **9.8** | Matches confirmed customer intent without assuming mobile intent; preserves tested drawer accessibility |
| 2 | Remove hamburger on every viewport | 6.0 | Could satisfy literal wording but high mobile regression risk |
| 3 | Keep Test 25 header unchanged | 5.2 | Does not address customer desktop feedback |
| 4 | Build a second header from scratch | 4.6 | Duplicates behavior and accessibility logic |
| 5 | Use CSS-only hiding without policy state | 4.2 | Hard to manage from Admin |

**Selected:** Option 1.

### 4.5 Missing-image / placeholder strategy

| Rank | Option | Score | Why / risk |
|---:|---|---:|---|
| 1 | Local final path → SmartImage error handling → documented `placehold.co` placeholder in prototype only | **9.5** | Explicit, visible missing-asset state; does not falsely imply real Armaghan photography |
| 2 | Commit generic local placeholder files | 8.1 | More reliable offline but does not satisfy the requested placeholder service workflow |
| 3 | Hotlink temporary stock photos | 4.8 | Licensing/provenance and false-representation risk |
| 4 | Broken-image glyph | 1.7 | Poor UX and violates SmartImage policy |
| 5 | Hide missing image area | 1.0 | Conceals missing assets and creates layout instability |

**Selected:** Option 1, only for unresolved Test 26 marketing-image slots. Product/catalog photography still follows the local-media rule.

### 4.6 Capability-detail presentation

| Rank | Option | Score | Why / risk |
|---:|---|---:|---|
| 1 | Adaptive detail overlay: bottom sheet on mobile/tablet, centered modal or side panel on desktop, deep-linkable later | **9.2** | Reuses existing interaction language and avoids unnecessary new routes |
| 2 | Dedicated page per capability | 8.1 | Better for SEO/content depth later, but heavier for current prototype |
| 3 | Accordion inside Home | 6.4 | Keeps context but makes landing page long |
| 4 | Tooltip/popover | 2.8 | Not suitable for long content |
| 5 | Put full content directly on each card | 2.1 | Overloads Home |

**Selected for prototype:** Option 1. Keep content data route-ready so dedicated pages remain possible later.

### 4.7 Favorites sharing

| Rank | Option | Score | Why / risk |
|---:|---|---:|---|
| 1 | Test 26 prototype: versioned share URL containing only compact product identifiers; production later uses server token | **9.0** | Works without backend, carries no personal data, demonstrates customer flow |
| 2 | Wait entirely for Laravel token backend | 8.3 | Architecturally ideal for production but leaves requested prototype untestable |
| 3 | Copy product links one by one | 4.2 | Does not satisfy “share the list” |
| 4 | Serialize full product JSON in URL | 2.8 | Bloated, brittle, leaks redundant data |
| 5 | localStorage-only share | 1.2 | Cannot be opened by another person/device |

**Selected:** Option 1 for prototype, with a strict payload length guard and no customer identity. Production replacement is a signed/opaque backend token.

## 5. Selected Test 26 customer-default policy

### Viewport profiles

| Setting | Mobile (<48rem) | Tablet (48–<64rem) | Desktop (>=64rem) |
|---|---|---|---|
| Header mode | compact drawer | compact drawer | expanded |
| Hamburger | on | on | off |
| Brand text beside logo | off | off | off |
| Language control | visible in drawer | visible in drawer | visible in header |
| Help entry | visible in drawer | visible in drawer | visible in header |
| Customer/login entry | visible in drawer | visible in drawer | visible in header |
| Home product grid | off | off | off |
| Hero mode | single | single | single |
| Category number labels | off | off | off |
| About section | on | on | on |
| Why Armaghan | on | on | on |
| Capabilities | on | on | on |
| Product category banners | on | on | on |
| Footer | on | on | on |

Alternate values are preserved in the appearance policy where they are useful:
- `headerMode: 'compact-drawer' | 'expanded'`
- `heroMode: 'single' | 'carousel'`
- `homeProductGrid: 'hidden' | 'recommended-6'`
- `categoryNumbers: boolean`
- per-section visibility booleans
- header action visibility booleans

Do **not** expose micro-style values (12px vs 16px spacing, minor radii, exact neutral shades) as Admin toggles.

## 6. Planned typed configuration shape

Suggested file: `src/types/appearance.ts`

```ts
export type ViewportProfile = 'mobile' | 'tablet' | 'desktop'
export type HeaderMode = 'compact-drawer' | 'expanded'
export type HeroMode = 'single' | 'carousel'
export type HomeProductGridMode = 'hidden' | 'recommended-6'

export interface ViewportAppearance {
  headerMode: HeaderMode
  showHamburger: boolean
  showBrandText: boolean
  showLanguage: boolean
  showHelp: boolean
  showAccount: boolean
  heroMode: HeroMode
  homeProductGrid: HomeProductGridMode
  showCategoryNumbers: boolean
  showAbout: boolean
  showWhy: boolean
  showCapabilities: boolean
  showProductBanners: boolean
  showFooter: boolean
}
```

A Test 26 store should:
- persist under `armaghan:test26:appearance`;
- have an immutable customer-default preset;
- expose `resetProfile(profile)` and `resetAll()`;
- validate/migrate loaded values instead of trusting arbitrary localStorage JSON;
- return a resolved policy for the active viewport.

## 7. File-by-file implementation plan

### New files
- `platform/frontend/src/types/appearance.ts`  
  Typed policy schema and allowed modes.

- `platform/frontend/src/stores/appearance.ts`  
  Test 26 appearance state, validation, persistence, customer defaults, reset actions.

- `platform/frontend/src/services/viewportProfile.ts`  
  One source of truth for viewport band resolution, using the same 48rem/64rem boundaries as layout CSS.

- `platform/frontend/src/features/admin/components/AppearanceSettings.vue`  
  Admin UI with Mobile / Tablet / Desktop tabs, grouped switches/selects, “Reset this device” and “Reset all to customer defaults”.

- `platform/frontend/src/features/home/components/HeroSection.vue`  
  Wrapper selecting single hero or preserved `HeroCarousel.vue`; do not delete the carousel.

- `platform/frontend/src/features/home/components/AboutArmaghanSection.vue`
- `platform/frontend/src/features/home/components/WhyArmaghanSection.vue`
- `platform/frontend/src/features/home/components/CapabilitiesSection.vue`
- `platform/frontend/src/features/home/components/CapabilityDetail.vue`
- `platform/frontend/src/features/home/components/ProductCategoryBanners.vue`

- `platform/frontend/src/features/favorites/shareFavorites.ts`  
  Versioned prototype URL encoder/decoder with product-id/code validation and no PII.

### Existing files to modify
- `platform/frontend/src/features/admin/components/AdminDashboard.vue`  
  Add an “Appearance” tab. Keep “Content” for copy editing; do not mix copy and responsive layout settings.

- `platform/frontend/src/views/HomeView.vue`  
  Become composition-only. Default order:
  1. Hero
  2. About Armaghan
  3. Why Armaghan
  4. Capabilities
  5. Product category banners
  Product grid remains available only behind the appearance flag and defaults off.

- `platform/frontend/src/features/home/components/HeroCarousel.vue`  
  Preserve as the alternate mode. Do not delete swipe behavior.

- `platform/frontend/src/components/layout/AppHeader.vue`  
  Consume resolved appearance policy. Preserve tested mobile drawer code. Remove adjacent brand/manufacturer text by default, not by deleting it.

- `platform/frontend/src/components/layout/MobileMenuDrawer.vue`  
  Only consume visibility policy where appropriate; preserve focus trap, Escape, backdrop close and reduced-motion behavior.

- `platform/frontend/src/views/ProductsView.vue`  
  Hide category numbers by default through policy; add the pale, low-contrast intro/banner surface as a semantic section rather than one-off inline styling.

- `platform/frontend/src/views/FavoritesView.vue`  
  Add “Share list” action after URL semantics are confirmed.

- `platform/frontend/src/stores/favorites.ts`  
  No personal metadata in share payload. Existing local favorites state remains unchanged.

- `platform/frontend/src/i18n/messages.ts`  
  Add all new user/admin labels for fa/ar/en/ku. No hardcoded visible copy in components.

- `platform/frontend/src/styles/main.css`  
  Add section/layout styles only after component split; retain mobile-first rules and avoid per-device duplicated styles when responsive CSS is sufficient.

- Test 26 build / immutable snapshot / deploy contract files  
  Add only when implementation begins; Test 25 stays untouched.

## 8. Content accepted for Test 26

### About Armaghan — Persian source
ارمغان مجموعه‌ای تخصصی در تولید و تأمین عمده پوشاک برای بازارهای صادراتی است. ما از انتخاب و توسعه محصول تا تولید، کنترل کیفیت، آماده‌سازی صادراتی و تحویل، یک مسیر منظم و قابل پیگیری ایجاد کرده‌ایم تا عمده‌فروشان و شرکای تجاری بتوانند با اطمینان بیشتری خرید کنند و همکاری خود را در سفارش‌های بعدی توسعه دهند.

### Why Armaghan
1. ظرفیت بالا، کیفیت پایدار  
   تولید در حجم بالا با کنترل کیفیت و مشخصات ثبت‌شده، برای حفظ ثبات محصول در سفارش‌ها.
2. تولید سفارشی  
   امکان تولید OEM، ODM و Private Label متناسب با نیاز بازار و مشخصات موردنظر مشتری.
3. قیمت مناسب، بدون واسطه  
   تأمین مستقیم از تولید، بدون واسطه؛ برای ارائه قیمت مناسب و رقابتی در خرید عمده.
4. متناسب با بازار، آماده فروش  
   انتخاب محصول متناسب با بازار هدف، همراه با کدگذاری و بسته‌بندی منظم برای عرضه و انبارداری آسان‌تر.

### Capability cards
1. کارخانه، توان تولید و سیستم کنترل کیفیت
2. آماده‌سازی محصول برای صادرات و بازارهای جهانی
3. زیرساخت، اسناد و اعتبار تجاری

Full supplied detail content should be stored as structured translation/content keys, not inline prose in the component.

## 9. Regression traps to avoid

- Do not delete `HeroCarousel.vue`; single hero is only the default.
- Do not delete `MobileMenuDrawer.vue`; desktop expanded header does not prove mobile hamburger rejection.
- Do not duplicate device detection in components.
- Do not use JavaScript viewport checks for purely visual layout that CSS can handle.
- Do not persist “device type” based on user-agent.
- Do not let admin settings create impossible states such as hidden hamburger + compact header + no alternate navigation entry.
- Do not show a remote placeholder as an actual Armaghan factory/certificate/product.
- Do not put final image URLs directly into many components; use one asset registry/content model.
- Do not let Test 26 storage keys overwrite Test 25.
- Do not alter `/t/25`.
- Do not add a backend only to support prototype Favorites sharing.

## 10. Safe implementation batches — each intended to stay <=25 minutes

### Batch 26A — configuration foundation
- typed appearance policy;
- viewport profile service;
- appearance Pinia store;
- customer-default preset;
- persistence validation;
- unit tests for resolution/reset/migration;
- no visible page redesign yet.

### Batch 26B — Admin appearance controls
- add Appearance tab;
- Mobile / Tablet / Desktop tabs;
- only high-value settings;
- reset controls;
- accessibility labels.

### Batch 26C — Home modularization
- split sections;
- single hero default while preserving carousel;
- default-hide ProductGrid;
- About / Why / Capabilities / Product banners skeleton with placeholder assets;
- no final visual polish until customer answers return.

### Batch 26D — Header and Products confirmed-safe changes
- desktop expanded header policy;
- preserve mobile drawer default;
- hide category numbers;
- pale Products intro surface;
- spacing polish.

### Batch 26E — Favorites share prototype
- implement only after confirming what metadata the customer expects;
- anonymous product-membership payload by default;
- shared-list read-only state;
- production-token migration note.

### Batch 26F — final content/assets/QA
- replace placeholders with approved local assets;
- ensure no unresolved image requirement remains untracked;
- RTL/LTR/mobile/tablet/desktop regression;
- source contract;
- build + immutable guard + scoped deploy.

## 11. Planning completion criteria

- [x] Test 25 rollback branch created.
- [x] Customer intent separated into confirmed vs ambiguous.
- [x] Five-option analysis recorded for high-risk architecture decisions.
- [x] Device default policy defined.
- [x] File-by-file plan defined.
- [x] Safe <=25-minute batches defined.
- [x] Image-requirements document created separately.
- [x] Customer-question document created separately.
- [ ] No Test 26 implementation code until Batch 26A begins.
