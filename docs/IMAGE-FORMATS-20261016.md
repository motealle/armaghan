# Mobile photo format acceptance — 2026-10-16

Explicit owner request: complete multi-format photo upload in one bounded run and verify it, rather than leaving HEIC dependent on native browser decoding.

| Rank | Method | Reason |
|---:|---|---|
| 1 | Native decode first, self-hosted lazy HEIC/HEIF decoder fallback; canonical JPEG/PNG/WebP uploads | Covers camera files without server codec changes, external image services or heavier initial browsing; existing validation remains authoritative |
| 2 | Add server libheif/ImageMagick conversion | Shared host has no guaranteed codec/install/shell capability |
| 3 | Native browser decode only | Existing gap: Chrome cannot decode HEIC on many phones |
| 4 | Ask customer to convert every photo manually | Fails requested low-friction product flow |
| 5 | Accept all extensions directly at backend | Rejected: bypasses content/decode policy and still cannot display unsupported codecs |

Selected: option1. Added pinned heic-to1.6.6 CSP build (libheif1.23.6), loaded only after HEIC native decode fails, worker-backed and self-hosted; 45-second bounded UI wait, no external image conversion. License notice/source included in public/licenses. JPEG/PNG/WebP remain canonical server inputs. AVIF/GIF/BMP decode through browser and re-encode; GIF becomes a static first frame. JPEG/JPG, PNG, WebP, HEIC, HEIF, AVIF, GIF and BMP are the explicit input list; TIFF/RAW/PDF/SVG and corrupt files are not promised.

Budgets remain20MiB source,8MiB upload, six product images,1920px longest optimized edge;64MP decoded-image ceiling added. Server authorization, actual MIME/decode/rewrite, revision, media ownership and derivatives remain unchanged. Product and Home flows reuse the same optimizer; input hints updated in four languages. No frozen01–28 files changed.

Verification: eight synthetic64x48 binary fixtures generated with Pillow/pillow-heif1.3.0, SHA256 manifest, no personal photos. Dedicated browser acceptance runs actual Chromium with external requests blocked; prepares each file with the real production module (including HEIC fallback), verifies readable canonical bytes and dimensions, rejects fake PNG and corrupt HEIC. Its exact outputs feed isolated Laravel acceptance: authenticated upload, persisted media/reload, served product card, Home upload/publication/display. No manager credentials or production products used. A passing mocked decoder is insufficient; dedicated workflow must pass before merge. Local Chromium download is unavailable in this environment, so real-codec/browser acceptance runs in GitHub CI. Local TS/build and17 focused unit checks passed before PR.

References: https://github.com/hoppergee/heic-to ; https://github.com/strukturag/libheif . Original customer's failing file/network still cannot be claimed tested without those bytes and that device. Successful eight-format pipeline acceptance establishes capability separately from that customer-specific observation.

## Verified closeout

PR35 merged5a11df89. Final acceptance38927801657 PASS: eight real browser conversions (including retained image pixels) + isolated server product/Home upload/persistence/display139assertions. Main108backend tests and75frontend release checks PASS; FTP38928014940 and Backend Code Deploy38928014905 PASS. Published root, editor, lazy decoder chunks and license notice match SHA256. Camera previews are prepared before save and conversion reused; original pending file is retained. Physical customer handset/original failing photo and Baghdad network remain separate observations, not blockers to the now-verified supported-format capability.
