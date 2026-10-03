<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BulkTagsController extends Controller
{
    public function update(Request $request, string $resource): JsonResponse
    {
        $data = $request->validate([
            'mode' => ['required', 'in:add,remove,replace'],
            'tags' => ['present', 'array', 'max:10'],
            'tags.*' => ['required', 'string', 'max:40', 'regex:/^[\p{L}\p{N}][\p{L}\p{N}\p{M} _\.\-‌]*$/u'],
            'items' => ['required', 'array', 'min:1', 'max:100'],
            'items.*' => ['required', 'array:id,revision'],
            'items.*.id' => ['required', 'integer', 'min:1', 'distinct'],
            'items.*.revision' => ['required', 'string', 'regex:/^[a-f0-9]{64}$/'],
        ]);
        $tags = $this->normalize($data['tags']);
        if ($data['mode'] !== 'replace' && ! $tags) throw ValidationException::withMessages(['tags' => 'At least one tag is required.']);
        [$model, $controller] = match ($resource) {
            'products' => [Product::class, ProductController::class],
            'customers' => [Customer::class, CustomerController::class],
            'users' => [User::class, UserController::class],
            default => abort(404),
        };
        DB::transaction(function () use ($request, $data, $tags, $model, $controller, $resource): void {
            $actor = User::query()->findOrFail($request->user()->id);
            abort_unless($actor->isActiveAdmin(), 403);
            $rows = $model::query()->whereIn('id', array_column($data['items'], 'id'))->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            abort_unless($rows->count() === count($data['items']), 404);
            foreach ($data['items'] as $item) {
                $row = $rows[$item['id']];
                if ($resource === 'users') {
                    abort_if($row->id === $actor->id || strtolower($row->email) === config('owner-access.primary_owner_email'), 403);
                    abort_if(! $actor->isPrimaryOwner() && ($row->role === UserRole::Admin || in_array(strtolower($row->email), config('owner-access.google_admin_emails'), true)), 403);
                }
                if ($resource === 'customers' && strtolower((string) $row->user?->email) === config('owner-access.primary_owner_email')) {
                    abort_unless($actor->isPrimaryOwner() && $actor->id === $row->user_id, 403);
                }
                abort_unless(hash_equals(app($controller)->revision($row), $item['revision']), 409, 'Record changed; reload before tagging.');
                $current = $row->adminTags();
                $next = match ($data['mode']) {
                    'add' => $this->normalize(array_merge($current, $tags)),
                    'remove' => array_values(array_filter($current, fn ($tag) => ! in_array(mb_strtolower($tag), array_map('mb_strtolower', $tags), true))),
                    'replace' => $tags,
                };
                if (count($next) > 10) throw ValidationException::withMessages(['tags' => 'Maximum ten tags per record.']);
                $row->adminTagRecord()->updateOrCreate([], ['tags' => $next]);
                ActivityLog::create(['actor_user_id' => $actor->id, 'action' => 'admin.'.$resource.'.tags-updated',
                    'subject_type' => $model, 'subject_id' => $row->id, 'metadata' => ['mode' => $data['mode'], 'count' => count($next)]]);
            }
        });
        return response()->json(['updated' => count($data['items'])])->header('Cache-Control', 'no-store, private');
    }

    private function normalize(array $tags): array
    {
        $unique = [];
        foreach ($tags as $tag) {
            $tag = trim(preg_replace('/\s+/u', ' ', $tag));
            if ($tag !== '') $unique[mb_strtolower($tag)] ??= $tag;
        }
        $tags = array_values($unique); sort($tags, SORT_STRING);
        return $tags;
    }
}
