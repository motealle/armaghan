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
immutable={
    line.strip()
    for line in (ROOT/"docs/IMMUTABLE-TESTS.txt").read_text(encoding="utf-8").splitlines()
    if line.strip() and not line.lstrip().startswith("#")
}
backlog=(ROOT/"docs/BACKLOG.md").read_text(encoding="utf-8")
rules=(ROOT/"docs/PROJECT-RULES.md").read_text(encoding="utf-8")
audit=(ROOT/"docs/TEST26-UX-AUDIT.md").read_text(encoding="utf-8")
images=(ROOT/"docs/TEST26-IMAGE-REQUIREMENTS.md").read_text(encoding="utf-8")
questions=(ROOT/"docs/TEST26-CUSTOMER-QUESTIONS.md").read_text(encoding="utf-8")

appearance_types=(SRC/"types/appearance.ts").read_text(encoding="utf-8")
appearance_store=(SRC/"stores/appearance.ts").read_text(encoding="utf-8")
appearance_spec=(SRC/"stores/appearance.spec.ts").read_text(encoding="utf-8")
viewport=(SRC/"services/viewportProfile.ts").read_text(encoding="utf-8")
appearance_admin=(SRC/"features/admin/components/AppearanceSettings.vue").read_text(encoding="utf-8")
admin=(SRC/"features/admin/components/AdminDashboard.vue").read_text(encoding="utf-8")
messages=(SRC/"i18n/messages.ts").read_text(encoding="utf-8")
home=(SRC/"views/HomeView.vue").read_text(encoding="utf-8")
header=(SRC/"components/layout/AppHeader.vue").read_text(encoding="utf-8")
drawer=(SRC/"components/layout/MobileMenuDrawer.vue").read_text(encoding="utf-8")
products=(SRC/"views/ProductsView.vue").read_text(encoding="utf-8")
favorites=(SRC/"views/FavoritesView.vue").read_text(encoding="utf-8")
app=(SRC/"App.vue").read_text(encoding="utf-8")
css=(SRC/"styles/main.css").read_text(encoding="utf-8")
hero_section=(SRC/"features/home/components/HeroSection.vue").read_text(encoding="utf-8")
about=(SRC/"features/home/components/AboutArmaghanSection.vue").read_text(encoding="utf-8")
why=(SRC/"features/home/components/WhyArmaghanSection.vue").read_text(encoding="utf-8")
capabilities=(SRC/"features/home/components/CapabilitiesSection.vue").read_text(encoding="utf-8")
banners=(SRC/"features/home/components/ProductCategoryBanners.vue").read_text(encoding="utf-8")
site_footer=(SRC/"components/layout/SiteFooter.vue").read_text(encoding="utf-8")
share=(SRC/"features/favorites/shareFavorites.ts").read_text(encoding="utf-8")
share_spec=(SRC/"features/favorites/shareFavorites.spec.ts").read_text(encoding="utf-8")
resolved=(SRC/"composables/useResolvedAppearance.ts").read_text(encoding="utf-8")
smart_image=(SRC/"components/media/SmartImage.vue").read_text(encoding="utf-8")
frontend_index=(FRONTEND/"index.html").read_text(encoding="utf-8")
bottom_nav=(SRC/"components/layout/BottomNav.vue").read_text(encoding="utf-8")
product_card=(SRC/"features/catalog/components/ProductCard.vue").read_text(encoding="utf-8")

assert pkg["version"]=="0.26.0"
assert lock["version"]=="0.26.0" and lock["packages"][""]["version"]=="0.26.0"
assert "../../t/26" in vite
assert "test26-build" in workflow and "path: t/26" in workflow
assert 'os.path.isdir("t/26")' in workflow and 'os.walk("t/26")' in workflow
assert "test_test26_appearance_contract.py" in workflow
assert "25" in immutable
assert launcher.index("./26/index.html?build=test26-footer-language-r8a") < launcher.index("./25/index.html")
assert "Current implementation target: **Test 26" in backlog
assert "rollback/test25-pre-test26" in backlog
assert "Tests 01–25 are frozen" in rules
assert "repository is the operational project memory" in rules.lower()
assert "Communication terminology rule" in rules
assert "first use of each specialist/technical term" in rules
assert "hard-coding is a fixed value/decision embedded directly in code" in rules
assert audit.count("| 1 |") >= 7
assert "placehold.co" in images and "T26-HERO-01" in images
assert "سوال" in questions or "سؤال" in questions

