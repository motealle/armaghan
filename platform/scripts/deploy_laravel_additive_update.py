#!/usr/bin/env python3
"""Guarded additive migration/dependency updater for the Armaghan Laravel backend.

This lane is intentionally narrower than a general migration runner:
- every existing migration must be byte-identical in the candidate;
- new migrations may only create new tables/indexes in their up() method;
- a consistent SQLite snapshot is created before migrations;
- old code keeps serving until candidate migrations succeed;
- code/public swaps are rollback-capable;
- schema additions are retained on code rollback because they are required to
  be backward-compatible additive changes.

It is designed for packages such as Spatie Media Library that add a new table
without mutating existing business tables.
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


def helper_php(token: str, secret: str, zip_name: str) -> str:
    template = r"""<?php
header('Content-Type: application/json; charset=utf-8');

$expected = __SECRET__;
$provided = (string) ($_GET['token'] ?? '');
if (!hash_equals($expected, $provided)) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'stage' => 'auth']);
    exit;
}

$action = (string) ($_GET['action'] ?? 'activate');
$accountRoot = dirname(__DIR__);
$appRoot = $accountRoot . '/armaghan-backend';
$nextRoot = $accountRoot . '/armaghan-backend-next-__TOKEN__';
$prevRoot = $accountRoot . '/armaghan-backend-prev-__TOKEN__';
$dataRoot = $accountRoot . '/armaghan-data';
$backupRoot = $dataRoot . '/backups';
$sharedEnv = $dataRoot . '/.env';
$dbPath = $dataRoot . '/database.sqlite';
$zipPath = $accountRoot . '/__ZIP__';
$publicRoot = __DIR__ . '/backend';
$publicNext = __DIR__ . '/backend-next-__TOKEN__';
$publicPrev = __DIR__ . '/backend-prev-__TOKEN__';
$backupPath = $backupRoot . '/armaghan-pre-additive-update-' . gmdate('Ymd-His') . '-__TOKEN__.sqlite';

