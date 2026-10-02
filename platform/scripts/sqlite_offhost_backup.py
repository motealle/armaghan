#!/usr/bin/env python3
"""Create and verify encrypted off-host SQLite backups for Armaghan.

Security properties:
- Production creates a consistent live snapshot with SQLite VACUUM INTO.
- The plaintext snapshot never leaves the production host.
- Production encrypts the snapshot with AES-256-GCM before FTP transfer.
- GitHub Actions stores only ciphertext + a non-secret manifest.
- Restore drill downloads the uploaded artifact, decrypts it only on an
  isolated runner, verifies integrity/foreign keys/table counts, then deletes
  the plaintext restored file.

A dedicated ARMAGHAN_BACKUP_PASSPHRASE GitHub secret is preferred. Until that
secret exists, the workflow derives a domain-separated backup key from the
existing FTP_PASSWORD secret. The raw secret is never printed or persisted.
"""
from __future__ import annotations

import argparse
import base64
import hashlib
import io
import json
import os
import secrets
import sqlite3
import ssl
import subprocess
import sys
import tempfile
from ftplib import FTP
from pathlib import Path
from urllib.parse import urlencode, urlparse
from urllib.request import Request, urlopen

KDF_ITERATIONS = 600_000
KDF_NAME = "pbkdf2-hmac-sha256"
CIPHER = "aes-256-gcm"
AAD = "armaghan-sqlite-offhost-backup-v1"
MAX_PLAINTEXT_BYTES = 128 * 1024 * 1024
KNOWN_TABLES = (
    "migrations",
    "users",
    "categories",
    "subcategories",
    "products",
    "customers",
    "favorite_shares",
    "magic_links",
    "style_profiles",
    "media",
)


def env(name: str) -> str:
    return os.environ.get(name, "").strip()


def ftp_endpoint(raw: str) -> tuple[str, int]:
    parsed = urlparse(raw) if "://" in raw else None
    host = parsed.hostname if parsed else raw
    port = (parsed.port or 21) if parsed else 21
    if not host:
        raise RuntimeError("FTP_SERVER is empty or invalid")
    return host, port


def php_quote(value: str) -> str:
    return "'" + value.replace("\\", "\\\\").replace("'", "\\'") + "'"


def choose_master_secret() -> tuple[bytes, str]:
    dedicated = env("ARMAGHAN_BACKUP_PASSPHRASE")
    fallback = env("FTP_PASSWORD")
    value = dedicated or fallback
    source = "dedicated_backup_secret" if dedicated else "ftp_password_kdf_fallback"

    if len(value) < 12:
        raise RuntimeError("Backup encryption secret is unavailable or too short")

    return value.encode("utf-8"), source


def derive_key(master: bytes, salt: bytes) -> bytes:
    return hashlib.pbkdf2_hmac(
        "sha256",
        master,
        salt,
        KDF_ITERATIONS,
        dklen=32,
    )


