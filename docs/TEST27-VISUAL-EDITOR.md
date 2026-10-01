# Test 27 — Visual Style Editor

Status: foundation + modularization/contrast batch validated on `ui/test27-visual-editor-foundation-20261001`. Test 26 remains immutable.

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

## Modular implementation layout

The visual editor is split by responsibility:

- `VisualEditor.vue`: composition-only shell and editor lifecycle surface.
- `VisualEditorTargetChooser.vue`: ambiguous-touch and hidden-target selection UI.
- `VisualEditorInspector.vue`: text/style controls and contrast feedback.
- `composables/useVisualEditorSelection.ts`: pointer listeners, selection, candidate lifecycle and selected-target marking.
- `composables/useResizableEditorSheet.ts`: bottom-sheet height, drag handle and document frame state.
- `selection.ts`: rendered-geometry candidate discovery.
- `contrast.ts`: WCAG contrast math and approved-token checks.
- `store.ts`: validated persistence + generated CSS.
- `VisualStyleRuntime.vue`: applies the saved profile independently of editor visibility.

This separation keeps the plugin removable, testable and maintainable as target coverage grows.

## Contrast guard

When both text and background are explicitly assigned from the approved token palette, the inspector enforces a minimum **4.5:1** normal-text contrast ratio. Token combinations that would fall below the threshold are disabled instead of silently creating unreadable text.

The implementation deliberately uses the stricter normal-text threshold for all editor text assignments because the editor does not yet classify target font size/weight as large text.

Reference:
- https://www.w3.org/WAI/WCAG22/Understanding/contrast-minimum.html

## Expanded editable target coverage

The foundation now covers:
- Header/brand text;
- Hero shell/media/caption/title;
- About section shell/heading/title/body/media;
- Why section shell/heading/intro/list and individual reason cards;
- Capabilities shell/heading/grid and individual capability cards/body surfaces;
- Product-banner section/heading and individual banner/copy surfaces;
- Product-card shell/media/title/code/actions;
- Footer shell/brand/brand text/columns;
- Home page/recommended-products section surfaces.

Canonical product/customer data remains owned by its domain editor rather than being overridden as visual free-form text.

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

## Staged validation result

- Rollback checkpoint: `rollback/test26-pre-test27-visual-editor`.
- Temporary branch-only validation workflow Run #4: **PASS**.
- Test 27 source contract: PASS.
- TypeScript type-check: PASS.
- Initial foundation Vue unit tests: **24/24 PASS**.
- Vite Test 27 build: PASS.
- The validation workflow had no FTP/deployment step.
- First failed validation exposed a stale Test 26 session-storage assertion and was corrected by moving the test to the isolated Test 27 namespace.
- Second failed validation was workflow-only: Pillow was absent from the temporary runner. Product code had already passed type-check/unit tests; the temporary workflow was corrected and the complete build then passed.


### Modularization / contrast validation

- Temporary branch-only refactor validation workflow: **PASS**.
- Test 27 source contract: PASS.
- TypeScript type-check: PASS.
- Vue unit tests: **25/25 PASS**, including dedicated contrast calculations.
- Vite Test 27 build: PASS.
- No FTP/deployment step was present in this branch validation.
- Selection listeners now attach only while editor mode is enabled and are removed when it is disabled/unmounted.
- Home editable-target coverage was expanded without changing Test 26 or domain-owned product/customer data.


## Main CI / staged deployment

- Main implementation head: `d9a6a68353345a34b0379feca77663a240e1c856`.
- GitHub Actions **FTP Deploy Run #254: PASS**.
- Historical Test 11–26 contracts: PASS.
- Test 27 visual-editor contract: PASS.
- TypeScript + Vue unit tests: PASS (**25/25**).
- Vite Test 27 build: PASS.
- FTP smoke: PASS.
- `deploy-t`: PASS; generated Test 27 uploaded to `/public_html/t/27`.
- `deploy-root`: skipped.
- No remote files were deleted.
- The deployment process re-uploaded mutable `/t/index.htm`, but its repository content still lists Test 26 first and contains no Test 27 launcher entry. Test 27 therefore remains staging-only pending review.


## Laravel/SQLite sync adapter

Test 27 now has a local-first Style Profile adapter.

Behavior:

- Browser editing remains immediate and durable in Test 27 local storage even when the Laravel API is absent.
- The public `staging` publication is fetched on page load, but is adopted only when there are no local edits or the current browser profile still matches the previously adopted server baseline.
- A real authenticated Laravel administrator session enables shared draft persistence.
- Draft changes debounce for 900ms before save.
- Every authenticated draft save sends the current `expected_checksum`.
- HTTP 409 is treated as a true concurrency conflict. The editor stops autosaving and asks the administrator to choose either the server version or explicitly replace it with the current device version.
- Server 401/403 never causes a UI failure or an authentication bypass; the editor returns to local-only mode.
- Staging Publish and the latest ten immutable versions are exposed in the editor sync panel.
- Restore calls the backend restore endpoint and therefore creates a new immutable version rather than rewriting history.
- API calls use same-origin credentials and support Laravel's `XSRF-TOKEN` cookie when present.
- `VITE_ARMAGHAN_API_BASE` can override the API origin later; the default is same-origin.

Current staged limitation:

The customer-facing Test 27 static bundle is live, but the production Laravel application / real admin session is not yet activated on the host. Therefore the current live Test 27 correctly shows local-only persistence until that backend deployment batch is completed.

Validation:

- Test 27 source contract: PASS.
- TypeScript: PASS.
- Vue unit tests: **30/30 PASS**.
- Numbered Test 27 Vite build: PASS.
