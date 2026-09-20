# Armaghan — Product Backlog

Priority: **P0 current**, P1 next, P2 later.  
Current implementation target: **Test 18 — App Navigation + Drawer Account Dashboard**.

## P0 — Test 18

### Snapshot + delivery
- [x] Freeze Tests 01–17.
- [x] Keep `/t/index.htm` mutable and newest test first.
- [x] Build Vue source into a new `/t/18` artifact.
- [x] Add a Test 18 regression contract before deployment.
- [x] Keep FTP incremental, launcher-synced, root-safe and non-destructive.

### Desktop / large-screen header
- [x] Restore the Test 16 app-like button treatment for Home / Products / Production / Favorites / Tracking.
- [x] Keep those five app-like navigation buttons centered in the large-screen header.
- [x] Keep language combo and sign-in/account controls visible only at `lg` and above.
- [x] Keep the hamburger hidden at `lg` and above.
- [x] Raise header stacking so scrolling content never paints above it.

### Theme
- [x] Remove the visible/system-selectable theme state.
- [x] Theme choices exposed to the user are only light and dark.
- [x] On first visit with no saved preference, initialize light/dark from `prefers-color-scheme`.
- [x] Persist a manual light/dark choice after the user toggles it.
- [x] Use one small icon-only sun/moon toggle at every breakpoint.
- [x] Keep the pre-mount theme script so first paint does not flash the wrong theme.

### Mobile / tablet drawer
- [x] Keep hamburger + drawer only below the desktop breakpoint.
- [x] Remove duplicated Home / Products / Production / Favorites / Tracking links from the drawer.
- [x] Put language selection at the top as four round buttons in one row: `Fa`, `En`, `ع`, `ک`.
- [x] Give each language button a full accessible name and correct `lang` attribute; do not use country flags as language labels.
- [x] Use about 60vw drawer width on phones; cap tablet drawer near the 320px navigation-drawer guideline.
- [x] For very narrow phones, allow a small minimum width so four accessible language targets still fit.
- [x] Use a 40% dark scrim plus backdrop blur outside the drawer.
- [x] Keep drawer keyboard-safe: Escape, focus trap, focus return, outside-tap close.
- [x] Keep the bottom account zone fixed inside the drawer.

### Drawer account dashboard — 10-idea brainstorm
The drawer must add account value instead of duplicating the bottom navigation. Candidate information:
1. Active order state.
2. Current order timeline stage.
3. Next required customer action.
4. Favorites / saved-products count.
5. Prepayment status.
6. Repeat-order shortcut.
7. Latest inquiry / quote state.
8. Private magic-link status.
9. New documents / approvals needing attention.
10. Direct sales-contact status / last handoff.

**Selected for Test 18:** active order, timeline/current stage, next action, favorites count. For admin, show customer count, active-order count, product count and next operational action. The selection favors glanceable, frequently useful, actionable information over rare configuration details.
- [x] Implement the selected customer dashboard metrics inside the drawer.
- [x] Implement an admin-specific summary inside the drawer.
- [x] For guests, show a concise sign-in benefit card instead of fake account metrics.
- [x] When signed in, show demo profile identity + country flag in the fixed bottom account row.
- [x] When impersonating, use the impersonated demo customer's name/flag/status.
- [x] Keep the blue sign-in button at the fixed bottom of the drawer for guests.

### Product card actions
- [x] Add an admin-selectable card-action display preference.
- [x] Mode A — default compact: icon-only actions, icons ~80% of labeled-mode size, centered vertically and horizontally.
- [x] Mode B — labeled: show text under the icons with labels `سفارش / مطلوب / مشخصات`.
- [x] Persist the admin selection locally in the prototype.
- [x] Expose the two-mode selector in the admin dashboard.
- [x] Keep WhatsApp green/white, favorite neutral with red-heart-only selected state, and details neutral.

### Bottom navigation
- [x] Preserve the existing app-like mobile/tablet bottom navigation.
- [x] Add a very subtle shadow using about 20% shadow alpha.
- [x] Keep safe-area padding and current touch-target sizing.

### Typography
- [x] Replace the main Vazirmatn stylesheet with **Vazirmatn FD** so ASCII digits render with Persian digit glyphs.
- [x] Apply Vazirmatn FD throughout Persian/Arabic/Kurdish UI.
- [x] Load and use **Roboto** when the active document language is English.
- [x] Keep `lang` and `dir` synchronized with locale selection.
- [x] Apply Vazirmatn FD to the test launcher as well.

### Image planning memory
- [x] Create root-level `pics.md`.
- [x] Add a table with: use/surface, dimensions, format, repository storage path, exact filename, Persian generation prompt.
- [x] Cover the future hero, baby, kids, modest-women, production, export/logistics, fabric/detail and trust/document photography needs.
- [x] Keep Test 18 itself SVG-first; `pics.md` is the future approved-media plan.

### Research-backed implementation notes
- Material navigation-drawer guidance historically caps temporary drawer width around **280px mobile / 320px tablet**; Test 18 combines the requested 60vw phone target with a tablet cap instead of allowing a very wide tablet drawer.
- W3C internationalization guidance recommends identifying languages as languages rather than with country flags; therefore drawer language controls use script/code labels plus accessible names, not flags.
- Language changes continue to update the document `lang` attribute for assistive technologies.

## P1 — Production backend
- [ ] Laravel 13 + SQLite development backend.
- [ ] Inertia + Vue 3 + TypeScript + Tailwind customer frontend.
- [ ] Filament 5 admin.
- [ ] Spatie Media Library + Filament integration for final approved photography.
- [ ] Google Socialite.
- [ ] Secure magic links with revoke / expiry / scope / audit.
- [ ] Auditable admin impersonation.
- [ ] MySQL staging after SQLite-backed behavior stabilizes.

## Definition of Done — Test 18
- [x] Tests 01–17 remain immutable.
- [x] Test 18 is first in the launcher.
- [x] Large-screen navigation is centered and app-like; mobile/tablet bottom navigation is unchanged in spirit.
- [x] Mobile/tablet drawer contains language + account dashboard, not duplicate primary navigation.
- [x] Drawer width/scrim behave correctly on phone and tablet.
- [x] Theme exposes only light/dark, but first visit inherits OS light/dark.
- [x] Desktop language and login controls remain visible; mobile/tablet copies live only inside the drawer.
- [x] Admin can choose compact icon-only or labeled card actions; compact is default.
- [x] Header always overlays page content during scroll.
- [x] Vazirmatn FD is active for Persian digits and Roboto is used for English.
- [x] `pics.md` exists at repository root with the complete future image specification table.
- [x] Type-check, unit tests, Test 11–18 contracts, build, FTP smoke and deploy all passed in **FTP Deploy Run #55**; root deploy was skipped and no remote files were deleted.

### Delivery record
- [x] Main implementation commit: `7e57c7f7994c8945efad0d6bcdb1b266e8e22402`.
- [x] FTP Deploy Run #55: QA success, build success, FTP smoke success, `deploy-t` success, `deploy-root` skipped.
- [x] Published `/public_html/t/18/index.html` and resynced `/public_html/t/index.htm`.