def helper_php(helper_secret: str, export_token: str, key_b64: str) -> str:
    template = r"""<?php
header('Content-Type: application/json; charset=utf-8');

$expected = __HELPER_SECRET__;
$provided = (string) ($_GET['token'] ?? '');
if (!hash_equals($expected, $provided)) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'stage' => 'auth']);
    exit;
}

$action = (string) ($_GET['action'] ?? 'backup');
$accountRoot = dirname(__DIR__);
$dataRoot = $accountRoot . '/armaghan-data';
$dbPath = $dataRoot . '/database.sqlite';
$exportRoot = $accountRoot . '/armaghan-backup-export-__EXPORT_TOKEN__';
$snapshotPath = $exportRoot . '/snapshot.sqlite';
$encryptedPath = $exportRoot . '/backup.sqlite.enc';
$key = base64_decode(__KEY_B64__, true);
$aad = 'armaghan-sqlite-offhost-backup-v1';
$maxBytes = 134217728;

function fail_backup(string $stage, int $status = 500, array $extra = []): void {
    http_response_code($status);
    echo json_encode(array_merge(['ok' => false, 'stage' => $stage], $extra), JSON_UNESCAPED_SLASHES);
    exit;
}
function remove_tree(string $path): void {
    if (!file_exists($path) && !is_link($path)) return;
    if (is_file($path) || is_link($path)) { @unlink($path); return; }
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($it as $item) {
        $item->isDir() && !$item->isLink() ? @rmdir($item->getPathname()) : @unlink($item->getPathname());
    }
    @rmdir($path);
}
function table_exists(PDO $pdo, string $table): bool {
    $statement = $pdo->prepare("SELECT COUNT(*) FROM sqlite_master WHERE type='table' AND name=?");
    $statement->execute([$table]);
    return ((int) $statement->fetchColumn()) === 1;
}

if ($action === 'cleanup') {
    remove_tree($exportRoot);
    echo json_encode(['ok' => true, 'stage' => 'cleanup'], JSON_UNESCAPED_SLASHES);
    exit;
}
if ($action !== 'backup') {
    fail_backup('unknown-action', 400);
}
if (!is_file($dbPath)) {
    fail_backup('database-missing');
}
if (!extension_loaded('openssl') || !function_exists('openssl_encrypt')) {
    fail_backup('openssl-missing');
}
if (!is_string($key) || strlen($key) !== 32) {
    fail_backup('key-invalid');
}
if (file_exists($exportRoot)) {
    fail_backup('stale-export');
}
if (!mkdir($exportRoot, 0700, true) && !is_dir($exportRoot)) {
    fail_backup('export-mkdir');
}
@chmod($exportRoot, 0700);

try {
    $pdo = new PDO('sqlite:' . $dbPath, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $pdo->exec('PRAGMA busy_timeout = 10000');
    $pdo->exec('VACUUM INTO ' . $pdo->quote($snapshotPath));

    clearstatcache(true, $snapshotPath);
    if (!is_file($snapshotPath) || filesize($snapshotPath) <= 0) {
        fail_backup('snapshot-missing');
    }
    @chmod($snapshotPath, 0600);

    $snapshotBytes = filesize($snapshotPath);
    if ($snapshotBytes === false || $snapshotBytes > $maxBytes) {
        fail_backup('snapshot-too-large', 409, ['max_bytes' => $maxBytes]);
    }

    $verify = new PDO('sqlite:' . $snapshotPath, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $integrity = (string) $verify->query('PRAGMA integrity_check')->fetchColumn();
    if ($integrity !== 'ok') {
        fail_backup('integrity-check');
    }
    $foreignKeyRows = $verify->query('PRAGMA foreign_key_check')->fetchAll(PDO::FETCH_ASSOC);
    if (count($foreignKeyRows) !== 0) {
        fail_backup('foreign-key-check');
    }

    $knownTables = [
        'migrations', 'users', 'categories', 'subcategories', 'products',
        'customers', 'favorite_shares', 'magic_links', 'style_profiles', 'media'
    ];
    $counts = [];
    foreach ($knownTables as $table) {
        if (table_exists($verify, $table)) {
            $counts[$table] = (int) $verify->query('SELECT COUNT(*) FROM "' . $table . '"')->fetchColumn();
        }
    }

    $tableNames = $verify->query(
        "SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name"
    )->fetchAll(PDO::FETCH_COLUMN);

    $plaintextSha256 = hash_file('sha256', $snapshotPath);
    $plaintext = file_get_contents($snapshotPath);
    if (!is_string($plaintext)) {
        fail_backup('snapshot-read');
    }

    $iv = random_bytes(12);
    $tag = '';
    $ciphertext = openssl_encrypt(
        $plaintext,
        'aes-256-gcm',
        $key,
        OPENSSL_RAW_DATA,
        $iv,
        $tag,
        $aad,
        16
    );
    unset($plaintext);

    if (!is_string($ciphertext) || strlen($tag) !== 16) {
        fail_backup('encrypt');
    }
    if (file_put_contents($encryptedPath, $ciphertext, LOCK_EX) === false) {
        fail_backup('encrypted-write');
    }
    unset($ciphertext);
    @chmod($encryptedPath, 0600);

    if (!@unlink($snapshotPath)) {
        fail_backup('plaintext-cleanup');
    }

    clearstatcache(true, $encryptedPath);
    echo json_encode([
        'ok' => true,
        'stage' => 'encrypted',
        'created_at' => gmdate('c'),
        'plaintext_bytes' => $snapshotBytes,
        'ciphertext_bytes' => filesize($encryptedPath),
        'plaintext_sha256' => $plaintextSha256,
        'ciphertext_sha256' => hash_file('sha256', $encryptedPath),
        'iv_b64' => base64_encode($iv),
        'tag_b64' => base64_encode($tag),
        'aad' => $aad,
        'sqlite_version' => (string) $verify->query('SELECT sqlite_version()')->fetchColumn(),
        'table_counts' => $counts,
        'tables' => array_values($tableNames),
        'integrity_check' => 'ok',
        'foreign_key_check_rows' => 0,
    ], JSON_UNESCAPED_SLASHES);
} catch (Throwable $exception) {
    @unlink($snapshotPath);
    @unlink($encryptedPath);
    fail_backup('exception', 500, ['error_type' => get_class($exception)]);
}
"""
    return (
        template.replace("__HELPER_SECRET__", php_quote(helper_secret))
        .replace("__EXPORT_TOKEN__", export_token)
        .replace("__KEY_B64__", php_quote(key_b64))
    )


