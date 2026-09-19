# Armaghan — Product Backlog

Priority: **P0 current**, P1 next, P2 later.  
Current implementation target: **Test 15 — Digikala Media, Lightbox & Locale**.

## P0 — Test 15

### Snapshot safety + CI
- [x] Freeze Tests 01–14.
- [x] Keep launcher mutable and newest test first.
- [x] Build Vue source to a new numbered `/t/15` artifact.
- [x] Keep type-check, unit tests, production build, SQLite and frozen-test guards before FTP.
- [x] Keep FTP incremental and non-destructive.
- [x] Add strict Digikala media completeness gate: 18 products × exactly 2 images.

### Product media
- [x] Use Digikala catalog imagery for the prototype under the authorization reported by the project owner.
- [x] Download/vend prototype images during CI from official Digikala API endpoints and serve them locally from the Test 15 artifact.
- [x] Record source product/image provenance in a generated manifest.
- [x] Give every prototype product two images.
- [x] Auto-rotate product images only when the card is visible.
- [x] Pause rotation while hovered/focused, while the page is hidden, and for reduced-motion users.
- [x] Add fullscreen PhotoSwipe lightbox.
- [x] Horizontal swipe between product images.
- [x] Pinch/double-action zoom and wheel zoom.
- [x] Background click/tap close + vertical-drag close + Escape.
- [x] Blurred dark backdrop and bottom product title/code overlay.
- [x] Preserve polished no-image fallback and shimmer behavior.
- [ ] Manually review Test 15 women-category Digikala imagery for modesty before promotion to final production content.

### Card action polish
- [x] Add the owner-uploaded WhatsApp SVG as the single WhatsApp brand asset.
- [x] Recolor WhatsApp logo to standard `#25D366`.
- [x] Keep WhatsApp CTA green with a white logo chip for contrast.
- [x] Reduce card action radius to `0.25rem`.
- [x] Remove red background/border treatment from favorite action.
- [x] Only the selected heart becomes red.
- [x] Add gentle heartbeat animation to selected heart.
- [x] Respect reduced-motion preference.

### Language
- [x] Add FA / AR / EN / KU-Sorani locale store.
- [x] Always expose manual language selection in the top bar.
- [x] Persist only explicit manual language choice.
- [x] First-visit IP-country/region guess with short timeout and browser-language fallback.
- [x] Region-first Kurdish mapping for Iraqi Kurdistan and Kurdish-majority Iranian provinces.
- [x] Iran outside mapped Kurdish regions → Persian.
- [x] Arabic countries → Arabic.
- [x] Other known countries → English.
- [x] West Azerbaijan uses Kurdish only when browser locale also indicates Kurdish to reduce false positives.
- [x] Update document `lang` / `dir` whenever locale changes.
- [ ] Complete translation coverage for every catalog/product/admin content field before final production handoff.
- [ ] Move geolocation decision server-side/CDN-side in production to avoid an extra client-side IP lookup.

### Authentication / account regression
- [x] Direct logout remains visible in header for every authenticated state.
- [x] Logout remains available inside open sheets.
- [x] Logout clears impersonation state as well as role state.
- [x] Test 15 uses new versioned session/catalog/favorites storage keys to avoid stale prototype state.
- [ ] Production Laravel auth + password reset + Google Socialite.
- [ ] Secure magic links with hash, revoke, expiry, scope and audit.
- [ ] Auditable admin impersonation.

### Ordering / WhatsApp
- [x] Preserve full Test 10-style production wizard.
- [x] Keep six request paths.
- [x] Keep one canonical WhatsApp message builder and seller number.
- [x] Localize WhatsApp message labels for four UI languages.
- [ ] Finish localized wizard copy and localized product content.

## P1 — Production backend
- [ ] Bootstrap Laravel 13 under `/platform/backend` with SQLite default.
- [ ] Inertia + Vue 3 + TypeScript + Tailwind customer application.
- [ ] Filament 5 admin.
- [ ] Convert current SQLite schema/seed to Laravel migrations, factories and seeders.
- [ ] Install `spatie/laravel-medialibrary` plus official Filament integration.
- [ ] Public/private media disks, responsive conversions and WebP/AVIF.
- [ ] Laravel Socialite.
- [ ] Pest feature tests + Playwright browser regression suite.
- [ ] MySQL staging only after SQLite-backed behavior is stable.

## Definition of Done — Test 15
1. Tests 01–14 remain untouched.
2. All source contracts, Vue type-checks, unit tests and production build pass.
3. CI confirms 18 Digikala prototype products and 36 local image files.
4. Every product card can rotate between exactly two images without layout shift.
5. Lightbox supports swipe, zoom, close gestures/keyboard and readable product caption.
6. WhatsApp/favorite/detail actions remain visually coherent without photography.
7. Manual language choice always works; automatic guess never overrides a saved manual choice.
8. Logout works from admin, customer and impersonation states.
9. Test 15 is listed first and deployed only to `/public_html/t/15`.
10. Root remains untouched and no remote deletion occurs.
