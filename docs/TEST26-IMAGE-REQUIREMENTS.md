# Test 26 — Image Requirements and Placeholder Contract

Status: **active asset checklist for Test 26**  
Purpose: this file is the handoff contract for the project owner or an agentic image-generation/upload run.

## Rules

1. Final approved Test 26 marketing assets are local repository files.
2. Until a final asset exists, the UI may use the listed `placehold.co` URL as a temporary prototype fallback.
3. A placeholder must clearly look like a placeholder; it must never be presented as a real Armaghan factory, certificate, staff member, customer or product photograph.
4. When the local image fails at runtime, `SmartImage.vue` must own the error/fallback transition.
5. Do not hotlink catalog/product photography. These placeholder URLs are only for unresolved Test 26 marketing slots.
6. Final local derivatives should be WebP unless a documented reason requires another format.
7. Keep source originals outside compiled test snapshots where practical; generate optimized derivatives deterministically.
8. Do not upscale an undersized source.
9. Strip EXIF/GPS metadata from final derivatives.
10. Every replacement run must update the status in this file.

## Asset checklist

| ID | Section | Required local path | Target size | Ratio | Format | Temporary fallback | Content brief | Status |
|---|---|---|---:|---:|---|---|---|---|
| T26-HERO-01 | Home Hero | `platform/frontend/public/images/test26/home/hero-selected.webp` | 800×450 (source; no upscale) | 16:9 | WebP | `https://placehold.co/1920x1080/0B2340/FFFFFF.webp?text=IMAGE+REQUIRED%0AHome+Hero+1920x1080` | Owner-selected generated option 1-2; single garment/trade image with no baked-in text | approved-local (owner choice 1-2) |
| T26-ABOUT-01 | About Armaghan | `platform/frontend/public/images/test26/home/about-armaghan.webp` | 800×450 (source; no upscale) | 16:9 | WebP | `https://placehold.co/1600x900/E8F2EF/10243E.webp?text=IMAGE+REQUIRED%0AAbout+Armaghan+1600x900` | Owner-selected generated conceptual handshake/trade option 2-2; do not imply depicted people are actual Armaghan staff | approved-local (owner choice 2-2) |
| T26-CAP-01 | Capability 1 | `platform/frontend/public/images/test26/home/capability-production.webp` | 800×450 (source; no upscale) | 16:9 | WebP | `https://placehold.co/1200x675/E8F2EF/10243E.webp?text=IMAGE+REQUIRED%0AProduction+Capability` | Owner-selected generated production/quality-control option 3-1; conceptual visual, not a verified Armaghan facility | approved-local (owner choice 3-1) |
| T26-CAP-02 | Capability 2 | `platform/frontend/public/images/test26/home/capability-export-prep.webp` | 800×450 (source; no upscale) | 16:9 | WebP | `https://placehold.co/1200x675/E8F2EF/10243E.webp?text=IMAGE+REQUIRED%0AExport+Preparation` | Owner-selected generated export-preparation option 4-3; conceptual visual, not a verified Armaghan facility | approved-local (owner choice 4-3) |
| T26-CAP-03 | Capability 3 | `platform/frontend/public/images/test26/home/capability-documents.webp` | 800×450 (source; no upscale) | 16:9 | WebP | `https://placehold.co/1200x675/E8F2EF/10243E.webp?text=IMAGE+REQUIRED%0ADocuments+and+Trade` | Owner-selected generated trade-document option 5-2; no certificates, seals, or official approvals are asserted | approved-local (owner choice 5-2) |
| T26-BANNER-01 | Product category banner — Baby | `platform/frontend/public/images/test26/home/banner-baby.webp` | 800×300 (source; no upscale) | 8:3 | WebP | `https://placehold.co/1600x600/F1F5F4/10243E.webp?text=IMAGE+REQUIRED%0ABaby+Category+Banner` | Owner-selected generated baby-category option 6-3; product-focused, no embedded text | approved-local (owner choice 6-3) |
| T26-BANNER-02 | Product category banner — Kids | `platform/frontend/public/images/test26/home/banner-kids.webp` | 800×300 (source; no upscale) | 8:3 | WebP | `https://placehold.co/1600x600/F1F5F4/10243E.webp?text=IMAGE+REQUIRED%0AKids+Category+Banner` | Owner-selected generated kids-category option 7-3; professional rather than childish, no embedded text | approved-local (owner choice 7-3) |
| T26-BANNER-03 | Product category banner — Women | `platform/frontend/public/images/test26/home/banner-women.webp` | 800×300 (source; no upscale) | 8:3 | WebP | `https://placehold.co/1600x600/F1F5F4/10243E.webp?text=IMAGE+REQUIRED%0AWomen+Category+Banner` | Owner-authorized automatic choice 8-2: three loose, full-coverage womenswear outfits on headless mannequins; no human model, visible hair, or body emphasis | approved-local (automatic choice 8-2) |
| T26-PRODUCTS-INTRO-01 | Products intro/background | `platform/frontend/public/images/test26/products/products-intro.webp` | 1920×520 | ~3.69:1 | WebP | `https://placehold.co/1920x520/EAF6F1/10243E.webp?text=OPTIONAL+IMAGE%0AProducts+Intro` | Optional pale/airy background visual for Products page intro. The section must still work without this image using a soft mint surface | optional |

