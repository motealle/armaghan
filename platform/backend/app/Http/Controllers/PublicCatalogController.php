<?php

namespace App\Http\Controllers;

use App\Enums\ProductAvailability;
use App\Http\Resources\PublicCategoryResource;
use App\Http\Resources\PublicProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PublicCatalogController extends Controller
{
    public function categories(): JsonResponse
    {
        $managedCategoryCodes = Category::query()
            ->orderBy('code')
            ->pluck('code')
            ->all();

        $managedSubcategoryCodes = Subcategory::query()
            ->orderBy('code')
            ->pluck('code')
            ->all();

        $categories = Category::query()
            ->where('active', true)
            ->with([
                'subcategories' => fn ($query) => $query
                    ->where('active', true)
                    ->orderBy('sort_order')
                    ->orderBy('code'),
            ])
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get();

        return PublicCategoryResource::collection($categories)
            ->additional([
                'catalog' => [
                    'managed_category_codes' => $managedCategoryCodes,
                    'managed_subcategory_codes' => $managedSubcategoryCodes,
                ],
            ])
            ->response()
            ->header('Cache-Control', 'public, max-age=60, stale-while-revalidate=300');
    }

    public function products(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'codes' => ['sometimes', 'array', 'min:1', 'max:80'],
            'codes.*' => ['required', 'string', 'max:64', 'distinct'],
            'category' => ['nullable', 'string', 'max:16'],
            'subcategory' => ['nullable', 'string', 'max:16'],
            'availability' => ['nullable', Rule::enum(ProductAvailability::class)],
            'q' => ['nullable', 'string', 'max:80'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $managedCodes = Product::query()
            ->orderBy('code')
            ->pluck('code')
            ->all();

        $query = Product::query()
            ->where('active', true)
            ->whereHas('subcategory', fn ($subcategory) => $subcategory
                ->where('active', true)
                ->whereHas('category', fn ($category) => $category->where('active', true)))
            ->with(['subcategory.category', 'subcategory.specDefinitions', 'specValues', 'media']);

        if (isset($validated['codes'])) {
            $query->whereIn('code', $validated['codes']);
        }

        if (isset($validated['category'])) {
            $query->whereHas(
                'subcategory.category',
                fn ($category) => $category->where('code', $validated['category']),
            );
        }

        if (isset($validated['subcategory'])) {
            $query->whereHas(
                'subcategory',
                fn ($subcategory) => $subcategory->where('code', $validated['subcategory']),
            );
        }

        if (isset($validated['availability'])) {
            $query->where('availability', $validated['availability']);
        }

        $needle = trim((string) ($validated['q'] ?? ''));
        if ($needle !== '') {
            $query->where(function ($query) use ($needle): void {
                $query
                    ->where('code', 'like', "%{$needle}%")
                    ->orWhere('name_fa', 'like', "%{$needle}%")
                    ->orWhere('name_ar', 'like', "%{$needle}%")
                    ->orWhere('name_en', 'like', "%{$needle}%")
                    ->orWhere('name_ku', 'like', "%{$needle}%");
            });
        }

        $products = $query
            ->orderBy('sort_order')
            ->orderBy('code')
            ->paginate((int) ($validated['per_page'] ?? 50))
            ->withQueryString();

        return PublicProductResource::collection($products)
            ->additional([
                'catalog' => [
                    'managed_codes' => $managedCodes,
                ],
            ])
            ->response()
            ->header('Cache-Control', 'public, max-age=30, stale-while-revalidate=120');
    }
}
