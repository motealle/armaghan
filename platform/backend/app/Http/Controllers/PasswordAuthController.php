<?php
namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\User;
use App\Support\CustomerSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class PasswordAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate(['email' => ['required', 'string', 'email', 'max:255'], 'password' => ['required', 'string', 'max:255']]);
        $user = User::query()->whereRaw('lower(email) = ?', [strtolower(trim($data['email']))])->first();
        // Equal hashing work for a missing account. This is an unusable dummy hash.
        $valid = Hash::check($data['password'], $user?->password ?? '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.');
        if (! $valid || ! $user || ! $user->active) {
            throw ValidationException::withMessages(['email' => 'Authentication failed.']);
        }
        if ($user->isActiveAdmin()) {
            Auth::guard('web')->login($user, true);
            $request->session()->forget([CustomerSession::KEY, 'password_hash_web', 'armaghan.password_setup_user_id', 'armaghan.password_setup_until']);
            $request->session()->regenerate(true);
            return response()->json(['redirect' => '/backend/admin'])->header('Cache-Control', 'no-store');
        }
        $customer = $user->customer;
        if ($user->role !== UserRole::Customer || ! $customer || ! $customer->active) {
            throw ValidationException::withMessages(['email' => 'Authentication failed.']);
        }
        $request->session()->forget(['armaghan.password_setup_user_id', 'armaghan.password_setup_until']);
        CustomerSession::login($request, $customer);
        return response()->json(['ok' => true])->header('Cache-Control', 'no-store');
    }

    public function register(Request $request): JsonResponse
    {
        $request->merge(['email' => strtolower(trim((string) $request->input('email')))]);
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255', 'confirmed', Password::min(12)->letters()->numbers()]]);
        try {
            $customer = DB::transaction(function () use ($data): Customer {
            if (in_array($data['email'], config('owner-access.google_admin_emails', []), true)
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
        $passwordRule = $user->isActiveAdmin()
            ? Password::min(8)
            : Password::min(12)->letters()->numbers();
        $data = $request->validate(['password' => ['required', 'string', 'max:255', 'confirmed', $passwordRule],
            'current_password' => ['nullable', 'string', 'max:255']]);
        if (! $this->recentGoogle($request, $user) && ! Hash::check($data['current_password'] ?? '', $user->password)) {
            throw ValidationException::withMessages(['current_password' => 'رمز فعلی درست نیست؛ یا دوباره با گوگل وارد شوید.']);
        }
        $user->update(['password' => $data['password'], 'password_configured_at' => now()]);
        $request->session()->forget(['armaghan.password_setup_user_id', 'armaghan.password_setup_until', 'password_hash_web']);
        if ($user->isActiveAdmin()) {
            Auth::guard('web')->login($user->fresh(), true);
        }
        $request->session()->regenerate(true);
        $request->session()->regenerateToken();
        return redirect()->away('https://armaghantrading.com/backend/account/security')->with('status', 'رمز حساب ذخیره شد.');
    }
}
