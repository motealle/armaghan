# Test 25 — Gray Dark Theme + Reversible Portrait Placeholder Pipeline

Test 25 is explicitly reversible. Test 24 remains immutable, the pre-change source is pinned at branch `rollback/test24-pre-test25`, original landscape placeholder media is never overwritten, and portrait media is reproducibly generated from those originals at build time.

Each item below was considered against five implementation paths. Scores are /10 and combine visual quality, accessibility, responsive behavior, reversibility, maintenance cost, and avoidance of common ecommerce/media mistakes.

## 1) Dark mode

| Rank | Option | Score | Why / risk |
|---:|---|---:|---|
| 1 | Neutral graphite gray surfaces; blue only for brand/active states | **9.8** | Quiet image-first workspace, clear layers, preserves Armaghan blue navbar |
| 2 | Neutral gray with no blue except navbar | 9.1 | Very clean but underuses brand cues |
| 3 | Reduce saturation of current navy | 7.7 | Smaller change but keeps the root problem |
| 4 | Pure OLED black | 6.8 | Harsh borders, tiring contrast for catalog browsing |
| 5 | Keep Test 24 dark theme | 3.2 | Too blue/black and visually heavy |

**Selected:** 1.

## 2) Horizontal placeholders inside portrait product cards

| Rank | Option | Score | Why / risk |
|---:|---|---:|---|
| 1 | Build-time portrait derivative: keep source centered, extend top/bottom from sampled edge colors, add very subtle vignette | **9.9** | No stretch/crop, deterministic, fast at runtime, reversible |
| 2 | Runtime contain + blurred duplicate backdrop | 8.5 | Safe fallback but still looks like a workaround |
| 3 | Solid neutral letterbox | 7.8 | Clean but produces obvious dead bands |
| 4 | Focal crop | 6.0 | Can remove meaningful garment edges |
| 5 | Stretch to portrait | 0.5 | Distorts product representation |

**Selected:** 1. Python/Pillow generates 960×1440 WebP derivatives during build. Originals remain untouched.

## 3) Keep landscape and portrait variants selectable

| Rank | Option | Score | Why / risk |
|---:|---|---:|---|
| 1 | Admin setting: Portrait / Landscape / Auto; portrait preferred by default | **9.8** | Reversible, future-proof, easy comparison |
| 2 | Portrait only | 7.8 | Good cards but removes useful original presentation |
| 3 | Landscape only | 6.4 | Keeps current problem |
| 4 | Viewport-only automatic selection | 6.1 | Less admin control and harder QA |
| 5 | Hardcode in source | 3.2 | Not operationally maintainable |

**Selected:** 1.

## 4) Media/card boundary

| Rank | Option | Score | Why / risk |
|---:|---|---:|---|
| 1 | Portrait derivative + subtle inset separation + soft vignette | **9.7** | Separates media/body without adding text or heavy borders |
| 2 | Hard divider line | 8.0 | Clear but mechanical |
| 3 | Strong gradient | 6.8 | Can contaminate product colors |
| 4 | Shadow around the whole image | 6.3 | Adds visual noise |
| 5 | No separation | 4.2 | Current card feels unfinished |

**Selected:** 1.

## 5) Category numbers 01/02/03

| Rank | Option | Score | Why / risk |
|---:|---|---:|---|
| 1 | Quiet neutral micro-badge integrated with card footer | **9.5** | Keeps useful indexing without competing with category name |
| 2 | Small outlined square | 8.5 | Structured but slightly technical |
| 3 | Remove numbers | 7.6 | Minimal but loses a useful visual anchor |
| 4 | Keep yellow/gold badge | 5.0 | Pulls attention away from category imagery |
| 5 | Larger/brighter numbers | 2.8 | Makes the existing problem worse |

**Selected:** 1.

## 6) Production-request category cards in English

| Rank | Option | Score | Why / risk |
|---:|---|---:|---|
| 1 | Dedicated compact category-card structure with number, title and bounded subtitle | **9.6** | Prevents awkward line breaks and keeps three choices scannable |
| 2 | Increase card width only | 7.7 | Helps desktop, not narrow mobile |
| 3 | Remove subtitles | 7.2 | Cleaner but loses guidance |
| 4 | Dropdown | 6.5 | Compact but slower to compare |
| 5 | Keep current generic wizard-option | 4.0 | Produces cramped mixed alignment |

