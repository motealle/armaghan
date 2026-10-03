<?php

namespace App\Support;

use App\Models\Customer;
use App\Enums\UserRole;
use Illuminate\Http\Request;

final class CustomerSession
{
    public const KEY = 'armaghan.customer_id';

    public static function current(Request $request): ?Customer
    {
        $id = (int) $request->session()->get(self::KEY, 0);

        if ($id < 1) {
            return null;
        }

        return Customer::query()
            ->whereKey($id)
            ->where('active', true)
            ->where(function ($query): void {
                $query->whereNull('user_id')->orWhereHas('user', fn ($user) => $user->where('active', true)->where('role', UserRole::Customer));
            })
            ->first();
    }

    public static function login(Request $request, Customer $customer): void
    {
        $request->session()->put(self::KEY, $customer->getKey());
        $request->session()->regenerate(true);
    }

    public static function logout(Request $request): void
    {
        $request->session()->forget(self::KEY);
        $request->session()->regenerate(true);
        $request->session()->regenerateToken();
    }
}
