# Armaghan Project Rules

## Deployment and immutable tests
1. `/t` is the validation workspace; root `/public_html` is protected.
2. Never recursive-delete, mirror-delete or root-wide-sync over FTP.
3. Prototype deployment may write only to `/public_html/t`.
4. Released tests are immutable snapshots.
5. Tests 01–23 are frozen; current source target is Test 24.
6. Do not modify an older numbered test to improve a newer one.
7. `/t/index.htm` is mutable and newest test must be first.
8. Current Vue test is generated from `/platform/frontend`; do not hand-edit compiled test files on the host.

## UX
9. Persian-first/RTL-first; English LTR; Arabic/Sorani RTL.
10. Mobile-first with desktop responsive extension.
11. Guest browsing and WhatsApp inquiry do not require login.
12. Bottom navigation contains top-level destinations only.
13. WhatsApp handoff always shows a message preview first.
14. Six request paths are domain values, not duplicated UI strings.
15. Fixed vs negotiable specs differ by icon, label and surface — never color alone.
16. Adaptive product details: bottom sheet mobile, side drawer desktop.
17. Product/category media preserves the full garment silhouette when cropping would hide meaningful edges; use a polished contained fallback instead of destructive crop.
18. Shimmer must not cause layout shift.
19. Touch targets, focus-visible, safe-area and reduced-motion behavior are required.

## Visual/media policy
20. Palette anchors: `#21946A`, `#151EDA`, `#C8E3DB`, `#FFFEFF`, `#FFB514`.
21. UI icons use Lucide Vue imports; no runtime global SVG sizing rules.
22. Final women photography must be fully modest Islamic hijab, no visible hair and no revealing/body-emphasizing styling.
23. The canonical generated set under `platform/frontend/public/images/final` is the default media source for the current Vue prototype and every later numbered prototype; temporary web stock is fallback-only and must not be presented as actual Armaghan products, factory, staff, customers or certificates.
24. Automated stock sourcing must use reuse-compatible licensing and record source, creator, license and checksum.
25. No runtime hotlinking for catalog photography; vendor an optimized local derivative.
26. Hidden Design Lab opens after 3-second top-bar long press and may switch design system/palette without changing business state.

## Vue 3 engineering
27. Frontend: Vue 3 + TypeScript + Vite + Vue Router + Pinia + Tailwind.
28. Use SFCs and `<script setup>`; no giant HTML-template strings.
29. Feature-first organization; stores only for cross-screen/domain state.
30. Components never construct WhatsApp URLs ad hoc; use the message-builder service.
31. Components do not read/write auth storage directly; use the session store.
32. Logout must be idempotent and available in every authenticated modal/sheet state.
33. SmartImage owns loading/error/fallback behavior for content photography.
34. Static tests use hash routing; production Laravel/Inertia may use server routes.
35. Type-check + unit tests + production build are deployment gates.

## Database/backend
36. Development defaults to SQLite with no MySQL credentials.
37. Production target is Laravel 13.
38. Admin target is Filament 5.
39. Google OAuth uses Laravel Socialite.
40. Production direct links use high-entropy values stored hashed with revoke/regenerate, scope, expiry and audit.
41. Admin impersonation must be explicit, reversible and auditable.

## Host-centric asset management
42. Runtime binary media is host-centric and stored on Laravel local/public/private filesystem disks, not database BLOBs.
43. Public media starts under `storage/app/public/media` and is served through `public/storage`.
44. Private customer/order documents remain under private host storage and are served only through authorized/temporary access.
45. Primary Laravel media package: `spatie/laravel-medialibrary`.
46. Primary Filament integration: official `filament/spatie-laravel-media-library-plugin`.
47. Do not introduce S3/CDN as the primary runtime store unless explicitly approved later; off-host storage is backup/DR for now.
48. Uploads require MIME/decode validation, size/pixel limits and metadata stripping.
49. Generated derivatives include deterministic card/gallery/hero sizes; never upscale small originals.
50. Production customer/private originals never belong in Git.

## QA
51. Frozen-test guard runs before deploy.
52. SQLite schema+seed smoke test runs before deploy.
53. Current source contract, web-stock provenance policy, Vue unit tests, TypeScript and Vite build run before deploy.
54. Verify mobile/desktop, RTL/LTR, sheet close, logout, impersonation, favorites, wizard and WhatsApp.
55. No remote deletion to match Git.
