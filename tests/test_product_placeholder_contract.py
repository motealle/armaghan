#!/usr/bin/env python3
from pathlib import Path
import json

ROOT = Path(__file__).resolve().parents[1]
PUBLIC = ROOT / "platform/frontend/public/images/placeholders"
SRC = ROOT / "platform/frontend/src"

manifest = json.loads((PUBLIC / "manifest.json").read_text(encoding="utf-8"))
assert manifest["default_set"] == "paper-cut"
set_ids = [item["id"] for item in manifest["sets"]]
assert set_ids == ["paper-cut", "flat-geometric", "dimensional"]
assert set(manifest["subcategories"]) == {"11", "12", "21", "22", "31", "32"}

for set_id in set_ids:
    for code in manifest["subcategories"]:
        avif = PUBLIC / set_id / f"sub-{code}.avif"
        webp = PUBLIC / set_id / f"sub-{code}.webp"
        assert avif.stat().st_size > 0 and b"ftypavif" in avif.read_bytes()[:32]
        webp_header = webp.read_bytes()[:12]
        assert webp.stat().st_size > 0 and webp_header[:4] == b"RIFF" and webp_header[8:12] == b"WEBP"

registry = (SRC / "data/productPlaceholders.ts").read_text(encoding="utf-8")
design = (SRC / "stores/design.ts").read_text(encoding="utf-8")
media = (SRC / "features/catalog/components/ProductMediaCarousel.vue").read_text(encoding="utf-8")
admin = (SRC / "features/admin/components/AdminDashboard.vue").read_text(encoding="utf-8")
smart_image = (SRC / "components/media/SmartImage.vue").read_text(encoding="utf-8")

assert "defaultPlaceholderSet: PlaceholderSetId = 'paper-cut'" in registry
for set_id in set_ids:
    assert set_id in registry
assert "placeholderSet" in design and "localStorage" in design
assert "productPlaceholder" in media and "fallbackImage" in media
assert "placeholderSets" in admin and 'role="radiogroup"' in admin and 'type="radio"' in admin
assert "fallbackSrc" in smart_image and "useFallback" in smart_image

print("Product placeholder media contract: PASS")
