# Test 27 — Visual Style Editor

Status: Test 27 is live and remains the active mutable UI review lane. Production Laravel/SQLite and Style Profile service semantics are live/verified; only the real browser Filament session + CSRF + reload/cross-device/Publish/Restore acceptance remains. Test 26 remains immutable.

## Product goal

Give an administrator a mobile-first WYSIWYG-style visual editor that edits the real page without turning Armaghan into a free-form page builder. The editor itself is a removable plugin layer; saved style/content overrides remain active after the editor UI is switched off.

## Approved base palette

The current authoritative five-color palette is:

| Token | Value | Intended baseline role |
|---|---|---|
| Brand Green | `#21946A` | action / secondary brand |
| Brand Blue / logo-background blue | `#0714C2` | primary brand / brand chrome |
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
- is persisted by Laravel as a versioned Style Profile when a real authenticated backend admin session is active.

## Deliberate non-goals for this foundation

- no arbitrary CSS textarea;
- no DOM reordering;
- no drag/drop page builder;
- no raw HTML editing;
- no unrestricted font/spacing/radius knobs;
- no authentication bypass or anonymous server write;
- no Test 26 mutation;
- no free-form page-builder features outside the registered target/token model.

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

Current production integration state:

Production Laravel/SQLite is live at `/backend`, the first real active administrator exists, and the Style Profile service has passed live transactional save/publish/restore acceptance against production SQLite. The only remaining acceptance layer is the actual browser session path: Filament login, CSRF-protected Test 27 autosave, reload/cross-device verification, staging Publish and Restore through the UI.

Validation:

- Test 27 source contract: PASS.
- TypeScript: PASS.
- Vue unit tests: **30/30 PASS**.
- Numbered Test 27 Vite build: PASS.


### Adapter staged deployment

- Main implementation head: `fb6f8e92406c7c805edb88d11800ca4f50f2150f`.
- **FTP Deploy Run #262: PASS**.
- Full Test 27 contract/TypeScript/unit/build QA: PASS.
- FTP smoke: PASS.
- `deploy-t`: PASS; Test 27 uploaded to `/public_html/t/27`.
- 112 files uploaded; no remote files deleted.
- `deploy-root`: skipped.
- Production Laravel and the real admin account are now available. The remaining check is to verify the live browser session/CSRF cycle end-to-end; local browser fallback remains available if the authenticated backend session is absent.


## Logo-blue / Home color customer request

Customer-requested color semantics are now live in Test 27:

- Repository-logo pixel probe selected `#0714C2` as the dominant logo-background blue.
- Canonical Brand Blue now resolves to `#0714C2`; the previous `#151EDA` canonical value is retired.
- Header / Footer / Hero brand chrome continue to resolve through `--role-brand-chrome -> --brand-blue`.
- Home page light-mode default is Brand White through `--role-page-background`.
- Primary Home panel default is Brand Mint through `--role-panel-background`.
- The Home page root can change background color but cannot be hidden.
- Why-item and Capability title/body text targets are independently editable per locale.
- Hidden dynamic targets remain recoverable from the structured browser.

### Saveability hardening

- API base defaults to same-origin `/backend`.
- Mutating Style Profile requests bootstrap a Laravel CSRF token and retry once on HTTP 419.
- Browser-local autosave remains active independently of backend availability.
- Sanitized JSON export/import provides a simple manual portable backup without creating a JSON runtime database.
- Real shared persistence remains gated by real Laravel admin authentication.

### Validation / deployment

- Latest dedicated branch CI: PASS.
- Backend CI Run #19: PASS.
- FTP Deploy Run #268: PASS.
- 112 Test 27 files uploaded; no remote deletes; root deploy skipped.


## Production persistence acceptance

The server-side persistence path has now been verified against the live production SQLite database.

Production Style Profile Acceptance Run `36878931948`: **PASS**.

The test:
- used the already deployed Laravel application and real production SQLite connection;
- required exactly one active administrator and verified the active-admin policy;
- saved a temporary structured draft;
- published staging twice with different temporary token values;
- restored the first temporary version as a new immutable version;
- verified the staging publication pointer;
- verified ActivityLog writes;
- then rolled back the entire outer transaction.

