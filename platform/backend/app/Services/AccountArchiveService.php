<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\UserController;
use App\Models\AccountArchive;
use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\CustomerBusinessProfile;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class AccountArchiveService
{
    public function prepare(User $actor, string $resource, int $id, string $revision): array
    {
        return DB::transaction(function () use ($actor, $resource, $id, $revision): array {
            [$user, $customer, $target] = $this->pair($resource, $id);
            $this->guard($actor, $user);
            $controller = $resource === 'users' ? UserController::class : CustomerController::class;
            abort_unless(hash_equals(app($controller)->revision($target), $revision), 409);
            $snapshot = $this->snapshot($user, $customer);
            $uuid = (string) Str::uuid();
            $receipt = Str::random(32);
            $label = $resource === 'users' ? $user->name : ($customer->company_name ?: $user?->name ?: '#'.$id);
            // Recovery material is authenticated encryption; credentials never appear in plain JSON.
            $backup = json_encode(['format' => 'armaghan.account-backup.v1', 'archive_id' => $uuid,
                'record' => ['resource' => $resource, 'id' => $id, 'label' => $label],
                'exported_at' => now()->toIso8601String(),
                'tags' => ['user' => $user?->adminTags() ?? [], 'customer' => $customer?->adminTags() ?? []],
                'user' => $user?->only(['id', 'name', 'email', 'role', 'active']),
                'customer' => $customer?->only(['id', 'user_id', 'company_name', 'whatsapp', 'country_code', 'country_name', 'notes', 'priority', 'active', 'direct_link_enabled']),
                'customer_business' => $customer?->businessProfile?->only(CustomerBusinessProfile::FIELDS),
                'recovery' => Crypt::encryptString(json_encode($snapshot, JSON_THROW_ON_ERROR)),
            ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            AccountArchive::create(['id' => $uuid, 'resource' => $resource, 'target_id' => $id,
                'user_id' => $user?->id, 'customer_id' => $customer?->id, 'actor_user_id' => $actor->id,
                'snapshot' => $snapshot + ['label' => $label], 'bundle_hash' => $this->fingerprint($snapshot),
                'backup_hash' => hash('sha256', $backup), 'receipt_hash' => hash('sha256', $receipt),
                'expires_at' => now()->addMinutes(15)]);
            $this->audit($actor, $uuid, 'backup_prepared');
            return ['archive_id' => $uuid, 'receipt' => $receipt, 'backup' => $backup,
                'backup_sha256' => hash('sha256', $backup), 'filename' => 'armaghan-'.$resource.'-'.$id.'-'.$uuid.'.json'];
        });
    }

    public function remove(User $actor, string $archiveId, string $receipt, string $backupHash): void
    {
        DB::transaction(function () use ($actor, $archiveId, $receipt, $backupHash): void {
            $archive = AccountArchive::lockForUpdate()->findOrFail($archiveId);
            abort_unless($archive->actor_user_id === $actor->id && $archive->expires_at->isFuture()
                && $archive->deleted_at === null && $archive->restored_at === null
                && hash_equals($archive->receipt_hash, hash('sha256', $receipt))
                && hash_equals($archive->backup_hash, $backupHash), 409, 'Download a fresh backup first.');
            [$user, $customer] = $this->pair($archive->resource, $archive->target_id);
            $this->guard($actor, $user);
            abort_unless(hash_equals($archive->bundle_hash, $this->fingerprint($this->snapshot($user, $customer))), 409);
            if ($user) {
                $user->forceFill(['active' => false, 'remember_token' => Str::random(60)])->save();
                DB::table('sessions')->where('user_id', $user->id)->delete();
                DB::table('password_reset_tokens')->where('email', $user->email)->delete();
            }
            if ($customer) {
                $customer->forceFill(['active' => false, 'direct_link_enabled' => false])->save();
                $customer->magicLinks()->whereNull('revoked_at')->update(['revoked_at' => now()]);
            }
            // Retain rows and foreign keys for database Undo; ordinary queries/auth now exclude them.
            $archive->forceFill(['deleted_at' => now(), 'receipt_hash' => hash('sha256', Str::random(64)),
                'bundle_hash' => $this->fingerprint($this->snapshot($user, $customer))])->save();
            $this->audit($actor, $archive->id, 'deleted');
        });
    }

    public function restore(User $actor, string $archiveId, ?string $backup = null): void
    {
        DB::transaction(function () use ($actor, $archiveId, $backup): void {
            $archive = AccountArchive::lockForUpdate()->findOrFail($archiveId);
            abort_unless($archive->deleted_at !== null && $archive->restored_at === null, 409);
            if ($backup !== null) abort_unless(hash_equals($archive->backup_hash, hash('sha256', $backup)), 422, 'Invalid backup.');
            [$user, $customer] = $this->pair($archive->resource, $archive->target_id, true);
            $this->guard($actor, $user);
            abort_unless(hash_equals($archive->bundle_hash, $this->fingerprint($this->snapshot($user, $customer))), 409);
            // Clear the marker inside the same transaction before guarded model writes.
            $archive->forceFill(['restored_at' => now()])->save();
            $original = $archive->snapshot;
            if ($user) $user->forceFill(['active' => (bool) $original['user']['active']])->save();
            if ($customer) $customer->forceFill(['active' => (bool) $original['customer']['active'],
                'direct_link_enabled' => (bool) $original['customer']['direct_link_enabled']])->save();
            // Revoked sessions, reset tokens and magic links are never resurrected.
            $this->audit($actor, $archive->id, 'restored');
        });
    }

    public function listing(User $actor, string $resource): array
    {
        abort_unless($actor->fresh()?->isActiveAdmin(), 403);
        return AccountArchive::where('resource', $resource)->whereNotNull('deleted_at')->whereNull('restored_at')
            ->orderByDesc('deleted_at')->get()->filter(function ($archive) use ($actor): bool {
                $user = $archive->user_id ? User::withoutGlobalScope('account_archive')->find($archive->user_id) : null;
                return ! $user || ($user->role !== UserRole::Admin && ! in_array(strtolower($user->email), config('owner-access.google_admin_emails'), true)) || $actor->isPrimaryOwner();
            })->map(fn ($archive) => ['id' => $archive->id, 'label' => $archive->snapshot['label'],
                'resource' => $archive->resource, 'deleted_at' => $archive->deleted_at->toIso8601String()])->values()->all();
    }

    private function pair(string $resource, int $id, bool $archived = false): array
    {
        $users = $archived ? User::withoutGlobalScope('account_archive') : User::query();
        $customers = $archived ? Customer::withoutGlobalScope('account_archive') : Customer::query();
        if ($resource === 'users') {
            $user = $users->lockForUpdate()->findOrFail($id);
            $customer = $customers->where('user_id', $user->id)->lockForUpdate()->first();
            return [$user, $customer, $user];
        }
        $customer = $customers->lockForUpdate()->findOrFail($id);
        $user = $customer->user_id ? $users->lockForUpdate()->findOrFail($customer->user_id) : null;
        return [$user, $customer, $customer];
    }

    private function guard(User $actor, ?User $user): void
    {
        $actor = User::lockForUpdate()->findOrFail($actor->id);
        abort_unless($actor->isActiveAdmin(), 403);
        if (! $user) return;
        abort_if($user->id === $actor->id || strtolower($user->email) === config('owner-access.primary_owner_email'), 403);
        if ($user->role === UserRole::Admin || in_array(strtolower($user->email), config('owner-access.google_admin_emails'), true)) {
            abort_unless($actor->isPrimaryOwner(), 403);
        }
    }

    private function snapshot(?User $user, ?Customer $customer): array
    {
        $attributes = static function ($model): ?array {
            if (! $model) return null;
            $data = $model->getAttributes();
            foreach (['active', 'direct_link_enabled', 'pinned'] as $field) {
                if (array_key_exists($field, $data)) $data[$field] = (int) (bool) $data[$field];
            }
            return $data;
        };
        $snapshot = ['user' => $attributes($user), 'customer' => $attributes($customer),
            'user_tags' => $user?->adminTags() ?? [], 'customer_tags' => $customer?->adminTags() ?? []];
        // No new null key for old archives: their original fingerprints remain valid.
        $profile = $customer?->businessProfile()->first();
        if ($profile) $snapshot['customer_business'] = $attributes($profile);
        return $snapshot;
    }

    private function fingerprint(array $snapshot): string
    {
        return hash('sha256', json_encode($snapshot, JSON_THROW_ON_ERROR));
    }

    private function audit(User $actor, string $id, string $action): void
    {
        ActivityLog::create(['actor_user_id' => $actor->id, 'action' => 'admin.account.'.$action,
            'subject_type' => AccountArchive::class, 'metadata' => ['archive_id' => $id]]);
    }
}
