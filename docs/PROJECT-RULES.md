# Armaghan Project Rules

## Deployment and immutable tests
1. `/t` is the validation workspace; root `/public_html` is protected.
2. Never recursive-delete, mirror-delete or root-wide-sync over FTP.
3. Prototype deployment may write only to `/public_html/t`.
4. Released tests are immutable snapshots.
5. Tests 01–11 are frozen; current source target is Test 12.
6. Do not modify an older numbered test to improve a newer one.
7. `/t/index.htm` is mutable and newest test must be first.
8. Test 12 is generated from `/platform/frontend`; do not hand-edit compiled Test 12 files on the host.

## UX
9. Persian-first/RTL-first; English LTR; Arabic/Sorani RTL.
10. Mobile-first with desktop responsive extension.
11. Guest browsing and WhatsApp inquiry do not require login.
12. Bottom navigation contains top-level destinations only.
13. WhatsApp handoff always shows a message preview first.
14. Six request paths are domain values, not duplicated UI strings.
15. Fixed vs negotiable specs differ by icon, label and surface — never color alone.
16. Adaptive product details: bottom sheet mobile, side drawer desktop.
17. Missing images must preserve aspect ratio and show polished fallback.
18. Shimmer must not cause layout shift.
19. Touch targets, focus-visible, safe-area and reduced-motion behavior are required.

## Visual system
20. Palette anchors: `#21946A`, `#151EDA`, `#C8E3DB`, `#FFFEFF`, `#FFB514`.
21. UI icons use Lucide Vue imports; no runtime global SVG sizing rules.
22. Women in generated photography must use fully modest Islamic hijab, no visible hair and no revealing/body-emphasizing styling.
23. Hidden Design Lab opens after 3-second top-bar long press and may switch design system/palette without changing business state.

## Vue 3 engineering
24. Test 12 frontend: Vue 3 + TypeScript + Vite + Vue Router + Pinia + Tailwind.
25. Use SFCs and `<script setup>`; no giant HTML-template strings.
26. Feature-first organization; stores only for cross-screen/domain state.
27. Components never construct WhatsApp URLs ad hoc; use the message-builder service.
28. Components do not read/write auth storage directly; use the session store.
29. Logout must be idempotent and available in every authenticated modal/sheet state.
30. SmartImage owns loading/error/fallback behavior for content photography.
31. Static Test 12 uses hash routing; production Laravel/Inertia may use server routes.
32. Type-check + unit tests + production build are deployment gates.

## Database/backend
33. Development defaults to SQLite with no MySQL credentials.
34. Production target is Laravel 13.
35. Admin target is Filament 5.
36. Google OAuth uses Laravel Socialite.
37. Production direct links use high-entropy values stored hashed with revoke/regenerate, scope, expiry and audit.
38. Admin impersonation must be explicit, reversible and auditable.

## Asset management
39. Binary media is stored on Laravel filesystem disks, not database BLOBs.
40. Database stores media metadata/relationships.
41. Primary Laravel media package: `spatie/laravel-medialibrary`.
42. Primary Filament integration: official `filament/spatie-laravel-media-library-plugin`.
43. Local disk is fine for development; production public media should be S3-compatible + CDN when hosting is ready.
44. Private customer/order documents use private disk + short-lived signed URLs.
45. Uploads require MIME/decode validation, size/pixel limits and metadata stripping.
46. Generated derivatives include deterministic card/gallery/hero sizes; never upscale small originals.
47. High-resolution production originals do not belong in Git.

## QA
48. Frozen-test guard runs before deploy.
49. SQLite schema smoke test runs before deploy.
50. Test 12 source contract, Vue unit tests, TypeScript and Vite build run before deploy.
51. Verify mobile/desktop, RTL/LTR, sheet close, logout, impersonation, favorites, wizard and WhatsApp.
52. No remote deletion to match Git.
