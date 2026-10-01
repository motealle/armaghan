#!/usr/bin/env python3
"""Safe shared-hosting preflight for Armaghan.

Uploads a short-lived PHP probe plus a sibling private marker, calls the probe
without printing hostnames or credentials, records non-sensitive capabilities,
and removes every temporary object in a finally block.
"""
from __future__ import annotations

import io
import json
import os
import secrets
import ssl
import sys
from ftplib import FTP
from urllib.parse import urlparse
from urllib.request import Request, urlopen
from urllib.error import HTTPError, URLError

REQUIRED_EXTENSIONS = [
    "ctype",
    "curl",
    "dom",
    "fileinfo",
    "filter",
    "hash",
    "mbstring",
    "openssl",
    "pcre",
    "pdo",
    "session",
    "tokenizer",
    "xml",
]


def env(name: str) -> str:
    return os.environ.get(name, "").strip()


def ftp_endpoint(raw: str) -> tuple[str, int]:
    parsed = urlparse(raw) if "://" in raw else None
    host = parsed.hostname if parsed else raw
    port = (parsed.port or 21) if parsed else 21
    if not host:
        raise RuntimeError("FTP_SERVER is empty or invalid")
    return host, port


def add_base(candidates: list[str], value: str) -> None:
    value = value.strip().rstrip("/")
    if not value:
        return
    if "://" not in value:
        value = "https://" + value
    parsed = urlparse(value)
    if parsed.scheme not in {"http", "https"} or not parsed.netloc:
        return
    base = f"{parsed.scheme}://{parsed.netloc}"
    if base not in candidates:
        candidates.append(base)


def candidate_bases(ftp_host: str) -> list[str]:
    candidates: list[str] = []
    for name in (
        "ARMAGHAN_SITE_URL",
        "SITE_URL",
        "APP_URL",
        "ARMAGHAN_SITE_URL_VAR",
        "SITE_URL_VAR",
        "APP_URL_VAR",
    ):
        add_base(candidates, env(name))

    derived = [ftp_host]
    for prefix in ("ftp.", "cpanel."):
        if ftp_host.lower().startswith(prefix):
            derived.append(ftp_host[len(prefix):])

    for host in derived:
        add_base(candidates, "https://" + host)
    for host in derived:
        add_base(candidates, "http://" + host)
    return candidates


def php_probe(run_token: str, private_dir: str, marker_name: str) -> str:
    required = json.dumps(REQUIRED_EXTENSIONS)
    return f"""<?php
header('Content-Type: application/json; charset=utf-8');
$required = json_decode('{required}', true);
$extensions = [];
foreach ($required as $ext) {{
    $extensions[$ext] = extension_loaded($ext);
}}
$privateDir = dirname(__DIR__) . '/{private_dir}';
$marker = $privateDir . '/{marker_name}';
$writeTest = $privateDir . '/php-write-test.txt';
$outsideWrite = @file_put_contents($writeTest, 'ok') !== false;
if ($outsideWrite) {{ @unlink($writeTest); }}

$pdoDrivers = class_exists('PDO') ? PDO::getAvailableDrivers() : [];
$sqliteVersion = null;
$sqliteMemory = false;
if (in_array('sqlite', $pdoDrivers, true)) {{
    try {{
        $pdo = new PDO('sqlite::memory:');
        $sqliteVersion = $pdo->query('select sqlite_version()')->fetchColumn();
        $pdo->exec('create table preflight (id integer primary key, value text)');
        $pdo->exec("insert into preflight(value) values ('ok')");
        $sqliteMemory = $pdo->query('select count(*) from preflight')->fetchColumn() === '1';
    }} catch (Throwable $e) {{
        $sqliteMemory = false;
    }}
}}

$https = (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off')
    || strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';

$result = [
    'probe' => 'armaghan-hosting-preflight',
    'run' => '{run_token}',
    'php_version' => PHP_VERSION,
    'php_version_id' => PHP_VERSION_ID,
    'sapi' => PHP_SAPI,
    'required_extensions' => $extensions,
    'pdo_drivers' => $pdoDrivers,
    'pdo_sqlite' => in_array('sqlite', $pdoDrivers, true),
    'sqlite3_extension' => extension_loaded('sqlite3'),
    'sqlite_version' => $sqliteVersion,
    'sqlite_memory_rw' => $sqliteMemory,
    'sqlite_private_file_rw' => $sqlitePrivateFile,
    'sqlite_foreign_keys' => $sqliteForeignKeys,
    'sqlite_vacuum_into' => $sqliteVacuumInto,
    'document_root_matches_probe_dir' => isset($_SERVER['DOCUMENT_ROOT'])
        && realpath((string) $_SERVER['DOCUMENT_ROOT']) === realpath(__DIR__),
    'outside_private_marker_readable' => is_readable($marker),
    'outside_private_dir_writable_by_php' => $outsideWrite,
    'open_basedir_enabled' => trim((string) ini_get('open_basedir')) !== '',
    'https_request' => $https,
    'functions' => [
        'proc_open' => function_exists('proc_open'),
        'exec' => function_exists('exec'),
        'shell_exec' => function_exists('shell_exec'),
        'symlink' => function_exists('symlink'),
    ],
    'limits' => [
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time'),
    ],
];
echo json_encode($result, JSON_UNESCAPED_SLASHES);
"""