## Crop / safe-area requirements

### Hero
- Keep primary subject away from the copy-safe region.
- Safe copy region should occupy at least ~30% of width on desktop.
- Mobile crop must remain meaningful at approximately 4:3 / square-ish viewport crops.
- No essential text inside the image.

### Capability cards
- Important subject must survive a center crop to roughly 4:3 on narrow screens.
- Do not place critical evidence, documents, labels or people at extreme edges.

### Product banners
- Compose for a wide banner first.
- Subject should remain readable if the card becomes ~3:2 or 16:9 on mobile.
- No text baked into the image; titles remain semantic HTML.

## Naming / agentic replacement procedure

For every asset above:

1. Read this file.
2. Locate the row by ID.
3. Obtain an owner-uploaded source or generate/source a compliant candidate.
4. Verify content against the brief and project rules.
5. Create the target WebP at or below the requested dimensions; never upscale.
6. Strip EXIF/GPS.
7. Save exactly at the listed local path.
8. Update this file’s status from `required` to one of:
   - `owner-supplied`
   - `generated-pending-review`
   - `approved-local`
9. If third-party stock is used, also record provenance/license in the existing web-stock manifest and follow `docs/ASSET-MANAGEMENT.md`.
10. Run media policy tests and the Test 26 source contract before deployment.

## Supplied visual references in the customer conversation

The supplied screenshots/photos are **references**, not automatically approved repository assets:
- ecommerce/catalog reference with dark navy header and pale-green category intro;
- three capability-card reference;
- business/export reference with Iran/Iraq context;
- apparel category/hero collage reference.

Do not commit or publish these reference images as production assets unless the owner separately confirms source rights and intended use.

## Placeholder service notes

Placehold supports explicit dimensions and WebP output. The current URLs deliberately include `IMAGE REQUIRED` text so a missing asset is obvious during QA instead of being mistaken for final media.

Reference: https://placehold.co/


## Customer-supplied source notes — final Test 26 clarification

- **About Armaghan:** owner selected generated candidate `T26-ABOUT-02.webp` (choice 2-2). The previously supplied source image remains unused; this generated trade-handshake scene is conceptual and does not identify actual Armaghan staff or premises.
- **Product category banners:** owner selected Baby option 6-3 and Kids option 7-3, and authorized automatic selection for the last slot. Women option 8-2 was selected for its fully covered garments displayed on headless mannequins, matching the prior no-model preference. No women’s placeholder remains.
- **Capabilities:** owner selected generated Production option 3-1, Export option 4-3, and Trade Documents option 5-2. These are conceptual images, not evidence of a specific Armaghan facility, commercial shipment, or official certification.
- **Hero:** owner selected generated option 1-2 (`T26-HERO-02.webp`). Test 26 uses a separate local file path; the previous `hero-brand.webp` and carousel assets are preserved. The slogan remains a separate editable text field.
