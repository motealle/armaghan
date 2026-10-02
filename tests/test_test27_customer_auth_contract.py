#!/usr/bin/env python3
"""Regression contract for Test 27 customer Backend session + Magic Link wiring."""
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
session = (ROOT / "platform/frontend/src/stores/session.ts").read_text(encoding="utf-8")
app = (ROOT / "platform/frontend/src/App.vue").read_text(encoding="utf-8")
login = (ROOT / "platform/frontend/src/features/auth/components/LoginSheet.vue").read_text(encoding="utf-8")
dashboard = (ROOT / "platform/frontend/src/features/customers/components/CustomerDashboard.vue").read_text(encoding="utf-8")

required_session = [
    "fetchCustomerSession",
    "backendAuthenticated",
    "hydrateFromBackend",
    "saveBackendCustomer",
    "logoutCustomerSession",
]
for needle in required_session:
    assert needle in session, f"Customer session contract missing: {needle}"

assert "hasBackendCustomerSession=await session.hydrateFromBackend()" in app
assert "!hasBackendCustomerSession&&magic" in app
assert "!hasBackendCustomerSession&&customerAccess" in app

assert "magicLinkRequestHelp" in login
assert "session.createMagicLink" not in login
assert "makeMagic()" not in login

assert "session.currentCustomerId??1" not in dashboard
assert "customers.items[0]" not in dashboard
assert "session.backendAuthenticated" in dashboard
assert "session.saveBackendCustomer" in dashboard

print("Test 27 customer auth/session contract: PASS")
