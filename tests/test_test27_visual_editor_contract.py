#!/usr/bin/env python3
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
SRC = ROOT / "platform/frontend/src"

store_files = [
    SRC / "stores/appearance.ts",
    SRC / "stores/design.ts",
    SRC / "stores/locale.ts",
    SRC / "stores/session.ts",
    SRC / "stores/catalog.ts",
    SRC / "stores/customers.ts",
    SRC / "stores/favorites.ts",
    SRC / "stores/theme.ts",
]
for path in store_files:
    text = path.read_text(encoding="utf-8")
    assert "armaghan:test26:" not in text, path
    assert "armaghan:test27:" in text, path

tokens = (SRC / "features/visual-editor/tokenRegistry.ts").read_text(encoding="utf-8")
for color in ("#21946A", "#151EDA", "#C8E3DB", "#FFFFFF", "#FFB514"):
    assert color in tokens

selection = (SRC / "features/visual-editor/selection.ts").read_text(encoding="utf-8")
assert "document.elementsFromPoint" in selection
assert "data-visual-editor-ui" in selection
assert "result.length>=8" in selection

editor = (SRC / "features/visual-editor/VisualEditor.vue").read_text(encoding="utf-8")
assert "visual-editor-sheet" in editor
assert "setPointerCapture" in editor
assert "چند عنصر نزدیک" in editor
assert "عناصر مخفی‌شده" in editor
assert "ذخیره خودکار" in editor
assert "visual.setEnabled(false)" in editor

profile = (SRC / "features/visual-editor/store.ts").read_text(encoding="utf-8")
assert "armaghan:test27:visual-style-profile:v1" in profile
assert "armaghan:test27:visual-editor-enabled" in profile
assert "css:compileCss(value)" in profile
assert "texts:Record" in profile
assert "isBrandTokenId" in profile

app = (SRC / "App.vue").read_text(encoding="utf-8")
assert 'session.isAdmin&&!session.impersonatedCustomerId' in app
assert "<VisualStyleRuntime/>" in app

appearance = (SRC / "features/admin/components/AppearanceSettings.vue").read_text(encoding="utf-8")
assert "openVisualEditor" in appearance
assert "visual.setEnabled(true)" in appearance

header = (SRC / "components/layout/AppHeader.vue").read_text(encoding="utf-8")
footer = (SRC / "components/layout/SiteFooter.vue").read_text(encoding="utf-8")
hero = (SRC / "features/home/components/HeroSection.vue").read_text(encoding="utf-8")
product = (SRC / "features/catalog/components/ProductCard.vue").read_text(encoding="utf-8")
css = (SRC / "styles/main.css").read_text(encoding="utf-8")

assert 'data-style-id="header.shell"' in header
assert "var(--role-brand-chrome)" in header
assert 'data-style-id="footer.shell"' in footer
assert 'data-style-id="hero.title"' in hero and 'data-editable-text="true"' in hero
assert 'data-style-id="product.card"' in product and 'data-style-id="product.title"' in product
assert "--role-brand-chrome:var(--brand-blue)" in css
assert "background:var(--role-brand-chrome)" in css
assert "html.visual-editor-active .bottom-nav{display:none!important}" in css

immutable = {
    line.strip()
    for line in (ROOT / "docs/IMMUTABLE-TESTS.txt").read_text(encoding="utf-8").splitlines()
    if line.strip() and not line.lstrip().startswith("#")
}
assert "26" in immutable

print("Test 27 visual editor foundation contract: PASS")
