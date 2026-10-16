# Home media cache round-trip repair — 2026-10-16

Observed production `/backend/api/home-media/production` returned HTTP200 with `__PHP_Incomplete_Class_Name: stdClass` inside images and srcsets. Laravel 13 cache.serializable_classes=false disallows cached objects. Existing tests use nonserializing array cache and missed warm-read corruption. This finding does not prove the separate handset-upload or Baghdad-connectivity causes.

| Rank | Method | Reason |
|---:|---|---|
| 1 | Cache plain arrays, cast to JSON maps only at response boundary, version cache key | Minimal scope; preserves security defaults, empty-map API contract and selection fingerprint; bypasses old incompatible entries |
| 2 | Cache JSON strings, decode per request | Safe but introduces redundant serialization |
| 3 | Allowlist stdClass in global cache configuration | Expands deserialization policy for one endpoint unnecessarily |
| 4 | Disable this cache | Avoids corruption but repeats expensive image-dimension lookups |
| 5 | Permit arbitrary cached classes | Rejected: weakens Laravel security defaults |

Selected: option 1. Reference: https://laravel.com/framework/docs/13.x/upgrade#cache-serializable_classes-configuration .

Regression: real database-backed serialized cache, compare cold/warm published hero and srcsets, reject PHP class metadata, preserve empty JSON maps in unselected staging channel. Existing channel, upload, revision and authorization tests remain. No frontend/build, schema, cache-policy, customer records, published image selections or frozen snapshots change. New v2 namespace lets old entries expire naturally without a global cache purge. Backend CI and guarded code deploy must pass before claiming live repair.

Other acceptance this run: live HelpSheet shows 86 questions, search for ۶ رقمی finds exactly one correct signup answer and accordion expands. Product page lists 280 products; three sampled real backend thumbnails load in browser. Full device acceptance, actual failing original photo and Baghdad network remain open.
