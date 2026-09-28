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
- [x] No Test 26 implementation code was started before Batch 26A began.


## 12. Run 1 batching decision — while customer answers are pending

The owner asked to make as much safe progress as possible, preferably in one run, but without creating regret while clarification answers are still pending.

| Rank | Delivery strategy | Score | Why / risk |
|---:|---|---:|---|
| 1 | **Two-stage safe release: ship Test 26 foundation + Admin controls now, integrate customer-facing layout after answers** | **9.9** | Creates the new immutable target, validates storage/build/deploy isolation and reversible controls without locking ambiguous visual decisions |
| 2 | Implement all Test 26 visuals now using current assumptions | 7.1 | Faster apparent progress, but header/mobile/capability-detail/footer answers could force immediate rework |
| 3 | Build everything on a feature branch and do not publish Test 26 yet | 7.0 | Very safe technically, but does not satisfy the request for the launcher to continue with a new numbered version |
| 4 | Hard-code customer defaults now and add reversibility later | 4.2 | Creates exactly the future-regret/refactor risk this phase is meant to remove |
| 5 | Wait for every customer answer before touching code | 3.9 | Avoids assumptions but wastes a safe window for architecture, version isolation and admin tooling |

**Selected:** Option 1.

### Run 1 scope
- move the active build/deploy target from Test 25 to Test 26 without modifying `/t/25`;
- bump frontend namespace/version to Test 26;
- migrate only catalog data forward from Test 25;
- add typed mobile/tablet/desktop appearance policy;
- add one shared viewport resolver aligned with Tailwind `md=48rem` and `lg=64rem`;
- add validated Pinia persistence + resets;
- add Admin → Appearance with high-value reversible settings;
- add Test 26 launcher entry and a dedicated release/source contract;
- **do not** yet apply ambiguous Home/Header/Capabilities/Footer visual changes.

### Why these boundaries are low-regret
Tailwind’s documented default breakpoints use `48rem` for `md` and `64rem` for `lg`, so the Admin’s three viewport profiles reuse existing layout boundaries rather than inventing a parallel device system. MDN’s `matchMedia()` guidance supports listening to media-query changes instead of relying on user-agent device detection. Pinia’s official guidance fits this state because it is shared across screens and needs typed state/actions, while local one-screen UI state should remain outside global stores.

References:
- https://tailwindcss.com/docs/responsive-design
- https://developer.mozilla.org/en-US/docs/Web/API/Window/matchMedia
- https://pinia.vuejs.org/core-concepts/


## 13. Run 1 delivery — reversible foundation

Delivered in this first safe stage:
- active build target moved to `/t/26`; `/t/25` remains frozen;
- frontend version / browser namespaces moved to Test 26, with catalog migration from Test 25;
- typed appearance profiles added for mobile / tablet / desktop;
- shared viewport resolver aligned with 48rem / 64rem;
- validated Pinia persistence, reset-one and reset-all customer defaults;
- Admin → Appearance added with high-value settings only;
- impossible compact-navigation state guarded by forcing the hamburger control on in compact-drawer mode;
- localized controls added for Persian, Arabic, English and Sorani;
- Test 26 contract added while all Test 11–25 regression contracts remain active;
- launcher lists Test 26 before Test 25.

Validation:
- Frontend build commit: `1b92b0f69a3e2ab68a9e8ce21b93eb3e735f2a74`
- GitHub Actions: **FTP Deploy Run #131 — success**
- immutable guard: PASS
- Test 11–26 contracts: PASS
- portrait generation: PASS
- TypeScript: PASS
- unit tests: PASS
- Vite build `/t/26`: PASS
- FTP smoke: PASS
- `deploy-t`: PASS
- `deploy-root`: skipped

Intentionally not implemented in Run 1:
- customer-facing Home modularization;
- applying the appearance policy to Header/Home/Products;
- capability-detail interaction;
- Favorites share semantics;
- final marketing imagery.

Those remain for Run 2 after the customer answers return, avoiding premature lock-in.


## 14. Run 2 — customer answers closed; public integration

Customer clarification is now treated as final for Test 26. No additional clarification gate is required.  
Rollback checkpoint for this integration: `rollback/test26-foundation-pre-integration` → `152e20e5ec062f48319a45727c7448d4ee68843e`.

