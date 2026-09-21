from pathlib import Path
import json

ROOT = Path(__file__).resolve().parents[1]
PUBLIC = ROOT / "platform/frontend/public/images/final"

ASSETS = {
    "hero/hero-brand": (1672, 941),
    "hero/hero-production": (1672, 941),
    "hero/hero-export": (1672, 941),
    "categories/category-baby": (1448, 1086),
    "categories/category-kids": (1448, 1086),
    "categories/category-women-modest": (1448, 1086),
    "details/fabric-detail": (1448, 1086),
    "services/custom-packaging": (1448, 1086),
    "services/private-label": (1448, 1086),
    "trust/quality-documents": (1448, 1086),
}


manifest = json.loads((PUBLIC / "manifest.json").read_text())
assert len(manifest["items"]) == len(ASSETS)
by_id = {item["id"]: item for item in manifest["items"]}

for stem, dimensions in ASSETS.items():
    name = Path(stem).name
    avif = PUBLIC / f"{stem}.avif"
    webp = PUBLIC / f"{stem}.webp"
    assert avif.stat().st_size > 0 and b"ftypavif" in avif.read_bytes()[:32]
    assert webp.stat().st_size > 0 and webp.read_bytes()[:4] == b"RIFF" and webp.read_bytes()[8:12] == b"WEBP"
    assert (by_id[name]["width"], by_id[name]["height"]) == dimensions

hero = (ROOT / "platform/frontend/src/features/home/components/HeroCarousel.vue").read_text()
catalog = (ROOT / "platform/frontend/src/data/catalog.ts").read_text()
smart_image = (ROOT / "platform/frontend/src/components/media/SmartImage.vue").read_text()
assert hero.count("./images/final/hero/") == 3
assert catalog.count("./images/final/categories/") == 9
assert "const gallery = [meta.image, './images/final/details/fabric-detail.webp', legacyFallback]" in catalog
assert 'type="image/avif"' in smart_image
assert "<img :src=\"src\"" in smart_image

print("Generated media contract: PASS")
