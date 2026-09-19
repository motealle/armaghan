# Asset Management Architecture — Armaghan

## Decision: host-centric media

Armaghan will be **host-centric by default**. Runtime media is stored on the same hosting environment as the Laravel application unless a later capacity/reliability review explicitly changes that decision.

### Runtime disks
- Public product/category/hero media: Laravel `public` disk → `storage/app/public/media` → served through `public/storage`.
- Private customer/order documents: Laravel `local` / private disk under `storage/app/private`.
- SQLite remains the development database. Media bytes are never stored as SQLite/MySQL BLOBs.
- Database rows store media relationships and metadata only.
- Off-host storage is **backup/DR only** at this stage, not the primary serving path.

This follows Laravel's filesystem abstraction while keeping the operational model simple for the current hosting setup.

## Package analysis and ranking

### 1 — Spatie Laravel Media Library — selected
**Score for Armaghan: 9.5/10**

Why it wins:
- attaches files directly to Eloquent models;
- named collections fit product cover/gallery, category, hero and private documents;
- deterministic conversions and responsive images;
- local Laravel disks work cleanly for host-centric storage;
- strong maintenance cadence and Laravel 13 compatibility;
- official Filament integration exists, so admin upload/reorder/preview uses the same media model;
- keeps a future migration to another Laravel filesystem disk possible without redesigning domain models.

Decision: use **`spatie/laravel-medialibrary` v11**, initially pinned to the tested 11.23 patch line rather than a completely open major range. Add the official Filament Spatie Media Library plugin when the Laravel/Filament admin is bootstrapped.

### 2 — Plank Laravel Mediable
**Score: 8.1/10**

Strengths:
- filesystem-driven;
- flexible polymorphic attachments and tags;
- image variants;
- smaller conceptual surface.

Why not selected:
- less direct alignment with our planned Filament admin;
- smaller ecosystem for our exact product/media workflow;
- we would need more custom glue for conversions/admin UX that Spatie already standardizes.

### 3 — Laravel core Filesystem + Image only
**Score: 7.2/10**

Strengths:
- fewest third-party dependencies;
- Laravel 13 already has strong local/public/private disk primitives and image manipulation.

Why not selected:
- we would have to build media collections, ordering, model attachment, conversion tracking, metadata and cleanup ourselves;
- higher long-term maintenance risk for a project where product imagery is core business data.

### 4 — SaaS-first media platforms
Not selected. They conflict with the current host-centric requirement and add vendor/runtime dependency before we need it.

## Host directory contract

Suggested production structure:

```text
storage/app/public/media/
  products/{media-id}/
    original.ext
    card-sm.webp
    card-md.webp
    gallery.webp
  categories/{media-id}/...
  hero/{media-id}/...
  brand/{media-id}/...

storage/app/private/
  customers/{customer-id}/documents/...
  orders/{order-id}/documents/...
```

Do not make business logic depend on these literal paths; Media Library / Laravel Storage owns path resolution.

## Media collections

- `product-cover`
- `product-gallery`
- `category-cover`
- `hero`
- `brand-assets`
- `customer-documents` (private)
- `order-documents` (private)

## Conversion contract

Keep the original subject to upload policy and create:
- `admin-thumb`: 160×160 crop;
- `card-sm`: 480×360;
- `card-md`: 960×720;
- `gallery`: maximum 1440px long edge;
- `hero`: 1920×1080 when source quality is sufficient.

Preferred delivery format is WebP now. AVIF can be added later after server capability/CPU cost is measured. Never upscale a small source.

## Upload pipeline

1. Inspect real MIME/content, not extension alone.
2. Enforce file-size and pixel limits.
3. Decode image; reject corrupt payloads.
4. Normalize EXIF orientation.
5. Strip EXIF/GPS and unneeded metadata.
6. Hash the original.
7. Store on the correct host disk.
8. Create the Media Library record/collection relation.
9. Queue conversions when queue workers are available; otherwise process synchronously within safe limits.
10. Mark the asset ready only when required derivatives exist.
11. Archive at business level before physical deletion.

## Metadata we need

- media id;
- Eloquent owner/model;
- collection;
- original filename;
- MIME / width / height / bytes;
- SHA-256;
- sort order;
- localized alt text;
- optional focal point;
- provenance/source status;
- creator/source URL/license for third-party temporary stock;
- created_by / created_at;
- archived_at when applicable.

## Web-stock bootstrap policy

Until owner-supplied photography is ready, Test iterations may vendor temporary web photography locally.

Rules:
- **no image generation** in this workflow;
- do not hotlink runtime product media;
- prefer Wikimedia Commons / Openverse-style sources with machine-readable provenance and reuse licensing;
- only licenses compatible with commercial website prototyping are accepted (e.g. Public Domain/CC0/CC BY/CC BY-SA); reject NC/ND/unknown;
- save an optimized local WebP copy under `platform/frontend/public/images/web-stock/`;
- store source page, creator, license, license URL and checksum in `platform/assets/web-stock/manifest.json`;
- expose attribution in the Test UI when the selected license requires it;
- strip EXIF/GPS from local derivatives;
- treat stock images as temporary visual scaffolding, not claims that they depict Armaghan's actual factory/products.

### Automated sourcing
`platform/scripts/vendor_web_stock.py` queries the Wikimedia Commons API with a descriptive User-Agent, selects only allowlisted licenses, downloads a bounded thumbnail, strips metadata, creates a WebP derivative and records provenance. The GitHub workflow commits the vendored derivatives to the repository so the deployed site remains host/local-file based.

## Frontend media contract

Vue does not construct backend storage paths. Production API/props return a media object, e.g.:

```json
{
  "id": 42,
  "alt": "پتوی نوزادی مدل ...",
  "placeholder": "...",
  "sources": {
    "card": {"src": "...", "srcset": "..."},
    "gallery": {"src": "...", "srcset": "..."}
  }
}
```

The reusable `SmartImage` owns shimmer, aspect ratio and error fallback.

## Backup / disaster recovery

Because serving is host-centric, backup discipline matters:
- database backup + media tree backup are one recovery unit;
- use daily off-host backups/snapshots;
- verify restore, not only backup creation;
- keep customer/private documents out of Git;
- repository-vendored temporary stock is reproducible and lower risk than customer uploads.

## Not needed yet

- enterprise DAM approval graphs;
- AI auto-tagging;
- face recognition;
- on-demand transformation SaaS;
- CDN cache invalidation machinery;
- multi-tenant asset rights systems.

The goal is a professional media layer without turning asset management into a separate product.
