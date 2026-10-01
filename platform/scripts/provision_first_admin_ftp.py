#!/usr/bin/env python3
"""Secure one-shot first-admin inspection/provision/recovery over FTP.

Plaintext credentials are never printed. The remote PHP helper can:
- inspect active-admin metadata without mutation;
- provision the first admin when none exists;
- recover the exact bootstrap admin created by a failed prior run, but only
  when email/name match and created_at is at/after a caller-supplied threshold.

For provision/recovery, a strong password is generated on-host, encrypted with
an ephemeral RSA public key before any database mutation, then stored hashed
through the existing Laravel User model. Only RSA ciphertext leaves the host.
The temporary public PHP helper is always removed over FTP.
"""
from __future__ import annotations

import argparse
import base64
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


def bootstrap_php(
    secret: str,
    email: str,
    name: str,
    public_key_b64: str,
    inspect_only: bool = False,
    recover_after: str = "",
) -> str:
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

$email = __EMAIL__;
$name = __NAME__;
$inspectOnly = __INSPECT_ONLY__;
$recoverAfter = __RECOVER_AFTER__;

$admins = App\Models\User::query()
    ->where('role', App\Enums\UserRole::Admin->value)
    ->where('active', true)
    ->orderBy('id')
    ->get(['id', 'name', 'email', 'created_at']);

$adminCount = $admins->count();

if ($inspectOnly) {
    echo json_encode([
        'ok' => true,
        'stage' => 'inspect',
        'active_admin_count' => $adminCount,
        'admins' => $admins->map(fn ($admin) => [
            'id' => $admin->id,
            'name' => $admin->name,
            'email' => $admin->email,
            'created_at' => optional($admin->created_at)->toISOString(),
        ])->values()->all(),
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

$publicKeyPem = base64_decode(__PUBLIC_KEY__, true);
if (!is_string($publicKeyPem) || $publicKeyPem === '') {
    http_response_code(500);
    echo json_encode(['ok' => false, 'stage' => 'public-key']);
    exit;
}

$password = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');

$publicKey = openssl_pkey_get_public($publicKeyPem);
if ($publicKey === false) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'stage' => 'public-key-load']);
    exit;
}

$encrypted = '';
if (!openssl_public_encrypt(
    $password,
    $encrypted,
    $publicKey,
    OPENSSL_PKCS1_OAEP_PADDING,
)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'stage' => 'credential-encryption']);
    exit;
}

$recovering = false;
$recoverUser = null;

if ($adminCount > 0) {
    if ($adminCount !== 1 || $recoverAfter === '') {
        http_response_code(409);
        echo json_encode([
            'ok' => false,
            'stage' => 'existing-admin',
            'active_admin_count' => $adminCount,
        ]);
        exit;
    }

    $candidate = $admins->first();
    $createdAt = optional($candidate->created_at)->toISOString();
    $matchesBootstrap = strtolower((string) $candidate->email) === strtolower($email)
        && (string) $candidate->name === $name
        && is_string($createdAt)
        && strcmp($createdAt, $recoverAfter) >= 0;

    if (!$matchesBootstrap) {
        http_response_code(409);
        echo json_encode([
            'ok' => false,
            'stage' => 'existing-admin-mismatch',
            'active_admin_count' => $adminCount,
        ]);
        exit;
    }

    $recovering = true;
    $recoverUser = $candidate;
}

if (!$recovering && App\Models\User::query()->whereRaw('LOWER(email) = ?', [strtolower($email)])->exists()) {
    http_response_code(409);
    echo json_encode(['ok' => false, 'stage' => 'email-exists']);
    exit;
}

