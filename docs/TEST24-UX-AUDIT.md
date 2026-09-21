# Test 24 — Responsive Commerce Polish + Admin Content Control

Test 24 responds to the supplied light/dark/mobile/English screenshots. Each requested change is evaluated against five implementation paths. Scores are out of 10 and combine clarity, accessibility, responsiveness, maintenance cost, brand consistency, and avoidance of common ecommerce/UI failures.

## 1) Product media: show the whole image without ugly empty bands

| Rank | Option | Score | Why / risk |
|---:|---|---:|---|
| 1 | Foreground `object-contain` + same-image blurred/toned backdrop + safe inset | **9.8** | Entire garment stays visible, background fills dead space, no destructive crop, works across ratios |
| 2 | Plain `object-contain` on solid media background | 8.8 | Preserves image but can create large empty bands |
| 3 | Smart focal-point `object-cover` per product | 8.1 | Strong visual fill but requires focal metadata and can still crop |
| 4 | Fixed source-specific aspect ratios | 7.3 | Good per asset, brittle across 500+ products |
| 5 | Keep `object-cover` and reduce zoom | 5.8 | Still loses corners/sleeves/garment edges |

**Selected:** Option 1. Product cards use a contained foreground image with a soft backdrop derived from the same local image. No new artwork is generated.

## 2) Dark mode inspired by Midjourney's neutral creative-workspace feel

| Rank | Option | Score | Why / risk |
|---:|---|---:|---|
| 1 | Neutral near-black layered surfaces + subtle brand-blue glow + keep Armaghan primary navbar | **9.7** | Premium, image-focused, lower chroma fatigue, preserves supplied brand tokens |
| 2 | Deep navy everywhere | 8.2 | Brand-aligned but visually heavy and currently feels overly blue |
| 3 | Pure black OLED theme | 7.8 | Strong contrast but harsh borders and less depth |
| 4 | Gray theme with blue navbar removed | 6.9 | Clean but violates the customer's brand anchor |
| 5 | Current dark palette | 5.9 | Too navy, surfaces blend together, weak hierarchy |

**Selected:** Option 1. `#151EDA` remains the primary/nav anchor; background/surfaces move toward charcoal with restrained borders, muted text and soft elevation.

## 3) Mobile hero text placement

| Rank | Option | Score | Why / risk |
|---:|---|---:|---|
| 1 | Structural split: image block followed by dedicated branded copy panel | **9.9** | No text can straddle the image/background seam, stable for all languages, strongest accessibility |
| 2 | Bottom gradient overlay entirely inside image | 8.8 | Familiar but long Persian/Arabic titles may cover product imagery |
| 3 | Floating glass card over lower image | 8.0 | Attractive but collision-prone on short screens |
| 4 | Center overlay | 6.8 | Covers hero subject and weakens scan |
| 5 | Keep absolute bottom positioning | 5.1 | Recreates the observed seam bug |

**Selected:** Option 1. Mobile gets a clean 16:9 image then copy/actions below; desktop becomes a true split hero rather than a stretched mobile layout.

## 4) Home-page main categories lost their images

| Rank | Option | Score | Why / risk |
|---:|---|---:|---|
| 1 | Reuse the same image-first category-card language as Products | **9.8** | Consistent recognition and behavior across entry points |
| 2 | Reuse images but different home-specific card style | 8.3 | Works but duplicates visual language |
| 3 | Restore old SmartImage cards | 7.4 | AVIF inference caused the current missing-image regression |
| 4 | Icons plus text | 6.2 | Fast but contradicts the user's image-led goal |
| 5 | Text-only | 4.8 | Reintroduces the original problem |

**Selected:** Option 1. Home and Products share the same visual category-card classes and the same user-provided optimized assets.

## 5) English typography and Product-page alignment

| Rank | Option | Score | Why / risk |
|---:|---|---:|---|
| 1 | Inter for English + reduced weight scale + true LTR layout containers | **9.8** | Professional commerce typography, fixes reversed card order/chip alignment, keeps hierarchy without 900-weight heaviness |
| 2 | Roboto Light with LTR fixes | 8.5 | Solid, but less refined than Inter for interface density |
| 3 | Manrope globally | 8.0 | Strong display voice, less neutral for dense admin/catalog UI |
| 4 | System font stack only | 7.5 | Fast and robust but inconsistent across OSes |
| 5 | Keep Roboto 900-heavy styling | 5.3 | Matches the observed chunky typography problem |

