#!/usr/bin/env python3
from pathlib import Path
import json
import re

root=Path(__file__).resolve().parents[1]
frontend=root/"platform"/"frontend"
src=frontend/"src"
vite=(frontend/"vite.config.ts").read_text(encoding="utf-8")
pkg=json.loads((frontend/"package.json").read_text(encoding="utf-8"))
catalog=(src/"data"/"catalog.ts").read_text(encoding="utf-8")
hero=(src/"features"/"home"/"components"/"HeroCarousel.vue").read_text(encoding="utf-8")
home=(src/"views"/"HomeView.vue").read_text(encoding="utf-8")
router=(src/"router"/"index.ts").read_text(encoding="utf-8")
asset_doc=(root/"docs"/"ASSET-MANAGEMENT.md").read_text(encoding="utf-8")
immutable=(root/"docs"/"IMMUTABLE-TESTS.txt").read_text(encoding="utf-8").splitlines()
launcher=(root/"t"/"index.htm").read_text(encoding="utf-8")
vendor=(root/"platform"/"scripts"/"vendor_web_stock.py").read_text(encoding="utf-8")
queries=json.loads((root/"platform"/"assets"/"web-stock"/"queries.json").read_text(encoding="utf-8"))

assert "outDir: '../../t/13'" in vite
assert pkg["version"] == "0.13.0"
assert "12" in {line.strip() for line in immutable}
assert launcher.index("./13/index.html") < launcher.index("./12/index.html")
assert catalog.count("product(") >= 19, "Expected product factory + at least 18 seeded products"
assert catalog.count("./images/web-stock/sub-") >= 6
assert "./images/web-stock/hero-brand.webp" in hero
assert "تصاویر آزمایشی با منبع آزاد" in home
assert "/credits" in router
assert "host-centric" in asset_doc.lower()
assert "spatie/laravel-medialibrary" in asset_doc
assert "commons.wikimedia.org/w/api.php" in vendor
assert "metadata stripped" in vendor
assert len(queries["assets"]) >= 10
assert not re.search(r'https?://[^"\'\s]+\.(?:jpe?g|png|webp|avif)', catalog, re.I)
print("Test 13 source contract: PASS")
