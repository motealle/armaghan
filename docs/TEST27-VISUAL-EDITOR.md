# Test 27 — Visual Style Editor

Status: foundation implementation in progress on `ui/test27-visual-editor-foundation-20261001`. Test 26 remains immutable.

## Product goal

Give an administrator a mobile-first WYSIWYG-style visual editor that edits the real page without turning Armaghan into a free-form page builder. The editor itself is a removable plugin layer; saved style/content overrides remain active after the editor UI is switched off.

## Approved base palette

The authoritative five-color palette supplied by the owner is:

| Token | Value | Intended baseline role |
|---|---|---|
| Brand Green | `#21946A` | action / secondary brand |
| Brand Blue | `#151EDA` | primary brand / brand chrome |
| Brand Mint | `#C8E3DB` | soft surfaces |
| Brand White | `#FFFFFF` | light surfaces |
| Brand Gold | `#FFB514` | accent / selection |

Test 27 introduces stable CSS variables `--brand-green`, `--brand-blue`, `--brand-mint`, `--brand-white`, `--brand-gold` and semantic `--role-brand-chrome`.

Header, footer and the dark/navy hero text bar use `--role-brand-chrome` rather than unrelated hard-coded navy values.

## Inspector placement — ranked analysis

| Rank | Pattern | Score | Why |
|---:|---|---:|---|
| 1 | **Non-modal, resizable bottom sheet on mobile; same inspector may become a compact side/floating pane on larger screens** | **10.0** | Stable touch target, room for text/color controls, page remains visible and scrollable, predictable safe area, low accidental movement |
| 2 | Bottom sheet plus a small floating launcher only | 9.2 | Good shortcut without making the floating control the editor itself |
| 3 | Fully draggable floating inspector | 7.1 | Useful on desktop, but occludes mobile content and creates small/moving touch targets |
| 4 | Element-local popover | 5.8 | Often clipped/covered near screen edges and unreliable when targets are dense |
| 5 | Inline-only editing directly in the DOM | 4.2 | Looks direct, but is fragile for touch, RTL/LTR, translated content and persistence |

**Selected:** option 1.

Google's standard bottom-sheet guidance explicitly describes a sheet that coexists with the main UI so both regions can remain visible/interactable. The Test 27 implementation follows that supporting-pane model rather than a modal sheet that blocks the page.

Reference:
- https://developer.android.com/reference/kotlin/androidx/compose/material/BottomSheetScaffold
- https://developer.android.com/design/ui/mobile/guides/layout-and-content/common-layouts

## Dense-touch target selection — ranked analysis

| Rank | Method | Score | Why |
|---:|---|---:|---|
| 1 | **Sample the finger-contact neighborhood with `document.elementsFromPoint()`, collect stable editable ancestors, then ask the administrator which candidate was intended** | **10.0** | Solves nested/overlapping/nearby targets without guessing and works with actual rendered geometry |
| 2 | Cycle through parent/child targets after repeated taps | 7.9 | Fewer controls but discoverability is poor and repeated tapping is error-prone |
| 3 | Force all editable targets to large temporary overlays | 7.0 | Clear but visually noisy and can hide the design being edited |
| 4 | Long-press context menu | 6.4 | Familiar on touch but conflicts with native selection/context behavior |
| 5 | Always accept the topmost DOM target | 3.0 | Fails exactly when adjacent/nested elements are hard to select |

**Selected:** option 1.

Implementation samples the exact point plus a small radius derived from the pointer contact size. It deduplicates stable `data-style-id` targets and returns at most eight choices. The chooser uses large touch controls.

References:
- https://developer.mozilla.org/en-US/docs/Web/API/Document/elementsFromPoint
- https://developer.mozilla.org/en-US/docs/Web/API/Pointer_events
- https://developer.mozilla.org/en-US/docs/Web/Accessibility/Guides/Understanding_WCAG/Operable

## Persistence model

The editor never stores arbitrary raw CSS entered by the user.

It stores a structured profile:

- per-element text color token;
- per-element background token;
- per-element border/divider token;
- hide/show state;
- locale-specific text override for explicitly text-editable elements.

The profile additionally stores compiled CSS generated from those safe values. Token IDs are validated against the approved five-color registry before CSS generation.

Text is not encoded in CSS; text overrides are stored separately, per locale, because content and presentation are different concerns.

Prototype keys:
- `armaghan:test27:visual-style-profile:v1`
- `armaghan:test27:visual-editor-enabled`

The editor enabled state is session-scoped; the saved profile is persistent. Turning the editor off removes the inspector/event handlers but keeps the applied profile.

## Frozen Test 26 isolation

Web Storage is scoped by origin, not URL path. Therefore Test 27 must not keep writing Test 26 browser-state keys.

All mutable frontend store keys in the current Test 27 source are moved from `armaghan:test26:*` to `armaghan:test27:*`. This ensures using Test 27 cannot alter Test 26 state when both are served from the same host.

Reference:
- https://developer.mozilla.org/en-US/docs/Web/API/Web_Storage_API/Using_the_Web_Storage_API

## Initial editable targets

Foundation targets are intentionally limited and stable:

- `header.shell`
- `header.brand`
- `header.brand-name` — text editable
- `header.manufacturer` — text editable
- `hero.shell`
- `hero.media`
- `hero.caption`
- `hero.title` — text editable
- `product.card`
- `product.media`
- `product.title`
- `product.code`
- `product.actions`
- `footer.shell`
- `footer.brand`
- `footer.brand-name` — text editable
- `footer.description` — text editable
- `footer.columns`

The registry expands in later bounded batches. Product/catalog text is not made free-form here because canonical product names belong to product/content data, not a style profile.

## Mobile behavior

- Default inspector height: approximately 48% of the viewport.
- Drag handle resizes between a safe minimum and about 78% of the viewport.
- No modal backdrop; the page above remains scrollable/selectable.
- Bottom navigation is temporarily hidden only while the admin visual editor is active so the editing pane owns the bottom safe area.
- Inspector controls use touch-friendly dimensions; the close button is 44×44 CSS pixels.
- Selecting a page target prevents its normal click action while editor mode is active.
- Scrolling remains possible because page pointer-down is not globally disabled.
- Selected targets receive a gold outline.
- Hidden targets remain recoverable from an explicit "hidden elements" list.

## Plugin boundary

Editor ON:
- selection listeners active;
- target outline active;
- bottom inspector visible.

Editor OFF:
- inspector removed;
- selection listeners become inert;
- outline removed;
- customer-facing page behavior returns to normal.

Saved style profile:
- remains applied through `VisualStyleRuntime.vue`;
- is independent from editor visibility;
- can later be persisted by Laravel as a versioned site-style profile.

## Deliberate non-goals for this foundation

- no arbitrary CSS textarea;
- no DOM reordering;
- no drag/drop page builder;
- no raw HTML editing;
- no unrestricted font/spacing/radius knobs;
- no production backend persistence yet;
- no Test 26 mutation;
- no launcher promotion until Test 27 is explicitly reviewed.
