# Armaghan — Product Backlog

Priority: **P0 current**, P1 next, P2 later.  
Current implementation target: **Test 16 — Adaptive Commerce Workspace**.

## P0 — Test 16

### Snapshot + delivery
- [x] Freeze Tests 01–15.
- [x] Keep `/t/index.htm` mutable and sync it on every `/t` deployment.
- [x] Build Vue source to a new `/t/16` artifact.
- [x] Preserve non-destructive incremental FTP deployment.
- [x] Add Test 16 contract before build/deploy.

### Responsive strategy
- [x] Keep mobile/tablet bottom navigation and app-like flow.
- [x] Hide mobile bottom navigation only at desktop breakpoint (`lg`).
- [x] Add desktop top navigation.
- [x] Widen desktop application shell to 1440px.
- [x] Add desktop sticky product filter rail.
- [x] Keep mobile/tablet category chips and search behavior unchanged in spirit.
- [x] Expand desktop product grid to 3 columns at large and 4 at extra-large widths.
- [ ] Browser QA at 360, 390, 768, 1024, 1280, 1440 and 1920px.

### Theme
- [x] Add explicit `system / light / dark` preference.
- [x] Follow Tailwind selector-based dark-mode pattern.
- [x] Persist explicit theme preference.
- [x] Follow OS changes when `system` is selected.
- [x] Apply theme before Vue mount to avoid first-paint theme flash.
- [x] Move core surfaces, text, borders and media backgrounds to CSS design tokens.
- [x] Keep hidden Design Lab independent from light/dark theme.
- [ ] Complete dark-mode audit for every admin/customer edge state.

### Product cards
- [x] Increase action radius to 0.75rem.
- [x] WhatsApp stays green with the provided WhatsApp mark in white.
- [x] Remove white chip around WhatsApp icon.
- [x] Favorite container stays neutral; only heart becomes red.
- [x] Keep subtle favorite heartbeat with reduced-motion protection.
- [x] Details action stays neutral and coherent with favorite action.
- [x] Improve desktop hover without changing touch behavior.

### Product media
- [x] Preserve two-image automatic carousel and PhotoSwipe lightbox.
- [x] Use `object-contain` instead of aggressive crop for product-list media.
- [x] Add a blurred low-opacity version of the same image behind the contained product image for a studio-frame effect.
- [x] Preserve shimmer, image failure fallback and no-layout-shift aspect ratio.
- [ ] Replace prototype media with owner-approved final photography before customer production handoff.

### Accessibility / regression
- [x] Preserve Test 14 skip-link and route focus behavior.
- [x] Preserve Test 15 sheet focus trap and image lightbox gestures.
- [x] Preserve logout in authenticated and impersonated states.
- [x] Keep language selector permanently available.
- [x] Respect `prefers-reduced-motion`.
- [ ] Add Playwright viewport/keyboard regression suite before production release.

## P1 — Production backend
- [ ] Bootstrap Laravel 13 under `/platform/backend` with SQLite as the default DB.
- [ ] Inertia + Vue 3 + TypeScript + Tailwind customer app.
- [ ] Filament 5 admin.
- [ ] Convert SQLite schema to Laravel migrations, factories and seeders.
- [ ] Install `spatie/laravel-medialibrary` + official Filament integration.
- [ ] Public/private storage disks, responsive conversions and WebP/AVIF.
- [ ] Laravel Socialite for Google login.
- [ ] Secure magic links with hashed tokens, revoke, expiry, scope and audit.
- [ ] Auditable admin impersonation.
- [ ] MySQL staging only after SQLite-backed product behavior is stable.

## Definition of Done — Test 16
1. Tests 01–15 are untouched snapshots.
2. Test 16 is first in the launcher.
3. Mobile/tablet navigation remains app-like.
4. Desktop has top navigation and a stable filter rail instead of a floating mobile nav.
5. Light, dark and system modes work without initial flash.
6. WhatsApp action is green with a white WhatsApp mark.
7. Favorite selection changes the heart only.
8. Product images are not aggressively cropped in cards.
9. Type-check, unit tests, Test 11–16 regression contracts and production build pass.
10. FTP uploads only `/t/16` plus the launcher; root stays untouched and no remote deletion occurs.
