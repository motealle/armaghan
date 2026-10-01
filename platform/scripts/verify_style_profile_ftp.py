#!/usr/bin/env python3
"""Transactional production acceptance for Armaghan Style Profile persistence.

Uploads a short-lived token-protected PHP helper, boots the already deployed
Laravel application, exercises draft save / staging publish / restore against
the live production SQLite database inside one outer transaction, then rolls
that transaction back and verifies the database returned to its original
logical state. No admin password/session is created or bypassed.
"""
from __future__ import annotations

import io
import json
import os
import secrets
import ssl
import sys
from ftplib import FTP
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


def acceptance_php(secret: str, probe_id: str) -> str:
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

$db = Illuminate\Support\Facades\DB::connection();
$service = $app->make(App\Services\StyleProfileService::class);
$probeId = __PROBE_ID__;

function snapshot_state(): array {
    $profile = App\Models\StyleProfile::query()
        ->where('slug', App\Services\StyleProfileService::DEFAULT_SLUG)
        ->first();
    $publication = App\Models\StyleProfilePublication::query()
        ->where('channel', 'staging')
        ->first();

    return [
        'profiles' => App\Models\StyleProfile::query()->count(),
        'versions' => App\Models\StyleProfileVersion::query()->count(),
        'publications' => App\Models\StyleProfilePublication::query()->count(),
        'activity' => App\Models\ActivityLog::query()->count(),
        'default_exists' => $profile !== null,
        'draft_checksum' => $profile?->draft_checksum,
        'staging_version_id' => $publication?->style_profile_version_id,
    ];
}

$admins = App\Models\User::query()
    ->where('role', App\Enums\UserRole::Admin->value)
    ->where('active', true)
    ->orderBy('id')
    ->get();

if ($admins->count() !== 1) {
    http_response_code(409);
    echo json_encode([
        'ok' => false,
        'stage' => 'admin-count',
        'active_admin_count' => $admins->count(),
    ]);
    exit;
}

$actor = $admins->first();
if (!$actor->isActiveAdmin()) {
    http_response_code(409);
    echo json_encode(['ok' => false, 'stage' => 'admin-policy']);
    exit;
}

$before = snapshot_state();
$inside = [];
$rolledBack = false;