def request_json(url: str, timeout: int = 180) -> dict:
    req = Request(
        url,
        headers={
            "User-Agent": "Armaghan-Offhost-Backup/1.0",
            "Accept": "application/json",
        },
    )
    with urlopen(req, timeout=timeout, context=ssl.create_default_context()) as response:
        return json.loads(response.read(256 * 1024).decode("utf-8"))


def ftp_connect() -> FTP:
    raw = env("FTP_SERVER")
    username = env("FTP_USERNAME")
    password = env("FTP_PASSWORD")
    if not raw or not username or not password:
        raise RuntimeError("FTP credentials unavailable")
    host, port = ftp_endpoint(raw)
    ftp = FTP()
    ftp.connect(host, port, timeout=30)
    ftp.login(username, password)
    return ftp


def safe_delete(ftp: FTP, path: str) -> None:
    try:
        ftp.delete(path)
    except Exception:
        pass


def backup(out_dir: Path) -> int:
    out_dir.mkdir(parents=True, exist_ok=True)
    for child in out_dir.iterdir():
        if child.is_file():
            child.unlink()

    master, key_source = choose_master_secret()
    salt = secrets.token_bytes(16)
    key = derive_key(master, salt)
    helper_secret = secrets.token_urlsafe(32)
    export_token = secrets.token_hex(8)
    helper_name = f"armaghan-offhost-backup-{export_token}.php"
    export_dir = f"armaghan-backup-export-{export_token}"
    site_url = env("ARMAGHAN_SITE_URL") or "https://armaghantrading.com"
    helper_url = (
        site_url.rstrip("/")
        + "/"
        + helper_name
        + "?"
        + urlencode({"token": helper_secret, "action": "backup"})
    )

    ftp = ftp_connect()
    helper_uploaded = False
    encrypted_downloaded = False
    cleanup_ok = True
    encrypted_local = out_dir / "backup.sqlite.enc"
    manifest_local = out_dir / "manifest.json"

    try:
        ftp.cwd("/public_html")
        ftp.storbinary(
            "STOR " + helper_name,
            io.BytesIO(
                helper_php(
                    helper_secret,
                    export_token,
                    base64.b64encode(key).decode("ascii"),
                ).encode("utf-8")
            ),
        )
        helper_uploaded = True

        result = request_json(helper_url)
        if not result.get("ok") or result.get("stage") != "encrypted":
            raise RuntimeError("Production backup helper did not complete encryption")
        if result.get("integrity_check") != "ok" or result.get("foreign_key_check_rows") != 0:
            raise RuntimeError("Production snapshot verification failed")

        ftp.cwd("/")
        ftp.cwd(export_dir)
        with encrypted_local.open("wb") as fh:
            ftp.retrbinary("RETR backup.sqlite.enc", fh.write)
        encrypted_downloaded = True

        ciphertext_sha256 = hashlib.sha256(encrypted_local.read_bytes()).hexdigest()
        if ciphertext_sha256 != result.get("ciphertext_sha256"):
            raise RuntimeError("Encrypted backup checksum mismatch after transfer")

        manifest = {
            "schema": 1,
            "kind": "armaghan-encrypted-sqlite-backup",
            "created_at": result.get("created_at"),
            "encryption": {
                "cipher": CIPHER,
                "aad": AAD,
                "iv_b64": result.get("iv_b64"),
                "tag_b64": result.get("tag_b64"),
                "kdf": KDF_NAME,
                "kdf_iterations": KDF_ITERATIONS,
                "salt_b64": base64.b64encode(salt).decode("ascii"),
                "key_source": key_source,
            },
            "plaintext_bytes": result.get("plaintext_bytes"),
            "ciphertext_bytes": result.get("ciphertext_bytes"),
            "plaintext_sha256": result.get("plaintext_sha256"),
            "ciphertext_sha256": ciphertext_sha256,
            "sqlite_version": result.get("sqlite_version"),
            "table_counts": result.get("table_counts"),
            "tables": result.get("tables"),
            "production_checks": {
                "integrity_check": result.get("integrity_check"),
                "foreign_key_check_rows": result.get("foreign_key_check_rows"),
            },
        }
        manifest_local.write_text(
            json.dumps(manifest, ensure_ascii=False, indent=2) + "\n",
            encoding="utf-8",
        )

        print("ARMAGHAN_OFFHOST_BACKUP")
        print(
            json.dumps(
                {
                    "ok": True,
                    "encrypted_before_transfer": True,
                    "key_source": key_source,
                    "plaintext_bytes": manifest["plaintext_bytes"],
                    "ciphertext_bytes": manifest["ciphertext_bytes"],
                    "table_count": len(manifest.get("tables") or []),
                    "critical_counts": manifest.get("table_counts"),
                    "integrity_check": "PASS",
                    "foreign_key_check": "PASS",
                },
                indent=2,
            )
        )
        return 0
    except Exception as exc:
        print("OFFHOST_BACKUP_ERROR:", type(exc).__name__)
        return 5
    finally:
        # Ask the still-present HTTPS helper to remove any private export first.
        # FTP cleanup below is only a fallback and also removes the helper itself.
        if helper_uploaded:
            try:
                cleanup_url = (
                    site_url.rstrip("/")
                    + "/"
                    + helper_name
                    + "?"
                    + urlencode({"token": helper_secret, "action": "cleanup"})
                )
                cleanup_result = request_json(cleanup_url, timeout=30)
                if not cleanup_result.get("ok"):
                    cleanup_ok = False
            except Exception:
                cleanup_ok = False

        try:
            if ftp.sock:
                ftp.cwd("/")
                try:
                    ftp.cwd(export_dir)
                    safe_delete(ftp, "backup.sqlite.enc")
                    safe_delete(ftp, "snapshot.sqlite")
                    ftp.cwd("/")
                    try:
                        ftp.rmd(export_dir)
                    except Exception:
                        pass
                except Exception:
                    pass

                if helper_uploaded:
                    ftp.cwd("/public_html")
                    safe_delete(ftp, helper_name)
                try:
                    ftp.quit()
                except Exception:
                    ftp.close()
        except Exception:
            cleanup_ok = False
            try:
                ftp.close()
            except Exception:
                pass

        print("OFFHOST_BACKUP_TEMP_CLEANUP:", "PASS" if cleanup_ok else "WARNING")


