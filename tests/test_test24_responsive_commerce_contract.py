#!/usr/bin/env python3
from pathlib import Path
import json

ROOT=Path(__file__).resolve().parents[1]
FRONTEND=ROOT/"platform/frontend"
SRC=FRONTEND/"src"

pkg=json.loads((FRONTEND/"package.json").read_text(encoding="utf-8"))
lock=json.loads((FRONTEND/"package-lock.json").read_text(encoding="utf-8"))
vite=(FRONTEND/"vite.config.ts").read_text(encoding="utf-8")
workflow=(ROOT/".github/workflows/ftp-deploy.yml").read_text(encoding="utf-8")
launcher=(ROOT/"t/index.htm").read_text(encoding="utf-8")
immutable={line.strip() for line in (ROOT/"docs/IMMUTABLE-TESTS.txt").read_text(encoding="utf-8").splitlines() if line.strip() and not line.lstrip().startswith("#")}
backlog=(ROOT/"docs/BACKLOG.md").read_text(encoding="utf-8")
rules=(ROOT/"docs/PROJECT-RULES.md").read_text(encoding="utf-8")
audit=(ROOT/"docs/TEST24-UX-AUDIT.md").read_text(encoding="utf-8")
google=(ROOT/"docs/GOOGLE-OAUTH-SETUP.md").read_text(encoding="utf-8")

smart=(SRC/"components/media/SmartImage.vue").read_text(encoding="utf-8")
media=(SRC/"features/catalog/components/ProductMediaCarousel.vue").read_text(encoding="utf-8")
hero=(SRC/"features/home/components/HeroCarousel.vue").read_text(encoding="utf-8")
home=(SRC/"views/HomeView.vue").read_text(encoding="utf-8")
products=(SRC/"views/ProductsView.vue").read_text(encoding="utf-8")
css=(SRC/"styles/main.css").read_text(encoding="utf-8")
admin=(SRC/"features/admin/components/AdminDashboard.vue").read_text(encoding="utf-8")
home_editor=(SRC/"features/admin/components/HomeContentEditor.vue").read_text(encoding="utf-8")
customers=(SRC/"stores/customers.ts").read_text(encoding="utf-8")
session=(SRC/"stores/session.ts").read_text(encoding="utf-8")
login=(SRC/"features/auth/components/LoginSheet.vue").read_text(encoding="utf-8")
detail=(SRC/"features/admin/components/CustomerDetailSheet.vue").read_text(encoding="utf-8")
messages=(SRC/"i18n/messages.ts").read_text(encoding="utf-8")
index=(FRONTEND/"index.html").read_text(encoding="utf-8")
app=(SRC/"App.vue").read_text(encoding="utf-8")

assert tuple(map(int,pkg["version"].split("."))) >= (0,24,0)
assert tuple(map(int,lock["version"].split("."))) >= (0,24,0) and tuple(map(int,lock["packages"][""]["version"].split("."))) >= (0,24,0)
assert "../../t/24" in vite or "../../t/25" in vite
assert ("test24-build" in workflow and "path: t/24" in workflow) or ("test25-build" in workflow and "path: t/25" in workflow)
assert ('os.path.isdir("t/24")' in workflow and 'os.walk("t/24")' in workflow) or ('os.path.isdir("t/25")' in workflow and 'os.walk("t/25")' in workflow)
assert "test_test24_responsive_commerce_contract.py" in workflow
assert "23" in immutable and "24" in immutable
assert launcher.index("./24/index.html") < launcher.index("./23/index.html")
assert "Test 24" in backlog
assert "Tests 01–24 are frozen; current source target is Test 25." in rules or "Tests 01–23 are frozen; current source target is Test 24." in rules
assert audit.count("| 1 |") >= 10