### 14.1 Integration strategy after final customer answers

| Rank | Strategy | Score | Why / risk |
|---:|---|---:|---|
| 1 | **Connect the existing per-viewport appearance policy to modular public components, preserving old modes behind settings** | **9.9** | Delivers the customer default without deleting owner-preferred alternatives; smallest regression surface |
| 2 | Hard-code the final customer layout and keep Admin controls decorative | 5.1 | Fast but violates the reversible requirement and repeats the Test 26 foundation bug |
| 3 | Duplicate Home/Header into customer and owner variants | 4.9 | High drift and twice the regression surface |
| 4 | Build a generic page builder before integration | 4.2 | Over-engineered for the current prototype |
| 5 | Wait for more customer feedback | 2.0 | Customer has explicitly closed clarification and asked us to infer remaining details |

**Selected:** Option 1.

### 14.2 Home composition

| Rank | Strategy | Score | Why / risk |
|---:|---|---:|---|
| 1 | **Composition-only Home using independent Hero / About / Why / Capabilities / Product Banner sections** | **9.8** | Modular, testable, independently hideable per viewport and compatible with future CMS mapping |
| 2 | Keep all new markup inline in `HomeView.vue` | 6.0 | Works but creates another maintenance hotspot |
| 3 | One monolithic landing component | 5.0 | Hides boundaries and makes rollback harder |
| 4 | Dedicated route for each Home section | 3.7 | Breaks the requested single landing flow |
| 5 | Keep Test 25 Home and only restyle it | 3.1 | Does not match the confirmed information architecture |

**Selected:** Option 1.

Customer default order:
1. Header
2. Single Hero
3. About Armaghan
4. Why Armaghan
5. Three capability cards
6. Three product-category banners
7. Footer

The product card grid is hidden by default and remains available only through the Admin appearance mode.

### 14.3 Header / device behavior

| Rank | Strategy | Score | Why / risk |
|---:|---|---:|---|
| 1 | **Expanded direct navigation on desktop; retain compact accessible drawer on mobile/tablet; allow Admin override per viewport** | **9.8** | Matches the customer’s desktop intent while respecting the owner’s concern that the customer did not clearly reject mobile navigation |
| 2 | Remove hamburger on every viewport | 5.6 | Literal but risky on small screens |
| 3 | Keep Test 25 header unchanged | 4.8 | Does not satisfy the customer’s visible-desktop-header request |
| 4 | Separate unrelated mobile/desktop implementations | 4.4 | Duplicates behavior and accessibility logic |
| 5 | Auto-decide from user agent | 2.0 | Brittle and inconsistent with project rules |

**Selected:** Option 1.

### 14.4 Capability details

| Rank | Strategy | Score | Why / risk |
|---:|---|---:|---|
| 1 | **Reuse the existing accessible AdaptivePanel: bottom sheet on smaller viewports, modal/panel on desktop** | **9.9** | Exactly matches the customer’s remembered “modern overlay, no subpage” request and reuses tested accessibility behavior |
| 2 | Dedicated capability pages | 7.0 | Good for future SEO, but explicitly not the current requested interaction |
| 3 | In-page accordion | 5.4 | Adds excessive Home length |
| 4 | Full text on cards | 3.1 | Destroys scanability |
| 5 | Tooltip/popover | 1.8 | Unsuitable for long commercial content |

**Selected:** Option 1.

### 14.5 Favorites sharing

| Rank | Strategy | Score | Why / risk |
|---:|---|---:|---|
| 1 | **Versioned anonymous URL containing only validated product codes, native Web Share when available, copy fallback otherwise** | **9.7** | Matches “products only, no personal information”; works in a backend-free prototype and remains migratable to a server token |
| 2 | Backend share token now | 8.2 | Production-grade but prohibited by the current no-backend prototype scope |
| 3 | Serialize full product JSON | 3.9 | Large, stale and unnecessary |
| 4 | Share one product at a time | 2.6 | Does not satisfy list sharing |
| 5 | localStorage-only list | 1.0 | Not shareable across devices |

**Selected:** Option 1.

The shared link changes only the receiver’s rendered list; it never mutates the sender’s favorites. No owner name, customer id, email, phone or visitor token is serialized.