try {
    $user = Illuminate\Support\Facades\DB::transaction(function () use (
        $name,
        $email,
        $password,
        $recovering,
        $recoverUser
    ) {
        if ($recovering) {
            $user = App\Models\User::query()->lockForUpdate()->findOrFail($recoverUser->id);
            $user->forceFill([
                'password' => $password,
                'remember_token' => Illuminate\Support\Str::random(60),
            ])->save();

            return $user->fresh();
        }

        $user = App\Models\User::query()->create([
            'name' => $name,
            'email' => strtolower($email),
            'password' => $password,
            'role' => App\Enums\UserRole::Admin->value,
            'active' => true,
        ]);

        $user->forceFill([
            'email_verified_at' => now(),
            'remember_token' => Illuminate\Support\Str::random(60),
        ])->save();

        return $user->fresh();
    }, 3);

    if (!$user->isActiveAdmin()) {
        throw new RuntimeException('admin-policy');
    }

    if (!Illuminate\Support\Facades\Hash::check($password, $user->password)) {
        throw new RuntimeException('password-hash');
    }

    echo json_encode([
        'ok' => true,
        'stage' => $recovering ? 'recovered' : 'provisioned',
        'user_id' => $user->id,
        'email' => $user->email,
        'active_admin_count' => App\Models\User::query()
            ->where('role', App\Enums\UserRole::Admin->value)
            ->where('active', true)
            ->count(),
        'password_ciphertext_b64' => base64_encode($encrypted),
    ], JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'stage' => 'provision-failed']);
}
"""
    return (
        template.replace("__SECRET__", php_quote(secret))
        .replace("__EMAIL__", php_quote(email))
        .replace("__NAME__", php_quote(name))
        .replace("__INSPECT_ONLY__", "true" if inspect_only else "false")
        .replace("__RECOVER_AFTER__", php_quote(recover_after))
        .replace("__PUBLIC_KEY__", php_quote(public_key_b64))
    )


def get_json(url: str, timeout: int = 45) -> dict:
    req = Request(url, headers={
        "User-Agent": "Armaghan-First-Admin-Provisioner/1.0",
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
    parser = argparse.ArgumentParser()
    parser.add_argument("--email", default="admin@armaghan.local")
    parser.add_argument("--name", default="Armaghan Administrator")
    parser.add_argument("--inspect-only", action="store_true")
    parser.add_argument("--recover-after", default="")
    args = parser.parse_args()

    raw_server = env("FTP_SERVER")
    username = env("FTP_USERNAME")
    password = env("FTP_PASSWORD")
    site_url = env("ARMAGHAN_SITE_URL") or "https://armaghantrading.com"
    public_key_b64 = env("ARMAGHAN_BOOTSTRAP_PUBLIC_KEY_B64")

    if not raw_server or not username or not password or not public_key_b64:
        print("FIRST_ADMIN_PROVISION_ERROR: required secure inputs unavailable")
        return 2

    try:
        base64.b64decode(public_key_b64, validate=True)
    except Exception:
        print("FIRST_ADMIN_PROVISION_ERROR: invalid public key encoding")
        return 2

    host, port = ftp_endpoint(raw_server)
    token = secrets.token_hex(8)
    request_secret = secrets.token_urlsafe(32)
    bootstrap_name = f"armaghan-first-admin-{token}.php"
    bootstrap_url = site_url.rstrip("/") + "/" + bootstrap_name + "?" + urlencode({"token": request_secret})

    ftp = FTP()
    uploaded = False
    cleanup_ok = True
    try:
        ftp.connect(host, port, timeout=30)
        ftp.login(username, password)
        ftp.cwd("/public_html")
        source = bootstrap_php(
            secret=request_secret,
            email=args.email,
            name=args.name,
            public_key_b64=public_key_b64,
            inspect_only=args.inspect_only,
            recover_after=args.recover_after,
        ).encode("utf-8")
        ftp.storbinary("STOR " + bootstrap_name, io.BytesIO(source))
        uploaded = True

        result = get_json(bootstrap_url, timeout=90)
        safe_result = {
            "ok": bool(result.get("ok")),
            "stage": result.get("stage"),
            "user_id": result.get("user_id"),
            "email": result.get("email"),
            "active_admin_count": result.get("active_admin_count"),
            "password_ciphertext_b64": result.get("password_ciphertext_b64"),
            "admins": result.get("admins") if args.inspect_only else None,
        }
        print("ARMAGHAN_FIRST_ADMIN_RESULT")
        print(json.dumps(safe_result, ensure_ascii=False, indent=2))

        if not safe_result["ok"]:
            return 4
        if not args.inspect_only and not safe_result["password_ciphertext_b64"]:
            return 4

        return 0
    except Exception as exc:
        print("FIRST_ADMIN_PROVISION_ERROR:", type(exc).__name__)
        return 5
    finally:
        try:
            if ftp.sock:
                ftp.cwd("/public_html")
                if uploaded:
                    safe_delete(ftp, bootstrap_name)
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
        print("FIRST_ADMIN_TEMP_CLEANUP:", "PASS" if cleanup_ok else "WARNING")


if __name__ == "__main__":
    sys.exit(main())
