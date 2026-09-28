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
| T26-HERO-01 | Home Hero | `platform/frontend/public/images/test26/home/hero-main.webp` | 1920×1080 | 16:9 | WebP | `https://placehold.co/1920x1080/0B2340/FFFFFF.webp?text=IMAGE+REQUIRED%0AHome+Hero+1920x1080` | One strong export-oriented apparel-manufacturing / brand image; no collage; leave a calm copy-safe area for one short slogan | required |
| T26-ABOUT-01 | About Armaghan | `platform/frontend/public/images/test26/home/about-armaghan.webp` | 1600×1200 | 4:3 | WebP | `https://placehold.co/1600x1200/E8F2EF/10243E.webp?text=IMAGE+REQUIRED%0AAbout+Armaghan+1600x1200` | Professional B2B/export context similar in tone to the supplied Iran/Iraq business reference; do not imply a real signed contract unless the source is authentic | required |
| T26-CAP-01 | Capability 1 | `platform/frontend/public/images/test26/home/capability-production.webp` | 1200×675 | 16:9 | WebP | `https://placehold.co/1200x675/E8F2EF/10243E.webp?text=IMAGE+REQUIRED%0AProduction+Capability` | Garment factory / production-line / quality-control visual; must not claim to depict Armaghan’s actual facility unless owner-supplied and verified | required |
| T26-CAP-02 | Capability 2 | `platform/frontend/public/images/test26/home/capability-export-prep.webp` | 1200×675 | 16:9 | WebP | `https://placehold.co/1200x675/E8F2EF/10243E.webp?text=IMAGE+REQUIRED%0AExport+Preparation` | Export preparation, coding, packing, carton organization, destination-market readiness | required |
| T26-CAP-03 | Capability 3 | `platform/frontend/public/images/test26/home/capability-documents.webp` | 1200×675 | 16:9 | WebP | `https://placehold.co/1200x675/E8F2EF/10243E.webp?text=IMAGE+REQUIRED%0ADocuments+and+Trade` | Trade documents / structured commercial process; avoid fake certificates, fake stamps or invented official approvals | required |
| T26-BANNER-01 | Product category banner — Baby | `platform/frontend/public/images/test26/home/banner-baby.webp` | 1600×600 | 8:3 | WebP | `https://placehold.co/1600x600/F1F5F4/10243E.webp?text=IMAGE+REQUIRED%0ABaby+Category+Banner` | Horizontal baby-category editorial banner with product-focused composition; no embedded text required | required |
| T26-BANNER-02 | Product category banner — Kids | `platform/frontend/public/images/test26/home/banner-kids.webp` | 1600×600 | 8:3 | WebP | `https://placehold.co/1600x600/F1F5F4/10243E.webp?text=IMAGE+REQUIRED%0AKids+Category+Banner` | Horizontal kids-category editorial banner; professional rather than childish | required |
| T26-BANNER-03 | Product category banner — Women | `platform/frontend/public/images/test26/home/banner-women.webp` | 1600×600 | 8:3 | WebP | `https://placehold.co/1600x600/F1F5F4/10243E.webp?text=IMAGE+REQUIRED%0AWomen+Category+Banner` | Horizontal women-category editorial banner; modest Islamic styling, no visible hair, no body-emphasizing/revealing styling | required |
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
