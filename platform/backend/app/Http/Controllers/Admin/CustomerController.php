<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    private const FIELDS = ['company_name', 'whatsapp', 'country_code', 'country_name', 'notes', 'priority', 'active', 'direct_link_enabled'];

    public function index(Request $request): JsonResponse
    {
        $input = $request->validate(['page' => ['sometimes', 'integer', 'min:1'], 'search' => ['nullable', 'string', 'max:100']]);
        $query = Customer::query()->with('user');
        if (! $request->user()->isPrimaryOwner()) {
            $query->where(function ($q): void {
                $q->whereDoesntHave('user')->orWhereHas('user', fn ($u) => $u->whereRaw('lower(email) != ?', [config('owner-access.primary_owner_email')]));
            });
        }
        if ($search = trim($input['search'] ?? '')) {
            $query->where(function ($q) use ($search): void {
                $q->where('company_name', 'like', '%'.$search.'%')
                    ->orWhere('whatsapp', 'like', '%'.$search.'%')
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%'));
            });
        }
        $page = $query->orderByDesc('id')->paginate(50);

        return $this->json(['customers' => $page->getCollection()->map(fn ($c) => $this->snapshot($c))->all(),
            'page' => $page->currentPage(), 'last_page' => $page->lastPage(), 'total' => $page->total()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules(false));
        $customer = DB::transaction(function () use ($data, $request) {
            $customer = Customer::query()->create(array_intersect_key($data, array_flip(self::FIELDS)));
            $this->audit($request, $customer, 'admin.customer.created', array_keys($data));
            return $customer;
        });

        return $this->json(['customer' => $this->snapshot($customer->refresh()->load('user'))], 201);
    }

    public function update(Request $request, Customer $customer): JsonResponse
    {
        $data = $request->validate($this->rules(true));
        $customer = DB::transaction(function () use ($data, $request, $customer) {
            $row = Customer::query()->lockForUpdate()->findOrFail($customer->id);
            if (strtolower((string) $row->user?->email) === config('owner-access.primary_owner_email')) {
                abort_unless($request->user()->isPrimaryOwner() && $request->user()->id === $row->user_id, 403);
            }
            abort_unless(hash_equals($this->revision($row), $data['revision']), 409, 'Customer changed; reload before saving.');
            $row->fill(array_intersect_key($data, array_flip(self::FIELDS)))->save();
            $this->audit($request, $row, 'admin.customer.updated', array_keys($data));
            return $row;
        });

        return $this->json(['customer' => $this->snapshot($customer->refresh()->load('user'))]);
    }

    private function rules(bool $update): array
    {
        return [
            'company_name' => [$update ? 'sometimes' : 'required', 'string', 'max:255'],
            'whatsapp' => ['sometimes', 'nullable', 'string', 'max:64'],
            'country_code' => ['sometimes', 'nullable', 'string', 'regex:/^[A-Z]{2}$/'],
            'country_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:10000'],
            'priority' => ['sometimes', 'integer', 'min:0', 'max:255'],
            'active' => ['sometimes', 'boolean'],
            'direct_link_enabled' => ['sometimes', 'boolean'],
            'revision' => $update ? ['required', 'string', 'regex:/^[a-f0-9]{64}$/'] : ['prohibited'],
            'user_id' => ['prohibited'], 'role' => ['prohibited'], 'password' => ['prohibited'],
            'email' => ['prohibited'], 'name' => ['prohibited'],
        ];
    }

    public function revision(Customer $customer): string
    {
        // Content fingerprint detects changes even within the same timestamp second.
        return hash('sha256', json_encode([$customer->only(array_merge(['id', 'user_id', 'updated_at'], self::FIELDS)), $customer->adminTags()], JSON_THROW_ON_ERROR));
    }

    private function snapshot(Customer $customer): array
    {
        return array_merge(['id' => $customer->id], $customer->only(self::FIELDS), [
            'has_account' => $customer->user_id !== null,
            'tags' => $customer->adminTags(),
            'name' => $customer->user?->name,
            'email' => $customer->user?->email,
            'revision' => $this->revision($customer),
        ]);
    }

    private function audit(Request $request, Customer $customer, string $action, array $fields): void
    {
        ActivityLog::query()->create(['actor_user_id' => $request->user()->id, 'customer_id' => $customer->id,
            'action' => $action, 'subject_type' => Customer::class, 'subject_id' => $customer->id,
            'metadata' => ['fields' => array_values(array_intersect(self::FIELDS, $fields))]]);
    }

    private function json(array $data, int $status = 200): JsonResponse
    {
        return response()->json($data, $status)->header('Cache-Control', 'no-store, private');
    }
}
