# Home placement and customer order guidance — 2026-11-01

Source base942028eb7919e8d45af8ad59bc402a3fb9dbef88. Scope: move Why to the last Home content position; make the actual existing manager order flow discoverable. Existing order API and policies remain authoritative.

Manager flow: sign in as manager → customer panel → Customers → find the customer's existing phone account → Register order on that row → verify selected phone and record ID → description → Register request. The new order expands immediately. Change stage handles adjacent normal progress; Correct stage permits existing historic orders with a required reason (10+ characters), subject to unchanged invoice/deposit prerequisites and owner-only terminal reopening. Do not invent payment confirmations. Current stage is always public to that customer; notes are public only when explicitly marked. Customer enters their own account to view progress; manager never needs their password. Creating a login alone does not create an order. Unlinked customer records need their own existing Create account action, not a duplicate customer.

Picker: page-aware server search; Persian/Arabic digits canonicalized; phone spaces/punctuation stripped; active rows only; selected row retained independently of result pages; explicit phone/#ID; late response guard/unmount invalidation. Four locales fa/ar/ku/en. No impersonation, automatic phone linking, private-data leak or change to protected backend routes.

Validation: focused picker tests cover page selection retention/inactive exclusion and out-of-order search replies; existing customer pin regression retained. Local type-check/build and release103 tests PASS; frozen01–28/auth/editor/root/FTP guards PASS. New actual-component Chromium harness covers Customers shortcut, selected phone account, normalized search, creation payload, immediate expand, stage/note save, customer read-only rendering, mobile overflow and Home section order across all four UI languages at375/768/1280. Browser API data is synthetic; backend persistence/privacy is covered by existing OrderTrackingTest, unchanged in this frontend-only release. Authenticated production writes/physical handset checks are not claimed.

Video research: https://developers.cloudflare.com/stream/uploading-videos/upload-via-link/ and https://developers.cloudflare.com/stream/ confirm public-URL import and adaptive360p–1080p delivery. Bunny official URL-fetch guide: https://github.com/BunnyWay/documentation/blob/main/stream/url-fetch.mdx . This request asks about options; no paid subscription, third-party transfer or video replacement initiated. Current files support progressive reads but remain roughly128MB each; progressive start does not reduce the encoded bitrate. Existing per-clip Aparat settings remain usable. Original videos and deployment scope preserved.

Publication: verified PR42/source61710bc52f24ba4505f1ac4797184e5c31fcf767, main6fdb1a4c5673fba5c8730eed1c2051f394a185a9. Final Frontend41307615167 and Browser41307615159 PASS; FTP41307768014, QA124851619732, Test29deploy124851809530 and root124852763343 PASS. Final build105tests/27files PASS. Stage controls moved ahead of financial forms after screenshot review; saved actions reset to notes/private. Explicit selector labels corrected before publication. Actual customer orders/accounts/payments were never mutated. Six live assets independently verified below.


Independent live verification:

```json
{
  "root_status": 200,
  "root_main": [
    "assets/index-By4evtUv.js"
  ],
  "test29_base": true,
  "guest_admin_orders": 401,
  "assets": [
    {
      "path": "assets/index-ZRUsWOKT.css",
      "bytes": 131483,
      "sha256": "458439e39a9144b10a5015089b7f5627e8400fe0a044f68fb5289bf4563b1a33",
      "status": 200,
      "verified": true
    },
    {
      "path": "assets/TrackingView-Bae7BoX9.js",
      "bytes": 12503,
      "sha256": "7b50749924c22b1356c8916e7fdb433a854d6e3b76dfc52308853f9feffa0894",
      "status": 200,
      "verified": true
    },
    {
      "path": "assets/HomeView-N_Xvq-xN.js",
      "bytes": 28113,
      "sha256": "f6737c4f31eff91b4907c78ac7a2f59c775e53751e6ab82f6caf40559ee565ec",
      "status": 200,
      "verified": true
    },
    {
      "path": "assets/customerSessionApi-99Ls81fo.js",
      "bytes": 78008,
      "sha256": "673b2dbb992cd28bc3f7f211dfec4617085ce01a774cd399e7e414aaa5ea3683",
      "status": 200,
      "verified": true
    },
    {
      "path": "assets/AdminDashboard-BPYqOli7.js",
      "bytes": 116085,
      "sha256": "a7328dc1472dc396322b31ad18da574bd900d39daa035040bb45c0fd7bddddbb",
      "status": 200,
      "verified": true
    },
    {
      "path": "assets/index-By4evtUv.js",
      "bytes": 308310,
      "sha256": "7fe14e9553240fc89c81ea90f9190de60553019b0d5a3344f2c5426c7ef1c190",
      "status": 200,
      "verified": true
    }
  ]
}
```
