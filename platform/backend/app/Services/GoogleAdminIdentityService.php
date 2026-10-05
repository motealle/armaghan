<?php
namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as ProviderUser;

final class GoogleAdminIdentityService
{
    public function resolve(ProviderUser $identity): ?User
    {
        $email = strtolower(trim((string) $identity->getEmail()));
        if (! in_array($email, config('owner-access.google_admin_emails', []), true)) {
            return null;
        }
        if (! preg_match('/^[A-Za-z0-9_-]{1,255}$/D', (string) $identity->getId())
            || ! in_array($identity->getRaw()['email_verified'] ?? null, [true, 'true'], true)) {
            throw new DomainException('Verified owner identity required');
        }
        return DB::transaction(function () use ($email, $identity): User {
            $user = User::withoutGlobalScope('account_archive')->whereRaw('lower(email) = ?', [$email])->lockForUpdate()->first();
            if ($user && ! $user->active) {
                throw new DomainException('Account unavailable');
            }
            if (! $user) {
                $user = User::create(['name' => mb_substr((string) ($identity->getName() ?: 'Administrator'), 0, 255),
                    'email' => $email, 'password' => Str::random(64), 'role' => UserRole::Admin, 'active' => true]);
            } elseif ($user->role !== UserRole::Admin) {
                // Never preserve a pre-existing customer password when elevating.
                $user->update(['role' => UserRole::Admin, 'password' => Str::random(64)]);
                $user->customer()->update(['active' => false]);
            }
            $user->forceFill(['email_verified_at' => now()])->save();
            return $user;
        });
    }
}
