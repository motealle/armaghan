<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BulkStatusController extends Controller
{
    public function update(Request $request, string $resource): JsonResponse
    {
        $data = $request->validate([
            'active' => ['required', 'boolean'],
            'items' => ['required', 'array', 'min:1', 'max:100'],
            'items.*' => ['required', 'array:id,revision'],
            'items.*.id' => ['required', 'integer', 'min:1', 'distinct'],
            'items.*.revision' => ['required', 'string', 'regex:/^[a-f0-9]{64}$/'],
        ]);
        [$model, $controller, $fields] = match ($resource) {
            'products' => [Product::class, ProductController::class, ['subcategory_id', 'code', 'name_fa', 'name_ar', 'name_en', 'name_ku', 'sort_order']],
            'customers' => [Customer::class, CustomerController::class, []],
            'users' => [User::class, UserController::class, ['name']],
            default => abort(404),
        };
        DB::transaction(function () use ($request, $data, $model, $controller, $fields, $resource): void {
            $rows = $model::query()->whereIn('id', array_column($data['items'], 'id'))->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            abort_unless($rows->count() === count($data['items']), 404);
            foreach ($data['items'] as $item) {
                $row = $rows[$item['id']];
                // Bulk operations never target the actor or the protected owner.
                if ($resource === 'users') {
                    abort_if($row->id === $request->user()->id || strtolower($row->email) === config('owner-access.primary_owner_email'), 403);
                }
                $payload = $row->only($fields) + ['active' => $data['active'], 'revision' => $item['revision']];
                if ($resource === 'products') $payload['availability'] = $row->availability->value;
                if ($resource === 'users') $payload['role'] = $row->role->value;
                $single = clone $request;
                $single->setJson(new \Symfony\Component\HttpFoundation\InputBag($payload));
                $single->request = new \Symfony\Component\HttpFoundation\InputBag($payload);
                // Reuse canonical validation, permissions, revision checks and audit.
                // The enclosing transaction rolls back every item on any failure.
                app($controller)->update($single, $row);
            }
        });
        return response()->json(['updated' => count($data['items'])])->header('Cache-Control', 'no-store, private');
    }
}
