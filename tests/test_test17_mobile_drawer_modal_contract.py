#!/usr/bin/env python3
from pathlib import Path

ROOT=Path(__file__).resolve().parents[1]
src=ROOT/"platform/frontend/src"
vite=(ROOT/"platform/frontend/vite.config.ts").read_text(encoding="utf-8")
header=(src/"components/layout/AppHeader.vue").read_text(encoding="utf-8")
drawer=(src/"components/layout/MobileMenuDrawer.vue").read_text(encoding="utf-8")
theme=(src/"components/layout/ThemeSwitcher.vue").read_text(encoding="utf-8")
login=(src/"features/auth/components/LoginSheet.vue").read_text(encoding="utf-8")
modal=(src/"components/ui/BaseModal.vue").read_text(encoding="utf-8")
card=(src/"features/catalog/components/ProductCard.vue").read_text(encoding="utf-8")
media=(src/"features/catalog/components/ProductMediaCarousel.vue").read_text(encoding="utf-8")
css=(src/"styles/main.css").read_text(encoding="utf-8")
pkg=(ROOT/"platform/frontend/package.json").read_text(encoding="utf-8")
launcher=(ROOT/"t/index.htm").read_text(encoding="utf-8")
immutable={line.strip() for line in (ROOT/"docs/IMMUTABLE-TESTS.txt").read_text(encoding="utf-8").splitlines() if line.strip() and not line.startswith("#")}

assert "../../t/17" in vite
assert "MobileMenuDrawer" in header
assert "Menu" in header and "mobile-menu-trigger" in header
assert "hidden lg:flex" in header, "Desktop navigation must remain desktop-only"
assert "desktop-nav-link" in header
assert "mobile-drawer-backdrop" in drawer and "mobile-drawer-panel" in drawer
assert "locale.setManual" in drawer
assert "session.logout" in drawer
assert "emit('login')" in drawer
assert "v-for="item in modes"" not in theme
assert "theme.setMode" in theme and "cycle" in theme
assert "BaseModal" in login and "BaseSheet" not in login
assert 'role="dialog"' in modal and 'aria-modal="true"' in modal
assert "modal-backdrop" in modal and "backdrop" in css
assert "PhotoSwipe" not in media
assert "<img" not in media
assert "<svg" in media
assert 'tone="white"' in card
assert ':size="27"' in card, "WhatsApp card icon should be about 20% larger than Test 16"
assert '"fetch:digikala"' in pkg, "Fetcher may remain available manually"
assert 'node scripts/fetch-digikala-assets.mjs &&' not in pkg, "Test 17 build must not depend on prototype photos"
assert "16" in immutable
assert launcher.index("./17/index.html") < launcher.index("./16/index.html")
print("Test 17 mobile drawer/modal contract: PASS")
