#!/usr/bin/env python3
"""First production Laravel deployment for Armaghan shared hosting.

Builds are prepared in CI. This script uploads one ZIP to the FTP account root,
uploads a short-lived activation controller to public_html, invokes it over
HTTPS, verifies Laravel health/API endpoints, and removes the temporary ZIP and
activation controller. It never prints FTP credentials, APP_KEY, filesystem
paths, or the activation token.
"""
from __future__ import annotations

import argparse
import io
import json
import os
import secrets
import ssl
import sys
from ftplib import FTP
from pathlib import Path
from urllib.parse import urlparse, urlencode
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


def activation_php(token: str, secret: str, site_url: str, zip_name: str) -> str:
    # Template is plain text rather than a Python f-string so PHP braces remain intact.
    template = r"""<?php
header('Content-Type: application/json; charset=utf-8');
$expected = __SECRET__;
$provided = (string) ($_GET['token'] ?? '');
if (!hash_equals($expected, $provided)) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'stage' => 'auth']);
    exit;
}

$accountRoot = dirname(__DIR__);
$appRoot = $accountRoot . '/armaghan-backend';
$nextRoot = $accountRoot . '/armaghan-backend-next-__TOKEN__';
$dataRoot = $accountRoot . '/armaghan-data';
$backupRoot = $dataRoot . '/backups';
$sharedEnv = $dataRoot . '/.env';
$dbPath = $dataRoot . '/database.sqlite';
$zipPath = $accountRoot . '/__ZIP__';
$publicRoot = __DIR__ . '/backend';

function fail_activation(string $stage): void {
    http_response_code(500);
    echo json_encode(['ok' => false, 'stage' => $stage], JSON_UNESCAPED_SLASHES);
    exit;
}

function ensure_dir(string $path): void {
    if (!is_dir($path) && !mkdir($path, 0770, true) && !is_dir($path)) {
        fail_activation('mkdir');
    }
}

function copy_tree(string $source, string $destination): void {
    ensure_dir($destination);
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($iterator as $item) {
        $relative = substr($item->getPathname(), strlen($source) + 1);
        $target = $destination . '/' . $relative;
        if ($item->isDir()) {
            ensure_dir($target);
        } else {
            ensure_dir(dirname($target));
            if (!copy($item->getPathname(), $target)) {
                fail_activation('copy-public');
            }
        }
    }
}

if (is_dir($appRoot)) {
    fail_activation('existing-app');
}
if (is_dir($nextRoot)) {
    fail_activation('stale-next');
}
if (!is_file($zipPath)) {
    fail_activation('missing-release');
}
if (file_exists($publicRoot) && !is_dir($publicRoot)) {
    fail_activation('public-path-conflict');
}

$zip = new ZipArchive();
if ($zip->open($zipPath) !== true) {
    fail_activation('zip-open');
}
ensure_dir($nextRoot);
if (!$zip->extractTo($nextRoot)) {
    $zip->close();
    fail_activation('zip-extract');
}
$zip->close();

foreach (['vendor/autoload.php', 'bootstrap/app.php', 'artisan', 'public/index.php'] as $required) {
    if (!is_file($nextRoot . '/' . $required)) {
        fail_activation('release-validation');
    }
}

ensure_dir($dataRoot);
ensure_dir($backupRoot);
ensure_dir($nextRoot . '/storage/framework/cache/data');
ensure_dir($nextRoot . '/storage/framework/sessions');
ensure_dir($nextRoot . '/storage/framework/views');
ensure_dir($nextRoot . '/storage/logs');
ensure_dir($nextRoot . '/bootstrap/cache');

if (!is_file($sharedEnv)) {
    $appKey = 'base64:' . base64_encode(random_bytes(32));
    $env = [
        'APP_NAME=Armaghan',
        'APP_ENV=production',
        'APP_KEY=' . $appKey,
        'APP_DEBUG=false',
        'APP_URL=__SITE_URL__/backend',
        'LOG_CHANNEL=single',
        'LOG_LEVEL=warning',
        'DB_CONNECTION=sqlite',
        'DB_DATABASE=' . $dbPath,
        'DB_FOREIGN_KEYS=true',
        'SESSION_DRIVER=database',
        'SESSION_LIFETIME=120',
        'SESSION_SECURE_COOKIE=true',
        'SESSION_HTTP_ONLY=true',
        'SESSION_SAME_SITE=lax',
        'SESSION_PATH=/',
        'SESSION_COOKIE=armaghan_backend_session',
        'CACHE_STORE=database',
        'QUEUE_CONNECTION=database',
        'FILESYSTEM_DISK=local',
        'ARMAGHAN_SQLITE_BACKUP_PATH=' . $backupRoot,
    ];
    if (file_put_contents($sharedEnv, implode(PHP_EOL, $env) . PHP_EOL, LOCK_EX) === false) {
        fail_activation('env-write');
    }
    @chmod($sharedEnv, 0600);
}
if (!copy($sharedEnv, $nextRoot . '/.env')) {
    fail_activation('env-copy');
}
@chmod($nextRoot . '/.env', 0600);

if (!is_file($dbPath)) {
    if (file_put_contents($dbPath, '') === false) {
        fail_activation('db-create');
    }
}
@chmod($dbPath, 0660);

chdir($nextRoot);
require $nextRoot . '/vendor/autoload.php';
$app = require $nextRoot . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$migrateExit = $kernel->call('migrate', ['--force' => true]);
if ($migrateExit !== 0) {
    fail_activation('migrate');
}

$backupExit = $kernel->call('armaghan:backup-sqlite');
if ($backupExit !== 0) {
    fail_activation('backup');
}

$pdo = $app->make('db')->connection('sqlite')->getPdo();
$tables = [
    'users' => false,
    'products' => false,
    'customers' => false,
    'style_profiles' => false,
    'style_profile_versions' => false,
    'style_profile_publications' => false,
];
foreach ($tables as $name => $_) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM sqlite_master WHERE type='table' AND name=?");
    $stmt->execute([$name]);
    $tables[$name] = ((int) $stmt->fetchColumn()) === 1;
}
foreach ($tables as $ok) {
    if (!$ok) {
        fail_activation('schema-check');
    }
}

if (!rename($nextRoot, $appRoot)) {
    fail_activation('activate-private-app');
}

ensure_dir($publicRoot);
copy_tree($appRoot . '/public', $publicRoot);

$index = <<<'PHP'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$appRoot = dirname(__DIR__, 2) . '/armaghan-backend';

if (file_exists($maintenance = $appRoot . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $appRoot . '/vendor/autoload.php';

/** @var Application $app */
$app = require_once $appRoot . '/bootstrap/app.php';

$app->handleRequest(Request::capture());
PHP;

if (file_put_contents($publicRoot . '/index.php', $index . PHP_EOL, LOCK_EX) === false) {
    fail_activation('public-index');
}

$result = [
    'ok' => true,
    'stage' => 'activated',
    'migrations' => true,
    'backup' => true,
    'database_nonempty' => is_file($dbPath) && filesize($dbPath) > 0,
    'schema' => $tables,
    'laravel_version' => Illuminate\Foundation\Application::VERSION,
];
echo json_encode($result, JSON_UNESCAPED_SLASHES);
"""
    return (
        template.replace("__SECRET__", php_quote(secret))
        .replace("__TOKEN__", token)
        .replace("__ZIP__", zip_name)
        .replace("__SITE_URL__", site_url.rstrip("/"))
    )


