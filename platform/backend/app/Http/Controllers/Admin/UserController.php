<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $data = $request->validate(['search' => ['nullable', 'string', 'max:100'], 'page' => ['nullable', 'integer', 'min:1']]);
        $query = User::query()->orderBy('id');
        // Business administrators manage customer accounts; only the primary
        // owner can see/manage administrator accounts in this directory.
        if (! $request->user()->isPrimaryOwner()) { $query->where('role', UserRole::Customer); }
        if ($search = trim($data['search'] ?? '')) {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%');
            });
        }
        $page = $query->paginate(25);
        return $this->json(['users' => $page->getCollection()->map(fn (User $user) => $this->snapshot($user)),
            'page' => $page->currentPage(), 'last_page' => $page->lastPage(), 'total' => $page->total()]);
    }

    public function storeForCustomer(Request $request, Customer $customer): JsonResponse
    {
        $request->validate(['customer_id' => ['prohibited'], 'role' => ['prohibited'], 'active' => ['prohibited']]);
        $request->merge(['customer_id' => $customer->id, 'role' => 'customer', 'active' => true]);
        return $this->store($request);
    }

    public function store(Request $request): JsonResponse
    {
        $request->merge(['email' => strtolower(trim((string) $request->input('email')))]);
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', 'max:255'],
            'role' => ['required', 'in:admin,customer'], 'active' => ['required', 'boolean'],
            'password' => ['required', 'string', 'max:255', 'confirmed', Password::min(12)->letters()->numbers()],
            'customer_id' => ['required_with:customer_revision', 'integer', 'min:1'],
            'customer_revision' => ['required_with:customer_id', 'string', 'regex:/^[a-f0-9]{64}$/'],
            'email_verified_at' => ['prohibited'], 'is_owner' => ['prohibited'], 'user_id' => ['prohibited']]);
        abort_if($data['role'] === 'admin' && ! $request->user()->isPrimaryOwner(), 403);
        // Reserved Google identities must prove ownership through Google, never
        // through an administrator choosing someone else's mailbox/password.
        if (in_array($data['email'], config('owner-access.google_admin_emails'), true)) {
            throw ValidationException::withMessages(['email' => 'Use verified Google sign-in for this identity.']);
        }
        abort_if(isset($data['customer_id']) && ($data['role'] !== 'customer' || ! $data['active']), 422);
        try {
            $user = DB::transaction(function () use ($request, $data): User {
                $actor = User::query()->findOrFail($request->user()->id);
                abort_unless($actor->isActiveAdmin(), 403);
                abort_if($data['role'] === 'admin' && ! $actor->isPrimaryOwner(), 403);
                $customer = null;
                if (isset($data['customer_id'])) {
                    $customer = Customer::query()->lockForUpdate()->findOrFail($data['customer_id']);
                    // Never reassign an existing login, including protected owner identities.
                    abort_if($customer->user_id !== null, 409, 'Customer already has an account.');
                    abort_unless($customer->active, 422, 'Customer must be active.');
                    abort_unless(hash_equals(app(CustomerController::class)->revision($customer), $data['customer_revision']), 409);
                }
                if (User::query()->whereRaw('lower(email) = ?', [$data['email']])->exists()) {
                    throw ValidationException::withMessages(['email' => 'Account already exists.']);
                }
                $user = User::create(array_intersect_key($data, array_flip(['name', 'email', 'role', 'active', 'password'])));
                if ($user->role === UserRole::Customer) {
                    if ($customer) {
                        $customer->update(['user_id' => $user->id]);
                        ActivityLog::create(['actor_user_id' => $actor->id, 'customer_id' => $customer->id,
                            'action' => 'admin.customer.account.created', 'subject_type' => Customer::class,
                            'subject_id' => $customer->id, 'metadata' => ['account_id' => $user->id]]);
                    } else {
                        Customer::create(['user_id' => $user->id, 'active' => $user->active, 'direct_link_enabled' => false]);
                    }
                }
                $this->audit($request, $user, 'created', ['name', 'role', 'active', 'password_initialized']);
                return $user;
            });
        } catch (\Illuminate\Database\UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['email' => 'Account already exists.']);
        }
        return $this->json(['user' => $this->snapshot($user)], 201);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'role' => ['required', 'in:admin,customer'],
            'active' => ['required', 'boolean'], 'revision' => ['required', 'string', 'size:64'],
            'email' => ['prohibited'], 'password' => ['prohibited'], 'email_verified_at' => ['prohibited'], 'is_owner' => ['prohibited']]);
        $updated = DB::transaction(function () use ($request, $user, $data): User {
            $target = User::query()->lockForUpdate()->findOrFail($user->id);
            $actor = User::query()->findOrFail($request->user()->id);
            abort_unless($actor->isActiveAdmin(), 403);
            // Protect the owner even against another administrator or forged UI.
            $protected = strtolower($target->email) === config('owner-access.primary_owner_email');
            abort_if($protected && ($target->id !== $actor->id || ! $actor->isPrimaryOwner() || ! $data['active'] || $data['role'] !== 'admin'), 403);
            $reserved = in_array(strtolower($target->email), config('owner-access.google_admin_emails'), true);
            abort_if(! $actor->isPrimaryOwner() && ($target->role === UserRole::Admin || $data['role'] === 'admin' || $reserved), 403);
            abort_if($data['role'] !== $target->role->value, 422, 'Create a new administrator; existing account roles are immutable.');
            abort_if($target->id === $actor->id && ! $protected, 403);
            abort_unless(hash_equals($this->revision($target), $data['revision']), 409, 'Account changed; reload before editing.');
            $target->update(array_intersect_key($data, array_flip(['name', 'role', 'active'])));
            if ($target->role === UserRole::Customer) {
                // Preserve existing customer history; never delete ownership.
                $customer = $target->customer()->firstOrCreate([], ['active' => $target->active, 'direct_link_enabled' => false]);
                $customer->update(['active' => $target->active]);
            } else {
                $target->customer()->update(['active' => false]);
            }
            $this->audit($request, $target, 'updated', ['name', 'role', 'active']);
            return $target;
        });
        return $this->json(['user' => $this->snapshot($updated)]);
    }

    public function revision(User $user): string
    {
        return hash('sha256', json_encode([$user->id, $user->name, $user->email, $user->role->value, $user->active,
            $user->adminTags(), $user->password, $user->updated_at?->toISOString(), $user->email_verified_at?->toISOString()], JSON_THROW_ON_ERROR));
    }
    private function snapshot(User $user): array
    {
        return ['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'role' => $user->role->value,
            'tags' => $user->adminTags(), 'active' => $user->active, 'is_owner' => $user->isPrimaryOwner(),
            'protected' => strtolower($user->email) === config('owner-access.primary_owner_email'), 'revision' => $this->revision($user)];
    }
    private function audit(Request $request, User $user, string $action, array $fields): void
    {
        ActivityLog::create(['actor_user_id' => $request->user()->id, 'action' => 'admin.account.'.$action,
            'subject_type' => User::class, 'subject_id' => $user->id, 'metadata' => ['fields' => $fields]]);
    }
    private function json(array $data, int $status = 200): JsonResponse
    {
        return response()->json($data, $status)->header('Cache-Control', 'no-store, private');
    }
}