try {
    $db->beginTransaction();

    $profile = $service->ensureDefault($actor);
    $baseStyles = is_array($profile->draft_styles) ? $profile->draft_styles : [];
    $baseTexts = is_array($profile->draft_texts) ? $profile->draft_texts : [];

    $stylesOne = $baseStyles;
    $stylesOne[$probeId] = ['backgroundColor' => 'blue'];

    $draftOne = $service->saveDraft($actor, [
        'name' => $profile->name,
        'expected_checksum' => $profile->draft_checksum,
        'styles' => $stylesOne,
        'texts' => $baseTexts,
    ]);

    if (!isset($draftOne->draft_styles[$probeId])
        || ($draftOne->draft_styles[$probeId]['backgroundColor'] ?? null) !== 'blue') {
        throw new RuntimeException('draft-one');
    }

    $versionOne = $service->publish($actor, 'staging', '27');

    $stylesTwo = $draftOne->draft_styles;
    $stylesTwo[$probeId] = ['backgroundColor' => 'green'];

    $draftTwo = $service->saveDraft($actor, [
        'name' => $draftOne->name,
        'expected_checksum' => $draftOne->draft_checksum,
        'styles' => $stylesTwo,
        'texts' => $draftOne->draft_texts,
    ]);

    if (($draftTwo->draft_styles[$probeId]['backgroundColor'] ?? null) !== 'green') {
        throw new RuntimeException('draft-two');
    }

    $versionTwo = $service->publish($actor, 'staging', '27');
    $versionThree = $service->restore($actor, $versionOne, 'staging', '27');

    $finalProfile = App\Models\StyleProfile::query()
        ->where('slug', App\Services\StyleProfileService::DEFAULT_SLUG)
        ->firstOrFail();
    $finalPublication = App\Models\StyleProfilePublication::query()
        ->where('channel', 'staging')
        ->firstOrFail();

    $inside = [
        'draft_save' => ($finalProfile->draft_styles[$probeId]['backgroundColor'] ?? null) === 'blue',
        'publish_sequence' => $versionTwo->version > $versionOne->version,
        'restore_new_version' => $versionThree->version > $versionTwo->version,
        'staging_points_to_restore' => $finalPublication->style_profile_version_id === $versionThree->id,
        'version_delta' => App\Models\StyleProfileVersion::query()->count() - $before['versions'],
        'activity_delta' => App\Models\ActivityLog::query()->count() - $before['activity'],
    ];

    foreach (['draft_save', 'publish_sequence', 'restore_new_version', 'staging_points_to_restore'] as $check) {
        if (!$inside[$check]) {
            throw new RuntimeException('acceptance-check');
        }
    }

    if ($inside['version_delta'] < 3 || $inside['activity_delta'] < 3) {
        throw new RuntimeException('acceptance-count');
    }

    $db->rollBack();
    $rolledBack = true;
} catch (Throwable $e) {
    if ($db->transactionLevel() > 0) {
        $db->rollBack();
        $rolledBack = true;
    }

    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'stage' => 'acceptance',
        'rolled_back' => $rolledBack,
        'error_type' => get_class($e),
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

$after = snapshot_state();
$rollbackIntact = $before == $after;

echo json_encode([
    'ok' => $rollbackIntact,
    'stage' => $rollbackIntact ? 'verified' : 'rollback-mismatch',
    'sqlite_driver' => Illuminate\Support\Facades\DB::connection()->getDriverName() === 'sqlite',
    'active_admin_count' => $admins->count(),
    'draft_save' => $inside['draft_save'] ?? false,
    'publish_sequence' => $inside['publish_sequence'] ?? false,
    'restore_new_version' => $inside['restore_new_version'] ?? false,
    'staging_points_to_restore' => $inside['staging_points_to_restore'] ?? false,
    'version_delta_inside_tx' => $inside['version_delta'] ?? null,
    'activity_delta_inside_tx' => $inside['activity_delta'] ?? null,
    'rolled_back' => $rolledBack,
    'rollback_intact' => $rollbackIntact,
], JSON_UNESCAPED_SLASHES);
"""
    return (
        template.replace("__SECRET__", php_quote(secret))
        .replace("__PROBE_ID__", php_quote(probe_id))
    )


def get_json(url: str, timeout: int = 90) -> dict:
    req = Request(url, headers={
        "User-Agent": "Armaghan-Style-Profile-Acceptance/1.0",
        "Accept": "application/json",
    })
    try:
        with urlopen(req, timeout=timeout, context=ssl.create_default_context()) as response:
            body = response.read(256 * 1024)
            return json.loads(body.decode("utf-8"))
    except Exception as exc:
        body = getattr(exc, "read", lambda *_: b"")()
        if body:
            try:
                return json.loads(body.decode("utf-8"))
            except Exception:
                pass
        raise


def safe_delete(ftp: FTP, path: str) -> None:
    try:
        ftp.delete(path)
    except Exception:
        pass


def main() -> int:
    raw_server = env("FTP_SERVER")
    username = env("FTP_USERNAME")
    password = env("FTP_PASSWORD")
    site_url = env("ARMAGHAN_SITE_URL") or "https://armaghantrading.com"

    if not raw_server or not username or not password:
        print("STYLE_PROFILE_ACCEPTANCE_ERROR: FTP secrets unavailable")
        return 2

    host, port = ftp_endpoint(raw_server)
    token = secrets.token_hex(8)
    request_secret = secrets.token_urlsafe(32)
    probe_id = "acceptance.probe_" + secrets.token_hex(4)
    helper_name = f"armaghan-style-profile-accept-{token}.php"
    helper_url = site_url.rstrip("/") + "/" + helper_name + "?" + urlencode({"token": request_secret})

    ftp = FTP()
    uploaded = False
    cleanup_ok = True
    try:
        ftp.connect(host, port, timeout=30)
        ftp.login(username, password)
        ftp.cwd("/public_html")
        source = acceptance_php(request_secret, probe_id).encode("utf-8")
        ftp.storbinary("STOR " + helper_name, io.BytesIO(source))
        uploaded = True

        result = get_json(helper_url, timeout=120)
        safe_result = {
            "ok": bool(result.get("ok")),
            "stage": result.get("stage"),
            "sqlite_driver": bool(result.get("sqlite_driver")),
            "active_admin_count": result.get("active_admin_count"),
            "draft_save": bool(result.get("draft_save")),
            "publish_sequence": bool(result.get("publish_sequence")),
            "restore_new_version": bool(result.get("restore_new_version")),
            "staging_points_to_restore": bool(result.get("staging_points_to_restore")),
            "version_delta_inside_tx": result.get("version_delta_inside_tx"),
            "activity_delta_inside_tx": result.get("activity_delta_inside_tx"),
            "rolled_back": bool(result.get("rolled_back")),
            "rollback_intact": bool(result.get("rollback_intact")),
            "error_type": result.get("error_type"),
        }
        print("ARMAGHAN_STYLE_PROFILE_PRODUCTION_ACCEPTANCE")
        print(json.dumps(safe_result, ensure_ascii=False, indent=2))

        required = [
            safe_result["ok"],
            safe_result["sqlite_driver"],
            safe_result["draft_save"],
            safe_result["publish_sequence"],
            safe_result["restore_new_version"],
            safe_result["staging_points_to_restore"],
            safe_result["rolled_back"],
            safe_result["rollback_intact"],
        ]
        return 0 if all(required) else 4
    except Exception as exc:
        print("STYLE_PROFILE_ACCEPTANCE_ERROR:", type(exc).__name__)
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
        print("STYLE_PROFILE_ACCEPTANCE_TEMP_CLEANUP:", "PASS" if cleanup_ok else "WARNING")


if __name__ == "__main__":
    sys.exit(main())
