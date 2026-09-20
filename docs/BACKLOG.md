# Armaghan — Product Backlog

Priority: **P0 current**, P1 next, P2 later.  
Current implementation target: **Test 17 — Cleaner Header, Drawer & Modal Login**.

## P0 — Test 17

### Snapshot + delivery
- [x] Freeze Tests 01–16.
- [x] Keep launcher mutable and newest test first.
- [x] Build Vue source into a new `/t/17` artifact.
- [x] Remove photo-fetch dependency from the active prototype build.
- [x] Keep FTP incremental, launcher-synced and non-destructive.
- [x] Add Test 17 contract before deployment.

### Mobile header
- [x] Keep mobile top bar visually simple.
- [x] Show brand + single theme icon + hamburger only.
- [x] Move language, login/logout and full navigation into a side drawer.
- [x] Keep mobile/tablet bottom navigation as the fast primary app-like navigation.
- [x] Add blurred dark backdrop to the drawer.
- [x] Close drawer on outside tap, Escape and route change.

### Desktop header
- [x] Center the main navigation at desktop width.
- [x] Remove pill/button treatment from navigation links.
- [x] Use a small underline-style active state.
- [x] Keep one compact icon-only theme control.
- [x] Keep language and login/account utilities on the edge of the header.

### Theme
- [x] Keep system / light / dark state.
- [x] One icon-only button cycles system → light → dark.
- [x] Keep persisted preference and OS-following system mode.
- [x] Preserve no-flash pre-mount theme application.

### Login
- [x] Replace login bottom sheet with centered modal/lightbox.
- [x] Add dark blurred backdrop.
- [x] Close on outside click and Escape.
- [x] Preserve focus trap and focus return.
- [x] Keep demo credentials 1/1 and 2/2.

### Product media
- [x] Remove prototype photography from cards.
- [x] Remove PhotoSwipe/lightbox behavior from the active product cards.
- [x] Restore a polished SVG garment placeholder.
- [x] Use the same SVG-first approach for generic SmartImage surfaces.
- [x] Keep image/download tooling in the repository for future approved photography, but do not run it in Test 17 build.

### Card actions
- [x] Keep WhatsApp green with white uploaded logo.
- [x] Increase WhatsApp mark from 22px to 27px on cards.
- [x] Increase primary WhatsApp mark to 28px in sheets/wizard.
- [x] Keep neutral favorite container and red heart-only selected state.
- [x] Preserve gentle heartbeat and reduced-motion behavior.

## P1 — Production backend
- [ ] Laravel 13 + SQLite development backend.
- [ ] Inertia + Vue 3 + TypeScript + Tailwind customer frontend.
- [ ] Filament 5 admin.
- [ ] Spatie Media Library + Filament integration for real approved photography later.
- [ ] Google Socialite.
- [ ] Secure magic links, revoke/expiry/scope/audit.
- [ ] Auditable impersonation.
- [ ] MySQL staging after SQLite-backed behavior stabilizes.

## Definition of Done — Test 17
1. Tests 01–16 remain immutable.
2. Test 17 is first in the launcher.
3. Mobile header contains only brand, theme button and hamburger.
4. Mobile drawer contains navigation, language and account actions.
5. Desktop navigation is centered and link-like, not pill-button navigation.
6. Theme control is one icon-only cycle button at every breakpoint.
7. Login is centered with a dark blurred backdrop and keyboard-safe focus handling.
8. Product cards show SVG placeholders and no runtime product photos.
9. WhatsApp logo is white and ~20% larger than Test 16.
10. Type-check, unit tests, regression contracts, build, FTP smoke and deploy all pass without touching root or deleting remote files.
