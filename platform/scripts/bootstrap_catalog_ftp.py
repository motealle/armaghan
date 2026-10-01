#!/usr/bin/env python3
"""One-shot guarded production catalog bootstrap for Armaghan."""
from __future__ import annotations

import io
import json
import os
import secrets
import ssl
import sys
from ftplib import FTP
from urllib.error import HTTPError
from urllib.parse import urlencode, urlparse
from urllib.request import Request, urlopen


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


def helper_php(secret: str) -> str:
    template = r"""<?php
header('Content-Type: application/json; charset=utf-8');

$expected = __SECRET__;
$provided = (string) ($_GET['token'] ?? '');
if (!hash_equals($expected, $provided)) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'stage' => 'auth']);
    exit;
}

$appRoot = dirname(__DIR__) . '/armaghan-backend';
if (!is_file($appRoot . '/vendor/autoload.php') || !is_file($appRoot . '/bootstrap/app.php')) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'stage' => 'app-missing']);
    exit;
}

require $appRoot . '/vendor/autoload.php';
$app = require $appRoot . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function catalog_counts(): array {
    return [
        'categories' => App\Models\Category::query()->count(),
        'subcategories' => App\Models\Subcategory::query()->count(),
        'products' => App\Models\Product::query()->count(),
    ];
}

$before = catalog_counts();
if ($before !== ['categories' => 0, 'subcategories' => 0, 'products' => 0]) {
    http_response_code(409);
    echo json_encode([
        'ok' => false,
        'stage' => 'catalog-not-empty',
        'before' => $before,
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

try {
    $backupExit = $kernel->call('armaghan:backup-sqlite');
    if ($backupExit !== 0) {
        throw new RuntimeException('backup');
    }

    $bootstrapExit = $kernel->call('armaghan:bootstrap-catalog', ['--apply' => true]);
    if ($bootstrapExit !== 0) {
        throw new RuntimeException('bootstrap');
    }

    $after = catalog_counts();
    $countsOk = $after === ['categories' => 3, 'subcategories' => 6, 'products' => 18];

    $codesOk =
        App\Models\Category::query()->whereIn('code', ['1', '2', '3'])->count() === 3
        && App\Models\Subcategory::query()->whereIn('code', ['11', '12', '21', '22', '31', '32'])->count() === 6
        && App\Models\Product::query()->whereIn('code', ['11001', '22001', '32003'])->count() === 3;

    if (!$countsOk || !$codesOk) {
        throw new RuntimeException('verification');
    }

    echo json_encode([
        'ok' => true,
        'stage' => 'verified',
        'backup_created' => true,
        'before' => $before,
        'after' => $after,
        'representative_codes_verified' => true,
    ], JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'stage' => 'bootstrap-failed',
        'error_type' => get_class($e),
    ], JSON_UNESCAPED_SLASHES);
}
"""
    return template.replace("__SECRET__", php_quote(secret))


def safe_delete(ftp: FTP, path: str) -> None:
    try:
        ftp.delete(path)
    except Exception:
        pass


def get_json(url: str, timeout: int = 120) -> dict:
    req = Request(
        url,
        headers={
            "User-Agent": "Armaghan-Catalog-Bootstrap/1.0",
            "Accept": "application/json",
        },
    )
    try:
        with urlopen(req, timeout=timeout, context=ssl.create_default_context()) as response:
            return json.loads(response.read(256 * 1024).decode("utf-8"))
    except HTTPError as exc:
        body = exc.read(256 * 1024)
        try:
            return json.loads(body.decode("utf-8"))
        except Exception:
            raise


def main() -> int:
    raw_server = env("FTP_SERVER")
    username = env("FTP_USERNAME")
    password = env("FTP_PASSWORD")
    site_url = env("ARMAGHAN_SITE_URL") or "https://armaghantrading.com"

    if not raw_server or not username or not password:
        print("CATALOG_BOOTSTRAP_ERROR: FTP secrets unavailable")
        return 2

    host, port = ftp_endpoint(raw_server)
    nonce = secrets.token_hex(8)
    secret = secrets.token_urlsafe(32)
    helper_name = f"armaghan-catalog-bootstrap-{nonce}.php"
    helper_url = site_url.rstrip("/") + "/" + helper_name + "?" + urlencode({"token": secret})

    ftp = FTP()
    uploaded = False
    cleanup_ok = True

    try:
        ftp.connect(host, port, timeout=30)
        ftp.login(username, password)
        ftp.cwd("/public_html")
        ftp.storbinary("STOR " + helper_name, io.BytesIO(helper_php(secret).encode("utf-8")))
        uploaded = True

        result = get_json(helper_url, timeout=180)
        safe_result = {
            "ok": bool(result.get("ok")),
            "stage": result.get("stage"),
            "backup_created": bool(result.get("backup_created")),
            "before": result.get("before"),
            "after": result.get("after"),
            "representative_codes_verified": bool(result.get("representative_codes_verified")),
            "error_type": result.get("error_type"),
        }

        print("ARMAGHAN_PRODUCTION_CATALOG_BOOTSTRAP")
        print(json.dumps(safe_result, ensure_ascii=False, indent=2))

        required = (
            safe_result["ok"]
            and safe_result["stage"] == "verified"
            and safe_result["backup_created"]
            and safe_result["before"] == {"categories": 0, "subcategories": 0, "products": 0}
            and safe_result["after"] == {"categories": 3, "subcategories": 6, "products": 18}
            and safe_result["representative_codes_verified"]
        )
        return 0 if required else 4
    except Exception as exc:
        print("CATALOG_BOOTSTRAP_ERROR:", type(exc).__name__)
        return 5
    finally:
        try:
            if ftp.sock:
                ftp.cwd("/public_html")
                if uploaded:
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

        print("CATALOG_BOOTSTRAP_TEMP_CLEANUP:", "PASS" if cleanup_ok else "WARNING")


if __name__ == "__main__":
    sys.exit(main())
