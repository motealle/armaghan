# Armaghan B2B Catalog — UX/UI Backlog

Priorities: P0 = must-have for current prototype milestone, P1 = next comparison/validation, P2 = later refinement.

## Epic A — Prototype foundation

- [x] P0 Establish `/t` archive structure.
- [x] P0 Define protected root vs `/t` deployment boundary.
- [x] P0 Record project handoff and rules.
- [x] P0 Set dark/light visual direction.
- [ ] P0 Build `/t/index.htm` launcher.
- [ ] P0 Build `/t/01/index.htm`.
- [ ] P0 Build `/t/01/assets/app.css`.
- [ ] P0 Build `/t/01/assets/app.js`.
- [ ] P0 Verify relative paths from `/t`.

## Epic B — Test 01 Unified Request Builder

### B1 Shell and navigation
- [ ] P0 RTL app shell.
- [ ] P0 Responsive header.
- [ ] P0 Mobile bottom navigation.
- [ ] P0 Theme toggle: Dark / Light.
- [ ] P0 Sticky inquiry summary CTA.

### B2 Homepage
- [ ] P0 B2B/export hero.
- [ ] P0 Product CTA.
- [ ] P0 Inquiry CTA.
- [ ] P0 Three category cards.
- [ ] P0 Trust/value cards.
- [ ] P0 Product grid.
- [ ] P0 Services cards.
- [ ] P0 Footer/contact area.

### B3 Product experience
- [ ] P0 Eight mock products: 3 baby, 3 kids, 2 women.
- [ ] P0 Available/unavailable examples.
- [ ] P0 Product image mini-switcher.
- [ ] P0 Product details bottom sheet/modal.
- [ ] P0 Fixed specs with lock icon.
- [ ] P0 Variable specs with editable controls.
- [ ] P0 Availability-aware request types.

### B4 Request builder
- [ ] P0 Central inquiry state.
- [ ] P0 Six request types.
- [ ] P0 Product-context entry.
- [ ] P0 Service-context entry.
- [ ] P0 Quantity/options step.
- [ ] P0 Review step.
- [ ] P0 Inquiry list/summary.
- [ ] P0 Remove/edit inquiry items.

### B5 WhatsApp
- [ ] P0 Generate structured Persian message.
- [ ] P0 Show preview modal.
- [ ] P0 Encode message safely.
- [ ] P0 Open WhatsApp with placeholder number.
- [ ] P0 Include product/link placeholders.

## Epic C — Visual taste testing

- [ ] P0 Dark theme: Midjourney-inspired visual-first direction.
- [ ] P0 Light theme: Digikala-inspired practical RTL commerce direction.
- [ ] P1 Compare density and hierarchy between themes.
- [ ] P1 Tune cards, sheets and navigation from customer feedback.
- [ ] P1 Test mobile-first interaction speed.

## Epic D — Test variants

- [ ] P1 Test 02 — Product Card Bottom Sheet Focus.
- [ ] P1 Test 03 — RFQ Cart / سبد استعلام.
- [ ] P1 Test 04 — Services Hub.
- [ ] P1 Test 05 — Customer Profile Model.
- [ ] P1 Test 06 — WhatsApp-first Quick Actions.
- [ ] P1 Test 07 — Classic Catalog.
- [ ] P1 Add every completed test to launcher at the top.

## Epic E — Customer validation

- [ ] P1 Run the 10 customer testing questions against Test 01.
- [ ] P1 Record qualitative feedback in `docs/CUSTOMER-FEEDBACK.md`.
- [ ] P1 Record chosen model and reasons.
- [ ] P1 Decide whether to proceed with Test 01 or another variant.

## Epic F — Prototype quality

- [ ] P1 Keyboard/focus pass.
- [ ] P1 RTL visual pass.
- [ ] P1 Mobile safe-area pass.
- [ ] P1 Desktop pass.
- [ ] P1 WhatsApp encoding edge cases.
- [ ] P1 LocalStorage persistence test.
- [ ] P1 No-absolute-path audit.
- [ ] P1 No-backend/no-network-dependency audit.

## Epic G — Production mapping (future, not now)

- [ ] P2 Map approved UX to Laravel/Blade/Livewire/Alpine.
- [ ] P2 Define product/category schema.
- [ ] P2 Define inquiry schema.
- [ ] P2 Define customer/order/document lifecycle.
- [ ] P2 Define media/PDF storage.
- [ ] P2 Define authentication timing.
- [ ] P2 Convert prototype states into production components.

## Definition of Done — Test 01

Test 01 is done when all P0 items in Epics A and B are complete, the prototype is usable in Persian RTL on mobile and desktop, all six request paths behave correctly, WhatsApp preview/navigation works, the launcher is updated, and the prototype can be deployed under `/public_html/t` without touching root `/public_html`.