def decrypt_with_php(
    encrypted_path: Path,
    output_path: Path,
    key: bytes,
    manifest: dict,
) -> None:
    decrypt_php = r"""<?php
$ciphertext = file_get_contents(getenv('BACKUP_INPUT'));
$key = base64_decode(getenv('BACKUP_KEY_B64'), true);
$iv = base64_decode(getenv('BACKUP_IV_B64'), true);
$tag = base64_decode(getenv('BACKUP_TAG_B64'), true);
$aad = getenv('BACKUP_AAD');
if (!is_string($ciphertext) || !is_string($key) || strlen($key) !== 32) exit(2);
$plaintext = openssl_decrypt(
    $ciphertext,
    'aes-256-gcm',
    $key,
    OPENSSL_RAW_DATA,
    $iv,
    $tag,
    $aad
);
if (!is_string($plaintext)) exit(3);
if (file_put_contents(getenv('BACKUP_OUTPUT'), $plaintext, LOCK_EX) === false) exit(4);
@chmod(getenv('BACKUP_OUTPUT'), 0600);
"""
    with tempfile.NamedTemporaryFile("w", suffix=".php", delete=False) as fh:
        fh.write(decrypt_php)
        php_path = Path(fh.name)

    enc = manifest["encryption"]
    child_env = os.environ.copy()
    child_env.update(
        {
            "BACKUP_INPUT": str(encrypted_path),
            "BACKUP_OUTPUT": str(output_path),
            "BACKUP_KEY_B64": base64.b64encode(key).decode("ascii"),
            "BACKUP_IV_B64": str(enc["iv_b64"]),
            "BACKUP_TAG_B64": str(enc["tag_b64"]),
            "BACKUP_AAD": str(enc["aad"]),
        }
    )

    try:
        completed = subprocess.run(
            ["php", str(php_path)],
            env=child_env,
            stdout=subprocess.DEVNULL,
            stderr=subprocess.DEVNULL,
            check=False,
        )
        if completed.returncode != 0:
            raise RuntimeError("Encrypted backup decryption failed")
    finally:
        try:
            php_path.unlink()
        except FileNotFoundError:
            pass


