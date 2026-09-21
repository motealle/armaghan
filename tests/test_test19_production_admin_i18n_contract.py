#!/usr/bin/env python3
from pathlib import Path
import json

ROOT=Path(__file__).resolve().parents[1]
src=ROOT/"platform/frontend/src"

pkg=json.loads((ROOT/"platform/frontend/package.json").read_text(encoding="utf-8"))
vite=(ROOT/"platform/frontend/vite.config.ts").read_text(encoding="utf-8")
index=(ROOT/"platform/frontend/index.html").read_text(encoding="utf-8")
workflow=(ROOT/".github/workflows/ftp-deploy.yml").read_text(encoding="utf-8")
launcher=(ROOT/"t/index.htm").read_text(encoding="utf-8")
immutable={line.strip() for line in (ROOT/"docs/IMMUTABLE-TESTS.txt").read_text(encoding="utf-8").splitlines() if line.strip() and not line.startswith("#")}
backlog=(ROOT/"docs/BACKLOG.md").read_text(encoding="utf-8")
audit=(ROOT/"docs/TEST19-UX-AUDIT.md").read_text(encoding="utf-8")

header=(src/"components/layout/AppHeader.vue").read_text(encoding="utf-8")
drawer=(src/"components/layout/MobileMenuDrawer.vue").read_text(encoding="utf-8")
sheet=(src/"components/ui/BaseSheet.vue").read_text(encoding="utf-8")
login=(src/"features/auth/components/LoginSheet.vue").read_text(encoding="utf-8")
admin=(src/"features/admin/components/AdminDashboard.vue").read_text(encoding="utf-8")
customer=(src/"features/admin/components/CustomerDetailSheet.vue").read_text(encoding="utf-8")
translation=(src/"features/admin/components/TranslationManager.vue").read_text(encoding="utf-8")
messages=(src/"i18n/messages.ts").read_text(encoding="utf-8")
locale=(src/"stores/locale.ts").read_text(encoding="utf-8")
customers=(src/"stores/customers.ts").read_text(encoding="utf-8")
favorites=(src/"stores/favorites.ts").read_text(encoding="utf-8")
session=(src/"stores/session.ts").read_text(encoding="utf-8")
media=(src/"features/catalog/components/ProductMediaCarousel.vue").read_text(encoding="utf-8")
css=(src/"styles/main.css").read_text(encoding="utf-8")
router=(src/"router/index.ts").read_text(encoding="utf-8")

assert tuple(map(int,pkg["version"].split("."))) >= (0,19,0)
assert "../../t/19" in vite
assert "Test 19" not in index, "Public document shell should look like a product, not a test"
assert "armaghan:test19:theme" in index
assert "test19-build" in workflow and "t/19" in workflow
assert "test_test19_production_admin_i18n_contract.py" in workflow
assert "18" in immutable
assert launcher.index("./19/index.html") < launcher.index("./18/index.html")

# Mobile/tablet shell
assert "header-action hidden lg:inline-flex" in header
assert "mobile-menu-trigger lg:hidden" in header
assert "language-square-button" in drawer
for token in ["Fa","En","ع","ک"]:
    assert token in drawer
assert "DrawerAccountDashboard" in drawer and "drawer-help-button" in drawer
assert "drawer-login-button" in drawer and "drawer-footer-brand" in drawer
assert "clamp(260px,70vw,340px)" in css
assert "rgba(5,9,24,.40)" in css
assert "drawer-enter-active" in css and "200ms" in css

# Sheet motion/accessibility
assert "<Transition name=\"sheet\">" in sheet
assert "sheet-enter-active" in css and "sheet-leave-active" in css
assert "transform:translate(-50%,100%)" in css
assert "prefers-reduced-motion" in css
assert "Escape" in sheet and "previousFocus" in sheet

# Product polish
assert "SVG" not in media
assert "product-placeholder-svg" in media
assert "chip-scroller" in css
assert "--bottom-nav-space" in css
assert '"Vazirmatn FD"' in css and "Roboto" in css
assert "site-footer" in css

# Admin CRM
for tab in ["adminOverview","adminCustomers","adminProducts","adminLanguages"]:
    assert tab in admin
assert "selectedCustomers" in admin and "selectedProducts" in admin
assert "bulkDeleteCustomers" in admin and "bulkDeleteProducts" in admin
assert "MoreVertical" in admin
assert "UserRoundCog" in admin
assert "CustomerDetailSheet" in admin
for field in ["email","whatsapp","address","location","notes","activeOrder","timelineStage","profileImage","accessMode"]:
    assert field in customer
assert "permanent" in customer and "expiring" in customer
assert "generateAccess" in customers and "revokeAccess" in customers

# Wishlist lead model
assert "visitor-token" in customers
assert "recordWishlistChange" in customers
assert "currentVisitorMessage" in customers
assert "recordWishlistChange" in favorites
assert "wishlistLeads" in admin
assert "window.open" in admin
assert "queueGuestMessage" in admin

# Auth adapters
assert "session.register" in login
assert "createMagicLink" in login
assert 'data-backend-endpoint="/auth/google/redirect"' in login
assert "googleBackendRequired" in login
assert "register(" in session
assert "createMagicLink" in session and "revokeMagicLink" in session and "consumeMagicLink" in session

# Managed i18n
for locale_id in ["fa:","ar:","en:","ku:"]:
    assert locale_id in messages
assert "translationGroups" in messages
assert "productNameTranslations" in messages and "specTranslations" in messages
assert "overrides" in locale and "setOverride" in locale and "resetOverrides" in locale
assert "TranslationManager" in admin
assert "translationSearch" in translation
assert "selected" in translation and "resetSelected" in translation
assert "document.documentElement.lang" in locale
assert "document.documentElement.dir" in locale
assert "html[data-locale=\"en\"]" in css

# No public developer credits route
assert "/credits" not in router

# Repository memory
assert "5-option" in audit or "۵" in audit or "رتبه" in audit
assert "Test 19" in backlog
print("Test 19 production/admin/i18n contract: PASS")
