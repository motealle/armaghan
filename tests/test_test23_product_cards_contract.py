#!/usr/bin/env python3
from pathlib import Path
import json
import hashlib

ROOT=Path(__file__).resolve().parents[1]
FRONTEND=ROOT/"platform/frontend"
SRC=FRONTEND/"src"
PUBLIC=FRONTEND/"public"
pkg=json.loads((FRONTEND/"package.json").read_text(encoding="utf-8"))
lock=json.loads((FRONTEND/"package-lock.json").read_text(encoding="utf-8"))
vite=(FRONTEND/"vite.config.ts").read_text(encoding="utf-8")
workflow=(ROOT/".github/workflows/ftp-deploy.yml").read_text(encoding="utf-8")
launcher=(ROOT/"t/index.htm").read_text(encoding="utf-8")
immutable={line.strip() for line in (ROOT/"docs/IMMUTABLE-TESTS.txt").read_text(encoding="utf-8").splitlines() if line.strip() and not line.lstrip().startswith("#")}
backlog=(ROOT/"docs/BACKLOG.md").read_text(encoding="utf-8")
rules=(ROOT/"docs/PROJECT-RULES.md").read_text(encoding="utf-8")
audit=(ROOT/"docs/TEST23-UX-AUDIT.md").read_text(encoding="utf-8")
products=(SRC/"views/ProductsView.vue").read_text(encoding="utf-8")
card=(SRC/"features/catalog/components/ProductCard.vue").read_text(encoding="utf-8")
media=(SRC/"features/catalog/components/ProductMediaCarousel.vue").read_text(encoding="utf-8")
smart=(SRC/"components/media/SmartImage.vue").read_text(encoding="utf-8")
catalog=(SRC/"data/catalog.ts").read_text(encoding="utf-8")
admin=(SRC/"features/admin/components/AdminDashboard.vue").read_text(encoding="utf-8")
css=(SRC/"styles/main.css").read_text(encoding="utf-8")
manifest=json.loads((PUBLIC/"images/category-navigation/manifest.json").read_text(encoding="utf-8"))

assert tuple(map(int,pkg["version"].split("."))) >= (0,23,0)
assert tuple(map(int,lock["version"].split("."))) >= (0,23,0) and tuple(map(int,lock["packages"][""]["version"].split("."))) >= (0,23,0)
assert "../../t/23" in vite or "../../t/24" in vite
assert ("test23-build" in workflow and "path: t/23" in workflow) or ("test24-build" in workflow and "path: t/24" in workflow)
assert ('os.path.isdir("t/23")' in workflow and 'os.walk("t/23")' in workflow) or ('os.path.isdir("t/24")' in workflow and 'os.walk("t/24")' in workflow)
assert "test_test23_product_cards_contract.py" in workflow
assert "22" in immutable and "23" in immutable
assert launcher.index("./23/index.html") < launcher.index("./22/index.html")
assert "Test 23" in backlog
assert "Tests 01–23 are frozen; current source target is Test 24." in rules or "Tests 01–22 are frozen; current source target is Test 23." in rules
assert audit.count("| 1 |") >= 10

# Main category image cards
for path in [
    "./images/category-navigation/category-baby.webp",
    "./images/category-navigation/category-kids.webp",
    "./images/category-navigation/category-women.webp",
]:
    assert path in catalog
assert "category-showcase" in products and "category-card-media" in products
assert ':aria-pressed="category===cat.code"' in products
assert "category.value===code?'all':code" in products
assert "desktop-filter-option" in products  # subcategory/status filtering remains
assert "{{locale.categoryName(cat.code,cat.name)}}" in products

# User-provided derived media contract
assert manifest["generation"]=="none"
assert manifest["processing"]["width"]==320 and manifest["processing"]["height"]==240
for item in manifest["items"]:
    asset=PUBLIC/"images/category-navigation"/item["path"]
    data=asset.read_bytes()
    assert len(data)==item["bytes"]
    assert data[:4]==b"RIFF" and data[8:12]==b"WEBP"
    assert hashlib.sha256(data).hexdigest()==item["sha256"]

# Product-card visual hierarchy and low-copy policy
assert "aspect?:'hero'|'card'|'square'|'product'" in smart
assert "aspect-[2/3]" in smart
assert 'aspect="product"' in media
assert "subcategoryName" not in media
assert "absolute inset-x-0 bottom-0" not in media
assert "availability-badge" not in card
assert "SlidersHorizontal" not in card
assert "visibleTitle" in card
assert "props.product.availability==='available'?displayName.value:locale.t('unavailable')" in card
assert '<div class="product-code-row">' in card and "{{product.code}}" in card
assert "displaySubcategory" not in card
assert "card-actions compact" in card
assert 'size="25.3"' in card
assert "Menu" in card and "ArrowDownRight" in card and "detail-menu-icon" in card
assert "adminCardActions" not in admin
assert ".category-card" in css and ".product-card-body" in css and ".detail-menu-icon" in css

# Test 23 browser-state isolation with catalog forward migration
for key_file in [
    FRONTEND/"index.html",
    SRC/"stores/session.ts",
    SRC/"stores/favorites.ts",
    SRC/"stores/customers.ts",
    SRC/"stores/design.ts",
    SRC/"stores/theme.ts",
    SRC/"stores/locale.ts",
]:
    text=key_file.read_text(encoding="utf-8")
    assert "armaghan:test23" in text or "armaghan:test24" in text
    assert "armaghan:test22" not in text

catalog_store=(SRC/"stores/catalog.ts").read_text(encoding="utf-8")
assert "const KEY='armaghan:test23:products-v1'" in catalog_store or "const KEY='armaghan:test24:products-v1'" in catalog_store
assert "'armaghan:test22:products-v1'" in catalog_store
if "const KEY='armaghan:test24:products-v1'" in catalog_store:
    assert "'armaghan:test23:products-v1'" in catalog_store

print("Test 23 product-card/category contract: PASS")