def restore_drill(input_dir: Path) -> int:
    manifest_path = input_dir / "manifest.json"
    encrypted_path = input_dir / "backup.sqlite.enc"
    if not manifest_path.is_file() or not encrypted_path.is_file():
        print("RESTORE_DRILL_ERROR: encrypted artifact bundle is incomplete")
        return 2

    manifest = json.loads(manifest_path.read_text(encoding="utf-8"))
    if manifest.get("schema") != 1 or manifest.get("kind") != "armaghan-encrypted-sqlite-backup":
        print("RESTORE_DRILL_ERROR: manifest schema is invalid")
        return 2

    actual_cipher_sha = hashlib.sha256(encrypted_path.read_bytes()).hexdigest()
    if actual_cipher_sha != manifest.get("ciphertext_sha256"):
        print("RESTORE_DRILL_ERROR: ciphertext checksum mismatch")
        return 3

    master, key_source = choose_master_secret()
    enc = manifest.get("encryption") or {}
    if enc.get("kdf") != KDF_NAME or int(enc.get("kdf_iterations", 0)) != KDF_ITERATIONS:
        print("RESTORE_DRILL_ERROR: unsupported key derivation metadata")
        return 3
    if enc.get("cipher") != CIPHER or enc.get("aad") != AAD:
        print("RESTORE_DRILL_ERROR: unsupported encryption metadata")
        return 3
    if enc.get("key_source") != key_source:
        print("RESTORE_DRILL_ERROR: backup key source changed; use the original secret source")
        return 3

    salt = base64.b64decode(str(enc["salt_b64"]), validate=True)
    key = derive_key(master, salt)

    with tempfile.TemporaryDirectory(prefix="armaghan-restore-") as temp_dir:
        restored = Path(temp_dir) / "restored.sqlite"
        try:
            decrypt_with_php(encrypted_path, restored, key, manifest)

            actual_plain_sha = hashlib.sha256(restored.read_bytes()).hexdigest()
            if actual_plain_sha != manifest.get("plaintext_sha256"):
                raise RuntimeError("Restored plaintext checksum mismatch")

            connection = sqlite3.connect(str(restored))
            try:
                integrity = connection.execute("PRAGMA integrity_check").fetchone()
                if not integrity or integrity[0] != "ok":
                    raise RuntimeError("SQLite integrity_check failed")

                foreign_rows = connection.execute("PRAGMA foreign_key_check").fetchall()
                if foreign_rows:
                    raise RuntimeError("SQLite foreign_key_check failed")

                table_rows = connection.execute(
                    "SELECT name FROM sqlite_master "
                    "WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name"
                ).fetchall()
                tables = [row[0] for row in table_rows]
                if tables != list(manifest.get("tables") or []):
                    raise RuntimeError("Restored table inventory differs from backup manifest")

                counts = {}
                for table, expected in (manifest.get("table_counts") or {}).items():
                    if table not in KNOWN_TABLES:
                        raise RuntimeError("Manifest contains an unexpected counted table")
                    actual = int(connection.execute(f'SELECT COUNT(*) FROM "{table}"').fetchone()[0])
                    counts[table] = actual
                    if actual != int(expected):
                        raise RuntimeError("Restored table count differs from backup manifest")

                connection.execute("BEGIN IMMEDIATE")
                connection.execute("ROLLBACK")
            finally:
                connection.close()

            print("ARMAGHAN_OFFHOST_RESTORE_DRILL")
            print(
                json.dumps(
                    {
                        "ok": True,
                        "artifact_round_trip": True,
                        "plaintext_checksum": "PASS",
                        "integrity_check": "PASS",
                        "foreign_key_check": "PASS",
                        "table_inventory": "PASS",
                        "critical_counts": counts,
                        "write_lock_open_rollback": "PASS",
                        "restored_plaintext_persisted": False,
                    },
                    indent=2,
                )
            )
            return 0
        except Exception as exc:
            print("RESTORE_DRILL_ERROR:", type(exc).__name__)
            return 5


def main() -> int:
    parser = argparse.ArgumentParser()
    sub = parser.add_subparsers(dest="command", required=True)

    backup_parser = sub.add_parser("backup")
    backup_parser.add_argument("--out", required=True)

    restore_parser = sub.add_parser("restore-drill")
    restore_parser.add_argument("--input", required=True)

    args = parser.parse_args()
    if args.command == "backup":
        return backup(Path(args.out))
    return restore_drill(Path(args.input))


if __name__ == "__main__":
    sys.exit(main())
