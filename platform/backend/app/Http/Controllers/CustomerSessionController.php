<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Support\CustomerSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerSessionController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return $this->customerResponse($this->customer($request));
    }

    public function update(Request $request): JsonResponse
    {
        $customer = $this->customer($request);

        $validated = $request->validate([
            'company_name' => ['nullable', 'string', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:64'],
            'country_code' => ['nullable', 'string', 'size:2'],
            'country_name' => ['nullable', 'string', 'max:255'],
        ]);

        if (array_key_exists('country_code', $validated) && $validated['country_code'] !== null) {
            $validated['country_code'] = strtoupper(trim($validated['country_code']));
        }

        $before = $customer->only(array_keys($validated));
        $customer->fill($validated);
        $customer->save();

        $changed = array_keys(array_filter(
            $validated,
            fn (mixed $value, string $key): bool => ($before[$key] ?? null) !== $value,
            ARRAY_FILTER_USE_BOTH,
        ));

        if ($changed !== []) {
            ActivityLog::query()->create([
                'customer_id' => $customer->getKey(),
                'action' => 'customer.profile.updated',
                'subject_type' => Customer::class,
                'subject_id' => $customer->getKey(),
                'metadata' => ['fields' => $changed],
            ]);
        }

        return $this->customerResponse($customer->fresh());
    }

    public function logout(Request $request): JsonResponse
    {
        $customer = $this->customer($request);

        ActivityLog::query()->create([
            'customer_id' => $customer->getKey(),
            'action' => 'customer.session.logged_out',
            'subject_type' => Customer::class,
            'subject_id' => $customer->getKey(),
        ]);

        CustomerSession::logout($request);

        return response()->json(['ok' => true])
            ->header('Cache-Control', 'no-store');
    }

    private function customer(Request $request): Customer
    {
        /** @var Customer $customer */
        $customer = $request->attributes->get('armaghan.customer');

        return $customer;
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