### 14.6 Image decision under incomplete final assets

| Rank | Strategy | Score | Why / risk |
|---:|---|---:|---|
| 1 | **Use already-approved local Hero media, exact approved About source when derivative is available, and documented visibly-labeled placeholders for unresolved capability/banner slots** | **9.6** | Avoids inventing evidence or pretending references are final assets; honors the existing image handoff contract |
| 2 | Reuse unrelated existing images for every missing slot | 5.8 | Looks more complete but misrepresents content |
| 3 | Hotlink stock imagery | 3.8 | Provenance and runtime-dependency risk |
| 4 | Hide missing-image areas | 2.8 | Conceals outstanding asset work |
| 5 | Generate final imagery silently in this UI run | 2.2 | Customer/reference approval and provenance would be unclear |

**Selected:** Option 1.

### 14.7 Confirmed customer interpretation used in code

- Hero: one image + one slogan only by default; carousel preserved as an optional mode.
- Home product grid: hidden by default; product category banners are the Home entry into Products.
- About: supplied copy is final; supplied handshake/trade visual is the requested image.
- Why Armaghan: simple elegant text list, not four cards.
- Capabilities: three cards; full details open in the adaptive overlay, not separate routes.
- Capability final images: not supplied; documented placeholders remain visible.
- Product category banners: customer confirmed the banner concept; supplied collage remains a reference because original per-banner assets were not supplied.
- Footer: simplified structured version based on the customer reference.
- Category numbers 01/02/03: hidden by default, retained only as recoverable presentation data.
- Products intro: pale mint/green surface.
- Favorites: share the product membership only, with no owner metadata.
- No more customer questions are required for Test 26 implementation.

### 14.8 Regression boundaries

- `/t/25` and older snapshots remain immutable.
- Test 26 keeps its existing namespace and build path.
- `HeroCarousel.vue` is not removed.
- `MobileMenuDrawer.vue` is not removed.
- Old Home product-grid mode is not removed.
- Header and section decisions are policy consumers rather than one-way deletion.
- Existing Test 16–25 contracts may accept the new reversible Test 26 structure only where the underlying historic guarantee is still preserved; they must not be weakened to hide a removed capability.


## 15. Run 2 delivery record

Implementation is complete for the customer-approved Test 26 interaction/layout scope, with unresolved marketing media kept explicit in the asset handoff contract.

- Rollback before public integration: `rollback/test26-foundation-pre-integration` → `152e20e5ec062f48319a45727c7448d4ee68843e`.
- Validated integration head: `fafcb0d31e248e3c12ca4533ee85708f5ab08502`.
- **FTP Deploy Run #185: SUCCESS**
  - immutable snapshot guard: PASS
  - Test 11–26 contracts: PASS
  - web-stock / generated-media / placeholder policies: PASS
  - portrait derivative generation: PASS
  - TypeScript type-check: PASS
  - unit tests, including Appearance and Favorites-share tests: PASS
  - Vite production build for `/t/26`: PASS
  - FTP smoke: PASS
  - scoped `deploy-t`: PASS
  - `deploy-root`: SKIPPED
- Launcher completion commit: `57d47e3919a9b87a3fb540723c832ca64fd23d4a`.
- **FTP Deploy Run #186: SUCCESS** — mutable `/t/index.htm` updated, root deployment skipped.
- Test 25 and older snapshots were not modified.

### Delivered customer defaults
- desktop: expanded direct header; mobile/tablet: compact accessible drawer;
- single Hero and editable single slogan;
- no product grid on Home;
- About Armaghan, simple Why list, three capability cards with adaptive detail overlay;
- three category banners linking into filtered Products;
- category numbers hidden;
- soft-mint Products introduction/filter separation;
- structured footer including the requested social-network presence without inventing profile URLs;
- Favorites list sharing by anonymous product-code URL with native Web Share where supported and copy fallback otherwise.

### Deliberately open media slots
The code does not fabricate final evidence imagery. Capability card images, the three final banner assets, and the optimized local derivative of the exact customer-approved About handshake image remain tracked in `docs/TEST26-IMAGE-REQUIREMENTS.md`. Their runtime fallbacks are intentionally conspicuous and documented.
