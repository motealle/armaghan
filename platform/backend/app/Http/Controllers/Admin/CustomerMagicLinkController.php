<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\CustomerMagicLinkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerMagicLinkController extends Controller
{
    public function store(
        Request $request,
        Customer $customer,
        CustomerMagicLinkService $magicLinks,
    ): JsonResponse {
        $validated = $request->validate([
            'expires_in_hours' => ['nullable', 'integer', 'min:1', 'max:168'],
        ]);

        $issued = $magicLinks->issue(
            $customer,
            (int) ($validated['expires_in_hours'] ?? 72),
            $request->user()?->getKey(),
        );

        return response()->json([
            'url' => $issued['url'],
            'expires_at' => $issued['magic_link']->expires_at?->toIso8601String(),
        ])->header('Cache-Control', 'no-store, private');
    }

    public function destroy(
        Request $request,
        Customer $customer,
        CustomerMagicLinkService $magicLinks,
    ): JsonResponse {
        $count = $magicLinks->revoke($customer, $request->user()?->getKey());

        return response()->json([
            'ok' => true,
            'revoked_count' => $count,
        ])->header('Cache-Control', 'no-store, private');
    }
}
