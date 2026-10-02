#!/usr/bin/env python3
"""Regression contract for Test 28 persisted FavoriteShare + WhatsApp wiring."""
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
view = (ROOT / "platform/frontend/src/views/FavoritesView.vue").read_text(encoding="utf-8")
api = (ROOT / "platform/frontend/src/features/favorites/shareFavorites.ts").read_text(encoding="utf-8")
router = (ROOT / "platform/frontend/src/router/index.ts").read_text(encoding="utf-8")
store = (ROOT / "platform/frontend/src/stores/favorites.ts").read_text(encoding="utf-8")

for needle in [
    "issueFavoriteShare",
    "resolveFavoriteShare",
    "revokeFavoriteShare",
    "whatsappUrl",
]:
    assert needle in view, f"FavoritesView missing persisted-share behavior: {needle}"

assert "buildFavoritesShareUrl" not in view
assert "shared=v1:" not in api
assert "/api/favorite-shares" in api
assert "/api/favorite-shares/resolve" in api
assert "method:'POST'" in api
assert "/api/customer/favorite-shares/" in api

assert "{path:'/favorites/share/:token',name:'favorite-share',component:FavoritesView}" in router
assert "session.isCustomer?1" not in store
assert "session.currentCustomerId" in store

print("Test 28 persisted FavoriteShare/WhatsApp contract: PASS")
