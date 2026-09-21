# Armaghan — Image Generation Decision Audit

## Source triangulation

Five repository files act as image lists or inventories:

1. `pics.md` — current Persian production list: 10 final assets, destinations, formats and prompts.
2. `docs/pics-Prompt.md` — older Test 11 prompt pack: 14 review assets.
3. `assets/generated-review/manifest.json` — generated-review queue tied to the older prompt pack.
4. `platform/assets/web-stock/queries.json` — 13 temporary stock-image slots.
5. `platform/assets/web-stock/manifest.json` — provenance for those temporary stock images.

The two prompt-bearing files are not merged mechanically. `pics.md` is the current product memory and wins on privacy, final paths, filenames and final formats. The older prompt pack contributes its useful global art direction: a coherent photographic family, soft diffused light, restrained brand palette, negative space and text/logo avoidance.

## Ranked implementation options

| Rank | Action | Why this rank | Important trade-off |
|---:|---|---|---|
| 1 | Generate the 10 canonical assets from `pics.md`, enriched by the compatible global rules in `docs/pics-Prompt.md`; archive originals outside Git, derive AVIF + WebP, validate, then wire the current Vue app to them | Best balance of privacy, consistency, performance, truthful visuals and maintainability; follows the newest repository contract and removes temporary stock dependencies from primary surfaces without bloating every CI checkout | Requires generation, deterministic processing and integration work |
| 2 | Generate all 14 Test 11 assets and map them into current slots | Broad coverage and easy compatibility with the old prototype | Reintroduces outdated names and real-child imagery; creates duplicate/unused assets |
| 3 | Generate only the three hero images first | Fastest visible improvement and allows style calibration | Leaves category and trust imagery inconsistent and keeps stock scaffolding in production |
| 4 | Keep the licensed web-stock set and only improve crops/compression | Lowest implementation risk and clear provenance | Generic imagery weakens brand differentiation and does not fulfill the requested generation work |
| 5 | Generate ad hoc images directly into UI folders without a canonical inventory | Quick for a one-off demo | Highest drift, overwrite, provenance, accessibility and maintenance risk |

## Selected action

Option 1 is selected because it treats image generation as a reproducible content pipeline rather than a one-time visual patch. It uses the current 10-slot contract, resolves the old/new privacy conflict in favor of no real children, retains the strongest shared art direction, archives source files outside Git, produces modern web formats, and makes rollback possible by keeping the existing stock set untouched.

## Global constraints for this run

- Photorealistic editorial-commerce family; calm, premium, trustworthy and B2B.
- Soft diffused light, realistic textiles and restrained use of navy, green, mint and gold.
- No text, readable labels, logos, watermarks, flags, fake certificates or unsupported claims.
- No identifiable workers; no real babies or children.
- Full modest Islamic dress for the adult womenswear subject.
- Stable crop before compression; AVIF primary and WebP fallback.
- Generated media is illustrative and must not be presented as documentary evidence of Armaghan facilities or products.
