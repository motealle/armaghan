# Test 28 — Production Hardening

Status: **LIVE**. Test 27 is frozen; Test 28 is the active mutable UI lane.

## Selected method

| Rank | Backup method | Score | Reason |
|---:|---|---:|---|
| 1 | `VACUUM INTO` → AES-256-GCM on Production → encrypted Actions artifact → isolated artifact-download restore drill | 10/10 | Consistent SQLite snapshot, encryption before off-host transfer, automated retention and evidence-backed recovery test |
| 2 | `sqlite3_rsync` to independent SSH/VPS | 8.8/10 | Strong technical option, but no independent SSH destination/credential is currently provisioned |
| 3 | Private object storage | 8.5/10 | Good long-term target but requires a new provider/secret |
| 4 | Secondary FTP/hosting backup | 6.4/10 | May share a failure domain with primary hosting |
| 5 | Same-host backup only | 2/10 | Not disaster recovery |

Selected automatically: **Option 1**.

## Backup invariants

- Production snapshot is generated with SQLite `VACUUM INTO`.
- `integrity_check` and `foreign_key_check` pass before encryption.
- Plaintext never leaves the production host.
- Encryption: AES-256-GCM; PBKDF2-HMAC-SHA256, 600,000 iterations, random per-backup salt.
- Public-repository artifact contains only `backup.sqlite.enc` + `manifest.json`.
- Scheduled daily at 01:23 UTC; 14-day artifact retention.
- Restore drill downloads the uploaded artifact, validates checksums/inventory/counts and deletes temporary plaintext.

## First production run

- Workflow: SQLite Off-host Backup #1 (`36974268704`) — **PASS**.
- Artifact: `armaghan-sqlite-backup-36974268704`; ID `11212796186`; size 337,663 bytes; expiry 2026-10-16T06:35:35Z.
- Production snapshot size before encryption: 335,872 bytes.
- Integrity: PASS; foreign keys: PASS; production temp cleanup: PASS.
- Restore artifact round-trip: PASS.
- Plaintext checksum: PASS; integrity: PASS; foreign keys: PASS; table inventory/counts: PASS; write-lock open/rollback: PASS.
- Restored plaintext persisted after drill: **false**.
- Current key source: `ftp_password_kdf_fallback` because the dedicated backup secret is not yet configured.

## Test 28 promotion

- `docs/IMMUTABLE-TESTS.txt` now freezes Test27.
- Current source state namespace is `armaghan:test28:*`.
- Vite/FTP refuse frozen target 27 or below.
- Test28 visual-editor, customer-auth, FavoriteShare and backup safety contracts are deployment gates.
- FTP #318 caught a stale historical Test26 assertion before deployment.
- FTP #319 caught two stale Test27 session-test fixtures before deployment.
- FTP #320: **PASS** after contract/fixture-only repairs; TypeScript + 41 Vue unit tests + Test28 build + FTP smoke PASS.
- `/public_html/t/28`: 112 files uploaded; no remote deletes; root skipped.
- Independent live verification: Test28 loads, frozen Test27 loads, launcher is Test28-first.

## Operational improvement

Add a dedicated `ARMAGHAN_BACKUP_PASSPHRASE` GitHub Actions secret. Future backup runs will prefer it. Until retained fallback-encrypted artifacts expire, preserve the ability to derive their keys from the original fallback secret.
