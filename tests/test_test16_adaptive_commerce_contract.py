#!/usr/bin/env python3
from pathlib import Path

ROOT=Path(__file__).resolve().parents[1]
src=ROOT/"platform/frontend/src"
css=(src/"styles/main.css").read_text(encoding="utf-8")
theme=(src/"stores/theme.ts").read_text(encoding="utf-8")
header=(src/"components/layout/AppHeader.vue").read_text(encoding="utf-8")
bottom=(src/"components/layout/BottomNav.vue").read_text(encoding="utf-8")
grid=(src/"features/catalog/components/ProductGrid.vue").read_text(encoding="utf-8")
card=(src/"features/catalog/components/ProductCard.vue").read_text(encoding="utf-8")
products=(src/"views/ProductsView.vue").read_text(encoding="utf-8")
launcher=(ROOT/"t/index.htm").read_text(encoding="utf-8")
immutable={line.strip() for line in (ROOT/"docs/IMMUTABLE-TESTS.txt").read_text(encoding="utf-8").splitlines() if line.strip() and not line.startswith("#")}

assert "@custom-variant dark" in css
for mode in ["'light'","'dark'"]:
    assert mode in theme
assert "localStorage" in theme
assert "ThemeSwitcher" in header
assert "desktop-nav-link" in header
assert "hidden lg:flex" in header or ("app-header-expanded" in header and "useResolvedAppearance" in header), "Desktop navigation must remain available; Test 26 may expose it per viewport policy"
assert ("md:hidden" in bottom) or ('v-show="profile===\'mobile\'"' in bottom and "useResolvedAppearance" in bottom), "Mobile bottom navigation must remain and hide from tablet upward"
assert "xl:grid-cols-4" in grid
assert "hidden" in products and "lg:block" in products and "lg:sticky" in products, "Desktop filter sidebar required"
assert "rounded-[.75rem]" in card or "border-radius:.75rem" in css
assert 'tone="white"' in card
assert "favorite-heart active" not in card, "Favorite should not recolor its container"
assert "@keyframes favorite-heartbeat" in css
assert "15" in immutable
assert launcher.index("./16/index.html") < launcher.index("./15/index.html")
print("Test 16 adaptive-commerce contract: PASS")
