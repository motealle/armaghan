# Connected order tracking and customer view — 2026-11-01

Base2cf84737771c466158355dff70e4415bf6eaa33a. Owner asks to audit the screenshot journey and give managers a nearby View as customer action. Existing real customer order in screenshot must not be recreated as a workaround. No actual production order lookup is possible without the owner's authenticated session; tests use isolated/synthetic records, not the screenshot's private order.

Changes: visible primary progress link after wizard registration; exact-reference deep link finds old orders through server filtering and automatically expands them. Track registration survives language changes and SPA return for the same actual customer+path/category/subcategory/note; explicit Start over clears it. WhatsApp clearly remains conversation. Customer section says My orders. Manager Customer orders button filters existing orders without opening creation; Register order remains separate. Reference search canonicalizes localized digits/case. New protected GET admin/orders/{order}/customer-view reuses snapshot(order,false), not admin data or an impersonation session. Only public notes, confirmed quotes and customer-visible files appear; file URLs use existing protected manager downloads. No auth/session swap, magic link, password or external send. Read-only action needs no order write/audit mutation. Shared Vue progress/customer-detail rendering prevents a simulated divergent timeline.

Risk checks: reject guests, customer-role users, inactive admins and protected-owner access. Assert customer projection equality, no private notes/revisions/owner IDs, unchanged authenticated manager and coexisting customer session, unchanged activity count, no POST preview, draft quote/private file filtering, real public file link. Filter tests cover older-than-page-one references and customer cross-account rejection. Browser375/768/1280 covers manager selected account, customer-order shortcut, read-only preview, private note exclusion, error reset, customer shared stage, actual wizard register, translated successful CTA, SPA return and reference auto-expansion. Existing lifecycle/payment and idempotency policies retained.

Local frontend type-check/build, focused picker/table/wizard tests and frozen/root/FTP checks PASS. PHP unavailable locally; GitHub PHP8.3 required checks execute actual Laravel tests. Production code-only release adds OrderTrackingTest and OrderCommercialTest to its focused gate. No migrations/dependency changes. Verified publication: PR43 final source156c4c5939cb419e45b7594ace0e4bbd6b68247b; main09d16a2aa930560f16d0c39f3bc1ce9276e64137. Frontend41321295109, Backend41321295101 (135tests/1308assertions and clean audit), Browser41321295097 PASS. Code release41321516418/job124892606533 PASS81tests/868assertions, snapshot/activation/cleanup and public health smoke; no schema/dependency drift. FTP41321516448, Test29deploy124892810789 and root124893690268 PASS. Customer preview touch target44px; settled screenshot review complete. Synthetic/isolated evidence is distinct from authenticated real screenshot-order/physical handset acceptance. No real order was created or changed for testing.


Independent live verification:

```json
{
  "root_status": 200,
  "root_main": [
    "assets/index-BEB2srKa.js"
  ],
  "test29_base": true,
  "guest_admin_orders": 401,
  "assets": [
    {
      "path": "assets/AdminDashboard-Dw85gNRx.css",
      "bytes": 68,
      "sha256": "fe3128a7dabc7191f5acf6172386626b8fcfea72b66647baac2ded5e54f51ab5",
      "status": 200,
      "verified": true
    },
    {
      "path": "assets/index-ZRUsWOKT.css",
      "bytes": 131483,
      "sha256": "458439e39a9144b10a5015089b7f5627e8400fe0a044f68fb5289bf4563b1a33",
      "status": 200,
      "verified": true
    },
    {
      "path": "assets/ProductionView-bi5aF2Oj.js",
      "bytes": 9851,
      "sha256": "2385c735f9a545baf7ba51f008b9bbdc600a0ef6111605ff734ec334dfb7cf14",
      "status": 200,
      "verified": true
    },
    {
      "path": "assets/TrackingView-U3Orp0q6.js",
      "bytes": 12503,
      "sha256": "50bc99069b9eab21c1f7c680a44f114e410c2d8c53feb176112e51da9d78e15a",
      "status": 200,
      "verified": true
    },
    {
      "path": "assets/AdminDashboard-CHiK5hCr.js",
      "bytes": 112323,
      "sha256": "40e9adb66e17fd55e5433b2666bdcbf59ff55f223b41fd6f9195ab7c505012fa",
      "status": 200,
      "verified": true
    },
    {
      "path": "assets/index-BEB2srKa.js",
      "bytes": 308415,
      "sha256": "9c8ac04b1dd64e0a58e35dc040e6016dd9c0f123f65ffbcc96ef65cacc4a25f7",
      "status": 200,
      "verified": true
    }
  ]
}
```
