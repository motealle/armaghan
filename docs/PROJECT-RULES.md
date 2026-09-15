# Armaghan Prototype Rules

## Scope

1. `/t` is the active UX/UI prototype workspace.
2. Root `/public_html` landing is protected.
3. Never use recursive FTP delete, mirror-delete, or root-wide synchronization.
4. Prototype CI/CD may deploy only to `/public_html/t`.
5. Every test is an immutable numbered snapshot: `/t/NN/`.
6. Do not overwrite prior tests.

## UX

7. Persian-first and RTL-first.
8. Mobile-first; desktop is a responsive extension.
9. Guest-first; login is not a prerequisite for inquiry.
10. Primary B2B action is «استعلام» / «لیست استعلام» / «سبد استعلام», not generic ecommerce wishlist.
11. Fixed specs and variable specs must be visually and semantically distinct; never rely on color alone.
12. Available and unavailable products must expose different valid request paths.
13. Product details use bottom sheets on mobile and modal/drawer on desktop.
14. WhatsApp is the primary commercial handoff; always show a message preview before navigation.
15. Touch targets must be generous and mobile controls must respect safe-area insets.

## Visual system

16. Dark theme taste reference: current Midjourney web — visual-first, deep dark surfaces, compact controls, rounded surfaces, strong image hierarchy, restrained chrome.
17. Light theme taste reference: Digikala — practical RTL commerce density, strong search/navigation, product-card clarity and explicit CTAs.
18. These are visual references, not a license to copy proprietary branding or layouts.
19. Armaghan branding remains primary: navy/gold/mint/green plus logo blue `#0613BF`.
20. The UI must feel export/B2B professional, not childish.

## Engineering

21. Prefer vanilla HTML/CSS/JavaScript.
22. No framework/build tool unless explicitly justified.
23. All internal links and assets must be relative.
24. Prototypes must work when `/t` is moved to the `public_html` root.
25. Keep state simple and inspectable; LocalStorage is allowed for inquiry state.
26. No backend, database, real authentication, payment, API integration or real customer uploads.
27. Mock data only; use visual placeholders instead of real product photography.
28. Keep reusable UI primitives consistent across tests.
29. Add a short test metadata comment to every test HTML file: Test, Title, Purpose, Hypothesis, Main UX Pattern.

## Content

30. Use Persian UI labels wherever the user-facing label is known.
31. Avoid unnecessary English UI copy; English may remain in technical metadata and code identifiers.
32. Keep wording short, trustworthy and commercial.
33. Do not invent business claims, prices, certifications or customer data.

## QA

34. Every prototype must be tested at narrow mobile width and desktop width.
35. Verify RTL, keyboard/focus basics, touch controls, modal closing, state transitions and WhatsApp encoding.
36. Verify no internal absolute URLs exist.
37. Verify the launcher lists the newest test first.
38. Verify deployment scope before any FTP write.
39. Never modify `/public_html` root as part of `/t` development.

## Delivery policy

40. Prefer one coherent commit/run for a complete prototype increment.
41. Keep deployment deterministic and allowlisted.
42. Do not delete remote files to make a prototype match the repository.
43. If a requirement conflicts with the root-protection rule, stop and report the conflict instead of improvising.
