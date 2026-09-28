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

assert pkg["version"]=="0.26.0"
assert lock["version"]=="0.26.0" and lock["packages"][""]["version"]=="0.26.0"
assert "../../t/26" in vite
assert "test26-build" in workflow and "path: t/26" in workflow
assert 'os.path.isdir("t/26")' in workflow and 'os.walk("t/26")' in workflow
assert "test_test26_appearance_contract.py" in workflow
assert "25" in immutable
assert launcher.index("./26/index.html") < launcher.index("./25/index.html")
assert "Current implementation target: **Test 26" in backlog
assert "rollback/test25-pre-test26" in backlog
assert "Tests 01–25 are frozen" in rules
assert "repository is the operational project memory" in rules.lower()
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

assert "AppearanceSettings" in admin
assert "adminAppearance" in admin
assert "appearance" in admin
assert "profileForWidth(767)" in appearance_spec
assert "profileForWidth(768)" in appearance_spec
assert "profileForWidth(1024)" in appearance_spec

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

print("Test 26 reversible appearance foundation contract: PASS")
