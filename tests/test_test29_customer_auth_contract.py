#!/usr/bin/env python3
"""Regression contract for Test 29 customer Backend session + Magic Link wiring."""
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
session = (ROOT / "platform/frontend/src/stores/session.ts").read_text(encoding="utf-8")
api = (ROOT / "platform/frontend/src/features/auth/services/customerSessionApi.ts").read_text(encoding="utf-8")
router = (ROOT / "platform/frontend/src/router/index.ts").read_text(encoding="utf-8")
magic_view = (ROOT / "platform/frontend/src/views/MagicLinkView.vue").read_text(encoding="utf-8")
app = (ROOT / "platform/frontend/src/App.vue").read_text(encoding="utf-8")
login = (ROOT / "platform/frontend/src/features/auth/components/LoginSheet.vue").read_text(encoding="utf-8")
dashboard = (ROOT / "platform/frontend/src/features/customers/components/CustomerDashboard.vue").read_text(encoding="utf-8")

required_session = [
    "fetchCustomerSession",
    "backendAuthenticated",
    "hydrateFromBackend",
    "saveBackendCustomer",
    "logoutCustomerSession",
    "consumeBackendMagicLink",
]
for needle in required_session:
    assert needle in session, f"Customer session contract missing: {needle}"

assert "/api/customer/magic-link/consume" in api
assert "method:'POST'" in api
assert "body:JSON.stringify({token})" in api

assert "{path:'/magic/:token',name:'magic-link',component:MagicLinkView}" in router
assert "session.consumeBackendMagicLink(token)" in magic_view
assert "router.replace('/tracking')" in magic_view

assert "hasBackendCustomerSession=await session.hydrateFromBackend()" in app
assert "!hasBackendCustomerSession&&customerAccess" in app
assert "params.get('magic')" not in app

assert "magicLinkRequestHelp" in login
assert "session.createMagicLink" not in login
assert "makeMagic()" not in login

assert "session.currentCustomerId??1" not in dashboard
assert "customers.items[0]" not in dashboard
assert "session.backendAuthenticated" in dashboard
assert "session.saveBackendCustomer" in dashboard

print("Test 29 customer auth/session contract: PASS")
