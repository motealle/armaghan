<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\CustomerBusinessProfile;
use Illuminate\Support\Facades\DB;
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

        // The customer's own business fields; administrative controls are never accepted.
        $profileFields = array_diff(CustomerBusinessProfile::FIELDS, ['pinned']);
        $rules = array_fill_keys(array_diff($profileFields,['contact_email','phone','preferred_language']), ['sometimes','nullable','string','max:255']);
        $validated = $request->validate(array_merge($rules, [
            'contact_email' => ['sometimes','nullable','email:rfc','max:255'],
            'phone' => ['sometimes','nullable','string','max:64'],
            'preferred_language' => ['sometimes','nullable','in:fa,en,ar,ku'],
            'company_name' => ['sometimes','nullable','string','max:255'],
            'whatsapp' => ['sometimes','nullable','string','max:64'],
            'country_code' => ['sometimes','nullable','string','size:2'],
            'country_name' => ['sometimes','nullable','string','max:255'],
        ]));
        if (array_key_exists('country_code',$validated) && $validated['country_code'] !== null) {
            $validated['country_code'] = strtoupper(trim($validated['country_code']));
        }
        $core = array_intersect_key($validated,array_flip(['company_name','whatsapp','country_code','country_name']));
        $business = array_intersect_key($validated,array_flip($profileFields));
        $changed = DB::transaction(function() use($customer,$core,$business): array {
            $row = Customer::query()->lockForUpdate()->findOrFail($customer->id);
            $profile = $row->businessProfile()->first();
            $before = array_merge($row->only(array_keys($core)), $profile?->only(array_keys($business)) ?? []);
            if ($core !== []) $row->fill($core)->save();
            if ($business !== []) $row->businessProfile()->updateOrCreate([], $business);
            return array_keys(array_filter(array_merge($core,$business),
                fn(mixed $value,string $field): bool => ($before[$field] ?? null) !== $value, ARRAY_FILTER_USE_BOTH));
        });
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
        $customer->loadMissing(['user','businessProfile']);
        $business = array_diff_key(array_merge(CustomerBusinessProfile::defaults(),
            $customer->businessProfile?->only(CustomerBusinessProfile::FIELDS) ?? []), ['pinned' => true]);
        return response()->json([
            'customer' => [
                'id' => $customer->getKey(),
                'name' => $customer->user?->name,
                'email' => $customer->user?->email,
                'company_name' => $customer->company_name,
                'whatsapp' => $customer->whatsapp,
                'country_code' => $customer->country_code,
                'country_name' => $customer->country_name,
                ...$business,
            ],
        ])->header('Cache-Control', 'no-store, private');
    }
}
