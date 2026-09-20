#!/usr/bin/env python3
from pathlib import Path

root = Path(__file__).resolve().parents[1]
src = root / "platform" / "frontend" / "src"
pkg = (root / "platform" / "frontend" / "package.json").read_text(encoding="utf-8")
header = (src / "components" / "layout" / "AppHeader.vue").read_text(encoding="utf-8")
sheet = (src / "components" / "ui" / "BaseSheet.vue").read_text(encoding="utf-8")
whatsapp = (src / "services" / "whatsapp.ts").read_text(encoding="utf-8")
wizard = (src / "features" / "orders" / "components" / "OrderWizard.vue").read_text(encoding="utf-8")
image = (src / "components" / "media" / "SmartImage.vue").read_text(encoding="utf-8")
css = (src / "styles" / "main.css").read_text(encoding="utf-8")
asset_doc = (root / "docs" / "ASSET-MANAGEMENT.md").read_text(encoding="utf-8")
launcher = (root / "t" / "index.htm").read_text(encoding="utf-8")

for package in ["vue", "vue-router", "pinia", "@lucide/vue", "tailwindcss", "vite", "vitest", "vue-tsc"]:
    assert f'"{package}"' in pkg, f"Missing frontend package {package}"

assert "session.logout()" in header, "Header must expose direct logout"
assert "session.logout()" in sheet, "Sheets must expose logout while authenticated"
assert "989381009231" in whatsapp, "Canonical seller WhatsApp number missing"
for path in ["simple", "available", "unavailable", "custom", "brand", "packaging"]:
    assert path in whatsapp, f"Missing request path {path}"
for label in ["تولید سفارشی", "تولید با برند", "تولید با بسته‌بندی"]:
    assert label in wizard, f"Production wizard missing {label}"
assert ("skeleton" in image and "@error" in image) or ("<svg" in image and "smart-placeholder-svg" in image), "SmartImage needs a resilient media fallback"
assert ".wa-card-action" in css and ".wa-primary" in css, "WhatsApp needs dedicated visual treatment"
assert "spatie/laravel-medialibrary" in asset_doc, "Media architecture must name primary media package"
assert launcher.index("./12/index.html") < launcher.index("./11/index.htm"), "Test 12 must be newest launcher entry"
print("Test 12 source contract: PASS")