for marker in [
    "ViewportProfile='mobile'|'tablet'|'desktop'",
    "HeaderMode='compact-drawer'|'expanded'",
    "HeroMode='single'|'carousel'",
    "HomeProductGridMode='hidden'|'recommended-6'",
]:
    assert marker in appearance_types

for marker in [
    "APPEARANCE_KEY='armaghan:test26:appearance'",
    "customerAppearanceDefaults",
    "sanitizeViewportAppearance",
    "sanitizeAppearanceProfiles",
    "resetProfile",
    "resetAll",
    "homeProductGrid:'hidden'",
    "heroMode:'single'",
    "showCategoryNumbers:false",
]:
    assert marker in appearance_store

for marker in [
    "TABLET_QUERY='(min-width: 48rem)'",
    "DESKTOP_QUERY='(min-width: 64rem)'",
    "profileForWidth",
    "subscribeViewportProfile",
]:
    assert marker in viewport

for marker in [
    "viewportMobile",
    "viewportTablet",
    "viewportDesktop",
    "headerModeLabel",
    "heroModeLabel",
    "homeProductGridLabel",
    "resetAllCustomerDefaults",
]:
    assert marker in appearance_admin
    assert marker in messages

assert "setHeaderMode" in appearance_admin
assert '@change="setHeaderMode"' in appearance_admin
assert ':checked="current.showHamburger" @change="setBoolean(\'showHamburger\',$event)"' in appearance_admin
assert "AppearanceSettings" in admin
assert "adminAppearance" in admin
assert "appearance" in admin
assert "profileForWidth(767)" in appearance_spec
assert "profileForWidth(768)" in appearance_spec
assert "profileForWidth(1024)" in appearance_spec
assert 'v-show="profile===\'mobile\'"' in bottom_nav
assert 'class="bottom-nav"' in bottom_nav
assert 'data-pwa-bottom-nav="true"' in bottom_nav
assert "useResolvedAppearance" in bottom_nav
assert "APPEARANCE_SCHEMA_KEY='armaghan:test26:appearance-schema'" in appearance_store
assert "APPEARANCE_SCHEMA_VERSION='4'" in appearance_store
assert "migrateLegacyProfiles" in appearance_store
assert "mobile:{...mobile,headerMode:'compact-drawer',showHamburger:false}" in appearance_store
assert "tablet:{...tablet,headerMode:'expanded',showHamburger:false}" in appearance_store
assert "desktop:{...desktop,headerMode:'expanded',showHamburger:false}" in appearance_store
assert "enforceNavigationInvariant" not in appearance_store
assert "tablet:{" in appearance_store and "headerMode:'expanded'" in appearance_store
assert "@media(min-width:768px){" in css
assert ":root{--bottom-nav-space:0rem}" in css
assert ":root{--bottom-nav-space:7.2rem}" not in css
assert ".bottom-nav{left:50%;right:auto;bottom:1rem;" not in css

strict_test26_files=[
    FRONTEND/"index.html",
    SRC/"stores/session.ts",
    SRC/"stores/favorites.ts",
    SRC/"stores/customers.ts",
    SRC/"stores/design.ts",
    SRC/"stores/theme.ts",
    SRC/"stores/locale.ts",
]
for path in strict_test26_files:
    text=path.read_text(encoding="utf-8")
    assert "armaghan:test26" in text, path
    assert "armaghan:test25" not in text, path

catalog=(SRC/"stores/catalog.ts").read_text(encoding="utf-8")
assert "const KEY='armaghan:test26:products-v1'" in catalog
assert "'armaghan:test25:products-v1'" in catalog
assert catalog.index("'armaghan:test25:products-v1'") < catalog.index("'armaghan:test24:products-v1'")

session_spec=(SRC/"stores/session.spec.ts").read_text(encoding="utf-8")
assert "armaghan:test26:role" in session_spec
assert "armaghan:test26:impersonation" in session_spec

# Customer-approved Test 26 integration: every high-value setting has a public consumer.
assert "useResolvedAppearance" in resolved and "subscribeViewportProfile" in resolved
assert "HeroSection" in home
for component in ["AboutArmaghanSection","WhyArmaghanSection","CapabilitiesSection","ProductCategoryBanners"]:
    assert component in home
