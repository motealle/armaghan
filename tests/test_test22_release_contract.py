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

assert tuple(map(int,pkg["version"].split("."))) >= (0,22,0)
assert tuple(map(int,lock["version"].split("."))) >= (0,22,0)
assert tuple(map(int,lock["packages"][""]["version"].split("."))) >= (0,22,0)
assert "../../t/22" in vite or "../../t/23" in vite or "../../t/24" in vite or "../../t/25" in vite

assert ("test22-build" in workflow and "path: t/22" in workflow) or ("test23-build" in workflow and "path: t/23" in workflow) or ("test24-build" in workflow and "path: t/24" in workflow) or ("test25-build" in workflow and "path: t/25" in workflow)
assert 'os.path.isdir("t/22")' in workflow or 'os.path.isdir("t/23")' in workflow or 'os.path.isdir("t/24")' in workflow or 'os.path.isdir("t/25")' in workflow
assert 'os.walk("t/22")' in workflow or 'os.walk("t/23")' in workflow or 'os.walk("t/24")' in workflow or 'os.walk("t/25")' in workflow
assert "test_test22_release_contract.py" in workflow
assert "Test Test 21 placeholder/admin contract" in workflow
assert "No remote files were deleted" in workflow
assert "deploy-root:" in workflow
assert "if: needs.plan.outputs.root == 'true'" in workflow

assert "21" in immutable and "22" in immutable
assert launcher.index("./22/index.html") < launcher.index("./21/index.html")
assert "Test 22" in backlog
assert "Tests 01–24 are frozen; current source target is Test 25." in rules or "Tests 01–23 are frozen; current source target is Test 24." in rules or "Tests 01–22 are frozen; current source target is Test 23." in rules or "Tests 01–21 are frozen; current source target is Test 22." in rules
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
    assert "armaghan:test22" in text or "armaghan:test23" in text or "armaghan:test24" in text or "armaghan:test25" in text, path
    assert "armaghan:test21" not in text, path

catalog = (SRC / "stores/catalog.ts").read_text(encoding="utf-8")
assert "const KEY='armaghan:test22:products-v1'" in catalog or "const KEY='armaghan:test23:products-v1'" in catalog or "const KEY='armaghan:test24:products-v1'" in catalog or "const KEY='armaghan:test25:products-v1'" in catalog
assert "'armaghan:test21:products-v1'" in catalog
if "const KEY='armaghan:test25:products-v1'" in catalog:
    assert "'armaghan:test24:products-v1'" in catalog
    assert catalog.index("'armaghan:test24:products-v1'") < catalog.index("'armaghan:test23:products-v1'")
elif "const KEY='armaghan:test24:products-v1'" in catalog:
    assert "'armaghan:test23:products-v1'" in catalog
    assert catalog.index("'armaghan:test23:products-v1'") < catalog.index("'armaghan:test22:products-v1'")
elif "const KEY='armaghan:test23:products-v1'" in catalog:
    assert "'armaghan:test22:products-v1'" in catalog
    assert catalog.index("'armaghan:test22:products-v1'") < catalog.index("'armaghan:test21:products-v1'")
else:
    assert catalog.index("'armaghan:test21:products-v1'") < catalog.index("'armaghan:test20:products-v4'")

session_spec = (SRC / "stores/session.spec.ts").read_text(encoding="utf-8")
assert "armaghan:test22:role" in session_spec or "armaghan:test23:role" in session_spec or "armaghan:test24:role" in session_spec or "armaghan:test25:role" in session_spec
assert "armaghan:test22:impersonation" in session_spec or "armaghan:test23:impersonation" in session_spec or "armaghan:test24:impersonation" in session_spec or "armaghan:test25:impersonation" in session_spec

print("Test 22 release contract: PASS")