**Selected:** 1.

## 7) Production-request reset/back hierarchy

| Rank | Option | Score | Why / risk |
|---:|---|---:|---|
| 1 | Small secondary reset in header + locale-aware back arrow in footer | **9.7** | Matches action importance and fixes English arrow direction |
| 2 | Text-only controls | 8.0 | Fine but less scannable |
| 3 | Icon-only both controls | 7.2 | Minimal but less discoverable |
| 4 | Current prominent reset/back | 4.7 | Too much emphasis and wrong LTR direction |
| 5 | Browser back only | 3.9 | Breaks wizard mental model |

**Selected:** 1.

## 8) English footer punctuation/direction

| Rank | Option | Score | Why / risk |
|---:|---|---:|---|
| 1 | Explicit locale-aware direction/isolation for footer text | **9.8** | Fixes punctuation ordering without modifying copy |
| 2 | Rewrite strings with punctuation hacks | 5.8 | Fragile across languages |
| 3 | Force LTR globally | 4.9 | Breaks Persian/Arabic/Kurdish |
| 4 | Remove punctuation | 4.2 | Hides symptom |
| 5 | Keep current behavior | 2.5 | Visible defect remains |

**Selected:** 1.

## 9) Home trust/capability cards in English

| Rank | Option | Score | Why / risk |
|---:|---|---:|---|
| 1 | Language-aware title/icon row plus tighter description rhythm | **9.4** | Better scan and consistent LTR/RTL alignment |
| 2 | Icon above title | 8.0 | Clean but increases card height |
| 3 | Reduce padding only | 6.6 | Does not fix hierarchy |
| 4 | Remove icons | 6.1 | Less visual structure |
| 5 | Keep current layout | 4.5 | Looks loose and uneven |

**Selected:** 1.

## 10) Reversibility / rollback

| Rank | Option | Score | Why / risk |
|---:|---|---:|---|
| 1 | Immutable Test 24 + rollback branch + isolated Test 25 state + generated derivatives | **10.0** | One ref move can restore source; old deployed snapshot remains untouched |
| 2 | Git revert only | 8.3 | Good but less explicit operational checkpoint |
| 3 | Feature flag inside Test 24 | 5.1 | Violates immutable snapshot policy |
| 4 | Overwrite current assets in place | 2.0 | Loses originals and makes comparison difficult |
| 5 | Edit deployed Test 24 directly | 0.0 | Violates project release rules |

**Selected:** 1.

## Additional defects from the supplied screenshots

- Dark card/body/background layers had insufficient tonal separation.
- Horizontal placeholder media created large dead zones in tall card frames.
- The white media block and dark card body had an abrupt unstyled seam.
- English Back arrow pointed in the wrong semantic direction.
- Start-over copy wrapped at a narrow width and appeared more important than the wizard task.
- English category subtitles were squeezed into an unsuitable generic flex layout.
- Footer punctuation could render visually on the wrong side in mixed-direction contexts.
- Capability cards needed explicit English LTR alignment and tighter vertical rhythm.

All selected fixes are implemented in Test 25 without modifying Test 24.

## Live delivery

- Rollback source checkpoint: `rollback/test24-pre-test25` at `2f10bacfbd33219cf036f546cf91b6a9bfb76916`.
- Implementation: `370635cf6167fcae2212f07ef665516e7e50dd04`.
- Final deployed source: `4a41f486b08cdceb56711fc06435c170f24a16bf`.
- GitHub Actions **FTP Deploy Run #86** completed successfully.
- All Test 11–25 contracts, immutable guard, Python syntax, TypeScript type-check, unit tests and Vite build passed.
- CI generated 18 portrait WebP placeholder derivatives under `images/placeholders-portrait`; the original landscape WebP/AVIF files were not overwritten.
- FTP smoke test passed.
- `/public_html/t/25/index.html`, the portrait derivative set and the mutable launcher were uploaded.
- `deploy-root` was skipped.
- Deployment completed with **104 files uploaded and no remote deletions**.

