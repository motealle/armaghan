# Test 25 Hotfix 2 — Screenshot Bug Audit

Rollback branch before this hotfix: `rollback/test25-pre-bugfix-2` → `6dc0b9188d50cae156c19fac86f548ed3e3d16f0`.

This hotfix is deliberately narrow: it fixes the supplied screenshot defects without changing product data, placeholder originals, navigation routes, or the Test 24 snapshot.

## 1) Literal `\n` appearing above image containers

Root cause found in source: `SmartImage.vue` contained a literal backslash+n text node immediately after the opening `<picture>` tag. The same accidental escape sequence also existed between two CSS comments.

| Rank | Option | Score | Why / risk |
|---:|---|---:|---|
| 1 | Remove the literal escape from the shared SmartImage template and add a regression assertion | **10.0** | Fixes every affected media container at the source; lowest risk |
| 2 | Hide the text with CSS overflow/color rules | 4.8 | Masks the symptom and can reappear elsewhere |
| 3 | Remove the text in each caller | 3.9 | Duplication; root component remains broken |
| 4 | Add JavaScript DOM cleanup after render | 2.0 | Brittle and unnecessary runtime work |
| 5 | Ignore it as a browser artifact | 0.5 | It is a deterministic source bug |

**Selected:** Option 1. The literal text node and the stray CSS escape are removed; CI now checks that they do not return.

## 2) Finger swipe for Hero slides

| Rank | Option | Score | Why / risk |
|---:|---|---:|---|
| 1 | Pointer Events + horizontal threshold + vertical-intent guard + `touch-action: pan-y` | **9.8** | One implementation for touch/pen, preserves vertical page scrolling, no dependency |
| 2 | Touch Events only | 8.2 | Works on phones but duplicates pointer behavior and is less future-proof |
| 3 | Add a carousel dependency | 7.3 | Feature-rich but unnecessary bundle/maintenance cost |
| 4 | CSS scroll-snap rebuild | 7.0 | Elegant but requires a larger hero architecture change |
| 5 | Keep dots/autoplay only | 4.0 | Does not meet direct-manipulation expectation |

**Selected:** Option 1. A swipe of at least 44px, predominantly horizontal, changes the slide; vertical gestures keep scrolling the page. Autoplay restarts after interaction.

## 3) Low-contrast blue foregrounds in Dark mode

| Rank | Option | Score | Why / risk |
|---:|---|---:|---|
| 1 | Keep brand blue for navigation/filled states, replace low-contrast foreground-only blue with light neutral gray | **9.7** | Preserves brand identity while fixing legibility exactly where it fails |
| 2 | Make every blue lighter | 8.0 | Better contrast but creates a lavender-heavy dark theme |
| 3 | Remove blue everywhere in dark mode | 7.1 | Neutral, but loses branded navigation/selection anchors |
| 4 | Increase brightness of the entire dark palette | 5.8 | Treats the background instead of the foreground problem |
| 5 | Keep current blue | 3.0 | Screenshot shows insufficient contrast |

**Selected:** Option 1. Product codes, details icon, card secondary actions and similar content-only foreground accents become light neutral gray in dark mode. Navbar/primary filled actions retain the Armaghan blue.

## 4) Mixed RTL/LTR behavior in English content

| Rank | Option | Score | Why / risk |
|---:|---|---:|---|
| 1 | Make the entire main-content tree LTR in English, then keep only intentional exceptions | **9.9** | Correct inheritance model; prevents repeated one-off alignment bugs |
| 2 | Continue patching individual components | 6.8 | Endless exceptions and regression risk |
| 3 | Apply `text-align:left` only | 5.9 | Does not fix flex/grid logical direction |
| 4 | Reorder arrays/data per language | 4.5 | Mixes localization with domain ordering |
| 5 | Keep current hybrid rules | 2.8 | Proven to produce mixed layouts |

**Selected:** Option 1. Main content, commerce layout, header layout, bottom navigation and mobile drawer inherit LTR in English; centered footer remains intentionally centered.

## 5) Additional screenshot-level safeguard: avoid gesture and contrast regressions

| Rank | Option | Score | Why / risk |
|---:|---|---:|---|
| 1 | Add a focused source contract for the four fixes and keep all Test 11–25 regressions running | **9.6** | Fast gate, catches the exact failure modes before FTP |
| 2 | Rely only on TypeScript/Vitest | 7.2 | They do not necessarily inspect CSS/text-node policy |
| 3 | Manual screenshot review only | 6.4 | Useful but not deterministic |
| 4 | Browser E2E suite now | 6.1 | Valuable later, but larger setup than this hotfix warrants |
| 5 | No new regression gate | 3.1 | Easy recurrence |

**Selected:** Option 1. A new Test 25 hotfix contract is added to CI before the production build/deploy step.