**Selected:** Option 1. English body stays readable at 400; bold utilities are visually reduced to 600–700. Category and filter rows become physically LTR in English.

## 6) Desktop / laptop must stop looking like an enlarged mobile app

| Rank | Option | Score | Why / risk |
|---:|---|---:|---|
| 1 | Responsive composition shift: split hero, horizontal category cards, editorial home trust layout, wider desktop rhythm | **9.7** | Uses available width meaningfully while preserving mobile information architecture |
| 2 | Increase max-width/padding only | 7.4 | More breathing room but still mobile-shaped |
| 3 | Separate desktop-only components | 7.2 | Maximum freedom but duplicate logic and maintenance |
| 4 | Dense dashboard-like desktop | 6.7 | Efficient but wrong tone for public commerce |
| 5 | Keep current responsive scaling | 5.5 | Main complaint remains |

**Selected:** Option 1. Same components adapt structurally at `lg`: desktop hero is split, category cards become horizontal, home intro becomes media + editorial trust column, and spacing/type scale changes.

## 7) Home manufacturer/capabilities/documents/sales copy editable from Admin

| Rank | Option | Score | Why / risk |
|---:|---|---:|---|
| 1 | Dedicated Home Content admin tab backed by existing per-locale override store | **9.9** | Discoverable, reuses current i18n persistence, no duplicate CMS model |
| 2 | Use generic Languages tab only | 8.0 | Already technically possible but hard to discover for nontechnical admin |
| 3 | New separate content Pinia store | 7.2 | Clear domain but duplicates translation persistence |
| 4 | Hardcoded JSON editor | 5.8 | Error-prone and poor UX |
| 5 | Keep code-only content | 3.5 | Fails requirement |

**Selected:** Option 1. Dedicated tab edits manufacturer label, intro title, capabilities title/text, documents title/text and sales title/text for each language.

## 8) Manual customer creation in Admin

| Rank | Option | Score | Why / risk |
|---:|---|---:|---|
| 1 | Keep quick-add form, allow email OR mobile, then open full Customer 360 immediately | **9.8** | Fast entry plus immediate completion of detailed profile; fixes existing email-only restriction |
| 2 | Full modal with all customer fields | 8.6 | Complete but slower for sales/admin entry |
| 3 | Inline full table row | 7.0 | Dense and error-prone on mobile/tablet |
| 4 | CSV-only creation | 5.7 | Useful for bulk, wrong for one customer |
| 5 | Existing required-email form unchanged | 5.2 | Manual add exists but violates new identity rule |

**Selected:** Option 1. A name plus at least one contact identifier is required; the newly created record opens in Customer 360.

## 9) Customer username must be mobile number or email

| Rank | Option | Score | Why / risk |
|---:|---|---:|---|
| 1 | Normalize login input and match either email or normalized phone/WhatsApp | **9.8** | Natural for customers, no separate username field, supports international formatting |
| 2 | Email-only | 8.0 | Simple but excludes phone-first customers |
| 3 | Phone-only | 7.3 | Common regionally but excludes email-first B2B buyers |
| 4 | New generated username | 5.9 | Extra credential to remember |
| 5 | Name as username | 3.8 | Non-unique and unsafe |

**Selected:** Option 1. Prototype authentication accepts either stored email or normalized mobile/WhatsApp when a password is set.

## 10) Google Sign-In activation path

| Rank | Option | Score | Why / risk |
|---:|---|---:|---|
| 1 | Laravel Socialite server-side authorization-code flow | **9.9** | Official Laravel path, keeps secret server-side, state/session validation, clean account linking |
| 2 | Google Identity frontend credential then backend verification | 8.8 | Valid architecture but adds a second auth pattern |
| 3 | Firebase Authentication | 7.6 | Good service, but introduces a new identity platform outside current Laravel target |
| 4 | OAuth directly from Vue with client secret | 1.0 | **Never**: secret exposure and wrong trust boundary |
| 5 | Fake prototype success | 0.0 | **Never**: creates false authentication/security |

**Selected:** Option 1. Repository documentation is expanded with exact Google Cloud + Socialite setup; live activation remains blocked only on the server backend/client credentials, not faked in static Vue.

## Common mistakes explicitly prevented

- No crop-first media for apparel where silhouette/edges matter.
- No text crossing hero image/content boundaries.
- No dark palette built from multiple saturated blues.
- No LTR language rendered inside RTL structural containers.
- No 900-weight English everywhere.
- No separate CMS state when the translation override layer already exists.
- No customer identity based on display name.
- No OAuth secret in the browser bundle or repository.
