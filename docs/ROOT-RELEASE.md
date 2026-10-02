# Independent root release selector

Owner authorization: 2026-10-02. Publish Test29 at https://armaghantrading.com/ while continuing numbered test development.

- config/root-release.json selects the existing numbered folder served at root. Changing this file publishes that version automatically. Workflow Dispatch → FTP Deploy → target=root → root_version also permits a one-off explicit selection; the config remains the durable selector, so reconcile it after a manual selection.
- No rebuild is required for an existing version. The promoter reads its deployed index.html (or legacy index.htm), places a base URL for its existing /t/<version>/ assets, and atomically replaces only root index.html. It never writes numbered versions, backend files, logo or host routing.
- Root release waits for a requested /t deployment to pass. It verifies initial JS/CSS before switching and compares published root bytes afterwards. On HTTP mismatch it restores the prior root entry.
- Prior root HTML is preserved under /t/_root-history/<sha256>.html. Frozen versions retain their files.
- Root uses the production Style Profile channel; /t uses staging. Routes retain the actual root or test path despite the asset base element.
- Active Test29 requested defaults: four Home eyebrows hidden, recoverable by explicit visual-editor show; unavailable/made-to-order customer label is only producible, smaller, lighter and centered. Production admin shortcuts point to actual Filament products/customers.
- Verified root and /t publication: FTP 37009040766; independent root/auth smoke 37009477221 PASS. Single-digit selections normalize to zero-padded legacy folders (1 → 01). Automated verification is not visual acceptance.

## Root Test29 and Google preparation — verified 2026-10-02

- Selected root version: 29, independently from active numbered development. Public root: https://armaghantrading.com/#/ ; /t/29 remains live; Tests 01–28 remain frozen.
- Frontend release 5d7af888959604518cca693e8747c8dbc36fb2f1, FTP run 37009040766: QA, 49 Vue tests across 13 files, type-check/build, scoped /t upload, published artifact checksum/launcher verification and root promotion PASS.
- Root/auth verification commit 1a4de2db706f6b14bcf2e5e63fc79c9ecaa34c86, FTP run 37009477221: static QA, eight promotion/rollback tests, guest customer HTTP 401, Products/Customers login boundary and root HTML/asset verification PASS; /t deployment SKIPPED.
- Google backend 0a07d9b98bfc8e5a4d297c02b4626064167255fb: Backend CI 37008505779 and Additive Deploy 37008505784 PASS. 49 backend tests / 348 assertions; one create-only identity table; consistent production database snapshot before migration; activation and admin-login smoke PASS.
- Live readiness reports Google provider configured=False. Credentials/Cloud registration and an actual Google signup/repeat login are OPEN. Never claim real Google login acceptance from mocked tests.
- Fresh desktop DOM at root: all four home eyebrows display:none; product badges read Producible in the observed English locale, 11.2px, weight 400, centered with zero center offset. Four locale source defaults preserve the concise equivalent (Persian تولیدپذیر). Root product links resolve /#/products; Home logo/hero/about images loaded. No mobile/tablet or broad theme/language visual acceptance is inferred.
- Real product upload/customer CRUD already exist in Filament. Guest /backend/admin/products redirects to /backend/admin/login. No authenticated upload/customer edit was performed in this run because no real admin session was available.
- Root accepts real Backend customer authentication only; browser-only review/admin roles do not authenticate root. Root LoginSheet hides local demo password/signup forms and offers backend Google/Magic Link and real admin entry.
- Root Style Profile reads production; /t reads staging. The root asset base does not change native/router/skip-link navigation destinations.
- Earlier pipeline attempts stopped safely before the new upload because of workflow-string escaping or historical text-only assertions. Corrected YAML was locally parsed; current release and all publication gates PASS. Use callback replacements for JavaScript replacement text containing shell dollar/apostrophe sequences.
- Follow-up P0: owner Cloud setup/private server credentials; real authenticated product-image/customer/Google acceptance; verify canonical root fragment paths for customer Magic Link/FavoriteShare against private server configuration (source historical defaults remain /t/27); complete deferred mobile/tablet and broad visual acceptance.