def fetch_probe(bases: list[str], probe_name: str, run_token: str) -> tuple[dict | None, dict]:
    diagnostics = {
        "candidate_count": len(bases),
        "https_candidate_reached": False,
        "http_candidate_reached": False,
        "attempt_errors": [],
    }
    context = ssl.create_default_context()
    for base in bases:
        url = base + "/" + probe_name
        scheme = urlparse(base).scheme
        try:
            req = Request(url, headers={"User-Agent": "Armaghan-Hosting-Preflight/1.0"})
            with urlopen(req, timeout=12, context=context) as response:
                body = response.read(64 * 1024)
                final_scheme = urlparse(response.geturl()).scheme
                if final_scheme == "https":
                    diagnostics["https_candidate_reached"] = True
                else:
                    diagnostics["http_candidate_reached"] = True
            data = json.loads(body.decode("utf-8"))
            if data.get("probe") == "armaghan-hosting-preflight" and data.get("run") == run_token:
                return data, diagnostics
            diagnostics["attempt_errors"].append("unexpected-response")
        except HTTPError as exc:
            diagnostics["attempt_errors"].append(f"http-{exc.code}")
        except (URLError, TimeoutError, ssl.SSLError):
            diagnostics["attempt_errors"].append("connection-or-tls")
        except (UnicodeDecodeError, json.JSONDecodeError):
            diagnostics["attempt_errors"].append("non-json-response")
        except Exception:
            diagnostics["attempt_errors"].append("other")
    diagnostics["attempt_errors"] = sorted(set(diagnostics["attempt_errors"]))
    return None, diagnostics


def safe_delete(ftp: FTP, filename: str) -> None:
    try:
        ftp.delete(filename)
    except Exception:
        pass


