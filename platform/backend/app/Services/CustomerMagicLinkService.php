<?php

namespace App\Services;

use App\Exceptions\MagicLinkUnavailable;
use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\MagicLink;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class CustomerMagicLinkService
{
    public const SCOPE = 'customer_portal';

    /**
     * @return array{magic_link: MagicLink, url: string}
     */
    public function issue(Customer $customer, int $expiresInHours = 72, ?int $actorUserId = null): array
    {
        if (! $customer->active || ! $customer->direct_link_enabled) {
            throw ValidationException::withMessages([
                'customer' => 'Customer direct access is disabled.',
            ]);
        }

        $expiresInHours = max(1, min(168, $expiresInHours));
        $token = Str::random(64);
        $tokenHash = hash('sha256', $token);

        return DB::transaction(function () use ($customer, $expiresInHours, $actorUserId, $token, $tokenHash): array {
            Customer::query()->whereKey($customer->getKey())->lockForUpdate()->firstOrFail();

            MagicLink::query()
                ->where('customer_id', $customer->getKey())
                ->where('scope', self::SCOPE)
                ->where('enabled', true)
                ->whereNull('last_used_at')
                ->whereNull('revoked_at')
                ->update([
                    'enabled' => false,
                    'revoked_at' => now(),
                    'updated_at' => now(),
                ]);

            $magicLink = MagicLink::query()->create([
                'customer_id' => $customer->getKey(),
                'token_hash' => $tokenHash,
                'scope' => self::SCOPE,
                'enabled' => true,
                'expires_at' => now()->addHours($expiresInHours),
            ]);

            ActivityLog::query()->create([
                'actor_user_id' => $actorUserId,
                'customer_id' => $customer->getKey(),
                'action' => 'customer.magic_link.issued',
                'subject_type' => MagicLink::class,
                'subject_id' => $magicLink->getKey(),
                'metadata' => [
                    'scope' => self::SCOPE,
                    'expires_in_hours' => $expiresInHours,
                ],
            ]);

            return [
                'magic_link' => $magicLink,
                'url' => route('customer.magic.consume', ['token' => $token]),
            ];
        });
    }

    public function consume(string $token): Customer
    {
        if (strlen($token) !== 64 || ! ctype_alnum($token)) {
            throw new MagicLinkUnavailable('Magic link is unavailable.');
        }

        $tokenHash = hash('sha256', $token);

        return DB::transaction(function () use ($tokenHash): Customer {
            $magicLink = MagicLink::query()
                ->where('token_hash', $tokenHash)
                ->where('scope', self::SCOPE)
                ->lockForUpdate()
                ->first();

            if (! $magicLink instanceof MagicLink || ! $this->isConsumable($magicLink)) {
                throw new MagicLinkUnavailable('Magic link is unavailable.');
            }

            $customer = Customer::query()
                ->whereKey($magicLink->customer_id)
                ->where('active', true)
                ->where('direct_link_enabled', true)
                ->first();

            if (! $customer instanceof Customer) {
                throw new MagicLinkUnavailable('Customer access is unavailable.');
            }

            $claimed = MagicLink::query()
                ->whereKey($magicLink->getKey())
                ->where('enabled', true)
                ->whereNull('last_used_at')
                ->whereNull('revoked_at')
                ->where('expires_at', '>', now())
                ->update([
                    'enabled' => false,
                    'last_used_at' => now(),
                    'updated_at' => now(),
                ]);

            if ($claimed !== 1) {
                throw new MagicLinkUnavailable('Magic link is unavailable.');
            }

            ActivityLog::query()->create([
                'customer_id' => $customer->getKey(),
                'action' => 'customer.magic_link.consumed',
                'subject_type' => MagicLink::class,
                'subject_id' => $magicLink->getKey(),
                'metadata' => [
                    'scope' => self::SCOPE,
                ],
            ]);

            return $customer;
        });
    }

    public function revoke(Customer $customer, ?int $actorUserId = null): int
    {
        return DB::transaction(function () use ($customer, $actorUserId): int {
            Customer::query()->whereKey($customer->getKey())->lockForUpdate()->firstOrFail();

            $count = MagicLink::query()
                ->where('customer_id', $customer->getKey())
                ->where('scope', self::SCOPE)
                ->where('enabled', true)
                ->whereNull('last_used_at')
                ->whereNull('revoked_at')
                ->update([
                    'enabled' => false,
                    'revoked_at' => now(),
                    'updated_at' => now(),
                ]);

            ActivityLog::query()->create([
                'actor_user_id' => $actorUserId,
                'customer_id' => $customer->getKey(),
                'action' => 'customer.magic_link.revoked',
                'subject_type' => Customer::class,
                'subject_id' => $customer->getKey(),
                'metadata' => [
                    'scope' => self::SCOPE,
                    'revoked_count' => $count,
                ],
            ]);

            return $count;
        });
    }

    private function isConsumable(MagicLink $magicLink): bool
    {
        if (! $magicLink->enabled || $magicLink->last_used_at !== null || $magicLink->revoked_at !== null) {
            return false;
        }

        if ($magicLink->expires_at === null) {
            return false;
        }

        return CarbonImmutable::instance($magicLink->expires_at)->isFuture();
    }
}
