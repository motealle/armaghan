<?php

namespace App\Http\Controllers;

use App\Exceptions\MagicLinkUnavailable;
use App\Models\Customer;
use App\Services\CustomerMagicLinkService;
use App\Support\CustomerSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerMagicLinkController extends Controller
{
    public function consume(
        Request $request,
        CustomerMagicLinkService $magicLinks,
    ): JsonResponse {
        $validated = $request->validate([
            'token' => ['required', 'string', 'size:64', 'alpha_num'],
        ]);

        try {
            $customer = $magicLinks->consume((string) $validated['token']);
        } catch (MagicLinkUnavailable) {
            return response()->json([
                'message' => 'Magic link is unavailable.',
            ], 410)->header('Cache-Control', 'no-store, private');
        }

        CustomerSession::login($request, $customer);

        return $this->customerResponse($customer);
    }

    private function customerResponse(Customer $customer): JsonResponse
    {
        return response()->json([
            'customer' => [
                'id' => $customer->getKey(),
                'company_name' => $customer->company_name,
                'whatsapp' => $customer->whatsapp,
                'country_code' => $customer->country_code,
                'country_name' => $customer->country_name,
            ],
        ])->header('Cache-Control', 'no-store, private');
    }
}