def main() -> int:
    raw_server = env("FTP_SERVER")
    username = env("FTP_USERNAME")
    password = env("FTP_PASSWORD")
    if not raw_server or not username or not password:
        print("PREFLIGHT_INFRA_ERROR: required FTP secrets are unavailable")
        return 2

    host, port = ftp_endpoint(raw_server)
    token = secrets.token_hex(8)
    run_token = f"{env('GITHUB_RUN_ID') or 'local'}-{token}"
    probe_name = f"armaghan-preflight-{token}.php"
    private_dir = f".armaghan-preflight-private-{token}"
    marker_name = "marker.txt"

    result: dict = {
        "schema": 1,
        "verdict": "INCONCLUSIVE",
        "ftp": {
            "connected": False,
            "public_html_found": False,
            "account_root_is_parent_of_public_html": False,
            "private_sibling_created": False,
            "cleanup_ok": False,
        },
        "http": {},
        "php": None,
        "checks": {},
        "deployment_strategy": "Build Composer vendor in CI; upload built application by FTP. Host-side Composer is not required.",
    }

    ftp = FTP()
    private_created = False
    probe_uploaded = False
    marker_uploaded = False
    cleanup_ok = True
    try:
        ftp.connect(host, port, timeout=20)
        ftp.login(username, password)
        result["ftp"]["connected"] = True

        ftp.cwd("/")
        try:
            ftp.cwd("public_html")
            result["ftp"]["public_html_found"] = True
            ftp.cwd("..")
            result["ftp"]["account_root_is_parent_of_public_html"] = ftp.pwd() == "/"
        except Exception:
            result["ftp"]["public_html_found"] = False
            raise RuntimeError("public_html is not reachable from the FTP account root")

        ftp.cwd("/")
        ftp.mkd(private_dir)
        private_created = True
        result["ftp"]["private_sibling_created"] = True
        ftp.cwd(private_dir)
        ftp.storbinary("STOR " + marker_name, io.BytesIO(b"armaghan-preflight-marker"))
        marker_uploaded = True

        ftp.cwd("/public_html")
        source = php_probe(run_token, private_dir, marker_name).encode("utf-8")
        ftp.storbinary("STOR " + probe_name, io.BytesIO(source))
        probe_uploaded = True

        php, http_diag = fetch_probe(candidate_bases(host), probe_name, run_token)
        result["http"] = http_diag
        result["php"] = php

        if php is None:
            result["verdict"] = "INCONCLUSIVE"
            result["checks"] = {
                "php_runtime_reached": False,
                "reason": "Temporary PHP probe could not be reached through any safe inferred web URL. FTP capability was tested, but PHP/runtime checks require the canonical site URL or hosting-panel access.",
            }
        else:
            extensions = php.get("required_extensions", {})
            php_version_ok = int(php.get("php_version_id", 0)) >= 80300
            required_extensions_ok = all(bool(extensions.get(ext)) for ext in REQUIRED_EXTENSIONS)
            sqlite_version = php.get("sqlite_version")
            sqlite_version_ok = False
            if sqlite_version:
                try:
                    sqlite_version_ok = tuple(map(int, str(sqlite_version).split(".")[:3])) >= (3, 26, 0)
                except ValueError:
                    sqlite_version_ok = False

            checks = {
                "php_runtime_reached": True,
                "php_8_3_or_newer": php_version_ok,
                "laravel_required_extensions": required_extensions_ok,
                "pdo_sqlite": bool(php.get("pdo_sqlite")),
                "sqlite3_extension": bool(php.get("sqlite3_extension")),
                "sqlite_3_26_or_newer": sqlite_version_ok,
                "sqlite_memory_read_write": bool(php.get("sqlite_memory_rw")),
                "sqlite_private_file_read_write": bool(php.get("sqlite_private_file_rw")),
                "sqlite_foreign_keys": bool(php.get("sqlite_foreign_keys")),
                "sqlite_vacuum_into": bool(php.get("sqlite_vacuum_into")),
                "document_root_is_public_html": bool(php.get("document_root_matches_probe_dir")),
                "php_can_read_private_sibling": bool(php.get("outside_private_marker_readable")),
                "php_can_write_private_sibling": bool(php.get("outside_private_dir_writable_by_php")),
                "https_runtime_request": bool(php.get("https_request")),
            }
            result["checks"] = checks
            hard = [
                checks["php_8_3_or_newer"],
                checks["laravel_required_extensions"],
                checks["pdo_sqlite"],
                checks["sqlite3_extension"],
                checks["sqlite_3_26_or_newer"],
                checks["sqlite_memory_read_write"],
                checks["sqlite_private_file_read_write"],
                checks["sqlite_foreign_keys"],
                checks["sqlite_vacuum_into"],
                checks["document_root_is_public_html"],
                checks["php_can_read_private_sibling"],
                checks["php_can_write_private_sibling"],
                checks["https_runtime_request"],
            ]
            result["verdict"] = "PASS" if all(hard) else "FAIL"
    except Exception as exc:
        result["verdict"] = "INCONCLUSIVE"
        result["checks"] = {
            "infrastructure_error": type(exc).__name__,
            "message": "The preflight could not complete. No hostname, path, username, or credential is printed.",
        }
    finally:
        try:
            if result["ftp"]["connected"]:
                ftp.cwd("/public_html")
                if probe_uploaded:
                    safe_delete(ftp, probe_name)
                ftp.cwd("/")
                if private_created:
                    try:
                        ftp.cwd(private_dir)
                        if marker_uploaded:
                            safe_delete(ftp, marker_name)
                        safe_delete(ftp, "php-write-test.txt")
                        safe_delete(ftp, "preflight.sqlite")
                        safe_delete(ftp, "preflight.sqlite-wal")
                        safe_delete(ftp, "preflight.sqlite-shm")
                        safe_delete(ftp, "preflight.sqlite-journal")
                        safe_delete(ftp, "preflight-backup.sqlite")
                        ftp.cwd("/")
                        ftp.rmd(private_dir)
                    except Exception:
                        cleanup_ok = False
                result["ftp"]["cleanup_ok"] = cleanup_ok
                try:
                    ftp.quit()
                except Exception:
                    ftp.close()
        except Exception:
            result["ftp"]["cleanup_ok"] = False
            try:
                ftp.close()
            except Exception:
                pass

    with open("hosting-preflight-result.json", "w", encoding="utf-8") as fh:
        json.dump(result, fh, ensure_ascii=False, indent=2)

    print("ARMAGHAN_HOSTING_PREFLIGHT_RESULT")
    print(json.dumps(result, ensure_ascii=False, indent=2))
    if not result["ftp"].get("cleanup_ok"):
        print("PREFLIGHT_CLEANUP_WARNING: temporary cleanup was not fully confirmed")
        return 3
    return 0


if __name__ == "__main__":
    sys.exit(main())
