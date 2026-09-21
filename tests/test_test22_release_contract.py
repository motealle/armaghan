#!/usr/bin/env python3
from pathlib import Path
import json

ROOT = Path(__file__).resolve().parents[1]
FRONTEND = ROOT / "platform/frontend"
SRC = FRONTEND / "src"

pkg = json.loads((FRONTEND / "package.json").read_text(encoding="utf-8"))
lock = json.loads((FRONTEND / "package-lock.json").read_text(encoding="utf-8"))
vite = (FRONTEND / "vite.config.ts").read_text(encoding="utf-8")
workflow = (ROOT / ".github/workflows/ftp-deploy.yml").read_text(encoding="utf-8")
launcher = (ROOT / "t/index.htm").read_text(encoding="utf-8")
immutable = {
    line.strip()
    for line in (ROOT / "docs/IMMUTABLE-TESTS.txt").read_text(encoding="utf-8").splitlines()
    if line.strip() and not line.lstrip().startswith("#")
}
backlog = (ROOT / "docs/BACKLOG.md").read_text(encoding="utf-8")
rules = (ROOT / "docs/PROJECT-RULES.md").read_text(encoding="utf-8")
release = (ROOT / "docs/TEST22-RELEASE.md").read_text(encoding="utf-8")

assert pkg["version"] == "0.22.0"
assert lock["version"] == "0.22.0"
assert lock["packages"][""]["version"] == "0.22.0"
assert "../../t/22" in vite
assert "../../t/21" not in vite

assert "test22-build" in workflow
assert "path: t/22" in workflow
assert 'os.path.isdir("t/22")' in workflow
assert 'os.walk("t/22")' in workflow
assert "test_test22_release_contract.py" in workflow
assert "Test Test 21 placeholder/admin contract" in workflow
assert "No remote files were deleted" in workflow
assert "deploy-root:" in workflow
assert "if: needs.plan.outputs.root == 'true'" in workflow

assert "21" in immutable
assert launcher.index("./22/index.html") < launcher.index("./21/index.html")
assert "Current implementation target: **Test 22" in backlog
assert "Tests 01–21 are frozen; current source target is Test 22." in rules
assert "Test 22" in release and "Test 21" in release

strict_test22_files = [
    FRONTEND / "index.html",
    SRC / "stores/session.ts",
    SRC / "stores/favorites.ts",
    SRC / "stores/customers.ts",
    SRC / "stores/design.ts",
    SRC / "stores/theme.ts",
    SRC / "stores/locale.ts",
]
for path in strict_test22_files:
    text = path.read_text(encoding="utf-8")
    assert "armaghan:test22" in text, path
    assert "armaghan:test21" not in text, path

catalog = (SRC / "stores/catalog.ts").read_text(encoding="utf-8")
assert "const KEY='armaghan:test22:products-v1'" in catalog
assert "'armaghan:test21:products-v1'" in catalog
assert catalog.index("'armaghan:test21:products-v1'") < catalog.index("'armaghan:test20:products-v4'")

session_spec = (SRC / "stores/session.spec.ts").read_text(encoding="utf-8")
assert "armaghan:test22:role" in session_spec
assert "armaghan:test22:impersonation" in session_spec

print("Test 22 release contract: PASS")
