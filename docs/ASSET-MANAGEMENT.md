# Asset Management Architecture — Armaghan

## Decision
Binary assets are **not domain data**. Database rows keep metadata and relationships; image/document bytes live on a storage disk. Laravel's Filesystem is the boundary so development can use local storage and production can move to S3-compatible storage without changing business code.

### Development
- DB: SQLite.
- Public catalog media disk: local filesystem.
- Generated review images: temporary Git review queue under `assets/generated-review/`.
- Never store production customer documents in Git.

### Production
- Public product/brand media: S3-compatible object storage + CDN.
- Private customer/order documents: private disk, short-lived signed URLs.
- Database stores media IDs, collection, alt text, focal point, dimensions, MIME, bytes, checksum, sort order and status; never BLOB image content.

## Recommended package stack

### Required at Laravel bootstrap
**spatie/laravel-medialibrary** is the primary media abstraction. It associates media with Eloquent models, supports separate disks, conversions, responsive images and queued processing. Use named collections such as:
- `product-gallery`
- `product-cover`
- `category-cover`
- `hero`
- `customer-documents`
- `brand-assets`

For Filament 5, use the **official** `filament/spatie-laravel-media-library-plugin` so admin uploads, reordering, table previews and conversions use the same media model. Do not add a second DAM abstraction in parallel.

### Optional only when needed
**intervention/image-laravel** is useful for bespoke editor operations not covered cleanly by Media Library conversions (manual crop editor, special compositing, watermarking). Do not add it on day one merely for resize/compression; Media Library already uses Spatie Image for conversions.

### Queue
Start with Laravel's database queue on small hosting. Move heavy media conversions to Redis when a persistent worker is available. Horizon becomes useful only after Redis and non-trivial queue volume exist.

## Conversion contract
Keep the original (subject to upload size policy) and create deterministic derivatives:
- `admin-thumb`: 160×160 crop
- `card-sm`: 480×360
- `card-md`: 960×720
- `gallery`: max 1440px long edge
- `hero`: 1920×1080
- optional retina hero only when source quality justifies it

Preferred delivery formats:
1. AVIF where conversion support is reliable,
2. WebP,
3. original/JPEG fallback where necessary.

Never upscale small sources.

## Upload pipeline
1. Validate MIME by actual file inspection, not extension alone.
2. Enforce file-size and pixel-dimension limits.
3. Decode the image; reject invalid/corrupt payloads.
4. Normalize orientation.
5. Strip unneeded EXIF/location metadata.
6. Generate checksum and stable media record.
7. Store original.
8. Dispatch conversions.
9. Generate responsive variants/srcset.
10. Mark media ready only after required conversions succeed.

## Frontend contract
Vue never constructs storage paths. API responses return a media object:

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

The reusable `SmartImage` component owns loading, shimmer, error fallback and aspect ratio. This avoids broken-image icons and layout shift.

## Metadata we actually need
For this business, keep:
- media id / model relation
- collection
- original filename
- MIME / width / height / bytes
- checksum
- sort order
- localized alt text
- optional focal point (x/y) for hero/category crops
- source/status (`generated`, `uploaded`, `approved`, `archived`)
- created_by
- created_at

We do **not** need a full enterprise DAM with approval graphs, AI tagging, rights-management contracts, face recognition, dynamic on-demand transforms, or multi-tenant asset libraries at this stage.

## Git policy
Git should contain:
- logos/icons owned by the project,
- small deterministic UI fallbacks,
- selected review assets temporarily when required,
- generated manifest metadata.

Git should not become the long-term original-photo archive. High-resolution originals eventually move to object storage. `assets/generated-review/` is only a temporary review queue; approved assets are later optimized/imported into the application media system.

## Naming
Use stable semantic names, not user-facing titles:
`hero-brand-01`, `category-baby-clothing-01`, `product-11001-01`.

Production storage keys should be UUID/media-ID based to avoid collisions; semantic filenames are only editorial metadata.

## Deletion
Use soft/archive semantics at the business level. Physical deletion of originals should be delayed and explicit. Product deletion must not silently delete media that is referenced by orders/snapshots.


## Practical scope for Armaghan
**Needed now**
- product/category/hero image collections;
- ordering/reordering;
- localized alt text;
- responsive card/gallery/hero derivatives;
- public vs private disks;
- upload validation;
- queue-ready conversions;
- archive instead of accidental destructive delete.

**Useful later**
- focal-point crop editor;
- direct-to-object-storage upload;
- CDN cache purge hooks;
- media usage report;
- bulk import/export.

**Not needed yet**
- enterprise DAM approval workflows;
- AI auto-tagging;
- digital-rights contract management;
- duplicate asset licensing workflows;
- expensive dynamic transformation SaaS.

This keeps the stack professional without turning asset management into a separate product.
