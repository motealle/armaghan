# Armaghan Project Rules

## Deployment and immutable tests
1. `/t` is the UX/product-validation workspace.
2. Root `/public_html` landing is protected.
3. Never recursive-delete, mirror-delete or root-wide-sync over FTP.
4. Prototype CI/CD may deploy only to `/public_html/t`.
5. Every released test is an immutable numbered snapshot.
6. The canonical frozen list is `docs/IMMUTABLE-TESTS.txt`; CI must fail if a commit modifies a frozen test directory.
7. Current work always goes into a new test folder. Do not “improve” an older test in place.
8. Launcher `/t/index.htm` is mutable and must list the newest test first.

## UX
9. Persian-first and RTL-first; Arabic and Sorani are RTL, English is LTR.
10. Mobile-first, with desktop as a responsive extension.
11. Guest browsing and inquiry must not require login.
12. WhatsApp is the primary commercial handoff and must show a message preview before navigation.
13. Fixed specs and negotiable specs must differ by icon, label and surface treatment; never color alone.
14. Available/unavailable products expose valid context-aware request paths.
15. Product detail uses an adaptive sheet: bottom sheet on mobile and drawer/modal on larger screens unless a test explicitly compares another pattern.
16. Touch targets, focus handling, reduced motion and safe-area insets are required.
17. Bottom navigation is for top-level destinations, not one-off actions.

## Visual system
18. Brand palette anchor: green `#21946A`, royal blue `#151EDA`, mint `#C8E3DB`, near-white `#FFFEFF`, gold `#FFB514`.
19. UI iconography must come from one coherent icon family; Test 11 uses Lucide.
20. Never apply unconstrained global sizing rules to all `svg` elements.
21. Photography is preferred; missing photography must fall back to polished scoped SVG/gradient placeholders.
22. Women shown in generated imagery must use fully modest Islamic hijab styling with no visible hair and no revealing/body-emphasizing clothing.
23. Shimmer/skeleton loading must not cause layout shift.
24. Five design-system variants and five color-set variants may be exposed in the hidden Test 11 design lab.

## Engineering
25. Tests 01–10 keep their original implementation.
26. Test 11 is allowed to use Tailwind because it explicitly evaluates a production-like Tailwind direction.
27. Prototype assets for Test 11 live under `/t/11`; do not modify `/t/shared-v2` because Tests 08–10 depend on it.
28. Internal links/assets use relative paths inside prototypes.
29. No real secrets, passwords, customer data or production credentials in prototype code.
30. Demo credentials `1/1` and `2/2` are strictly test-only and must never be reused by the production backend.
31. Client-side demo auth is not security; production auth must be server-side.
32. Browser-uploaded prototype images are mock/demo state only.
33. The production application target lives separately under `/platform`.

## Database and backend
34. Development database defaults to SQLite and must work without MySQL credentials.
35. SQLite path and schema must be reproducible from repository scripts.
36. MySQL is a later deployment target configured only through environment/secrets.
37. Production target: Laravel + Inertia/Vue + Tailwind for customer UI and Filament for admin.
38. Magic links in production must use high-entropy random tokens stored hashed, with revoke/regenerate, optional expiry, scope and audit.
39. Admin impersonation must be auditable and visually obvious while active.
40. Uploaded media must be validated, size-limited and server-optimized in production.

## QA and delivery
41. QA runs before FTP smoke-test/deploy.
42. Verify mobile, desktop, RTL/LTR, keyboard/focus, modal/sheet closing, state transitions and WhatsApp encoding.
43. Verify no frozen test paths changed.
44. Verify newest test is first in launcher.
45. Verify deployment scope before FTP write.
46. Never modify `/public_html` root as part of `/t` development.
47. Prefer one coherent final main-branch commit/run for each prototype increment.
48. Do not delete remote files to make hosting match Git.