function fail_update(string $stage, int $status = 500, array $extra = []): void {
    http_response_code($status);
    echo json_encode(array_merge(['ok' => false, 'stage' => $stage], $extra), JSON_UNESCAPED_SLASHES);
    exit;
}
function ensure_dir(string $path): void {
    if (!is_dir($path) && !mkdir($path, 0770, true) && !is_dir($path)) {
        fail_update('mkdir');
    }
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
function copy_tree(string $source, string $destination): void {
    if (!is_dir($source)) return;
    ensure_dir($destination);
    @chmod($destination, 0755);
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($it as $item) {
        $relative = substr($item->getPathname(), strlen($source) + 1);
        $target = $destination . '/' . $relative;
        if ($item->isDir()) {
            ensure_dir($target);
            @chmod($target, 0755);
        } else {
            ensure_dir(dirname($target));
            if (!copy($item->getPathname(), $target)) fail_update('copy-tree');
            @chmod($target, 0644);
        }
    }
}
function migration_map(string $root): array {
    $files = glob($root . '/database/migrations/*.php') ?: [];
    sort($files, SORT_STRING);
    $map = [];
    foreach ($files as $file) {
        $map[basename($file)] = hash_file('sha256', $file);
    }
    return $map;
}
function additive_migration_is_safe(string $path): bool {
    $source = (string) file_get_contents($path);
    $upPos = strpos($source, 'function up');
    if ($upPos === false) return false;
    $downPos = strpos($source, 'function down', $upPos + 1);
    $upSource = ($downPos === false || $downPos <= $upPos)
        ? substr($source, $upPos)
        : substr($source, $upPos, $downPos - $upPos);
    $up = strtolower($upSource);

    if (!str_contains($up, 'schema::create')) return false;

    $blocked = [
        'schema::table',
        'schema::drop',
        'schema::rename',
        '->drop',
        '->rename',
        'db::statement',
        'db::unprepared',
        'delete from',
        'update ',
        'insert into',
        'alter table',
    ];
    foreach ($blocked as $needle) {
        if (str_contains($up, $needle)) return false;
    }
    return true;
}
function write_public_index(string $path): void {
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
    if (file_put_contents($path . '/index.php', $index . PHP_EOL, LOCK_EX) === false) {
        fail_update('public-index');
    }
    @chmod($path . '/index.php', 0644);
}
function ensure_public_storage_link(string $publicRoot, string $appRoot): void {
    $link = $publicRoot . '/storage';
    $target = $appRoot . '/storage/app/public';
    ensure_dir($target);

    if (is_link($link)) {
        $resolved = realpath($link);
        $targetResolved = realpath($target);
        if ($resolved !== false && $targetResolved !== false && $resolved === $targetResolved) return;
        if (!@unlink($link)) fail_update('storage-link-remove');
    } elseif (file_exists($link)) {
        fail_update('storage-link-conflict');
    }

    if (!function_exists('symlink') || !@symlink($target, $link)) {
        fail_update('storage-link-create');
    }
}

if ($action === 'cleanup') {
    remove_tree($prevRoot);
    remove_tree($publicPrev);
    remove_tree($nextRoot);
    remove_tree($publicNext);
    echo json_encode(['ok' => true, 'stage' => 'cleanup'], JSON_UNESCAPED_SLASHES);
    exit;
}
if ($action === 'rollback') {
    if (!is_dir($prevRoot) || !is_dir($publicPrev)) fail_update('rollback-missing-previous');
    remove_tree($appRoot);
    if (!rename($prevRoot, $appRoot)) fail_update('rollback-private');
    remove_tree($publicRoot);
    if (!rename($publicPrev, $publicRoot)) fail_update('rollback-public');
    ensure_public_storage_link($publicRoot, $appRoot);
    remove_tree($nextRoot);
    remove_tree($publicNext);
    echo json_encode([
        'ok' => true,
        'stage' => 'rolled-back',
        'schema_additions_retained' => true,
    ], JSON_UNESCAPED_SLASHES);
    exit;
}
if ($action !== 'activate') fail_update('unknown-action', 400);

foreach ([$appRoot, $publicRoot] as $requiredDir) {
    if (!is_dir($requiredDir)) fail_update('existing-app-missing');
}
foreach ([$sharedEnv, $dbPath, $zipPath] as $requiredFile) {
    if (!is_file($requiredFile)) fail_update('required-file-missing');
}
foreach ([$nextRoot, $prevRoot, $publicNext, $publicPrev] as $mustNotExist) {
    if (file_exists($mustNotExist)) fail_update('stale-stage-path');
}

$zip = new ZipArchive();
if ($zip->open($zipPath) !== true) fail_update('zip-open');
ensure_dir($nextRoot);
if (!$zip->extractTo($nextRoot)) { $zip->close(); fail_update('zip-extract'); }
$zip->close();

foreach (['vendor/autoload.php', 'bootstrap/app.php', 'artisan', 'public/index.php', 'composer.lock'] as $required) {
    if (!is_file($nextRoot . '/' . $required)) fail_update('release-validation');
}

$currentMigrations = migration_map($appRoot);
$candidateMigrations = migration_map($nextRoot);
foreach ($currentMigrations as $name => $hash) {
    if (!array_key_exists($name, $candidateMigrations)) {
        fail_update('migration-removed', 409, ['migration' => $name]);
    }
    if (!hash_equals($hash, $candidateMigrations[$name])) {
        fail_update('migration-modified', 409, ['migration' => $name]);
    }
}
$addedMigrations = array_values(array_diff(array_keys($candidateMigrations), array_keys($currentMigrations)));
foreach ($addedMigrations as $name) {
    if (!additive_migration_is_safe($nextRoot . '/database/migrations/' . $name)) {
        fail_update('migration-not-additive', 409, ['migration' => $name]);
    }
}

$dependencyChanged = hash_file('sha256', $appRoot . '/composer.lock') !== hash_file('sha256', $nextRoot . '/composer.lock');
if (!$dependencyChanged && count($addedMigrations) === 0) {
    fail_update('no-additive-drift', 409);
}

$mediaLibraryPresent = is_dir($nextRoot . '/vendor/spatie/laravel-medialibrary');
if ($mediaLibraryPresent && (!extension_loaded('gd') || !extension_loaded('exif'))) {
    fail_update('media-runtime-extension', 409, [
        'gd' => extension_loaded('gd'),
        'exif' => extension_loaded('exif'),
    ]);
}

if (!copy($sharedEnv, $nextRoot . '/.env')) fail_update('env-copy');
@chmod($nextRoot . '/.env', 0600);
foreach ([
    $nextRoot . '/storage/app',
    $nextRoot . '/storage/app/public',
    $nextRoot . '/storage/framework/cache/data',
    $nextRoot . '/storage/framework/sessions',
    $nextRoot . '/storage/framework/views',
    $nextRoot . '/storage/logs',
    $nextRoot . '/bootstrap/cache',
    $backupRoot,
] as $dir) ensure_dir($dir);

copy_tree($appRoot . '/storage/app', $nextRoot . '/storage/app');
copy_tree($appRoot . '/storage/logs', $nextRoot . '/storage/logs');

$pdo = new PDO('sqlite:' . $dbPath, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$pdo->exec('PRAGMA busy_timeout = 10000');
$pdo->exec('VACUUM INTO ' . $pdo->quote($backupPath));
clearstatcache(true, $backupPath);
if (!is_file($backupPath) || filesize($backupPath) <= 0) fail_update('backup');
@chmod($backupPath, 0600);

copy_tree($nextRoot . '/public', $publicNext);
write_public_index($publicNext);

chdir($nextRoot);
require $nextRoot . '/vendor/autoload.php';
$app = require $nextRoot . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$migrateExit = $kernel->call('migrate', ['--force' => true]);
if ($migrateExit !== 0) {
    try {
        Illuminate\Support\Facades\DB::purge('sqlite');
    } catch (Throwable $ignored) {
        // Continue with file-level restore.
    }
    if (!copy($backupPath, $dbPath)) {
        fail_update('migrate-restore-failed');
    }
    @chmod($dbPath, 0660);
    fail_update('migrate-restored');
}

if (!rename($appRoot, $prevRoot)) fail_update('stage-private-previous');
if (!rename($nextRoot, $appRoot)) {
    @rename($prevRoot, $appRoot);
    fail_update('activate-private');
}
if (!rename($publicRoot, $publicPrev)) {
    @rename($appRoot, $nextRoot);
    @rename($prevRoot, $appRoot);
    fail_update('stage-public-previous');
}
if (!rename($publicNext, $publicRoot)) {
    @rename($publicPrev, $publicRoot);
    @rename($appRoot, $nextRoot);
    @rename($prevRoot, $appRoot);
    fail_update('activate-public');
}
ensure_public_storage_link($publicRoot, $appRoot);

echo json_encode([
    'ok' => true,
    'stage' => 'activated',
    'backup_created' => true,
    'dependency_changed' => $dependencyChanged,
    'added_migrations' => count($addedMigrations),
    'schema_policy' => 'additive-create-only',
    'public_storage_link' => is_link($publicRoot . '/storage'),
], JSON_UNESCAPED_SLASHES);
"""
    return (
        template.replace("__SECRET__", php_quote(secret))
        .replace("__TOKEN__", token)
        .replace("__ZIP__", zip_name)
    )


def safe_delete(ftp: FTP, path: str) -> None:
    try:
        ftp.delete(path)
    except Exception:
        pass


def get_json(url: str, timeout: int = 120) -> dict:
    req = Request(url, headers={"User-Agent": "Armaghan-Backend-Additive-Updater/1.0", "Accept": "application/json"})
    try:
        with urlopen(req, timeout=timeout, context=ssl.create_default_context()) as response:
            return json.loads(response.read(256 * 1024).decode("utf-8"))
    except HTTPError as exc:
        body = exc.read(256 * 1024)
        try:
            return json.loads(body.decode("utf-8"))
        except Exception:
            raise


def get_status(url: str, timeout: int = 30) -> int:
    req = Request(url, headers={"User-Agent": "Armaghan-Backend-Additive-Updater/1.0"})
    try:
        with urlopen(req, timeout=timeout, context=ssl.create_default_context()) as response:
            response.read(64 * 1024)
            return int(getattr(response, "status", 200))
    except HTTPError as exc:
        return int(exc.code)


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--release", required=True)
    args = parser.parse_args()
    release = Path(args.release)
    if not release.is_file():
        print("BACKEND_ADDITIVE_UPDATE_ERROR: release ZIP missing")
        return 2

    raw_server, username, password = env("FTP_SERVER"), env("FTP_USERNAME"), env("FTP_PASSWORD")
    site_url = env("ARMAGHAN_SITE_URL") or "https://armaghantrading.com"
    if not raw_server or not username or not password:
        print("BACKEND_ADDITIVE_UPDATE_ERROR: FTP secrets unavailable")
        return 2

    host, port = ftp_endpoint(raw_server)
    token = secrets.token_hex(8)
    secret = secrets.token_urlsafe(32)
    zip_name = f"armaghan-backend-additive-{token}.zip"
    helper_name = f"armaghan-backend-additive-update-{token}.php"
    helper_base = site_url.rstrip("/") + "/" + helper_name

    ftp = FTP()
    uploaded_zip = uploaded_helper = activated = False
    cleanup_ok = True
    try:
        ftp.connect(host, port, timeout=30)
        ftp.login(username, password)
        ftp.cwd("/")
        with release.open("rb") as fh:
            ftp.storbinary("STOR " + zip_name, fh)
        uploaded_zip = True

        ftp.cwd("/public_html")
        ftp.storbinary("STOR " + helper_name, io.BytesIO(helper_php(token, secret, zip_name).encode("utf-8")))
        uploaded_helper = True

        result = get_json(helper_base + "?" + urlencode({"token": secret, "action": "activate"}), timeout=180)
        safe_result = {
            "ok": bool(result.get("ok")),
            "stage": result.get("stage"),
            "backup_created": bool(result.get("backup_created")),
            "dependency_changed": result.get("dependency_changed"),
            "added_migrations": result.get("added_migrations"),
            "schema_policy": result.get("schema_policy"),
            "public_storage_link": bool(result.get("public_storage_link")),
            "migration": result.get("migration"),
        }
        print("ARMAGHAN_BACKEND_ADDITIVE_UPDATE")
        print(json.dumps(safe_result, indent=2))
        if not safe_result["ok"]:
            return 4
        activated = True

        checks = {
            "health": get_status(site_url.rstrip("/") + "/backend/up"),
            "admin_login": get_status(site_url.rstrip("/") + "/backend/admin/login"),
            "products_route": get_status(site_url.rstrip("/") + "/backend/admin/products"),
            "catalog_categories": get_status(site_url.rstrip("/") + "/backend/api/catalog/categories"),
            "catalog_products": get_status(site_url.rstrip("/") + "/backend/api/catalog/products?per_page=1"),
        }
        print("ARMAGHAN_BACKEND_ADDITIVE_UPDATE_SMOKE")
        print(json.dumps(checks, indent=2))
        smoke_ok = (
            checks["health"] == 200
            and checks["admin_login"] == 200
            and checks["products_route"] in (200, 302, 303)
            and checks["catalog_categories"] == 200
            and checks["catalog_products"] == 200
        )
        if not smoke_ok:
            rollback = get_json(helper_base + "?" + urlencode({"token": secret, "action": "rollback"}), timeout=120)
            rollback_health = get_status(site_url.rstrip("/") + "/backend/up")
            rollback_ok = bool(rollback.get("ok")) and rollback_health == 200
            print("ARMAGHAN_BACKEND_ADDITIVE_UPDATE_ROLLBACK:", "PASS" if rollback_ok else "FAIL")
            print("ARMAGHAN_BACKEND_ADDITIVE_UPDATE_ROLLBACK_HEALTH:", rollback_health)
            print("ARMAGHAN_BACKEND_ADDITIVE_UPDATE_SCHEMA_RETAINED:", bool(rollback.get("schema_additions_retained")))
            activated = False
            return 6

        cleanup = get_json(helper_base + "?" + urlencode({"token": secret, "action": "cleanup"}), timeout=120)
        if not cleanup.get("ok"):
            print("BACKEND_ADDITIVE_UPDATE_WARNING: previous-release cleanup incomplete")
            return 7
        activated = False
        return 0
    except Exception as exc:
        print("BACKEND_ADDITIVE_UPDATE_ERROR:", type(exc).__name__)
        if activated:
            try:
                rollback = get_json(helper_base + "?" + urlencode({"token": secret, "action": "rollback"}), timeout=120)
                rollback_health = get_status(site_url.rstrip("/") + "/backend/up")
                rollback_ok = bool(rollback.get("ok")) and rollback_health == 200
                print("ARMAGHAN_BACKEND_ADDITIVE_UPDATE_ROLLBACK:", "PASS" if rollback_ok else "FAIL")
                print("ARMAGHAN_BACKEND_ADDITIVE_UPDATE_ROLLBACK_HEALTH:", rollback_health)
            except Exception:
                print("ARMAGHAN_BACKEND_ADDITIVE_UPDATE_ROLLBACK: ERROR")
        return 5
    finally:
        try:
            if ftp.sock:
                ftp.cwd("/")
                if uploaded_zip:
                    safe_delete(ftp, zip_name)
                if uploaded_helper:
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
        print("BACKEND_ADDITIVE_UPDATE_TEMP_CLEANUP:", "PASS" if cleanup_ok else "WARNING")


if __name__ == "__main__":
    sys.exit(main())
