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
audit=(ROOT/"docs/TEST20-UX-AUDIT.md").read_text(encoding="utf-8")

adaptive=(src/"components/ui/AdaptivePanel.vue").read_text(encoding="utf-8")
customer=(src/"features/admin/components/CustomerDetailSheet.vue").read_text(encoding="utf-8")
products=(src/"features/admin/components/AdminProductsPanel.vue").read_text(encoding="utf-8")
editor=(src/"features/admin/components/ProductEditorPanel.vue").read_text(encoding="utf-8")
admin=(src/"features/admin/components/AdminDashboard.vue").read_text(encoding="utf-8")
customers=(src/"stores/customers.ts").read_text(encoding="utf-8")
catalog=(src/"stores/catalog.ts").read_text(encoding="utf-8")
data=(src/"data/catalog.ts").read_text(encoding="utf-8")
domain=(src/"types/domain.ts").read_text(encoding="utf-8")
locale=(src/"stores/locale.ts").read_text(encoding="utf-8")
css=(src/"styles/main.css").read_text(encoding="utf-8")

assert tuple(map(int,pkg["version"].split("."))) >= (0,20,0)
assert "../../t/20" in vite
assert "test20-build" in workflow and "t/20" in workflow
assert "test_test20_customer_product_admin_contract.py" in workflow
assert "19" in immutable
assert launcher.index("./20/index.html") < launcher.index("./19/index.html")

# Customer 360: adaptive surface and blank-sheet regression
assert "AdaptivePanel" in customer
assert "toRaw" in customer and "structuredClone(toRaw(row))" in customer
assert "customerNotFound" in customer
assert "adaptive-panel" in adaptive
assert "role=\"dialog\"" in adaptive and "aria-modal=\"true\"" in adaptive
assert "Escape" in adaptive and "previousFocus" in adaptive
assert "@media(min-width:1024px)" in css and ".adaptive-panel.wide" in css
assert "translate(-50%,100%)" in css
assert "priorityStars" in customers and "loginPassword" in customers
assert "customer-stars" in customer and "priorityStars" in customer
for field in ["email","whatsapp","address","location","notes","orderCount","activeOrder","timelineStage","profileImage","loginPassword","accessMode"]:
    assert field in customer
assert "permanent" in customer and "expiring" in customer
assert "generateAccess" in customers and "revokeAccess" in customers

# Product hover micro-interaction
assert "scale(1.012)" in css
assert "(hover:hover)" in css and "(pointer:fine)" in css
assert "prefers-reduced-motion" in css

# Scalable product admin
assert "AdminProductsPanel" in admin
assert "query=ref('')" in products
assert "categoryFilter" in products and "subcategoryFilter" in products
assert "pageSize=ref(50)" in products
for size in ['25','50','100']:
    assert f':value="{size}"' in products
assert "filtered" in products and "paged" in products and "pageCount" in products
assert "toggleAllPage" in products and "allPageSelected" in products
assert "selected" in products and "bulkDelete" in products
assert "MoreVertical" in products and "row-menu" in products
assert ':disabled="selected.length>0"' in products
assert "pagination-button" in css and "width:44px;height:44px" in css

# Multilingual Add/Edit + two-digit code inference
assert "ProductNames" in domain and "names?: ProductNames" in domain
assert "productDefaultsFromCode" in data
assert "code.trim().slice(0,2)" in data
assert "ProductEditorPanel" in products
assert "namesByLanguage" in editor
for model in ["nameFa","nameAr","nameEn","nameKu"]:
    assert model in editor
assert "productDefaultsFromCode" in editor
assert "resetFromCode" in editor
assert "lockedText" in editor and "negotiableText" in editor
assert "catalog.update" in editor and "catalog.add" in editor
assert "function update(product: Product)" in catalog
assert "productNameFor" in locale
assert "custom?.[target]" in locale

# Test isolation and repo memory
for key_file in [
    ROOT/"platform/frontend/index.html",
    src/"stores/session.ts",
    src/"stores/favorites.ts",
    src/"stores/customers.ts",
    src/"stores/catalog.ts",
    src/"stores/locale.ts",
]:
    text=key_file.read_text(encoding="utf-8")
    assert "test20" in text or "armaghan:locale:manual" in text

assert "Customer 360" in audit and "Subtle Hover Scale" in audit
assert "Test 20" in backlog
print("Test 20 customer/product admin contract: PASS")
