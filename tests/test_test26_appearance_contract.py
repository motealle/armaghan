#!/usr/bin/env python3
from pathlib import Path
import re

ROOT = Path(__file__).resolve().parents[1]
FRONTEND = ROOT / "platform/frontend"
vite = (FRONTEND / "vite.config.ts").read_text(encoding="utf-8")
workflow = (ROOT / ".github/workflows/ftp-deploy.yml").read_text(encoding="utf-8")
launcher = (ROOT / "t/index.htm").read_text(encoding="utf-8")
immutable = {
    line.strip()
    for line in (ROOT / "docs/IMMUTABLE-TESTS.txt").read_text(encoding="utf-8").splitlines()
    if line.strip() and not line.lstrip().startswith("#")
}
rules = (ROOT / "docs/PROJECT-RULES.md").read_text(encoding="utf-8")
backlog = (ROOT / "docs/BACKLOG.md").read_text(encoding="utf-8")
audit = (ROOT / "docs/TEST26-UX-AUDIT.md").read_text(encoding="utf-8")
images = (ROOT / "docs/TEST26-IMAGE-REQUIREMENTS.md").read_text(encoding="utf-8")
selected = (ROOT / "docs/TEST26-SELECTED-IMAGES.md").read_text(encoding="utf-8")

assert "26" in immutable
assert "Tests 01–26 are frozen" in rules
assert "snapshot/test26-final" in rules
assert "Test 26 is frozen" in backlog
assert launcher.index("./26/index.html?build=test26-footer-language-r8a") < launcher.index("./25/index.html")
assert "FTP Deploy Run #234" in backlog
assert "Run 8" in audit

assert images.count("approved-local") >= 8
for choice in ("1-2","2-2","3-1","4-3","5-2","6-3","7-3","8-2"):
    assert choice in selected

active_match = re.search(r"ACTIVE_UI_TEST:\s*'(\d{2})'", workflow)
assert active_match is not None
assert int(active_match.group(1)) > 26
assert "ARMAGHAN_UI_TARGET" in workflow
assert "test26-build" not in workflow
assert "path: t/26" not in workflow
assert 'os.path.isdir("t/26")' not in workflow
assert 'os.walk("t/26")' not in workflow
assert "ARMAGHAN_UI_TARGET" in vite
assert "../../t/26" not in vite
assert "../../.build/frontend" in vite
limit_match = re.search(r"Number\(uiTarget\) <= (\d+)", vite)
assert limit_match is not None
assert int(limit_match.group(1)) >= 26

print("Test 26 frozen handoff contract: PASS")
