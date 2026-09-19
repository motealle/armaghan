# Vue 3 Architecture — Armaghan

## Decision
The maintainable frontend baseline is a **Vite + Vue 3 + TypeScript SPA** with Vue Single-File Components and `<script setup>`. The static validation build is Test 12. Later, the same component/domain layers move behind Laravel/Inertia without preserving prototype-only persistence.

Current stack:
- Vue 3
- TypeScript
- Vite
- Vue Router
- Pinia
- Tailwind CSS v4 via the Vite plugin
- @lucide/vue
- Vitest + Vue Test Utils

## What we gain
- Component isolation instead of long template strings.
- Compile-time type checking for product/order/auth contracts.
- Testable stores and composables.
- Explicit router state and deep-linkable screens.
- Reusable order wizard, media and sheet primitives.
- Better refactoring when Laravel APIs replace mock repositories.
- Tree-shaken icon imports instead of runtime scanning/global SVG rules.
- Production build with hashed assets and code splitting.

## What we give up / costs
- A Node build step is mandatory.
- More files and conventions than the earlier single-file prototypes.
- Dependency upgrades become a maintenance responsibility.
- Client-side SPA code can grow if state is placed indiscriminately in Pinia.
- Static Test 12 uses hash routing because the FTP host has no SPA rewrite contract; production Laravel/Inertia will use server routes.
- Prototype auth remains demonstrative until Laravel owns sessions.

## Architecture rules

### 1. Feature-first, not one giant components folder
```
src/
  app/
  components/
    layout/
    media/
    ui/
  features/
    auth/
    catalog/
    favorites/
    orders/
    customers/
    admin/
  stores/
  router/
  data/
  types/
  styles/
```

### 2. Stores only for cross-screen state
Pinia contains session, favorites, design preferences and order-draft state. Sheet visibility, input focus and other local UI state remain in components/composables.

### 3. Repository boundary
Components do not read LocalStorage or call HTTP directly. Feature repositories/services expose domain operations. Test 12 uses a browser mock repository; Laravel will later replace it with HTTP/Inertia props.

### 4. Auth
A single session store owns `login`, `logout`, `impersonate` and `stopImpersonating`. Logout is idempotent: it works regardless of current route, modal state or impersonation state and clears every demo-session key.

### 5. Media
Every content image goes through `SmartImage.vue`, which provides:
- fixed aspect ratio,
- shimmer,
- lazy/eager policy,
- fallback illustration,
- no broken-image browser glyph,
- eventual responsive `srcset` contract.

### 6. Orders
The six business paths are domain values:
- simple purchase
- buy available
- request unavailable
- custom production
- production with brand
- production with packaging

The production wizard is its own feature state machine. Screens render steps; components do not infer business rules ad hoc.

### 7. WhatsApp
One message-builder module owns normalization and URL creation. UI only chooses a request path and asks the builder for preview/link. This prevents inconsistent phone/message behavior across cards and wizards.

### 8. Tests
Minimum gates:
- type-check,
- unit tests for logout/session,
- unit tests for request-path/message generation,
- unit tests for production wizard transitions,
- build,
- immutable snapshot guard,
- SQLite schema smoke test.

Later add Playwright for mobile/desktop visual and full-flow regression.

## Laravel/Inertia transition
Vue components remain useful. Replace:
- mock repositories → Laravel endpoints/Inertia props/actions,
- LocalStorage session → Laravel session,
- mock products/customers → Eloquent/API resources,
- browser image compression → server media pipeline.

Filament remains the production admin control plane. The Vue admin screens in Test 12 are product/interaction validation, not a duplicate permanent admin framework.

## SSR
Not required now. Vue's own guidance favors the simpler Vite setup when SSR is not needed. Search/catalog pages can later gain server-rendered public landing pages in Laravel if SEO requirements justify it; do not adopt Nuxt solely for fashion.
