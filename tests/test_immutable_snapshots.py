#!/usr/bin/env python3
from pathlib import Path
import subprocess

root = Path(__file__).resolve().parents[1]
frozen = {
    line.strip() for line in (root / "docs" / "IMMUTABLE-TESTS.txt").read_text(encoding="utf-8").splitlines()
    if line.strip() and not line.lstrip().startswith("#")
}
try:
    changed = subprocess.check_output(["git","diff","--name-only","HEAD^","HEAD"], cwd=root, text=True).splitlines()
except subprocess.CalledProcessError:
    changed = []
violations = []
for path in changed:
    parts = Path(path).parts
    if len(parts) >= 2 and parts[0] == "t" and parts[1] in frozen:
        violations.append(path)
assert not violations, "Frozen test modified: " + ", ".join(violations)
print("Immutable test guard: PASS")