for marker in ["policy.showAbout","policy.showWhy","policy.showCapabilities","policy.showProductBanners","policy.homeProductGrid==='recommended-6'"]:
    assert marker in home
assert "HeroCarousel" in hero_section and "policy.heroMode==='carousel'" in hero_section
assert "heroSingleSlogan" in hero_section
assert "aboutArmaghanText" in about and "test26Media.about" in about
assert "whyCapacityTitle" in why and "whyMarketTitle" in why
home26=(SRC/"data/home26.ts").read_text(encoding="utf-8")
assert "AdaptivePanel" in capabilities and "capabilityMore" in capabilities
assert "placehold.co" in home26 and "test26Media" in home26 and "productBannerMedia" in home26
assert "category:category.code" in banners and "productBannerMedia" in banners

for marker in ["policy.headerMode==='expanded'","policy.showBrandText","policy.showLanguage","policy.showHelp","policy.showAccount","policy.showHamburger"]:
    assert marker in header
assert "mobile-header-language" in header
assert "profile==='mobile'&&policy.headerMode!=='expanded'" in header
assert ':show-language="policy.showLanguage&&profile!==\'mobile\'"' in header
for marker in [':show-help="policy.showHelp"',':show-account="policy.showAccount"',':show-brand-text="policy.showBrandText"']:
    assert marker in header
assert "props.showLanguage" in drawer and "props.showHelp" in drawer and "props.showAccount" in drawer
assert 'v-if="policy.showCategoryNumbers"' in products
assert "products-intro-surface" in products and ".products-intro-surface" in css
assert "SiteFooter" in app and "const showFooter=computed" in app
assert "policy.value.showFooter" in app
assert "profile.value!=='mobile'||route.path==='/'" in app
assert 'v-if="showFooter"' in app
assert "test26-site-footer" in site_footer and "footerSalesTitle" in site_footer

# Favorites share carries product codes only, is versioned, and has native-share + copy fallback.
assert "FAVORITES_SHARE_PREFIX='v1:'" in share
assert "buildFavoritesShareUrl" in share and "decodeFavoriteCodes" in share
assert "navigator.share" in favorites and "navigator.clipboard" in favorites
assert "favorites.items.map(product=>product.code)" in favorites
assert "sharedFavoritesPrivacy" in favorites
assert "not.toContain('name=')" in share_spec and "not.toContain('email=')" in share_spec

for key in [
    "aboutArmaghanText","whyArmaghanTitle","capabilitiesTitle","productBannersTitle",
    "shareFavorites","sharedFavoritesTitle","footerSalesTitle",
]:
    assert key in messages

assert "KNOWN_AVIF_PREFIXES" in smart_image
assert "!KNOWN_AVIF_PREFIXES.some" in smart_image
avif_registry=smart_image.split("KNOWN_AVIF_PREFIXES=[",1)[1].split("] as const",1)[0]
assert "/images/test26/" not in avif_registry
for filename in [
    "hero-selected.webp","about-armaghan.webp","capability-production.webp","capability-export-prep.webp",
    "capability-documents.webp","banner-baby.webp","banner-kids.webp","banner-women.webp",
]:
    assert (FRONTEND/"public/images/test26/home"/filename).is_file(), filename
assert 'name="armaghan-build" content="test26-footer-language-r8a"' in frontend_index
assert "/* Test 26 — customer-approved reversible landing integration */" in css
assert "Test 26 mobile UX polish" in css
assert ".product-card-title{text-align:center}" in css
assert ".product-code-row{justify-content:center}" in css
assert ".products-page .filter-chip.active" in css and "color:var(--c-text)" in css
assert "@media(max-width:767.98px)" in css
assert "width:calc(100% + 1.5rem)" in css
assert "margin-inline:-.75rem" in css
assert "margin-bottom:-1rem" in css
assert "@media(min-width:768px)" in css and ".bottom-nav{display:none!important}" in css
assert "rollback/test26-foundation-pre-integration" in audit or "Run 2" in audit
assert "Test 26 Run 8" in backlog
assert "Run 8 — mobile footer scope, top language, terminology rule" in audit

print("Test 26 reversible appearance + customer integration contract: PASS")
