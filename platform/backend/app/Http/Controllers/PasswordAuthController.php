<?php
namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\AdminAuthState;
use App\Models\User;
use App\Models\CustomerLoginIdentity;
use App\Support\CustomerPhone;
use App\Rules\CustomerSimplePassword;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Support\CustomerSession;
use App\Services\TemporaryAdminAccessService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class PasswordAuthController extends Controller
{
    public function login(Request $request, TemporaryAdminAccessService $temporary): JsonResponse
    {
        $identifier = trim((string) ($request->input('identifier') ?? $request->input('email') ?? ''));
        $request->merge(['identifier' => $identifier]);
        $data = $request->validate([
            'identifier' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        $user = null;
        if (str_contains($data['identifier'], '@')) {
            $email = strtolower($data['identifier']);
            $candidate = User::query()->whereRaw('lower(email) = ?', [$email])->first();
            // Equal hashing work for a missing account. This is an unusable dummy hash.
            $valid = Hash::check($data['password'], $candidate?->password ?? '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.');
            if ($valid) {
                $user = $candidate;
            }
        } else {
            $user = $temporary->authenticate($data['identifier'], $data['password']);
            // Never let a customer username shadow a reserved administrator alias.
            if (! $user && ! array_key_exists(strtolower($data['identifier']), config('temporary-admin-access.aliases', []))) {
                $phone = CustomerPhone::normalize($data['identifier']);
                $username = $phone ? null : CustomerPhone::username($data['identifier']);
                $identity = $phone
                    ? CustomerLoginIdentity::query()->where('phone_e164', $phone)->first()
                    : ($username ? CustomerLoginIdentity::query()->where('username', $username)->first() : null);
                $candidate = $identity?->user;
                // Equal work for existing and missing account; no account enumeration.
                // Share failed-attempt budget across username and phone aliases.
                $accountKey = $candidate ? 'customer-login-account:'.$candidate->getKey() : null;
                $blocked = $accountKey && RateLimiter::tooManyAttempts($accountKey, 10);
                $hash = $candidate?->password ?? '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';
                // Exact legacy passwords stay valid. Normalize digit-only PINs only
                // for phone-identity customers; never alter email or admin passwords.
                $valid = Hash::check($data['password'], $hash);
                $pin = CustomerSimplePassword::normalizedPin($data['password']);
                if (! $valid && $pin !== $data['password']) {
                    $valid = Hash::check($pin, $hash);
                }
                if ($blocked) {
                    throw ValidationException::withMessages(['identifier' => 'Authentication failed.']);
                }
                if ($valid) {
                    if ($accountKey) RateLimiter::clear($accountKey);
                    $user = $candidate;
                } elseif ($accountKey) {
                    RateLimiter::hit($accountKey, 900);
                }
            }
        }

        if (! $user || ! $user->active) {
            throw ValidationException::withMessages(['identifier' => 'Authentication failed.']);
        }

        if ($user->isActiveAdmin()) {
            AdminAuthState::query()->updateOrCreate(['user_id' => $user->id], ['password_configured_at' => now()]);
            Auth::guard('web')->login($user, true);
            $request->session()->forget([CustomerSession::KEY, 'password_hash_web', 'armaghan.password_setup_user_id', 'armaghan.password_setup_until']);
            $request->session()->regenerate(true);
            return response()->json(['redirect' => '/backend/admin'])->header('Cache-Control', 'no-store');
        }

        $customer = $user->customer;
        if ($user->role !== UserRole::Customer || ! $customer || ! $customer->active) {
            throw ValidationException::withMessages(['identifier' => 'Authentication failed.']);
        }

        $request->session()->forget(['armaghan.password_setup_user_id', 'armaghan.password_setup_until']);
        CustomerSession::login($request, $customer);

        return response()->json(['ok' => true])->header('Cache-Control', 'no-store');
    }

    public function register(Request $request): JsonResponse
    {
        if ($request->exists('phone') || $request->exists('username')) {
            return $this->registerPhone($request);
        }
        $request->merge(['email' => strtolower(trim((string) $request->input('email')))]);
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255', 'confirmed', Password::min(12)->letters()->numbers()]]);
        try {
            $customer = DB::transaction(function () use ($data): Customer {
            if (str_ends_with($data['email'], '@accounts.armaghan.invalid')
                || in_array($data['email'], config('owner-access.google_admin_emails', []), true)
                || User::query()->whereRaw('lower(email) = ?', [$data['email']])->exists()) {
                throw ValidationException::withMessages(['email' => 'Registration unavailable.']);
            }
            // Roles, active state and ownership are server-owned, never request fields.
            $user = User::create(['name' => trim($data['name']), 'email' => $data['email'], 'password' => $data['password'],
                'role' => UserRole::Customer, 'active' => true]);
            return Customer::create(['user_id' => $user->id, 'active' => true, 'direct_link_enabled' => false]);
            });
        } catch (\Illuminate\Database\UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['email' => 'Registration unavailable.']);
        }
        CustomerSession::login($request, $customer);
        return response()->json(['ok' => true], 201)->header('Cache-Control', 'no-store');
    }

    private function registerPhone(Request $request): JsonResponse
    {
        $username = CustomerPhone::username((string) $request->input('username', ''));
        $phone = CustomerPhone::normalize((string) $request->input('phone', ''));
        $request->merge([
            'username' => $username, 'phone' => $phone,
            'password' => CustomerSimplePassword::normalizedPin($request->input('password')),
            'password_confirmation' => CustomerSimplePassword::normalizedPin($request->input('password_confirmation')),
        ]);
        $data = $request->validate([
            'username' => ['required','string','regex:/^[a-z][a-z0-9._-]{2,31}$/D'],
            'phone' => ['required','string','regex:/^\\+[1-9][0-9]{7,14}$/D'],
            'name' => ['sometimes','nullable','string','max:255'],
            'password' => ['required','string','max:255','confirmed', new CustomerSimplePassword()],
        ]);
        $reserved = array_merge(['admin','administrator','root','mot','amirau'], array_keys(config('temporary-admin-access.aliases', [])));
        if (in_array($data['username'], $reserved, true)) {
            throw ValidationException::withMessages(['username' => 'Registration unavailable.']);
        }
        try {
            $customer = DB::transaction(function () use ($data): Customer {
                // Do not link to a preexisting trade record by an UNVERIFIED phone.
                if (CustomerLoginIdentity::query()->where('username', $data['username'])->orWhere('phone_e164', $data['phone'])->exists()) {
                    throw ValidationException::withMessages(['username' => 'Registration unavailable.']);
                }
                // Users.email is non-null and unique in the legacy schema. Reserve a
                // nondeliverable random internal identifier; never present it as email.
                $user = User::create([
                    'name' => trim((string) ($data['name'] ?? '')) ?: $data['username'],
                    'email' => 'phone-'.Str::uuid().'@accounts.armaghan.invalid',
                    'password' => $data['password'],
                    'role' => UserRole::Customer,
                    'active' => true,
                ]);
                CustomerLoginIdentity::create([
                    'user_id' => $user->id,
                    'username' => $data['username'],
                    'phone_e164' => $data['phone'],
                ]);
                $customer = Customer::create(['user_id' => $user->id, 'active' => true, 'direct_link_enabled' => false]);
                $customer->businessProfile()->create(['phone' => $data['phone']]);
                return $customer;
            });
        } catch (\Illuminate\Database\UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['username' => 'Registration unavailable.']);
        }
        CustomerSession::login($request, $customer);
        return response()->json(['ok' => true], 201)->header('Cache-Control', 'no-store');
    }

    private function currentUser(Request $request): User
    {
        $admin = Auth::guard('web')->user();
        if ($admin?->isActiveAdmin()) {
            return $admin;
        }
        $user = CustomerSession::current($request)?->user;
        abort_unless($user && $user->active && $user->role === UserRole::Customer, 401);
        return $user;
    }

    public function security(Request $request)
    {
        $user = $this->currentUser($request);
        return response()->view('account-security', ['user' => $user, 'recentGoogle' => $this->recentGoogle($request, $user)])
            ->header('Cache-Control', 'no-store, private');
    }

    private function recentGoogle(Request $request, User $user): bool
    {
        return (int) $request->session()->get('armaghan.password_setup_user_id') === (int) $user->id
            && (int) $request->session()->get('armaghan.password_setup_until') > now()->timestamp;
    }

    public function password(Request $request)
    {
        $user = $this->currentUser($request);
        $passwordRules = $user->isActiveAdmin()
            ? ['required', 'string', 'max:255', 'confirmed']
            : ['required', 'string', 'max:255', 'confirmed', Password::min(12)->letters()->numbers()];
        $data = $request->validate(['password' => $passwordRules,
            'current_password' => ['nullable', 'string', 'max:255']]);
        if (! $user->isActiveAdmin() && ! $this->recentGoogle($request, $user) && ! Hash::check($data['current_password'] ?? '', $user->password)) {
            throw ValidationException::withMessages(['current_password' => 'رمز فعلی درست نیست؛ یا دوباره با گوگل وارد شوید.']);
        }
        $user->update(['password' => $data['password']]);
        if ($user->isActiveAdmin()) {
            AdminAuthState::query()->updateOrCreate(['user_id' => $user->id], ['password_configured_at' => now()]);
        }
        $request->session()->forget(['armaghan.password_setup_user_id', 'armaghan.password_setup_until', 'password_hash_web']);
        if ($user->isActiveAdmin()) {
            Auth::guard('web')->login($user->fresh(), true);
        }
        $request->session()->regenerate(true);
        $request->session()->regenerateToken();
        return redirect()->away('https://armaghantrading.com/backend/account/security')->with('status', 'رمز حساب ذخیره شد.');
    }
}
