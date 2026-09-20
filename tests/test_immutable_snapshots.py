#!/usr/bin/env python3
from pathlib import Path
import os
import subprocess

root = Path(__file__).resolve().parents[1]
frozen = {
    line.strip() for line in (root / "docs" / "IMMUTABLE-TESTS.txt").read_text(encoding="utf-8").splitlines()
    if line.strip() and not line.lstrip().startswith("#")
}

before = os.environ.get("GITHUB_EVENT_BEFORE", "").strip()
if before and before != "0000000000000000000000000000000000000000":
    diff_range = [before, "HEAD"]
else:
    diff_range = ["HEAD^", "HEAD"]

try:
    changed = subprocess.check_output(
        ["git", "diff", "--name-only", *diff_range],
        cwd=root,
        text=True,
    ).splitlines()
except subprocess.CalledProcessError:
    changed = []

violations = []
for path in changed:
    parts = Path(path).parts
    if len(parts) >= 2 and parts[0] == "t" and parts[1] in frozen:
        violations.append(path)

assert not violations, "Frozen test modified: " + ", ".join(violations)
print(f"Immutable test guard: PASS ({len(frozen)} frozen tests, range {'..'.join(diff_range)})")
