# Independent root release selector

Owner authorization: 2026-10-02. Publish Test29 at https://armaghantrading.com/ while continuing numbered test development.

- config/root-release.json selects the existing numbered folder served at root. Changing this file publishes that version automatically. Workflow Dispatch → FTP Deploy → target=root → root_version also permits a one-off explicit selection; the config remains the durable selector, so reconcile it after a manual selection.
- No rebuild is required for an existing version. The promoter reads its deployed index.html (or legacy index.htm), places a base URL for its existing /t/<version>/ assets, and atomically replaces only root index.html. It never writes numbered versions, backend files, logo or host routing.
- Root release waits for a requested /t deployment to pass. It verifies initial JS/CSS before switching and compares published root bytes afterwards. On HTTP mismatch it restores the prior root entry.
- Prior root HTML is preserved under /t/_root-history/<sha256>.html. Frozen versions retain their files.
- Root uses the production Style Profile channel; /t uses staging. Routes retain the actual root or test path despite the asset base element.
- Active Test29 requested defaults: four Home eyebrows hidden, recoverable by explicit visual-editor show; unavailable/made-to-order customer label is only producible, smaller, lighter and centered. Production admin shortcuts point to actual Filament products/customers.
- Root deployment verification pending; record actual run and commit after success. Automated verification is not visual acceptance.
