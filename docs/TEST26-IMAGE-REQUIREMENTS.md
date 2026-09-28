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
| T26-HERO-01 | Home Hero | `platform/frontend/public/images/final/hero/hero-brand.webp` | existing approved derivative | 16:9 | WebP | `https://placehold.co/1920x1080/0B2340/FFFFFF.webp?text=IMAGE+REQUIRED%0AHome+Hero+1920x1080` | Single hero selected from the existing supplied/approved hero set; copy is one short slogan only | approved-local |
| T26-ABOUT-01 | About Armaghan | `platform/frontend/public/images/test26/home/about-armaghan.webp` | up to 1600×900 | 16:9 source | WebP | `https://placehold.co/1600x900/E8F2EF/10243E.webp?text=IMAGE+REQUIRED%0AAbout+Armaghan+1600x900` | Use the exact owner-supplied handshake/trade image from the customer conversation (source attachment 1672×941); preserve aspect ratio and do not upscale | owner-supplied-source; derivative pending |
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


## Customer-supplied source notes — final Test 26 clarification

- **About Armaghan:** customer explicitly approved the supplied handshake/trade image. Conversation source filename: `50d629c7-a11a-489a-812f-ba620a705a0a.jpg`, observed source size **1672×941**. The runtime slot intentionally keeps the documented `placehold.co` fallback until an optimized local WebP derivative is committed.
- **Product category banners:** customer approved the visual direction shown in the supplied collage/screenshot `image.png` (**669×558**) but previously stated that the original banner assets had not been pasted. Treat the screenshot as visual reference only; keep the three banner rows open until original/high-resolution images are available or an agentic image run generates compliant replacements.
- **Capabilities:** customer accepted the supplied three-card reference as the intended direction but did not provide the final three individual images. Keep the three capability rows open and visibly placeholder-backed.
- **Hero:** customer confirmed a **single** hero image and accepted the previously supplied hero image set. Test 26 currently selects the existing local `hero-brand.webp` derivative, with a one-slogan-only composition. The slogan remains editable in Admin so later copy replacement is non-destructive.


## Generated candidate review — 2026-09-29

- Three numbered alternatives are available for each of the eight required image slots, plus three optional Products intro backgrounds (27 review previews total).
- Files and the visual selection index are in `assets/generated-review/test26/`; use the identifiers in its `README.md` to record choices.
- These are lightweight 800px-wide (or banner-ratio) WebP review previews with status `generated-pending-review`. They are not the final target derivatives and are not connected to Test 26. The existing approved Hero and owner-approved About source remain untouched.
- After selection, create final local derivatives at the dimensions and aspect ratios above, strip metadata, update each manifest status to `approved-local`, and only then connect the selected paths to Test 26.
- All candidates are generic generated concepts. They must not be described as actual Armaghan staff, factory, SKU photography, certificates or shipment evidence.
