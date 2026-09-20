#!/usr/bin/env python3
from pathlib import Path
import json

ROOT=Path(__file__).resolve().parents[1]
src=ROOT/"platform/frontend/src"
vite=(ROOT/"platform/frontend/vite.config.ts").read_text(encoding="utf-8")
pkg=json.loads((ROOT/"platform/frontend/package.json").read_text(encoding="utf-8"))
index=(ROOT/"platform/frontend/index.html").read_text(encoding="utf-8")
header=(src/"components/layout/AppHeader.vue").read_text(encoding="utf-8")
drawer=(src/"components/layout/MobileMenuDrawer.vue").read_text(encoding="utf-8")
dashboard=(src/"components/layout/DrawerAccountDashboard.vue").read_text(encoding="utf-8")
theme=(src/"stores/theme.ts").read_text(encoding="utf-8")
switcher=(src/"components/layout/ThemeSwitcher.vue").read_text(encoding="utf-8")
design=(src/"stores/design.ts").read_text(encoding="utf-8")
admin=(src/"features/admin/components/AdminDashboard.vue").read_text(encoding="utf-8")
card=(src/"features/catalog/components/ProductCard.vue").read_text(encoding="utf-8")
bottom=(src/"components/layout/BottomNav.vue").read_text(encoding="utf-8")
css=(src/"styles/main.css").read_text(encoding="utf-8")
locale=(src/"stores/locale.ts").read_text(encoding="utf-8")
pics=(ROOT/"pics.md").read_text(encoding="utf-8")
launcher=(ROOT/"t/index.htm").read_text(encoding="utf-8")
immutable={line.strip() for line in (ROOT/"docs/IMMUTABLE-TESTS.txt").read_text(encoding="utf-8").splitlines() if line.strip() and not line.startswith("#")}

assert "../../t/18" in vite
assert tuple(map(int,pkg["version"].split("."))) >= (0,18,0)
assert "z-[90]" in header
assert "hidden lg:flex" in header and "lg:justify-self-center" in header
for icon in ["House","Grid2X2","WandSparkles","Heart","ClipboardList"]:
    assert icon in header
assert "desktop-nav-link" in header
assert "header-select" in header and "hidden lg:block" in header
assert "mobile-menu-trigger lg:hidden" in header

assert "RouterLink v-for" not in drawer, "Primary navigation must not be duplicated in the drawer"
for token in ["Fa","En","ع","ک"]:
    assert token in drawer
assert "language-round-button" in drawer
assert 'lang="item.lang"' in drawer
assert "DrawerAccountDashboard" in drawer
assert "drawer-account-footer" in drawer
assert "demoCustomers" in drawer and "profile" in drawer
assert "session.logout" in drawer and "emit('login')" in drawer
assert "drawer-metric-grid" in dashboard
for phrase in ["activeOrders","favorites.ids.length","nextCustomerAction","catalog.items.length"]:
    assert phrase in dashboard

assert "ThemeMode='light'|'dark'" in theme
assert "'system'" not in theme
assert "prefers-color-scheme: dark" in theme
assert "theme.toggle" in switcher
assert "Monitor" not in switcher

assert "cardActionMode" in design and "'compact'" in design and "'labeled'" in design
assert "cardActionMode" in admin
assert "compactActions" in card
assert "سفارش" in card
assert "v-if=\"!compactActions\"" in card
assert "compactActions ? 22 : 27" in card
assert "compactActions ? 16 : 20" in card

assert "rgba(5,9,24,.40)" in css
assert "60vw" in css and "20rem" in css
assert "box-shadow:0 -6px 18px rgba(17,24,39,.20)" in css
assert '"Vazirmatn FD"' in css
assert 'html[lang="en"] body' in css and "Roboto" in css
assert "Vazirmatn-FD-font-face.css" in index
assert "fonts.googleapis.com" in index and "Roboto" in index
assert "armaghan:test18:theme" in index
assert "system" not in index.split("armaghan:test18:theme",1)[1].split("</script>",1)[0]

assert "document.documentElement.lang" in locale
assert "lg:hidden" in bottom
assert "pics.md" not in pics  # sanity: actual content, not a self-link placeholder
for heading in ["ابعاد پیشنهادی","فرمت نهایی","مسیر ذخیره‌سازی در ریپو","نام فایل","پرامپت تولید تصویر به فارسی"]:
    assert heading in pics
for planned in ["hero-brand.avif","category-baby.avif","category-kids.avif","category-women-modest.avif","hero-production.avif","hero-export.avif"]:
    assert planned in pics

assert "17" in immutable
assert launcher.index("./18/index.html") < launcher.index("./17/index.html")
assert "Vazirmatn-FD-font-face.css" in launcher
print("Test 18 app-navigation/drawer-dashboard contract: PASS")
