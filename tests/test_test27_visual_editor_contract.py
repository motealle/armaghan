#!/usr/bin/env python3
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
SRC = ROOT / "platform/frontend/src"
EDITOR = SRC / "features/visual-editor"

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

tokens = (EDITOR / "tokenRegistry.ts").read_text(encoding="utf-8")
for color in ("#21946A", "#151EDA", "#C8E3DB", "#FFFFFF", "#FFB514"):
    assert color in tokens

contrast = (EDITOR / "contrast.ts").read_text(encoding="utf-8")
assert "NORMAL_TEXT_MIN_CONTRAST=4.5" in contrast
assert "tokenContrastRatio" in contrast
assert "passesNormalTextContrast" in contrast
assert (EDITOR / "contrast.spec.ts").exists()

selection = (EDITOR / "selection.ts").read_text(encoding="utf-8")
selection_composable = (EDITOR / "composables/useVisualEditorSelection.ts").read_text(encoding="utf-8")
sheet_composable = (EDITOR / "composables/useResizableEditorSheet.ts").read_text(encoding="utf-8")
assert "document.elementsFromPoint" in selection
assert "data-visual-editor-ui" in selection
assert "result.length>=8" in selection
assert "addListeners" in selection_composable and "removeListeners" in selection_composable
assert "watch(enabled" in selection_composable
assert "setPointerCapture" in sheet_composable
assert "visual-editor-active" in sheet_composable

editor = (EDITOR / "VisualEditor.vue").read_text(encoding="utf-8")
chooser = (EDITOR / "VisualEditorTargetChooser.vue").read_text(encoding="utf-8")
inspector = (EDITOR / "VisualEditorInspector.vue").read_text(encoding="utf-8")
quick_launcher = (EDITOR / "VisualEditorQuickLauncher.vue").read_text(encoding="utf-8")
target_browser = (EDITOR / "VisualEditorTargetBrowser.vue").read_text(encoding="utf-8")
target_registry = (EDITOR / "targetRegistry.ts").read_text(encoding="utf-8")
style_api = (EDITOR / "services/styleProfileApi.ts").read_text(encoding="utf-8")
sync_composable = (EDITOR / "composables/useVisualProfileSync.ts").read_text(encoding="utf-8")
public_baseline = (EDITOR / "composables/usePublicVisualProfileBaseline.ts").read_text(encoding="utf-8")
sync_panel = (EDITOR / "VisualEditorSyncPanel.vue").read_text(encoding="utf-8")
assert "visual-editor-sheet" in editor
assert "useVisualEditorSelection" in editor
assert "useResizableEditorSheet" in editor
assert "VisualEditorTargetChooser" in editor
assert "VisualEditorInspector" in editor
assert "VisualEditorTargetBrowser" in editor
assert "VisualEditorSyncPanel" in editor
assert "useVisualProfileSync" in editor
assert "browserOpen=ref(true)" in editor
assert "ذخیره خودکار" in editor
assert "visual.setEnabled(false)" in editor
assert "چند عنصر نزدیک" in chooser
assert "عناصر مخفی‌شده" in chooser
assert "wouldFailContrast" in inspector
assert "4.5:1" in inspector
assert "ویرایش ظاهر" in quick_launcher
assert "visual.setEnabled(true)" in quick_launcher
assert "router.push('/')" in quick_launcher
assert "انتخاب منظم عناصر" in target_browser
assert "credentials:'same-origin'" in style_api
assert "X-XSRF-TOKEN" in style_api
assert "expected_checksum" in style_api
assert "saveAdminStyleProfileDraft" in sync_composable
assert "state.value='conflict'" in sync_composable
assert "window.setTimeout(()=>{void saveNow()},900)" in sync_composable
assert "بارگذاری نسخه سرور" in sync_panel
assert "انتشار نسخه فعلی در staging" in sync_panel
assert "fetchPublicStyleProfile('staging'" in public_baseline
assert "toggleVisibility" in target_browser
for target_id in ("header.shell","hero.title","home.about.title","home.why.title","home.capabilities.title","home.product-banners.title","product.card","footer.shell"):
    assert target_id in target_registry

profile = (EDITOR / "store.ts").read_text(encoding="utf-8")
assert "armaghan:test27:visual-style-profile:v1" in profile
assert "armaghan:test27:visual-editor-enabled" in profile
assert "css:compileCss(value)" in profile
assert "texts:Record" in profile
assert "isBrandTokenId" in profile

app = (SRC / "App.vue").read_text(encoding="utf-8")
assert 'session.isAdmin&&!session.impersonatedCustomerId' in app
assert "<VisualStyleRuntime/>" in app
assert "<VisualEditorQuickLauncher/>" in app

appearance = (SRC / "features/admin/components/AppearanceSettings.vue").read_text(encoding="utf-8")
assert "openVisualEditor" in appearance
assert "visual.setEnabled(true)" in appearance

header = (SRC / "components/layout/AppHeader.vue").read_text(encoding="utf-8")
footer = (SRC / "components/layout/SiteFooter.vue").read_text(encoding="utf-8")
hero = (SRC / "features/home/components/HeroSection.vue").read_text(encoding="utf-8")
product = (SRC / "features/catalog/components/ProductCard.vue").read_text(encoding="utf-8")
about = (SRC / "features/home/components/AboutArmaghanSection.vue").read_text(encoding="utf-8")
why = (SRC / "features/home/components/WhyArmaghanSection.vue").read_text(encoding="utf-8")
capabilities = (SRC / "features/home/components/CapabilitiesSection.vue").read_text(encoding="utf-8")
banners = (SRC / "features/home/components/ProductCategoryBanners.vue").read_text(encoding="utf-8")
css = (SRC / "styles/main.css").read_text(encoding="utf-8")
runtime = (EDITOR / "VisualStyleRuntime.vue").read_text(encoding="utf-8")
launcher = (ROOT / "t/index.htm").read_text(encoding="utf-8")

assert 'data-style-id="header.shell"' in header
assert "var(--role-brand-chrome)" in header
assert 'data-style-id="footer.shell"' in footer
assert 'data-style-id="hero.title"' in hero and 'data-editable-text="true"' in hero
assert 'data-style-id="product.card"' in product and 'data-style-id="product.title"' in product
assert 'data-style-id="home.about.title"' in about and 'data-editable-text="true"' in about
assert 'data-style-id="home.why.title"' in why and 'data-style-id="home.why.list"' in why
assert 'data-style-id="home.capabilities.title"' in capabilities
assert 'home.capability.${item.id}' in capabilities
assert 'data-style-id="home.product-banners.title"' in banners
assert 'home.product-banner.${category.code}' in banners
assert "--role-brand-chrome:var(--brand-blue)" in css
assert "background:var(--role-brand-chrome)" in css
assert "html.visual-editor-active .bottom-nav{display:none!important}" in css
assert "usePublicVisualProfileBaseline" in runtime
assert ".visual-editor-sync-panel{" in css
assert './27/index.html' in launcher
assert launcher.index("./27/index.html") < launcher.index("./26/index.html")

immutable = {
    line.strip()
    for line in (ROOT / "docs/IMMUTABLE-TESTS.txt").read_text(encoding="utf-8").splitlines()
    if line.strip() and not line.lstrip().startswith("#")
}
assert "26" in immutable

print("Test 27 visual editor foundation contract: PASS")
