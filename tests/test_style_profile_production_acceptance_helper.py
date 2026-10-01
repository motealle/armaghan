#!/usr/bin/env python3
from pathlib import Path

ROOT=Path(__file__).resolve().parents[1]
script=(ROOT/"platform/scripts/verify_style_profile_ftp.py").read_text(encoding="utf-8")

assert "beginTransaction()" in script
assert "rollBack()" in script
assert "rollback_intact" in script
assert "StyleProfileService" in script
assert "publish($actor, 'staging'" in script
assert "restore($actor, $versionOne, 'staging'" in script
assert "acceptance.probe_" in script
assert "safe_delete(ftp, helper_name)" in script
assert "Auth::login" not in script
assert "loginUsingId" not in script
assert "password" not in script.split("def main()",1)[0].lower() or "FTP_PASSWORD" in script
assert "ARMAGHAN_STYLE_PROFILE_PRODUCTION_ACCEPTANCE" in script
print("Production Style Profile acceptance helper contract: PASS")
