#!/usr/bin/env python3
from pathlib import Path
import json

ROOT=Path(__file__).resolve().parents[1]
src=ROOT/"platform/frontend/src"

pkg=json.loads((ROOT/"platform/frontend/package.json").read_text(encoding="utf-8"))
vite=(ROOT/"platform/frontend/vite.config.ts").read_text(encoding="utf-8")
workflow=(ROOT/".github/workflows/ftp-deploy.yml").read_text(encoding="utf-8")
launcher=(ROOT/"t/index.htm").read_text(encoding="utf-8")
immutable={line.strip() for line in (ROOT/"docs/IMMUTABLE-TESTS.txt").read_text(encoding="utf-8").splitlines() if line.strip() and not line.startswith("#")}
backlog=(ROOT/"docs/BACKLOG.md").read_text(encoding="utf-8")
audit=(ROOT/"docs/TEST21-UX-AUDIT.md").read_text(encoding="utf-8")
registry=(src/"data/productPlaceholders.ts").read_text(encoding="utf-8")
design=(src/"stores/design.ts").read_text(encoding="utf-8")
media=(src/"features/catalog/components/ProductMediaCarousel.vue").read_text(encoding="utf-8")
admin=(src/"features/admin/components/AdminDashboard.vue").read_text(encoding="utf-8")
smart=(src/"components/media/SmartImage.vue").read_text(encoding="utf-8")

assert tuple(map(int,pkg["version"].split("."))) >= (0,21,0)
assert "../../t/21" in vite or "../../t/22" in vite or "../../t/23" in vite or "../../t/24" in vite or "../../t/25" in vite
assert ("test21-build" in workflow and "t/21" in workflow) or ("test22-build" in workflow and "t/22" in workflow) or ("test23-build" in workflow and "t/23" in workflow) or ("test24-build" in workflow and "t/24" in workflow) or ("test25-build" in workflow and "t/25" in workflow)
assert "test_test21_placeholder_admin_contract.py" in workflow
assert "20" in immutable and "21" in immutable
assert launcher.index("./21/index.html") < launcher.index("./20/index.html")

assert "defaultPlaceholderSet: PlaceholderSetId = 'paper-cut'" in registry
for set_id in ["paper-cut","flat-geometric","dimensional"]:
    assert set_id in registry
assert "armaghan:test21:placeholder-set" in design or "armaghan:test22:placeholder-set" in design or "armaghan:test23:placeholder-set" in design or "armaghan:test24:placeholder-set" in design or "armaghan:test25:placeholder-set" in design
assert "productPlaceholder" in media and "fallbackImage" in media
assert 'role="radiogroup"' in admin and 'type="radio"' in admin
assert "fallbackSrc" in smart and "useFallback" in smart
assert "Test 21" in backlog and "رتبه" in audit

for key_file in [
    ROOT/"platform/frontend/index.html",
    src/"stores/session.ts",
    src/"stores/favorites.ts",
    src/"stores/customers.ts",
    src/"stores/catalog.ts",
    src/"stores/design.ts",
    src/"stores/theme.ts",
]:
    assert "test21" in key_file.read_text(encoding="utf-8") or "test22" in key_file.read_text(encoding="utf-8") or "test23" in key_file.read_text(encoding="utf-8") or "test24" in key_file.read_text(encoding="utf-8") or "test25" in key_file.read_text(encoding="utf-8")

print("Test 21 placeholder/admin contract: PASS")
