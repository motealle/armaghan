#!/usr/bin/env python3
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
workflow = (ROOT / ".github/workflows/ftp-deploy.yml").read_text(encoding="utf-8")
launcher = (ROOT / "t/index.htm").read_text(encoding="utf-8")
immutable = {
    line.strip()
    for line in (ROOT / "docs/IMMUTABLE-TESTS.txt").read_text(encoding="utf-8").splitlines()
    if line.strip() and not line.lstrip().startswith("#")
}
backlog = (ROOT / "docs/BACKLOG.md").read_text(encoding="utf-8")
release = (ROOT / "docs/TEST22-RELEASE.md").read_text(encoding="utf-8")

assert "22" in immutable
assert "./22/index.html" in launcher and "./21/index.html" in launcher
assert launcher.index("./22/index.html") < launcher.index("./21/index.html")
assert "Test 22" in backlog and "FTP Deploy Run #74" in backlog
assert "Test 22" in release and "no remote file deletion" in release.lower()
assert "test_test22_release_contract.py" in workflow
assert "Protect immutable test snapshots" in workflow

print("Test 22 historical release contract: PASS")
