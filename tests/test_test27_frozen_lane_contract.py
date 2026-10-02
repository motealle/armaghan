#!/usr/bin/env python3
"""Historical Test27 boundary contract, resolved against the current active lane."""
from pathlib import Path
import re

ROOT=Path(__file__).resolve().parents[1]
immutable={
    line.strip()
    for line in (ROOT/"docs/IMMUTABLE-TESTS.txt").read_text(encoding="utf-8").splitlines()
    if line.strip() and not line.lstrip().startswith("#")
}
workflow=(ROOT/".github/workflows/ftp-deploy.yml").read_text(encoding="utf-8")
vite=(ROOT/"platform/frontend/vite.config.ts").read_text(encoding="utf-8")
launcher=(ROOT/"t/index.htm").read_text(encoding="utf-8")

assert "27" in immutable
active_match = re.search(r"ACTIVE_UI_TEST:\s*'(\d{2})'", workflow)
assert active_match is not None
active = int(active_match.group(1))
assert active > 27
assert str(active) not in immutable
assert f"int(target)>{active - 1}" in workflow
assert f"Number(uiTarget) <= {active - 1}" in vite
assert "./28/index.html" in launcher
assert "./27/index.html" in launcher
assert launcher.index("./28/index.html") < launcher.index("./27/index.html")

for relative in (
    "stores/appearance.ts",
    "stores/design.ts",
    "stores/locale.ts",
    "stores/session.ts",
    "stores/catalog.ts",
    "stores/customers.ts",
    "stores/favorites.ts",
    "stores/theme.ts",
    "features/visual-editor/store.ts",
):
    text=(ROOT/"platform/frontend/src"/relative).read_text(encoding="utf-8")
    assert "armaghan:test27:" not in text, relative
    assert f"armaghan:test{active}:" in text, relative

print("Test 27 freeze / Test 28 promotion contract: PASS")