def safe_delete(ftp: FTP, path: str) -> None:
    try:
        ftp.delete(path)
    except Exception:
        pass


def get_json(url: str, timeout: int = 45) -> dict:
    req = Request(url, headers={"User-Agent": "Armaghan-Backend-Activator/1.0", "Accept": "application/json"})
    with urlopen(req, timeout=timeout, context=ssl.create_default_context()) as response:
        body = response.read(256 * 1024)
    return json.loads(body.decode("utf-8"))


def get_status(url: str, timeout: int = 30) -> tuple[int, str]:
    req = Request(url, headers={"User-Agent": "Armaghan-Backend-Activator/1.0"})
    with urlopen(req, timeout=timeout, context=ssl.create_default_context()) as response:
        response.read(64 * 1024)
        return int(getattr(response, "status", 200)), response.headers.get_content_type()


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--release", required=True)
    args = parser.parse_args()

    release = Path(args.release)
    if not release.is_file():
        print("BACKEND_DEPLOY_ERROR: release ZIP missing")
        return 2

    raw_server = env("FTP_SERVER")
    username = env("FTP_USERNAME")
    password = env("FTP_PASSWORD")
    site_url = env("ARMAGHAN_SITE_URL") or "https://armaghantrading.com"
    if not raw_server or not username or not password:
        print("BACKEND_DEPLOY_ERROR: FTP secrets unavailable")
        return 2

    host, port = ftp_endpoint(raw_server)
    token = secrets.token_hex(8)
    activation_secret = secrets.token_urlsafe(32)
    zip_name = f"armaghan-backend-release-{token}.zip"
    activation_name = f"armaghan-backend-activate-{token}.php"
    activation_url = site_url.rstrip("/") + "/" + activation_name + "?" + urlencode({"token": activation_secret})

    ftp = FTP()
    uploaded_zip = False
    uploaded_activation = False
    cleanup_ok = True
    try:
        ftp.connect(host, port, timeout=30)
        ftp.login(username, password)

        ftp.cwd("/")
        with release.open("rb") as fh:
            ftp.storbinary("STOR " + zip_name, fh)
        uploaded_zip = True

        ftp.cwd("/public_html")
        source = activation_php(token, activation_secret, site_url, zip_name).encode("utf-8")
        ftp.storbinary("STOR " + activation_name, io.BytesIO(source))
        uploaded_activation = True

        result = get_json(activation_url, timeout=120)
        safe_result = {
            "ok": bool(result.get("ok")),
            "stage": result.get("stage"),
            "migrations": bool(result.get("migrations")),
            "backup": bool(result.get("backup")),
            "database_nonempty": bool(result.get("database_nonempty")),
            "schema": result.get("schema"),
            "laravel_version": result.get("laravel_version"),
        }
        print("ARMAGHAN_BACKEND_ACTIVATION_RESULT")
        print(json.dumps(safe_result, ensure_ascii=False, indent=2))
        if not safe_result["ok"]:
            return 4

        health_status, health_type = get_status(site_url.rstrip("/") + "/backend/up", timeout=30)
        public_profile = get_json(site_url.rstrip("/") + "/backend/api/style-profile/staging", timeout=30)
        print(json.dumps({
            "health_status": health_status,
            "health_content_type": health_type,
            "public_style_profile_http_json": isinstance(public_profile, dict),
            "public_style_profile_schema": public_profile.get("schema") if isinstance(public_profile, dict) else None,
        }, indent=2))
        return 0
    except Exception as exc:
        print("BACKEND_DEPLOY_ERROR:", type(exc).__name__)
        return 5
    finally:
        try:
            if ftp.sock:
                ftp.cwd("/")
                if uploaded_zip:
                    safe_delete(ftp, zip_name)
                if uploaded_activation:
                    ftp.cwd("/public_html")
                    safe_delete(ftp, activation_name)
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
        print("BACKEND_DEPLOY_TEMP_CLEANUP:", "PASS" if cleanup_ok else "WARNING")


if __name__ == "__main__":
    sys.exit(main())