Observed inside the transaction:
- 3 new immutable versions;
- 3 new activity records.

After rollback:
- profile/version/publication/activity logical state exactly matched the state before the test;
- the short-lived public helper was removed.

This proves production persistence/version/restore semantics without bypassing browser authentication. The only remaining acceptance layer is the real browser session/CSRF path.


## Current authoritative state — 2026-10-01

- Test 27 is live and visible in the mutable launcher.
- Canonical Brand Blue is logo-background blue `#0714C2`; `#151EDA` is retired.
- Home light background defaults to Brand White.
- Primary Home panels default to Brand Mint.
- Production Laravel 13.34.0 + SQLite is live at `/backend`.
- One real active production administrator exists.
- Production Style Profile service semantics passed live transactional acceptance (Run `36878931948`) with complete rollback.
- Remaining editor persistence acceptance is browser-only: real Filament session + CSRF, edit/save, reload/cross-device, staging Publish and Restore.
- Canonical cross-project current status: `docs/CURRENT-STATUS.md`.


## Customer visual-correction batch — 2026-10-01

### Ranked implementation strategies

| Rank | Method | Score | Why |
|---:|---|---:|---|
| 1 | **Semantic design roles + centralized customer product-label helper + persisted Feature Flag** | **10.0** | Keeps content, theme and behavior separate; preserves domain data; remains editor-compatible and rollback-safe |
| 2 | CSS overrides plus inline template conditions | 7.8 | Fast, but duplicates presentation logic and is harder to test |
| 3 | Delete/replace `Product.name` in catalog data | 5.4 | Makes the UI look right but damages admin/backend data ownership and future migration |
| 4 | Scatter hard-coded colors and `v-if` checks across components | 3.2 | High regression risk and conflicts with the token/editor architecture |
| 5 | Fork a second customer-only theme/page | 2.1 | Creates parallel UI paths and long-term maintenance cost |

**Selected:** option 1.

Implementation contract:
- Footer brand block is logo-only; no brand name or descriptive sentence is rendered beside it.
- Available products display their localized six-way subcategory label; unavailable or made-to-order products display one localized unified unavailable/producible label.
- Underlying `Product.name` data remains intact for admin/backend compatibility; only customer presentation changes.
- Product code uses a Brand Blue capsule with Brand White text and a restrained Brand Gold border.
- Subcategory codes 11/12/21/22/31/32 are controlled by `showSubcategoryCodes`; customer default is OFF for mobile/tablet/desktop.
- Appearance schema v5 preserves schema-v4 choices and only introduces the new subcategory-code default.
- Products page light background defaults to Brand White; product cards default to Brand Mint. Both remain structured editor surfaces.
- Light-surface headings use an accessibility-safe dark-green role derived primarily from Brand Green plus the current neutral text role, keeping the result visibly green on White/Mint. Dark mode keeps Brand Green directly.
- Why Armaghan separators use a 3px Brand Blue rule; numbered circles use Brand Blue with Brand Gold numbers.
- Image-overlay and Brand Blue footer/hero headings keep their high-contrast light treatment rather than forcing green where it would reduce legibility.

Accessibility basis: normal text retains the 4.5:1 target. The product-code capsule and unavailable badge use Brand Blue pairings instead of placing white/gold directly on Mint.


### Customer visual-correction delivery

- Rollback checkpoint: `rollback/test27-pre-customer-ui-corrections` → `40dc89b5aa97bd09fb664b88b1fb8dc9d54c15d9`.
- Main implementation: `972d66bcd6e0e3ab72946d2fd14ffe8a6d329052`.
- A stale Test 23 source assertion blocked FTP Deploy #275 before any build/deploy; the historical contract was corrected without changing the frozen Test 23 snapshot.
- Final validated release marker: `65b9f1158019aece79438fbc680af4070e0c0665`.
- **FTP Deploy #277 PASS**: Test 11–27 contracts, type-check, unit tests, Test 27 build and FTP smoke passed; 112 files uploaded; no remote files deleted; root deployment skipped.
