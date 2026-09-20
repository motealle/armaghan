# Armaghan — Product Backlog

Priority: **P0 current**, P1 next, P2 later.  
Current implementation target: **Test 18 — App Navigation + Drawer Account Dashboard**.

## P0 — Test 18

### Snapshot + delivery
- [ ] Freeze Tests 01–17.
- [ ] Keep `/t/index.htm` mutable and newest test first.
- [ ] Build Vue source into a new `/t/18` artifact.
- [ ] Add a Test 18 regression contract before deployment.
- [ ] Keep FTP incremental, launcher-synced, root-safe and non-destructive.

### Desktop / large-screen header
- [ ] Restore the Test 16 app-like button treatment for Home / Products / Production / Favorites / Tracking.
- [ ] Keep those five app-like navigation buttons centered in the large-screen header.
- [ ] Keep language combo and sign-in/account controls visible only at `lg` and above.
- [ ] Keep the hamburger hidden at `lg` and above.
- [ ] Raise header stacking so scrolling content never paints above it.

### Theme
- [ ] Remove the visible/system-selectable theme state.
- [ ] Theme choices exposed to the user are only light and dark.
- [ ] On first visit with no saved preference, initialize light/dark from `prefers-color-scheme`.
- [ ] Persist a manual light/dark choice after the user toggles it.
- [ ] Use one small icon-only sun/moon toggle at every breakpoint.
- [ ] Keep the pre-mount theme script so first paint does not flash the wrong theme.

### Mobile / tablet drawer
- [ ] Keep hamburger + drawer only below the desktop breakpoint.
- [ ] Remove duplicated Home / Products / Production / Favorites / Tracking links from the drawer.
- [ ] Put language selection at the top as four round buttons in one row: `Fa`, `En`, `ع`, `ک`.
- [ ] Give each language button a full accessible name and correct `lang` attribute; do not use country flags as language labels.
- [ ] Use about 60vw drawer width on phones; cap tablet drawer near the 320px navigation-drawer guideline.
- [ ] For very narrow phones, allow a small minimum width so four accessible language targets still fit.
- [ ] Use a 40% dark scrim plus backdrop blur outside the drawer.
- [ ] Keep drawer keyboard-safe: Escape, focus trap, focus return, outside-tap close.
- [ ] Keep the bottom account zone fixed inside the drawer.

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
- [ ] Implement the selected customer dashboard metrics inside the drawer.
- [ ] Implement an admin-specific summary inside the drawer.
- [ ] For guests, show a concise sign-in benefit card instead of fake account metrics.
- [ ] When signed in, show demo profile identity + country flag in the fixed bottom account row.
- [ ] When impersonating, use the impersonated demo customer's name/flag/status.
- [ ] Keep the blue sign-in button at the fixed bottom of the drawer for guests.

### Product card actions
- [ ] Add an admin-selectable card-action display preference.
- [ ] Mode A — default compact: icon-only actions, icons ~80% of labeled-mode size, centered vertically and horizontally.
- [ ] Mode B — labeled: show text under the icons with labels `سفارش / مطلوب / مشخصات`.
- [ ] Persist the admin selection locally in the prototype.
- [ ] Expose the two-mode selector in the admin dashboard.
- [ ] Keep WhatsApp green/white, favorite neutral with red-heart-only selected state, and details neutral.

### Bottom navigation
- [ ] Preserve the existing app-like mobile/tablet bottom navigation.
- [ ] Add a very subtle shadow using about 20% shadow alpha.
- [ ] Keep safe-area padding and current touch-target sizing.

### Typography
- [ ] Replace the main Vazirmatn stylesheet with **Vazirmatn FD** so ASCII digits render with Persian digit glyphs.
- [ ] Apply Vazirmatn FD throughout Persian/Arabic/Kurdish UI.
- [ ] Load and use **Roboto** when the active document language is English.
- [ ] Keep `lang` and `dir` synchronized with locale selection.
- [ ] Apply Vazirmatn FD to the test launcher as well.

### Image planning memory
- [ ] Create root-level `pics.md`.
- [ ] Add a table with: use/surface, dimensions, format, repository storage path, exact filename, Persian generation prompt.
- [ ] Cover the future hero, baby, kids, modest-women, production, export/logistics, fabric/detail and trust/document photography needs.
- [ ] Keep Test 18 itself SVG-first; `pics.md` is the future approved-media plan.

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
1. Tests 01–17 remain immutable.
2. Test 18 is first in the launcher.
3. Large-screen navigation is centered and app-like; mobile/tablet bottom navigation is unchanged in spirit.
4. Mobile/tablet drawer contains language + account dashboard, not duplicate primary navigation.
5. Drawer width/scrim behave correctly on phone and tablet.
6. Theme exposes only light/dark, but first visit inherits OS light/dark.
7. Desktop language and login controls remain visible; mobile/tablet copies live only inside the drawer.
8. Admin can choose compact icon-only or labeled card actions; compact is default.
9. Header always overlays page content during scroll.
10. Vazirmatn FD is active for Persian digits and Roboto is used for English.
11. `pics.md` exists at repository root with the complete future image specification table.
12. Type-check, unit tests, Test 11–18 contracts, build, FTP smoke and deploy all pass without touching root or deleting remote files.
