#!/usr/bin/env python3
from pathlib import Path
import json
import re

ROOT=Path(__file__).resolve().parents[1]
FRONTEND=ROOT/"platform/frontend"
SRC=FRONTEND/"src"

pkg=json.loads((FRONTEND/"package.json").read_text(encoding="utf-8"))
lock=json.loads((FRONTEND/"package-lock.json").read_text(encoding="utf-8"))
workflow=(ROOT/".github/workflows/ftp-deploy.yml").read_text(encoding="utf-8")
launcher=(ROOT/"t/index.htm").read_text(encoding="utf-8")
immutable={line.strip() for line in (ROOT/"docs/IMMUTABLE-TESTS.txt").read_text(encoding="utf-8").splitlines() if line.strip() and not line.lstrip().startswith("#")}
backlog=(ROOT/"docs/BACKLOG.md").read_text(encoding="utf-8")
rules=(ROOT/"docs/PROJECT-RULES.md").read_text(encoding="utf-8")
audit=(ROOT/"docs/TEST25-UX-AUDIT.md").read_text(encoding="utf-8")
generator=(ROOT/"platform/scripts/generate_placeholder_portraits.py").read_text(encoding="utf-8")

registry=(SRC/"data/productPlaceholders.ts").read_text(encoding="utf-8")
spec=(SRC/"data/productPlaceholders.spec.ts").read_text(encoding="utf-8")
design=(SRC/"stores/design.ts").read_text(encoding="utf-8")
media=(SRC/"features/catalog/components/ProductMediaCarousel.vue").read_text(encoding="utf-8")
smart=(SRC/"components/media/SmartImage.vue").read_text(encoding="utf-8")
admin=(SRC/"features/admin/components/AdminDashboard.vue").read_text(encoding="utf-8")
css=(SRC/"styles/main.css").read_text(encoding="utf-8")
wizard=(SRC/"features/orders/components/OrderWizard.vue").read_text(encoding="utf-8")
messages=(SRC/"i18n/messages.ts").read_text(encoding="utf-8")

assert tuple(map(int,pkg["version"].split("."))) >= (0,25,0)
assert tuple(map(int,lock["version"].split("."))) >= (0,25,0) and tuple(map(int,lock["packages"][""]["version"].split("."))) >= (0,25,0)
assert "generate:portraits" in pkg["scripts"]
assert "generate:portraits" in pkg["scripts"]["build"]
assert "pillow==11.3.0" in workflow.lower()
assert "test_test25_reversible_media_contract.py" in workflow
assert "24" in immutable and "25" in immutable
assert launcher.index("./25/index.html") < launcher.index("./24/index.html")
assert "Test 25" in backlog
assert "rollback/test24-pre-test25" in backlog
assert "25" in immutable
assert audit.count("| 1 |") >= 10

# Reproducible portrait derivatives; originals are a separate source tree.
for token in [
    'SOURCE = FRONTEND / "public/images/placeholders"',
    'TARGET = FRONTEND / "public/images/placeholders-portrait"',
    'TARGET_SIZE = (960, 1440)',
    'average_edge',
    'Image.Resampling.LANCZOS',
    'quality=88',
]:
    assert token in generator
assert "resize" in generator and "paste" in generator
assert "source_sha256" in generator and "target_sha256" in generator

# Registry keeps both orientations and defaults tall product cards to portrait.
assert "PlaceholderOrientation = 'portrait' | 'landscape' | 'auto'" in registry
assert "defaultPlaceholderOrientation: PlaceholderOrientation = 'portrait'" in registry
assert "landscapePlaceholder" in registry and "portraitPlaceholder" in registry
assert "./images/placeholders-portrait/" in registry
assert "productPlaceholder(set.id, code, 'landscape')" in spec

# Admin can reversibly choose the media orientation.
assert "placeholderOrientation" in design
assert any(int(n) >= 25 for n in re.findall(r"armaghan:test(\d{2}):placeholder-orientation", design))
for value in ["portrait","landscape","auto"]:
    assert f'value="{value}"' in admin
assert "placeholderOrientationHelp" in admin
assert "placeholderPortrait" in messages and "placeholderLandscape" in messages and "placeholderAuto" in messages

# Product media uses selected portrait path and original landscape as fallback.
assert "landscapePlaceholder" in media
assert "design.placeholderOrientation" in media
assert 'fit="edge-extend"' in media
assert "'edge-extend'" in smart
assert "smart-image-vignette" in smart and "edge-extend-backdrop" in smart
assert "/images/placeholders-portrait/" in smart

# Gray dark theme and calmer category numbering.
for token in ["--c-bg:#151618","--c-surface:#1e2024","--c-surface-2:#25282d","--c-border:#383c43"]:
    assert token in css
assert "--brand-blue:#151EDA" in css and "--c-primary:var(--brand-blue)" in css
assert "html.dark .product-card" in css
assert ".category-card.active .category-number" in css
assert "font:700 .61rem" in css

# Product media/body seam is visually defined without status text.
assert ".product-card-media::after" in css
assert ".smart-image-vignette" in css

# Production wizard fixes observed English layout problems.
assert "const BackIcon=computed(()=>locale.direction==='ltr'?ArrowLeft:ArrowRight)" in wizard
assert "wizard-category-option" in wizard
assert "wizard-category-subtitle" in wizard
assert 'class="hidden sm:inline"' in wizard
assert 'html[data-locale="en"] .wizard-category-grid' in css

# Footer and home trust cards isolate English direction.
assert 'html[data-locale="en"] .site-footer' in css
assert "unicode-bidi:isolate" in css
assert 'html[data-locale="en"] .home-trust-card' in css

# Test 25 state isolation with catalog migration from Test 24.

def namespace_at_least(text: str, minimum: int) -> bool:
    return any(int(n) >= minimum for n in re.findall(r"armaghan:test(\d{2})", text))

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
    assert namespace_at_least(text,25)
    assert "armaghan:test24" not in text

catalog=(SRC/"stores/catalog.ts").read_text(encoding="utf-8")
current_match=re.search(r"const KEY='armaghan:test(\d{2}):products-v1'",catalog)
assert current_match and int(current_match.group(1)) >= 25
assert "'armaghan:test24:products-v1'" in catalog

print("Test 25 reversible media/dark/wizard contract: PASS")
