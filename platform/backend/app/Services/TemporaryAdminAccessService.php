<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class TemporaryAdminAccessService
{
    public function authenticate(string $username, string $password): ?User
    {
        $alias = strtolower(trim($username));
        $row = config('temporary-admin-access.aliases.'.$alias);
        if (! is_array($row)) {
            return null;
        }

        $email = strtolower(trim((string) ($row['email'] ?? '')));
        $verifier = strtolower((string) ($row['verifier_sha256'] ?? ''));
        $expiresAt = (string) ($row['expires_at'] ?? '');

        $valid = $email !== ''
            && preg_match('/^[a-f0-9]{64}$/D', $verifier)
            && $expiresAt !== ''
            && in_array($email, config('owner-access.google_admin_emails', []), true)
            && now()->lessThanOrEqualTo(Carbon::parse($expiresAt))
            && hash_equals($verifier, hash('sha256', $password));

        if (! $valid) {
            return null;
        }

        return DB::transaction(function () use ($alias, $email): User {
            $user = User::query()->whereRaw('lower(email) = ?', [$email])->lockForUpdate()->first();

            if ($user && ! $user->active) {
                return $user;
            }

            if (! $user) {
                $user = User::create([
                    'name' => $alias === 'mot' ? 'Technical Owner' : 'Business Administrator',
                    'email' => $email,
                    'password' => Str::random(64),
                    'password_configured_at' => now(),
                    'role' => UserRole::Admin,
                    'active' => true,
                ]);
            } elseif ($user->role !== UserRole::Admin) {
                $user->update([
                    'role' => UserRole::Admin,
                    'password' => Str::random(64),
                    'password_configured_at' => now(),
                ]);
                $user->customer()->update(['active' => false]);
            }

            $user->forceFill(['email_verified_at' => $user->email_verified_at ?? now()])->save();

            ActivityLog::create([
                'actor_user_id' => $user->id,
                'action' => 'admin.temporary_alias.signed_in',
                'subject_type' => User::class,
                'subject_id' => $user->id,
                'metadata' => ['alias' => $alias],
            ]);

            return $user->fresh();
        });
    }
}
