<?php

namespace App\Http\Middleware;

use App\Support\CustomerSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomerSession
{
    public function handle(Request $request, Closure $next): Response
    {
        $customer = CustomerSession::current($request);

        if ($customer === null) {
            $request->session()->forget(CustomerSession::KEY);

            return response()->json(['message' => 'Unauthenticated.'], 401)
                ->header('Cache-Control', 'no-store');
        }

        $request->attributes->set('armaghan.customer', $customer);

        return $next($request);
    }
}
