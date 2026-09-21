#!/usr/bin/env python3
from pathlib import Path

ROOT=Path(__file__).resolve().parents[1]
SRC=ROOT/"platform/frontend/src"

smart=(SRC/"components/media/SmartImage.vue").read_text(encoding="utf-8")
hero=(SRC/"features/home/components/HeroCarousel.vue").read_text(encoding="utf-8")
css=(SRC/"styles/main.css").read_text(encoding="utf-8")
workflow=(ROOT/".github/workflows/ftp-deploy.yml").read_text(encoding="utf-8")
audit=(ROOT/"docs/TEST25-BUGFIX2-UX-AUDIT.md").read_text(encoding="utf-8")

# Literal escaped newline must never leak into rendered SmartImage markup again.
assert '<picture v-if="currentSrc" class="absolute inset-0 z-20">\\n' not in smart
assert 'polish */\\n/* Test 25 deployment trigger' not in css

# Hero swipe: pointer events + horizontal threshold + vertical guard + touch-action.
for marker in ["beginSwipe","finishSwipe","cancelSwipe","swipeStartX","swipeStartY","Math.abs(dx)>=44","Math.abs(dx)>Math.abs(dy)*1.15"]:
    assert marker in hero
for event in ['@pointerdown="beginSwipe"','@pointerup="finishSwipe"','@pointercancel="cancelSwipe"']:
    assert event in hero
assert "touch-action:pan-y pinch-zoom" in css

# Dark-mode low-contrast foreground blue is neutralized outside branded navigation/fill states.
for marker in [
    "html.dark .card-action",
    "html.dark .product-code-row code",
    "html.dark .detail-menu-icon",
    "html.dark .category-chevron",
    "html.dark .customer-kpi",
    "color:#d7dae0",
]:
    assert marker in css
assert "html.dark .nav-item.active" in css  # branded navigation remains intentionally blue.

# English uses one inherited LTR content model rather than scattered text-only patches.
assert 'html[data-locale="en"] #main-content{direction:ltr;text-align:left}' in css
for marker in [
    'html[data-locale="en"] .app-header-layout',
    'html[data-locale="en"] .commerce-layout',
    'html[data-locale="en"] .bottom-nav>div',
    'html[data-locale="en"] .mobile-drawer-panel',
]:
    assert marker in css
assert 'html[data-locale="en"] .site-footer' in css

# Decision record and CI gate.
assert audit.count("| 1 |") >= 5
assert "test_test25_bugfix2_contract.py" in workflow

print("Test 25 hotfix 2 contract: PASS")
