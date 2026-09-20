#!/usr/bin/env python3
from pathlib import Path

ROOT=Path(__file__).resolve().parents[1]
src=ROOT/"platform/frontend/src"
vite=(ROOT/"platform/frontend/vite.config.ts").read_text(encoding="utf-8")
css=(src/"styles/main.css").read_text(encoding="utf-8")
theme=(src/"stores/theme.ts").read_text(encoding="utf-8")
header=(src/"components/layout/AppHeader.vue").read_text(encoding="utf-8")
bottom=(src/"components/layout/BottomNav.vue").read_text(encoding="utf-8")
grid=(src/"features/catalog/components/ProductGrid.vue").read_text(encoding="utf-8")
card=(src/"features/catalog/components/ProductCard.vue").read_text(encoding="utf-8")
media=(src/"features/catalog/components/ProductMediaCarousel.vue").read_text(encoding="utf-8")
products=(src/"views/ProductsView.vue").read_text(encoding="utf-8")
manifest=(ROOT/"platform/frontend/public/manifest.webmanifest").read_text(encoding="utf-8")
launcher=(ROOT/"t/index.htm").read_text(encoding="utf-8")
immutable={line.strip() for line in (ROOT/"docs/IMMUTABLE-TESTS.txt").read_text(encoding="utf-8").splitlines() if line.strip() and not line.startswith("#")}

assert "../../t/16" in vite
assert "Test 16" in manifest
assert "@custom-variant dark" in css
for mode in ["'system'","'light'","'dark'"]:
    assert mode in theme
assert "prefers-color-scheme: dark" in theme
assert "localStorage" in theme
assert "ThemeSwitcher" in header
assert "hidden lg:flex" in header, "Desktop navigation should appear only at desktop breakpoint"
assert "lg:hidden" in bottom, "Mobile/tablet bottom navigation must remain but hide on desktop"
assert "xl:grid-cols-4" in grid
assert "hidden" in products and "lg:block" in products and "lg:sticky" in products, "Desktop filter sidebar required"
assert "rounded-[.75rem]" in card or "border-radius:.75rem" in css
assert 'tone="white"' in card
assert "favorite-heart active" not in card, "Favorite should not recolor its container"
assert "@keyframes favorite-heartbeat" in css
assert "object-contain" in media
assert "backdrop-blur" in media or "blur-" in media
assert "15" in immutable
assert launcher.index("./16/index.html") < launcher.index("./15/index.html")
print("Test 16 adaptive-commerce contract: PASS")
