<?php
namespace App\Services;

use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\CustomerGoogleIdentity;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as ProviderUser;

final class GoogleCustomerIdentityService
{
    public function resolve(ProviderUser $identity, ?Customer $current = null): Customer
    {
        $subject = (string) $identity->getId();
        $email = strtolower(trim((string) $identity->getEmail()));
        $raw = $identity->getRaw();
        if (! preg_match('/^[A-Za-z0-9_-]{1,255}$/D', $subject)
            || strlen($email) > 255 || ! filter_var($email, FILTER_VALIDATE_EMAIL)
            || ! in_array($raw['email_verified'] ?? null, [true, 'true'], true)) {
            throw new DomainException('Google identity unavailable');
        }

        return DB::transaction(function () use ($subject, $email, $identity, $current): Customer {
            $linked = CustomerGoogleIdentity::query()->where('google_subject', $subject)->lockForUpdate()->first();
            if ($linked) {
                $customer = $linked->customer()->with('user')->first();
                $this->requireCustomer($customer);
                return $customer;
            }
            $user = User::withoutGlobalScope('account_archive')->whereRaw('lower(email) = ?', [$email])->lockForUpdate()->first();
            // Email equality alone never grants an existing account or administrator access.
            if ($user && (! $current || (int) $current->user_id !== (int) $user->id
                || $user->role !== UserRole::Customer || ! $user->active)) {
                throw new DomainException('Existing account requires authenticated linking');
            }
            if ($current && (! $current->active || ($current->user_id && (! $user || (int) $current->user_id !== (int) $user->id)))) {
                throw new DomainException('Customer linking unavailable');
            }
            if ($current && CustomerGoogleIdentity::query()->where('customer_id', $current->id)->exists()) {
                throw new DomainException('Customer already linked');
            }
            if (! $user) {
                $user = User::create([
                    'name' => mb_substr((string) ($identity->getName() ?: 'Customer'), 0, 255),
                    'email' => $email,
                    'password' => Str::random(64),
                    'role' => UserRole::Customer,
                    'active' => true,
                ]);
                $user->forceFill(['email_verified_at' => now()])->save();
            }
            $customer = $current
                ? Customer::query()->whereKey($current->id)->lockForUpdate()->firstOrFail()
                : Customer::create(['user_id' => $user->id, 'active' => true, 'direct_link_enabled' => true]);
            if ($current && ! $customer->user_id) {
                $customer->update(['user_id' => $user->id]);
            }
            $this->requireCustomer($customer->load('user'));
            CustomerGoogleIdentity::create(['customer_id' => $customer->id, 'google_subject' => $subject]);
            return $customer;
        });
    }

    private function requireCustomer(?Customer $customer): void
    {
        if (! $customer || ! $customer->active || ! $customer->user
            || ! $customer->user->active || $customer->user->role !== UserRole::Customer) {
            throw new DomainException('Customer unavailable');
        }
    }
}
