#!/usr/bin/env python3
"""Freeze Test28 and protect the active release's directory, namespace and launcher order."""
from pathlib import Path
import re

ROOT = Path(__file__).resolve().parents[1]
frozen = {line.strip() for line in (ROOT / "docs/IMMUTABLE-TESTS.txt").read_text().splitlines()
          if line.strip() and not line.lstrip().startswith("#")}
workflow = (ROOT / ".github/workflows/ftp-deploy.yml").read_text()
vite = (ROOT / "platform/frontend/vite.config.ts").read_text()
launcher = (ROOT / "t/index.htm").read_text()
active_match = re.search(r"ACTIVE_UI_TEST:\s*'(\d{2})'", workflow)
assert active_match
active = int(active_match.group(1))
assert active >= 29 and str(active) not in frozen
assert all(f"{n:02d}" in frozen for n in range(1, 29))
assert f"Number(uiTarget) <= {active - 1}" in vite
assert f"int(target)>{active - 1}" in workflow
assert launcher.index(f"./{active}/index.html") < launcher.index("./28/index.html")
main = (ROOT / "platform/frontend/src/main.ts").read_text()
assert f"dataset.uiTest='{active}'" in main
for name in ("appearance", "design", "locale", "session", "catalog", "customers", "favorites", "theme"):
    text = (ROOT / f"platform/frontend/src/stores/{name}.ts").read_text()
    assert f"armaghan:test{active}:" in text, name
    assert "armaghan:test28:" not in text, name
locale = (ROOT / "platform/frontend/src/stores/locale.ts").read_text()
assert f"const MANUAL_KEY='armaghan:test{active}:locale:manual'" in locale
assert "armaghan:locale:manual" not in locale
print("Test28 freeze / active release isolation contract: PASS")
