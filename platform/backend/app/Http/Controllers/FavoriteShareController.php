<?php

namespace App\Http\Controllers;

use App\Exceptions\FavoriteShareUnavailable;
use App\Models\Customer;
use App\Models\FavoriteShare;
use App\Services\FavoriteShareService;
use App\Support\CustomerSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteShareController extends Controller
{
    public function store(Request $request, FavoriteShareService $shares): JsonResponse
    {
        $maxProducts = (int) config('armaghan.favorite_share.max_products', 30);
        $validated = $request->validate([
            'product_codes' => ['required', 'array', 'min:1', 'max:'.$maxProducts],
            'product_codes.*' => ['required', 'string', 'max:64', 'distinct'],
        ]);

        $customer = CustomerSession::current($request);
        $issued = $shares->issue($validated['product_codes'], $customer);

        return response()->json([
            'share' => [
                'id' => $issued['share']->getKey(),
                'url' => $issued['url'],
                'expires_at' => $issued['share']->expires_at?->toIso8601String(),
                'owned' => $issued['owned'],
            ],
        ])->header('Cache-Control', 'no-store, private');
    }

    public function resolve(Request $request, FavoriteShareService $shares): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string', 'max:64', 'alpha_num'],
        ]);

        try {
            $share = $shares->resolve((string) $validated['token']);
        } catch (FavoriteShareUnavailable) {
            return response()->json([
                'message' => 'Favorite share is unavailable.',
            ], 410)->header('Cache-Control', 'no-store, private');
        }

        return response()->json([
            'share' => [
                'id' => $share->getKey(),
                'expires_at' => $share->expires_at?->toIso8601String(),
                'product_codes' => $share->products->pluck('code')->values()->all(),
            ],
        ])->header('Cache-Control', 'no-store, private');
    }

    public function destroy(
        Request $request,
        FavoriteShare $favoriteShare,
        FavoriteShareService $shares,
    ): JsonResponse {
        /** @var Customer $customer */
        $customer = $request->attributes->get('armaghan.customer');

        try {
            $shares->revoke($favoriteShare, $customer);
        } catch (FavoriteShareUnavailable) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        return response()->json(['ok' => true])
            ->header('Cache-Control', 'no-store, private');
    }
}
