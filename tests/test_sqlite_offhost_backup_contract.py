#!/usr/bin/env python3
"""Static safety contract for encrypted off-host SQLite backup + restore drill."""
from pathlib import Path

ROOT=Path(__file__).resolve().parents[1]
script=(ROOT/"platform/scripts/sqlite_offhost_backup.py").read_text(encoding="utf-8")
workflow=(ROOT/".github/workflows/sqlite-offhost-backup.yml").read_text(encoding="utf-8")

for needle in (
    "VACUUM INTO",
    "aes-256-gcm",
    "pbkdf2_hmac",
    "integrity_check",
    "foreign_key_check",
    "plaintext-cleanup",
    "ciphertext_sha256",
    "restored_plaintext_persisted",
):
    assert needle in script, needle

assert "actions/upload-artifact@v4" in workflow
assert "actions/download-artifact@v4" in workflow
assert "retention-days: 14" in workflow
assert 'cron: "23 1 * * *"' in workflow
assert "ARMAGHAN_BACKUP_PASSPHRASE" in workflow
assert "FTP_PASSWORD" in workflow
assert "backup.sqlite.enc" in workflow
assert "manifest.json" in workflow

# A public repository must never upload the plaintext production SQLite file.
artifact_block=workflow.split("Upload encrypted off-host artifact",1)[1].split("restore-drill:",1)[0]
assert "database.sqlite" not in artifact_block
assert "snapshot.sqlite" not in artifact_block
assert "restored.sqlite" not in artifact_block

# Restore validation must happen only after artifact download.
assert workflow.index("Download encrypted artifact round-trip") < workflow.index("Restore and verify isolated SQLite copy")

print("Encrypted off-host SQLite backup contract: PASS")