# Full-image product media without destructive crop.
assert "fit?:'cover'|'contain'|'contain-blur'" in smart or "'edge-extend'" in smart
assert "smart-image-backdrop" in smart and "smart-image-contained" in smart
assert "object-contain" in smart
assert 'fit="contain-blur"' in media or 'fit="edge-extend"' in media
assert ".smart-image-backdrop" in css and ".smart-image-contained" in css

# Mobile hero is structurally split; desktop changes composition.
assert "hero-media-pane" in hero and "hero-copy-pane" in hero
assert "absolute inset-0 z-40 flex items-end" not in hero
assert ".hero-copy-pane" in css
assert "grid-template-columns:minmax(0,1.38fr)" in css
assert ".hero-media-pane .smart-image" in css

# Home categories use the same image-first visual language as Products.
assert "home-category-grid" in home
assert "category-card home-category-card" in home
assert '<img :src="category.image"' in home
assert 'SmartImage :src="category.image"' not in home
assert "category-showcase" in products

# Dark theme: neutral layered surfaces, while brand primary remains canonical.
assert "--c-primary:#151EDA" in css
dark_tokens = ["--c-bg:#08090c","--c-surface:#111319","--c-surface-2:#171a22","--c-border:#292d36"] if "--c-bg:#08090c" in css else ["--c-bg:#151618","--c-surface:#1e2024","--c-surface-2:#25282d","--c-border:#383c43"]
for token in dark_tokens:
    assert token in css
assert "html.dark body" in css

# English typography + true LTR structures.
assert "family=Inter:wght@300;400;500;600;700" in index
assert "font-family:Inter,Roboto" in css
assert 'html[data-locale="en"] .category-showcase' in css
assert 'html[data-locale="en"] .products-page' in css
assert 'html[data-locale="en"] :where(.font-black)' in css

# Wider desktop rhythm.
assert 'max-w-[1500px]' in app
assert ".home-about-layout" in css and ".home-trust-card" in css
assert ".products-page .category-card" in css

# Dedicated editable home content in admin using the existing translation override store.
assert "HomeContentEditor" in admin
assert "'content'" in admin and "FilePenLine" in admin
for key in ["manufacturer","brandIntro","brandCapabilities","brandCapabilitiesText","brandDocuments","brandDocumentsText","brandSales","brandSalesText"]:
    assert key in home_editor
assert "locale.setOverride" in home_editor and "locale.resetOverride" in home_editor

# Manual customer add: name + either email or mobile, then open Customer 360.
assert "contactIdentifierRequired" in admin
assert "if(!email&&!whatsapp)" in admin
assert "const id=customers.add" in admin
assert "customerDetailId.value=id" in admin
assert "email?:string;whatsapp?:string" in customers
assert "return id" in customers

# Customer login identifier can be email or normalized mobile.
assert "function normalizePhone" in session
assert "emailMatches" in session and "phoneMatches" in session
assert "normalizePhone(item.whatsapp)" in session
assert "loginIdentifier" in login and "loginIdentifier" in detail
assert "contactIdentifierRequired" in messages

# Google remains server-side Socialite, never fake browser auth.
assert "composer require laravel/socialite" in google
assert "GOOGLE_CLIENT_SECRET" in google
assert "/auth/google/redirect" in google and "/auth/google/callback" in google
assert "client secret" in google.lower()
assert 'data-backend-endpoint="/auth/google/redirect"' in login

# Test 24 storage isolation and catalog migration.
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
    assert "armaghan:test24" in text or "armaghan:test25" in text
    assert "armaghan:test23" not in text

catalog_store=(SRC/"stores/catalog.ts").read_text(encoding="utf-8")
assert "const KEY='armaghan:test24:products-v1'" in catalog_store or "const KEY='armaghan:test25:products-v1'" in catalog_store
assert "'armaghan:test23:products-v1'" in catalog_store
if "const KEY='armaghan:test25:products-v1'" in catalog_store:
    assert "'armaghan:test24:products-v1'" in catalog_store

print("Test 24 responsive commerce/admin content contract: PASS")
